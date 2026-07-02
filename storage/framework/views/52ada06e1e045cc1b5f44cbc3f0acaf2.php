

<?php $__env->startSection('title', 'مجموعات CRM'); ?>
<?php $__env->startSection('header', 'CRM — المجموعات'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
    <div class="flex justify-between">
        <a href="<?php echo e(route('admin.crm.dashboard')); ?>" class="text-sm text-sky-600 font-semibold">← لوحة CRM</a>
        <a href="<?php echo e(route('admin.crm.groups.create')); ?>" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-bold">مجموعة جديدة</a>
    </div>
    <div class="grid gap-4">
        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-2xl bg-white border p-5">
                <div class="flex justify-between">
                    <div>
                        <h3 class="font-bold text-lg"><?php echo e($group->name); ?></h3>
                        <p class="text-sm text-slate-500">قائد الفريق: <?php echo e($group->teamLeader?->name ?? '—'); ?> — <?php echo e($group->leads_count); ?> Lead</p>
                    </div>
                    <a href="<?php echo e(route('admin.crm.groups.edit', $group)); ?>" class="text-sky-600 text-sm font-bold">إدارة</a>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\crm\groups\index.blade.php ENDPATH**/ ?>