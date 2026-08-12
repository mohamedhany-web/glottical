<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('one_to_one_sessions')) {
            return;
        }

        Schema::table('one_to_one_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('one_to_one_sessions', 'series_id')) {
                $table->string('series_id', 36)->nullable()->after('notes')->index();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('one_to_one_sessions')) {
            return;
        }

        Schema::table('one_to_one_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('one_to_one_sessions', 'series_id')) {
                $table->dropColumn('series_id');
            }
        });
    }
};
