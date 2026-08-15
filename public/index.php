<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

register_shutdown_function(static function () use ($app): void {
    $lastError = error_get_last();
    if (!$lastError) {
        return;
    }

    $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
    if (!in_array($lastError['type'] ?? null, $fatalTypes, true)) {
        return;
    }

    try {
        $app->make(\Psr\Log\LoggerInterface::class)->error('Fatal shutdown error', [
            'message' => $lastError['message'] ?? null,
            'file' => $lastError['file'] ?? null,
            'line' => $lastError['line'] ?? null,
            'url' => $_SERVER['REQUEST_URI'] ?? null,
            'method' => $_SERVER['REQUEST_METHOD'] ?? null,
        ]);
    } catch (\Throwable $e) {
        // Avoid any secondary failures at shutdown.
    }
});

// Hostinger/LiteSpeed قد يحوّل /media/* إلى index.php ويفقد المسار قبل توجيه Laravel.
$currentPath = parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH) ?: '';
if (! preg_match('#/(media|storage)/.+#', (string) $currentPath)) {
    foreach (['REDIRECT_URL', 'REDIRECT_REDIRECT_URL', 'HTTP_X_ORIGINAL_URL', 'UNENCODED_URL'] as $rewriteKey) {
        $raw = $_SERVER[$rewriteKey] ?? null;
        if (! is_string($raw) || $raw === '') {
            continue;
        }
        $rewrittenPath = parse_url($raw, PHP_URL_PATH) ?: $raw;
        $rewrittenPath = '/'.ltrim(str_replace('\\', '/', (string) $rewrittenPath), '/');
        if (! preg_match('#^/(media|storage)/.+#', $rewrittenPath)) {
            continue;
        }
        $query = (string) ($_SERVER['QUERY_STRING'] ?? '');
        $_SERVER['REQUEST_URI'] = $rewrittenPath.($query !== '' ? '?'.$query : '');
        $_SERVER['PATH_INFO'] = $rewrittenPath;
        break;
    }
}

$app->handleRequest(Request::capture());
