<?php

namespace App\Services;

use App\Models\Order;

/**
 * ربط إشعار/عودة فواتيرك بطلب معلّق.
 */
class FawaterakOrderResolver
{
    public static function extractInvoiceId(array $payload): ?string
    {
        $candidates = [
            $payload['invoice_id'] ?? null,
            $payload['invoiceId'] ?? null,
            $payload['InvoiceId'] ?? null,
            $payload['transactionId'] ?? null,
            $payload['transaction_id'] ?? null,
            data_get($payload, 'data.invoice_id'),
            data_get($payload, 'data.invoiceId'),
        ];

        foreach ($candidates as $candidate) {
            if ($candidate === null || $candidate === '') {
                continue;
            }
            $id = trim((string) $candidate);
            if ($id !== '') {
                return $id;
            }
        }

        return null;
    }

    public static function extractPaidStatus(array $payload): string
    {
        return strtolower((string) (
            $payload['invoice_status']
            ?? $payload['status']
            ?? $payload['payment_status']
            ?? data_get($payload, 'data.invoice_status')
            ?? data_get($payload, 'data.status')
            ?? data_get($payload, 'data.payment_status')
            ?? ''
        ));
    }

    public static function isPaidStatus(string $status): bool
    {
        return in_array($status, ['paid', 'success', 'completed', 'successful', '1', 'true', 'yes'], true);
    }

    /**
     * @return array<string, mixed>
     */
    public static function normalizedPayLoad(array $payload): array
    {
        $raw = data_get($payload, 'pay_load') ?? data_get($payload, 'data.pay_load') ?? $payload['payLoad'] ?? null;

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);

            return is_array($decoded) ? $decoded : [];
        }

        return is_array($raw) ? $raw : [];
    }

    public static function resolvePendingOrder(array $payload, ?string $invoiceId = null): ?Order
    {
        $invoiceId = $invoiceId ?: self::extractInvoiceId($payload);

        if ($invoiceId) {
            $byInvoice = Order::query()
                ->where('fawaterak_invoice_id', $invoiceId)
                ->where('status', Order::STATUS_PENDING)
                ->first();
            if ($byInvoice) {
                return $byInvoice;
            }
        }

        $payLoad = self::normalizedPayLoad($payload);
        $orderKey = data_get($payLoad, 'order_id');
        if (is_numeric($orderKey)) {
            $byId = Order::query()
                ->whereKey((int) $orderKey)
                ->where('status', Order::STATUS_PENDING)
                ->first();
            if ($byId) {
                return $byId;
            }
        }

        $invoiceNumber = data_get($payload, 'invoice_number')
            ?? data_get($payload, 'data.invoice_number')
            ?? data_get($payload, 'InvoiceNumber');

        if (is_string($invoiceNumber) && preg_match('/ORD-(\d+)/i', $invoiceNumber, $matches)) {
            return Order::query()
                ->whereKey((int) $matches[1])
                ->where('status', Order::STATUS_PENDING)
                ->first();
        }

        $description = data_get($payload, 'cartItems.0.description');
        if (is_string($description) && preg_match('/ORD-(\d+)/i', $description, $matches)) {
            return Order::query()
                ->whereKey((int) $matches[1])
                ->where('status', Order::STATUS_PENDING)
                ->first();
        }

        return null;
    }
}
