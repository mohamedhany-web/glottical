<?php $__env->startSection('title', 'الملف الشخصي - Glottical'); ?>
<?php $__env->startSection('page_title', 'الملف الشخصي'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $roleLabels = [
        'admin' => 'إداري',
        'super_admin' => 'مدير عام',
    ];
    $roleLabel = $roleLabels[$user->role] ?? 'إداري';
    $memberSince = $user->created_at ? $user->created_at->copy()->locale('ar')->translatedFormat('d F Y') : '—';
    $lastLogin = $user->last_login_at ? $user->last_login_at->copy()->locale('ar')->diffForHumans() : '—';
    $inputClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-2 block text-sm font-medium text-ink';
?>

<div class="space-y-5">
    <?php if(session('recovery_codes')): ?>
        <div class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
            <div class="mb-3 flex items-center gap-3">
                <span class="inline-flex size-9 items-center justify-center rounded-xl bg-metal/15 text-metal"><i class="fas fa-key text-sm"></i></span>
                <div>
                    <h3 class="text-sm font-semibold text-ink">رموز الاسترداد — احفظها في مكان آمن</h3>
                    <p class="mt-0.5 text-xs text-muted">كل رمز يُستخدم مرة واحدة فقط عند فقدان جهاز المصادقة.</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 font-mono text-sm sm:grid-cols-4">
                <?php $__currentLoopData = session('recovery_codes'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="rounded-xl border border-line bg-[#f7f8fa] px-3 py-2 text-ink"><?php echo e($code); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php session()->forget('recovery_codes'); ?>
        </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
        <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft" role="status">
            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent"><i class="fas fa-check text-sm"></i></span>
            <p><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?>

    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">حسابك · البيانات والأمان</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">الملف الشخصي</h2>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink hover:bg-canvas">
                <i class="fas fa-arrow-right text-xs"></i>
                لوحة التحكم
            </a>
            <?php if(! $user->hasTwoFactorEnabled()): ?>
                <a href="<?php echo e(route('two-factor.setup')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                    <i class="fas fa-shield-alt text-xs"></i>
                    تفعيل 2FA
                </a>
            <?php endif; ?>
        </div>
    </section>

    
    <section class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="flex flex-col gap-5 border-b border-line px-4 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <div class="flex items-center gap-4">
                <div class="flex size-14 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-accent-soft text-xl font-semibold text-accent">
                    <?php if($user->profile_image): ?>
                        <img src="<?php echo e($user->profile_image_url); ?>" alt="" class="size-full object-cover" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                        <span class="hidden"><?php echo e(mb_substr($user->name, 0, 1)); ?></span>
                    <?php else: ?>
                        <span><?php echo e(mb_substr($user->name, 0, 1)); ?></span>
                    <?php endif; ?>
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="truncate text-base font-semibold text-ink"><?php echo e($user->name); ?></h3>
                        <span class="rounded-lg bg-accent-soft px-2 py-0.5 text-[10px] font-medium text-accent"><?php echo e($roleLabel); ?></span>
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-canvas-muted px-2 py-0.5 text-[10px] font-medium <?php echo e($user->is_active ? 'text-success' : 'text-danger'); ?>">
                            <span class="size-1.5 rounded-full <?php echo e($user->is_active ? 'bg-success' : 'bg-danger'); ?>"></span>
                            <?php echo e($user->is_active ? 'نشط' : 'غير نشط'); ?>

                        </span>
                    </div>
                    <p class="mt-1 text-xs text-muted">
                        <?php if($user->email): ?><?php echo e($user->email); ?><?php endif; ?>
                        <?php if($user->email && $user->phone): ?> · <?php endif; ?>
                        <?php if($user->phone): ?><?php echo e($user->phone); ?><?php endif; ?>
                    </p>
                </div>
            </div>
            <span class="inline-flex w-fit items-center gap-2 rounded-xl border border-line bg-[#f7f8fa] px-3 py-1.5 text-xs font-medium text-muted">
                <i class="fas fa-lock text-accent text-[10px]"></i>
                بياناتك مشفرة وآمنة
            </span>
        </div>
        <div class="admin-kpi-grid grid gap-3 p-4 sm:grid-cols-3 sm:p-5">
            <article class="rounded-2xl border border-line bg-[#f7f8fa] p-4">
                <div class="inline-flex size-9 items-center justify-center rounded-xl bg-accent-soft text-accent">
                    <i class="fas fa-calendar-alt text-sm"></i>
                </div>
                <p class="mt-3 text-xs text-muted">تاريخ الانضمام</p>
                <p class="mt-1 text-sm font-semibold tracking-tight text-ink"><?php echo e($memberSince); ?></p>
            </article>
            <article class="rounded-2xl border border-line bg-[#f7f8fa] p-4">
                <div class="inline-flex size-9 items-center justify-center rounded-xl bg-accent-soft text-accent">
                    <i class="fas fa-id-badge text-sm"></i>
                </div>
                <p class="mt-3 text-xs text-muted">رقم العضوية</p>
                <p class="mt-1 text-sm font-semibold tabular-nums tracking-tight text-ink">#<?php echo e(str_pad($user->id, 5, '0', STR_PAD_LEFT)); ?></p>
            </article>
            <article class="rounded-2xl border border-line bg-[#f7f8fa] p-4">
                <div class="inline-flex size-9 items-center justify-center rounded-xl bg-metal/15 text-metal">
                    <i class="fas fa-clock text-sm"></i>
                </div>
                <p class="mt-3 text-xs text-muted">آخر تسجيل دخول</p>
                <p class="mt-1 text-sm font-semibold tracking-tight text-ink"><?php echo e($lastLogin); ?></p>
            </article>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        <div class="space-y-5">
            <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <div class="mb-4 flex items-center gap-3">
                    <span class="inline-flex size-9 items-center justify-center rounded-xl bg-accent-soft text-accent"><i class="fas fa-info-circle text-sm"></i></span>
                    <div>
                        <h3 class="text-base font-semibold text-ink">معلومات الحساب</h3>
                        <p class="mt-0.5 text-xs text-muted">ملخص سريع لحالة حسابك</p>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between gap-3 rounded-xl border border-line bg-[#f7f8fa] px-3 py-2.5">
                        <span class="text-muted">نوع الحساب</span>
                        <span class="rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-semibold text-accent"><?php echo e($roleLabel); ?></span>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-xl border border-line bg-[#f7f8fa] px-3 py-2.5">
                        <span class="text-muted">الحالة</span>
                        <span class="inline-flex items-center gap-2 text-xs font-semibold <?php echo e($user->is_active ? 'text-success' : 'text-danger'); ?>">
                            <span class="size-2 rounded-full <?php echo e($user->is_active ? 'bg-success' : 'bg-danger'); ?>"></span>
                            <?php echo e($user->is_active ? 'نشط' : 'غير نشط'); ?>

                        </span>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-xl border border-line bg-[#f7f8fa] px-3 py-2.5">
                        <span class="text-muted">المصادقة الثنائية</span>
                        <span class="text-xs font-semibold <?php echo e($user->hasTwoFactorEnabled() ? 'text-success' : 'text-muted'); ?>">
                            <?php echo e($user->hasTwoFactorEnabled() ? 'مفعّلة' : 'غير مفعّلة'); ?>

                        </span>
                    </div>
                </div>
            </article>

            <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <div class="mb-4 flex items-center gap-3">
                    <span class="inline-flex size-9 items-center justify-center rounded-xl bg-accent-soft text-accent"><i class="fas fa-shield-alt text-sm"></i></span>
                    <div>
                        <h3 class="text-base font-semibold text-ink">المصادقة الثنائية</h3>
                        <p class="mt-0.5 text-xs text-muted">طبقة أمان إضافية لتسجيل الدخول</p>
                    </div>
                </div>
                <?php if($user->hasTwoFactorEnabled()): ?>
                    <p class="mb-4 text-sm leading-6 text-muted">مفعّلة — يتم طلب رمز التحقق عند كل تسجيل دخول.</p>
                    <form action="<?php echo e(route('two-factor.disable')); ?>" method="POST" class="space-y-3" onsubmit="return confirm('هل تريد تعطيل المصادقة الثنائية؟ ستحتاج إدخال كلمة المرور.');">
                        <?php echo csrf_field(); ?>
                        <input type="password" name="password" required placeholder="كلمة المرور للتأكيد" class="<?php echo e($inputClass); ?>">
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-xs font-medium text-danger"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <button type="submit" class="btn-press inline-flex h-10 w-full items-center justify-center rounded-xl border border-danger/30 bg-danger/10 text-sm font-medium text-danger transition hover:bg-danger/15">
                            تعطيل المصادقة الثنائية
                        </button>
                    </form>
                <?php else: ?>
                    <p class="mb-4 text-sm leading-6 text-muted">تفعيل المصادقة الثنائية يزيد أمان دخولك للمنصة.</p>
                    <a href="<?php echo e(route('two-factor.setup')); ?>" class="btn-press inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-accent text-sm font-medium text-white">
                        <i class="fas fa-mobile-alt text-xs"></i>
                        تفعيل المصادقة الثنائية
                    </a>
                <?php endif; ?>
            </article>
        </div>

        <div class="lg:col-span-2">
            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold text-ink">تحديث البيانات الأساسية</h3>
                        <p class="mt-0.5 text-xs text-muted">راجع معلوماتك وحدّثها في أي وقت</p>
                    </div>
                </div>

                <form method="POST" action="<?php echo e(route('admin.profile.update')); ?>" class="space-y-5 p-4 sm:p-5" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label class="<?php echo e($labelClass); ?>" for="name">الاسم الكامل</label>
                            <input id="name" type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>" required class="<?php echo e($inputClass); ?>">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1.5 text-xs font-medium text-danger"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="<?php echo e($labelClass); ?>" for="phone">رقم الهاتف</label>
                            <input id="phone" type="text" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>" required class="<?php echo e($inputClass); ?>">
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1.5 text-xs font-medium text-danger"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="md:col-span-2">
                            <label class="<?php echo e($labelClass); ?>" for="email">البريد الإلكتروني <span class="font-normal text-muted">(اختياري)</span></label>
                            <input id="email" type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" class="<?php echo e($inputClass); ?>">
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1.5 text-xs font-medium text-danger"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div>
                        <label class="<?php echo e($labelClass); ?>">صورة الملف الشخصي</label>
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                            <div class="flex size-24 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-dashed border-line bg-[#f7f8fa]">
                                <?php if($user->profile_image): ?>
                                    <img src="<?php echo e($user->profile_image_url); ?>" alt="" class="size-full object-cover" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                                    <i class="fas fa-camera hidden text-2xl text-muted"></i>
                                <?php else: ?>
                                    <i class="fas fa-camera text-2xl text-muted"></i>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-dashed border-line bg-[#f7f8fa] px-5 py-3 text-sm font-medium text-ink-soft transition hover:border-accent/40 hover:bg-accent-soft hover:text-accent">
                                    <i class="fas fa-upload"></i>
                                    <span>اختر صورة جديدة (PNG أو JPG)</span>
                                    <input type="file" name="profile_image" accept="image/*" class="hidden">
                                </label>
                                <?php $__errorArgs = ['profile_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1.5 text-xs font-medium text-danger"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 rounded-2xl border border-line bg-[#f7f8fa] p-4 sm:p-5">
                        <div>
                            <h4 class="text-sm font-semibold text-ink">تغيير كلمة المرور</h4>
                            <p class="mt-1 text-xs text-muted">اترك الحقول فارغة إذا لم ترغب في التغيير</p>
                        </div>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-muted" for="current_password">كلمة المرور الحالية</label>
                                <input id="current_password" type="password" name="current_password" class="<?php echo e($inputClass); ?>">
                                <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1.5 text-xs font-medium text-danger"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-muted" for="password">كلمة المرور الجديدة</label>
                                <input id="password" type="password" name="password" class="<?php echo e($inputClass); ?>">
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1.5 text-xs font-medium text-danger"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-muted" for="password_confirmation">تأكيد كلمة المرور</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" class="<?php echo e($inputClass); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 border-t border-line pt-5 sm:flex-row sm:items-center sm:justify-between">
                        <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn-press order-2 inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-line bg-surface px-5 text-sm font-medium text-ink hover:bg-canvas sm:order-1">
                            <i class="fas fa-arrow-right text-xs"></i>
                            رجوع للوحة التحكم
                        </a>
                        <button type="submit" class="btn-press order-1 inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-accent px-6 text-sm font-medium text-white sm:order-2">
                            <i class="fas fa-save text-xs"></i>
                            حفظ التعديلات
                        </button>
                    </div>
                </form>
            </article>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\profile\index.blade.php ENDPATH**/ ?>