

<?php $__env->startSection('title', 'تعديل مجموعة · '.$group->name); ?>
<?php $__env->startSection('page_title', 'تعديل المجموعة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المبيعات · CRM · المجموعات</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]"><?php echo e($group->name); ?></h2>
            <p class="mt-1 text-sm text-muted">
                قائد الفريق: <?php echo e($group->teamLeader?->name ?? '—'); ?>

                · <?php echo e($group->members->where('is_active', true)->count()); ?> عضو
                · <?php echo e($group->leads_count); ?> عميل محتمل
            </p>
        </div>
        <a href="<?php echo e(route('admin.crm.groups.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
            <i class="fas fa-arrow-right text-xs"></i>
            العودة للقائمة
        </a>
    </section>

    <?php if(session('success')): ?>
        <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft" role="status">
            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent"><i class="fas fa-check text-sm"></i></span>
            <p><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800"><?php echo e($errors->first()); ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft xl:col-span-1">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">بيانات المجموعة</h3>
            </div>
            <form method="POST" action="<?php echo e(route('admin.crm.groups.update', $group)); ?>" class="space-y-5 p-4 sm:p-5">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div>
                    <label class="<?php echo e($labelClass); ?>" for="name">اسم المجموعة <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" value="<?php echo e(old('name', $group->name)); ?>" required class="<?php echo e($fieldClass); ?>">
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
                            <option value="<?php echo e($l->id); ?>" <?php if((string) old('team_leader_id', $group->team_leader_id) === (string) $l->id): echo 'selected'; endif; ?>><?php echo e($l->name); ?></option>
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
                <label class="inline-flex items-center gap-2 text-sm text-ink">
                    <input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $group->is_active)): echo 'checked'; endif; ?> class="rounded border-line text-accent focus:ring-accent/20">
                    المجموعة نشطة
                </label>
                <button type="submit" class="btn-press inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                    <i class="fas fa-save text-xs"></i>
                    حفظ التعديلات
                </button>
            </form>
        </article>

        <div class="space-y-5 xl:col-span-2">
            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">إضافة عضو</h3>
                    <p class="mt-0.5 text-xs text-muted">أضف موظفي التسويق والمبيعات لربط العملاء بالمجموعة</p>
                </div>
                <form method="POST" action="<?php echo e(route('admin.crm.groups.members.store', $group)); ?>" class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-3 sm:items-end sm:p-5">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label class="<?php echo e($labelClass); ?>" for="user_id">الموظف</label>
                        <select name="user_id" id="user_id" required class="<?php echo e($fieldClass); ?>">
                            <option value="">اختر موظفاً</option>
                            <optgroup label="تسويق">
                                <?php $__currentLoopData = $marketingUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </optgroup>
                            <optgroup label="مبيعات">
                                <?php $__currentLoopData = $salesUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </optgroup>
                        </select>
                    </div>
                    <div>
                        <label class="<?php echo e($labelClass); ?>" for="role">الدور</label>
                        <select name="role" id="role" class="<?php echo e($fieldClass); ?>">
                            <option value="marketing">تسويق</option>
                            <option value="sales">مبيعات</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-press inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                        <i class="fas fa-user-plus text-xs"></i>
                        إضافة
                    </button>
                </form>
            </article>

            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">أعضاء المجموعة</h3>
                </div>
                <div class="divide-y divide-line">
                    <?php $__empty_1 = true; $__currentLoopData = $group->members->where('is_active', true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-4 sm:px-5">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="inline-flex size-10 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-sm font-semibold text-accent">
                                    <?php echo e(mb_substr($m->user?->name ?? '?', 0, 1)); ?>

                                </div>
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-ink"><?php echo e($m->user?->name ?? '—'); ?></p>
                                    <p class="text-xs text-muted"><?php echo e($m->role === 'marketing' ? 'تسويق' : ($m->role === 'sales' ? 'مبيعات' : $m->role)); ?></p>
                                </div>
                            </div>
                            <form method="POST" action="<?php echo e(route('admin.crm.groups.members.destroy', [$group, $m])); ?>" onsubmit="return confirm('إزالة هذا العضو من المجموعة؟')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn-press inline-flex h-8 items-center rounded-lg border border-line px-3 text-xs font-medium text-rose-600 hover:border-rose-300 hover:bg-rose-50">
                                    إزالة
                                </button>
                            </form>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-4 py-12 text-center text-sm text-muted sm:px-5">
                            لا يوجد أعضاء بعد — أضف موظفين من النموذج أعلاه.
                        </div>
                    <?php endif; ?>
                </div>
            </article>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\crm\groups\edit.blade.php ENDPATH**/ ?>