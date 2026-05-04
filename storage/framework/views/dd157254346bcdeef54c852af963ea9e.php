<?php $__env->startSection('title', 'إشعارات البريد — '.$audienceLabel); ?>
<?php $__env->startSection('header', 'إشعارات البريد (Gmail) — '.$audienceLabel); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.email-broadcasts.index', 'all_users')); ?>" class="px-3 py-2 rounded-lg border <?php echo e($audience === 'all_users' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white border-slate-200 text-slate-700'); ?> text-sm font-bold">كل المستخدمين</a>
            <a href="<?php echo e(route('admin.email-broadcasts.index', 'students')); ?>" class="px-3 py-2 rounded-lg border <?php echo e($audience === 'students' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white border-slate-200 text-slate-700'); ?> text-sm font-bold">الطلاب</a>
            <a href="<?php echo e(route('admin.email-broadcasts.index', 'instructors')); ?>" class="px-3 py-2 rounded-lg border <?php echo e($audience === 'instructors' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white border-slate-200 text-slate-700'); ?> text-sm font-bold">المدربين</a>
            <a href="<?php echo e(route('admin.email-broadcasts.index', 'employees')); ?>" class="px-3 py-2 rounded-lg border <?php echo e($audience === 'employees' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white border-slate-200 text-slate-700'); ?> text-sm font-bold">الموظفين</a>
        </div>
        <a href="<?php echo e(route('admin.email-broadcasts.create', $audience)); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold">
            <i class="fas fa-paper-plane"></i> إرسال بريد جديد
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 font-semibold">
                    <tr>
                        <th class="text-right px-4 py-3">العنوان</th>
                        <th class="text-right px-4 py-3">الحالة</th>
                        <th class="text-right px-4 py-3">الإرسال</th>
                        <th class="text-right px-4 py-3">المنشئ</th>
                        <th class="text-right px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $broadcasts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3 font-medium text-slate-900"><?php echo e($b->subject); ?></td>
                            <td class="px-4 py-3 text-slate-700"><?php echo e($b->status); ?></td>
                            <td class="px-4 py-3 text-xs text-slate-500"><?php echo e($b->sent_at?->format('Y-m-d H:i') ?? '—'); ?></td>
                            <td class="px-4 py-3 text-slate-700"><?php echo e($b->creator?->name ?? '—'); ?></td>
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('admin.email-broadcasts.show', [$audience, $b])); ?>" class="text-blue-700 font-bold hover:underline">تفاصيل</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="px-4 py-12 text-center text-slate-500">لا توجد رسائل بعد.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($broadcasts->hasPages()): ?>
            <div class="px-4 py-3 border-t border-slate-100"><?php echo e($broadcasts->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\email-broadcasts\index.blade.php ENDPATH**/ ?>