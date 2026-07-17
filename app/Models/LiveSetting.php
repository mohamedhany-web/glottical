<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LiveSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'label'];

    /**
     * تطبيع نطاق Jitsi: إزالة البروتوكول والشرطة الأخيرة لاستخدام النطاق فقط (مثل meet.glottical.com).
     * ضروري لأن السكربت يُحمّل من https://النطاق/external_api.js
     */
    public static function normalizeJitsiDomain(string $domain): string
    {
        $domain = trim($domain);
        $domain = preg_replace('#^https?://#i', '', $domain);
        $domain = rtrim($domain, '/');
        return $domain;
    }

    /**
     * نطاق Jitsi المستخدم في الميتينج: الإعداد المحفوظ (النطاق الافتراضي من سيرفرات البث) أولاً،
     * ثم أول سيرفر نشط إن لم يكن هناك إعداد، وأخيراً meet.jit.si للاختبار فقط.
     * يُرجع النطاق مُطبّعاً (بدون https://) لاستخدامه في تحميل السكربت.
     */
    public static function getJitsiDomain(): string
    {
        $domain = trim((string) static::get('jitsi_domain', ''));
        if ($domain !== '') {
            return static::normalizeJitsiDomain($domain);
        }
        $server = LiveServer::where('status', 'active')->first();
        if ($server && trim($server->domain) !== '') {
            return static::normalizeJitsiDomain($server->domain);
        }
        return 'meet.jit.si';
    }

    public static function get(string $key, $default = null)
    {
        $setting = Cache::remember("live_setting_{$key}", 3600, function () use ($key) {
            return static::where('key', $key)->first();
        });

        if (!$setting) return $default;

        return match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            'json'    => json_decode($setting->value, true),
            default   => $setting->value,
        };
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value) : (string) $value]
        );
        Cache::forget("live_setting_{$key}");
    }

    public static function getByGroup(string $group)
    {
        return static::where('group', $group)->get();
    }
}
