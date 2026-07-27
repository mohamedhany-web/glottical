<?php $__env->startSection('title', 'تعديل المستخدم - ' . $user->name); ?>
<?php $__env->startSection('page_title', 'تعديل المستخدم'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $listRoute = ($user->role === 'student' && Route::has('admin.students-accounts.index'))
        ? route('admin.students-accounts.index')
        : route('admin.users.index');
    $listLabel = ($user->role === 'student' && Route::has('admin.students-accounts.index'))
        ? 'إدارة الطلاب والحسابات'
        : 'إدارة المستخدمين';
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">الحسابات · <?php echo e($listLabel); ?></p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">تعديل بيانات المستخدم</h2>
            <p class="mt-1 text-sm text-muted"><?php echo e($user->name); ?> · تحديث الاسم، التواصل، الدور وحالة الحساب</p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.users.show', $user->id)); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-eye text-xs"></i>
                عرض
            </a>
            <a href="<?php echo e($listRoute); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                رجوع للقائمة
            </a>
        </div>
    </section>

    <?php if(session('success') || request('updated') == '1'): ?>
        <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft" role="status">
            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent"><i class="fas fa-check text-sm"></i></span>
            <p><?php echo e(session('success', 'تم تحديث بيانات المستخدم بنجاح')); ?></p>
        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="rounded-2xl border border-danger/20 bg-danger/5 p-4 text-sm text-danger shadow-soft">
            <p class="mb-2 font-semibold">يرجى تصحيح ما يلي:</p>
            <ul class="list-inside list-disc space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($err); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('admin.users.update', $user->id)); ?>" id="editUserForm" class="space-y-5">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="grid gap-5 lg:grid-cols-3">
            <div class="space-y-5 lg:col-span-2">
                <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                    <div class="border-b border-line px-4 py-4 sm:px-5">
                        <h3 class="text-base font-semibold text-ink">المعلومات الأساسية</h3>
                        <p class="mt-0.5 text-xs text-muted">الاسم، البريد، رقم الهاتف</p>
                    </div>
                    <div class="grid grid-cols-1 gap-5 p-4 sm:p-5 md:grid-cols-2">
                        <div>
                            <label for="name" class="<?php echo e($labelClass); ?>">الاسم الكامل <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="<?php echo e(old('name', $user->name)); ?>" required maxlength="255" class="<?php echo e($fieldClass); ?>" />
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs font-medium text-danger"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="phone" class="<?php echo e($labelClass); ?>">رقم الهاتف <span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="phone" value="<?php echo e(old('phone', $user->phone)); ?>" required class="<?php echo e($fieldClass); ?>" dir="ltr" />
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs font-medium text-danger"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="md:col-span-2">
                            <label for="email" class="<?php echo e($labelClass); ?>">البريد الإلكتروني</label>
                            <input type="email" name="email" id="email" value="<?php echo e(old('email', $user->email)); ?>" maxlength="255" class="<?php echo e($fieldClass); ?>" />
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs font-medium text-danger"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="md:col-span-2">
                            <label for="password" class="<?php echo e($labelClass); ?>">كلمة المرور الجديدة <span class="font-normal text-muted">(اختياري)</span></label>
                            <input type="password" name="password" id="password" minlength="8" class="<?php echo e($fieldClass); ?>" placeholder="اتركه فارغاً إذا لم ترغب بتغيير كلمة المرور" />
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs font-medium text-danger"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </article>

                <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                    <div class="border-b border-line px-4 py-4 sm:px-5">
                        <h3 class="text-base font-semibold text-ink">نبذة تعريفية</h3>
                        <p class="mt-0.5 text-xs text-muted">اختياري · الحد الأقصى 1000 حرف</p>
                    </div>
                    <div class="p-4 sm:p-5">
                        <label for="bio" class="sr-only">نبذة تعريفية</label>
                        <textarea name="bio" id="bio" rows="4" maxlength="1000" class="<?php echo e($areaClass); ?>"><?php echo e(old('bio', $user->bio ?? '')); ?></textarea>
                        <?php $__errorArgs = ['bio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs font-medium text-danger"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </article>
            </div>

            <div class="space-y-5">
                <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                    <div class="border-b border-line px-4 py-4 sm:px-5">
                        <h3 class="text-base font-semibold text-ink">الدور والحالة</h3>
                        <p class="mt-0.5 text-xs text-muted">مستوى الوصول وحالة الحساب</p>
                    </div>
                    <div class="space-y-5 p-4 sm:p-5">
                        <div>
                            <label for="role" class="<?php echo e($labelClass); ?>">الدور <span class="text-danger">*</span></label>
                            <select name="role" id="role" required class="<?php echo e($fieldClass); ?> cursor-pointer">
                                <option value="super_admin" <?php echo e(old('role', $user->is_employee ? 'employee' : $user->role) == 'super_admin' ? 'selected' : ''); ?>>مدير عام</option>
                                <option value="admin" <?php echo e(old('role', $user->role) == 'admin' ? 'selected' : ''); ?>>إداري</option>
                                <option value="instructor" <?php echo e(old('role', $user->role) == 'instructor' ? 'selected' : ''); ?>>مدرب</option>
                                <option value="teacher" <?php echo e(old('role', $user->role) == 'teacher' ? 'selected' : ''); ?>>مدرس</option>
                                <option value="student" <?php echo e(old('role', $user->role) == 'student' ? 'selected' : ''); ?>><?php echo e(__('admin.student_role_label')); ?></option>
                                <option value="parent" <?php echo e(old('role', $user->role) == 'parent' ? 'selected' : ''); ?>>ولي أمر</option>
                                <option value="employee" <?php echo e(old('role', $user->is_employee ? 'employee' : $user->role) == 'employee' ? 'selected' : ''); ?>>موظف</option>
                            </select>
                            <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs font-medium text-danger"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="is_active" class="<?php echo e($labelClass); ?>">حالة الحساب <span class="text-danger">*</span></label>
                            <select name="is_active" id="is_active" required class="<?php echo e($fieldClass); ?> cursor-pointer">
                                <option value="1" <?php echo e(old('is_active', ($user->is_active ?? true) ? '1' : '0') == '1' ? 'selected' : ''); ?>>نشط</option>
                                <option value="0" <?php echo e(old('is_active', ($user->is_active ?? true) ? '1' : '0') == '0' ? 'selected' : ''); ?>>غير نشط</option>
                            </select>
                            <?php $__errorArgs = ['is_active'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs font-medium text-danger"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </article>

                <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                    <div class="space-y-2 p-4 sm:p-5">
                        <button type="submit" class="btn-press inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                            <i class="fas fa-save text-xs"></i>
                            حفظ التعديلات
                        </button>
                        <a href="<?php echo e(route('admin.users.show', $user->id)); ?>" class="btn-press inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                            <i class="fas fa-times text-xs"></i>
                            إلغاء
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\users\edit.blade.php ENDPATH**/ ?>