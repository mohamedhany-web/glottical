<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * حساب جذر التطبيق (APP_URL) من الطلب الفعلي — ضروري عند التشغيل داخل مجلد فرعي على XAMPP.
 */
class ApplicationUrl
{
    public static function resolveRootUrl(?Request $request = null): string
    {
        $request ??= request();

        if ($request) {
            $script = $request->server('SCRIPT_NAME') ?: $request->getScriptName();
            if (is_string($script) && $script !== '' && str_ends_with($script, '/index.php')) {
                $dir = str_replace('\\', '/', dirname($script));
                if ($dir !== '/' && $dir !== '' && $dir !== '.') {
                    return rtrim($request->getSchemeAndHttpHost().$dir, '/');
                }
            }
        }

        $configured = rtrim((string) config('app.url'), '/');
        if ($configured !== '') {
            return $configured;
        }

        return $request ? rtrim($request->getSchemeAndHttpHost(), '/') : '';
    }
}
