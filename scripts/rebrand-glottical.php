<?php

/**
 * Replace legacy Muallimx branding in source files (not vendor/storage).
 * Usage: php scripts/rebrand-glottical.php
 */

$root = dirname(__DIR__);
$extensions = ['php', 'blade.php', 'js', 'json', 'md', 'sql'];
$skipDirs = ['vendor', 'node_modules', 'storage', '.git'];
$skipFiles = [
    'scripts/rebrand-glottical.php',
    'app/Services/MuallimxAiClient.php',
    'config/muallimx_ai.php',
];

$replacements = [
    "config('app.name', 'Muallimx')" => "config('app.name')",
    'config("app.name", "Muallimx")' => 'config("app.name")',
    "config('app.name', 'Glottical')" => "config('app.name')",
    'config("app.name", "Glottical")' => 'config("app.name")',
    'Muallimx Academy' => 'Glottical Academy',
    'Muallimx Classroom' => 'Glottical Classroom',
    'Muallimx AI' => 'Glottical AI',
    'Muallimx —' => 'Glottical —',
    'منصة Muallimx' => 'منصة Glottical',
    'منصة Muallimx' => 'منصة Glottical',
    'معرض Muallimx' => 'معرض Glottical',
    'فريق Muallimx' => 'فريق Glottical',
    'Muallimx administration' => 'Glottical administration',
    'إدارة Muallimx' => 'إدارة Glottical',
    'Muallimx' => 'Glottical',
    "'Muallimx'" => "'Glottical'",
    '"Muallimx"' => '"Glottical"',
    'muallimx-shell-v' => 'glottical-shell-v',
    'muallimx-board-' => 'glottical-board-',
    'Muallimx-' => 'Glottical-',
];

$changed = 0;
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if (! $file->isFile()) {
        continue;
    }

    $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
    foreach ($skipDirs as $skip) {
        if (str_starts_with($relative, $skip.'/') || $relative === $skip) {
            continue 2;
        }
    }
    if (in_array($relative, $skipFiles, true)) {
        continue;
    }

    $ext = $file->getExtension();
    if ($ext === 'php' && str_ends_with($relative, '.blade.php')) {
        $ext = 'blade.php';
    }
    if (! in_array($ext, $extensions, true) && ! str_ends_with($relative, '.blade.php')) {
        continue;
    }

    $content = file_get_contents($file->getPathname());
    $original = $content;
    foreach ($replacements as $from => $to) {
        $content = str_replace($from, $to, $content);
    }

    if ($content !== $original) {
        file_put_contents($file->getPathname(), $content);
        $changed++;
        echo "updated: $relative\n";
    }
}

echo "Done. $changed files updated.\n";
