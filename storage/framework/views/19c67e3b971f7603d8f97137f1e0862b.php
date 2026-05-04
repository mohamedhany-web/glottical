

<?php $__env->startSection('title', 'Lead #' . $salesLead->id); ?>
<?php $__env->startSection('header', 'عميل محتمل #' . $salesLead->id); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-4xl">
    <a href="<?php echo e(route('admin.sales.leads.index')); ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900">
        <i class="fas fa-arrow-right"></i> القائمة
    </a>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-wrap justify-between gap-3">
            <h2 class="text-xl font-black text-slate-900"><?php echo e($salesLead->name); ?></h2>
            <span class="rounded-full bg-slate-100 text-slate-800 px-3 py-1 text-sm font-bold"><?php echo e($salesLead->status_label); ?></span>
        </div>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div><dt class="text-slate-500 font-semibold">البريد</dt><dd><?php echo e($salesLead->email ?: '—'); ?></dd></div>
            <div><dt class="text-slate-500 font-semibold">الهاتف</dt><dd><?php echo e($salesLead->phone ?: '—'); ?></dd></div>
            <div><dt class="text-slate-500 font-semibold">الشركة</dt><dd><?php echo e($salesLead->company ?: '—'); ?></dd></div>
            <div><dt class="text-slate-500 font-semibold">المصدر</dt><dd><?php echo e($salesLead->source_label); ?></dd></div>
            <div><dt class="text-slate-500 font-semibold">كورس الاهتمام</dt><dd><?php echo e($salesLead->interestedCourse?->title ?? '—'); ?></dd></div>
            <div><dt class="text-slate-500 font-semibold">أنشأه</dt><dd><?php echo e($salesLead->creator?->name ?? '—'); ?></dd></div>
            <div><dt class="text-slate-500 font-semibold">المسؤول</dt><dd><?php echo e($salesLead->assignedTo?->name ?? '—'); ?></dd></div>
        </dl>
        <?php if($salesLead->notes): ?>
            <div class="rounded-lg bg-slate-50 border border-slate-100 p-3 text-sm whitespace-pre-wrap"><?php echo e($salesLead->notes); ?></div>
        <?php endif; ?>
        <?php if($salesLead->isConverted()): ?>
            <div class="rounded-lg border border-emerald-200 bg-emerald-50/50 p-4 text-sm space-y-2">
                <p class="font-bold text-emerald-900">تحويل</p>
                <?php if($salesLead->converted_at): ?><p><?php echo e($salesLead->converted_at->format('Y-m-d H:i')); ?></p><?php endif; ?>
                <?php if($salesLead->linkedUser): ?>
                    <p>مستخدم: <a href="<?php echo e(route('admin.users.show', $salesLead->linkedUser->id)); ?>" class="font-bold text-emerald-700 hover:underline"><?php echo e($salesLead->linkedUser->name); ?></a></p>
                <?php endif; ?>
                <?php if($salesLead->convertedOrder): ?>
                    <p>طلب: <a href="<?php echo e(route('admin.orders.show', $salesLead->convertedOrder)); ?>" class="font-bold text-emerald-700 hover:underline">#<?php echo e($salesLead->convertedOrder->id); ?></a>
                        — <?php echo e($salesLead->convertedOrder->course?->title ?? '—'); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php if($salesLead->isLost() && $salesLead->lost_reason): ?>
            <div class="rounded-lg border border-rose-200 bg-rose-50/50 p-4 text-sm">
                <p class="font-bold text-rose-900 mb-1">سبب الخسارة</p>
                <p class="text-rose-800 whitespace-pre-wrap"><?php echo e($salesLead->lost_reason); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\sales\leads\show.blade.php ENDPATH**/ ?>