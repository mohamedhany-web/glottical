

<?php $__env->startSection('title', 'طلبات المبيعات'); ?>
<?php $__env->startSection('header', 'طلبات المبيعات'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-wrap gap-3">
        <a href="<?php echo e(route('employee.sales.desk')); ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-right"></i> لوحة المبيعات
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">بحث</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم، بريد، هاتف، رقم الطلب، كورس…"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">الحالة</label>
                <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
                    <option value="">الكل</option>
                    <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>معلّق</option>
                    <option value="approved" <?php echo e(request('status') === 'approved' ? 'selected' : ''); ?>>معتمد</option>
                    <option value="rejected" <?php echo e(request('status') === 'rejected' ? 'selected' : ''); ?>>مرفوض</option>
                </select>
            </div>
            <div class="flex flex-wrap items-end gap-3">
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="mine" value="1" <?php echo e(request('mine') ? 'checked' : ''); ?> class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    طلباتي فقط
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="unassigned" value="1" <?php echo e(request('unassigned') ? 'checked' : ''); ?> class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    بلا مندوب
                </label>
            </div>
            <div class="lg:col-span-4 flex flex-wrap gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">تطبيق</button>
                <a href="<?php echo e(route('employee.sales.orders.index')); ?>" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-semibold">إعادة ضبط</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 font-semibold">
                    <tr>
                        <th class="text-right px-4 py-3">#</th>
                        <th class="text-right px-4 py-3">العميل</th>
                        <th class="text-right px-4 py-3">الكورس</th>
                        <th class="text-right px-4 py-3">المبلغ</th>
                        <th class="text-right px-4 py-3">المندوب</th>
                        <th class="text-right px-4 py-3">الحالة</th>
                        <th class="text-right px-4 py-3">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50/80">
                        <td class="px-4 py-3">
                            <a href="<?php echo e(route('employee.sales.orders.show', $order)); ?>" class="font-bold text-emerald-700 hover:underline">#<?php echo e($order->id); ?></a>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900"><?php echo e($order->user?->name ?? '—'); ?></div>
                            <div class="text-xs text-gray-500"><?php echo e($order->user?->email); ?></div>
                        </td>
                        <td class="px-4 py-3 text-gray-800"><?php echo e($order->course?->title ?? '—'); ?></td>
                        <td class="px-4 py-3 font-semibold tabular-nums"><?php echo e(number_format((float) $order->amount, 2)); ?></td>
                        <td class="px-4 py-3 text-gray-700"><?php echo e($order->salesOwner?->name ?? '—'); ?></td>
                        <td class="px-4 py-3">
                            <?php if($order->status === \App\Models\Order::STATUS_PENDING): ?>
                                <span class="rounded-full bg-amber-100 text-amber-800 px-2 py-0.5 text-xs font-bold">معلّق</span>
                            <?php elseif($order->status === \App\Models\Order::STATUS_APPROVED): ?>
                                <span class="rounded-full bg-emerald-100 text-emerald-800 px-2 py-0.5 text-xs font-bold">معتمد</span>
                            <?php else: ?>
                                <span class="rounded-full bg-rose-100 text-rose-800 px-2 py-0.5 text-xs font-bold">مرفوض</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap"><?php echo e($order->created_at?->format('Y-m-d H:i')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-500">لا توجد طلبات مطابقة.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($orders->hasPages()): ?>
            <div class="px-4 py-3 border-t border-gray-100"><?php echo e($orders->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\employee\sales\orders-index.blade.php ENDPATH**/ ?>