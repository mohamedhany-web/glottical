<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
            if (!filled($transaction->transaction_number)) {
                // Temporary unique value to satisfy unique constraint before we have an ID.
                $transaction->transaction_number = 'TXN-TMP-' . Str::uuid()->toString();
            }
        });

        static::created(function (Transaction $transaction) {
            if (is_string($transaction->transaction_number) && Str::startsWith($transaction->transaction_number, 'TXN-TMP-')) {
                $transaction->transaction_number = self::humanTransactionNumber($transaction->id);
                $transaction->saveQuietly();
            }
        });
    }

    public static function humanTransactionNumber(int $id): string
    {
        return 'TXN-' . str_pad((string) $id, 8, '0', STR_PAD_LEFT);
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

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
