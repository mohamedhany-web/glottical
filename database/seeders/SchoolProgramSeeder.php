<?php

namespace Database\Seeders;

use App\Models\SchoolSubject;
use App\Models\SchoolYear;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SchoolProgramSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('school_years') || ! Schema::hasTable('school_subjects')) {
            $this->command?->warn('School tables missing — skip SchoolProgramSeeder.');

            return;
        }

        $years = [
            ['level_number' => 1, 'name' => 'Islamic Foundations 1', 'tagline' => 'Building the Basics', 'slug' => 'islamic-foundations-1'],
            ['level_number' => 2, 'name' => 'Islamic Foundations 2', 'tagline' => 'Growing in Understanding', 'slug' => 'islamic-foundations-2'],
            ['level_number' => 3, 'name' => 'Islamic Foundations 3', 'tagline' => 'Strengthening Knowledge', 'slug' => 'islamic-foundations-3'],
            ['level_number' => 4, 'name' => 'Islamic Foundations 4', 'tagline' => 'Deepening Faith', 'slug' => 'islamic-foundations-4'],
            ['level_number' => 5, 'name' => 'Islamic Foundations 5', 'tagline' => 'Developing Character', 'slug' => 'islamic-foundations-5'],
            ['level_number' => 6, 'name' => 'Islamic Foundations 6', 'tagline' => 'Preparing for Life', 'slug' => 'islamic-foundations-6'],
        ];

        foreach ($years as $i => $year) {
            SchoolYear::query()->updateOrCreate(
                ['level_number' => $year['level_number']],
                [
                    'name' => $year['name'],
                    'slug' => $year['slug'],
                    'tagline' => $year['tagline'],
                    'description' => $year['tagline'],
                    'is_active' => true,
                    'sort_order' => $i + 1,
                ]
            );
        }

        $subjects = [
            ['name' => 'Quran Studies', 'slug' => 'quran-studies', 'icon' => 'book-quran', 'description' => 'Understanding, recitation & connection with the Quran'],
            ['name' => 'Aqeedah', 'slug' => 'aqeedah', 'icon' => 'moon', 'description' => 'Building a clear and strong Islamic foundation'],
            ['name' => 'Fiqh', 'slug' => 'fiqh', 'icon' => 'mosque', 'description' => 'Learning how to practice Islam in everyday life'],
            ['name' => 'Seerah', 'slug' => 'seerah', 'icon' => 'star-and-crescent', 'description' => 'Discovering the life and example of Prophet Muhammad ﷺ'],
            ['name' => 'Tafsir', 'slug' => 'tafsir', 'icon' => 'book-open', 'description' => 'Understanding the meanings and lessons of the Quran'],
            ['name' => 'Islamic Character', 'slug' => 'islamic-character', 'icon' => 'heart', 'description' => 'Building manners, values and Muslim identity'],
        ];

        foreach ($subjects as $i => $subject) {
            SchoolSubject::query()->updateOrCreate(
                ['slug' => $subject['slug']],
                [
                    'name' => $subject['name'],
                    'description' => $subject['description'],
                    'icon' => $subject['icon'],
                    'is_active' => true,
                    'sort_order' => $i + 1,
                ]
            );
        }
    }
}
