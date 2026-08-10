<?php

return [
    'xp' => [
        'attendance_present' => 50,
        'attendance_late' => 35,
        'assignment_submit' => 80,
        'exam_complete' => 100,
        'feed_post' => 15,
        'feed_comment' => 5,
    ],

    // Level N requires (N-1) * per_level XP cumulative threshold via floor(xp / per_level) + 1
    'xp_per_level' => 250,

    'feed' => [
        'max_body' => 1000,
        'student_can_announce' => false,
    ],
];
