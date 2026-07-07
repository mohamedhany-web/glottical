

<?php $__env->startSection('title', 'مجموعة CRM'); ?>
<?php $__env->startSection('header', 'CRM — مجموعة جديدة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4 max-w-lg">
    <?php echo $__env->make('partials.crm-admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<form method="POST" action="<?php echo e(route('admin.crm.groups.store')); ?>" class="space-y-4 rounded-2xl bg-white border p-6">
    <?php echo csrf_field(); ?>
    <div>
        <label class="block text-sm font-semibold mb-1">اسم المجموعة</label>
        <input name="name" class="w-full rounded-lg border px-3 py-2" required>
    </div>
    <div>
        <label class="block text-sm font-semibold mb-1">قائد الفريق</label>
        <select name="team_leader_id" class="w-full rounded-lg border px-3 py-2">
            <option value="">—</option>
            <?php $__currentLoopData = $leaders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($l->id); ?>"><?php echo e($l->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <button class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-bold">حفظ</button>
</form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/admin/crm/groups/create.blade.php ENDPATH**/ ?>