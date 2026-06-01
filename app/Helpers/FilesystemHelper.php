<?php

if (! function_exists('community_disk')) {
    /**
     * قرص تخزين ملفات المجتمع (تقديمات المساهمين).
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
     * رابط عرض ملف من storage/app/public أو R2.
     */
    function storage_public_url(?string $path, ?string $preferredDisk = null): ?string
    {
        return \App\Services\PublicStorageUrl::fromPath($path, $preferredDisk);
    }
}

if (! function_exists('storage_asset')) {
    /**
     * بديل asset('storage/...') — يحترم مجلد التطبيق الفرعي ونفس نطاق الطلب.
     */
    function storage_asset(?string $path): ?string
    {
        return storage_public_url($path);
    }
}

if (! function_exists('storage_base_url')) {
    /**
     * قاعدة روابط التخزين للاستخدام في JavaScript: storage_base_url() + '/' + path
     */
    function storage_base_url(): string
    {
        return rtrim(\App\Support\ApplicationUrl::resolveRootUrl(), '/').'/'.\App\Services\PublicStorageUrl::PROXY_PATH;
    }
}
