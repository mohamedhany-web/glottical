<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_folders', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('slug')->unique();
            $table->string('description_ar')->nullable();
            $table->string('description_en')->nullable();
            $table->string('icon', 64)->default('fas fa-folder');
            $table->string('color', 32)->default('blue');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        if (Schema::hasTable('live_recordings') && ! Schema::hasColumn('live_recordings', 'library_folder_id')) {
            Schema::table('live_recordings', function (Blueprint $table) {
                $table->foreignId('library_folder_id')
                    ->nullable()
                    ->after('session_id')
                    ->constrained('library_folders')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('live_recordings') && Schema::hasColumn('live_recordings', 'library_folder_id')) {
            Schema::table('live_recordings', function (Blueprint $table) {
                $table->dropConstrainedForeignId('library_folder_id');
            });
        }

        Schema::dropIfExists('library_folders');
    }
};
