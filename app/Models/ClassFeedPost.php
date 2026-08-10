<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassFeedPost extends Model
{
    protected $fillable = [
        'tutoring_group_cohort_id',
        'user_id',
        'post_type',
        'body',
        'is_pinned',
        'is_hidden',
        'hidden_by',
        'hidden_at',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'is_hidden' => 'boolean',
            'hidden_at' => 'datetime',
        ];
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(TutoringGroupCohort::class, 'tutoring_group_cohort_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ClassFeedComment::class);
    }

    public function visibleComments(): HasMany
    {
        return $this->comments()->where('is_hidden', false)->orderBy('created_at');
    }

    public function typeLabel(): string
    {
        return match ($this->post_type) {
            'announcement' => 'إعلان',
            'question' => 'سؤال',
            default => $this->post_type,
        };
    }
}
