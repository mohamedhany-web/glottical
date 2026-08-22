<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class AdminInvoiceCrudTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        $this->createInvoiceTables();
        $this->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
        ]);
    }

    public function test_admin_can_list_and_open_create_invoice_pages(): void
    {
        $admin = $this->seedAdmin();
        $this->seedStudent('طالب فاتورة');

        $this->actingAs($admin)
            ->get(route('admin.invoices.index'))
            ->assertOk()
            ->assertSee('إدارة الفواتير', false);

        $this->actingAs($admin)
            ->get(route('admin.invoices.create'))
            ->assertOk()
            ->assertSee('إنشاء فاتورة جديدة', false)
            ->assertSee('طالب فاتورة', false);
    }

    public function test_admin_can_create_invoice_without_description(): void
    {
        $admin = $this->seedAdmin();
        $student = $this->seedStudent();

        $this->actingAs($admin)
            ->from(route('admin.invoices.create'))
            ->post(route('admin.invoices.store'), [
                'user_id' => $student->id,
                'type' => 'course',
                'subtotal' => 250,
                'tax_amount' => 0,
                'discount_amount' => 0,
            ])
            ->assertRedirect(route('admin.invoices.index'));

        $invoice = Invoice::query()->first();
        $this->assertNotNull($invoice);
        $this->assertSame((int) $student->id, (int) $invoice->user_id);
        $this->assertSame('course', $invoice->type);
        $this->assertSame('فاتورة كورس', $invoice->description);
        $this->assertSame('250.00', (string) $invoice->total_amount);
        $this->assertSame('pending', $invoice->status);
        $this->assertNotEmpty($invoice->invoice_number);
    }

    public function test_admin_can_show_invoice_even_when_related_tables_are_partial(): void
    {
        $admin = $this->seedAdmin();
        $student = $this->seedStudent();
        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-SHOW-1',
            'user_id' => $student->id,
            'type' => 'course',
            'description' => 'فاتورة اختبار',
            'subtotal' => 100,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 100,
            'status' => 'pending',
            'due_date' => now()->addDays(7)->toDateString(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.invoices.show', $invoice))
            ->assertOk()
            ->assertSee('INV-TEST-SHOW-1', false)
            ->assertSee($student->name, false);
    }

    public function test_payment_can_store_fawaterak_gateway_value(): void
    {
        $student = $this->seedStudent();
        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-PAY-1',
            'user_id' => $student->id,
            'type' => 'course',
            'description' => 'فاتورة دفع',
            'subtotal' => 90,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 90,
            'status' => 'paid',
            'paid_at' => now()->toDateString(),
        ]);

        $payment = Payment::create([
            'payment_number' => 'PAY-TEST-1',
            'invoice_id' => $invoice->id,
            'user_id' => $student->id,
            'payment_method' => 'online',
            'payment_gateway' => 'fawaterak',
            'amount' => 90,
            'currency' => 'USD',
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        $this->assertSame('fawaterak', $payment->fresh()->payment_gateway);
    }

    protected function seedAdmin(): User
    {
        return User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
    }

    protected function seedStudent(string $name = 'طالب الفاتورة'): User
    {
        return User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'name' => $name,
            'password' => Hash::make('password'),
        ]);
    }

    protected function createInvoiceTables(): void
    {
        if (! Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number')->unique();
                $table->foreignId('user_id');
                $table->string('type')->default('course');
                $table->string('description');
                $table->decimal('subtotal', 10, 2);
                $table->decimal('tax_amount', 10, 2)->default(0);
                $table->decimal('discount_amount', 10, 2)->default(0);
                $table->decimal('total_amount', 10, 2);
                $table->string('status')->default('pending');
                $table->date('due_date')->nullable();
                $table->date('paid_at')->nullable();
                $table->text('notes')->nullable();
                $table->json('items')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->string('payment_number')->unique();
                $table->foreignId('invoice_id');
                $table->foreignId('user_id');
                $table->string('payment_method')->default('cash');
                $table->string('payment_gateway', 40)->nullable();
                $table->decimal('amount', 10, 2);
                $table->decimal('gateway_fee_amount', 12, 2)->nullable();
                $table->decimal('net_after_gateway_fee', 12, 2)->nullable();
                $table->string('currency', 3)->default('USD');
                $table->string('status')->default('pending');
                $table->string('transaction_id')->nullable();
                $table->string('reference_number')->nullable();
                $table->json('gateway_response')->nullable();
                $table->text('notes')->nullable();
                $table->dateTime('paid_at')->nullable();
                $table->unsignedBigInteger('processed_by')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable();
                $table->string('session_id')->nullable();
                $table->string('action');
                $table->text('description')->nullable();
                $table->string('model_type')->nullable();
                $table->unsignedBigInteger('model_id')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
            });
        }
    }
}
