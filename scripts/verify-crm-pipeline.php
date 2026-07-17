<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SalesLead;
use App\Models\User;
use App\Services\Crm\CrmAccessService;
use App\Services\Crm\CrmLeadService;
use Illuminate\Support\Facades\Route;

$pass = 0;
$fail = 0;

function check(bool $ok, string $label): void
{
    global $pass, $fail;
    echo ($ok ? 'OK  ' : 'FAIL ').$label."\n";
    $ok ? $pass++ : $fail++;
}

// From contacted, can jump to payment_pending
$lead = new SalesLead(['status' => SalesLead::STATUS_CONTACTED]);
check($lead->canTransitionTo(SalesLead::STATUS_INTERESTED), 'contacted → interested');
check($lead->canTransitionTo(SalesLead::STATUS_PLACEMENT_TEST), 'contacted → placement_test');
check($lead->canTransitionTo(SalesLead::STATUS_OFFER_SENT), 'contacted → offer_sent');
check($lead->canTransitionTo(SalesLead::STATUS_PAYMENT_PENDING), 'contacted → payment_pending');
check($lead->canTransitionTo(SalesLead::STATUS_CLOSED_LOST), 'contacted → closed_lost');
check(! $lead->canTransitionTo(SalesLead::STATUS_NEW), 'contacted ↛ new');

$sales = User::where('email', 'like', '%sales%')->where('is_employee', 1)->first()
    ?? User::whereHas('employeeJob', fn ($q) => $q->where('code', 'sales'))->first();

check((bool) $sales, 'sales user exists');

if ($sales) {
    $dbLead = SalesLead::query()->where('assigned_to', $sales->id)->whereNotIn('status', ['closed_won', 'closed_lost'])->first();
    if (! $dbLead) {
        $dbLead = SalesLead::query()->create([
            'name' => 'Pipeline Test Lead',
            'source' => SalesLead::SOURCE_OTHER,
            'status' => SalesLead::STATUS_CONTACTED,
            'assigned_to' => $sales->id,
            'marketing_owner_id' => $sales->id,
            'created_by' => $sales->id,
        ]);
    } else {
        $dbLead->update(['status' => SalesLead::STATUS_CONTACTED]);
    }

    $selectable = CrmAccessService::selectableStatusesFor($sales, $dbLead->fresh());
    echo 'selectable: '.implode(', ', $selectable)."\n";
    check(in_array(SalesLead::STATUS_INTERESTED, $selectable, true), 'sales sees interested');
    check(in_array(SalesLead::STATUS_PLACEMENT_TEST, $selectable, true), 'sales sees placement_test');
    check(in_array(SalesLead::STATUS_OFFER_SENT, $selectable, true), 'sales sees offer_sent');
    check(in_array(SalesLead::STATUS_PAYMENT_PENDING, $selectable, true), 'sales sees payment_pending');
    check(in_array(SalesLead::STATUS_CLOSED_LOST, $selectable, true), 'sales sees closed_lost');
    check(! in_array(SalesLead::STATUS_PAYMENT_CONFIRMED, $selectable, true), 'sales cannot pick payment_confirmed');

    CrmLeadService::transitionStatus($dbLead->fresh(), SalesLead::STATUS_OFFER_SENT, $sales, 'jump test');
    check($dbLead->fresh()->status === SalesLead::STATUS_OFFER_SENT, 'sales can jump contacted→offer_sent');
}

$admin = User::where('role', 'super_admin')->first()
    ?? User::whereHas('roles')->get()->first(fn ($u) => $u->hasPermission('manage.leads'));

check((bool) $admin, 'admin user exists');
if ($admin) {
    $any = SalesLead::query()->latest()->first();
    if ($any) {
        $forced = CrmLeadService::forceStatus($any, SalesLead::STATUS_CONTACTED, $admin, 'admin force reopen/test');
        check($forced->status === SalesLead::STATUS_CONTACTED, 'admin force status works');
    }
}

check(Route::has('admin.crm.pipeline'), 'pipeline route registered');

$view = view('admin.crm.pipeline.index', [
    'columns' => [
        SalesLead::STATUS_CONTACTED => [
            'label' => 'تم التواصل',
            'count' => 0,
            'leads' => collect(),
        ],
    ],
    'statusLabels' => SalesLead::statusLabels(),
    'totalLeads' => 0,
    'openLeads' => 0,
]);
check($view->name() === 'admin.crm.pipeline.index', 'pipeline view exists');

echo "\n{$pass} passed, {$fail} failed\n";
exit($fail > 0 ? 1 : 0);
