<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * تسجيل أن المستخدم استخدم معاينته المجانية الوحيدة (فتح ملف واحد قبل الاشتراك).
 * كل مستخدم مسجل له صف واحد فقط: أول عنصر فتحه يُسجّل هنا.
 */
class CurriculumLibraryPreviewOpen extends Model
{
    protected $table = 'curriculum_library_preview_opens';

    protected $fillable = [
        'user_id',
        'curriculum_library_item_id',
        'opened_at',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(CurriculumLibraryItem::class, 'curriculum_library_item_id');
    }

    public static function hasUsedFreePreview(int $userId): bool
    {
        return static::where('user_id', $userId)->exists();
    }

    public static function recordFreePreviewUsed(int $userId, int $itemId): void
    {
        static::firstOrCreate(
            ['user_id' => $userId],
            [
                'curriculum_library_item_id' => $itemId,
                'opened_at' => now(),
            ]
        );
    }
}
