<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('notifications') || Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        // `message` and morph class names were truncated by the old ENUMs.
        DB::statement("ALTER TABLE `notifications` MODIFY COLUMN `type` VARCHAR(40) NOT NULL DEFAULT 'general'");
        DB::statement("ALTER TABLE `notifications` MODIFY COLUMN `target_type` VARCHAR(191) NULL DEFAULT 'individual'");
    }

    public function down(): void
    {
        if (! Schema::hasTable('notifications') || Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("UPDATE `notifications` SET `type` = 'general' WHERE `type` NOT IN ('general', 'course', 'exam', 'assignment', 'grade', 'announcement', 'reminder', 'warning', 'system', 'employee')");
        DB::statement("UPDATE `notifications` SET `target_type` = 'individual' WHERE `target_type` IS NULL OR `target_type` NOT IN ('all_students', 'course_students', 'year_students', 'subject_students', 'individual')");
        DB::statement("ALTER TABLE `notifications` MODIFY COLUMN `type` ENUM('general', 'course', 'exam', 'assignment', 'grade', 'announcement', 'reminder', 'warning', 'system', 'employee') DEFAULT 'general'");
        DB::statement("ALTER TABLE `notifications` MODIFY COLUMN `target_type` ENUM('all_students', 'course_students', 'year_students', 'subject_students', 'individual') NOT NULL DEFAULT 'individual'");
    }
};
