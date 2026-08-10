<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolMission extends Model
{
    protected $fillable = [
        'tutoring_group_cohort_id',
        'code',
        'title',
        'description',
        'cadence',
        'mission_type',
        'target_count',
        'xp_reward',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'target_count' => 'integer',
            'xp_reward' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(TutoringGroupCohort::class, 'tutoring_group_cohort_id');
    }

    public function progressRows(): HasMany
    {
        return $this->hasMany(SchoolMissionProgress::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
