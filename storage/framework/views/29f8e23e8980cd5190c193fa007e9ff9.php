

<?php $__env->startSection('title', $label . ' - ميزة من باقتك'); ?>
<?php $__env->startSection('header', $label); ?>

<?php $__env->startSection('content'); ?>
<?php
    $cfg = $featureConfig ?? [];
    $icon = $cfg['icon'] ?? 'fa-star';
    $iconBg = $cfg['icon_bg'] ?? 'bg-sky-100 dark:bg-sky-900/40';
    $iconText = $cfg['icon_text'] ?? 'text-sky-600 dark:text-sky-400';
?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-lg overflow-hidden">
        <div class="bg-gradient-to-l from-slate-50 via-white to-white dark:from-slate-800/90 dark:via-slate-800/95 dark:to-slate-900/95 p-6 border-b border-slate-100 dark:border-slate-700">
            <div class="flex flex-wrap items-center gap-3 mb-2">
                <span class="w-12 h-12 rounded-xl <?php echo e($iconBg); ?> <?php echo e($iconText); ?> flex items-center justify-center">
                    <i class="fas <?php echo e($icon); ?> text-lg"></i>
                </span>
                <div class="flex-1 min-w-0">
                    <h1 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-slate-100"><?php echo e($label); ?></h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-200 text-xs font-semibold border border-amber-200 dark:border-amber-800/60">
                        ميزة من باقتك
                    </span>
                </div>
            </div>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-2"><?php echo e($description); ?></p>
        </div>
        <div class="p-6">
            <div class="rounded-xl border border-dashed border-slate-200 dark:border-slate-600 bg-slate-50/50 dark:bg-slate-900/40 p-8 text-center">
                <p class="text-slate-600 dark:text-slate-300 mb-4">هذه الصفحة مخصصة لهذه الميزة. سيتم إثراؤها بمحتوى تفاعلي لاحقاً حسب نوع الخدمة.</p>
                <a href="<?php echo e(route('student.my-subscription')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-sky-600 dark:bg-sky-700 text-white text-sm font-semibold hover:bg-sky-700 dark:hover:bg-sky-600 transition-colors">
                    <i class="fas fa-layer-group"></i>
                    عرض تفاصيل اشتراكي
                </a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\student\features\show.blade.php ENDPATH**/ ?>