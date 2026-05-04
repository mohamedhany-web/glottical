<?php
    $user = auth()->user();
    $user->loadMissing('employeeJob');
    $sidebarSections = \App\Support\EmployeeSidebar::sectionsFor($user);
    $firstSidebarLink = \Illuminate\Support\Arr::get($sidebarSections, '0.links.0');
    $employeeHomeUrl = $user->employeeCan('dashboard')
        ? route('employee.dashboard')
        : (! empty($firstSidebarLink['route'] ?? null)
            ? route($firstSidebarLink['route'])
            : url('/'));
?>
<div class="flex flex-col h-full bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 text-white dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
    <div class="flex items-center justify-center min-h-16 px-4 py-3 border-b border-slate-700/50 dark:border-slate-600/50">
        <a href="<?php echo e($employeeHomeUrl); ?>" class="flex items-center gap-2">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-briefcase text-white text-lg"></i>
            </div>
            <div class="text-right min-w-0">
                <span class="text-lg font-bold text-white block leading-tight">Muallimx</span>
                <?php if($user->employeeJob): ?>
                    <span class="text-[11px] text-slate-400 font-semibold truncate block max-w-[9rem]"><?php echo e($user->employeeJob->name); ?></span>
                <?php endif; ?>
            </div>
        </a>
    </div>

    <nav class="flex-1 overflow-y-auto overflow-x-hidden px-3 py-4 space-y-5 employee-sidebar-nav">
        <?php $__empty_1 = true; $__currentLoopData = $sidebarSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="space-y-1">
                <?php if(!empty($section['title'])): ?>
                    <p class="px-4 text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-2"><?php echo e($section['title']); ?></p>
                <?php endif; ?>
                <?php $__currentLoopData = $section['links']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $isActive = \App\Support\EmployeeSidebar::linkIsActive($link);
                        $baseNav = 'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 w-full';
                        $inactiveNav = 'text-slate-300 hover:bg-slate-700/50 hover:text-white dark:text-slate-400';
                        $activeClass = $link['active_class'] ?? 'bg-blue-600 shadow-lg';
                    ?>
                    <?php
                        $linkHref = isset($link['route_params'])
                            ? route($link['route'], $link['route_params'])
                            : route($link['route']);
                    ?>
                    <a href="<?php echo e($linkHref); ?>"
                       class="<?php echo e($baseNav); ?> <?php echo e($isActive ? $activeClass.' text-white' : $inactiveNav); ?>"
                       @click="if (window.innerWidth < 1024) { $dispatch('close-sidebar'); }">
                        <i class="<?php echo e($link['icon']); ?> text-base w-5 text-center shrink-0"></i>
                        <span class="truncate"><?php echo e($link['label']); ?></span>
                        <?php if(($link['key'] ?? '') === 'notifications'): ?>
                            <?php
                                try {
                                    $unreadCount = $user->notifications()->whereNull('read_at')->count();
                                } catch (\Exception $e) {
                                    $unreadCount = 0;
                                }
                            ?>
                            <?php if($unreadCount > 0): ?>
                                <span class="mr-auto bg-red-500 text-white text-xs font-bold rounded-full px-2 py-0.5 min-w-[20px] text-center shrink-0"><?php echo e($unreadCount > 99 ? '99+' : $unreadCount); ?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="px-4 py-6 text-center text-slate-400 text-sm">
                لا توجد عناصر قائمة مفعّلة لوظيفتك. راجع الإدارة.
            </div>
        <?php endif; ?>
    </nav>

    <div class="border-t border-slate-700/50 dark:border-slate-600/50 p-4">
        <div class="flex items-center gap-3 mb-3">
            <?php $profileImage = $user->profile_image_url ?? null; ?>
            <?php if($profileImage): ?>
                <img src="<?php echo e($profileImage); ?>" alt="<?php echo e($user->name); ?>" class="w-10 h-10 rounded-full object-cover border-2 border-blue-400 flex-shrink-0">
            <?php else: ?>
                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                    <?php echo e(mb_substr($user->name, 0, 1, 'UTF-8')); ?>

                </div>
            <?php endif; ?>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white truncate"><?php echo e($user->name); ?></p>
                <p class="text-xs text-slate-400 truncate"><?php echo e(optional($user->employeeJob)->name ?? 'موظف'); ?></p>
            </div>
        </div>
        <form method="POST" action="<?php echo e(route('logout')); ?>" class="w-full">
            <?php echo csrf_field(); ?>
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-slate-700/50 hover:bg-slate-700 dark:bg-slate-600/50 dark:hover:bg-slate-600 text-white text-sm font-semibold rounded-xl transition-colors">
                <i class="fas fa-sign-out-alt"></i>
                <span>تسجيل الخروج</span>
            </button>
        </form>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\layouts\employee-sidebar.blade.php ENDPATH**/ ?>