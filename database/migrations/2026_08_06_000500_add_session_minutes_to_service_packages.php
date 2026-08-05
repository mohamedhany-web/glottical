<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('service_packages') && ! Schema::hasColumn('service_packages', 'session_minutes')) {
            Schema::table('service_packages', function (Blueprint $table) {
                $table->unsignedSmallInteger('session_minutes')->nullable()->after('units_count');
            });

            DB::table('service_packages')->whereNull('session_minutes')->update(['session_minutes' => 60]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('service_packages') && Schema::hasColumn('service_packages', 'session_minutes')) {
            Schema::table('service_packages', function (Blueprint $table) {
                $table->dropColumn('session_minutes');
            });
        }
    }
};
