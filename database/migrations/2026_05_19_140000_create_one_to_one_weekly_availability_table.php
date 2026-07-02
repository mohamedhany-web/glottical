<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('one_to_one_weekly_availability')) {
            Schema::create('one_to_one_weekly_availability', function (Blueprint $table) {
                $table->id();
                $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
                $table->unsignedTinyInteger('day_of_week'); // 1=Monday .. 7=Sunday (ISO)
                $table->time('start_time');
                $table->time('end_time');
                $table->unsignedSmallInteger('slot_duration_minutes')->default(60);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['instructor_id', 'day_of_week', 'is_active'], 'oto_avail_instr_day_active_idx');
            });
        }

        if (Schema::hasTable('one_to_one_sessions') && ! Schema::hasColumn('one_to_one_sessions', 'booked_by_user_id')) {
            Schema::table('one_to_one_sessions', function (Blueprint $table) {
                $table->foreignId('booked_by_user_id')->nullable()->after('classroom_meeting_id')->constrained('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('one_to_one_sessions') && Schema::hasColumn('one_to_one_sessions', 'booked_by_user_id')) {
            Schema::table('one_to_one_sessions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('booked_by_user_id');
            });
        }

        Schema::dropIfExists('one_to_one_weekly_availability');
    }
};
