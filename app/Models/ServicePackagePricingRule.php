<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ServicePackagePricingRule extends Model
{
    protected $fillable = [
        'name',
        'scope',
        'price_per_session',
        'min_sessions',
        'max_sessions',
        'session_step',
        'session_minutes',
        'duration_days',
        'discount_tiers',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price_per_session' => 'decimal:2',
            'min_sessions' => 'integer',
            'max_sessions' => 'integer',
            'session_step' => 'integer',
            'session_minutes' => 'integer',
            'duration_days' => 'integer',
            'discount_tiers' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeLabel(): string
    {
        return ServicePackage::scopes()[$this->scope] ?? $this->scope;
    }
}
