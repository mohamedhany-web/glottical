

<?php $__env->startSection('title', 'مكتبة المناهج التفاعلية'); ?>
<?php $__env->startSection('header', 'مكتبة المناهج التفاعلية'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900">عناصر المنهج</h1>
                <p class="text-sm text-slate-600 mt-1">إدارة محتوى مكتبة المناهج التفاعلية الجاهزة للمعلمين.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('admin.curriculum-library.categories')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50">
                    <i class="fas fa-folder"></i> التصنيفات
                </a>
                <a href="<?php echo e(route('admin.curriculum-library.items.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">
                    <i class="fas fa-plus"></i> إضافة عنصر
                </a>
            </div>
        </div>

        <form method="GET" class="p-4 border-b border-slate-100 flex flex-wrap gap-3 items-center">
            <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="بحث في العنوان أو الوصف أو المادة..."
                   class="px-3 py-2 rounded-lg border border-slate-200 text-sm w-64">
            <select name="category_id" class="px-3 py-2 rounded-lg border border-slate-200 text-sm">
                <option value="">كل التصنيفات</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category_id') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button type="submit" class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200"><i class="fas fa-search ml-1"></i> بحث</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-slate-50 text-slate-600 text-sm font-semibold">
                    <tr>
                        <th class="px-4 py-3">العنوان</th>
                        <th class="px-4 py-3">التصنيف</th>
                        <th class="px-4 py-3">المادة / المرحلة</th>
                        <th class="px-4 py-3">الحالة</th>
                        <th class="px-4 py-3">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('admin.curriculum-library.items.edit', $item)); ?>" class="font-semibold text-slate-900 hover:text-indigo-600"><?php echo e($item->title); ?></a>
                            </td>
                            <td class="px-4 py-3 text-slate-600"><?php echo e($item->category?->name ?? '—'); ?></td>
                            <td class="px-4 py-3 text-slate-600"><?php echo e($item->subject ?? '—'); ?> <?php if($item->grade_level): ?> / <?php echo e($item->grade_level); ?> <?php endif; ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium <?php echo e($item->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'); ?>">
                                    <?php echo e($item->is_active ? 'نشط' : 'معطل'); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('admin.curriculum-library.items.structure', $item)); ?>" class="text-sky-600 hover:text-sky-800 text-sm font-semibold mr-2">هيكل</a>
                                <a href="<?php echo e(route('admin.curriculum-library.items.edit', $item)); ?>" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">تعديل</a>
                                <form action="<?php echo e(route('admin.curriculum-library.items.destroy', $item)); ?>" method="POST" class="inline-block mr-2" onsubmit="return confirm('حذف هذا العنصر؟');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-rose-600 hover:text-rose-800 text-sm font-semibold">حذف</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-500">لا توجد عناصر. <a href="<?php echo e(route('admin.curriculum-library.items.create')); ?>" class="text-indigo-600 font-semibold">أضف أول عنصر</a></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($items->hasPages()): ?>
            <div class="px-6 py-4 border-t border-slate-100"><?php echo e($items->withQueryString()->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\curriculum-library\index.blade.php ENDPATH**/ ?>