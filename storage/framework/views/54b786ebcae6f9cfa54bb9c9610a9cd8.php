

<?php $__env->startSection('title', 'مجموعة CRM جديدة'); ?>
<?php $__env->startSection('page_title', 'مجموعة جديدة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المبيعات · CRM · المجموعات</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">مجموعة جديدة</h2>
            <p class="mt-1 text-sm text-muted">أنشئ مجموعة ثم أضف الأعضاء من صفحة الإدارة</p>
        </div>
        <a href="<?php echo e(route('admin.crm.groups.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
            <i class="fas fa-arrow-right text-xs"></i>
            العودة
        </a>
    </section>

    <?php if($errors->any()): ?>
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800"><?php echo e($errors->first()); ?></div>
    <?php endif; ?>

    <article class="max-w-xl overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <form method="POST" action="<?php echo e(route('admin.crm.groups.store')); ?>" class="space-y-5 p-5 sm:p-6">
            <?php echo csrf_field(); ?>
            <div>
                <label class="<?php echo e($labelClass); ?>" for="name">اسم المجموعة <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" required class="<?php echo e($fieldClass); ?>" placeholder="مثال: فريق القاهرة">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="<?php echo e($labelClass); ?>" for="team_leader_id">قائد الفريق</label>
                <select name="team_leader_id" id="team_leader_id" class="<?php echo e($fieldClass); ?>">
                    <option value="">— اختياري —</option>
                    <?php $__currentLoopData = $leaders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($l->id); ?>" <?php if((string) old('team_leader_id') === (string) $l->id): echo 'selected'; endif; ?>><?php echo e($l->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['team_leader_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="flex flex-wrap gap-2 border-t border-line pt-5">
                <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                    <i class="fas fa-save text-xs"></i>
                    حفظ
                </button>
                <a href="<?php echo e(route('admin.crm.groups.index')); ?>" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line px-5 text-sm font-medium text-ink hover:bg-canvas">إلغاء</a>
            </div>
        </form>
    </article>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\crm\groups\create.blade.php ENDPATH**/ ?>