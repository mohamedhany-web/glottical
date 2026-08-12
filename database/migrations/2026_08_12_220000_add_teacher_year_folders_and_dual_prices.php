<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('library_folders')) {
            Schema::table('library_folders', function (Blueprint $table) {
                if (! Schema::hasColumn('library_folders', 'instructor_id')) {
                    $table->foreignId('instructor_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
                }
                if (! Schema::hasColumn('library_folders', 'academic_year_id')) {
                    $table->unsignedBigInteger('academic_year_id')->nullable()->after('instructor_id')->index();
                }
                if (! Schema::hasColumn('library_folders', 'kind')) {
                    $table->string('kind', 20)->default('videos')->after('academic_year_id');
                }
                if (! Schema::hasColumn('library_folders', 'requires_library_entitlement')) {
                    $table->boolean('requires_library_entitlement')->default(true)->after('is_active');
                }
            });
        }

        if (Schema::hasTable('lecture_materials')) {
            Schema::table('lecture_materials', function (Blueprint $table) {
                if (! Schema::hasColumn('lecture_materials', 'library_folder_id')) {
                    $table->foreignId('library_folder_id')->nullable()->after('lecture_id')->constrained('library_folders')->nullOnDelete();
                }
            });

            // السماح بمواد فولدر بدون محاضرة
            try {
                Schema::table('lecture_materials', function (Blueprint $table) {
                    $table->unsignedBigInteger('lecture_id')->nullable()->change();
                });
            } catch (\Throwable $e) {
                // بعض المحركات قد لا تدعم change بدون doctrine — نتجاوز بهدوء
            }
        }

        if (Schema::hasTable('advanced_courses')) {
            Schema::table('advanced_courses', function (Blueprint $table) {
                if (! Schema::hasColumn('advanced_courses', 'price_egp')) {
                    $table->decimal('price_egp', 10, 2)->nullable()->after('price');
                }
                if (! Schema::hasColumn('advanced_courses', 'price_egp_after_discount')) {
                    $table->decimal('price_egp_after_discount', 10, 2)->nullable()->after('price_egp');
                }
                if (! Schema::hasColumn('advanced_courses', 'price_usd')) {
                    $table->decimal('price_usd', 10, 2)->nullable()->after('price_egp_after_discount');
                }
                if (! Schema::hasColumn('advanced_courses', 'price_usd_after_discount')) {
                    $table->decimal('price_usd_after_discount', 10, 2)->nullable()->after('price_usd');
                }
            });
        }

        if (Schema::hasTable('orders') && ! Schema::hasColumn('orders', 'currency')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('currency', 8)->nullable()->after('amount');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('advanced_courses')) {
            Schema::table('advanced_courses', function (Blueprint $table) {
                foreach (['price_egp', 'price_egp_after_discount', 'price_usd', 'price_usd_after_discount'] as $col) {
                    if (Schema::hasColumn('advanced_courses', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('lecture_materials') && Schema::hasColumn('lecture_materials', 'library_folder_id')) {
            Schema::table('lecture_materials', function (Blueprint $table) {
                $table->dropConstrainedForeignId('library_folder_id');
            });
        }

        if (Schema::hasTable('library_folders')) {
            Schema::table('library_folders', function (Blueprint $table) {
                if (Schema::hasColumn('library_folders', 'instructor_id')) {
                    $table->dropConstrainedForeignId('instructor_id');
                }
                foreach (['academic_year_id', 'kind', 'requires_library_entitlement'] as $col) {
                    if (Schema::hasColumn('library_folders', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
