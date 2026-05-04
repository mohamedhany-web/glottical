<?php $__env->startSection('title', 'إضافة فرصة أكاديمية'); ?>
<?php $__env->startSection('header', 'إضافة فرصة أكاديمية'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <form action="<?php echo e(route('admin.academy-opportunities.store')); ?>" method="POST" class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 space-y-4">
        <?php echo csrf_field(); ?>
        <?php echo $__env->make('admin.academy-opportunities.form', ['opportunity' => null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="text-left">
            <button class="px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold">حفظ الفرصة</button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\academy-opportunities\create.blade.php ENDPATH**/ ?>