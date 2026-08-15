<?php

namespace App\Services;

use App\Jobs\ConvertCurriculumPresentationJob;
use App\Models\CurriculumLibraryMaterial;
use App\Models\CurriculumPresentationDerivative;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use Throwable;

class CurriculumPresentationConversionService
{
    public const ENGINE = 'libreoffice+pdftoppm';

    public function derivativesTableReady(): bool
    {
        try {
            return Schema::hasTable('curriculum_presentation_derivatives');
        } catch (Throwable) {
            return false;
        }
    }

    public function isConversionEnabled(): bool
    {
        return (bool) config('curriculum_presentation.enabled', true);
    }

    public function materialIsConvertible(CurriculumLibraryMaterial $material): bool
    {
        if ($material->file_kind !== 'pptx') {
            return false;
        }

        $ext = strtolower(pathinfo((string) ($material->original_name ?: $material->path), PATHINFO_EXTENSION));
        if (! in_array($ext, ['ppt', 'pptx'], true)) {
            $ext = strtolower(pathinfo((string) $material->path, PATHINFO_EXTENSION));
        }

        return in_array($ext, ['ppt', 'pptx'], true);
    }

    /**
     * Queue conversion after material row exists. Never mutates material.path.
     */
    public function queueMaterialIfEligible(CurriculumLibraryMaterial $material, bool $sync = false): ?CurriculumPresentationDerivative
    {
        if (! $this->derivativesTableReady() || ! $this->isConversionEnabled()) {
            return null;
        }

        if (! $this->materialIsConvertible($material)) {
            return null;
        }

        $derivative = $this->ensurePendingDerivativeForMaterial($material);

        if ($sync) {
            ConvertCurriculumPresentationJob::dispatchSync(
                CurriculumPresentationDerivative::SOURCE_MATERIAL,
                (int) $material->id
            );
        } else {
            ConvertCurriculumPresentationJob::dispatch(
                CurriculumPresentationDerivative::SOURCE_MATERIAL,
                (int) $material->id
            )->onQueue((string) config('curriculum_presentation.queue', 'default'));
        }

        return $derivative->fresh();
    }

    public function ensurePendingDerivativeForMaterial(CurriculumLibraryMaterial $material): CurriculumPresentationDerivative
    {
        $disk = $material->storage_disk ?: 'r2';

        $derivative = CurriculumPresentationDerivative::query()->firstOrNew([
            'source_type' => CurriculumPresentationDerivative::SOURCE_MATERIAL,
            'source_id' => $material->id,
        ]);

        if (! $derivative->exists) {
            $derivative->fill([
                'storage_disk' => $disk,
                'status' => CurriculumPresentationDerivative::STATUS_PENDING,
                'version' => 1,
                'engine' => self::ENGINE,
                'error_message' => null,
                'manifest_path' => null,
                'slide_count' => null,
                'width' => null,
                'height' => null,
                'source_checksum' => null,
                'ready_at' => null,
            ]);
            $derivative->save();

            return $derivative;
        }

        $nextVersion = max(1, (int) $derivative->version);
        if (in_array($derivative->status, [
            CurriculumPresentationDerivative::STATUS_READY,
            CurriculumPresentationDerivative::STATUS_FAILED,
            CurriculumPresentationDerivative::STATUS_UNAVAILABLE,
            CurriculumPresentationDerivative::STATUS_STALE,
        ], true)) {
            $nextVersion++;
        }

        $derivative->fill([
            'storage_disk' => $disk,
            'status' => CurriculumPresentationDerivative::STATUS_PENDING,
            'version' => $nextVersion,
            'engine' => self::ENGINE,
            'error_message' => null,
            'ready_at' => null,
        ]);
        $derivative->save();

        return $derivative;
    }

    /**
     * @return array{soffice: ?string, pdftoppm: ?string, ready: bool, missing: list<string>}
     */
    public function detectBinaries(): array
    {
        $finder = new ExecutableFinder;

        $sofficeConfigured = (string) config('curriculum_presentation.soffice_path', 'soffice');
        $pdftoppmConfigured = (string) config('curriculum_presentation.pdftoppm_path', 'pdftoppm');

        $soffice = $this->resolveExecutable($sofficeConfigured, ['soffice', 'libreoffice'], $finder);
        $pdftoppm = $this->resolveExecutable($pdftoppmConfigured, ['pdftoppm'], $finder);

        $missing = [];
        if ($soffice === null) {
            $missing[] = 'soffice';
        }
        if ($pdftoppm === null) {
            $missing[] = 'pdftoppm';
        }

        return [
            'soffice' => $soffice,
            'pdftoppm' => $pdftoppm,
            'ready' => $soffice !== null && $pdftoppm !== null,
            'missing' => $missing,
        ];
    }

