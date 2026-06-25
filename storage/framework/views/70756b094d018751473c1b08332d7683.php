<?php
    $isRtl = app()->getLocale() === 'ar';
    $langSwitch = fn (string $lang) => request()->fullUrlWithQuery(array_merge(request()->query(), ['lang' => $lang]));
?>
<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(__('auth.register')); ?> — <?php echo e(config('app.name')); ?></title>
    <meta name="theme-color" content="#0d1528">
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
                    acad: {
                        blue: '#0B3D91',
                        blueDark: '#072a66',
                        blueSoft: '#E8EEF8',
                        cyan: '#00A3C4',
                        yellow: '#F5B800',
                        ink: '#1a2d4d',
                        navy: '#0d1528',
                        navyMid: '#1a2d4d',
                    },
                    mx: { indigo: '#1F2A7A', navy: '#283593', orange: '#FB5607' },
                },
                fontFamily: { sans: ['Cairo','Tajawal','IBM Plex Sans Arabic','system-ui','sans-serif'] },
            },
        },
    };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"></noscript>
    <style>
        [x-cloak]{display:none!important}
        *{font-family:'Cairo','IBM Plex Sans Arabic','Tajawal',system-ui,sans-serif;box-sizing:border-box}
        html{min-height:100%;scroll-behavior:smooth}
        body{margin:0;min-height:100vh}
        .auth-bg{
            background:linear-gradient(165deg,#0d1528 0%,#121f38 42%,#1a2d4d 100%);
        }
        .auth-dots{
            background-image:radial-gradient(circle at 1px 1px,rgba(255,255,255,.055) 1px,transparent 0);
            background-size:24px 24px;
        }
        .auth-glow{
            background:radial-gradient(ellipse 65% 45% at 85% 10%,rgba(245,184,0,.12),transparent 50%),
                        radial-gradient(ellipse 55% 45% at 10% 90%,rgba(0,163,196,.18),transparent 52%);
        }
        .input-auth{
            width:100%;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:14px;padding:13px 16px;font-size:15px;font-weight:600;color:#0f172a;transition:border-color .2s,box-shadow .2s,background .2s;
        }
        .input-auth:hover{border-color:#cbd5e1;background:#f1f5f9}
        .input-auth:focus{outline:none;border-color:#0B3D91;box-shadow:0 0 0 3px rgba(11,61,145,.12);background:#fff}
        .input-auth::placeholder{color:#94a3b8}
        .input-auth.has-error{border-color:#ef4444}
        .input-auth.has-error:focus{box-shadow:0 0 0 3px rgba(239,68,68,.12)}
        .phone-row{display:flex;border:1.5px solid #e2e8f0;border-radius:14px;background:#f8fafc;overflow:hidden;transition:border-color .2s,box-shadow .2s,background .2s}
        .phone-row:hover{border-color:#cbd5e1;background:#f1f5f9}
        .phone-row:focus-within{border-color:#0B3D91;box-shadow:0 0 0 3px rgba(11,61,145,.12);background:#fff}
        .phone-row select,.phone-row input{border:none;background:transparent;outline:none;font-size:15px;font-weight:600;color:#0f172a;padding:13px 12px}
        .phone-row select{flex-shrink:0;min-width:8rem;max-width:11rem;border-inline-end:1.5px solid #e2e8f0;cursor:pointer;-webkit-appearance:none;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 8px center;background-size:18px;padding-right:28px}
        .phone-row input{flex:1;min-width:0}
        @media(max-width:640px){
            .phone-row select{min-width:7rem;max-width:45%;font-size:14px}
        }
    </style>
</head>
<body class="auth-bg text-white antialiased" x-data="{ showPassword: false, showPasswordConfirm: false }">
    <div class="fixed inset-0 auth-dots pointer-events-none opacity-90"></div>
    <div class="fixed inset-0 auth-glow pointer-events-none"></div>

    <div class="relative z-10 min-h-screen flex flex-col items-center px-4 py-10 sm:py-12">
        <div class="absolute top-4 <?php echo e($isRtl ? 'left-4' : 'right-4'); ?> flex items-center gap-1 rounded-xl border border-white/15 bg-white/5 p-1 text-xs font-bold backdrop-blur-md">
            <a href="<?php echo e($langSwitch('ar')); ?>" class="px-3 py-1.5 rounded-lg <?php echo e($isRtl ? 'bg-white/20 text-white' : 'text-white/70 hover:text-white'); ?>" hreflang="ar">عربي</a>
            <a href="<?php echo e($langSwitch('en')); ?>" class="px-3 py-1.5 rounded-lg <?php echo e(! $isRtl ? 'bg-white/20 text-white' : 'text-white/70 hover:text-white'); ?>" hreflang="en">EN</a>
        </div>

        <div class="w-full max-w-[34rem] lg:max-w-[36rem] mt-6">
            <div class="rounded-[1.65rem] overflow-hidden border border-white/15 shadow-[0_24px_80px_-20px_rgba(0,0,0,.55)] bg-white/[0.97] backdrop-blur-xl">
                <div class="h-1 w-full bg-gradient-to-l from-acad-cyan via-acad-yellow to-acad-cyan"></div>
                <div class="px-6 sm:px-8 pt-8 pb-8 text-slate-800">
                    <div class="flex justify-center">
                        <?php echo $__env->make('partials.auth-brand-link', ['size' => 'sm', 'fallback' => 'orange', 'mb' => 'mb-5'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                    <h1 class="text-center font-black text-2xl sm:text-[1.65rem] text-acad-ink leading-tight mb-1"><?php echo e(__('auth.register')); ?></h1>
                    <p class="text-center text-slate-500 text-sm mb-6"><?php echo e(__('auth.register_subtitle')); ?></p>

                    <div class="mb-6 flex gap-3 p-3.5 rounded-xl bg-acad-blueSoft/90 border border-acad-blue/10">
                        <span class="w-9 h-9 rounded-lg bg-white border border-acad-blue/10 flex items-center justify-center shrink-0 shadow-sm">
                            <i class="fas fa-circle-info text-acad-blue text-sm"></i>
                        </span>
                        <p class="text-xs sm:text-sm font-semibold text-acad-ink leading-relaxed"><?php echo e(__('auth.register_portal_note')); ?></p>
                    </div>

                    <?php if(!empty($pendingReferralCode)): ?>
                    <div class="mb-6 flex items-start gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200/80">
                        <span class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center shrink-0 text-white shadow-md">
                            <i class="fas fa-gift"></i>
                        </span>
                        <div class="text-sm text-emerald-950 min-w-0">
                            <p class="font-bold mb-1"><?php echo e(__('auth.referral_invite_title')); ?></p>
                            <p class="text-emerald-800/95 leading-relaxed"><?php echo e(__('auth.referral_invite_body', ['code' => $pendingReferralCode])); ?></p>
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

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4">
                            <div>
                                <label for="name" class="block text-sm font-bold text-acad-ink mb-1.5"><?php echo e(__('auth.full_name')); ?></label>
                                <div class="relative">
                                    <span class="absolute <?php echo e($isRtl?'right-4':'left-4'); ?> top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fas fa-user text-sm"></i></span>
                                    <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" required
                                           class="input-auth <?php echo e($isRtl?'pr-11':'pl-11'); ?> <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> has-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           placeholder="<?php echo e(__('auth.enter_full_name')); ?>">
                                </div>
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-acad-ink mb-1.5"><?php echo e(__('auth.phone_number')); ?></label>
                                <div class="phone-row <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> !border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <select name="country_code" required dir="ltr" aria-label="<?php echo e(__('auth.country_code_aria')); ?>">
                                        <?php $__currentLoopData = $phoneCountries ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($c['dial_code']); ?>" <?php echo e(old('country_code', $defaultCountry['dial_code'] ?? '+966') === $c['dial_code'] ? 'selected' : ''); ?>>
                                            <?php echo e($c['dial_code']); ?> <?php echo e($c['name_ar']); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <input type="tel" name="phone" value="<?php echo e(old('phone')); ?>" required placeholder="xxxxxxxx" dir="ltr" aria-label="<?php echo e(__('auth.phone_aria')); ?>">
                                </div>
                                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-bold text-acad-ink mb-1.5"><?php echo e(__('auth.email')); ?> <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute <?php echo e($isRtl?'right-4':'left-4'); ?> top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fas fa-envelope text-sm"></i></span>
                                    <input type="email" name="email" id="email" value="<?php echo e(old('email')); ?>" required
                                           class="input-auth <?php echo e($isRtl?'pr-11':'pl-11'); ?> <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> has-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           placeholder="you@example.com" dir="ltr">
                                </div>
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label for="password" class="block text-sm font-bold text-acad-ink mb-1.5"><?php echo e(__('auth.password')); ?></label>
                                <div class="relative">
                                    <span class="absolute <?php echo e($isRtl?'right-4':'left-4'); ?> top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fas fa-lock text-sm"></i></span>
                                    <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required
                                           class="input-auth <?php echo e($isRtl?'pr-11 pl-11':'pl-11 pr-11'); ?> <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> has-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           placeholder="<?php echo e(__('auth.enter_strong_password')); ?>">
                                    <button type="button" @click="showPassword = !showPassword"
                                            class="absolute <?php echo e($isRtl?'left-3':'right-3'); ?> top-1/2 -translate-y-1/2 w-9 h-9 flex items-center justify-center rounded-lg text-slate-400 hover:text-acad-blue hover:bg-slate-100 transition-colors">
                                        <i x-show="!showPassword" class="fas fa-eye text-sm"></i>
                                        <i x-show="showPassword" x-cloak class="fas fa-eye-slash text-sm"></i>
                                    </button>
                                </div>
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="password_confirmation" class="block text-sm font-bold text-acad-ink mb-1.5"><?php echo e(__('auth.password_confirmation')); ?></label>
                                <div class="relative">
                                    <span class="absolute <?php echo e($isRtl?'right-4':'left-4'); ?> top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fas fa-lock text-sm"></i></span>
                                    <input :type="showPasswordConfirm ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" required
                                           class="input-auth <?php echo e($isRtl?'pr-11 pl-11':'pl-11 pr-11'); ?>"
                                           placeholder="<?php echo e(__('auth.reenter_password')); ?>">
                                    <button type="button" @click="showPasswordConfirm = !showPasswordConfirm"
                                            class="absolute <?php echo e($isRtl?'left-3':'right-3'); ?> top-1/2 -translate-y-1/2 w-9 h-9 flex items-center justify-center rounded-lg text-slate-400 hover:text-acad-blue hover:bg-slate-100 transition-colors">
                                        <i x-show="!showPasswordConfirm" class="fas fa-eye text-sm"></i>
                                        <i x-show="showPasswordConfirm" x-cloak class="fas fa-eye-slash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 mt-5">
                            <input type="checkbox" id="terms" required
                                   class="mt-0.5 w-4 h-4 rounded border-slate-300 text-acad-blue focus:ring-acad-blue/25 shrink-0">
                            <label for="terms" class="text-sm text-slate-600 leading-relaxed">
                                <?php echo e(__('auth.agree_terms')); ?>

                                <a href="<?php echo e(route('public.terms')); ?>" class="font-bold text-acad-blue hover:text-acad-cyan hover:underline"><?php echo e(__('auth.terms_of_use')); ?></a>
                                <?php echo e(__('auth.and')); ?>

                                <a href="<?php echo e(route('public.privacy')); ?>" class="font-bold text-acad-blue hover:text-acad-cyan hover:underline"><?php echo e(__('auth.privacy_policy')); ?></a>
                            </label>
                        </div>

                        <button type="submit" class="mt-6 w-full flex items-center justify-center gap-2 rounded-xl bg-acad-yellow text-acad-blue font-black py-3.5 text-base shadow-lg shadow-acad-blue/10 hover:brightness-105 active:scale-[0.99] transition-all">
                            <i class="fas fa-user-plus text-sm"></i>
                            <span><?php echo e(__('auth.create_account_btn')); ?></span>
                        </button>
                    </form>

                    <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                        <p class="text-sm text-slate-600">
                            <?php echo e(__('auth.already_have_account')); ?>

                            <a href="<?php echo e(route('login')); ?>" class="font-black text-acad-blue hover:text-acad-cyan transition-colors"><?php echo e(__('auth.login')); ?></a>
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-8 text-center">
                <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center gap-2 text-sm text-white/55 hover:text-acad-yellow transition-colors">
                    <i class="fas fa-arrow-<?php echo e($isRtl?'right':'left'); ?> text-xs"></i>
                    <?php echo e(__('auth.back_to_home')); ?>

                </a>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\auth\register.blade.php ENDPATH**/ ?>