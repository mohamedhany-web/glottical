<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FreeTrialBooking extends Model
{
    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'name', 'email', 'phone', 'goal', 'user_id',
        'starts_at', 'ends_at', 'duration_minutes', 'status', 'notes',
        'recommended_school_year_id', 'admin_notes',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recommendedSchoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class, 'recommended_school_year_id');
    }
}
