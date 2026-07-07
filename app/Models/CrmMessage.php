<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmMessage extends Model
{
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'crm_group_id',
        'sales_lead_id',
        'body',
        'attachment_path',
        'attachment_name',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(CrmGroup::class, 'crm_group_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(SalesLead::class, 'sales_lead_id');
    }

    public function isDirect(): bool
    {
        return $this->recipient_id !== null;
    }

    public function isGroupChannel(): bool
    {
        return $this->crm_group_id !== null && $this->recipient_id === null && $this->sales_lead_id === null;
    }

    public function isLeadThread(): bool
    {
        return $this->sales_lead_id !== null;
    }
}
