<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('one_to_one_sessions')) {
            Schema::create('one_to_one_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_course_enrollment_id')->constrained('student_course_enrollments')->cascadeOnDelete();
                $table->foreignId('advanced_course_id')->constrained('advanced_courses')->cascadeOnDelete();
                $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
                $table->unsignedTinyInteger('session_number')->default(1);
                $table->timestamp('scheduled_at')->nullable();
                $table->unsignedSmallInteger('duration_minutes')->default(60);
                $table->string('status', 32)->default('pending_schedule');
                $table->foreignId('classroom_meeting_id')->nullable()->constrained('classroom_meetings')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['instructor_id', 'status', 'scheduled_at']);
                $table->index(['student_id', 'status', 'scheduled_at']);
            });
        }

        if (Schema::hasTable('classroom_meetings') && ! Schema::hasColumn('classroom_meetings', 'one_to_one_session_id')) {
            Schema::table('classroom_meetings', function (Blueprint $table) {
                $table->foreignId('one_to_one_session_id')
                    ->nullable()
                    ->after('consultation_request_id')
                    ->constrained('one_to_one_sessions')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasTable('orders') && ! Schema::hasColumn('orders', 'auto_renew')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->boolean('auto_renew')->default(false)->after('billing_mode');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('classroom_meetings') && Schema::hasColumn('classroom_meetings', 'one_to_one_session_id')) {
            Schema::table('classroom_meetings', function (Blueprint $table) {
                $table->dropConstrainedForeignId('one_to_one_session_id');
            });
        }

        Schema::dropIfExists('one_to_one_sessions');

        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'auto_renew')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('auto_renew');
            });
        }
    }
};
