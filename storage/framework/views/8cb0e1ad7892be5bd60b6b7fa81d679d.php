

<?php $__env->startSection('title', 'العملاء المحتملون'); ?>
<?php $__env->startSection('header', 'العملاء المحتملون (Leads)'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="<?php echo e(route('employee.sales.desk')); ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-right"></i> لوحة المبيعات
        </a>
        <a href="<?php echo e(route('employee.sales.leads.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold shadow-sm">
            <i class="fas fa-plus"></i> إضافة Lead
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">بحث</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم، بريد، هاتف، شركة، رقم…"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">الحالة</label>
                <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                    <option value="">الكل</option>
                    <?php $__currentLoopData = \App\Models\SalesLead::statusLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>" <?php echo e(request('status') === $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">المصدر</label>
                <select name="source" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                    <option value="">الكل</option>
                    <?php $__currentLoopData = \App\Models\SalesLead::sourceLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>" <?php echo e(request('source') === $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="lg:col-span-4 flex flex-wrap items-center gap-4">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="mine" value="1" <?php echo e(request('mine') ? 'checked' : ''); ?> class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                    المعيّنة لي فقط
                </label>
                <button type="submit" class="px-4 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold">تطبيق</button>
                <a href="<?php echo e(route('employee.sales.leads.index')); ?>" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold">إعادة ضبط</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 font-semibold">
                    <tr>
                        <th class="text-right px-4 py-3">#</th>
                        <th class="text-right px-4 py-3">الاسم</th>
                        <th class="text-right px-4 py-3">تواصل</th>
                        <th class="text-right px-4 py-3">المصدر</th>
                        <th class="text-right px-4 py-3">الحالة</th>
                        <th class="text-right px-4 py-3">المسؤول</th>
                        <th class="text-right px-4 py-3">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50/80">
                        <td class="px-4 py-3">
                            <a href="<?php echo e(route('employee.sales.leads.show', $lead)); ?>" class="font-bold text-teal-700 hover:underline">#<?php echo e($lead->id); ?></a>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900"><?php echo e($lead->name); ?></div>
                            <?php if($lead->company): ?><div class="text-xs text-gray-500"><?php echo e($lead->company); ?></div><?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-gray-700 text-xs">
                            <div><?php echo e($lead->email ?: '—'); ?></div>
                            <div><?php echo e($lead->phone ?: ''); ?></div>
                        </td>
                        <td class="px-4 py-3 text-gray-700"><?php echo e($lead->source_label); ?></td>
                        <td class="px-4 py-3">
                            <?php if($lead->status === \App\Models\SalesLead::STATUS_CONVERTED): ?>
                                <span class="rounded-full bg-emerald-100 text-emerald-800 px-2 py-0.5 text-xs font-bold"><?php echo e($lead->status_label); ?></span>
                            <?php elseif($lead->status === \App\Models\SalesLead::STATUS_LOST): ?>
                                <span class="rounded-full bg-rose-100 text-rose-800 px-2 py-0.5 text-xs font-bold"><?php echo e($lead->status_label); ?></span>
                            <?php elseif($lead->status === \App\Models\SalesLead::STATUS_QUALIFIED): ?>
                                <span class="rounded-full bg-indigo-100 text-indigo-800 px-2 py-0.5 text-xs font-bold"><?php echo e($lead->status_label); ?></span>
                            <?php else: ?>
                                <span class="rounded-full bg-amber-100 text-amber-800 px-2 py-0.5 text-xs font-bold"><?php echo e($lead->status_label); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-gray-700"><?php echo e($lead->assignedTo?->name ?? '—'); ?></td>
                        <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap"><?php echo e($lead->created_at?->format('Y-m-d')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-500">لا توجد نتائج. أضف عميلاً محتملاً من الزر أعلاه.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($leads->hasPages()): ?>
            <div class="px-4 py-3 border-t border-gray-100"><?php echo e($leads->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\employee\sales\leads\index.blade.php ENDPATH**/ ?>