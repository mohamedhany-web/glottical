<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('advanced_courses')) {
            Schema::table('advanced_courses', function (Blueprint $table) {
                if (! Schema::hasColumn('advanced_courses', 'delivery_type')) {
                    $table->string('delivery_type', 20)->default('group')->after('is_free')
                        ->comment('group = جماعي، one_to_one = فردي مع معلم');
                }
                if (! Schema::hasColumn('advanced_courses', 'billing_mode')) {
                    $table->string('billing_mode', 20)->default('one_time')->after('delivery_type')
                        ->comment('one_time = دفعة واحدة، monthly = اشتراك شهري');
                }
                if (! Schema::hasColumn('advanced_courses', 'monthly_price')) {
                    $table->decimal('monthly_price', 10, 2)->nullable()->after('price_after_discount');
                }
                if (! Schema::hasColumn('advanced_courses', 'monthly_price_after_discount')) {
                    $table->decimal('monthly_price_after_discount', 10, 2)->nullable()->after('monthly_price');
                }
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (! Schema::hasColumn('orders', 'billing_mode')) {
                    $table->string('billing_mode', 20)->default('one_time')->after('amount');
                }
            });
        }

        if (Schema::hasTable('student_course_enrollments')) {
            Schema::table('student_course_enrollments', function (Blueprint $table) {
                if (! Schema::hasColumn('student_course_enrollments', 'auto_renew')) {
                    $table->boolean('auto_renew')->default(false)->after('access_type');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('advanced_courses')) {
            Schema::table('advanced_courses', function (Blueprint $table) {
                foreach (['delivery_type', 'billing_mode', 'monthly_price', 'monthly_price_after_discount'] as $col) {
                    if (Schema::hasColumn('advanced_courses', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'billing_mode')) {
                    $table->dropColumn('billing_mode');
                }
            });
        }

        if (Schema::hasTable('student_course_enrollments')) {
            Schema::table('student_course_enrollments', function (Blueprint $table) {
                if (Schema::hasColumn('student_course_enrollments', 'auto_renew')) {
                    $table->dropColumn('auto_renew');
                }
            });
        }
    }
};
