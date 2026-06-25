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
    <title><?php echo e(__('auth.login')); ?> — <?php echo e(config('app.name')); ?></title>
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
            background:radial-gradient(ellipse 70% 50% at 20% 0%,rgba(0,163,196,.22),transparent 55%),
                        radial-gradient(ellipse 50% 40% at 95% 85%,rgba(245,184,0,.14),transparent 50%);
        }
        .input-auth{
            width:100%;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:14px;padding:14px 16px 14px 16px;font-size:15px;font-weight:600;color:#0f172a;transition:border-color .2s,box-shadow .2s,background .2s;
        }
        .input-auth:hover{border-color:#cbd5e1;background:#f1f5f9}
        .input-auth:focus{outline:none;border-color:#0B3D91;box-shadow:0 0 0 3px rgba(11,61,145,.12);background:#fff}
        .input-auth::placeholder{color:#94a3b8}
        .input-auth.has-error{border-color:#ef4444}
        .input-auth.has-error:focus{box-shadow:0 0 0 3px rgba(239,68,68,.12)}
    </style>
</head>
<body class="auth-bg text-white antialiased" x-data="{ showPassword: false }">
    <div class="fixed inset-0 auth-dots pointer-events-none opacity-90"></div>
    <div class="fixed inset-0 auth-glow pointer-events-none"></div>

    <div class="relative z-10 min-h-screen flex flex-col items-center justify-center px-4 py-10 sm:py-14">
        <div class="absolute top-4 <?php echo e($isRtl ? 'left-4' : 'right-4'); ?> flex items-center gap-1 rounded-xl border border-white/15 bg-white/5 p-1 text-xs font-bold backdrop-blur-md">
            <a href="<?php echo e($langSwitch('ar')); ?>" class="px-3 py-1.5 rounded-lg <?php echo e($isRtl ? 'bg-white/20 text-white' : 'text-white/70 hover:text-white'); ?>" hreflang="ar">عربي</a>
            <a href="<?php echo e($langSwitch('en')); ?>" class="px-3 py-1.5 rounded-lg <?php echo e(! $isRtl ? 'bg-white/20 text-white' : 'text-white/70 hover:text-white'); ?>" hreflang="en">EN</a>
        </div>

        <div class="w-full max-w-[440px]">
            <div class="rounded-[1.65rem] overflow-hidden border border-white/15 shadow-[0_24px_80px_-20px_rgba(0,0,0,.55)] bg-white/[0.97] backdrop-blur-xl">
                <div class="h-1 w-full bg-gradient-to-l from-acad-yellow via-acad-cyan to-acad-yellow"></div>
                <div class="px-6 sm:px-8 pt-8 pb-8 text-slate-800">
                    <div class="flex justify-center">
                        <?php echo $__env->make('partials.auth-brand-link', ['size' => 'sm', 'fallback' => 'gradient', 'mb' => 'mb-6'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                    <h1 class="text-center font-black text-2xl sm:text-[1.65rem] text-acad-ink leading-tight mb-1"><?php echo e(__('auth.welcome_back')); ?></h1>
                    <p class="text-center text-slate-500 text-sm mb-8"><?php echo e(__('auth.enter_credentials')); ?></p>

                    <form action="<?php echo e(route('login')); ?>" method="POST" class="space-y-5">
                        <?php echo csrf_field(); ?>
                        <?php if(session('status')): ?>
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-sm font-semibold">
                            <i class="fas fa-check-circle text-emerald-500 shrink-0"></i>
                            <?php echo e(session('status')); ?>

                        </div>
                        <?php endif; ?>
                        <?php if(session('warning')): ?>
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-950 text-sm font-semibold">
                            <i class="fas fa-exclamation-triangle text-amber-500 shrink-0"></i>
                            <?php echo e(session('warning')); ?>

                        </div>
                        <?php endif; ?>
                        <div style="display:none" aria-hidden="true">
                            <input type="text" name="website" tabindex="-1" autocomplete="off">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-bold text-acad-ink mb-2"><?php echo e(__('auth.email')); ?></label>
                            <div class="relative">
                                <span class="absolute <?php echo e($isRtl?'right-4':'left-4'); ?> top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fas fa-envelope text-sm"></i></span>
                                <input type="email" name="email" id="email" value="<?php echo e(old('email')); ?>" required autocomplete="email" autofocus
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
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs text-red-600 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-bold text-acad-ink mb-2"><?php echo e(__('auth.password')); ?></label>
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
                                       placeholder="••••••••">
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
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs text-red-600 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="flex items-center justify-between gap-3 flex-wrap">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-acad-blue focus:ring-acad-blue/25">
                                <span class="text-sm text-slate-600"><?php echo e(__('auth.remember')); ?></span>
                            </label>
                            <a href="<?php echo e(route('password.request')); ?>" class="text-sm font-bold text-acad-blue hover:text-acad-cyan transition-colors"><?php echo e(__('auth.forgot_password')); ?></a>
                        </div>
                        <button type="submit" class="w-full flex items-center justify-center gap-2 rounded-xl bg-acad-yellow text-acad-blue font-black py-3.5 text-base shadow-lg shadow-acad-blue/10 hover:brightness-105 active:scale-[0.99] transition-all">
                            <span><?php echo e(__('auth.login')); ?></span>
                            <i class="fas fa-arrow-<?php echo e($isRtl?'left':'right'); ?> text-sm"></i>
                        </button>
                    </form>

                    <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                        <p class="text-sm text-slate-600">
                            <?php echo e(__('auth.no_account_question')); ?>

                            <a href="<?php echo e(route('register')); ?>" class="font-black text-acad-blue hover:text-acad-cyan transition-colors"><?php echo e(__('auth.no_account_register_now')); ?></a>
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4 text-sm">
                <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center gap-2 text-white/55 hover:text-acad-yellow transition-colors">
                    <i class="fas fa-arrow-<?php echo e($isRtl?'right':'left'); ?> text-xs"></i>
                    <?php echo e(__('auth.back_to_home')); ?>

                </a>
                <span class="hidden sm:inline text-white/25">|</span>
                <div class="flex items-center gap-2 text-white/45 text-xs max-w-xs text-center sm:text-start">
                    <i class="fas fa-shield-halved text-acad-cyan/80 shrink-0"></i>
                    <span><?php echo e(__('auth.visual_desc')); ?></span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\auth\login.blade.php ENDPATH**/ ?>