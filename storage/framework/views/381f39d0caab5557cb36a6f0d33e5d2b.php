<?php $__env->startSection('title', __('admin.course_categories')); ?>
<?php $__env->startSection('header', __('admin.course_categories')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full px-4 py-6 space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 dark:bg-emerald-900/20 dark:border-emerald-800 px-4 py-3 text-sm text-emerald-800 dark:text-emerald-200">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="section-card">
        <div class="section-card-header">
            <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100"><?php echo e(__('admin.course_categories')); ?></h1>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                المسارات التي تظهر في قائمة التصفية بصفحة الكورسات العامة. أضف اسماً لكل مسار ثم اختره عند إنشاء أو تعديل كورس.
            </p>
        </div>
        <div class="p-6 sm:p-8 border-t border-slate-100 dark:border-slate-700">
            <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4">إضافة مسار جديد</h2>
            <form method="POST" action="<?php echo e(route('admin.course-categories.store')); ?>" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <?php echo csrf_field(); ?>
                <div class="md:col-span-5 space-y-2">
                    <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">اسم المسار *</label>
                    <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" required maxlength="255"
                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                           placeholder="مثال: التدريس التفاعلي">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="md:col-span-3 space-y-2">
                    <label for="sort_order" class="block text-sm font-medium text-slate-700 dark:text-slate-300">ترتيب العرض</label>
                    <input type="number" name="sort_order" id="sort_order" value="<?php echo e(old('sort_order')); ?>" min="0" max="99999"
                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                           placeholder="تلقائي إن وُجد فارغاً">
                    <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="md:col-span-2 flex items-center gap-2 pb-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500" <?php echo e(old('is_active', true) ? 'checked' : ''); ?>>
                    <label for="is_active" class="text-sm text-slate-700 dark:text-slate-300">نشط</label>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center gap-2 bg-sky-600 hover:bg-sky-700 text-white px-5 py-3 rounded-xl font-semibold transition">
                        <i class="fas fa-plus"></i>
                        إضافة
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="section-card overflow-hidden">
        <div class="section-card-header">
            <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">المسارات الحالية</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300">
                    <tr>
                        <th class="text-right px-4 py-3 font-semibold">الاسم</th>
                        <th class="text-right px-4 py-3 font-semibold w-28">الترتيب</th>
                        <th class="text-right px-4 py-3 font-semibold w-28">الحالة</th>
                        <th class="text-right px-4 py-3 font-semibold w-40">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                            <td class="px-4 py-3 text-slate-800 dark:text-slate-200 font-medium"><?php echo e($row->name); ?></td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-400"><?php echo e($row->sort_order); ?></td>
                            <td class="px-4 py-3">
                                <?php if($row->is_active): ?>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">نشط</span>
                                <?php else: ?>
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 dark:bg-slate-600 dark:text-slate-300">معطّل</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="<?php echo e(route('admin.course-categories.edit', $row)); ?>" class="inline-flex items-center gap-1 text-sky-600 hover:text-sky-800 text-xs font-semibold">
                                        <i class="fas fa-pen"></i> تعديل
                                    </a>
                                    <form method="POST" action="<?php echo e(route('admin.course-categories.destroy', $row)); ?>" class="inline" onsubmit="return confirm('حذف هذا المسار؟ الكورسات المرتبطة ستُزال ربطها بالمسار.');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="inline-flex items-center gap-1 text-rose-600 hover:text-rose-800 text-xs font-semibold">
                                            <i class="fas fa-trash"></i> حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-slate-500 dark:text-slate-400">لا توجد مسارات بعد. أضف مساراً بالأعلى ليظهر في صفحة الكورسات.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\course-categories\index.blade.php ENDPATH**/ ?>