<?php $__env->startSection('title', 'تعديل مسار'); ?>
<?php $__env->startSection('header', __('admin.course_categories')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <div class="section-card max-w-2xl">
        <div class="section-card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="text-sm text-slate-500 dark:text-slate-400 mb-2">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-sky-600"><?php echo e(__('admin.dashboard')); ?></a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo e(route('admin.course-categories.index')); ?>" class="hover:text-sky-600"><?php echo e(__('admin.course_categories')); ?></a>
                    <span class="mx-2">/</span>
                    <span class="text-slate-700 dark:text-slate-300">تعديل</span>
                </nav>
                <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100">تعديل المسار</h1>
            </div>
            <a href="<?php echo e(route('admin.course-categories.index')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-600 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                <i class="fas fa-arrow-right"></i>
                العودة
            </a>
        </div>
        <div class="p-6 sm:p-8 border-t border-slate-100 dark:border-slate-700">
            <form method="POST" action="<?php echo e(route('admin.course-categories.update', $category)); ?>" class="space-y-6">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">اسم المسار *</label>
                    <input type="text" name="name" id="name" value="<?php echo e(old('name', $category->name)); ?>" required maxlength="255"
                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="space-y-2">
                    <label for="sort_order" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">ترتيب العرض</label>
                    <input type="number" name="sort_order" id="sort_order" value="<?php echo e(old('sort_order', $category->sort_order)); ?>" min="0" max="99999"
                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <label class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500" <?php echo e(old('is_active', $category->is_active) ? 'checked' : ''); ?>>
                    <span class="text-sm text-slate-700 dark:text-slate-300">مسار نشط (يظهر في قائمة التصفية العامة)</span>
                </label>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white px-6 py-3 rounded-xl font-semibold transition">
                        حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\course-categories\edit.blade.php ENDPATH**/ ?>