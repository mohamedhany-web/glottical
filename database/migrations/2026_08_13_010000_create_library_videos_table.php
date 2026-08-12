<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_folder_id')->nullable()->constrained('library_folders')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('external_url', 2000)->nullable();
            $table->string('file_path', 1000)->nullable();
            $table->string('storage_disk', 40)->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->string('mime_type', 120)->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_published', 'library_folder_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_videos');
    }
};
