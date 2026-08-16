<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Public\PayPalCheckoutController;
use App\Models\Order;
use App\Services\PayPalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class PayPalWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $raw = $request->getContent();
        $payload = json_decode($raw, true);
        if (! is_array($payload)) {
            return response()->json(['ok' => false, 'message' => 'invalid payload'], 400);
        }

        try {
            app(PayPalService::class)->assertValidWebhook($request, $payload);
        } catch (RuntimeException $e) {
            Log::warning('PayPal webhook signature rejected', [
                'error' => $e->getMessage(),
                'event' => $payload['event_type'] ?? null,
            ]);

            return response()->json(['ok' => false, 'message' => 'invalid signature'], 400);
        }

        $event = (string) ($payload['event_type'] ?? '');
        Log::info('PayPal webhook received', [
            'event' => $event,
            'id' => $payload['id'] ?? null,
        ]);

        $paypalOrderId = $this->paypalOrderIdFromPayload($payload);
        if (! $paypalOrderId) {
            return response()->json(['ok' => true, 'message' => 'ignored: no order id']);
        }

        $order = Order::query()
            ->where('paypal_order_id', $paypalOrderId)
            ->first();

        if (! $order) {
            $customId = data_get($payload, 'resource.custom_id')
                ?? data_get($payload, 'resource.purchase_units.0.custom_id');
            if (is_numeric($customId)) {
                $order = Order::query()->whereKey((int) $customId)->first();
            }
        }

        if (! $order) {
            return response()->json(['ok' => true, 'message' => 'no matching order']);
        }

        if ($order->status === Order::STATUS_APPROVED) {
            return response()->json(['ok' => true, 'message' => 'already approved']);
        }

        try {
            $paypal = app(PayPalService::class);
            $remote = $paypal->getOrder($paypalOrderId);
            if (! $paypal->isOrderPaid($remote)
                && ! in_array($event, ['CHECKOUT.ORDER.APPROVED', 'PAYMENT.CAPTURE.COMPLETED'], true)) {
                return response()->json(['ok' => true, 'message' => 'not paid yet']);
            }

            app(PayPalCheckoutController::class)->captureAndApprove($order, $paypalOrderId);
        } catch (Throwable $e) {
            Log::error('PayPal webhook capture failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['ok' => false, 'message' => 'capture failed'], 500);
        }

        return response()->json(['ok' => true, 'type' => 'order']);
    }

    private function paypalOrderIdFromPayload(array $payload): ?string
    {
        $candidates = [
            data_get($payload, 'resource.supplementary_data.related_ids.order_id'),
            data_get($payload, 'resource.id'),
        ];
        $event = (string) ($payload['event_type'] ?? '');
        if ($event === 'PAYMENT.CAPTURE.COMPLETED') {
            $candidates = [
                data_get($payload, 'resource.supplementary_data.related_ids.order_id'),
                data_get($payload, 'resource.custom_id'),
            ];
        }

        foreach ($candidates as $value) {
            if (is_string($value) && $value !== '' && ! is_numeric($value)) {
                return $value;
            }
            if (is_string($value) && str_starts_with($value, 'WH-')) {
                continue;
            }
        }

        foreach ($candidates as $value) {
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return null;
    }
}
