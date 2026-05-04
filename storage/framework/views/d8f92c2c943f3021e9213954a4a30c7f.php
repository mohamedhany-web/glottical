<?php $isRtl = app()->getLocale() === 'ar'; ?>
<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>إنشاء حساب — Muallimx</title>
    <meta name="theme-color" content="#283593">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    navy: { 950:'#0F172A' },
                    brand: { 400:'#22d3ee', 500:'#06b6d4', 600:'#0891b2' },
                    mx: {
                        navy: '#283593',
                        indigo: '#1F2A7A',
                        orange: '#FB5607',
                        rose: '#FFE5F7'
                    }
                }
            }
        }
    }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"></noscript>

    <style>
        *{font-family:'Cairo','IBM Plex Sans Arabic','Tajawal',system-ui,sans-serif;margin:0;padding:0;box-sizing:border-box}
        h1,h2,h3,h4,.font-heading{font-family:'Cairo','Tajawal','IBM Plex Sans Arabic',sans-serif}
        html{height:100%}
        body{min-height:100%;overflow-y:auto}

        @keyframes float-slow{0%,100%{transform:translateY(0) rotate(0deg)}50%{transform:translateY(-18px) rotate(2deg)}}
        @keyframes float-delayed{0%,100%{transform:translateY(0)}50%{transform:translateY(-12px) rotate(-1.5deg)}}
        .float-slow{animation:float-slow 8s ease-in-out infinite}
        .float-delayed{animation:float-delayed 10s ease-in-out infinite 2s}

        .text-gradient{background:linear-gradient(135deg,#FB5607 0%,#283593 70%,#1F2A7A 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}

        .input-field{background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:14px;padding:13px 16px;font-size:15px;font-weight:500;color:#0f172a;transition:all .25s ease;width:100%}
        .input-field:hover{border-color:#cbd5e1;background:#f1f5f9}
        .input-field:focus{outline:none;border-color:#283593;box-shadow:0 0 0 3px rgba(40,53,147,.12);background:#fff}
        .input-field::placeholder{color:#94a3b8}
        .input-field.has-error{border-color:#ef4444}
        .input-field.has-error:focus{box-shadow:0 0 0 3px rgba(239,68,68,.12)}

        .btn-register{position:relative;overflow:hidden;background:#FB5607;color:#fff;border:none;border-radius:14px;padding:15px;font-size:16px;font-weight:700;cursor:pointer;transition:all .3s ease;width:100%}
        .btn-register:hover{transform:translateY(-1px);box-shadow:0 12px 32px -8px rgba(251,86,7,.4)}
        .btn-register::before{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.15),transparent);transition:left .5s}
        .btn-register:hover::before{left:100%}

        .phone-row{display:flex;border:1.5px solid #e2e8f0;border-radius:14px;background:#f8fafc;overflow:hidden;transition:all .25s ease}
        .phone-row:hover{border-color:#cbd5e1;background:#f1f5f9}
        .phone-row:focus-within{border-color:#283593;box-shadow:0 0 0 3px rgba(40,53,147,.12);background:#fff}
        .phone-row select,.phone-row input{border:none;background:transparent;outline:none;font-size:15px;font-weight:500;color:#0f172a;padding:13px 12px}
        .phone-row select{flex-shrink:0;min-width:8rem;max-width:11rem;border-inline-end:1.5px solid #e2e8f0;cursor:pointer;-webkit-appearance:none;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 8px center;background-size:18px;padding-right:28px}
        .phone-row input{flex:1;min-width:0}

        @media(max-width:640px){
            .form-grid{display:grid;grid-template-columns:1fr;gap:0}
            .phone-row select{min-width:7rem;max-width:45%;font-size:14px}
        }
    </style>
</head>
<body class="bg-white" x-data="{ showPassword: false, showPasswordConfirm: false }">
    <div class="flex min-h-screen">

        
        <div class="hidden lg:flex lg:w-[42%] xl:w-[45%] relative items-center justify-center overflow-hidden sticky top-0 h-screen" style="background:radial-gradient(circle at 12% 80%,rgba(255,229,247,.45),transparent 32%),radial-gradient(circle at 88% 20%,rgba(40,53,147,.12),transparent 34%),linear-gradient(180deg,#f4f6ff 0%,#fbfbff 60%,#ffffff 100%)">
            <div class="absolute inset-0 opacity-40" style="background-image:radial-gradient(circle at 1px 1px,rgba(40,53,147,.08) 1px,transparent 0);background-size:30px 30px"></div>
            <div class="absolute top-[-15%] <?php echo e($isRtl?'left-[-8%]':'right-[-8%]'); ?> w-[450px] h-[450px] rounded-full bg-[#283593]/10 blur-[100px] float-slow"></div>
            <div class="absolute bottom-[-10%] <?php echo e($isRtl?'right-[-5%]':'left-[-5%]'); ?> w-[350px] h-[350px] rounded-full bg-[#FB5607]/10 blur-[80px] float-delayed"></div>

            <div class="relative z-10 max-w-sm px-10 text-center">
                <?php echo $__env->make('partials.auth-brand-link', ['size' => 'lg'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                <h1 class="font-heading text-3xl font-black text-mx-indigo leading-tight mb-5">
                    انضم لآلاف المعلمين
                    <br><span class="text-gradient">وابدأ العمل أونلاين</span>
                </h1>
                <p class="text-slate-600 text-sm leading-relaxed mb-10">
                    أنشئ حسابك واحصل على تدريب تطبيقي، أدوات AI ذكية، وفرص عمل حقيقية — مجاناً.
                </p>

                <div class="space-y-3 text-<?php echo e($isRtl?'right':'left'); ?>">
                    <?php
                    $perks = [
                        ['icon'=>'fa-graduation-cap','color'=>'brand','text'=>'دبلومات وكورسات احترافية'],
                        ['icon'=>'fa-wand-magic-sparkles','color'=>'purple','text'=>'مساعد AI لتحضير الحصص'],
                        ['icon'=>'fa-file-alt','color'=>'blue','text'=>'مناهج وأنشطة جاهزة'],
                        ['icon'=>'fa-id-badge','color'=>'emerald','text'=>'بروفايل مهني + توظيف'],
                        ['icon'=>'fa-certificate','color'=>'amber','text'=>'شهادات معتمدة وإجازات'],
                    ];
                    ?>
                    <?php $__currentLoopData = $perks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center gap-3 p-3 rounded-xl">
                        <span class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center flex-shrink-0">
                            <i class="fas <?php echo e($perk['icon']); ?> text-[#283593] text-sm"></i>
                        </span>
                        <span class="text-slate-700 text-sm font-medium"><?php echo e($perk['text']); ?></span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        
        <div class="flex-1 flex flex-col items-center px-5 sm:px-8 py-8 lg:py-10 bg-white overflow-y-auto">

            
            <div class="lg:hidden w-full max-w-lg">
                <?php echo $__env->make('partials.auth-brand-link', ['size' => 'sm', 'fallback' => 'orange', 'mb' => 'mb-6'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>

            <div class="w-full max-w-lg">
                <div class="text-center lg:text-<?php echo e($isRtl?'right':'left'); ?> mb-6">
                    <h2 class="font-heading text-2xl sm:text-3xl font-black text-mx-indigo mb-2">
                        إنشاء حساب جديد
                    </h2>
                    <p class="text-slate-500 text-sm sm:text-base">سجّل الآن وابدأ رحلتك في التعليم أونلاين</p>
                </div>

                <div class="mb-5 flex items-center gap-3 p-3.5 rounded-2xl bg-[#FFE5F7] border border-[#f5c7e8]">
                    <span class="w-8 h-8 rounded-lg bg-white flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-info-circle text-[#283593] text-sm"></i>
                    </span>
                    <p class="text-xs sm:text-sm font-semibold text-navy-950">هذا النموذج لتسجيل المعلمين والطلاب. للتسجيل كمدرب تواصل معنا.</p>
                </div>

                <?php if(!empty($pendingReferralCode)): ?>
                <div class="mb-5 flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-200">
                    <span class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center flex-shrink-0 text-white">
                        <i class="fas fa-gift text-lg"></i>
                    </span>
                    <div class="text-sm text-emerald-950">
                        <p class="font-bold mb-1">أنت تسجّل عبر رابط دعوة</p>
                        <p class="text-emerald-800">سيتم ربط حسابك بكود الإحالة <span class="font-mono font-bold"><?php echo e($pendingReferralCode); ?></span> بعد إتمام التسجيل (إن كان البرنامج مفعّلاً).</p>
                    </div>
                </div>
                <?php endif; ?>

                <form action="<?php echo e(route('register')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="referral_code" value="<?php echo e(old('referral_code', $pendingReferralCode ?? '')); ?>">
                    <?php
                        $phoneCountries = $phoneCountries ?? config('phone_countries.countries', []);
                        $defaultCountry = $defaultCountry ?? collect($phoneCountries)->firstWhere('code', config('phone_countries.default_country', 'SA'));
                    ?>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">
                        
                        <div>
                            <label for="name" class="block text-sm font-bold text-navy-950 mb-2">الاسم الكامل</label>
                            <div class="relative">
                                <span class="absolute <?php echo e($isRtl?'right-4':'left-4'); ?> top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fas fa-user text-sm"></i></span>
                                <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" required
                                       class="input-field <?php echo e($isRtl?'pr-11':'pl-11'); ?> <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> has-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       placeholder="أدخل اسمك الكامل">
                            </div>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-500 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div>
                            <label class="block text-sm font-bold text-navy-950 mb-2">رقم الهاتف</label>
                            <div class="phone-row <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <select name="country_code" required dir="ltr">
                                    <?php $__currentLoopData = $phoneCountries ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($c['dial_code']); ?>" <?php echo e(old('country_code', $defaultCountry['dial_code'] ?? '+966') === $c['dial_code'] ? 'selected' : ''); ?>>
                                        <?php echo e($c['dial_code']); ?> <?php echo e($c['name_ar']); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <input type="tel" name="phone" value="<?php echo e(old('phone')); ?>" required placeholder="xxxxxxxx" dir="ltr">
                            </div>
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-500 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div>
                            <label for="email" class="block text-sm font-bold text-navy-950 mb-2">البريد الإلكتروني <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <span class="absolute <?php echo e($isRtl?'right-4':'left-4'); ?> top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fas fa-envelope text-sm"></i></span>
                                <input type="email" name="email" id="email" value="<?php echo e(old('email')); ?>" required
                                       class="input-field <?php echo e($isRtl?'pr-11':'pl-11'); ?> <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> has-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       placeholder="example@email.com" dir="ltr">
                            </div>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-500 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div>
                            <label for="password" class="block text-sm font-bold text-navy-950 mb-2">كلمة المرور</label>
                            <div class="relative">
                                <span class="absolute <?php echo e($isRtl?'right-4':'left-4'); ?> top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fas fa-lock text-sm"></i></span>
                                <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required
                                       class="input-field <?php echo e($isRtl?'pr-11 pl-11':'pl-11 pr-11'); ?> <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> has-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       placeholder="كلمة مرور قوية">
                                <button type="button" @click="showPassword = !showPassword"
                                        class="absolute <?php echo e($isRtl?'left-4':'right-4'); ?> top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#283593] transition-colors">
                                    <i x-show="!showPassword" class="fas fa-eye text-sm"></i>
                                    <i x-show="showPassword" class="fas fa-eye-slash text-sm"></i>
                                </button>
                            </div>
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-500 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="sm:col-span-2">
                            <label for="password_confirmation" class="block text-sm font-bold text-navy-950 mb-2">تأكيد كلمة المرور</label>
                            <div class="relative">
                                <span class="absolute <?php echo e($isRtl?'right-4':'left-4'); ?> top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fas fa-lock text-sm"></i></span>
                                <input :type="showPasswordConfirm ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" required
                                       class="input-field <?php echo e($isRtl?'pr-11 pl-11':'pl-11 pr-11'); ?>"
                                       placeholder="أعد كتابة كلمة المرور">
                                <button type="button" @click="showPasswordConfirm = !showPasswordConfirm"
                                        class="absolute <?php echo e($isRtl?'left-4':'right-4'); ?> top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#283593] transition-colors">
                                    <i x-show="!showPasswordConfirm" class="fas fa-eye text-sm"></i>
                                    <i x-show="showPasswordConfirm" class="fas fa-eye-slash text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    
                    <div class="flex items-start gap-3 mt-5">
                        <input type="checkbox" id="terms" required
                               class="mt-0.5 w-4 h-4 rounded-md border-slate-300 text-[#283593] focus:ring-[#283593]/20 transition-colors flex-shrink-0">
                        <label for="terms" class="text-sm text-slate-600 leading-relaxed">
                            أوافق على
                            <a href="#" class="font-semibold text-[#283593] hover:underline">شروط الاستخدام</a>
                            و
                            <a href="#" class="font-semibold text-[#283593] hover:underline">سياسة الخصوصية</a>
                        </label>
                    </div>

                    
                    <button type="submit" class="btn-register mt-6 flex items-center justify-center gap-2">
                        <i class="fas fa-user-plus text-sm"></i>
                        <span>إنشاء الحساب</span>
                    </button>
                </form>

                
                <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                    <p class="text-sm text-slate-500">
                        لديك حساب بالفعل؟
                        <a href="<?php echo e(route('login')); ?>" class="font-bold text-[#283593] hover:text-[#1F2A7A] transition-colors">سجّل دخولك</a>
                    </p>
                </div>

                
                <div class="mt-5 text-center pb-4">
                    <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fas fa-arrow-<?php echo e($isRtl?'right':'left'); ?> text-xs"></i>
                        العودة للرئيسية
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\auth\register.blade.php ENDPATH**/ ?>