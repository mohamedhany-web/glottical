<?php
/**
 * Remap previous Glottical blue/yellow → new purple/cream platform palette.
 * php public/css/landing/_rebrand.php
 *
 * Palette: #4B3A78 · #B77CFF · #FFFBE6 · #C9FFD8 · #FFB7A5
 */
declare(strict_types=1);

$dir = __DIR__;
$files = glob($dir . '/*.css') ?: [];

$map = [
    // Primary blues → dark purple family
    '#0B3D91' => '#4B3A78',
    '#0b3d91' => '#4B3A78',
    '#072A66' => '#3A2C5C',
    '#072a66' => '#3A2C5C',
    '#051F4D' => '#2E234A',
    '#051f4d' => '#2E234A',
    '#051E4A' => '#2E234A',
    '#051e4a' => '#2E234A',
    '#1A56B0' => '#5C4A8C',
    '#1a56b0' => '#5C4A8C',
    '#3D6BC4' => '#B77CFF',
    '#3d6bc4' => '#B77CFF',
    '#6B8FD4' => '#C9A0FF',
    '#6b8fd4' => '#C9A0FF',
    '#0997d9' => '#B77CFF',
    '#E8EEF8' => '#F3E9FF',
    '#e8eef8' => '#F3E9FF',
    '#EEF3FB' => '#F6F0FF',
    '#eef3fb' => '#F6F0FF',
    '#C5D4F0' => '#E2D0FF',
    '#c5d4f0' => '#E2D0FF',
    '#D6E2F5' => '#E2D0FF',
    '#d6e2f5' => '#E2D0FF',
    '#F4F7FC' => '#FFFBE6',
    '#f4f7fc' => '#FFFBE6',
    '#F3F5F7' => '#FFFBE6',
    '#f3f5f7' => '#FFFBE6',

    // Gold → peach warm accent
    '#F5B800' => '#FFB7A5',
    '#f5b800' => '#FFB7A5',
    '#D99E00' => '#F59A84',
    '#d99e00' => '#F59A84',
    '#E5AB00' => '#F59A84',
    '#e5ab00' => '#F59A84',
    '#C99400' => '#E88970',
    '#c99400' => '#E88970',
    '#FFD24D' => '#FFCDBE',
    '#ffd24d' => '#FFCDBE',
    '#FFE9A8' => '#FFE4DC',
    '#ffe9a8' => '#FFE4DC',
    '#FFF6D6' => '#FFF0EB',
    '#fff6d6' => '#FFF0EB',
    '#FFF8E6' => '#FFFBE6',
    '#fff8e6' => '#FFFBE6',

    // Ink
    '#0B1220' => '#2E234A',
    '#0b1220' => '#2E234A',

    // rgba blue/gold families
    'rgba(11,61,145,' => 'rgba(75,58,120,',
    'rgba(11, 61, 145,' => 'rgba(75, 58, 120,',
    'rgba(7,42,102,' => 'rgba(58,44,92,',
    'rgba(7, 42, 102,' => 'rgba(58, 44, 92,',
    'rgba(5,31,77,' => 'rgba(46,35,74,',
    'rgba(5, 31, 77,' => 'rgba(46, 35, 74,',
    'rgba(107,143,212,' => 'rgba(183,124,255,',
    'rgba(107, 143, 212,' => 'rgba(183, 124, 255,',
    'rgba(245,184,0,' => 'rgba(255,183,165,',
    'rgba(245, 184, 0,' => 'rgba(255, 183, 165,',
    'rgba(11,18,32,' => 'rgba(46,35,74,',
    'rgba(11, 18, 32,' => 'rgba(46, 35, 74,',
];

$rootBanner = "    /* Glottical brand: #4B3A78 · #B77CFF · #FFFBE6 · #C9FFD8 · #FFB7A5 */";

foreach ($files as $file) {
    if (str_ends_with($file, '_rebrand.php')) {
        continue;
    }
    $css = (string) file_get_contents($file);
    $out = strtr($css, $map);
    $out = preg_replace(
        '/\/\*\s*Glottical brand:[^*]*\*\//',
        '/* Glottical brand: #4B3A78 · #B77CFF · #FFFBE6 · #C9FFD8 · #FFB7A5 */',
        $out,
        1
    ) ?? $out;
    if (! str_contains($out, '#4B3A78') && str_contains($out, ':root')) {
        $out = preg_replace(
            '/:root\s*\{/',
            ":root {\n".$rootBanner,
            $out,
            1
        ) ?? $out;
    }
    file_put_contents($file, $out);
    echo basename($file)." OK\n";
}

// Mirror into resources/css/landing when present
$resDir = dirname(__DIR__, 3).'/resources/css/landing';
if (is_dir($resDir)) {
    foreach (glob($resDir.'/*.css') ?: [] as $file) {
        $css = (string) file_get_contents($file);
        $out = strtr($css, $map);
        $out = preg_replace(
            '/\/\*\s*Glottical brand:[^*]*\*\//',
            '/* Glottical brand: #4B3A78 · #B77CFF · #FFFBE6 · #C9FFD8 · #FFB7A5 */',
            $out,
            1
        ) ?? $out;
        file_put_contents($file, $out);
        echo 'resources/'.basename($file)." OK\n";
    }
}

echo "Done\n";
