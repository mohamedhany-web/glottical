<?php

return [
    'lesson_duration_minutes' => 50,

    'subjects' => [
        'quran' => ['en' => 'Quran', 'ar' => 'قرآن'],
        'arabic' => ['en' => 'Arabic', 'ar' => 'العربية'],
        'islamic_studies' => ['en' => 'Islamic Studies', 'ar' => 'دراسات إسلامية'],
        'tajweed' => ['en' => 'Tajweed', 'ar' => 'تجويد'],
        'aqeedah' => ['en' => 'Aqeedah', 'ar' => 'عقيدة'],
        'fiqh' => ['en' => 'Fiqh', 'ar' => 'فقه'],
    ],

    'age_groups' => [
        '4-6' => ['en' => '4–6', 'ar' => '4–6'],
        '7-9' => ['en' => '7–9', 'ar' => '7–9'],
        '10-12' => ['en' => '10–12', 'ar' => '10–12'],
        '13-15' => ['en' => '13–15', 'ar' => '13–15'],
        'teens' => ['en' => 'Teens', 'ar' => 'مراهقون'],
    ],

    'genders' => [
        'female' => ['en' => 'Female', 'ar' => 'معلمة'],
        'male' => ['en' => 'Male', 'ar' => 'معلم'],
    ],

    'languages' => [
        'english' => ['en' => 'English', 'ar' => 'إنجليزية'],
        'arabic' => ['en' => 'Arabic', 'ar' => 'عربية'],
        'bilingual' => ['en' => 'Bilingual', 'ar' => 'ثنائي اللغة'],
    ],

    'specializations' => [
        'quran_non_arabic' => ['en' => 'Quran for Non-Arabic Speakers', 'ar' => 'قرآن لغير الناطقين بالعربية'],
        'arabic_non_arabic' => ['en' => 'Arabic for Non-Arabic Speakers', 'ar' => 'عربية لغير الناطقين'],
        'children' => ['en' => 'Children', 'ar' => 'الأطفال'],
        'new_muslims' => ['en' => 'New Muslims', 'ar' => 'مسلمون جدد'],
    ],

    'availability' => [
        'morning' => ['en' => 'Morning', 'ar' => 'صباحًا'],
        'afternoon' => ['en' => 'Afternoon', 'ar' => 'ظهرًا'],
        'evening' => ['en' => 'Evening', 'ar' => 'مساءً'],
        'weekend' => ['en' => 'Weekend', 'ar' => 'نهاية الأسبوع'],
    ],

    'packages' => [
        1 => [
            'months' => 1,
            'lessons' => 4,
            'label_en' => 'Monthly',
            'label_ar' => 'شهري',
            'sub_en' => '4 Private Lessons / Month',
            'sub_ar' => '4 حصص خاصة / شهريًا',
        ],
        3 => [
            'months' => 3,
            'lessons' => 12,
            'label_en' => '3-Month Plan',
            'label_ar' => 'خطة 3 أشهر',
            'sub_en' => '12 Private Lessons',
            'sub_ar' => '12 حصة خاصة',
        ],
    ],
];
