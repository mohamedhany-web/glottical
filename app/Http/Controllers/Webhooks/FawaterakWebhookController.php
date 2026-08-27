<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\FawaterakOrderResolver;
use App\Services\FawaterakPaymentVerifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * استقبال إشعار فواتيرك بعد الدفع — يُكمّل التفعيل عند فقدان الجلسة.
 */
class FawaterakWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();
        Log::info('Fawaterak webhook received', [
            'keys' => array_keys($payload),
            'invoice_id' => FawaterakOrderResolver::extractInvoiceId($payload),
            'status' => FawaterakOrderResolver::extractPaidStatus($payload),
        ]);

        $invoiceId = FawaterakOrderResolver::extractInvoiceId($payload);
        $status = FawaterakOrderResolver::extractPaidStatus($payload);

        if (! $invoiceId && ! FawaterakOrderResolver::isPaidStatus($status)) {
            return response()->json(['ok' => true, 'message' => 'ignored: no invoice']);
        }

        if ($invoiceId && ! FawaterakOrderResolver::isPaidStatus($status)) {
            return response()->json(['ok' => true, 'message' => 'ignored status']);
        }

        $order = FawaterakOrderResolver::resolvePendingOrder($payload, $invoiceId);
        if (! $order) {
            Log::warning('Fawaterak webhook: no matching pending order', [
                'invoice_id' => $invoiceId,
                'pay_load' => FawaterakOrderResolver::normalizedPayLoad($payload),
            ]);

            return response()->json(['ok' => true, 'message' => 'no matching pending record']);
        }

        $invoiceId = $invoiceId ?: (string) ($order->fawaterak_invoice_id ?? '');

        return $this->approvePendingOrder($order, $invoiceId, $payload);
    }

    private function approvePendingOrder(Order $order, string $invoiceId, array $payload): JsonResponse
    {
        try {
            DB::transaction(function () use ($order, $invoiceId, $payload) {
                $locked = Order::whereKey($order->id)->lockForUpdate()->first();
                if (! $locked || $locked->status !== Order::STATUS_PENDING) {
                    return;
                }

                app(FawaterakPaymentVerifier::class)->assertOrderPaid($locked, null, array_merge($payload, [
                    'invoice_id' => $invoiceId ?: FawaterakOrderResolver::extractInvoiceId($payload),
                ]));

                app(\App\Http\Controllers\Public\CheckoutController::class)
                    ->approveOrderAfterOnlinePaymentPublic(
                        $locked,
                        'fawaterak',
                        $invoiceId ?: FawaterakOrderResolver::extractInvoiceId($payload),
                        $payload,
                        'فواتيرك (Webhook)'
                    );
            });

            return response()->json(['ok' => true, 'type' => 'order', 'order_id' => $order->id]);
        } catch (InvalidArgumentException $e) {
            Log::warning('Fawaterak webhook payment not verified', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error('Fawaterak webhook order approval failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['ok' => false, 'message' => 'approval failed'], 500);
        }
    }
}
