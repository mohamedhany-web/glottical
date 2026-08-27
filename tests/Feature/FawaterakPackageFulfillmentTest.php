<?php

namespace Tests\Feature;

use App\Http\Controllers\Public\CheckoutController;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ServicePackage;
use App\Models\StudentServiceEntitlement;
use App\Models\Transaction;
use App\Models\User;
use App\Services\FawaterakOrderResolver;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class FawaterakPackageFulfillmentTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        $this->extendSchema();
    }

    public function test_online_payment_approves_service_package_with_subscription_invoice_and_entitlement(): void
    {
        $student = User::factory()->create(['role' => 'student', 'is_active' => true]);
        $package = ServicePackage::create([
            'name' => 'باقة خاصة',
            'slug' => 'private-pack-'.uniqid(),
            'scope' => ServicePackage::SCOPE_PRIVATE_LESSONS,
            'plan_type' => ServicePackage::PLAN_PRIVATE,
            'term_months' => 1,
            'weekly_group_sessions' => 0,
            'weekly_private_sessions' => 2,
            'units_count' => 8,
            'session_minutes' => 50,
            'duration_days' => 30,
            'price' => 120,
            'currency' => 'USD',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $order = Order::create([
            'user_id' => $student->id,
            'service_package_id' => $package->id,
            'order_type' => Order::TYPE_SERVICE_PACKAGE,
            'original_amount' => 120,
            'discount_amount' => 0,
            'amount' => 120,
            'currency' => 'USD',
            'payment_method' => 'online',
            'status' => Order::STATUS_PENDING,
            'fawaterak_invoice_id' => 'FW-12345',
        ]);

        app(CheckoutController::class)->approveOrderAfterOnlinePaymentPublic(
            $order,
            'fawaterak',
            'FW-12345',
            ['paid' => true],
            'فواتيرك (test)'
        );

        $order->refresh();
        $this->assertSame(Order::STATUS_APPROVED, $order->status);
        $this->assertNotNull($order->invoice_id);
        $this->assertNotNull($order->payment_id);

        $invoice = Invoice::find($order->invoice_id);
        $this->assertSame('subscription', $invoice->type);
        $this->assertStringContainsString('باقة', $invoice->description);

        $payment = Payment::find($order->payment_id);
        $this->assertSame('fawaterak', $payment->payment_gateway);

        $transaction = Transaction::query()->where('payment_id', $payment->id)->first();
        $this->assertSame('subscription', $transaction->category);

        $this->assertDatabaseHas('student_service_entitlements', [
            'user_id' => $student->id,
            'order_id' => $order->id,
            'status' => StudentServiceEntitlement::STATUS_ACTIVE,
        ]);
    }

    public function test_resolver_finds_order_by_ord_invoice_number(): void
    {
        $student = User::factory()->create(['role' => 'student', 'is_active' => true]);
        $order = Order::create([
            'user_id' => $student->id,
            'order_type' => Order::TYPE_SERVICE_PACKAGE,
            'amount' => 50,
            'currency' => 'USD',
            'payment_method' => 'online',
            'status' => Order::STATUS_PENDING,
        ]);

        $resolved = FawaterakOrderResolver::resolvePendingOrder([
            'invoice_number' => 'ORD-'.$order->id,
            'status' => 'paid',
        ]);

        $this->assertNotNull($resolved);
        $this->assertSame($order->id, $resolved->id);
    }

    protected function extendSchema(): void
    {
        if (! Schema::hasTable('service_packages')) {
            Schema::create('service_packages', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('scope', 64);
                $table->string('plan_type', 32)->nullable();
                $table->unsignedInteger('term_months')->nullable();
                $table->unsignedInteger('weekly_group_sessions')->default(0);
                $table->unsignedInteger('weekly_private_sessions')->default(0);
                $table->unsignedInteger('units_count')->default(0);
                $table->unsignedInteger('session_minutes')->default(50);
                $table->unsignedInteger('duration_days')->default(30);
                $table->decimal('price', 12, 2)->default(0);
                $table->string('currency', 8)->default('USD');
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->unsignedBigInteger('tutoring_group_id')->nullable();
                $table->timestamps();
            });
        }

        Schema::dropIfExists('orders');
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->unsignedBigInteger('service_package_id')->nullable();
            $table->unsignedBigInteger('advanced_course_id')->nullable();
            $table->unsignedBigInteger('tutoring_group_id')->nullable();
            $table->string('order_type', 40)->default('course');
            $table->json('custom_package_data')->nullable();
            $table->decimal('original_amount', 12, 2)->nullable();
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency', 8)->nullable();
            $table->string('payment_method', 32)->default('online');
            $table->string('status', 32)->default('pending');
            $table->string('fawaterak_invoice_id', 64)->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        if (! Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number')->unique();
                $table->foreignId('user_id');
                $table->string('type', 32)->default('course');
                $table->text('description')->nullable();
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->decimal('tax_amount', 12, 2)->default(0);
                $table->decimal('discount_amount', 12, 2)->default(0);
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->string('status', 32)->default('pending');
                $table->timestamp('due_date')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->text('notes')->nullable();
                $table->json('items')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->string('payment_number')->unique();
                $table->unsignedBigInteger('invoice_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('payment_method')->nullable();
                $table->string('payment_gateway')->nullable();
                $table->decimal('amount', 12, 2)->default(0);
                $table->decimal('gateway_fee_amount', 12, 2)->default(0);
                $table->decimal('net_after_gateway_fee', 12, 2)->nullable();
                $table->string('currency', 8)->default('USD');
                $table->string('status')->default('pending');
                $table->string('transaction_id')->nullable();
                $table->json('gateway_response')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->string('transaction_number')->unique();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('payment_id')->nullable();
                $table->unsignedBigInteger('invoice_id')->nullable();
                $table->unsignedBigInteger('expense_id')->nullable();
                $table->unsignedBigInteger('subscription_id')->nullable();
                $table->string('type')->default('credit');
                $table->string('category')->nullable();
                $table->decimal('amount', 12, 2)->default(0);
                $table->string('currency', 8)->default('USD');
                $table->text('description')->nullable();
                $table->string('status')->default('completed');
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('student_service_entitlements')) {
            Schema::create('student_service_entitlements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->unsignedBigInteger('service_package_id')->nullable();
                $table->unsignedBigInteger('order_id')->nullable();
                $table->string('scope', 64);
                $table->string('plan_type', 32)->nullable();
                $table->unsignedInteger('term_months')->nullable();
                $table->unsignedInteger('weekly_group_sessions')->default(0);
                $table->unsignedInteger('weekly_private_sessions')->default(0);
                $table->unsignedInteger('units_total')->default(0);
                $table->unsignedInteger('units_used')->default(0);
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->string('status', 32)->default('active');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }
}
