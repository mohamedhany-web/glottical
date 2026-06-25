

<?php $__env->startSection('title', 'تصميم شهادة المنصة'); ?>
<?php $__env->startSection('header', 'تصميم شهادة المنصة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">معاينة تصميم الشهادة</h1>
                <p class="text-gray-600 mt-1 text-sm">نفس القالب المستخدم عند اختيار «شهادة النظام» عند إصدار شهادة. البيانات أدناه نصوص توضيحية فقط.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('admin.certificates.preview-sample')); ?>" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 rounded-lg border-2 border-indigo-200 bg-indigo-50 hover:bg-indigo-100 text-indigo-900 px-4 py-2.5 text-sm font-bold transition-colors">
                    <i class="fas fa-external-link-alt text-indigo-600 text-xs"></i>
                    فتح المعاينة في تبويب جديد
                </a>
                <a href="<?php echo e(route('admin.certificates.create')); ?>"
                   class="inline-flex items-center gap-2 rounded-lg bg-sky-600 hover:bg-sky-700 text-white px-4 py-2.5 text-sm font-bold transition-colors">
                    <i class="fas fa-plus text-xs"></i>
                    إصدار شهادة
                </a>
                <a href="<?php echo e(route('admin.certificates.index')); ?>"
                   class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-800 px-4 py-2.5 text-sm font-semibold">
                    رجوع للقائمة
                </a>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-slate-100/80 overflow-hidden shadow-inner" style="min-height: 70vh;">
        <iframe
            src="<?php echo e(route('admin.certificates.preview-sample')); ?>"
            title="معاينة شهادة المنصة"
            class="w-full border-0 bg-white"
            style="min-height: 70vh;"
            loading="lazy"
        ></iframe>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\certificates\design.blade.php ENDPATH**/ ?>