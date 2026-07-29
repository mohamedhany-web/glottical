<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            if (! Schema::hasColumn('packages', 'currency')) {
                $table->string('currency', 10)->default('USD')->after('original_price');
            }
            if (! Schema::hasColumn('packages', 'track')) {
                $table->string('track', 32)->nullable()->after('currency');
            }
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            if (Schema::hasColumn('packages', 'track')) {
                $table->dropColumn('track');
            }
            if (Schema::hasColumn('packages', 'currency')) {
                $table->dropColumn('currency');
            }
        });
    }
};
