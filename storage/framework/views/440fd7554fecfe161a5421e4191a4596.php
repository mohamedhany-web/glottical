
<?php $__env->startSection('title', 'إضافة سلايدر'); ?>
<?php $__env->startSection('header', 'سلايدر جديد للصفحة الرئيسية'); ?>
<?php $__env->startSection('content'); ?>
<div class="w-full">
    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
            <h1 class="text-xl font-bold text-slate-900">إضافة سلايدر للهيرو</h1>
        </div>
        <form action="<?php echo e(route('admin.homepage-sliders.store')); ?>" method="POST" enctype="multipart/form-data" class="p-5 sm:p-8">
            <?php echo csrf_field(); ?>
            <?php echo $__env->make('admin.homepage-sliders._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div class="flex flex-wrap gap-3 pt-6 mt-6 border-t border-slate-200">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-semibold">
                    <i class="fas fa-save"></i> حفظ
                </button>
                <a href="<?php echo e(route('admin.homepage-sliders.index')); ?>" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold">إلغاء</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/admin/homepage-sliders/create.blade.php ENDPATH**/ ?>