    /**
     * Convert a material presentation into derived slide artifacts.
     * MUST NOT modify, rename, overwrite, or delete CurriculumLibraryMaterial.path.
     */
    public function convertMaterial(CurriculumLibraryMaterial $material): CurriculumPresentationDerivative
    {
        if (! $this->derivativesTableReady()) {
            throw new \RuntimeException('curriculum_presentation_derivatives table is missing.');
        }

        $derivative = CurriculumPresentationDerivative::query()->firstOrCreate(
            [
                'source_type' => CurriculumPresentationDerivative::SOURCE_MATERIAL,
                'source_id' => $material->id,
            ],
            [
                'storage_disk' => $material->storage_disk ?: 'r2',
                'status' => CurriculumPresentationDerivative::STATUS_PENDING,
                'version' => 1,
                'engine' => self::ENGINE,
            ]
        );

        $sourceDiskName = $material->storage_disk ?: 'r2';
        $sourcePath = (string) $material->path;
        $sourceDisk = Storage::disk($sourceDiskName);

        // Capture original identity before any work — never write these back differently.
        $originalPath = $sourcePath;
        $originalDisk = $sourceDiskName;

        $binaries = $this->detectBinaries();
        if (! $binaries['ready']) {
            $msg = 'Conversion binaries unavailable: '.implode(', ', $binaries['missing']);
            $derivative->fill([
                'status' => CurriculumPresentationDerivative::STATUS_UNAVAILABLE,
                'error_message' => $msg,
                'engine' => self::ENGINE,
                'storage_disk' => $sourceDiskName,
            ]);
            $derivative->save();
            $this->assertSourceUntouched($material, $originalPath, $originalDisk);

            return $derivative->fresh();
        }

        if ($sourcePath === '' || ! $sourceDisk->exists($sourcePath)) {
            $derivative->fill([
                'status' => CurriculumPresentationDerivative::STATUS_FAILED,
                'error_message' => 'Source presentation object not found on storage disk.',
                'storage_disk' => $sourceDiskName,
            ]);
            $derivative->save();
            $this->assertSourceUntouched($material, $originalPath, $originalDisk);

            return $derivative->fresh();
        }

        $version = max(1, (int) $derivative->version);
        $prefix = sprintf(
            'curriculum-library/derivatives/material/%d/v%d',
            (int) $material->id,
            $version
        );

        $tempRoot = null;
        $derivativeDisk = Storage::disk($sourceDiskName);

        try {
            $derivative->fill([
                'status' => CurriculumPresentationDerivative::STATUS_PROCESSING,
                'storage_disk' => $sourceDiskName,
                'error_message' => null,
                'engine' => self::ENGINE,
            ]);
            $derivative->save();

            $tempRoot = $this->makeIsolatedTempDir((int) $material->id);
            $ext = strtolower(pathinfo($material->original_name ?: $sourcePath, PATHINFO_EXTENSION)) ?: 'pptx';
            if (! in_array($ext, ['ppt', 'pptx'], true)) {
                $ext = 'pptx';
            }
            $localSource = $tempRoot.DIRECTORY_SEPARATOR.'source.'.$ext;

            $this->downloadToLocal($sourceDisk, $sourcePath, $localSource);
            $checksum = hash_file('sha256', $localSource) ?: null;

            $pdfPath = $this->convertOfficeToPdf(
                $binaries['soffice'],
                $localSource,
                $tempRoot
            );

            $format = $this->normalizedImageFormat();
            $slidesDir = $tempRoot.DIRECTORY_SEPARATOR.'slides';
            $thumbsDir = $tempRoot.DIRECTORY_SEPARATOR.'thumbs';
            $this->ensureLocalDir($slidesDir);
            $this->ensureLocalDir($thumbsDir);

            $slideFiles = $this->pdfToImages(
                $binaries['pdftoppm'],
                $pdfPath,
                $slidesDir,
                'slide',
                (int) config('curriculum_presentation.dpi', 144),
                $format
            );
            $thumbFiles = $this->pdfToImages(
                $binaries['pdftoppm'],
                $pdfPath,
                $thumbsDir,
                'thumb',
                (int) config('curriculum_presentation.thumb_dpi', 72),
                $format
            );

            if ($slideFiles === []) {
                throw new \RuntimeException('pdftoppm produced no slide images.');
            }

            // Clean only this version prefix before uploading (never the source object).
            $this->deleteDerivedPrefix($derivativeDisk, $prefix);

            $width = null;
            $height = null;
            $slidesMeta = [];

            foreach ($slideFiles as $index => $slideLocal) {
                $n = $index + 1;
                $slideKey = $prefix.'/slides/slide-'.str_pad((string) $n, 3, '0', STR_PAD_LEFT).'.'.$format;
                $thumbLocal = $thumbFiles[$index] ?? null;
                $thumbKey = $prefix.'/thumbs/thumb-'.str_pad((string) $n, 3, '0', STR_PAD_LEFT).'.'.$format;

                $derivativeDisk->put($slideKey, file_get_contents($slideLocal), [
                    'visibility' => 'private',
                    'ContentType' => $format === 'jpeg' ? 'image/jpeg' : 'image/png',
                ]);

                if ($thumbLocal && is_file($thumbLocal)) {
                    $derivativeDisk->put($thumbKey, file_get_contents($thumbLocal), [
                        'visibility' => 'private',
                        'ContentType' => $format === 'jpeg' ? 'image/jpeg' : 'image/png',
                    ]);
                } else {
                    $thumbKey = null;
                }

                if ($width === null || $height === null) {
                    $size = @getimagesize($slideLocal);
                    if (is_array($size)) {
                        $width = (int) ($size[0] ?? 0) ?: null;
                        $height = (int) ($size[1] ?? 0) ?: null;
                    }
                }

                $slidesMeta[] = [
                    'index' => $n,
                    'path' => $slideKey,
                    'thumb' => $thumbKey,
                ];
            }

            $manifest = [
                'source_type' => CurriculumPresentationDerivative::SOURCE_MATERIAL,
                'source_id' => (int) $material->id,
                'version' => $version,
                'slide_count' => count($slidesMeta),
                'width' => $width,
                'height' => $height,
                'format' => $format,
                'dpi' => (int) config('curriculum_presentation.dpi', 144),
                'engine' => self::ENGINE,
                'source_checksum' => $checksum,
                'source_path_unchanged' => $originalPath,
                'slides' => $slidesMeta,
                'generated_at' => now()->toIso8601String(),
            ];

            $manifestPath = $prefix.'/manifest.json';
            $derivativeDisk->put($manifestPath, json_encode($manifest, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), [
                'visibility' => 'private',
                'ContentType' => 'application/json',
            ]);

            $derivative->fill([
                'storage_disk' => $sourceDiskName,
                'manifest_path' => $manifestPath,
                'status' => CurriculumPresentationDerivative::STATUS_READY,
                'slide_count' => count($slidesMeta),
                'width' => $width,
                'height' => $height,
                'version' => $version,
                'source_checksum' => $checksum,
                'error_message' => null,
                'engine' => self::ENGINE,
                'ready_at' => now(),
            ]);
            $derivative->save();

            $this->assertSourceUntouched($material, $originalPath, $originalDisk);

            return $derivative->fresh();
        } catch (Throwable $e) {
            Log::error('Curriculum presentation conversion failed', [
                'material_id' => $material->id,
                'message' => $e->getMessage(),
            ]);

            try {
                $this->deleteDerivedPrefix($derivativeDisk, $prefix);
            } catch (Throwable) {
            }

            $derivative->fill([
                'status' => CurriculumPresentationDerivative::STATUS_FAILED,
                'error_message' => mb_substr($e->getMessage(), 0, 2000),
                'engine' => self::ENGINE,
                'storage_disk' => $sourceDiskName,
                'ready_at' => null,
            ]);
            $derivative->save();

            $this->assertSourceUntouched($material, $originalPath, $originalDisk);

            throw $e;
        } finally {
            if ($tempRoot !== null) {
                $this->removeLocalTree($tempRoot);
            }
        }
    }

