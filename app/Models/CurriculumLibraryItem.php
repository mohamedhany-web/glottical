<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CurriculumLibraryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'content',
        'grade_level',
        'subject',
        'language',
        'item_type',
        'meta',
        'order',
        'is_active',
        'is_free_preview',
    ];

    protected $casts = [
        'meta' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
        'is_free_preview' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title ?? '') ?: 'item';
            }
            $model->slug = static::ensureUniqueSlug((string) $model->slug, null);
        });

        static::updating(function ($model) {
            if ($model->isDirty('slug')) {
                $model->slug = static::ensureUniqueSlug((string) $model->slug, $model->getKey());
            }
        });
    }

    /**
     * يضمن عدم تكرار slug عبر إلحاق -2، -3، … مع احترام حد الطول في قاعدة البيانات.
     */
    public static function ensureUniqueSlug(string $base, ?int $exceptId = null): string
    {
        $base = trim($base);
        if ($base === '') {
            $base = 'item';
        }
        $base = Str::limit($base, 200, '');

        $slug = $base;
        $n = 2;
        while (static::query()
            ->when($exceptId !== null, fn ($q) => $q->where('id', '!=', $exceptId))
            ->where('slug', $slug)
            ->exists()) {
            $suffix = '-'.$n;
            $slug = Str::limit($base, 255 - strlen($suffix), '').$suffix;
            $n++;
        }

        return $slug;
    }

    public function category()
    {
        return $this->belongsTo(CurriculumLibraryCategory::class, 'category_id');
    }

    /** هل يحق للطالب الوصول لهذا العنصر بحسب تصنيف «قسم خاص»؟ */
    public function isAccessibleByStudent(?User $user): bool
    {
        if (!$this->category_id) {
            return true;
        }

        $category = $this->relationLoaded('category') ? $this->category : $this->category()->first();
        if (!$category || !$category->is_restricted) {
            return true;
        }

        return $user && $category->restrictedUsers()->where('users.id', $user->id)->exists();
    }

    public function files()
    {
        return $this->hasMany(CurriculumLibraryItemFile::class, 'curriculum_library_item_id')->orderBy('order')->orderBy('id');
    }

    public function sections()
    {
        return $this->hasMany(CurriculumLibrarySection::class, 'curriculum_library_item_id')->orderBy('order')->orderBy('id');
    }

    public function scopeByLanguage($query, ?string $lang)
    {
        if ($lang && in_array($lang, ['ar', 'en', 'fr'], true)) {
            return $query->where('language', $lang);
        }
        return $query;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('title');
    }

    public function scopeInCategory($query, $categoryId)
    {
        if ($categoryId) {
            return $query->where('category_id', $categoryId);
        }
        return $query;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
