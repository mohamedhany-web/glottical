<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            // Prevent duplicate invoice numbers from legacy count-based generators.
            if (empty($invoice->invoice_number) || self::where('invoice_number', $invoice->invoice_number)->exists()) {
                $invoice->invoice_number = self::generateUniqueInvoiceNumber();
            }
        });
    }

    public static function generateUniqueInvoiceNumber(): string
    {
        do {
            $candidate = 'INV-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4));
        } while (self::where('invoice_number', $candidate)->exists());

        return $candidate;
    }

    protected $fillable = [
        'invoice_number',
        'user_id',
        'type',
        'description',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'status',
        'due_date',
        'paid_at',
        'notes',
        'items',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'date',
        'items' => 'array',
    ];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // الحصول على جميع المعاملات المرتبطة (من خلال Payments أو مباشرة)
    public function getAllTransactionsAttribute()
    {
        // المعاملات المرتبطة مباشرة بالفواتير
        $directTransactions = $this->transactions;
        
        // المعاملات المرتبطة من خلال Payments
        $paymentTransactions = Transaction::whereHas('payment', function($q) {
            $q->where('invoice_id', $this->id);
        })->get();
        
        // دمج المعاملات وتجنب التكرار
        return $directTransactions->merge($paymentTransactions)->unique('id')->values();
    }

    public function enrollments()
    {
        return $this->hasMany(StudentCourseEnrollment::class);
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }

    public function expense()
    {
        return $this->hasOne(Expense::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'overdue')
                ->orWhere(function ($inner) {
                    $inner->where('status', 'pending')
                        ->whereNotNull('due_date')
                        ->whereDate('due_date', '<', now()->toDateString());
                });
        });
    }

    // Methods
    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function getPaidAmountAttribute()
    {
        return $this->payments()
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function getRemainingAmountAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isOverdue()
    {
        return $this->due_date && $this->due_date < now() && !$this->isPaid();
    }
}
