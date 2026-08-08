<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HiringForm extends Model
{
    protected $fillable = [
        'title',
        'description',
        'is_published',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'settings' => 'array',
        ];
    }

    public function fields(): HasMany
    {
        return $this->hasMany(HiringFormField::class)->orderBy('sort_order')->orderBy('id');
    }

    public function activeFields(): HasMany
    {
        return $this->fields()->where('is_active', true);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(TutorApplication::class);
    }

    public static function published(): ?self
    {
        return static::query()
            ->where('is_published', true)
            ->orderByDesc('id')
            ->first();
    }
}
