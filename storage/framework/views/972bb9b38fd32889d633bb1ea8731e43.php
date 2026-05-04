

<?php $__env->startSection('title', __('admin.hiring_academies')); ?>
<?php $__env->startSection('header', __('admin.hiring_academies')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8">
    <?php if(session('success')): ?>
        <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 px-5 py-4 text-sm font-medium"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-2xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 px-5 py-4 text-sm font-medium"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-900 via-slate-900 to-slate-950 text-white p-8 md:p-10 shadow-xl">
        <div class="absolute top-0 left-0 w-72 h-72 bg-indigo-500/20 rounded-full blur-3xl -translate-y-1/2 -translate-x-1/2"></div>
        <div class="absolute bottom-0 right-0 w-64 h-64 bg-cyan-500/15 rounded-full blur-3xl translate-y-1/2 translate-x-1/2"></div>
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <p class="text-indigo-200 text-xs font-bold uppercase tracking-widest mb-2"><?php echo e(__('admin.hiring_academies_tagline')); ?></p>
                <h1 class="text-2xl md:text-3xl font-black font-heading mb-2"><?php echo e(__('admin.hiring_academies_hero_title')); ?></h1>
                <p class="text-slate-300 text-sm max-w-xl leading-relaxed"><?php echo e(__('admin.hiring_academies_hero_desc')); ?></p>
            </div>
            <a href="<?php echo e(route('admin.hiring-academies.create')); ?>" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl bg-white text-indigo-900 font-bold text-sm shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all shrink-0">
                <i class="fas fa-plus"></i>
                <?php echo e(__('admin.hiring_academy_add')); ?>

            </a>
        </div>
        <div class="relative z-10 grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
            <div class="rounded-2xl bg-white/10 backdrop-blur border border-white/10 p-4">
                <p class="text-indigo-200 text-xs font-semibold"><?php echo e(__('admin.hiring_stats_academies')); ?></p>
                <p class="text-2xl font-black mt-1"><?php echo e(number_format($stats['total'])); ?></p>
            </div>
            <div class="rounded-2xl bg-white/10 backdrop-blur border border-white/10 p-4">
                <p class="text-indigo-200 text-xs font-semibold"><?php echo e(__('admin.hiring_stats_active')); ?></p>
                <p class="text-2xl font-black mt-1 text-emerald-300"><?php echo e(number_format($stats['active'])); ?></p>
            </div>
            <div class="rounded-2xl bg-white/10 backdrop-blur border border-white/10 p-4">
                <p class="text-indigo-200 text-xs font-semibold"><?php echo e(__('admin.hiring_stats_leads')); ?></p>
                <p class="text-2xl font-black mt-1 text-amber-300"><?php echo e(number_format($stats['lead'])); ?></p>
            </div>
            <div class="rounded-2xl bg-white/10 backdrop-blur border border-white/10 p-4">
                <p class="text-indigo-200 text-xs font-semibold"><?php echo e(__('admin.hiring_stats_opportunities')); ?></p>
                <p class="text-2xl font-black mt-1"><?php echo e(number_format($stats['opportunities'])); ?></p>
            </div>
        </div>
    </div>

    <form method="get" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1"><?php echo e(__('admin.search')); ?></label>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="<?php echo e(__('admin.hiring_search_placeholder')); ?>" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
        </div>
        <div class="w-40">
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1"><?php echo e(__('admin.status')); ?></label>
            <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                <option value=""><?php echo e(__('admin.all')); ?></option>
                <?php $__currentLoopData = \App\Models\HiringAcademy::statusLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($k); ?>" <?php if(request('status') === $k): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-800 dark:bg-slate-600 text-white text-sm font-bold"><?php echo e(__('admin.filter')); ?></button>
    </form>

    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900/50 text-xs uppercase text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-right"><?php echo e(__('admin.hiring_academy_name')); ?></th>
                        <th class="px-4 py-3 text-right"><?php echo e(__('admin.contact')); ?></th>
                        <th class="px-4 py-3 text-right"><?php echo e(__('admin.city')); ?></th>
                        <th class="px-4 py-3 text-right"><?php echo e(__('admin.status')); ?></th>
                        <th class="px-4 py-3 text-right"><?php echo e(__('admin.opportunities')); ?></th>
                        <th class="px-4 py-3 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    <?php $__empty_1 = true; $__currentLoopData = $academies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-900/30">
                            <td class="px-4 py-3">
                                <p class="font-bold text-slate-900 dark:text-white"><?php echo e($a->name); ?></p>
                                <?php if($a->legal_name): ?><p class="text-xs text-slate-500"><?php echo e($a->legal_name); ?></p><?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                <?php if($a->contact_name): ?><span class="block"><?php echo e($a->contact_name); ?></span><?php endif; ?>
                                <?php if($a->contact_email): ?><span class="text-xs"><?php echo e($a->contact_email); ?></span><?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-slate-600"><?php echo e($a->city ?? '—'); ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold
                                    <?php if($a->status === 'active'): ?> bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200
                                    <?php elseif($a->status === 'lead'): ?> bg-amber-100 text-amber-900 dark:bg-amber-900/30 dark:text-amber-100
                                    <?php else: ?> bg-slate-200 text-slate-700 dark:bg-slate-600 dark:text-slate-100 <?php endif; ?>">
                                    <?php echo e($a->statusLabel()); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3 font-semibold text-indigo-600 dark:text-indigo-400"><?php echo e(number_format($a->opportunities_count)); ?></td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="<?php echo e(route('admin.hiring-academies.show', $a)); ?>" class="text-sky-600 dark:text-sky-400 font-semibold hover:underline"><?php echo e(__('admin.view')); ?></a>
                                    <a href="<?php echo e(route('admin.hiring-academies.edit', $a)); ?>" class="text-slate-600 dark:text-slate-300 font-semibold hover:underline"><?php echo e(__('admin.edit')); ?></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="px-4 py-16 text-center text-slate-500"><?php echo e(__('admin.hiring_no_academies')); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-700"><?php echo e($academies->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\hiring-academies\index.blade.php ENDPATH**/ ?>