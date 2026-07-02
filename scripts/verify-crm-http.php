<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

use App\Models\User;

$errors = [];
$routes = [
    ['GET', '/admin/crm', 'admin'],
    ['GET', '/admin/crm/leads', 'admin'],
    ['GET', '/admin/crm/commissions', 'admin'],
    ['GET', '/admin/crm/audit', 'admin'],
    ['GET', '/admin/crm/groups', 'admin'],
    ['GET', '/employee/crm', 'employee'],
    ['GET', '/employee/crm/leads', 'employee'],
];

$admin = User::where('role', 'super_admin')->first() ?? User::where('role', 'admin')->first();
$employee = User::employees()->first();

foreach ($routes as [$method, $uri, $type]) {
    $user = $type === 'admin' ? $admin : $employee;
    if (! $user) {
        $errors[] = "no user for $uri";
        continue;
    }

    $request = \Illuminate\Http\Request::create($uri, $method);
    $request->setUserResolver(fn () => $user);

    try {
        $response = $kernel->handle($request);
        $code = $response->getStatusCode();
        if ($code >= 500) {
            $errors[] = "$uri => HTTP $code";
        }
        $kernel->terminate($request, $response);
    } catch (\Throwable $e) {
        $errors[] = "$uri => ".$e->getMessage();
    }
}

echo json_encode(['ok' => empty($errors), 'errors' => $errors], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).PHP_EOL;
exit(empty($errors) ? 0 : 1);
