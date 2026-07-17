<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\CrmGroup;
use App\Models\User;
use App\Services\Crm\CrmAccessService;

echo "=== ALL CRM GROUPS ===\n";
foreach (CrmGroup::with(['teamLeader:id,name,email', 'members.user:id,name'])->orderBy('id')->get() as $g) {
    $tl = $g->teamLeader;
    echo "#{$g->id} {$g->name} active=".((int) $g->is_active).' tl='.($tl ? "{$tl->id}:{$tl->email}" : 'NULL')."\n";
    if ($tl) {
        echo '  tl_role='.CrmAccessService::crmRole($tl).' job='.($tl->employeeJobCode() ?? 'null');
        echo ' custom='.($tl->usesCustomEmployeePermissions() ? '1' : '0');
        echo ' assign_perm='.(CrmAccessService::hasCrmPermission($tl, 'crm_assign_leads') ? '1' : '0');
        echo ' sales_count='.CrmAccessService::assignableSalesUsers($tl)->count()."\n";
    }
    foreach ($g->members as $m) {
        echo "  member {$m->user_id}:".($m->user?->name ?? '?')." role={$m->role} active=".((int) $m->is_active)."\n";
    }
}

echo "\n=== Users with job crm_team_leader but no group ===\n";
User::query()->where('is_employee', true)->whereHas('employeeJob', fn ($q) => $q->where('code', 'crm_team_leader'))->each(function (User $u) {
    $n = CrmAccessService::teamGroupsFor($u)->count();
    if ($n === 0) {
        echo "{$u->id} {$u->email} groups=0 assignable=".CrmAccessService::assignableSalesUsers($u)->count()."\n";
    }
});
