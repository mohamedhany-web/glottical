<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tutoring_cohort_enrollments')) {
            Schema::create('tutoring_cohort_enrollments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tutoring_group_cohort_id')->constrained('tutoring_group_cohorts')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('status', 32)->default('active');
                $table->timestamp('enrolled_at')->nullable();
                $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->foreignId('student_service_entitlement_id')->nullable()->constrained('student_service_entitlements')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['tutoring_group_cohort_id', 'user_id'], 'cohort_user_enrollment_unique');
                $table->index(['user_id', 'status']);
                $table->index('status');
            });
        }

        if (! Schema::hasTable('tutoring_class_sessions')) {
            Schema::create('tutoring_class_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tutoring_group_cohort_id')->constrained('tutoring_group_cohorts')->cascadeOnDelete();
                $table->foreignId('tutoring_group_id')->constrained('tutoring_groups')->cascadeOnDelete();
                $table->unsignedInteger('session_number')->default(1);
                $table->string('title')->nullable();
                $table->timestamp('starts_at');
                $table->timestamp('ends_at')->nullable();
                $table->string('status', 32)->default('scheduled');
                $table->foreignId('classroom_meeting_id')->nullable()->constrained('classroom_meetings')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['tutoring_group_cohort_id', 'session_number'], 'cohort_session_number_unique');
                $table->index(['starts_at', 'status']);
                $table->index('tutoring_group_id');
            });
        }

        if (! Schema::hasTable('tutoring_class_attendances')) {
            Schema::create('tutoring_class_attendances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tutoring_class_session_id')->constrained('tutoring_class_sessions')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('status', 32)->default('present');
                $table->timestamp('joined_at')->nullable();
                $table->timestamp('left_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['tutoring_class_session_id', 'user_id'], 'session_user_attendance_unique');
            });
        }

        if (Schema::hasTable('tutoring_group_cohorts')) {
            Schema::table('tutoring_group_cohorts', function (Blueprint $table) {
                if (! Schema::hasColumn('tutoring_group_cohorts', 'sessions_count')) {
                    $table->unsignedSmallInteger('sessions_count')->nullable()->after('study_time');
                }
                if (! Schema::hasColumn('tutoring_group_cohorts', 'session_duration_minutes')) {
                    $table->unsignedSmallInteger('session_duration_minutes')->nullable()->after('sessions_count');
                }
                if (! Schema::hasColumn('tutoring_group_cohorts', 'ends_at')) {
                    $table->timestamp('ends_at')->nullable()->after('starts_at');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tutoring_class_attendances');
        Schema::dropIfExists('tutoring_class_sessions');
        Schema::dropIfExists('tutoring_cohort_enrollments');

        if (Schema::hasTable('tutoring_group_cohorts')) {
            Schema::table('tutoring_group_cohorts', function (Blueprint $table) {
                foreach (['sessions_count', 'session_duration_minutes', 'ends_at'] as $col) {
                    if (Schema::hasColumn('tutoring_group_cohorts', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
