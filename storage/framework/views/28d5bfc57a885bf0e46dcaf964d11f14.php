
<?php $__env->startSection('title', 'عرض ملف — ' . basename($path)); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('admin.live-servers.ssh-browse', [$server, 'path' => dirname($path)])); ?>" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500"><i class="fas fa-arrow-right"></i></a>
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white"><i class="fas fa-file-code text-cyan-500 ml-2"></i><?php echo e(basename($path)); ?></h1>
                <p class="text-sm text-slate-500 font-mono break-all mt-0.5"><?php echo e($path); ?></p>
            </div>
        </div>
        <a href="<?php echo e(route('admin.live-servers.ssh-browse', [$server, 'path' => dirname($path)])); ?>" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium hover:bg-slate-300"><i class="fas fa-folder ml-1"></i> العودة للمجلد</a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <pre class="p-4 text-sm text-slate-700 dark:text-slate-300 overflow-auto max-h-[70vh] font-mono whitespace-pre-wrap break-words"><?php echo e(e($content)); ?></pre>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\live-servers\ssh-file.blade.php ENDPATH**/ ?>