    /**
     * Delete derived artifacts for a material. Never touches material.path.
     */
    public function deleteDerivativesForMaterial(CurriculumLibraryMaterial $material): void
    {
        if (! $this->derivativesTableReady()) {
            return;
        }

        $derivative = CurriculumPresentationDerivative::query()
            ->where('source_type', CurriculumPresentationDerivative::SOURCE_MATERIAL)
            ->where('source_id', $material->id)
            ->first();

        if (! $derivative) {
            return;
        }

        $diskName = $derivative->storage_disk ?: ($material->storage_disk ?: 'r2');
        $disk = Storage::disk($diskName);

        // Delete all version prefixes under material id.
        $base = sprintf('curriculum-library/derivatives/material/%d', (int) $material->id);
        $this->deleteDerivedPrefix($disk, $base);

        $derivative->delete();
    }

    protected function assertSourceUntouched(CurriculumLibraryMaterial $material, string $originalPath, string $originalDisk): void
    {
        $material->refresh();
        if ((string) $material->path !== $originalPath) {
            Log::critical('Curriculum presentation conversion mutated material.path — this must never happen', [
                'material_id' => $material->id,
                'expected' => $originalPath,
                'actual' => $material->path,
            ]);
        }
        $currentDisk = $material->storage_disk ?: 'r2';
        if ($currentDisk !== $originalDisk) {
            Log::critical('Curriculum presentation conversion mutated material.storage_disk', [
                'material_id' => $material->id,
                'expected' => $originalDisk,
                'actual' => $currentDisk,
            ]);
        }
    }

