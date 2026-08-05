<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('one_to_one_sessions')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE one_to_one_sessions MODIFY student_course_enrollment_id BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE one_to_one_sessions MODIFY advanced_course_id BIGINT UNSIGNED NULL');

            return;
        }

        Schema::table('one_to_one_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('student_course_enrollment_id')->nullable()->change();
            $table->unsignedBigInteger('advanced_course_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('one_to_one_sessions')
            || DB::table('one_to_one_sessions')->whereNull('student_course_enrollment_id')->orWhereNull('advanced_course_id')->exists()) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE one_to_one_sessions MODIFY student_course_enrollment_id BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE one_to_one_sessions MODIFY advanced_course_id BIGINT UNSIGNED NOT NULL');

            return;
        }

        Schema::table('one_to_one_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('student_course_enrollment_id')->nullable(false)->change();
            $table->unsignedBigInteger('advanced_course_id')->nullable(false)->change();
        });
    }
};
