<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tutor_applications')) {
            return;
        }

        Schema::create('tutor_applications', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone', 40)->nullable();
            $table->string('nationality', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('gender', 16)->nullable();
            $table->string('headline')->nullable();
            $table->text('bio')->nullable();
            $table->text('experience')->nullable();
            $table->string('education')->nullable();
            $table->unsignedTinyInteger('years_experience')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('id_document_path')->nullable();
            $table->string('certificate_path')->nullable();
            $table->string('intro_video_path')->nullable();
            $table->string('intro_video_url')->nullable();
            $table->string('status', 32)->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutor_applications');
    }
};
