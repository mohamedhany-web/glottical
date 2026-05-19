<?php

if (!function_exists('community_disk')) {
    /**
     * قرص تخزين ملفات المجتمع (تقديمات المساهمين).
     * يُفضّل القراءة من .env إن وُجدت لتجنب مشكلة كاش الإعدادات.
     *
     * @return string 'r2' أو 'local'
     */
    function community_disk(): string
    {
        $envDisk = env('FILESYSTEM_DISK_COMMUNITY');
        if ($envDisk !== null && $envDisk !== '' && in_array($envDisk, ['r2', 'local'], true)) {
            return $envDisk;
        }
        return config('filesystems.community_disk', 'local');
    }
}

if (! function_exists('storage_public_url')) {
    /**
     * رابط عرض ملف من storage/app/public أو R2 عبر /storage/...
     */
    function storage_public_url(?string $path, ?string $preferredDisk = null): ?string
    {
        return \App\Services\PublicStorageUrl::fromPath($path, $preferredDisk);
    }
}
