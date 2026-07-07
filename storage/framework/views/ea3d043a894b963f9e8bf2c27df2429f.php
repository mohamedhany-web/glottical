

<?php $__env->startSection('title', 'عميل محتمل #'.$salesLead->id); ?>
<?php $__env->startSection('header', 'CRM — '.$salesLead->name); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-5xl">
    <?php echo $__env->make('partials.crm-admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if(session('success')): ?><div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div><?php endif; ?>
    <?php if($errors->any()): ?><div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm"><?php echo e($errors->first()); ?></div><?php endif; ?>

    <div class="rounded-2xl bg-white border p-6 space-y-4">
        <div class="flex justify-between gap-3">
            <h2 class="text-xl font-black"><?php echo e($salesLead->name); ?></h2>
            <span class="px-3 py-1 rounded-full bg-slate-100 text-sm font-bold"><?php echo e($salesLead->status_label); ?></span>
        </div>
        <dl class="grid sm:grid-cols-2 gap-3 text-sm">
            <div><dt class="text-slate-500">مالك التسويق (ثابت)</dt><dd class="font-bold"><?php echo e($salesLead->marketingOwner?->name ?? '—'); ?></dd></div>
            <div><dt class="text-slate-500">موظف المبيعات</dt><dd class="font-bold"><?php echo e($salesLead->assignedTo?->name ?? '—'); ?></dd></div>
            <div><dt class="text-slate-500">البريد / الهاتف</dt><dd><?php echo e($salesLead->email ?: '—'); ?> / <?php echo e($salesLead->phone ?: '—'); ?></dd></div>
            <div><dt class="text-slate-500">المصدر</dt><dd><?php echo e($salesLead->source_label); ?></dd></div>
            <div><dt class="text-slate-500">كورس الاهتمام</dt><dd><?php echo e($salesLead->interestedCourse?->title ?? '—'); ?></dd></div>
            <div><dt class="text-slate-500">مجموعة الفريق</dt><dd><?php echo e($salesLead->crmGroup?->name ?? '—'); ?></dd></div>
        </dl>
        <?php if($salesLead->notes): ?><div class="rounded-lg bg-slate-50 p-3 text-sm whitespace-pre-wrap"><?php echo e($salesLead->notes); ?></div><?php endif; ?>
    </div>

    <?php if(!$salesLead->isClosed()): ?>
    <div class="rounded-2xl bg-white border p-6 space-y-4">
        <h3 class="font-bold">تعيين لموظف مبيعات</h3>
        <p class="text-sm text-slate-500">بعد التعيين تصبح الحالة «مُعيَّن للمبيعات» — الموظف يبدأ المتابعة من قائمته.</p>
        <form method="POST" action="<?php echo e(route('admin.crm.leads.assign', $salesLead)); ?>" class="grid sm:grid-cols-3 gap-3">
            <?php echo csrf_field(); ?>
            <select name="assigned_to" class="rounded-lg border px-3 py-2 text-sm" required>
                <option value="">اختر موظف مبيعات</option>
                <?php $__currentLoopData = $salesUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($u->id); ?>" <?php if($salesLead->assigned_to==$u->id): echo 'selected'; endif; ?>><?php echo e($u->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <select name="crm_group_id" class="rounded-lg border px-3 py-2 text-sm">
                <option value="">فريق العمل (اختياري)</option>
                <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($g->id); ?>" <?php if($salesLead->crm_group_id==$g->id): echo 'selected'; endif; ?>><?php echo e($g->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button class="px-4 py-2 rounded-xl bg-sky-600 text-white text-sm font-bold">تعيين</button>
        </form>
    </div>

    <div class="rounded-2xl bg-white border p-6 space-y-4">
        <h3 class="font-bold">تغيير الحالة (إدارة)</h3>
        <form method="POST" action="<?php echo e(route('admin.crm.leads.transition', $salesLead)); ?>" class="space-y-3">
            <?php echo csrf_field(); ?>
            <select name="status" class="w-full rounded-lg border px-3 py-2 text-sm" required>
                <?php $__currentLoopData = \App\Models\SalesLead::allowedTransitions()[$salesLead->status] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($st); ?>"><?php echo e(\App\Models\SalesLead::statusLabels()[$st]); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <textarea name="note" rows="2" class="w-full rounded-lg border px-3 py-2 text-sm" placeholder="ملاحظة (اختياري)"></textarea>
            <button class="px-4 py-2 rounded-xl bg-violet-600 text-white text-sm font-bold">تحديث الحالة</button>
        </form>
    </div>
    <?php endif; ?>

    <?php if($salesLead->commissions->isNotEmpty()): ?>
    <div class="rounded-2xl bg-white border p-6">
        <h3 class="font-bold mb-3">العمولات</h3>
        <div class="space-y-2 text-sm">
            <?php $__currentLoopData = $salesLead->commissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex justify-between border-b pb-2">
                    <span><?php echo e($c->user?->name); ?> — <?php echo e($c->typeLabel()); ?></span>
                    <span class="font-bold"><?php echo e(number_format($c->commission_amount_egp, 2)); ?> ج.م (<?php echo e($c->statusLabel()); ?>)</span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="rounded-2xl bg-white border p-6">
        <h3 class="font-bold mb-3">سجل المتابعة</h3>
        <div class="space-y-2 max-h-64 overflow-y-auto text-xs">
            <?php $__empty_1 = true; $__currentLoopData = $salesLead->auditLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="border-b pb-2">
                    <span class="font-bold"><?php echo e($log->actionLabel()); ?></span>
                    <span class="text-slate-500"> — <?php echo e($log->user?->name); ?> — <?php echo e($log->created_at?->format('Y-m-d H:i')); ?></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-slate-500">لا سجلات بعد</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/admin/crm/leads/show.blade.php ENDPATH**/ ?>