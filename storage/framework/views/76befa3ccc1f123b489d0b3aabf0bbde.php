

<?php $__env->startSection('title', __('admin.hiring_academy_add')); ?>
<?php $__env->startSection('header', __('admin.hiring_academy_add')); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl space-y-6">
    <form action="<?php echo e(route('admin.hiring-academies.store')); ?>" method="POST" class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-6 md:p-8 space-y-6">
        <?php echo csrf_field(); ?>
        <?php echo $__env->make('admin.hiring-academies._form', ['academy' => null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-lg">حفظ</button>
            <a href="<?php echo e(route('admin.hiring-academies.index')); ?>" class="px-6 py-3 rounded-xl border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-sm font-semibold">إلغاء</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\hiring-academies\create.blade.php ENDPATH**/ ?>