    protected function resolveExecutable(string $configured, array $fallbacks, ExecutableFinder $finder): ?string
    {
        $candidates = array_values(array_unique(array_filter(array_merge([$configured], $fallbacks))));

        foreach ($candidates as $candidate) {
            if ($candidate === '') {
                continue;
            }
            if (str_contains($candidate, DIRECTORY_SEPARATOR) || str_starts_with($candidate, '/')) {
                if (is_file($candidate) && is_executable($candidate)) {
                    return $candidate;
                }

                continue;
            }
            $found = $finder->find($candidate);
            if (is_string($found) && $found !== '') {
                return $found;
            }
        }

        return null;
    }

    protected function makeIsolatedTempDir(int $materialId): string
    {
        $base = Storage::disk((string) config('curriculum_presentation.temp_disk', 'local'))
            ->path((string) config('curriculum_presentation.temp_prefix', 'curriculum-presentation-tmp'));
        $dir = $base.DIRECTORY_SEPARATOR.'m'.$materialId.'_'.Str::lower(Str::random(12));
        $this->ensureLocalDir($dir);

        return $dir;
    }

    protected function ensureLocalDir(string $dir): void
    {
        if (! is_dir($dir) && ! mkdir($dir, 0700, true) && ! is_dir($dir)) {
            throw new \RuntimeException('Unable to create temp directory: '.$dir);
        }
    }

