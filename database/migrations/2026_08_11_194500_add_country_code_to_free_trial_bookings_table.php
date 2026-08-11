<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('free_trial_bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('free_trial_bookings', 'country_code')) {
                $table->string('country_code', 12)->nullable()->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('free_trial_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('free_trial_bookings', 'country_code')) {
                $table->dropColumn('country_code');
            }
        });
    }
};
