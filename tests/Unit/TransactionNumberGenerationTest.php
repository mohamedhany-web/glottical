<?php

namespace Tests\Unit;

use App\Models\Transaction;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TransactionNumberGenerationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('transactions');
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type')->default('credit');
            $table->string('category')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency', 8)->default('USD');
            $table->string('status')->default('completed');
            $table->timestamps();
        });
    }

    public function test_generate_unique_transaction_number_skips_existing_suffixes(): void
    {
        Transaction::create([
            'transaction_number' => 'TXN-00000003',
            'type' => 'credit',
            'amount' => 10,
            'currency' => 'USD',
            'status' => 'completed',
        ]);

        $this->assertSame('TXN-00000004', Transaction::generateUniqueTransactionNumber());
    }

    public function test_generate_unique_skips_gap_when_higher_number_exists(): void
    {
        Transaction::create([
            'transaction_number' => 'TXN-00000004',
            'type' => 'credit',
            'amount' => 10,
            'currency' => 'USD',
            'status' => 'completed',
        ]);

        // Only 1 row → count()+1 would wrongly yield TXN-00000002 / TXN-00000004 collision risk.
        $this->assertSame(1, Transaction::count());
        $this->assertSame('TXN-00000005', Transaction::generateUniqueTransactionNumber());
    }

    public function test_creating_hook_replaces_duplicate_transaction_number(): void
    {
        Transaction::create([
            'transaction_number' => 'TXN-00000003',
            'type' => 'credit',
            'amount' => 10,
            'currency' => 'USD',
            'status' => 'completed',
        ]);

        $transaction = Transaction::create([
            'transaction_number' => 'TXN-00000003',
            'type' => 'credit',
            'amount' => 20,
            'currency' => 'USD',
            'status' => 'completed',
        ]);

        $this->assertSame('TXN-00000004', $transaction->transaction_number);
        $this->assertSame(2, Transaction::count());
    }

    public function test_ignores_non_numeric_txn_formats_when_finding_max(): void
    {
        Transaction::create([
            'transaction_number' => 'TXN-20260826151955-ABCD',
            'type' => 'debit',
            'amount' => 1,
            'currency' => 'USD',
            'status' => 'completed',
        ]);
        Transaction::create([
            'transaction_number' => 'TXN-00000002',
            'type' => 'credit',
            'amount' => 10,
            'currency' => 'USD',
            'status' => 'completed',
        ]);

        $this->assertSame('TXN-00000003', Transaction::generateUniqueTransactionNumber());
    }
}
