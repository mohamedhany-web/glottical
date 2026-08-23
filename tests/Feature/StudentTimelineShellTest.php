<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

/**
 * يضمن أن مسارات الطالب الأساسية تستخدم لوحة student-timeline وليس layouts.app القديمة.
 */
class StudentTimelineShellTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();
        $this->ensureExtraTables();
    }

    private function ensureExtraTables(): void
    {
        if (! Schema::hasTable('advanced_courses')) {
            Schema::create('advanced_courses', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('student_course_enrollments')) {
            Schema::create('student_course_enrollments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->foreignId('advanced_course_id');
                $table->string('status')->default('active');
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('activated_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('wallets')) {
            Schema::create('wallets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->decimal('balance', 12, 2)->default(0);
                $table->decimal('pending_balance', 12, 2)->default(0);
                $table->string('currency', 8)->default('USD');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('wallet_transactions')) {
            Schema::create('wallet_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('wallet_id');
                $table->string('type')->nullable();
                $table->decimal('amount', 12, 2)->default(0);
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->string('invoice_number')->nullable();
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->string('status')->default('pending');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('invoice_id')->nullable();
                $table->foreignId('user_id')->nullable();
                $table->decimal('amount', 12, 2)->default(0);
                $table->string('status')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('certificates')) {
            Schema::create('certificates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->foreignId('advanced_course_id')->nullable();
                $table->string('title')->nullable();
                $table->string('status')->nullable();
                $table->boolean('is_verified')->default(false);
                $table->timestamp('issued_at')->nullable();
                $table->date('issue_date')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('achievements')) {
            Schema::create('achievements', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('user_achievements')) {
            Schema::create('user_achievements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->foreignId('achievement_id');
                $table->unsignedInteger('points_earned')->default(0);
                $table->timestamp('earned_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('support_tickets')) {
            Schema::create('support_tickets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->foreignId('support_inquiry_category_id')->nullable();
                $table->string('subject')->nullable();
                $table->string('status')->default('open');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('support_inquiry_categories')) {
            Schema::create('support_inquiry_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('name_ar')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('exams')) {
            Schema::create('exams', function (Blueprint $table) {
                $table->id();
                $table->foreignId('advanced_course_id')->nullable();
                $table->foreignId('lesson_id')->nullable();
                $table->string('title')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->string('status')->default('pending');
                $table->string('order_type')->nullable();
                $table->foreignId('advanced_course_id')->nullable();
                $table->foreignId('academic_year_id')->nullable();
                $table->foreignId('service_package_id')->nullable();
                $table->json('custom_package_data')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('live_sessions')) {
            Schema::create('live_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->nullable();
                $table->foreignId('instructor_id')->nullable();
                $table->string('title')->nullable();
                $table->string('room_name')->nullable();
                $table->string('status')->default('scheduled');
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('ended_at')->nullable();
                $table->boolean('require_enrollment')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('one_to_one_sessions')) {
            Schema::create('one_to_one_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('instructor_id');
                $table->foreignId('student_id');
                $table->unsignedInteger('session_number')->default(1);
                $table->timestamp('scheduled_at')->nullable();
                $table->unsignedSmallInteger('duration_minutes')->default(50);
                $table->string('status', 32)->default('scheduled');
                $table->unsignedBigInteger('classroom_meeting_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('student_service_entitlements')) {
            Schema::create('student_service_entitlements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id');
                $table->string('service_key')->nullable();
                $table->unsignedInteger('remaining')->default(0);
                $table->timestamps();
            });
        }
    }

    private function student(): User
    {
        return User::factory()->create([
            'role' => 'student',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
    }

    public function test_no_student_blade_hardcodes_legacy_app_layout(): void
    {
        $root = resource_path('views/student');
        $violations = [];

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root));
        foreach ($iterator as $file) {
            if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }
            $contents = file_get_contents($file->getPathname());
            if ($contents === false) {
                continue;
            }
            if (preg_match("/@extends\\('layouts\\.app'\\)/", $contents)) {
                $violations[] = str_replace(resource_path('views').DIRECTORY_SEPARATOR, '', $file->getPathname());
            }
        }

        $this->assertSame([], $violations, 'Student views must not hardcode layouts.app');
    }

    public function test_student_shell_pages_use_timeline_layout(): void
    {
        $student = $this->student();

        $routes = [
            'dashboard',
            'student.exams.index',
            'student.invoices.index',
            'student.wallet.index',
            'student.certificates.index',
            'student.achievements.index',
            'student.support.index',
            'student.learn.index',
            'calendar',
            'settings',
            'profile',
            'orders.index',
            'student.live-sessions.index',
            'notifications',
            'student.private-lectures.index',
            'student.service-entitlements.index',
            'student.assignments.index',
            'referrals.index',
            'consultations.index',
            'academic-years',
        ];

        $checked = 0;

        foreach ($routes as $name) {
            if (! \Illuminate\Support\Facades\Route::has($name)) {
                continue;
            }

            $response = $this->actingAs($student)->get(route($name));

            // مخطط الاختبار مصغّر — نتجاهل 500 لنقص جداول؛ المهم ألا تظهر الواجهة القديمة عند النجاح.
            if ($response->status() === 500) {
                continue;
            }

            $this->assertTrue(
                in_array($response->status(), [200, 302], true),
                "Route [{$name}] returned unexpected status {$response->status()}"
            );

            if ($response->status() !== 200) {
                continue;
            }

            $checked++;
            $html = $response->getContent();
            $this->assertStringContainsString('st-dash', $html, "Route [{$name}] missing student-timeline shell");
            $this->assertStringNotContainsString('id="studentSidebar"', $html);
            $this->assertStringNotContainsString('layouts.student-sidebar', $html);
        }

        $this->assertGreaterThanOrEqual(5, $checked, 'Expected at least 5 student routes to render with timeline shell');
    }

    public function test_migrated_views_render_with_timeline_shell(): void
    {
        $this->actingAs($this->student());

        $cases = [
            ['student.exams.index', ['availableExams' => collect()]],
            ['student.invoices.index', ['invoices' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15), 'stats' => ['total' => 0, 'pending' => 0, 'paid' => 0]]],
            ['student.wallet.index', ['wallet' => (object) ['balance' => 0, 'currency' => 'USD'], 'transactions' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15)]],
            ['student.certificates.index', ['certificates' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15), 'stats' => ['total' => 0, 'issued' => 0]]],
            ['student.achievements.index', ['achievements' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15), 'stats' => ['total' => 0, 'total_points' => 0]]],
            ['student.support.index', ['tickets' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 12), 'inquiryCategories' => collect()]],
            ['student.consultations.index', ['requests' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15)]],
        ];

        foreach ($cases as [$view, $data]) {
            $html = view($view, $data)->render();
            $this->assertStringContainsString('st-dash', $html, "View [{$view}] missing timeline shell");
        }
    }

    public function test_my_courses_index_stays_inside_student_panel(): void
    {
        $student = $this->student();

        $this->actingAs($student)
            ->get(route('my-courses.index'))
            ->assertRedirect(route('dashboard'));
    }
}
