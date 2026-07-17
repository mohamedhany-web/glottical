<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$t0 = microtime(true);
$req = Illuminate\Http\Request::create('/', 'GET');
$resp = $kernel->handle($req);
$t1 = microtime(true);
$html = $resp->getContent();

$checks = [
    'status_200' => $resp->getStatusCode() === 200,
    'seo_robots' => str_contains($html, 'index, follow'),
    'seo_twitter' => str_contains($html, 'twitter:card'),
    'seo_canonical' => str_contains($html, 'rel="canonical"'),
    'seo_hreflang_default' => str_contains($html, 'hreflang="x-default"'),
    'og_image_exists' => str_contains($html, '/images/og-image.jpg'),
    'jsonld_webpage' => str_contains($html, 'WebPage'),
    'single_h1' => substr_count(strtolower($html), '<h1') === 1,
    'free_trial_modal' => str_contains($html, 'free-trial-modal'),
    'fonts_slim' => str_contains($html, 'Cairo:wght@400;600;700;800') && ! str_contains($html, 'Tajawal'),
    'hero_lazy_others' => str_contains($html, 'loading="lazy"'),
];

foreach ($checks as $k => $ok) {
    echo ($ok ? 'OK ' : 'FAIL ').$k.PHP_EOL;
}

echo 'ttfb_ms='.round(($t1 - $t0) * 1000).PHP_EOL;
echo 'html_kb='.round(strlen($html) / 1024).PHP_EOL;

// second hit should be cached
$t2 = microtime(true);
$resp2 = $kernel->handle(Illuminate\Http\Request::create('/', 'GET'));
$t3 = microtime(true);
echo 'cached_ttfb_ms='.round(($t3 - $t2) * 1000).' status='.$resp2->getStatusCode().PHP_EOL;
echo 'og_filesize='.(is_file(public_path('images/og-image.jpg')) ? filesize(public_path('images/og-image.jpg')) : 0).PHP_EOL;

$kernel->terminate($req, $resp);
