<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * يعيد إنشاء جداول مكتبة المناهج التفاعلية (Manahij X) بعد حذف SaaS،
 * دون المساس بجداول CurriculumItem / course_sections الحالية.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('curriculum_library_categories')) {
            Schema::create('curriculum_library_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->unsignedInteger('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->boolean('is_restricted')->default(false);
                $table->timestamps();
            });
        } elseif (! Schema::hasColumn('curriculum_library_categories', 'is_restricted')) {
            Schema::table('curriculum_library_categories', function (Blueprint $table) {
                $table->boolean('is_restricted')->default(false)->after('is_active');
            });
        }

        if (! Schema::hasTable('curriculum_library_items')) {
            Schema::create('curriculum_library_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->nullable()->constrained('curriculum_library_categories')->nullOnDelete();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->longText('content')->nullable();
                $table->string('grade_level', 50)->nullable();
                $table->string('subject', 100)->nullable();
                $table->string('language', 5)->default('ar');
                $table->string('item_type', 20)->default('presentation');
                $table->json('meta')->nullable();
                $table->unsignedInteger('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->boolean('is_free_preview')->default(false);
                $table->timestamps();
            });
        } else {
            Schema::table('curriculum_library_items', function (Blueprint $table) {
                if (! Schema::hasColumn('curriculum_library_items', 'language')) {
                    $table->string('language', 5)->default('ar');
                }
                if (! Schema::hasColumn('curriculum_library_items', 'item_type')) {
                    $table->string('item_type', 20)->default('presentation');
                }
                if (! Schema::hasColumn('curriculum_library_items', 'is_free_preview')) {
                    $table->boolean('is_free_preview')->default(false);
                }
            });
        }

        if (! Schema::hasTable('curriculum_library_item_files')) {
            Schema::create('curriculum_library_item_files', function (Blueprint $table) {
                $table->id();
                $table->foreignId('curriculum_library_item_id')->constrained('curriculum_library_items')->cascadeOnDelete();
                $table->string('path');
                $table->string('storage_disk', 40)->default('public');
                $table->string('label')->nullable();
                $table->string('file_type', 20)->default('presentation');
                $table->unsignedInteger('order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('curriculum_library_preview_opens')) {
            Schema::create('curriculum_library_preview_opens', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('curriculum_library_item_id');
                $table->timestamp('opened_at');
                $table->unique('user_id');
                $table->foreign('curriculum_library_item_id', 'cl_preview_item_fk')
                    ->references('id')->on('curriculum_library_items')->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('curriculum_library_category_user')) {
            Schema::create('curriculum_library_category_user', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('category_id');
                $table->unsignedBigInteger('user_id');
                $table->timestamps();
                $table->unique(['category_id', 'user_id'], 'cl_cat_user_unique');
                $table->foreign('category_id', 'fk_cl_cu_cat')->references('id')->on('curriculum_library_categories')->cascadeOnDelete();
                $table->foreign('user_id', 'fk_cl_cu_usr')->references('id')->on('users')->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('curriculum_library_sections')) {
            Schema::create('curriculum_library_sections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('curriculum_library_item_id')
                    ->constrained('curriculum_library_items')
                    ->cascadeOnDelete();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->string('title');
                $table->text('description')->nullable();
                $table->unsignedInteger('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            Schema::table('curriculum_library_sections', function (Blueprint $table) {
                $table->foreign('parent_id', 'fk_cl_sec_parent')
                    ->references('id')
                    ->on('curriculum_library_sections')
                    ->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('curriculum_library_materials')) {
            Schema::create('curriculum_library_materials', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('curriculum_library_section_id');
                $table->string('title')->nullable();
                $table->string('path');
                $table->string('storage_disk', 32)->default('r2');
                $table->string('original_name')->nullable();
                $table->string('file_kind', 20)->default('other');
                $table->boolean('view_in_platform')->default(true);
                $table->boolean('allow_download')->default(false);
                $table->unsignedInteger('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->string('animation_video_path')->nullable();
                $table->string('animation_video_disk', 32)->nullable();
                $table->string('animation_video_original_name')->nullable();
                $table->string('animation_video_mime', 128)->nullable();
                $table->unsignedBigInteger('animation_video_size')->nullable();
                $table->timestamp('animation_video_uploaded_at')->nullable();
                $table->timestamps();

                $table->foreign('curriculum_library_section_id', 'fk_cl_mat_sec')
                    ->references('id')
                    ->on('curriculum_library_sections')
                    ->cascadeOnDelete();
            });
        } else {
            Schema::table('curriculum_library_materials', function (Blueprint $table) {
                foreach ([
                    'animation_video_path' => fn (Blueprint $t) => $t->string('animation_video_path')->nullable(),
                    'animation_video_disk' => fn (Blueprint $t) => $t->string('animation_video_disk', 32)->nullable(),
                    'animation_video_original_name' => fn (Blueprint $t) => $t->string('animation_video_original_name')->nullable(),
                    'animation_video_mime' => fn (Blueprint $t) => $t->string('animation_video_mime', 128)->nullable(),
                    'animation_video_size' => fn (Blueprint $t) => $t->unsignedBigInteger('animation_video_size')->nullable(),
                    'animation_video_uploaded_at' => fn (Blueprint $t) => $t->timestamp('animation_video_uploaded_at')->nullable(),
                ] as $col => $def) {
                    if (! Schema::hasColumn('curriculum_library_materials', $col)) {
                        $def($table);
                    }
                }
            });
        }

        if (! Schema::hasTable('curriculum_presentation_derivatives')) {
            Schema::create('curriculum_presentation_derivatives', function (Blueprint $table) {
                $table->id();
                $table->string('source_type', 32);
                $table->unsignedBigInteger('source_id');
                $table->string('storage_disk', 32)->default('r2');
                $table->string('manifest_path')->nullable();
                $table->string('status', 32)->default('pending');
                $table->unsignedInteger('slide_count')->nullable();
                $table->unsignedInteger('width')->nullable();
                $table->unsignedInteger('height')->nullable();
                $table->unsignedInteger('version')->default(1);
                $table->string('source_checksum', 128)->nullable();
                $table->text('error_message')->nullable();
                $table->string('engine', 64)->nullable();
                $table->timestamp('ready_at')->nullable();
                $table->timestamps();

                $table->unique(['source_type', 'source_id'], 'cpd_source_unique');
                $table->index(['status', 'updated_at'], 'cpd_status_updated_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_presentation_derivatives');
        Schema::dropIfExists('curriculum_library_materials');
        Schema::dropIfExists('curriculum_library_sections');
        Schema::dropIfExists('curriculum_library_category_user');
        Schema::dropIfExists('curriculum_library_preview_opens');
        Schema::dropIfExists('curriculum_library_item_files');
        Schema::dropIfExists('curriculum_library_items');
        Schema::dropIfExists('curriculum_library_categories');
    }
};
