

<?php $__env->startSection('title', 'فريقي وأداء الأعضاء'); ?>
<?php $__env->startSection('header', 'CRM — فريقي'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <?php echo $__env->make('partials.crm-employee-nav', ['role' => $role], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <?php if(session('success')): ?><div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div><?php endif; ?>
  <?php if($errors->any()): ?><div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm"><?php echo e($errors->first()); ?></div><?php endif; ?>

  <?php if($memberStats->isNotEmpty()): ?>
  <div class="rounded-2xl border bg-white overflow-hidden">
    <div class="px-5 py-3 border-b font-bold">أداء كل عضو في الفريق</div>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase">
          <tr>
            <th class="px-4 py-3 text-right">العضو</th>
            <th class="px-4 py-3 text-right">الدور</th>
            <th class="px-4 py-3 text-right">المجموعة</th>
            <th class="px-4 py-3 text-right">إجمالي</th>
            <th class="px-4 py-3 text-right">مفتوحة</th>
            <th class="px-4 py-3 text-right">ناجحة</th>
            <th class="px-4 py-3 text-right">بانتظار الدفع</th>
            <th class="px-4 py-3 text-right">إيراد</th>
            <th class="px-4 py-3 text-right">عمولات</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <?php $__currentLoopData = $memberStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
              <td class="px-4 py-3 font-semibold"><?php echo e($row['user_name']); ?></td>
              <td class="px-4 py-3"><?php echo e($row['role_label']); ?></td>
              <td class="px-4 py-3 text-gray-600"><?php echo e($row['group_name']); ?></td>
              <td class="px-4 py-3"><?php echo e($row['total_leads']); ?></td>
              <td class="px-4 py-3"><?php echo e($row['open_leads'] ?? 0); ?></td>
              <td class="px-4 py-3 text-emerald-700 font-bold"><?php echo e($row['closed_won']); ?></td>
              <td class="px-4 py-3 text-amber-700"><?php echo e($row['payment_pending'] ?? 0); ?></td>
              <td class="px-4 py-3"><?php echo e(number_format($row['revenue'], 2)); ?> ج.م</td>
              <td class="px-4 py-3"><?php echo e(number_format($row['commissions'], 2)); ?> ج.م</td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

  <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <div class="rounded-2xl border bg-white p-6 space-y-4">
    <div class="flex justify-between items-center gap-3">
      <h2 class="text-lg font-black"><?php echo e($group->name); ?></h2>
      <span class="text-xs bg-sky-100 text-sky-800 px-2 py-1 rounded"><?php echo e($group->leads_count); ?> عميل</span>
    </div>

    <div class="space-y-2">
      <?php $__empty_1 = true; $__currentLoopData = $group->activeMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="flex justify-between items-center border rounded-lg px-4 py-2 text-sm">
          <div>
            <span class="font-semibold"><?php echo e($member->user?->name); ?></span>
            <span class="text-gray-500 mr-2">— <?php echo e($member->role === 'marketing' ? 'تسويق' : 'مبيعات'); ?></span>
          </div>
          <?php if($canManage): ?>
          <form method="POST" action="<?php echo e(route('employee.crm.team.members.destroy', [$group, $member])); ?>" onsubmit="return confirm('إزالة العضو من الفريق؟')">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button class="text-rose-600 text-xs font-bold">إزالة</button>
          </form>
          <?php endif; ?>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-sm text-gray-500">لا يوجد أعضاء نشطون.</p>
      <?php endif; ?>
    </div>

    <?php if($canManage): ?>
    <form method="POST" action="<?php echo e(route('employee.crm.team.members.store', $group)); ?>" class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-3 border-t">
      <?php echo csrf_field(); ?>
      <select name="user_id" class="rounded-lg border px-3 py-2 text-sm" required>
        <option value="">اختر موظفاً</option>
        <optgroup label="تسويق">
          <?php $__currentLoopData = $marketingUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </optgroup>
        <optgroup label="مبيعات">
          <?php $__currentLoopData = $salesUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </optgroup>
      </select>
      <select name="role" class="rounded-lg border px-3 py-2 text-sm" required>
        <option value="marketing">تسويق</option>
        <option value="sales">مبيعات</option>
      </select>
      <button class="px-4 py-2 rounded-xl bg-sky-600 text-white font-bold text-sm">إضافة عضو</button>
    </form>
    <?php endif; ?>
  </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

  <?php if($groups->isEmpty()): ?>
    <div class="rounded-xl bg-amber-50 border border-amber-200 text-amber-900 px-4 py-3 text-sm">لم يُعيَّن لك فريق بعد. تواصل مع الإدارة لربطك كقائد فريق.</div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/employee/crm/team/index.blade.php ENDPATH**/ ?>