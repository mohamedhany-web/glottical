<?php

namespace App\Services\Crm;

use App\Models\CrmAuditLog;
use App\Models\SalesLead;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class CrmAuditService
{
    public static function log(
        string $action,
        ?SalesLead $lead = null,
        ?User $user = null,
        ?array $old = null,
        ?array $new = null
    ): CrmAuditLog {
        return CrmAuditLog::create([
            'user_id' => $user?->id ?? auth()->id(),
            'sales_lead_id' => $lead?->id,
            'action' => $action,
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => Request::ip(),
            'created_at' => now(),
        ]);
    }
}
