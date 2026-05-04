

<?php $__env->startSection('title', 'العملاء المحتملون'); ?>
<?php $__env->startSection('header', 'العملاء المحتملون (Leads)'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <p class="text-sm text-slate-600">عرض كل الـ Leads التي يسجّلها فريق المبيعات. التعديل والتحويل من لوحة الموظف.</p>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 sm:p-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">بحث</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم، بريد، هاتف، رقم…"
                       class="rounded-lg border border-slate-300 px-3 py-2 text-sm w-64 max-w-full">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">الحالة</label>
                <select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">الكل</option>
                    <?php $__currentLoopData = \App\Models\SalesLead::statusLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>" <?php echo e(request('status') === $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">تصفية</button>
            <a href="<?php echo e(route('admin.sales.leads.index')); ?>" class="px-4 py-2 rounded-lg bg-slate-200 text-slate-800 text-sm font-semibold">إعادة ضبط</a>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 font-semibold">
                    <tr>
                        <th class="text-right px-4 py-3">#</th>
                        <th class="text-right px-4 py-3">الاسم</th>
                        <th class="text-right px-4 py-3">تواصل</th>
                        <th class="text-right px-4 py-3">الحالة</th>
                        <th class="text-right px-4 py-3">المسؤول</th>
                        <th class="text-right px-4 py-3">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-4 py-3">
                            <a href="<?php echo e(route('admin.sales.leads.show', $lead)); ?>" class="font-bold text-emerald-700 hover:underline">#<?php echo e($lead->id); ?></a>
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-900"><?php echo e($lead->name); ?></td>
                        <td class="px-4 py-3 text-xs text-slate-600">
                            <div><?php echo e($lead->email ?: '—'); ?></div>
                            <div><?php echo e($lead->phone); ?></div>
                        </td>
                        <td class="px-4 py-3"><?php echo e($lead->status_label); ?></td>
                        <td class="px-4 py-3 text-slate-700"><?php echo e($lead->assignedTo?->name ?? '—'); ?></td>
                        <td class="px-4 py-3 text-xs text-slate-500"><?php echo e($lead->created_at?->format('Y-m-d')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="px-4 py-12 text-center text-slate-500">لا توجد سجلات.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($leads->hasPages()): ?>
            <div class="px-4 py-3 border-t border-slate-100"><?php echo e($leads->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\sales\leads\index.blade.php ENDPATH**/ ?>