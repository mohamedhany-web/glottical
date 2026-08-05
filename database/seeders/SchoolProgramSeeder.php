<?php

namespace Database\Seeders;

use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SchoolProgramSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('academic_years') || ! Schema::hasTable('academic_subjects')) {
            $this->command?->warn('School/academic tables missing — skip SchoolProgramSeeder.');

            return;
        }

        $years = [
            ['level_number' => 1, 'name' => 'Islamic Foundations 1', 'tagline' => 'Building the Basics', 'slug' => 'islamic-foundations-1', 'code' => 'SCH-L1'],
            ['level_number' => 2, 'name' => 'Islamic Foundations 2', 'tagline' => 'Growing in Understanding', 'slug' => 'islamic-foundations-2', 'code' => 'SCH-L2'],
            ['level_number' => 3, 'name' => 'Islamic Foundations 3', 'tagline' => 'Strengthening Knowledge', 'slug' => 'islamic-foundations-3', 'code' => 'SCH-L3'],
            ['level_number' => 4, 'name' => 'Islamic Foundations 4', 'tagline' => 'Deepening Faith', 'slug' => 'islamic-foundations-4', 'code' => 'SCH-L4'],
            ['level_number' => 5, 'name' => 'Islamic Foundations 5', 'tagline' => 'Developing Character', 'slug' => 'islamic-foundations-5', 'code' => 'SCH-L5'],
            ['level_number' => 6, 'name' => 'Islamic Foundations 6', 'tagline' => 'Preparing for Life', 'slug' => 'islamic-foundations-6', 'code' => 'SCH-L6'],
        ];

        foreach ($years as $i => $year) {
            AcademicYear::query()->updateOrCreate(
                ['code' => $year['code']],
                [
                    'name' => $year['name'],
                    'slug' => $year['slug'],
                    'tagline' => $year['tagline'],
                    'level_number' => $year['level_number'],
                    'description' => $year['tagline'],
                    'is_active' => true,
                    'order' => $i + 1,
                    'icon' => 'fas fa-school',
                    'color' => '#0B3D91',
                    'price' => 0,
                ]
            );
        }

        $subjects = [
            ['name' => 'Quran Studies', 'slug' => 'quran-studies', 'code' => 'SCH-QURAN', 'icon' => 'fas fa-book-quran', 'description' => 'Understanding, recitation & connection with the Quran'],
            ['name' => 'Aqeedah', 'slug' => 'aqeedah', 'code' => 'SCH-AQEEDAH', 'icon' => 'fas fa-moon', 'description' => 'Building a clear and strong Islamic foundation'],
            ['name' => 'Fiqh', 'slug' => 'fiqh', 'code' => 'SCH-FIQH', 'icon' => 'fas fa-mosque', 'description' => 'Learning how to practice Islam in everyday life'],
            ['name' => 'Seerah', 'slug' => 'seerah', 'code' => 'SCH-SEERAH', 'icon' => 'fas fa-star-and-crescent', 'description' => 'Discovering the life and example of Prophet Muhammad ﷺ'],
            ['name' => 'Tafsir', 'slug' => 'tafsir', 'code' => 'SCH-TAFSIR', 'icon' => 'fas fa-book-open', 'description' => 'Understanding the meanings and lessons of the Quran'],
            ['name' => 'Islamic Character', 'slug' => 'islamic-character', 'code' => 'SCH-CHAR', 'icon' => 'fas fa-heart', 'description' => 'Building manners, values and Muslim identity'],
        ];

        foreach ($subjects as $i => $subject) {
            AcademicSubject::query()->updateOrCreate(
                ['slug' => $subject['slug']],
                [
                    'academic_year_id' => null,
                    'name' => $subject['name'],
                    'code' => $subject['code'],
                    'description' => $subject['description'],
                    'icon' => $subject['icon'],
                    'color' => '#0B3D91',
                    'is_active' => true,
                    'order' => $i + 1,
                ]
            );
        }
    }
}
