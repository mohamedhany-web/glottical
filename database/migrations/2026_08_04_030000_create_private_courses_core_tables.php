<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * بنية كورسات بريفيت الأساسية:
 * - محادثات/تذاكر طالب ↔ معلم (الإدارة تراها)
 * - استقبال الطالب على المنصة
 * - تتبع إشعار تسكين الطالب مع المعلم
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('private_lesson_threads')) {
            Schema::create('private_lesson_threads', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('student_instructor_assignment_id')->nullable()->constrained('student_instructor_assignments')->nullOnDelete();
                $table->foreignId('advanced_course_id')->nullable()->constrained('advanced_courses')->nullOnDelete();
                $table->foreignId('tutoring_group_id')->nullable()->constrained('tutoring_groups')->nullOnDelete();
                $table->string('subject', 255)->nullable();
                $table->string('status', 32)->default('open'); // open | pending_admin | closed
                $table->boolean('admin_visible')->default(true);
                $table->timestamp('last_message_at')->nullable();
                $table->timestamps();

                $table->index(['student_id', 'instructor_id'], 'plt_student_instructor_idx');
                $table->index(['status', 'last_message_at'], 'plt_status_last_msg_idx');
            });
        }

        if (! Schema::hasTable('private_lesson_messages')) {
            Schema::create('private_lesson_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('private_lesson_thread_id')->constrained('private_lesson_threads')->cascadeOnDelete();
                $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
                $table->string('sender_role', 32); // student | instructor | admin
                $table->text('body');
                $table->boolean('is_internal_note')->default(false); // ملاحظة داخلية للإدارة فقط
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->index(['private_lesson_thread_id', 'created_at'], 'plm_thread_created_idx');
            });
        }

        if (! Schema::hasTable('student_receptions')) {
            Schema::create('student_receptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('instructor_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('channel', 40)->default('platform'); // platform | whatsapp | phone
                $table->string('status', 32)->default('pending'); // pending | welcomed | completed
                $table->string('source', 64)->nullable(); // private_course | assignment | purchase
                $table->text('notes')->nullable();
                $table->json('checklist')->nullable();
                $table->timestamp('welcomed_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->unique('student_id');
                $table->index(['status', 'created_at']);
            });
        }

        if (Schema::hasTable('student_instructor_assignments')
            && ! Schema::hasColumn('student_instructor_assignments', 'instructor_notified_at')) {
            Schema::table('student_instructor_assignments', function (Blueprint $table) {
                $table->timestamp('instructor_notified_at')->nullable()->after('ends_at');
                $table->timestamp('student_notified_at')->nullable()->after('instructor_notified_at');
            });
        }

        if (Schema::hasTable('one_to_one_sessions')
            && ! Schema::hasColumn('one_to_one_sessions', 'is_private_lecture')) {
            Schema::table('one_to_one_sessions', function (Blueprint $table) {
                $table->boolean('is_private_lecture')->default(true)->after('duration_minutes');
                $table->string('system_channel', 40)->default('private_courses')->after('is_private_lecture');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('one_to_one_sessions')
            && Schema::hasColumn('one_to_one_sessions', 'is_private_lecture')) {
            Schema::table('one_to_one_sessions', function (Blueprint $table) {
                $table->dropColumn(['is_private_lecture', 'system_channel']);
            });
        }

        if (Schema::hasTable('student_instructor_assignments')
            && Schema::hasColumn('student_instructor_assignments', 'instructor_notified_at')) {
            Schema::table('student_instructor_assignments', function (Blueprint $table) {
                $table->dropColumn(['instructor_notified_at', 'student_notified_at']);
            });
        }

        Schema::dropIfExists('private_lesson_messages');
        Schema::dropIfExists('private_lesson_threads');
        Schema::dropIfExists('student_receptions');
    }
};
