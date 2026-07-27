<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * إسقاط جداول موديل باقات مزايا المعلمين/SaaS القديم.
 * لا يمس اشتراكات الدورات الشهرية (حقول enrollments) ولا باقات الكورسات (packages).
 */
return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            // curriculum library
            'curriculum_library_preview_opens',
            'curriculum_library_materials',
            'curriculum_library_sections',
            'curriculum_library_item_files',
            'curriculum_library_category_user',
            'curriculum_library_items',
            'curriculum_library_categories',
            // hiring / academies
            'recruitment_teacher_presentations',
            'academy_opportunity_applications',
            'academy_opportunities',
            'hiring_academies',
            // portfolio
            'portfolio_project_images',
            'portfolio_projects',
            // AI suite
            'student_saved_ai_games',
            // SaaS subscriptions
            'subscription_requests',
            'subscriptions',
        ];

        Schema::disableForeignKeyConstraints();
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
        Schema::enableForeignKeyConstraints();

        if (Schema::hasTable('settings')) {
            DB::table('settings')->where('key', 'teacher_features')->delete();
        }
    }

    public function down(): void
    {
        // لا نستعيد جداول الموديل القديم — أُزيلت عمداً.
    }
};
