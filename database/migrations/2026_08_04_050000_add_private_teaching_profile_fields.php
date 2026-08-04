<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (! Schema::hasColumn('users', 'gender')) {
                    $table->string('gender', 16)->nullable()->after('bio');
                }
                if (! Schema::hasColumn('users', 'private_teaching_meta')) {
                    $table->json('private_teaching_meta')->nullable()->after('portfolio_marketing_published');
                }
            });
        }

        if (Schema::hasTable('one_to_one_weekly_availability')
            && Schema::hasColumn('one_to_one_weekly_availability', 'slot_duration_minutes')) {
            DB::table('one_to_one_weekly_availability')
                ->whereNull('slot_duration_minutes')
                ->orWhere('slot_duration_minutes', 60)
                ->update(['slot_duration_minutes' => 50]);
        }

        if (Schema::hasTable('one_to_one_sessions')
            && Schema::hasColumn('one_to_one_sessions', 'duration_minutes')) {
            DB::table('one_to_one_sessions')
                ->where(function ($q) {
                    $q->whereNull('duration_minutes')->orWhere('duration_minutes', 60);
                })
                ->whereIn('status', ['pending_schedule', 'scheduled'])
                ->update(['duration_minutes' => 50]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'private_teaching_meta')) {
                    $table->dropColumn('private_teaching_meta');
                }
                // keep gender — may be used elsewhere
            });
        }
    }
};
