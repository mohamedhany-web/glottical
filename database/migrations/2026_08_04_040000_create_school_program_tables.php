<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('school_years')) {
            Schema::create('school_years', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('tagline')->nullable();
                $table->text('description')->nullable();
                $table->unsignedTinyInteger('level_number');
                $table->string('image_path')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
                $table->index(['is_active', 'sort_order']);
                $table->unique('level_number');
            });
        }

        if (! Schema::hasTable('school_subjects')) {
            Schema::create('school_subjects', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('icon', 64)->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
                $table->index(['is_active', 'sort_order']);
            });
        }

        if (Schema::hasTable('tutoring_groups')) {
            Schema::table('tutoring_groups', function (Blueprint $table) {
                if (! Schema::hasColumn('tutoring_groups', 'school_year_id')) {
                    $table->foreignId('school_year_id')->nullable()->after('learning_path')
                        ->constrained('school_years')->nullOnDelete();
                }
                if (! Schema::hasColumn('tutoring_groups', 'school_subject_id')) {
                    $table->foreignId('school_subject_id')->nullable()->after('school_year_id')
                        ->constrained('school_subjects')->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('free_trial_bookings')) {
            Schema::table('free_trial_bookings', function (Blueprint $table) {
                if (! Schema::hasColumn('free_trial_bookings', 'recommended_school_year_id')) {
                    $table->foreignId('recommended_school_year_id')->nullable()->after('notes')
                        ->constrained('school_years')->nullOnDelete();
                }
                if (! Schema::hasColumn('free_trial_bookings', 'admin_notes')) {
                    $table->text('admin_notes')->nullable()->after('recommended_school_year_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('free_trial_bookings')) {
            Schema::table('free_trial_bookings', function (Blueprint $table) {
                if (Schema::hasColumn('free_trial_bookings', 'recommended_school_year_id')) {
                    $table->dropConstrainedForeignId('recommended_school_year_id');
                }
                if (Schema::hasColumn('free_trial_bookings', 'admin_notes')) {
                    $table->dropColumn('admin_notes');
                }
            });
        }

        if (Schema::hasTable('tutoring_groups')) {
            Schema::table('tutoring_groups', function (Blueprint $table) {
                if (Schema::hasColumn('tutoring_groups', 'school_subject_id')) {
                    $table->dropConstrainedForeignId('school_subject_id');
                }
                if (Schema::hasColumn('tutoring_groups', 'school_year_id')) {
                    $table->dropConstrainedForeignId('school_year_id');
                }
            });
        }

        Schema::dropIfExists('school_subjects');
        Schema::dropIfExists('school_years');
    }
};
