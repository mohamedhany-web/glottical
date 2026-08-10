<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('student_xp_ledger')) {
            Schema::create('student_xp_ledger', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->integer('amount');
                $table->unsignedInteger('balance_after');
                $table->string('reason', 64);
                $table->string('source_type', 64)->nullable();
                $table->unsignedBigInteger('source_id')->nullable();
                $table->foreignId('tutoring_group_cohort_id')->nullable()->constrained('tutoring_group_cohorts')->nullOnDelete();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'reason', 'source_type', 'source_id'], 'student_xp_idempotent');
                $table->index(['user_id', 'created_at']);
                $table->index(['tutoring_group_cohort_id', 'created_at'], 'student_xp_cohort_created_idx');
            });
        }

        if (! Schema::hasTable('student_learning_streaks')) {
            Schema::create('student_learning_streaks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
                $table->unsignedSmallInteger('current_streak')->default(0);
                $table->unsignedSmallInteger('longest_streak')->default(0);
                $table->date('last_activity_date')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('school_missions')) {
            Schema::create('school_missions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tutoring_group_cohort_id')->nullable()->constrained('tutoring_group_cohorts')->cascadeOnDelete();
                $table->string('code', 64);
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('cadence', 16)->default('daily'); // daily|weekly
                $table->string('mission_type', 32); // attend_session|submit_assignment|exam_complete|earn_xp
                $table->unsignedSmallInteger('target_count')->default(1);
                $table->unsignedInteger('xp_reward')->default(0);
                $table->boolean('is_active')->default(true);
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();

                $table->unique(['code', 'tutoring_group_cohort_id'], 'school_missions_code_cohort_unique');
            });
        }

        if (! Schema::hasTable('school_mission_progress')) {
            Schema::create('school_mission_progress', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_mission_id')->constrained('school_missions')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('period_key', 32); // Y-m-d or Y-W
                $table->unsignedSmallInteger('progress_count')->default(0);
                $table->string('status', 16)->default('active'); // active|completed
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->unique(['school_mission_id', 'user_id', 'period_key'], 'school_mission_progress_unique');
                $table->index(['user_id', 'status']);
            });
        }

        if (! Schema::hasTable('class_feed_posts')) {
            Schema::create('class_feed_posts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tutoring_group_cohort_id')->constrained('tutoring_group_cohorts')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('post_type', 24)->default('question'); // announcement|question
                $table->text('body');
                $table->boolean('is_pinned')->default(false);
                $table->boolean('is_hidden')->default(false);
                $table->foreignId('hidden_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('hidden_at')->nullable();
                $table->timestamps();

                $table->index(['tutoring_group_cohort_id', 'created_at'], 'class_feed_posts_cohort_created_idx');
            });
        }

        if (! Schema::hasTable('class_feed_comments')) {
            Schema::create('class_feed_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('class_feed_post_id')->constrained('class_feed_posts')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->text('body');
                $table->boolean('is_hidden')->default(false);
                $table->foreignId('hidden_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('hidden_at')->nullable();
                $table->timestamps();

                $table->index(['class_feed_post_id', 'created_at'], 'class_feed_comments_post_created_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('class_feed_comments');
        Schema::dropIfExists('class_feed_posts');
        Schema::dropIfExists('school_mission_progress');
        Schema::dropIfExists('school_missions');
        Schema::dropIfExists('student_learning_streaks');
        Schema::dropIfExists('student_xp_ledger');
    }
};
