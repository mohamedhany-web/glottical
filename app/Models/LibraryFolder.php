<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LibraryFolder extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'slug',
        'description_ar',
        'description_en',
        'icon',
        'color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public const COLORS = ['blue', 'pink', 'orange', 'purple', 'green'];

    protected static function booted(): void
    {
        static::saving(function (self $folder) {
            if (blank($folder->slug)) {
                $base = $folder->name_en ?: $folder->name_ar;
                $folder->slug = Str::slug($base) ?: ('folder-'.Str::random(6));
            }
        });
    }

    public function recordings(): HasMany
    {
        return $this->hasMany(LiveRecording::class, 'library_folder_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function displayName(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        if ($locale === 'en' && filled($this->name_en)) {
            return (string) $this->name_en;
        }

        return (string) $this->name_ar;
    }

    public function displayDescription(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();

        if ($locale === 'en' && filled($this->description_en)) {
            return (string) $this->description_en;
        }

        return $this->description_ar ? (string) $this->description_ar : null;
    }
}
