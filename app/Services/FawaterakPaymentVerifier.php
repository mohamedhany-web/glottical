<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * تأكيد دفع فواتيرك قبل تفعيل أي طلب/رصيد.
 */
class FawaterakPaymentVerifier
{
    public function __construct(
        private readonly FawaterakApiService $api,
    ) {}

    /**
     * يستخرج معرف الفاتورة من الطلب/الجلسة/سجل الطلب.
     */
    public function resolveInvoiceId(Order $order, ?Request $request = null, array $payload = []): ?string
    {
        $candidates = [
            $request?->query('invoice_id'),
            $request?->query('invoiceId'),
            $request?->query('InvoiceId'),
            $request?->query('transactionId'),
            $request?->query('transaction_id'),
            $payload['invoice_id'] ?? null,
            $payload['invoiceId'] ?? null,
            $payload['InvoiceId'] ?? null,
            data_get($payload, 'data.invoice_id'),
            data_get($payload, 'data.invoiceId'),
            $order->fawaterak_invoice_id,
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

    /**
     * يتحقق أن الفاتورة مدفوعة عبر API فواتيرك. يرمي عند الفشل.
     *
     * @throws InvalidArgumentException
     */
    public function assertOrderPaid(Order $order, ?Request $request = null, array $payload = []): string
    {
        $invoiceId = $this->resolveInvoiceId($order, $request, $payload);
        if ($invoiceId === null) {
            throw new InvalidArgumentException(
                'تعذّر تأكيد الدفع: لا يوجد رقم فاتورة من فواتيرك. إن تم الخصم فعلاً تواصل مع الدعم برقم الطلب #'.$order->id
            );
        }

        if ((string) $order->fawaterak_invoice_id !== $invoiceId) {
            $order->forceFill(['fawaterak_invoice_id' => $invoiceId])->save();
        }

        if (! $this->api->isConfigured()) {
            Log::warning('Fawaterak verify skipped: API token missing', [
                'order_id' => $order->id,
                'invoice_id' => $invoiceId,
            ]);
            throw new InvalidArgumentException(
                'تعذّر تأكيد الدفع آلياً (إعدادات فواتيرك API غير مكتملة). سيُفعَّل الطلب بعد تأكيد الإدارة أو اكتمال الـ webhook.'
            );
        }

        $result = $this->api->resolveInvoicePaid($invoiceId);
        if (! $result['paid']) {
            Log::warning('Fawaterak invoice not paid', [
                'order_id' => $order->id,
                'invoice_id' => $invoiceId,
                'message' => $result['message'],
                'invoice' => $result['invoice'],
            ]);
            throw new InvalidArgumentException(
                'لم يتم تأكيد الدفع لدى فواتيرك بعد. إن اكتمل الخصم انتظر لحظات أو تواصل مع الدعم برقم الطلب #'.$order->id
            );
        }

        return $invoiceId;
    }
}
