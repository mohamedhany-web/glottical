<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$req = Illuminate\Http\Request::create('/', 'GET');
$resp = $kernel->handle($req);
$html = $resp->getContent();
$code = $resp->getStatusCode();

echo "home_status={$code}\n";
echo str_contains($html, '00d4ff') ? "HAS_NEON\n" : "no_neon\n";
echo str_contains($html, '00A3C4') ? "HAS_OLD_CYAN\n" : "no_old_cyan\n";
echo str_contains($html, '8FA3C0') ? "has_muted_hex\n" : "no_muted_hex\n";
echo str_contains($html, 'E6B009') ? "gold_ok\n" : "gold_fail\n";
echo str_contains($html, '1A3F73') ? "blue_ok\n" : "blue_fail\n";
echo str_contains($html, 'free-trial-modal') ? "modal_ok\n" : "modal_fail\n";
echo str_contains($html, 'data-open-free-trial') ? "cta_ok\n" : "cta_fail\n";
echo str_contains($html, 'freeTrialBooking') ? "js_ok\n" : "js_fail\n";

$req2 = Illuminate\Http\Request::create('/free-trial/slots?days=14', 'GET', [], [], [], ['HTTP_ACCEPT' => 'application/json']);
$resp2 = $kernel->handle($req2);
$data = json_decode($resp2->getContent(), true);
echo 'slots_status='.$resp2->getStatusCode().' total='.($data['total'] ?? 0).' duration='.($data['duration_minutes'] ?? '?')."\n";

// book one slot
$slot = $data['slots_by_date'][$data['dates'][0] ?? ''][0]['starts_at'] ?? null;
if ($slot) {
    $token = csrf_token();
    $bookReq = Illuminate\Http\Request::create('/free-trial/book', 'POST', [], [], [], [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_ACCEPT' => 'application/json',
        'HTTP_X_CSRF_TOKEN' => $token,
    ], json_encode([
        'name' => 'اختبار تناسق الألوان',
        'email' => 'palette-test@example.com',
        'phone' => '01000000000',
        'goal' => 'test',
        'starts_at' => $slot,
    ]));
    $bookReq->headers->set('X-CSRF-TOKEN', $token);
    // Need session for CSRF - skip full book if complex; use service directly
    $booking = App\Services\FreeTrialBookingService::book([
        'name' => 'اختبار تناسق الألوان',
        'email' => 'palette-test@example.com',
        'phone' => '01000000000',
        'goal' => 'test',
        'starts_at' => $slot,
    ]);
    echo 'booking_ok id='.$booking->id.' duration='.$booking->duration_minutes."\n";
} else {
    echo "booking_skip no_slot\n";
}

$kernel->terminate($req, $resp);
