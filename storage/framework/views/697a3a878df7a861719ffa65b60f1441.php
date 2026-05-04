<?php $__env->startSection('title', 'طلبات التقديم على الفرصة'); ?>
<?php $__env->startSection('header', 'طلبات التقديم'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm font-medium"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
            <a href="<?php echo e(route('admin.academy-opportunities.recruitment', $opportunity)); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700">
                <i class="fas fa-briefcase"></i> مكتب التوظيف (عروض معتمدة للأكاديمية)
            </a>
            <a href="<?php echo e(route('admin.academy-opportunities.index')); ?>" class="text-sm text-sky-600 font-semibold hover:underline">← فرص الأكاديميات</a>
        </div>
        <h1 class="text-xl font-bold text-slate-900"><?php echo e($opportunity->title); ?></h1>
        <p class="text-sm text-slate-600 mt-1"><?php echo e($opportunity->organization_name); ?></p>
        <form method="GET" class="mt-3">
            <select name="status" class="px-3 py-2 rounded-lg border border-slate-200 text-sm">
                <option value="all" <?php echo e(($status ?? 'all') === 'all' ? 'selected' : ''); ?>>كل الحالات</option>
                <?php $__currentLoopData = ['submitted','reviewing','accepted','rejected']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s); ?>" <?php echo e(($status ?? 'all') === $s ? 'selected' : ''); ?>><?php echo e($s); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button class="px-3 py-2 rounded-lg bg-sky-600 text-white text-sm font-semibold">تصفية</button>
        </form>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs uppercase text-slate-600">
                        <th class="px-4 py-3 text-right">المعلم</th>
                        <th class="px-4 py-3 text-right">Ranking</th>
                        <th class="px-4 py-3 text-right">الرسالة</th>
                        <th class="px-4 py-3 text-right">الحالة الحالية</th>
                        <th class="px-4 py-3 text-right">وقت التقديم</th>
                        <th class="px-4 py-3 text-right">تحديث الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900"><?php echo e($a->user->name ?? '—'); ?></td>
                            <td class="px-4 py-3 text-xs font-bold text-violet-700"><?php echo e((int) ($a->ranking_score ?? 0)); ?></td>
                            <td class="px-4 py-3 text-sm text-slate-700"><?php echo e($a->message ?: '—'); ?></td>
                            <td class="px-4 py-3 text-xs text-slate-700"><?php echo e($a->status); ?></td>
                            <td class="px-4 py-3 text-xs text-slate-500"><?php echo e(optional($a->applied_at ?? $a->created_at)->format('Y-m-d H:i')); ?></td>
                            <td class="px-4 py-3">
                                <form method="POST" action="<?php echo e(route('admin.academy-opportunities.applications.status', [$opportunity, $a])); ?>" class="flex items-center gap-2">
                                    <?php echo csrf_field(); ?>
                                    <select name="status" class="px-2 py-1 rounded border border-slate-200 text-xs">
                                        <?php $__currentLoopData = ['submitted','reviewing','accepted','rejected']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($s); ?>" <?php echo e($a->status === $s ? 'selected' : ''); ?>><?php echo e($s); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <button class="px-3 py-1 rounded bg-sky-600 text-white text-xs font-semibold">حفظ</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">لا توجد طلبات تقديم حتى الآن.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-slate-200"><?php echo e($applications->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\academy-opportunities\applications.blade.php ENDPATH**/ ?>