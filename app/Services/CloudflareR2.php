<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * جاهزية قرص Cloudflare R2 من إعدادات filesystems (وليس env() بعد config:cache).
 */
class CloudflareR2
{
    /**
     * @return list<string>
     */
    public static function missingFields(): array
    {
        $cfg = config('filesystems.disks.r2', []);
        $missing = [];
        foreach (['key' => 'AWS_ACCESS_KEY_ID', 'secret' => 'AWS_SECRET_ACCESS_KEY', 'bucket' => 'AWS_BUCKET', 'endpoint' => 'AWS_ENDPOINT'] as $field => $env) {
            if (trim((string) ($cfg[$field] ?? '')) === '') {
                $missing[] = $env;
            }
        }

        return $missing;
    }

    public static function isReady(): bool
    {
        return self::missingFields() === [];
    }

    /**
     * @param  'r2'|'s3'|'public'|'local'|string  $preferred
     */
    public static function resolveDisk(string $preferred = 'r2'): string
    {
        $preferred = strtolower(trim($preferred));
        if ($preferred === '' || $preferred === '0') {
            $preferred = 'r2';
        }

        if ($preferred === 'r2') {
            if (self::isReady()) {
                return 'r2';
            }

            Log::warning('Cloudflare R2 is not ready; falling back to public disk.', [
                'missing' => self::missingFields(),
            ]);

            return 'public';
        }

        if ($preferred === 's3') {
            $bucket = trim((string) config('filesystems.disks.s3.bucket', ''));

            return $bucket !== '' ? 's3' : 'public';
        }

        if (in_array($preferred, ['public', 'local'], true)) {
            return $preferred === 'local' ? 'local' : 'public';
        }

        return self::isReady() ? 'r2' : 'public';
    }
}
