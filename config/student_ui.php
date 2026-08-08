<?php

/**
 * واجهة الطالب — إخفاء احتياطي بدون حذف بيانات.
 * غيّر أي قيمة إلى true لإعادة إظهار القسم في السايدبار/اللوحة.
 */
return [
    // أقسام غير مستخدمة حالياً
    'show_courses' => false,
    'show_exams' => false,
    'show_certificates' => false,
    'show_achievements' => false,
    'show_wallet' => false,
    'show_orders' => true,
    'show_referrals' => false,
    'show_consultations' => false,
    'show_legacy_calendar' => false,
    'show_course_progress' => false,
    'show_live_broadcast' => false,

    // أقسام نشطة
    'show_school' => true,
    'show_classes' => true,
    'show_private_lessons' => true,
    'show_assignments' => true,
    'show_libraries' => true,
    'show_notifications' => true,
    'show_profile' => true,
    'show_settings' => true,
    'show_entitlements' => true,

    // تذكير قبل الموعد (دقائق)
    'reminder_minutes' => 30,
];
