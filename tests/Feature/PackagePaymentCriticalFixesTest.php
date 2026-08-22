<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\ServicePackage;
use App\Models\StudentServiceEntitlement;
use App\Models\TutoringCohortEnrollment;
use App\Models\TutoringGroup;
use App\Models\TutoringGroupCohort;
use App\Models\User;
use App\Services\FawaterakPaymentVerifier;
use App\Services\StudentEntitlementService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class PackagePaymentCriticalFixesTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        $this->extendSchemaForPackagesAndPayments();
    }

    public function test_service_package_order_persists_package_currency_usd(): void
    {
        config(['currency.code' => 'USD', 'fawaterak.currency' => 'USD']);

        $student = User::factory()->create(['role' => 'student', 'is_active' => true]);
        $package = ServicePackage::create([
            'name' => 'باقة المدرسة',
            'slug' => 'school-usd-'.uniqid(),
            'scope' => ServicePackage::SCOPE_TUTORING_COLLECTIVE,
            'plan_type' => ServicePackage::PLAN_SCHOOL,
            'term_months' => 1,
            'weekly_group_sessions' => 2,
            'weekly_private_sessions' => 0,
            'units_count' => 8,
            'session_minutes' => 60,
            'duration_days' => 30,
            'price' => 1500,
            'currency' => 'USD',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $order = StudentEntitlementService::createOrder($student, $package, 'online');

        $this->assertSame('USD', $order->currency);
        $this->assertSame('USD', $order->currencyCode());
        $this->assertSame(1500.0, (float) $order->amount);
    }

    public function test_fawaterak_verifier_rejects_unpaid_invoice(): void
    {
        config([
            'fawaterak.api.token' => 'test-token',
            'fawaterak.api.base_url' => 'https://staging.fawaterk.com/api/v2',
            'fawaterak.env' => 'test',
        ]);

        Http::fake([
            'staging.fawaterk.com/api/v2/getInvoiceData/*' => Http::response([
                'status' => 'success',
                'data' => ['paid' => 0, 'invoice_status' => 'unpaid'],
            ], 200),
        ]);

        $student = User::factory()->create(['role' => 'student', 'is_active' => true]);
        $order = Order::create([
            'user_id' => $student->id,
            'order_type' => Order::TYPE_SERVICE_PACKAGE,
            'amount' => 100,
            'currency' => 'USD',
            'payment_method' => 'online',
            'status' => Order::STATUS_PENDING,
            'fawaterak_invoice_id' => 'INV-99',
        ]);

        $this->expectException(\InvalidArgumentException::class);
        app(FawaterakPaymentVerifier::class)->assertOrderPaid($order);
    }

    public function test_fawaterak_verifier_accepts_paid_invoice(): void
    {
        config([
            'fawaterak.api.token' => 'test-token',
            'fawaterak.api.base_url' => 'https://staging.fawaterk.com/api/v2',
            'fawaterak.env' => 'test',
        ]);

        Http::fake([
            'staging.fawaterk.com/api/v2/getInvoiceData/*' => Http::response([
                'status' => 'success',
                'data' => ['paid' => 1, 'invoice_status' => 'paid'],
            ], 200),
        ]);

        $student = User::factory()->create(['role' => 'student', 'is_active' => true]);
        $order = Order::create([
            'user_id' => $student->id,
            'order_type' => Order::TYPE_SERVICE_PACKAGE,
            'amount' => 100,
            'currency' => 'USD',
            'payment_method' => 'online',
            'status' => Order::STATUS_PENDING,
        ]);

        $invoiceId = app(FawaterakPaymentVerifier::class)->assertOrderPaid($order, null, [
            'invoice_id' => 'INV-paid-1',
        ]);

        $this->assertSame('INV-paid-1', $invoiceId);
        $this->assertSame('INV-paid-1', $order->fresh()->fawaterak_invoice_id);
    }

    public function test_paid_cohort_enrolls_with_school_package_credit_without_checkout(): void
    {
        [$group, $cohort] = $this->seedPaidClass();
        $student = User::factory()->create(['role' => 'student', 'is_active' => true]);

        $entitlement = StudentServiceEntitlement::create([
            'user_id' => $student->id,
            'scope' => ServicePackage::SCOPE_TUTORING_COLLECTIVE,
            'plan_type' => ServicePackage::PLAN_SCHOOL,
            'units_total' => 8,
            'units_used' => 0,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addMonth(),
            'status' => StudentServiceEntitlement::STATUS_ACTIVE,
            'notes' => 'test school credit',
        ]);

        $this->actingAs($student)
            ->post(route('student.classes.enroll', $cohort))
            ->assertRedirect(route('student.classes.show', $cohort));

        $this->assertDatabaseHas('tutoring_cohort_enrollments', [
            'tutoring_group_cohort_id' => $cohort->id,
            'user_id' => $student->id,
            'status' => TutoringCohortEnrollment::STATUS_ACTIVE,
            'student_service_entitlement_id' => $entitlement->id,
        ]);

        $this->assertSame(1, (int) $entitlement->fresh()->units_used);
    }

    public function test_paid_cohort_still_redirects_to_checkout_without_credit(): void
    {
        [$group, $cohort] = $this->seedPaidClass();
        $student = User::factory()->create(['role' => 'student', 'is_active' => true]);

        $this->actingAs($student)
            ->post(route('student.classes.enroll', $cohort))
            ->assertRedirect(route('public.groups.checkout', [
                'slug' => $group->slug,
                'cohort' => $cohort->id,
            ]));

        $this->assertDatabaseMissing('tutoring_cohort_enrollments', [
            'tutoring_group_cohort_id' => $cohort->id,
            'user_id' => $student->id,
        ]);
    }

    /**
     * @return array{0: TutoringGroup, 1: TutoringGroupCohort}
     */
    protected function seedPaidClass(): array
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $group = TutoringGroup::create([
            'type' => TutoringGroup::TYPE_COLLECTIVE,
            'title' => 'فصل مدفوع',
            'slug' => 'paid-class-'.uniqid(),
            'instructor_id' => $instructor->id,
            'price' => 199,
            'capacity' => 10,
            'duration_minutes' => 60,
            'sessions_per_month' => 4,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $start = Carbon::now()->next(Carbon::SATURDAY)->setTime(18, 0);
        $cohort = TutoringGroupCohort::create([
            'tutoring_group_id' => $group->id,
            'title' => 'دفعة مدفوعة',
            'slug' => 'paid-cohort-'.uniqid(),
            'starts_at' => $start,
            'study_days' => [6],
            'study_time' => '18:00',
            'sessions_count' => 4,
            'session_duration_minutes' => 60,
            'timezone' => 'Africa/Cairo',
            'capacity' => 10,
            'enrolled_count' => 0,
            'min_enrollment' => 1,
            'status' => TutoringGroupCohort::STATUS_OPEN,
            'is_visible' => true,
            'sort_order' => 0,
        ]);

        return [$group, $cohort];
    }

    protected function extendSchemaForPackagesAndPayments(): void
    {
        Schema::create('service_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('tagline')->nullable();
            $table->string('badge')->nullable();
            $table->string('scope')->default('global');
            $table->string('plan_type')->nullable();
            $table->unsignedTinyInteger('term_months')->nullable();
            $table->unsignedTinyInteger('weekly_group_sessions')->default(0);
            $table->unsignedTinyInteger('weekly_private_sessions')->default(0);
            $table->boolean('includes_community')->default(false);
            $table->boolean('includes_libraries')->default(false);
            $table->json('features')->nullable();
            $table->json('gifts')->nullable();
            $table->unsignedBigInteger('tutoring_group_id')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->unsignedBigInteger('academic_subject_id')->nullable();
            $table->unsignedInteger('units_count')->default(1);
            $table->unsignedInteger('session_minutes')->nullable();
            $table->unsignedInteger('duration_days')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('original_price', 10, 2)->nullable();
            $table->string('currency', 8)->default('USD');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->unsignedBigInteger('advanced_course_id')->nullable();
            $table->unsignedBigInteger('tutoring_group_id')->nullable();
            $table->unsignedBigInteger('tutoring_group_package_id')->nullable();
            $table->unsignedBigInteger('tutoring_group_cohort_id')->nullable();
            $table->unsignedBigInteger('service_package_id')->nullable();
            $table->json('custom_package_data')->nullable();
            $table->string('order_type')->default('course');
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->decimal('original_amount', 10, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('wallet_credit_amount', 10, 2)->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency', 8)->nullable();
            $table->string('billing_mode')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->string('payment_method')->nullable();
            $table->unsignedBigInteger('wallet_id')->nullable();
            $table->string('payment_proof')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->string('fawaterak_invoice_id')->nullable();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('sales_owner_id')->nullable();
            $table->timestamp('sales_contacted_at')->nullable();
            $table->unsignedBigInteger('sales_lead_id')->nullable();
            $table->timestamps();
        });
    }
}
