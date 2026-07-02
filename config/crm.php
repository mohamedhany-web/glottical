<?php

return [
    /*
    | نسب العمولة الافتراضية (%) من مبلغ الطلب المعتمد
    */
    'commission_rates' => [
        'marketing' => (float) env('CRM_COMMISSION_MARKETING', 5),
        'sales' => (float) env('CRM_COMMISSION_SALES', 10),
        'team_leader' => (float) env('CRM_COMMISSION_TEAM_LEADER', 2),
    ],

    /*
    | أدوار CRM الداخلية
    */
    'roles' => [
        'super_admin' => 'super_admin',
        'team_leader' => 'team_leader',
        'marketing' => 'marketing',
        'sales' => 'sales',
        'finance' => 'finance',
    ],

    'employee_job_codes' => [
        'marketing' => 'crm_marketing',
        'team_leader' => 'crm_team_leader',
        'finance' => 'crm_finance',
        'sales' => 'sales',
    ],
];
