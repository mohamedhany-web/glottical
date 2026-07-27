<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TutoringGroup extends Model
{
    public const TYPE_INDIVIDUAL = 'individual';

    public const TYPE_COLLECTIVE = 'collective';

    protected $fillable = [
        'type',
        'title',
        'slug',
        'description',
        'image_path',
        'instructor_id',
        'price',
        'currency',
        'capacity',
        'duration_minutes',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'capacity' => 'integer',
            'duration_minutes' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(TutoringGroupBooking::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeIndividual(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_INDIVIDUAL);
    }

    public function scopeCollective(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_COLLECTIVE);
    }

    public function isIndividual(): bool
    {
        return $this->type === self::TYPE_INDIVIDUAL;
    }

    public function isCollective(): bool
    {
        return $this->type === self::TYPE_COLLECTIVE;
    }

    public function typeLabel(): string
    {
        return $this->isIndividual() ? 'مجموعة فردية' : 'مجموعة جماعية';
    }

    public function imageUrl(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        if (Str::startsWith($this->image_path, ['http://', 'https://'])) {
            return $this->image_path;
        }

        return Storage::disk('public')->url($this->image_path);
    }

    public function formattedPrice(): string
    {
        if ($this->price === null) {
            return 'حسب الاتفاق';
        }

        return number_format((float) $this->price, 0).' '.($this->currency ?: 'EGP');
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'group-'.Str::random(6);
        $slug = $base;
        $i = 1;

        while (
            static::query()
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
