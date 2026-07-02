

<?php $__env->startSection('title', 'تعديل مجموعة'); ?>
<?php $__env->startSection('header', 'CRM — '.$group->name); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-3xl">
    <?php if(session('success')): ?><div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div><?php endif; ?>

    <form method="POST" action="<?php echo e(route('admin.crm.groups.update', $group)); ?>" class="rounded-2xl bg-white border p-6 space-y-4">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <input name="name" value="<?php echo e($group->name); ?>" class="w-full rounded-lg border px-3 py-2" required>
        <select name="team_leader_id" class="w-full rounded-lg border px-3 py-2">
            <option value="">قائد الفريق</option>
            <?php $__currentLoopData = $leaders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($l->id); ?>" <?php if($group->team_leader_id==$l->id): echo 'selected'; endif; ?>><?php echo e($l->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" <?php if($group->is_active): echo 'checked'; endif; ?>> نشطة</label>
        <button class="px-5 py-2 rounded-xl bg-indigo-600 text-white font-bold">حفظ</button>
    </form>

    <div class="rounded-2xl bg-white border p-6 space-y-4">
        <h3 class="font-bold">إضافة عضو</h3>
        <form method="POST" action="<?php echo e(route('admin.crm.groups.members.store', $group)); ?>" class="grid sm:grid-cols-3 gap-3">
            <?php echo csrf_field(); ?>
            <select name="user_id" class="rounded-lg border px-3 py-2 text-sm" required>
                <optgroup label="تسويق">
                    <?php $__currentLoopData = $marketingUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </optgroup>
                <optgroup label="مبيعات">
                    <?php $__currentLoopData = $salesUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </optgroup>
            </select>
            <select name="role" class="rounded-lg border px-3 py-2 text-sm">
                <option value="marketing">تسويق</option>
                <option value="sales">مبيعات</option>
            </select>
            <button class="px-4 py-2 rounded-xl bg-sky-600 text-white text-sm font-bold">إضافة</button>
        </form>
        <ul class="divide-y text-sm">
            <?php $__currentLoopData = $group->members->where('is_active', true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="flex justify-between py-2">
                    <span><?php echo e($m->user?->name); ?> (<?php echo e($m->role); ?>)</span>
                    <form method="POST" action="<?php echo e(route('admin.crm.groups.members.destroy', [$group, $m])); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button class="text-rose-600 text-xs font-bold">إلغاء</button>
                    </form>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\crm\groups\edit.blade.php ENDPATH**/ ?>