

<?php $__env->startSection('title', 'تصنيفات استفسار الدعم الفني'); ?>
<?php $__env->startSection('header', 'تصنيفات استفسار الدعم الفني'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm font-medium"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <h1 class="text-xl font-bold text-slate-900">تصنيفات الاستفسار</h1>
        <p class="text-sm text-slate-600 mt-1">يختار الطالب أحد هذه التصنيفات عند إنشاء تذكرة دعم. التصنيفات المعطّلة لا تظهر في نموذج الطالب.</p>
        <a href="<?php echo e(route('admin.support-tickets.index')); ?>" class="inline-flex items-center gap-2 mt-4 text-sm font-semibold text-sky-600 hover:text-sky-800">
            <i class="fas fa-arrow-right"></i> العودة لتذاكر الدعم
        </a>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <h2 class="font-bold text-slate-900 mb-4">إضافة تصنيف جديد</h2>
        <form method="POST" action="<?php echo e(route('admin.support-inquiry-categories.store')); ?>" class="flex flex-col sm:flex-row flex-wrap items-end gap-3">
            <?php echo csrf_field(); ?>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-slate-600 mb-1">اسم التصنيف</label>
                <input type="text" name="name" value="<?php echo e(old('name')); ?>" required maxlength="120"
                       class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm" placeholder="مثال: مشكلة في البث المباشر">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="w-28">
                <label class="block text-xs font-semibold text-slate-600 mb-1">الترتيب</label>
                <input type="number" name="sort_order" value="<?php echo e(old('sort_order', 0)); ?>" min="0" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
            </div>
            <label class="inline-flex items-center gap-2 pb-2 text-sm text-slate-700">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-sky-600"> نشط
            </label>
            <button type="submit" class="px-5 py-2 rounded-xl bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700">إضافة</button>
        </form>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 bg-slate-50 border-b border-slate-200">
            <h2 class="font-bold text-slate-900">التصنيفات الحالية</h2>
        </div>
        <div class="divide-y divide-slate-100">
            <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="p-4 sm:p-5 space-y-3">
                    <form method="POST" action="<?php echo e(route('admin.support-inquiry-categories.update', $cat)); ?>" class="flex flex-col lg:flex-row flex-wrap items-end gap-3">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-xs font-semibold text-slate-500 mb-1">الاسم</label>
                            <input type="text" name="name" value="<?php echo e(old('name', $cat->name)); ?>" required maxlength="120" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                        </div>
                        <div class="w-24">
                            <label class="block text-xs font-semibold text-slate-500 mb-1">ترتيب</label>
                            <input type="number" name="sort_order" value="<?php echo e(old('sort_order', $cat->sort_order)); ?>" min="0" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                        </div>
                        <label class="inline-flex items-center gap-2 pb-2 text-sm text-slate-700">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-sky-600" <?php echo e($cat->is_active ? 'checked' : ''); ?>> نشط
                        </label>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-semibold">حفظ التعديل</button>
                    </form>
                    <form method="POST" action="<?php echo e(route('admin.support-inquiry-categories.destroy', $cat)); ?>" class="inline" onsubmit="return confirm('حذف هذا التصنيف؟ التذاكر المرتبطة ستبقى دون تصنيف.');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline">حذف التصنيف</button>
                    </form>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="px-5 py-10 text-center text-sm text-slate-500">لا توجد تصنيفات بعد.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\support-inquiry-categories\index.blade.php ENDPATH**/ ?>