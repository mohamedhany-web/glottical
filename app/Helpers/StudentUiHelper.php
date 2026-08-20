<?php

if (! function_exists('student_ui')) {
    /**
     * هل يظهر قسم في واجهة الطالب؟ (إخفاء احتياطي من config/student_ui.php)
     */
    function student_ui(string $key, bool $default = false): bool
    {
        return (bool) config('student_ui.'.$key, $default);
    }
}

if (! function_exists('instructor_ui')) {
    /**
     * هل يظهر قسم في واجهة المعلم؟ (إخفاء احتياطي من config/instructor_ui.php)
     */
    function instructor_ui(string $key, bool $default = false): bool
    {
        return (bool) config('instructor_ui.'.$key, $default);
    }
}
