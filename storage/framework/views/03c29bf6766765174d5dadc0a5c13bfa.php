

<?php $__env->startSection('title', 'تقرير CRM'); ?>
<?php $__env->startSection('header', $report->title); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4 max-w-3xl">

  <?php if(session('success')): ?><div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div><?php endif; ?>

  <div class="rounded-2xl border bg-white p-6 space-y-3 text-sm">
    <p><span class="text-gray-500">الموظف:</span> <strong><?php echo e($report->user?->name); ?></strong></p>
    <p><span class="text-gray-500">النوع:</span> <?php echo e($report->type_label); ?> — <span class="text-gray-500">الحالة:</span> <?php echo e($report->status_label); ?></p>
    <?php if($report->period_start): ?><p><span class="text-gray-500">الفترة:</span> <?php echo e($report->period_start->format('Y-m-d')); ?> — <?php echo e($report->period_end?->format('Y-m-d')); ?></p><?php endif; ?>
    <?php if($report->group): ?><p><span class="text-gray-500">المجموعة:</span> <?php echo e($report->group->name); ?></p><?php endif; ?>
    <?php if($report->summary): ?><div class="bg-gray-50 rounded-lg p-3 whitespace-pre-wrap"><?php echo e($report->summary); ?></div><?php endif; ?>
    <?php if($report->file_path): ?>
      <a href="<?php echo e(route('admin.crm.reports.download', $report)); ?>" class="inline-flex items-center gap-2 text-indigo-600 font-bold"><i class="fas fa-download"></i> تحميل الملف</a>
    <?php endif; ?>
    <?php if($report->admin_notes): ?><div class="border-t pt-3 text-gray-700"><strong>ملاحظات الإدارة:</strong> <?php echo e($report->admin_notes); ?></div><?php endif; ?>
  </div>

  <?php if($report->status !== \App\Models\CrmReport::STATUS_REVIEWED): ?>
  <form method="POST" action="<?php echo e(route('admin.crm.reports.review', $report)); ?>" class="rounded-2xl border bg-white p-6 space-y-3">
    <?php echo csrf_field(); ?>
    <h3 class="font-bold">تأكيد المراجعة</h3>
    <textarea name="admin_notes" rows="3" class="w-full rounded-lg border px-3 py-2" placeholder="ملاحظات للموظف (اختياري)"></textarea>
    <button class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold text-sm">تمت المراجعة</button>
  </form>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\crm\reports\show.blade.php ENDPATH**/ ?>