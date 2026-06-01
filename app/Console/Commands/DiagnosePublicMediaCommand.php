<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Services\AdminPanelBranding;
use App\Services\PlatformMediaSettings;
use App\Services\PublicMediaStorage;
use App\Services\PublicStorageUrl;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DiagnosePublicMediaCommand extends Command
{
    protected $signature = 'media:diagnose {--sample=courses : مسار عينة للاختبار}';

    protected $description = 'فحص إعدادات R2 وروابط الصور (للاستضافة الإنتاجية)';

    public function handle(): int
    {
        $this->info('APP_URL: '.config('app.url'));
        $this->info('PUBLIC_MEDIA_DISK: '.PublicMediaStorage::resolvedDisk());
        $this->info('ADMIN_BRANDING_DISK: '.AdminPanelBranding::resolvedDisk());
        $this->line('R2_PUBLIC_URL (.env): '.(config('filesystems.r2_public_url') ?: '(غير مضبوط)'));
        $this->line('R2 من إعدادات النظام: '.(PlatformMediaSettings::r2PublicBaseUrl() ?: '(غير مضبوط)'));
        $this->line('بروكسي الوسائط: /'.PublicStorageUrl::PROXY_PATH.'/…');
        $this->line('AWS_URL: '.(config('filesystems.disks.r2.url') ?: '(فارغ)'));
        $this->line('AWS_ENDPOINT: '.(config('filesystems.disks.r2.endpoint') ?: '(فارغ)'));
        $this->line('AWS_BUCKET: '.(config('filesystems.disks.r2.bucket') ?: '(فارغ)'));

        $publicBase = PublicStorageUrl::cloudPublicBaseUrl('r2');
        if ($publicBase) {
            $this->info('رابط R2 العام للمتصفح: '.$publicBase);
        } else {
            $this->warn('لا يوجد رابط R2 عام — الصور ستُمرَّر عبر /storage/ أو روابط موقّعة (أبطأ).');
            $this->warn('فعّل Public access على الـ bucket وأضف R2_PUBLIC_URL=https://pub-xxx.r2.dev');
        }

        $logoPath = Setting::getValue(AdminPanelBranding::SETTING_KEY);
        $this->line('مسار الشعار في الإعدادات: '.($logoPath ?: '(افتراضي site/logo.png)'));
        AdminPanelBranding::forgetLogoUrlCache();
        $logoUrl = AdminPanelBranding::logoPublicUrl();
        $this->info('رابط الشعار: '.($logoUrl ?: '(لا يوجد — سيُستخدم شعار احتياطي)'));
        if (is_string($logoUrl) && str_contains($logoUrl, 'cloudflarestorage.com')) {
            $this->error('رابط الشعار يشير إلى cloudflarestorage.com — لن يظهر في المتصفح. اضبط R2_PUBLIC_URL.');
        }
        if (is_string($logoUrl) && str_starts_with($logoUrl, 'data:')) {
            $this->comment('يُستخدم الشعار الاحتياطي المضمّن (تحقق من R2_PUBLIC_URL ومسار الملف).');
        }

        $sample = (string) $this->option('sample');
        $sample = str_replace('\\', '/', ltrim($sample, '/'));
        try {
            $exists = Storage::disk('r2')->exists($sample);
            $this->line("ملف عينة [{$sample}] على R2: ".($exists ? 'موجود' : 'غير موجود'));
            if ($exists) {
                $this->info('رابط العينة: '.PublicStorageUrl::fromPath($sample));
            }
        } catch (\Throwable $e) {
            $this->error('فشل الاتصال بـ R2: '.$e->getMessage());
        }

        $this->newLine();
        $this->comment('بعد تعديل .env على السيرفر: php artisan config:clear && php artisan cache:clear');

        return self::SUCCESS;
    }
}