    protected function downloadToLocal($disk, string $remotePath, string $localPath): void
    {
        $stream = $disk->readStream($remotePath);
        if ($stream === false) {
            throw new \RuntimeException('Unable to read source presentation stream.');
        }

        $out = fopen($localPath, 'wb');
        if ($out === false) {
            if (is_resource($stream)) {
                fclose($stream);
            }
            throw new \RuntimeException('Unable to open local temp file for writing.');
        }

        try {
            stream_copy_to_stream($stream, $out);
        } finally {
            fclose($out);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        if (! is_file($localPath) || filesize($localPath) <= 0) {
            throw new \RuntimeException('Downloaded source presentation is empty.');
        }
    }

    protected function convertOfficeToPdf(string $soffice, string $localSource, string $tempRoot): string
    {
        $outDir = $tempRoot.DIRECTORY_SEPARATOR.'pdf';
        $this->ensureLocalDir($outDir);

        $cmd = [
            $soffice,
            '--headless',
            '--nologo',
            '--nofirststartwizard',
            '--norestore',
            '--convert-to',
            'pdf',
            '--outdir',
            $outDir,
            $localSource,
        ];

        $this->runProcess($cmd, $tempRoot);

        $pdfs = glob($outDir.DIRECTORY_SEPARATOR.'*.pdf') ?: [];
        if ($pdfs === []) {
            throw new \RuntimeException('LibreOffice did not produce a PDF.');
        }

        return $pdfs[0];
    }

    /**
     * @return list<string> sorted local image paths
     */
    protected function pdfToImages(
        string $pdftoppm,
        string $pdfPath,
        string $outDir,
        string $prefix,
        int $dpi,
        string $format
    ): array {
        $this->ensureLocalDir($outDir);
        $outPrefix = $outDir.DIRECTORY_SEPARATOR.$prefix;

        $cmd = [
            $pdftoppm,
            '-r',
            (string) max(36, $dpi),
        ];

        if ($format === 'jpeg') {
            $cmd[] = '-jpeg';
            $quality = (int) config('curriculum_presentation.image_quality', 85);
            $cmd[] = '-jpegopt';
            $cmd[] = 'quality='.max(1, min(100, $quality));
        } else {
            $cmd[] = '-png';
        }

        $cmd[] = $pdfPath;
        $cmd[] = $outPrefix;

        $this->runProcess($cmd, $outDir);

        $pattern = $outPrefix.'*.'.($format === 'jpeg' ? 'jpg' : 'png');
        $files = glob($pattern) ?: [];
        if ($files === [] && $format === 'jpeg') {
            $files = glob($outPrefix.'*.jpeg') ?: [];
        }
        natcasesort($files);

        return array_values($files);
    }

    /**
     * @param  list<string>  $command
     */
    protected function runProcess(array $command, string $cwd): void
    {
        $timeout = (float) config('curriculum_presentation.timeout_seconds', 600);

        if (class_exists(Process::class)) {
            $process = new Process($command, $cwd, null, null, $timeout);
            $process->run();
            if (! $process->isSuccessful()) {
                $err = trim($process->getErrorOutput() ?: $process->getOutput());
                throw new \RuntimeException('Process failed ('.$command[0].'): '.mb_substr($err, 0, 1500));
            }

            return;
        }

        $this->runViaProcOpen($command, $cwd, $timeout);
    }

    /**
     * @param  list<string>  $command
     */
    protected function runViaProcOpen(array $command, string $cwd, float $timeout): void
    {
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $cmdLine = implode(' ', array_map('escapeshellarg', $command));
        $process = proc_open($cmdLine, $descriptors, $pipes, $cwd);
        if (! is_resource($process)) {
            throw new \RuntimeException('Unable to start process: '.$command[0]);
        }

        fclose($pipes[0]);
        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);

        $stdout = '';
        $stderr = '';
        $start = microtime(true);

        while (true) {
            $stdout .= stream_get_contents($pipes[1]) ?: '';
            $stderr .= stream_get_contents($pipes[2]) ?: '';
            $status = proc_get_status($process);
            if (! $status['running']) {
                break;
            }
            if ((microtime(true) - $start) > $timeout) {
                proc_terminate($process, 9);
                fclose($pipes[1]);
                fclose($pipes[2]);
                proc_close($process);
                throw new \RuntimeException('Process timed out: '.$command[0]);
            }
            usleep(50000);
        }

        $stdout .= stream_get_contents($pipes[1]) ?: '';
        $stderr .= stream_get_contents($pipes[2]) ?: '';
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exit = proc_close($process);

        if ($exit !== 0) {
            throw new \RuntimeException('Process failed ('.$command[0].'): '.mb_substr(trim($stderr ?: $stdout), 0, 1500));
        }
    }

    protected function normalizedImageFormat(): string
    {
        $format = strtolower((string) config('curriculum_presentation.image_format', 'png'));

        return in_array($format, ['jpg', 'jpeg'], true) ? 'jpeg' : 'png';
    }

    protected function deleteDerivedPrefix($disk, string $prefix): void
    {
        $prefix = rtrim($prefix, '/');
        if ($prefix === '' || ! str_starts_with($prefix, 'curriculum-library/derivatives/')) {
            return;
        }

        try {
            $disk->deleteDirectory($prefix);
        } catch (Throwable $e) {
            // Fallback: delete files under prefix if deleteDirectory unsupported.
            try {
                $files = $disk->allFiles($prefix);
                if ($files !== []) {
                    $disk->delete($files);
                }
            } catch (Throwable) {
                Log::warning('Failed cleaning derived prefix', [
                    'prefix' => $prefix,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }

    protected function removeLocalTree(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $items = scandir($dir);
        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir.DIRECTORY_SEPARATOR.$item;
            if (is_dir($path)) {
                $this->removeLocalTree($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }
}
