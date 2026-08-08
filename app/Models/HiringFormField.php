<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HiringFormField extends Model
{
    public const TYPE_SHORT_TEXT = 'short_text';

    public const TYPE_LONG_TEXT = 'long_text';

    public const TYPE_EMAIL = 'email';

    public const TYPE_PHONE = 'phone';

    public const TYPE_NUMBER = 'number';

    public const TYPE_SELECT = 'select';

    public const TYPE_RADIO = 'radio';

    public const TYPE_CHECKBOX = 'checkbox';

    public const TYPE_DATE = 'date';

    public const TYPE_URL = 'url';

    public const TYPE_FILE = 'file';

    public const TYPE_SECTION = 'section';

    protected $fillable = [
        'hiring_form_id',
        'type',
        'label',
        'help_text',
        'placeholder',
        'is_required',
        'options',
        'system_key',
        'sort_order',
        'is_active',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'options' => 'array',
            'settings' => 'array',
            'sort_order' => 'integer',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(HiringForm::class, 'hiring_form_id');
    }

    public static function typeLabels(): array
    {
        return [
            self::TYPE_SHORT_TEXT => 'نص قصير',
            self::TYPE_LONG_TEXT => 'نص طويل',
            self::TYPE_EMAIL => 'بريد إلكتروني',
            self::TYPE_PHONE => 'جوال',
            self::TYPE_NUMBER => 'رقم',
            self::TYPE_SELECT => 'قائمة منسدلة',
            self::TYPE_RADIO => 'اختيار واحد',
            self::TYPE_CHECKBOX => 'اختيارات متعددة',
            self::TYPE_DATE => 'تاريخ',
            self::TYPE_URL => 'رابط',
            self::TYPE_FILE => 'رفع ملف',
            self::TYPE_SECTION => 'عنوان قسم',
        ];
    }

    public static function systemKeys(): array
    {
        return [
            'full_name' => 'الاسم الكامل',
            'phone' => 'الجوال',
            'nationality' => 'الجنسية',
            'city' => 'المدينة / الدولة',
            'gender' => 'النوع',
            'headline' => 'عنوان الملف',
            'bio' => 'النبذة',
            'experience' => 'الخبرات',
            'education' => 'المؤهل',
            'years_experience' => 'سنوات الخبرة',
            'photo' => 'الصورة الشخصية',
            'id_document' => 'الهوية / الجواز',
            'certificate' => 'الشهادة',
            'intro_video' => 'فيديو تعريفي (ملف)',
            'intro_video_url' => 'رابط فيديو تعريفي',
        ];
    }

    public function typeLabel(): string
    {
        return self::typeLabels()[$this->type] ?? $this->type;
    }

    public function isSection(): bool
    {
        return $this->type === self::TYPE_SECTION;
    }

    public function isFile(): bool
    {
        return $this->type === self::TYPE_FILE;
    }

    public function acceptsMultiple(): bool
    {
        return $this->type === self::TYPE_CHECKBOX;
    }

    public function optionList(): array
    {
        $opts = $this->options ?? [];
        if (! is_array($opts)) {
            return [];
        }

        return array_values(array_filter(array_map(function ($o) {
            if (is_array($o)) {
                return trim((string) ($o['label'] ?? $o['value'] ?? ''));
            }

            return trim((string) $o);
        }, $opts)));
    }

    public function fileAccept(): string
    {
        $kind = $this->settings['file_kind'] ?? 'any';

        return match ($kind) {
            'image' => 'image/jpeg,image/png,image/webp,image/gif',
            'pdf' => 'application/pdf,.pdf',
            'video' => 'video/mp4,video/webm,video/quicktime',
            'image_pdf' => 'image/jpeg,image/png,image/webp,application/pdf,.pdf',
            default => 'image/*,application/pdf,video/mp4,video/webm,.pdf,.doc,.docx',
        };
    }
}
