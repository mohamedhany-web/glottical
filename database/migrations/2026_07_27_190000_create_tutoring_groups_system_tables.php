<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutoring_groups', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20); // individual | collective
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('currency', 8)->default('EGP');
            $table->unsignedSmallInteger('capacity')->default(1);
            $table->unsignedSmallInteger('duration_minutes')->default(60);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['type', 'is_active']);
            $table->index(['instructor_id', 'type']);
        });

        Schema::create('tutor_work_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 1-7 ISO
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('slot_duration_minutes')->default(60);
            $table->string('applies_to', 20)->default('both'); // individual | collective | both
            $table->boolean('is_active')->default(true);
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['instructor_id', 'day_of_week', 'is_active']);
        });

        Schema::create('tutoring_group_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutoring_group_id')->constrained('tutoring_groups')->cascadeOnDelete();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('guest_name')->nullable();
            $table->string('guest_phone')->nullable();
            $table->string('guest_email')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('status', 20)->default('pending'); // pending|confirmed|cancelled|completed
            $table->text('admin_notes')->nullable();
            $table->text('student_notes')->nullable();
            $table->timestamps();

            $table->index(['instructor_id', 'starts_at', 'status']);
            $table->index(['tutoring_group_id', 'starts_at', 'status']);
            $table->index(['status', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutoring_group_bookings');
        Schema::dropIfExists('tutor_work_schedules');
        Schema::dropIfExists('tutoring_groups');
    }
};
