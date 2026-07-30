<?php

namespace App\Console\Commands;

use App\Models\CourseLesson;
use App\Services\CourseVideoStorage;
use App\Services\PublicMediaStorage;
use App\Services\PublicStorageUrl;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateCourseVideosToR2 extends Command
{
    protected $signature = 'media:migrate-videos-to-r2
                            {--dry-run : عرض دون رفع}
                            {--upload-sample : رفع فيديو اختباري صغير إلى R2 والتحقق}';

    protected $description = 'ترحيل فيديوهات course-videos من التخزين المحلي إلى Cloudflare R2';

    public function handle(): int
    {
        if (CourseVideoStorage::resolvedDisk() !== 'r2') {
            $this->error('PUBLIC_MEDIA_DISK ليس r2. اضبط .env ثم php artisan config:clear');

            return self::FAILURE;
        }

        if ($this->option('upload-sample')) {
            return $this->uploadSample();
        }

        $dryRun = (bool) $this->option('dry-run');
        $base = storage_path('app/public/'.CourseVideoStorage::DIRECTORY);
        $uploaded = 0;
        $skipped = 0;
        $failed = 0;
        $updatedDb = 0;

        if (is_dir($base)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS)
            );
            foreach ($iterator as $file) {
                if (! $file->isFile()) {
                    continue;
                }
                $relative = CourseVideoStorage::DIRECTORY.'/'.$file->getFilename();
                if ($dryRun) {
                    $this->line(' '.$relative);
                    $skipped++;

                    continue;
                }
                if (Storage::disk('r2')->exists($relative)) {
                    $skipped++;

                    continue;
                }
                if (CourseVideoStorage::mirrorLocalToR2($relative)) {
                    $uploaded++;
                    $this->info("رُفع: {$relative}");
                } else {
                    $failed++;
                    $this->warn("فشل: {$relative}");
                }
            }
        } else {
            $this->comment('لا يوجد مجلد محلي course-videos');
        }

        // تطبيع video_url في قاعدة البيانات من /storage/... إلى مسار نسبي
        $lessons = CourseLesson::query()
            ->whereNotNull('video_url')
            ->where('video_url', '!=', '')
            ->get(['id', 'video_url']);

        foreach ($lessons as $lesson) {
            $raw = (string) $lesson->video_url;
            if (! CourseVideoStorage::looksLikeStoredMediaUrl($raw) && ! str_starts_with(ltrim($raw, '/'), CourseVideoStorage::DIRECTORY.'/')) {
                continue;
            }
            $relative = CourseVideoStorage::normalizeStoredPath($raw);
            if ($relative === '' || $relative === $raw) {
                continue;
            }
            if ($dryRun) {
                $this->line(" DB #{$lesson->id}: {$raw} → {$relative}");
                continue;
            }
            $lesson->video_url = $relative;
            $lesson->save();
            $updatedDb++;
        }

        $this->newLine();
        $this->info("رفع {$uploaded} | تخطي {$skipped} | فشل {$failed} | تحديث DB {$updatedDb}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function uploadSample(): int
    {
        $this->info('رفع فيديو اختباري إلى Cloudflare R2…');

        $tmp = storage_path('app/course-video-sample.mp4');
        // أصغر ملف MP4 صالح تقريباً (ftyp+mdat فارغ تقريباً) — للتحقق من الرفع فقط
        $bytes = base64_decode(
            'AAAAIGZ0eXBpc29tAAACAGlzb21pc28yYXZjMW1wNDEAAAAIZnJlZQAAAv1tZGF0AAACrgYF'.
            'U5WUDwAAAABkYXRhAAAAAA=='
        );
        if ($bytes === false || $bytes === '') {
            $this->error('تعذر تجهيز ملف العينة');

            return self::FAILURE;
        }
        file_put_contents($tmp, $bytes);

        try {
            $uploaded = new \Illuminate\Http\UploadedFile(
                $tmp,
                'course-video-sample.mp4',
                'video/mp4',
                null,
                true
            );
            $path = CourseVideoStorage::store($uploaded);
            $exists = Storage::disk('r2')->exists($path);
            $url = CourseVideoStorage::publicUrl($path) ?? PublicStorageUrl::fromPath($path, 'r2');

            $this->info('path: '.$path);
            $this->info('exists_on_r2: '.($exists ? 'yes' : 'no'));
            $this->info('playback_url: '.($url ?: '(none)'));

            if (! $exists) {
                $this->error('الملف غير موجود على R2 بعد الرفع');

                return self::FAILURE;
            }

            $this->comment('تم رفع العينة بنجاح. يمكنك حذفها لاحقاً من bucket إن رغبت.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        } finally {
            if (is_file($tmp)) {
                @unlink($tmp);
            }
        }
    }
}
