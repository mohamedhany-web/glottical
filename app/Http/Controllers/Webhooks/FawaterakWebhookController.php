<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * استقبال إشعار فواتيرك بعد الدفع — يُكمّل التفعيل عند فقدان الجلسة.
 */
class FawaterakWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();
        Log::info('Fawaterak webhook received', ['keys' => array_keys($payload)]);

        $invoiceId = $payload['invoice_id']
            ?? $payload['invoiceId']
            ?? $payload['InvoiceId']
            ?? data_get($payload, 'data.invoice_id')
            ?? data_get($payload, 'data.invoiceId');

        $status = strtolower((string) (
            $payload['invoice_status']
            ?? $payload['status']
            ?? data_get($payload, 'data.status')
            ?? ''
        ));

        if (! $invoiceId) {
            return response()->json(['ok' => false, 'message' => 'missing invoice_id'], 422);
        }

        if (! in_array($status, ['paid', 'success', 'completed', '1', 'true'], true)) {
            return response()->json(['ok' => true, 'message' => 'ignored status']);
        }

        $invoiceId = (string) $invoiceId;

        $order = Order::query()
            ->where('fawaterak_invoice_id', $invoiceId)
            ->where('status', Order::STATUS_PENDING)
            ->first();

        if ($order) {
            return $this->approveCourseOrder($order, $invoiceId, $payload);
        }

        return response()->json(['ok' => true, 'message' => 'no matching pending record']);
    }

    private function approveCourseOrder(Order $order, string $invoiceId, array $payload): JsonResponse
    {
        try {
            DB::transaction(function () use ($order, $invoiceId, $payload) {
                $locked = Order::whereKey($order->id)->lockForUpdate()->first();
                if (! $locked || $locked->status !== Order::STATUS_PENDING) {
                    return;
                }

                app(\App\Http\Controllers\Public\CheckoutController::class)
                    ->approveOrderAfterOnlinePaymentPublic(
                        $locked,
                        'fawaterak',
                        $invoiceId,
                        $payload,
                        'فواتيرك (Webhook)'
                    );
            });

            return response()->json(['ok' => true, 'type' => 'course_order']);
        } catch (\Throwable $e) {
            Log::error('Fawaterak webhook order approval failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['ok' => false], 500);
        }
    }
}
