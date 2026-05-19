<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminPanelBranding
{
    public const SETTING_KEY = 'admin_panel_logo_path';

    /**
     * قرص التخزين: public محلي، أو r2 لـ Cloudflare R2.
     */
    public static function resolvedDisk(): string
    {
        $d = (string) config('filesystems.admin_branding_disk', 'public');

        if ($d === 'r2') {
            $bucket = config('filesystems.disks.r2.bucket');
            $endpoint = config('filesystems.disks.r2.endpoint');
            if (empty($bucket) || empty($endpoint)) {
                Log::warning('ADMIN_BRANDING_DISK=r2 لكن إعدادات R2 غير مكتملة؛ يُستخدم القرص public.');

                return 'public';
            }
        }

        if ($d === 's3') {
            $bucket = config('filesystems.disks.s3.bucket');
            if (empty($bucket)) {
                return 'public';
            }
        }

        if (! in_array($d, ['public', 'r2', 's3'], true)) {
            return 'public';
        }

        return $d;
    }

    /**
     * رابط عرض الشعار. للقرص المحلي يُفضّل مضيف الطلب الحالي لتفادي كسر الرابط بين localhost و 127.0.0.1.
     */
    public static function logoPublicUrl(): ?string
    {
        $path = Setting::getValue(self::SETTING_KEY);
        if (is_string($path) && $path !== '') {
            $path = str_replace('\\', '/', ltrim($path, '/'));
            $disk = self::resolvedDisk();

            if (Storage::disk($disk)->exists($path)) {
                return $disk === 'public'
                    ? self::publicStorageUrl($path)
                    : Storage::disk($disk)->url($path);
            }

            if ($disk !== 'public' && Storage::disk('public')->exists($path)) {
                return self::publicStorageUrl($path);
            }
        }

        $defaultPath = \App\Providers\AppServiceProvider::SITE_LOGO_STORAGE_PATH;
        if (PublicStorageUrl::publicDiskHasFile($defaultPath)) {
            return self::publicStorageUrl($defaultPath);
        }

        return asset('logo-removebg-preview.png');
    }

    private static function publicStorageUrl(string $path): string
    {
        return PublicStorageUrl::fromPath($path, self::resolvedDisk()) ?? PublicStorageUrl::localWebUrl($path);
    }

    public static function removeLogo(): void
    {
        $path = Setting::getValue(self::SETTING_KEY);
        if (is_string($path) && $path !== '') {
            self::deletePhysicalFile($path);
        }
        Setting::setValue(self::SETTING_KEY, null);
    }

    public static function storeLogo(UploadedFile $file): void
    {
        $disk = self::resolvedDisk();

        $ext = match ($file->getMimeType()) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'png'),
        };
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            $ext = 'png';
        }

        $name = 'admin-panel-logo.'.$ext;
        $oldPath = Setting::getValue(self::SETTING_KEY);

        if ($disk === 'public') {
            Storage::disk('public')->makeDirectory('site');
            $stored = $file->storeAs('site', $name, 'public');
        } else {
            $stored = Storage::disk($disk)->putFileAs('site', $file, $name, 'public');
        }

        if (! is_string($stored) || $stored === '') {
            throw new \RuntimeException('فشل حفظ ملف الشعار.');
        }

        Setting::setValue(self::SETTING_KEY, $stored);

        if (is_string($oldPath) && $oldPath !== '' && $oldPath !== $stored) {
            self::deletePhysicalFile($oldPath);
        }
    }

    private static function deletePhysicalFile(string $path): void
    {
        $path = str_replace('\\', '/', ltrim($path, '/'));
        foreach (array_unique([self::resolvedDisk(), 'public']) as $d) {
            if (! in_array($d, ['public', 'r2', 's3'], true)) {
                continue;
            }
            if (Storage::disk($d)->exists($path)) {
                Storage::disk($d)->delete($path);
            }
        }
    }
}
