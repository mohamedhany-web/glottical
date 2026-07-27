

<?php $__env->startSection('title', 'بيانات المسوقين'); ?>
<?php $__env->startSection('header', 'صندوق المسوقين بالعمولة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <?php echo $__env->make('partials.crm-employee-nav', ['role' => $role], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <?php if(session('success')): ?><div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div><?php endif; ?>
  <?php if($errors->any()): ?><div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm"><?php echo e($errors->first()); ?></div><?php endif; ?>

  <div class="rounded-2xl bg-gradient-to-l from-emerald-600 to-teal-700 text-white p-5">
    <h2 class="font-black text-lg">استلام ومتابعة بيانات المسوقين</h2>
    <p class="text-emerald-50 text-sm mt-1">استلم الليدز المرسلة من المسوقين بالعمولة، وتابع تقدمها حتى الاشتراك.</p>
  </div>

  <div class="grid grid-cols-3 gap-3">
    <div class="rounded-xl border bg-white p-4"><p class="text-xs text-gray-500">بانتظار الاستلام</p><p class="text-2xl font-black text-amber-700"><?php echo e(number_format($stats['inbox'])); ?></p></div>
    <div class="rounded-xl border bg-white p-4"><p class="text-xs text-gray-500">أسندها وتتقدم</p><p class="text-2xl font-black text-sky-700"><?php echo e(number_format($stats['tracking_open'])); ?></p></div>
    <div class="rounded-xl border bg-white p-4"><p class="text-xs text-gray-500">اشتركوا</p><p class="text-2xl font-black text-emerald-700"><?php echo e(number_format($stats['tracking_subscribed'])); ?></p></div>
  </div>

  <div class="rounded-2xl border bg-white overflow-hidden">
    <div class="px-5 py-3 border-b font-bold">صندوق الوارد من المسوقين</div>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-600"><tr>
          <th class="px-4 py-3 text-right">العميل</th>
          <th class="px-4 py-3 text-right">المسوق</th>
          <th class="px-4 py-3 text-right">الكورس</th>
          <th class="px-4 py-3 text-right">أُرسل</th>
          <th class="px-4 py-3 text-right"></th>
        </tr></thead>
        <tbody class="divide-y">
          <?php $__empty_1 = true; $__currentLoopData = $inbox; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td class="px-4 py-3">
                <div class="font-bold"><?php echo e($lead->name); ?></div>
                <div class="text-xs text-gray-500"><?php echo e($lead->phone ?: $lead->email); ?></div>
              </td>
              <td class="px-4 py-3"><?php echo e($lead->marketingOwner?->name ?? '—'); ?></td>
              <td class="px-4 py-3"><?php echo e($lead->interestedCourse?->title ?? '—'); ?></td>
              <td class="px-4 py-3 whitespace-nowrap text-xs"><?php echo e($lead->submitted_to_sales_at?->format('Y-m-d H:i')); ?></td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap gap-2 justify-end">
                  <a href="<?php echo e(route('employee.crm.leads.show', $lead)); ?>" class="text-sky-700 font-bold text-xs">عرض</a>
                  <?php if($canClaim): ?>
                    <form method="POST" action="<?php echo e(route('employee.crm.marketing-inbox.claim', $lead)); ?>" class="inline">
                      <?php echo csrf_field(); ?>
                      <button class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-bold">استلام</button>
                    </form>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="5" class="px-4 py-10 text-center text-gray-500">لا توجد بيانات مرسلة من المسوقين حالياً</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php if($inbox->hasPages()): ?><div class="p-4"><?php echo e($inbox->links()); ?></div><?php endif; ?>
  </div>

  <div class="rounded-2xl border bg-white overflow-hidden">
    <div class="px-5 py-3 border-b font-bold">متابعة تقدم ليدز المسوقين عندك</div>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-600"><tr>
          <th class="px-4 py-3 text-right">العميل</th>
          <th class="px-4 py-3 text-right">المسوق</th>
          <th class="px-4 py-3 text-right">الكورس</th>
          <th class="px-4 py-3 text-right">التقدم</th>
          <th class="px-4 py-3 text-right"></th>
        </tr></thead>
        <tbody class="divide-y">
          <?php $__empty_1 = true; $__currentLoopData = $tracking; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td class="px-4 py-3 font-bold"><?php echo e($lead->name); ?></td>
              <td class="px-4 py-3"><?php echo e($lead->marketingOwner?->name ?? '—'); ?></td>
              <td class="px-4 py-3"><?php echo e($lead->order?->course?->title ?? $lead->interestedCourse?->title ?? '—'); ?></td>
              <td class="px-4 py-3"><span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-bold"><?php echo e($lead->status_label); ?></span></td>
              <td class="px-4 py-3"><a href="<?php echo e(route('employee.crm.leads.show', $lead)); ?>" class="text-emerald-700 font-bold text-xs">متابعة</a></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="5" class="px-4 py-10 text-center text-gray-500">استلم من الصندوق أعلاه لتظهر هنا متابعة التقدم</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php if($tracking->hasPages()): ?><div class="p-4"><?php echo e($tracking->links()); ?></div><?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\employee\crm\sales\marketing-inbox.blade.php ENDPATH**/ ?>