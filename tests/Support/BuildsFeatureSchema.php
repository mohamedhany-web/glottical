<?php

namespace Tests\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * مخطط SQLite مصغّر للاختبارات — مشروع الهجرات يعتمد MySQL (information_schema).
 */
trait BuildsFeatureSchema
{
    protected function buildFeatureSchema(): void
    {
        Schema::dropAllTables();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('student');
            $table->boolean('is_active')->default(true);
            $table->string('gender')->nullable();
            $table->string('address')->nullable();
            $table->string('timezone', 64)->nullable();
            $table->text('bio')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('portfolio_intro_video_url')->nullable();
            $table->json('private_teaching_meta')->nullable();
            $table->json('portfolio_marketing_published')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('role_id');
            $table->timestamps();
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id');
            $table->foreignId('permission_id');
            $table->timestamps();
        });

        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('permission_id');
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('sender_id')->nullable();
            $table->string('title');
            $table->text('message')->nullable();
            $table->string('type')->nullable();
            $table->string('priority')->nullable();
            $table->string('audience')->nullable();
            $table->string('action_url')->nullable();
            $table->string('action_text')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_read')->default(false);
            $table->json('data')->nullable();
            $table->timestamps();
        });

        Schema::create('hiring_forms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_published')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        Schema::create('hiring_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hiring_form_id');
            $table->string('type', 40);
            $table->string('label');
            $table->text('help_text')->nullable();
            $table->string('placeholder')->nullable();
            $table->boolean('is_required')->default(false);
            $table->json('options')->nullable();
            $table->string('system_key', 60)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        Schema::create('tutor_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hiring_form_id')->nullable();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('nationality')->nullable();
            $table->string('city')->nullable();
            $table->string('gender')->nullable();
            $table->string('headline')->nullable();
            $table->text('bio')->nullable();
            $table->text('experience')->nullable();
            $table->text('education')->nullable();
            $table->unsignedInteger('years_experience')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('id_document_path')->nullable();
            $table->string('certificate_path')->nullable();
            $table->string('intro_video_path')->nullable();
            $table->string('intro_video_url')->nullable();
            $table->json('answers')->nullable();
            $table->string('status', 32)->default('draft');
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->foreignId('activated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('instructor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique();
            $table->string('headline')->nullable();
            $table->text('bio')->nullable();
            $table->text('experience')->nullable();
            $table->json('skills')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->json('social_links')->nullable();
            $table->timestamps();
        });

        Schema::create('tutoring_groups', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20);
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->foreignId('instructor_id');
            $table->decimal('price', 10, 2)->nullable();
            $table->unsignedInteger('hourly_rate')->nullable();
            $table->unsignedInteger('sessions_per_month')->nullable();
            $table->string('whatsapp_group_url')->nullable();
            $table->string('learning_path')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->unsignedBigInteger('academic_subject_id')->nullable();
            $table->string('currency', 8)->default('EGP');
            $table->unsignedSmallInteger('capacity')->default(1);
            $table->unsignedSmallInteger('duration_minutes')->default(60);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('tutoring_group_cohorts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutoring_group_id');
            $table->string('title');
            $table->string('slug');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->json('study_days')->nullable();
            $table->time('study_time')->nullable();
            $table->unsignedSmallInteger('sessions_count')->nullable();
            $table->unsignedSmallInteger('session_duration_minutes')->nullable();
            $table->string('timezone')->nullable();
            $table->unsignedInteger('capacity')->default(8);
            $table->unsignedInteger('enrolled_count')->default(0);
            $table->unsignedInteger('min_enrollment')->default(1);
            $table->string('status', 32)->default('open');
            $table->timestamp('postponed_to')->nullable();
            $table->string('whatsapp_group_url')->nullable();
            $table->timestamp('enrollment_closes_at')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('classroom_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->unsignedBigInteger('consultation_request_id')->nullable();
            $table->unsignedBigInteger('one_to_one_session_id')->nullable();
            $table->unsignedBigInteger('tutoring_group_booking_id')->nullable();
            $table->string('code', 32)->unique();
            $table->string('room_name', 64);
            $table->string('title')->nullable();
            $table->timestamp('scheduled_for')->nullable();
            $table->unsignedInteger('planned_duration_minutes')->nullable();
            $table->unsignedInteger('max_participants')->nullable();
            $table->unsignedInteger('participants_peak')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        Schema::create('tutoring_class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutoring_group_cohort_id');
            $table->foreignId('tutoring_group_id');
            $table->unsignedInteger('session_number')->default(1);
            $table->string('title')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->string('status', 32)->default('scheduled');
            $table->foreignId('classroom_meeting_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['tutoring_group_cohort_id', 'session_number'], 'cohort_session_number_unique');
        });

        Schema::create('tutoring_cohort_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutoring_group_cohort_id');
            $table->foreignId('user_id');
            $table->string('status', 32)->default('active');
            $table->timestamp('enrolled_at')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('student_service_entitlement_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['tutoring_group_cohort_id', 'user_id'], 'cohort_user_enrollment_unique');
        });

        Schema::create('tutoring_class_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutoring_class_session_id');
            $table->foreignId('user_id');
            $table->string('status', 32)->default('present');
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['tutoring_class_session_id', 'user_id'], 'session_user_attendance_unique');
        });

        Schema::create('student_service_entitlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->unsignedBigInteger('service_package_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('scope', 64)->default('global');
            $table->string('plan_type')->nullable();
            $table->unsignedInteger('term_months')->nullable();
            $table->unsignedInteger('weekly_group_sessions')->nullable();
            $table->unsignedInteger('weekly_private_sessions')->nullable();
            $table->boolean('includes_community')->default(false);
            $table->boolean('includes_libraries')->default(false);
            $table->unsignedBigInteger('tutoring_group_id')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->unsignedBigInteger('academic_subject_id')->nullable();
            $table->unsignedInteger('units_total')->default(0);
            $table->unsignedInteger('units_used')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('status', 32)->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('tutoring_group_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tutoring_group_id')->nullable();
            $table->unsignedBigInteger('student_service_entitlement_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('payment_status', 32)->nullable();
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('status', 32)->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('student_tutoring_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->unsignedBigInteger('tutoring_group_id')->nullable();
            $table->unsignedBigInteger('tutoring_group_package_id')->nullable();
            $table->unsignedInteger('sessions_total')->default(0);
            $table->unsignedInteger('sessions_used')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('status')->default('active');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('student_service_entitlement_id')->nullable();
            $table->timestamps();
        });
    }
}
