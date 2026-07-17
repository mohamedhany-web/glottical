<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SalesLead;
use App\Models\User;
use App\Services\Crm\CrmAccessService;
use App\Services\Crm\CrmLeadService;
use App\Services\Crm\CrmMarketingPerformanceService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

$pass = 0;
$fail = 0;
function check(bool $ok, string $label): void
{
    global $pass, $fail;
    echo ($ok ? 'OK  ' : 'FAIL ').$label."\n";
    $ok ? $pass++ : $fail++;
}

check(Schema::hasColumn('sales_leads', 'submitted_to_sales_at'), 'column submitted_to_sales_at');
check(Route::has('employee.crm.marketing.desk'), 'route marketing.desk');
check(Route::has('employee.crm.marketing-inbox.index'), 'route marketing-inbox');
check(Route::has('employee.crm.leads.submit-to-sales'), 'route submit-to-sales');

$marketer = User::where('email', 'like', '%marketing%')->where('is_employee', 1)->first()
    ?? User::whereHas('employeeJob', fn ($q) => $q->where('code', 'crm_marketing'))->first();
$sales = User::where('email', 'like', '%sales%')->where('is_employee', 1)->whereHas('employeeJob', fn ($q) => $q->where('code', 'sales'))->first()
    ?? User::whereHas('employeeJob', fn ($q) => $q->where('code', 'sales'))->first();

check((bool) $marketer, 'marketer user');
check((bool) $sales, 'sales user');

if ($marketer && $sales) {
    $lead = SalesLead::create([
        'name' => 'Affiliate Flow Test '.uniqid(),
        'source' => SalesLead::SOURCE_SOCIAL,
        'status' => SalesLead::STATUS_NEW,
        'created_by' => $marketer->id,
        'marketing_owner_id' => $marketer->id,
        'interested_advanced_course_id' => null,
    ]);

    check(CrmAccessService::canSubmitLeadToSales($marketer, $lead), 'marketer can submit');
    CrmLeadService::submitToSales($lead, $marketer, 'test handoff');
    $lead->refresh();
    check((bool) $lead->submitted_to_sales_at, 'submitted_at set');
    check(CrmAccessService::marketingInboxQuery()->where('id', $lead->id)->exists(), 'in marketing inbox');
    check(CrmAccessService::canClaimMarketingLead($sales, $lead), 'sales can claim');
    check(CrmAccessService::canViewLead($sales, $lead), 'sales can view inbox lead');

    CrmLeadService::claimFromMarketingInbox($lead, $sales);
    $lead->refresh();
    check((int) $lead->assigned_to === (int) $sales->id, 'claimed by sales');
    check($lead->status === SalesLead::STATUS_ASSIGNED, 'status assigned after claim');

    $summary = CrmMarketingPerformanceService::summaryFor($marketer);
    check($summary['total_leads'] >= 1, 'marketer summary has leads');
    check(isset($summary['commission_percent']), 'commission percent present');

    $lead->delete();
}

echo "\n{$pass} passed, {$fail} failed\n";
exit($fail > 0 ? 1 : 0);
