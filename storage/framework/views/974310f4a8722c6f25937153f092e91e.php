<nav class="flex flex-wrap gap-2 rounded-2xl border border-line bg-surface p-3 shadow-soft">
    <?php
        $crmLinks = [
            ['route' => 'admin.crm.dashboard', 'match' => 'admin.crm.dashboard', 'label' => 'لوحة CRM', 'icon' => 'fa-chart-pie'],
            ['route' => 'admin.crm.pipeline', 'match' => 'admin.crm.pipeline', 'label' => 'Pipeline', 'icon' => 'fa-columns'],
            ['route' => 'admin.crm.leads.index', 'match' => 'admin.crm.leads.*', 'label' => 'العملاء المحتملون', 'icon' => 'fa-user-plus'],
            ['route' => 'admin.crm.commissions.index', 'match' => 'admin.crm.commissions.*', 'label' => 'العمولات', 'icon' => 'fa-coins'],
            ['route' => 'admin.crm.audit.index', 'match' => 'admin.crm.audit.*', 'label' => 'سجل المتابعة', 'icon' => 'fa-clipboard-list'],
            ['route' => 'admin.crm.groups.index', 'match' => 'admin.crm.groups.*', 'label' => 'مجموعات الفريق', 'icon' => 'fa-people-group'],
            ['route' => 'admin.crm.reports.index', 'match' => 'admin.crm.reports.*', 'label' => 'تقارير CRM', 'icon' => 'fa-file-alt'],
            ['route' => 'admin.crm.sales-permissions.index', 'match' => 'admin.crm.sales-permissions.*', 'label' => 'صلاحيات المبيعات', 'icon' => 'fa-user-shield'],
            ['route' => 'admin.employee-jobs.index', 'match' => 'admin.employee-jobs.*', 'label' => 'صلاحيات الوظائف', 'icon' => 'fa-briefcase'],
        ];
    ?>
    <?php $__currentLoopData = $crmLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(Route::has($link['route'])): ?>
            <?php $active = request()->routeIs($link['match']); ?>
            <a href="<?php echo e(route($link['route'])); ?>"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl px-3 text-sm font-medium transition <?php echo e($active ? 'bg-accent text-white' : 'border border-line bg-surface text-ink-soft hover:border-accent/30 hover:text-accent'); ?>">
                <i class="fas <?php echo e($link['icon']); ?> text-xs"></i>
                <?php echo e($link['label']); ?>

            </a>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</nav>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\partials\crm-admin-nav.blade.php ENDPATH**/ ?>