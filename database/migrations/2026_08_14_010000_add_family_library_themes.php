<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('library_folders') && ! Schema::hasColumn('library_folders', 'content_theme')) {
            Schema::table('library_folders', function (Blueprint $table) {
                $table->string('content_theme', 40)->default('general')->after('kind');
                $table->index('content_theme');
            });
        }

        if (Schema::hasTable('lecture_materials')) {
            Schema::table('lecture_materials', function (Blueprint $table) {
                if (! Schema::hasColumn('lecture_materials', 'content_theme')) {
                    $table->string('content_theme', 40)->default('general')->after('title');
                }
                if (! Schema::hasColumn('lecture_materials', 'experience_mode')) {
                    $table->string('experience_mode', 20)->default('download')->after('content_theme');
                }
                if (! Schema::hasColumn('lecture_materials', 'description')) {
                    $table->text('description')->nullable()->after('experience_mode');
                }
            });
        }

        if (Schema::hasTable('library_videos')) {
            Schema::table('library_videos', function (Blueprint $table) {
                if (! Schema::hasColumn('library_videos', 'content_theme')) {
                    $table->string('content_theme', 40)->default('general')->after('audience');
                }
                if (! Schema::hasColumn('library_videos', 'series_title')) {
                    $table->string('series_title')->nullable()->after('title');
                }
                if (! Schema::hasColumn('library_videos', 'age_label')) {
                    $table->string('age_label', 40)->nullable()->after('series_title');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('library_folders') && Schema::hasColumn('library_folders', 'content_theme')) {
            Schema::table('library_folders', function (Blueprint $table) {
                $table->dropIndex(['content_theme']);
                $table->dropColumn('content_theme');
            });
        }

        if (Schema::hasTable('lecture_materials')) {
            Schema::table('lecture_materials', function (Blueprint $table) {
                foreach (['content_theme', 'experience_mode', 'description'] as $col) {
                    if (Schema::hasColumn('lecture_materials', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('library_videos')) {
            Schema::table('library_videos', function (Blueprint $table) {
                foreach (['content_theme', 'series_title', 'age_label'] as $col) {
                    if (Schema::hasColumn('library_videos', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
