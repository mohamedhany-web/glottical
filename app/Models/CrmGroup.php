<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmGroup extends Model
{
    protected $fillable = [
        'name',
        'team_leader_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function teamLeader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'team_leader_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(CrmGroupMember::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(SalesLead::class, 'crm_group_id');
    }

    public function activeMembers(): HasMany
    {
        return $this->members()->where('is_active', true);
    }
}
