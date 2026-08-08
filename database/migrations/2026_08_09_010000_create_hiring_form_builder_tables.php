<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('hiring_forms')) {
            Schema::create('hiring_forms', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->boolean('is_published')->default(true);
                $table->json('settings')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('hiring_form_fields')) {
            Schema::create('hiring_form_fields', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hiring_form_id')->constrained('hiring_forms')->cascadeOnDelete();
                $table->string('type', 40);
                $table->string('label');
                $table->text('help_text')->nullable();
                $table->string('placeholder')->nullable();
                $table->boolean('is_required')->default(false);
                $table->json('options')->nullable();
                $table->string('system_key', 60)->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->json('settings')->nullable();
                $table->timestamps();

                $table->index(['hiring_form_id', 'sort_order']);
            });
        }

        if (Schema::hasTable('tutor_applications')) {
            Schema::table('tutor_applications', function (Blueprint $table) {
                if (! Schema::hasColumn('tutor_applications', 'hiring_form_id')) {
                    $table->foreignId('hiring_form_id')->nullable()->after('id')->constrained('hiring_forms')->nullOnDelete();
                }
                if (! Schema::hasColumn('tutor_applications', 'answers')) {
                    $table->json('answers')->nullable()->after('intro_video_url');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tutor_applications')) {
            Schema::table('tutor_applications', function (Blueprint $table) {
                if (Schema::hasColumn('tutor_applications', 'hiring_form_id')) {
                    $table->dropConstrainedForeignId('hiring_form_id');
                }
                if (Schema::hasColumn('tutor_applications', 'answers')) {
                    $table->dropColumn('answers');
                }
            });
        }

        Schema::dropIfExists('hiring_form_fields');
        Schema::dropIfExists('hiring_forms');
    }
};
