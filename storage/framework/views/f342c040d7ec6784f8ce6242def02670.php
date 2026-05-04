
<?php $__env->startSection('title', 'تسجيلات جلسات البث'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">
            <i class="fas fa-play-circle text-emerald-500 ml-2"></i>تسجيلات جلسات البث
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">مشاهدة تسجيلات الجلسات المنتهية (مخزنة على R2)</p>
    </div>

    <?php if($recordings->isEmpty()): ?>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-12 text-center">
        <i class="fas fa-film text-5xl text-slate-300 dark:text-slate-600 mb-4"></i>
        <p class="text-lg font-semibold text-slate-600 dark:text-slate-400">لا توجد تسجيلات متاحة حالياً</p>
        <p class="text-sm text-slate-500 mt-1">ستظهر هنا تسجيلات الجلسات بعد انتهائها ونشرها من الإدارة</p>
        <a href="<?php echo e(route('student.live-sessions.index')); ?>" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium">عودة لجلسات البث</a>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__currentLoopData = $recordings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('student.live-recordings.show', $rec)); ?>" class="block bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 hover:border-emerald-300 dark:hover:border-emerald-700 hover:shadow-lg transition-all group">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
                <i class="fas fa-play text-lg"></i>
            </div>
            <h3 class="font-bold text-slate-800 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors"><?php echo e($rec->title ?? 'تسجيل #' . $rec->id); ?></h3>
            <p class="text-sm text-slate-500 mt-1"><?php echo e($rec->session?->title); ?></p>
            <div class="flex items-center gap-3 mt-2 text-xs text-slate-400">
                <span><i class="fas fa-clock ml-1"></i> <?php echo e($rec->duration_for_humans); ?></span>
                <span><i class="fas fa-hdd ml-1"></i> <?php echo e($rec->file_size_for_humans); ?></span>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php if($recordings->hasPages()): ?>
    <div class="mt-6"><?php echo e($recordings->links()); ?></div>
    <?php endif; ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\student\live-recordings\index.blade.php ENDPATH**/ ?>