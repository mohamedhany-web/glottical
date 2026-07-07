

<?php $__env->startSection('title', 'طلبات بانتظار الدفع'); ?>
<?php $__env->startSection('header', 'CRM — الطلبات'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
  <?php echo $__env->make('partials.crm-employee-nav', ['role' => $role], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <p class="text-sm text-gray-600">
    <?php if($viewAll ?? false): ?>
      عرض كل الطلبات كما في لوحة الإدارة — راجعها ثم اعتمد بعد التأكد من استلام المبلغ.
    <?php else: ?>
      راجع الطلبات المرتبطة بعملاء CRM ثم اضغط اعتماد بعد التأكد من استلام المبلغ.
    <?php endif; ?>
  </p>

  <?php if($viewAll ?? false): ?>
  <form method="GET" class="flex flex-wrap gap-2 items-center text-sm">
    <select name="status" class="rounded-lg border px-3 py-2">
      <option value="pending" <?php if(request('status', 'pending')==='pending'): echo 'selected'; endif; ?>>معلقة</option>
      <option value="approved" <?php if(request('status')==='approved'): echo 'selected'; endif; ?>>معتمدة</option>
      <option value="rejected" <?php if(request('status')==='rejected'): echo 'selected'; endif; ?>>مرفوضة</option>
    </select>
    <label class="flex items-center gap-2"><input type="checkbox" name="crm_only" value="1" <?php if(request('crm_only')): echo 'checked'; endif; ?>> CRM فقط</label>
    <button class="px-3 py-2 rounded-lg bg-slate-700 text-white font-bold">تصفية</button>
  </form>
  <?php endif; ?>

  <div class="rounded-2xl border bg-white overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase">
        <tr>
          <th class="px-4 py-3 text-right">#</th>
          <th class="px-4 py-3 text-right">الطالب</th>
          <th class="px-4 py-3 text-right">الكورس</th>
          <th class="px-4 py-3 text-right">المبلغ</th>
          <th class="px-4 py-3 text-right">طريقة الدفع</th>
          <?php if($viewAll ?? false): ?><th class="px-4 py-3 text-right">مندوب</th><?php endif; ?>
          <th class="px-4 py-3 text-right"></th>
        </tr>
      </thead>
      <tbody class="divide-y">
        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td class="px-4 py-3"><?php echo e($order->id); ?></td>
            <td class="px-4 py-3 font-semibold"><?php echo e($order->user?->name ?? '—'); ?></td>
            <td class="px-4 py-3"><?php echo e($order->course?->title ?? '—'); ?></td>
            <td class="px-4 py-3 font-bold"><?php echo e(number_format((float) $order->amount, 2)); ?> ج.م</td>
            <td class="px-4 py-3"><?php echo e($order->payment_method ?? '—'); ?></td>
            <?php if($viewAll ?? false): ?><td class="px-4 py-3 text-gray-600"><?php echo e($order->salesOwner?->name ?? '—'); ?></td><?php endif; ?>
            <td class="px-4 py-3">
              <?php if($order->status === \App\Models\Order::STATUS_PENDING): ?>
              <form method="POST" action="<?php echo e(route('employee.crm.orders.approve', $order)); ?>" onsubmit="return confirm('تأكيد اعتماد الطلب واستلام الدفع؟')">
                <?php echo csrf_field(); ?>
                <button type="submit" class="text-emerald-600 font-bold text-xs">اعتماد الطلب</button>
              </form>
              <?php else: ?>
                <span class="text-gray-500 text-xs"><?php echo e($order->status); ?></span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="<?php echo e(($viewAll ?? false) ? 7 : 6); ?>" class="px-4 py-10 text-center text-gray-500">لا توجد طلبات</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="p-4"><?php echo e($orders->links()); ?></div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/employee/crm/orders.blade.php ENDPATH**/ ?>