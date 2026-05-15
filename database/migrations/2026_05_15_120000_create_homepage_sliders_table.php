<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_sliders', function (Blueprint $table) {
            $table->id();
            $table->string('source_type', 20)->default('course'); // course | path | custom
            $table->foreignId('advanced_course_id')->nullable()->constrained('advanced_courses')->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->string('kicker', 120)->nullable();
            $table->string('title', 255)->nullable();
            $table->text('subtitle')->nullable();
            $table->string('image_path', 500)->nullable();
            $table->string('primary_label', 120)->nullable();
            $table->string('primary_url', 500)->nullable();
            $table->string('secondary_label', 120)->nullable();
            $table->string('secondary_url', 500)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
            $table->index(['source_type', 'advanced_course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_sliders');
    }
};
