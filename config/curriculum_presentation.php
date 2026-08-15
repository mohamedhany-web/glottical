<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Curriculum presentation slide derivatives
    |--------------------------------------------------------------------------
    |
    | Converts PPT/PPTX materials into slide images for an in-app viewer.
    | Original objects are NEVER modified — only additive derived keys are written.
    |
    */

    'enabled' => filter_var(env('CURRICULUM_PRESENTATION_CONVERSION_ENABLED', true), FILTER_VALIDATE_BOOL),

    'queue' => env('CURRICULUM_PRESENTATION_QUEUE', 'default'),

    'soffice_path' => env('CURRICULUM_PRESENTATION_SOFFICE_PATH', 'soffice'),

    'pdftoppm_path' => env('CURRICULUM_PRESENTATION_PDFTOPPM_PATH', 'pdftoppm'),

    'timeout_seconds' => (int) env('CURRICULUM_PRESENTATION_TIMEOUT_SECONDS', 600),

    'image_format' => strtolower((string) env('CURRICULUM_PRESENTATION_IMAGE_FORMAT', 'png')),

    'image_quality' => (int) env('CURRICULUM_PRESENTATION_IMAGE_QUALITY', 85),

    'dpi' => (int) env('CURRICULUM_PRESENTATION_DPI', 144),

    'thumb_dpi' => (int) env('CURRICULUM_PRESENTATION_THUMB_DPI', 72),

    'temp_disk' => env('CURRICULUM_PRESENTATION_TEMP_DISK', 'local'),

    'temp_prefix' => env('CURRICULUM_PRESENTATION_TEMP_PREFIX', 'curriculum-presentation-tmp'),

    'job_tries' => (int) env('CURRICULUM_PRESENTATION_JOB_TRIES', 3),

    'job_backoff_seconds' => array_values(array_filter(array_map(
        'intval',
        explode(',', (string) env('CURRICULUM_PRESENTATION_JOB_BACKOFF', '30,90,180'))
    ))),

    /*
    |--------------------------------------------------------------------------
    | Optional animation-preserving companion video (MP4/WebM sidecar)
    |--------------------------------------------------------------------------
    |
    | Admin-exported PowerPoint video attached alongside the original PPT/PPTX.
    | Never replaces CurriculumLibraryMaterial.path — storage prefix is separate.
    |
    */

    'animation_video_max_bytes' => (int) env(
        'CURRICULUM_PRESENTATION_ANIMATION_VIDEO_MAX_BYTES',
        500 * 1024 * 1024
    ),

    'animation_video_allowed_extensions' => ['mp4', 'webm'],

    'animation_video_allowed_mimes' => [
        'video/mp4',
        'video/webm',
        'application/mp4',
        'application/octet-stream',
    ],

    'animation_video_temp_url_minutes' => (int) env(
        'CURRICULUM_PRESENTATION_ANIMATION_VIDEO_TEMP_URL_MINUTES',
        20
    ),

];
