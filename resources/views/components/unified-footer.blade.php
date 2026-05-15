{{-- نفس فوتر الصفحة الرئيسية (إعدادات النظام) — للصفحات التي تستخدم layouts.public أو صفحات مستقلة --}}
@include('partials.public-site-footer', [
    'footerExtraClass' => trim(($footerExtraClass ?? '') . ' mt-auto shrink-0'),
    'footerVariant' => $footerVariant ?? 'stream',
])
