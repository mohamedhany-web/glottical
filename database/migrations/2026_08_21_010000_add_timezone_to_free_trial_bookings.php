<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('free_trial_bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('free_trial_bookings', 'timezone')) {
                $table->string('timezone', 64)->nullable()->after('notes');
            }
            if (! Schema::hasColumn('free_trial_bookings', 'us_state')) {
                $table->string('us_state', 64)->nullable()->after('timezone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('free_trial_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('free_trial_bookings', 'us_state')) {
                $table->dropColumn('us_state');
            }
            if (Schema::hasColumn('free_trial_bookings', 'timezone')) {
                $table->dropColumn('timezone');
            }
        });
    }
};
