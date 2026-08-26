<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'user_id',
        'payment_id',
        'invoice_id',
        'expense_id',
        'subscription_id',
        'type',
        'category',
        'amount',
        'currency',
        'description',
        'status',
        'metadata',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Transaction $transaction) {
            // Prevent duplicates from legacy count-based generators (gaps after deletes / id-based numbers).
            if (empty($transaction->transaction_number) || self::where('transaction_number', $transaction->transaction_number)->exists()) {
                $transaction->transaction_number = self::generateUniqueTransactionNumber();
            }
        });
    }

    /**
     * رقم معاملة فريد — يعتمد على أعلى رقم TXN-* رقمي موجود وليس على count().
     */
    public static function generateUniqueTransactionNumber(): string
    {
        $maxSuffix = self::query()
            ->where('transaction_number', 'like', 'TXN-%')
            ->pluck('transaction_number')
            ->map(static function (string $number): int {
                if (preg_match('/^TXN-(\d+)$/', $number, $matches)) {
                    return (int) $matches[1];
                }

                return 0;
            })
            ->max() ?? 0;

        do {
            $maxSuffix++;
            $candidate = 'TXN-'.str_pad((string) $maxSuffix, 8, '0', STR_PAD_LEFT);
        } while (self::where('transaction_number', $candidate)->exists());

        return $candidate;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
