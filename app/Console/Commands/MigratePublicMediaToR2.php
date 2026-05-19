<?php

namespace App\Console\Commands;

use App\Services\PublicMediaStorage;
use App\Services\PublicStorageUrl;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigratePublicMediaToR2 extends Command
{
    protected $signature = 'media:migrate-to-r2
                            {--dry-run : عرض الملفات دون رفع}
                            {--path= : مجلد محدد داخل storage/app/public مثل courses}';

    protected $description = 'رفع ملفات storage/app/public إلى Cloudflare R2 (عند PUBLIC_MEDIA_DISK=r2)';

    public function handle(): int
    {
        if (PublicMediaStorage::resolvedDisk() !== 'r2') {
            $this->error('PUBLIC_MEDIA_DISK ليس r2. اضبط .env ثم php artisan config:clear');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $onlyPath = $this->option('path');

        $base = storage_path('app/public');
        if (! is_dir($base)) {
            $this->warn('لا يوجد مجلد storage/app/public');

            return self::SUCCESS;
        }

        $files = $this->collectFiles($base, $onlyPath ? (string) $onlyPath : null);
        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        $uploaded = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($files as $relative) {
            $relative = PublicMediaStorage::normalizePath($relative);
            if ($dryRun) {
                $this->line(' '.$relative);
                $skipped++;

                continue;
            }

            if (Storage::disk('r2')->exists($relative)) {
                $skipped++;
                $bar->advance();

                continue;
            }

            if (PublicMediaStorage::mirrorLocalToR2($relative)) {
                $uploaded++;
            } else {
                $failed++;
                $this->newLine();
                $this->warn("فشل: {$relative}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("تم: رفع {$uploaded} | موجود مسبقاً/تخطي {$skipped} | فشل {$failed}");

        if (! $dryRun && $uploaded > 0) {
            $this->comment('تحقق من رابط عينة:');
            $sample = $files[0] ?? 'courses';
            $this->line(PublicStorageUrl::fromPath(is_string($sample) ? $sample : 'courses/test.jpg'));
        }

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function collectFiles(string $base, ?string $prefix): array
    {
        $paths = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $basename = $file->getBasename();
            if ($basename === '.gitignore' || str_starts_with($basename, '.')) {
                continue;
            }

            $full = $file->getPathname();
            $relative = PublicMediaStorage::normalizePath(substr($full, strlen($base) + 1));

            if ($prefix !== null && $prefix !== '' && ! str_starts_with($relative, trim($prefix, '/').'/') && $relative !== trim($prefix, '/')) {
                continue;
            }

            $paths[] = $relative;
        }

        sort($paths);

        return $paths;
    }
}
