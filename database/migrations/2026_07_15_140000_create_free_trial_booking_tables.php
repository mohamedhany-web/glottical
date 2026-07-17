<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('free_trial_weekly_availability', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('day_of_week'); // 1=Mon … 7=Sun
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('slot_duration_minutes')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['day_of_week', 'is_active']);
        });

        Schema::create('free_trial_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 64)->nullable();
            $table->string('goal')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->unsignedSmallInteger('duration_minutes')->default(30);
            $table->string('status', 32)->default('confirmed'); // confirmed|cancelled|completed
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['starts_at', 'status']);
        });

        $rows = [];
        foreach ([1, 2, 3, 4, 5, 6] as $day) {
            $rows[] = [
                'day_of_week' => $day,
                'start_time' => '10:00:00',
                'end_time' => '18:00:00',
                'slot_duration_minutes' => 30,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $rows[] = [
                'day_of_week' => $day,
                'start_time' => '18:00:00',
                'end_time' => '21:00:00',
                'slot_duration_minutes' => 30,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('free_trial_weekly_availability')->insert($rows);
    }

    public function down(): void
    {
        Schema::dropIfExists('free_trial_bookings');
        Schema::dropIfExists('free_trial_weekly_availability');
    }
};
