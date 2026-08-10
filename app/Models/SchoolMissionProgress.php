<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolMissionProgress extends Model
{
    protected $table = 'school_mission_progress';

    protected $fillable = [
        'school_mission_id',
        'user_id',
        'period_key',
        'progress_count',
        'status',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'progress_count' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function mission(): BelongsTo
    {
        return $this->belongsTo(SchoolMission::class, 'school_mission_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
