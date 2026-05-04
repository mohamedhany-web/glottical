

<?php $__env->startSection('title', 'طلبات الإجازات — الموارد البشرية'); ?>
<?php $__env->startSection('header', 'مراجعة طلبات الإجازات'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="<?php echo e(route('employee.hr-desk.index')); ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-right"></i> لوحة الموارد البشرية
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-900 px-4 py-3 text-sm"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-gray-500">الإجمالي</p>
            <p class="text-2xl font-black text-slate-900 tabular-nums"><?php echo e($stats['total']); ?></p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-4 shadow-sm">
            <p class="text-xs font-semibold text-amber-800">معلّقة</p>
            <p class="text-2xl font-black text-amber-900 tabular-nums"><?php echo e($stats['pending']); ?></p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-4 shadow-sm">
            <p class="text-xs font-semibold text-emerald-800">موافق</p>
            <p class="text-2xl font-black text-emerald-900 tabular-nums"><?php echo e($stats['approved']); ?></p>
        </div>
        <div class="rounded-2xl border border-rose-200 bg-rose-50/50 p-4 shadow-sm">
            <p class="text-xs font-semibold text-rose-800">مرفوض</p>
            <p class="text-2xl font-black text-rose-900 tabular-nums"><?php echo e($stats['rejected']); ?></p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">بحث</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم، بريد، رمز موظف…"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-rose-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">الموظف</label>
                <select name="employee_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="">الكل</option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($emp->id); ?>" <?php echo e((string) request('employee_id') === (string) $emp->id ? 'selected' : ''); ?>><?php echo e($emp->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">الحالة</label>
                <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="">الكل</option>
                    <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>معلّقة</option>
                    <option value="approved" <?php echo e(request('status') === 'approved' ? 'selected' : ''); ?>>موافق</option>
                    <option value="rejected" <?php echo e(request('status') === 'rejected' ? 'selected' : ''); ?>>مرفوض</option>
                    <option value="cancelled" <?php echo e(request('status') === 'cancelled' ? 'selected' : ''); ?>>ملغاة</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold">تطبيق</button>
                <a href="<?php echo e(route('employee.hr.leaves.index')); ?>" class="px-3 py-2 rounded-lg bg-gray-200 text-gray-800 text-sm font-semibold">إعادة</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 font-semibold">
                    <tr>
                        <th class="text-right px-4 py-3">الموظف</th>
                        <th class="text-right px-4 py-3">النوع</th>
                        <th class="text-right px-4 py-3">الفترة</th>
                        <th class="text-right px-4 py-3">الحالة</th>
                        <th class="text-right px-4 py-3">التقديم</th>
                        <th class="text-right px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $leaveRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50/80">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900"><?php echo e($req->employee?->name ?? '—'); ?></div>
                            <div class="text-xs text-gray-500"><?php echo e($req->employee?->employee_code ?? $req->employee?->email); ?></div>
                        </td>
                        <td class="px-4 py-3"><?php echo e($req->type_label); ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-700"><?php echo e($req->start_date?->format('Y-m-d')); ?> → <?php echo e($req->end_date?->format('Y-m-d')); ?> (<?php echo e($req->days); ?> يوم)</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold
                                <?php if($req->status === 'pending'): ?> bg-amber-100 text-amber-800
                                <?php elseif($req->status === 'approved'): ?> bg-emerald-100 text-emerald-800
                                <?php elseif($req->status === 'rejected'): ?> bg-rose-100 text-rose-800
                                <?php else: ?> bg-gray-100 text-gray-700 <?php endif; ?>"><?php echo e($req->status_label); ?></span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap"><?php echo e($req->created_at?->format('Y-m-d')); ?></td>
                        <td class="px-4 py-3">
                            <a href="<?php echo e(route('employee.hr.leaves.show', $req)); ?>" class="text-rose-700 hover:underline font-bold">تفاصيل</a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="px-4 py-12 text-center text-gray-500">لا توجد طلبات.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($leaveRequests->hasPages()): ?>
            <div class="px-4 py-3 border-t border-gray-100"><?php echo e($leaveRequests->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\employee\hr\leaves\index.blade.php ENDPATH**/ ?>