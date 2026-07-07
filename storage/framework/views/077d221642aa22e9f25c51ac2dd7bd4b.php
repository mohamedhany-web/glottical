<?php

    $user = auth()->user();

    $crmRole = $role ?? \App\Services\Crm\CrmAccessService::crmRole($user);

?>

<nav class="flex flex-wrap gap-2 rounded-2xl border bg-white p-3 text-sm">

    <a href="<?php echo e(route('employee.crm.dashboard')); ?>" class="px-3 py-1.5 rounded-lg font-bold <?php echo e(request()->routeIs('employee.crm.dashboard') ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700'); ?>">لوحة CRM</a>

    <?php if(\App\Services\Crm\CrmAccessService::canViewTeamPerformance($user) || \App\Services\Crm\CrmAccessService::canManageTeam($user)): ?>

        <a href="<?php echo e(route('employee.crm.team.index')); ?>" class="px-3 py-1.5 rounded-lg font-bold <?php echo e(request()->routeIs('employee.crm.team.*') ? 'bg-sky-600 text-white' : 'bg-slate-100 text-slate-700'); ?>">فريقي</a>

    <?php endif; ?>

    <a href="<?php echo e(route('employee.crm.leads.index')); ?>" class="px-3 py-1.5 rounded-lg font-bold <?php echo e(request()->routeIs('employee.crm.leads.*') && !request()->routeIs('employee.crm.leads.create') ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700'); ?>">العملاء المحتملون</a>

    <?php if(\App\Services\Crm\CrmAccessService::hasCrmPermission($user, 'crm_create_leads')): ?>

        <a href="<?php echo e(route('employee.crm.leads.create')); ?>" class="px-3 py-1.5 rounded-lg font-bold <?php echo e(request()->routeIs('employee.crm.leads.create') ? 'bg-teal-600 text-white' : 'bg-slate-100 text-slate-700'); ?>">إضافة عميل جديد</a>

    <?php endif; ?>

    <?php if(\App\Services\Crm\CrmAccessService::canViewSalesFinancialReports($user)): ?>

        <a href="<?php echo e(route('employee.crm.sales-financial')); ?>" class="px-3 py-1.5 rounded-lg font-bold <?php echo e(request()->routeIs('employee.crm.sales-financial') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700'); ?>">تقارير مبيعات</a>

    <?php endif; ?>

    <?php if(\App\Services\Crm\CrmAccessService::canViewAllOrders($user)): ?>

        <a href="<?php echo e(route('employee.crm.orders')); ?>" class="px-3 py-1.5 rounded-lg font-bold <?php echo e(request()->routeIs('employee.crm.orders*') ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-700'); ?>">كل الطلبات</a>

    <?php endif; ?>

    <?php if(\App\Services\Crm\CrmAccessService::canSubmitReports($user)): ?>

        <a href="<?php echo e(route('employee.crm.reports.index')); ?>" class="px-3 py-1.5 rounded-lg font-bold <?php echo e(request()->routeIs('employee.crm.reports.*') ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-700'); ?>">تقارير CRM</a>

    <?php endif; ?>

    <?php if(\App\Services\Crm\CrmAccessService::canUseMessages($user)): ?>

        <a href="<?php echo e(route('employee.crm.messages.index')); ?>" class="px-3 py-1.5 rounded-lg font-bold <?php echo e(request()->routeIs('employee.crm.messages.*') ? 'bg-cyan-600 text-white' : 'bg-slate-100 text-slate-700'); ?>">التواصل</a>

    <?php endif; ?>

    <a href="<?php echo e(route('employee.crm.commissions')); ?>" class="px-3 py-1.5 rounded-lg font-bold <?php echo e(request()->routeIs('employee.crm.commissions*') ? 'bg-violet-600 text-white' : 'bg-slate-100 text-slate-700'); ?>">عمولاتي</a>

    <?php if($crmRole === 'sales'): ?>

        <a href="<?php echo e(route('employee.sales.desk')); ?>" class="px-3 py-1.5 rounded-lg font-bold <?php echo e(request()->routeIs('employee.sales.*') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-700'); ?>">مكتب المبيعات</a>

    <?php endif; ?>

</nav>

<?php /**PATH C:\xampp\htdocs\glottical\resources\views/partials/crm-employee-nav.blade.php ENDPATH**/ ?>