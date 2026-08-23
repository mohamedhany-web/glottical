<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            if (empty($payment->payment_number) || self::where('payment_number', $payment->payment_number)->exists()) {
                $payment->payment_number = self::generateUniquePaymentNumber();
            }
        });
    }

    /**
     * رقم دفع فريد — يعتمد على أعلى رقم PAY-* موجود وليس على count() (يتجنب التكرار بعد الحذف أو التزامن).
     */
    public static function generateUniquePaymentNumber(): string
    {
        $maxSuffix = self::query()
            ->where('payment_number', 'like', 'PAY-%')
            ->pluck('payment_number')
            ->map(static function (string $number): int {
                if (preg_match('/PAY-(\d+)/', $number, $matches)) {
                    return (int) $matches[1];
                }

                return 0;
            })
            ->max() ?? 0;

        do {
            $maxSuffix++;
            $candidate = 'PAY-'.str_pad((string) $maxSuffix, 8, '0', STR_PAD_LEFT);
        } while (self::where('payment_number', $candidate)->exists());

        return $candidate;
    }

    protected $fillable = [
        'payment_number',
        'invoice_id',
        'user_id',
        'payment_method',
        'payment_gateway',
        'wallet_id',
        'installment_payment_id',
        'amount',
        'gateway_fee_amount',
        'net_after_gateway_fee',
        'currency',
        'status',
        'transaction_id',
        'reference_number',
        'gateway_response',
        'notes',
        'paid_at',
        'processed_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_fee_amount' => 'decimal:2',
        'net_after_gateway_fee' => 'decimal:2',
        'paid_at' => 'datetime',
        'gateway_response' => 'array',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function installmentPayment()
    {
        return $this->belongsTo(InstallmentPayment::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);
    }
}
