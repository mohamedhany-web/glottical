<?php $__env->startSection('title', 'الملف الشخصي - لوحة الإدارة'); ?>
<?php $__env->startSection('header', 'الملف الشخصي'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $roleLabels = [
        'admin' => ['label' => 'إداري', 'color' => 'from-indigo-500 to-violet-600', 'chip' => 'bg-indigo-50 text-indigo-700 border border-indigo-200'],
        'super_admin' => ['label' => 'مدير عام', 'color' => 'from-blue-600 to-indigo-700', 'chip' => 'bg-blue-50 text-blue-700 border border-blue-200'],
    ];
    $roleMeta = $roleLabels[$user->role] ?? ['label' => 'إداري', 'color' => 'from-slate-500 to-slate-600', 'chip' => 'bg-slate-100 text-slate-700 border border-slate-200'];

    $memberSince = $user->created_at ? $user->created_at->copy()->locale('ar')->translatedFormat('d F Y') : '—';
    $lastLogin = $user->last_login_at ? $user->last_login_at->copy()->locale('ar')->diffForHumans() : '—';
?>

<div class="space-y-6 sm:space-y-8">
    
    <?php if(session('recovery_codes')): ?>
        <div class="rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-white p-6 shadow-sm">
            <h3 class="font-heading font-bold text-amber-900 mb-2 flex items-center gap-2">
                <span class="w-9 h-9 rounded-xl bg-amber-500/20 flex items-center justify-center"><i class="fas fa-key text-amber-600"></i></span>
                رموز الاسترداد — احفظها في مكان آمن
            </h3>
            <p class="text-sm text-amber-800/90 mb-4">استخدم أحد هذه الرموز للدخول إذا لم يكن معك جهاز المصادقة. كل رمز يُستخدم مرة واحدة فقط.</p>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 font-mono text-sm">
                <?php $__currentLoopData = session('recovery_codes'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="bg-white px-3 py-2 rounded-xl border border-amber-200 text-amber-900"><?php echo e($code); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php session()->forget('recovery_codes'); ?>
        </div>
    <?php endif; ?>

    
    <?php if(session('success')): ?>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/80 px-5 py-4 flex items-center gap-3 shadow-sm">
            <span class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center text-white flex-shrink-0"><i class="fas fa-check"></i></span>
            <p class="font-semibold text-emerald-800"><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?>

    
    <div class="rounded-3xl border border-slate-200/80 bg-white overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
        <div class="bg-gradient-to-br from-navy-800 via-navy-900 to-navy-950 px-6 py-8 sm:px-8 sm:py-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex flex-col sm:flex-row sm:items-center gap-5">
                    <div class="profile-avatar flex items-center justify-center h-24 w-24 sm:h-28 sm:w-28 rounded-2xl bg-gradient-to-br <?php echo e($roleMeta['color']); ?> text-white overflow-hidden shadow-xl ring-4 ring-white/20 flex-shrink-0 mx-auto sm:mx-0">
                        <?php if($user->profile_image): ?>
                            <img src="<?php echo e($user->profile_image_url); ?>" alt="صورة الملف الشخصي" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                            <span class="text-4xl sm:text-5xl font-heading font-black leading-none hidden"><?php echo e(mb_substr($user->name, 0, 1)); ?></span>
                        <?php else: ?>
                            <span class="text-4xl sm:text-5xl font-heading font-black leading-none"><?php echo e(mb_substr($user->name, 0, 1)); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="text-center sm:text-right flex-1">
                        <span class="inline-flex items-center gap-2 rounded-xl <?php echo e($roleMeta['chip']); ?> px-3 py-1.5 text-xs font-bold mb-2">
                            <i class="fas fa-user-shield text-indigo-500"></i>
                            <?php echo e($roleMeta['label']); ?>

                        </span>
                        <h1 class="font-heading text-2xl sm:text-3xl font-black text-white mb-1"><?php echo e($user->name); ?></h1>
                        <p class="text-slate-300 text-sm mb-4">إدارة بياناتك وإعدادات حسابك الشخصي</p>
                        <div class="flex flex-wrap justify-center sm:justify-end gap-2">
                            <?php if($user->phone): ?>
                                <span class="inline-flex items-center gap-2 rounded-xl bg-white/10 text-white px-4 py-2 text-sm font-medium border border-white/10">
                                    <i class="fas fa-phone text-cyan-400"></i>
                                    <?php echo e($user->phone); ?>

                                </span>
                            <?php endif; ?>
                            <?php if($user->email): ?>
                                <span class="inline-flex items-center gap-2 rounded-xl bg-white/10 text-white px-4 py-2 text-sm font-medium border border-white/10">
                                    <i class="fas fa-envelope text-cyan-400"></i>
                                    <?php echo e($user->email); ?>

                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 lg:max-w-md">
                    <div class="rounded-2xl bg-white/10 backdrop-blur border border-white/10 p-4 text-center">
                        <div class="w-10 h-10 rounded-xl bg-cyan-500/30 flex items-center justify-center text-cyan-300 mx-auto mb-2">
                            <i class="fas fa-calendar-week text-sm"></i>
                        </div>
                        <div class="text-xs font-medium text-slate-400 mb-0.5">تاريخ الانضمام</div>
                        <div class="text-sm font-bold text-white"><?php echo e($memberSince); ?></div>
                    </div>
                    <div class="rounded-2xl bg-white/10 backdrop-blur border border-white/10 p-4 text-center">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/30 flex items-center justify-center text-indigo-300 mx-auto mb-2">
                            <i class="fas fa-user-shield text-sm"></i>
                        </div>
                        <div class="text-xs font-medium text-slate-400 mb-0.5">نوع الحساب</div>
                        <div class="text-sm font-bold text-white"><?php echo e($roleMeta['label']); ?></div>
                    </div>
                    <div class="rounded-2xl bg-white/10 backdrop-blur border border-white/10 p-4 text-center">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/30 flex items-center justify-center text-amber-300 mx-auto mb-2">
                            <i class="fas fa-clock-rotate-left text-sm"></i>
                        </div>
                        <div class="text-xs font-medium text-slate-400 mb-0.5">آخر تسجيل دخول</div>
                        <div class="text-sm font-bold text-white"><?php echo e($lastLogin); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        
        <div class="space-y-6">
            <div class="section-card rounded-3xl p-6 border border-slate-200/80 bg-white shadow-sm hover:shadow-md hover:border-slate-200 transition-all duration-300">
                <h2 class="font-heading text-lg font-bold text-slate-800 mb-5 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                        <i class="fas fa-info-circle text-sm"></i>
                    </span>
                    معلومات الحساب
                </h2>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="font-medium text-slate-600">رقم العضوية</span>
                        <span class="font-bold text-slate-800">#<?php echo e(str_pad($user->id, 5, '0', STR_PAD_LEFT)); ?></span>
                    </div>
                    <div class="flex items-center justify-between gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="font-medium text-slate-600">نوع الحساب</span>
                        <span class="rounded-lg px-2.5 py-1 text-xs font-bold <?php echo e($roleMeta['chip']); ?>"><?php echo e($roleMeta['label']); ?></span>
                    </div>
                    <div class="flex items-center justify-between gap-3 p-3 rounded-xl border <?php echo e($user->is_active ? 'bg-emerald-50/80 border-emerald-200' : 'bg-rose-50/80 border-rose-200'); ?>">
                        <span class="font-medium text-slate-600">الحالة</span>
                        <span class="inline-flex items-center gap-2 text-xs font-bold <?php echo e($user->is_active ? 'text-emerald-700' : 'text-rose-700'); ?>">
                            <span class="relative flex h-2 w-2">
                                <?php if($user->is_active): ?>
                                    <span class="absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-75 animate-ping"></span>
                                <?php endif; ?>
                                <span class="relative inline-flex h-2 w-2 rounded-full <?php echo e($user->is_active ? 'bg-emerald-500' : 'bg-rose-500'); ?>"></span>
                            </span>
                            <?php echo e($user->is_active ? 'نشط' : 'غير نشط'); ?>

                        </span>
                    </div>
                </div>
            </div>

            <div class="section-card rounded-3xl p-6 border border-slate-200/80 bg-white shadow-sm hover:shadow-md hover:border-slate-200 transition-all duration-300">
                <h2 class="font-heading text-lg font-bold text-slate-800 mb-5 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/20">
                        <i class="fas fa-shield-alt text-sm"></i>
                    </span>
                    المصادقة الثنائية
                </h2>
                <?php if($user->hasTwoFactorEnabled()): ?>
                    <p class="text-sm text-slate-600 mb-4">مفعّلة — يتم طلب رمز التحقق عند كل تسجيل دخول.</p>
                    <form action="<?php echo e(route('two-factor.disable')); ?>" method="POST" class="space-y-3" onsubmit="return confirm('هل تريد تعطيل المصادقة الثنائية؟ ستحتاج إدخال كلمة المرور.');">
                        <?php echo csrf_field(); ?>
                        <input type="password" name="password" required placeholder="كلمة المرور للتأكيد"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:bg-white transition-colors">
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-rose-600 text-xs font-medium"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <button type="submit" class="w-full py-2.5 rounded-xl border border-rose-200 bg-rose-50 text-rose-700 font-bold text-sm hover:bg-rose-100 transition-colors">
                            تعطيل المصادقة الثنائية
                        </button>
                    </form>
                <?php else: ?>
                    <p class="text-sm text-slate-600 mb-4">تفعيل المصادقة الثنائية يزيد أمان دخولك للمنصة.</p>
                    <a href="<?php echo e(route('two-factor.setup')); ?>" class="flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 text-white font-bold text-sm shadow-lg shadow-emerald-500/25 hover:shadow-xl hover:from-emerald-500 hover:to-emerald-400 transition-all">
                        <i class="fas fa-mobile-alt"></i>
                        تفعيل المصادقة الثنائية
                    </a>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="lg:col-span-2">
            <div class="section-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 bg-white shadow-sm hover:shadow-md hover:border-slate-200 transition-all duration-300">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                    <div>
                        <h3 class="font-heading text-xl font-bold text-slate-800 mb-1">تحديث البيانات الأساسية</h3>
                        <p class="text-sm text-slate-500">قم بمراجعة معلوماتك وتحديثها في أي وقت</p>
                    </div>
                    <span class="inline-flex items-center gap-2 text-xs font-bold rounded-xl bg-cyan-50 text-cyan-700 border border-cyan-200 px-4 py-2 w-fit">
                        <i class="fas fa-shield-check"></i>
                        بياناتك مشفرة وآمنة
                    </span>
                </div>

                <form method="POST" action="<?php echo e(route('admin.profile.update')); ?>" class="space-y-6" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">الاسم الكامل</label>
                            <div class="relative">
                                <i class="fas fa-user absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                                <input type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>" required
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pr-11 pl-4 py-3 text-slate-800 font-medium focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:bg-white transition-colors">
                            </div>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-rose-600 text-xs mt-2 font-medium"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">رقم الهاتف</label>
                            <div class="relative">
                                <i class="fas fa-phone absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                                <input type="text" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>" required
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pr-11 pl-4 py-3 text-slate-800 font-medium focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:bg-white transition-colors">
                            </div>
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-rose-600 text-xs mt-2 font-medium"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-slate-700 mb-2">البريد الإلكتروني (اختياري)</label>
                            <div class="relative">
                                <i class="fas fa-at absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                                <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>"
                                       class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pr-11 pl-4 py-3 text-slate-800 font-medium focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:bg-white transition-colors">
                            </div>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-rose-600 text-xs mt-2 font-medium"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-3">صورة الملف الشخصي</label>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-2xl overflow-hidden border-2 border-dashed border-slate-200 bg-slate-50 flex items-center justify-center flex-shrink-0">
                                <?php if($user->profile_image): ?>
                                    <img src="<?php echo e($user->profile_image_url); ?>" alt="صورة الملف الشخصي" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                                    <i class="fas fa-camera text-slate-400 text-3xl hidden"></i>
                                <?php else: ?>
                                    <i class="fas fa-camera text-slate-400 text-3xl"></i>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 px-6 py-3 text-sm font-bold text-slate-600 hover:bg-slate-100 hover:border-slate-300 transition-all">
                                    <i class="fas fa-upload text-blue-500"></i>
                                    <span>اختر صورة جديدة (PNG أو JPG — حد أقصى 40 ميجابايت)</span>
                                    <input type="file" name="profile_image" accept="image/*" class="hidden">
                                </label>
                                <?php $__errorArgs = ['profile_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-rose-600 text-xs mt-2 font-medium"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/50 p-6 space-y-4">
                        <h4 class="font-heading font-bold text-slate-800">تغيير كلمة المرور</h4>
                        <p class="text-xs text-slate-500">اترك الحقول فارغة إذا لم ترغب في التغيير</p>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-2">كلمة المرور الحالية</label>
                                <input type="password" name="current_password"
                                       class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors">
                                <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-rose-600 text-xs mt-2 font-medium"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-2">كلمة المرور الجديدة</label>
                                <input type="password" name="password"
                                       class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors">
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-rose-600 text-xs mt-2 font-medium"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-2">تأكيد كلمة المرور</label>
                                <input type="password" name="password_confirmation"
                                       class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-colors">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between pt-6 border-t border-slate-200">
                        <a href="<?php echo e(route('admin.dashboard')); ?>" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-all order-2 sm:order-1">
                            <i class="fas fa-arrow-right"></i>
                            رجوع للوحة التحكم
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-500/25 hover:shadow-xl hover:from-blue-500 hover:to-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all order-1 sm:order-2">
                            <i class="fas fa-save"></i>
                            حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\profile\index.blade.php ENDPATH**/ ?>