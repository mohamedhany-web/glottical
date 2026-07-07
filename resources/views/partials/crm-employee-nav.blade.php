@php

    $user = auth()->user();

    $crmRole = $role ?? \App\Services\Crm\CrmAccessService::crmRole($user);

@endphp

<nav class="flex flex-wrap gap-2 rounded-2xl border bg-white p-3 text-sm">

    <a href="{{ route('employee.crm.dashboard') }}" class="px-3 py-1.5 rounded-lg font-bold {{ request()->routeIs('employee.crm.dashboard') ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700' }}">لوحة CRM</a>

    @if(\App\Services\Crm\CrmAccessService::canViewTeamPerformance($user) || \App\Services\Crm\CrmAccessService::canManageTeam($user))

        <a href="{{ route('employee.crm.team.index') }}" class="px-3 py-1.5 rounded-lg font-bold {{ request()->routeIs('employee.crm.team.*') ? 'bg-sky-600 text-white' : 'bg-slate-100 text-slate-700' }}">فريقي</a>

    @endif

    <a href="{{ route('employee.crm.leads.index') }}" class="px-3 py-1.5 rounded-lg font-bold {{ request()->routeIs('employee.crm.leads.*') && !request()->routeIs('employee.crm.leads.create') ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700' }}">العملاء المحتملون</a>

    @if(\App\Services\Crm\CrmAccessService::hasCrmPermission($user, 'crm_create_leads'))

        <a href="{{ route('employee.crm.leads.create') }}" class="px-3 py-1.5 rounded-lg font-bold {{ request()->routeIs('employee.crm.leads.create') ? 'bg-teal-600 text-white' : 'bg-slate-100 text-slate-700' }}">إضافة عميل جديد</a>

    @endif

    @if(\App\Services\Crm\CrmAccessService::canViewSalesFinancialReports($user))

        <a href="{{ route('employee.crm.sales-financial') }}" class="px-3 py-1.5 rounded-lg font-bold {{ request()->routeIs('employee.crm.sales-financial') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700' }}">تقارير مبيعات</a>

    @endif

    @if(\App\Services\Crm\CrmAccessService::canViewAllOrders($user))

        <a href="{{ route('employee.crm.orders') }}" class="px-3 py-1.5 rounded-lg font-bold {{ request()->routeIs('employee.crm.orders*') ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-700' }}">كل الطلبات</a>

    @endif

    @if(\App\Services\Crm\CrmAccessService::canSubmitReports($user))

        <a href="{{ route('employee.crm.reports.index') }}" class="px-3 py-1.5 rounded-lg font-bold {{ request()->routeIs('employee.crm.reports.*') ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-700' }}">تقارير CRM</a>

    @endif

    @if(\App\Services\Crm\CrmAccessService::canUseMessages($user))

        <a href="{{ route('employee.crm.messages.index') }}" class="px-3 py-1.5 rounded-lg font-bold {{ request()->routeIs('employee.crm.messages.*') ? 'bg-cyan-600 text-white' : 'bg-slate-100 text-slate-700' }}">التواصل</a>

    @endif

    <a href="{{ route('employee.crm.commissions') }}" class="px-3 py-1.5 rounded-lg font-bold {{ request()->routeIs('employee.crm.commissions*') ? 'bg-violet-600 text-white' : 'bg-slate-100 text-slate-700' }}">عمولاتي</a>

    @if($crmRole === 'sales')

        <a href="{{ route('employee.sales.desk') }}" class="px-3 py-1.5 rounded-lg font-bold {{ request()->routeIs('employee.sales.*') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700' }}">مكتب المبيعات</a>

    @endif

</nav>

