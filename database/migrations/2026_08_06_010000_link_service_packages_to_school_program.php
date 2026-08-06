<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('service_packages')) {
            Schema::table('service_packages', function (Blueprint $table) {
                if (! Schema::hasColumn('service_packages', 'academic_year_id')) {
                    $table->foreignId('academic_year_id')
                        ->nullable()
                        ->after('tutoring_group_id')
                        ->constrained('academic_years')
                        ->nullOnDelete();
                }
                if (! Schema::hasColumn('service_packages', 'academic_subject_id')) {
                    $table->foreignId('academic_subject_id')
                        ->nullable()
                        ->after('academic_year_id')
                        ->constrained('academic_subjects')
                        ->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('student_service_entitlements')) {
            Schema::table('student_service_entitlements', function (Blueprint $table) {
                if (! Schema::hasColumn('student_service_entitlements', 'academic_year_id')) {
                    $table->foreignId('academic_year_id')
                        ->nullable()
                        ->after('tutoring_group_id')
                        ->constrained('academic_years')
                        ->nullOnDelete();
                }
                if (! Schema::hasColumn('student_service_entitlements', 'academic_subject_id')) {
                    $table->foreignId('academic_subject_id')
                        ->nullable()
                        ->after('academic_year_id')
                        ->constrained('academic_subjects')
                        ->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('student_service_entitlements')) {
            Schema::table('student_service_entitlements', function (Blueprint $table) {
                if (Schema::hasColumn('student_service_entitlements', 'academic_subject_id')) {
                    $table->dropConstrainedForeignId('academic_subject_id');
                }
                if (Schema::hasColumn('student_service_entitlements', 'academic_year_id')) {
                    $table->dropConstrainedForeignId('academic_year_id');
                }
            });
        }

        if (Schema::hasTable('service_packages')) {
            Schema::table('service_packages', function (Blueprint $table) {
                if (Schema::hasColumn('service_packages', 'academic_subject_id')) {
                    $table->dropConstrainedForeignId('academic_subject_id');
                }
                if (Schema::hasColumn('service_packages', 'academic_year_id')) {
                    $table->dropConstrainedForeignId('academic_year_id');
                }
            });
        }
    }
};
