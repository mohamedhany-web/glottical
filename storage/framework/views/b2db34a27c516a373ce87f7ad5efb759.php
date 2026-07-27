<?php $__env->startSection('title', 'عرض المستخدم - ' . $user->name); ?>
<?php $__env->startSection('page_title', 'عرض المستخدم'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $roles = [
        'super_admin' => ['label' => 'مدير عام', 'badge' => 'bg-danger/10 text-danger'],
        'admin' => ['label' => 'إداري', 'badge' => 'bg-accent-soft text-accent'],
        'instructor' => ['label' => 'مدرب', 'badge' => 'bg-metal/15 text-metal'],
        'teacher' => ['label' => 'مدرس', 'badge' => 'bg-metal/15 text-metal'],
        'student' => ['label' => __('admin.student_role_label'), 'badge' => 'bg-accent-soft text-accent'],
        'parent' => ['label' => 'ولي أمر', 'badge' => 'bg-canvas-muted text-muted'],
        'employee' => ['label' => 'موظف', 'badge' => 'bg-metal/15 text-metal'],
    ];
    $roleKey = $user->is_employee ? 'employee' : $user->role;
    $roleMeta = $roles[$roleKey] ?? $roles['student'];
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
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]"><?php echo e($user->name); ?></h2>
            <p class="mt-1 text-sm text-muted">عضوية #<?php echo e(str_pad($user->id, 5, '0', STR_PAD_LEFT)); ?> · تفاصيل الحساب والحالة</p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                <i class="fas fa-pen text-xs"></i>
                تعديل
            </a>
            <a href="<?php echo e($listRoute); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                رجوع للقائمة
            </a>
        </div>
    </section>

    <?php if(session('success')): ?>
        <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft" role="status">
            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent"><i class="fas fa-check text-sm"></i></span>
            <p><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?>

    <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl <?php echo e($roleMeta['badge']); ?>">
                <i class="fas fa-user-shield text-sm"></i>
            </div>
            <p class="mt-3 text-xs text-muted">الدور</p>
            <p class="mt-1">
                <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-semibold <?php echo e($roleMeta['badge']); ?>"><?php echo e($roleMeta['label']); ?></span>
            </p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl <?php echo e($user->is_active ? 'bg-accent-soft text-accent' : 'bg-canvas-muted text-muted'); ?>">
                <i class="fas fa-<?php echo e($user->is_active ? 'user-check' : 'user-slash'); ?> text-sm"></i>
            </div>
            <p class="mt-3 text-xs text-muted">حالة الحساب</p>
            <p class="mt-1">
                <?php if($user->is_active): ?>
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-semibold text-accent">
                        <span class="size-1.5 rounded-full bg-accent"></span>
                        نشط
                    </span>
                <?php else: ?>
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-canvas-muted px-2.5 py-1 text-xs font-semibold text-muted">
                        <span class="size-1.5 rounded-full bg-muted"></span>
                        غير نشط
                    </span>
                <?php endif; ?>
            </p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-accent-soft text-accent">
                <i class="fas fa-calendar-plus text-sm"></i>
            </div>
            <p class="mt-3 text-xs text-muted">تاريخ التسجيل</p>
            <p class="mt-1 text-sm font-semibold tabular-nums text-ink"><?php echo e($user->created_at ? $user->created_at->format('Y-m-d H:i') : '—'); ?></p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-metal/15 text-metal">
                <i class="fas fa-clock text-sm"></i>
            </div>
            <p class="mt-3 text-xs text-muted">آخر تسجيل دخول</p>
            <p class="mt-1 text-sm font-semibold tabular-nums text-ink"><?php echo e($user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : '—'); ?></p>
        </article>
    </section>

    <div class="grid gap-5 lg:grid-cols-5">
        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft lg:col-span-3">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">البيانات الأساسية</h3>
                <p class="mt-0.5 text-xs text-muted">الهوية وبيانات التواصل</p>
            </div>
            <div class="p-4 sm:p-5">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-start">
                    <div class="shrink-0">
                        <?php if($user->profile_image): ?>
                            <img src="<?php echo e($user->profile_image_url); ?>" alt="<?php echo e($user->name); ?>" class="size-24 rounded-2xl border border-line object-cover">
                        <?php else: ?>
                            <span class="inline-flex size-24 items-center justify-center rounded-2xl bg-accent-soft text-3xl font-semibold text-accent">
                                <?php echo e(mb_substr($user->name, 0, 1, 'UTF-8')); ?>

                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="min-w-0 flex-1 space-y-3">
                        <div class="rounded-xl border border-line bg-canvas/60 p-4">
                            <p class="text-xs font-medium text-muted">الاسم</p>
                            <p class="mt-1 text-sm font-semibold text-ink"><?php echo e($user->name); ?></p>
                        </div>
                        <div class="rounded-xl border border-line bg-canvas/60 p-4">
                            <p class="text-xs font-medium text-muted">البريد الإلكتروني</p>
                            <p class="mt-1 break-all text-sm font-semibold text-ink"><?php echo e($user->email ?: '—'); ?></p>
                        </div>
                        <div class="rounded-xl border border-line bg-canvas/60 p-4">
                            <p class="text-xs font-medium text-muted">رقم الهاتف</p>
                            <p class="mt-1 text-sm font-semibold text-ink" dir="ltr"><?php echo e($user->phone ?: '—'); ?></p>
                        </div>
                    </div>
                </div>
                <?php if($user->bio): ?>
                    <div class="mt-5 border-t border-line pt-5">
                        <p class="text-xs font-medium text-muted">النبذة التعريفية</p>
                        <div class="mt-2 rounded-xl border border-line bg-canvas/60 p-4">
                            <p class="whitespace-pre-wrap text-sm leading-7 text-ink"><?php echo e($user->bio); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft lg:col-span-2">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">إجراءات سريعة</h3>
                <p class="mt-0.5 text-xs text-muted">تعديل أو العودة للقائمة</p>
            </div>
            <div class="space-y-2 p-4 sm:p-5">
                <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>" class="btn-press inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                    <i class="fas fa-pen text-xs"></i>
                    تعديل بيانات المستخدم
                </a>
                <a href="<?php echo e($listRoute); ?>" class="btn-press inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                    <i class="fas fa-arrow-right text-xs"></i>
                    <?php echo e($listLabel); ?>

                </a>
            </div>
        </article>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\users\show.blade.php ENDPATH**/ ?>