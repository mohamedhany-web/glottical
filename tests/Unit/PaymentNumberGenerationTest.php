<?php

namespace Tests\Unit;

use App\Models\Payment;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PaymentNumberGenerationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('payments');
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency', 8)->default('USD');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function test_generate_unique_payment_number_skips_existing_suffixes(): void
    {
        Payment::create([
            'payment_number' => 'PAY-00000003',
            'amount' => 10,
            'currency' => 'USD',
            'status' => 'completed',
        ]);

        $this->assertSame('PAY-00000004', Payment::generateUniquePaymentNumber());
    }

    public function test_creating_hook_replaces_duplicate_payment_number(): void
    {
        Payment::create([
            'payment_number' => 'PAY-00000003',
            'amount' => 10,
            'currency' => 'USD',
            'status' => 'completed',
        ]);

        $payment = Payment::create([
            'payment_number' => 'PAY-00000003',
            'amount' => 20,
            'currency' => 'USD',
            'status' => 'completed',
        ]);

        $this->assertSame('PAY-00000004', $payment->payment_number);
        $this->assertSame(2, Payment::count());
    }
}
