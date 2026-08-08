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
