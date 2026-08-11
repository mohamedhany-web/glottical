<?php

namespace Database\Seeders;

use App\Models\LibraryFolder;
use Illuminate\Database\Seeder;

class LibraryFolderSeeder extends Seeder
{
    public function run(): void
    {
        $folders = [
            [
                'name_ar' => 'أساسيات',
                'name_en' => 'Basics',
                'slug' => 'basics',
                'description_ar' => 'مقاطع تأسيسية للمبتدئات',
                'description_en' => 'Foundation clips for beginners',
                'icon' => 'fas fa-seedling',
                'color' => 'green',
                'sort_order' => 10,
            ],
            [
                'name_ar' => 'محادثة',
                'name_en' => 'Conversation',
                'slug' => 'conversation',
                'description_ar' => 'تمارين محادثة واستماع',
                'description_en' => 'Speaking and listening practice',
                'icon' => 'fas fa-comments',
                'color' => 'blue',
                'sort_order' => 20,
            ],
            [
                'name_ar' => 'قواعد',
                'name_en' => 'Grammar',
                'slug' => 'grammar',
                'description_ar' => 'شرح القواعد بطريقة مبسطة',
                'description_en' => 'Simplified grammar lessons',
                'icon' => 'fas fa-book',
                'color' => 'purple',
                'sort_order' => 30,
            ],
            [
                'name_ar' => 'تسجيلات الحصص',
                'name_en' => 'Class recordings',
                'slug' => 'class-recordings',
                'description_ar' => 'تسجيلات الجلسات المباشرة',
                'description_en' => 'Live session recordings',
                'icon' => 'fas fa-video',
                'color' => 'orange',
                'sort_order' => 40,
            ],
        ];

        foreach ($folders as $row) {
            LibraryFolder::query()->updateOrCreate(
                ['slug' => $row['slug']],
                array_merge($row, ['is_active' => true])
            );
        }
    }
}
