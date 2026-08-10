<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentXpLedger extends Model
{
    protected $table = 'student_xp_ledger';

    protected $fillable = [
        'user_id',
        'amount',
        'balance_after',
        'reason',
        'source_type',
        'source_id',
        'tutoring_group_cohort_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'balance_after' => 'integer',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(TutoringGroupCohort::class, 'tutoring_group_cohort_id');
    }
}
