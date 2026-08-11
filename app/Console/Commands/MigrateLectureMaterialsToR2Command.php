<?php

namespace App\Console\Commands;

use App\Models\LectureMaterial;
use App\Services\LectureMaterialStorage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class MigrateLectureMaterialsToR2Command extends Command
{
    protected $signature = 'materials:migrate-to-r2 {--delete-local : حذف النسخة المحلية بعد نجاح الرفع}';

    protected $description = 'ترحيل ملفات مكتبة الماتريال من التخزين المحلي إلى Cloudflare R2';

    public function handle(): int
    {
        $disk = LectureMaterialStorage::resolvedDisk();
        if ($disk !== 'r2' && $disk !== 's3') {
            $this->error('LECTURE_MATERIALS_DISK / PUBLIC_MEDIA_DISK ليس r2. اضبط .env ثم أعد المحاولة.');

            return self::FAILURE;
        }

        if (! Schema::hasTable('lecture_materials')) {
            $this->warn('جدول lecture_materials غير موجود.');

            return self::SUCCESS;
        }

        $query = LectureMaterial::query()->whereNotNull('file_path')->where('file_path', '!=', '');
        if (Schema::hasColumn('lecture_materials', 'storage_disk')) {
            $query->where(function ($q) {
                $q->whereNull('storage_disk')
                    ->orWhere('storage_disk', 'public')
                    ->orWhere('storage_disk', '');
            });
        }

        $total = $query->count();
        $this->info("الملفات المرشحة: {$total} → {$disk}");

        $ok = 0;
        $fail = 0;

        $query->orderBy('id')->chunkById(50, function ($materials) use (&$ok, &$fail, $disk) {
            foreach ($materials as $material) {
                $path = (string) $material->file_path;
                if (! Storage::disk('public')->exists($path)) {
                    // ربما مرفوع مسبقاً على السحابة
                    if (Storage::disk($disk)->exists($path)) {
                        $material->update(['storage_disk' => $disk]);
                        $ok++;
                        $this->line("✓ #{$material->id} محدّث (موجود على {$disk})");
                    } else {
                        $fail++;
                        $this->warn("✗ #{$material->id} غير موجود محلياً ولا على {$disk}: {$path}");
                    }

                    continue;
                }

                if (LectureMaterialStorage::mirrorLocalToCloud($path)) {
                    $material->update(['storage_disk' => $disk]);
                    if ($this->option('delete-local')) {
                        try {
                            Storage::disk('public')->delete($path);
                        } catch (\Throwable) {
                        }
                    }
                    $ok++;
                    $this->line("✓ #{$material->id} → {$disk}");
                } else {
                    $fail++;
                    $this->warn("✗ #{$material->id} فشل الترحيل: {$path}");
                }
            }
        });

        $this->info("انتهى: نجح {$ok} / فشل {$fail}");

        return $fail > 0 ? self::FAILURE : self::SUCCESS;
    }
}
