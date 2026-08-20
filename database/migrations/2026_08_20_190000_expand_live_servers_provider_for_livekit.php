<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('live_servers')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE live_servers MODIFY provider VARCHAR(32) NOT NULL DEFAULT 'livekit'");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('live_servers')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE live_servers MODIFY provider VARCHAR(32) NOT NULL DEFAULT 'jitsi'");
        }
    }
};
