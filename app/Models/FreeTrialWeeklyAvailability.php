<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FreeTrialWeeklyAvailability extends Model
{
    protected $table = 'free_trial_weekly_availability';

    protected $fillable = [
        'day_of_week', 'start_time', 'end_time', 'slot_duration_minutes', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'slot_duration_minutes' => 'integer',
        'day_of_week' => 'integer',
    ];
}
