<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('lecture_materials')) {
            return;
        }

        Schema::table('lecture_materials', function (Blueprint $table) {
            if (! Schema::hasColumn('lecture_materials', 'storage_disk')) {
                $table->string('storage_disk', 32)->default('public')->after('file_path');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('lecture_materials')) {
            return;
        }

        Schema::table('lecture_materials', function (Blueprint $table) {
            if (Schema::hasColumn('lecture_materials', 'storage_disk')) {
                $table->dropColumn('storage_disk');
            }
        });
    }
};
