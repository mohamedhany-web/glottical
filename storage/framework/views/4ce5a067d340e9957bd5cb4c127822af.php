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
    <title><?php echo e(__('auth.two_factor')); ?> — <?php echo e(config('app.name')); ?></title>
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
                        cyan: '#00A3C4',
                        yellow: '#F5B800',
                        ink: '#1a2d4d',
                        navy: '#0d1528',
                        navyMid: '#1a2d4d',
                    },
                },
                fontFamily: { sans: ['Cairo','Tajawal','IBM Plex Sans Arabic','system-ui','sans-serif'] },
            },
        },
    };
    </script>
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"></noscript>
    <style>
        *{font-family:'Cairo','IBM Plex Sans Arabic','Tajawal',system-ui,sans-serif;box-sizing:border-box}
        html{min-height:100%;scroll-behavior:smooth}
        body{margin:0;min-height:100vh}
        .auth-bg{background:linear-gradient(165deg,#0d1528 0%,#121f38 42%,#1a2d4d 100%)}
        .auth-dots{background-image:radial-gradient(circle at 1px 1px,rgba(255,255,255,.055) 1px,transparent 0);background-size:24px 24px}
        .auth-glow{background:radial-gradient(ellipse 70% 50% at 20% 0%,rgba(0,163,196,.22),transparent 55%),radial-gradient(ellipse 50% 40% at 95% 85%,rgba(245,184,0,.14),transparent 50%)}
        .input-auth{width:100%;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:14px;padding:1rem 1rem;font-size:1.35rem;font-weight:800;letter-spacing:.35em;text-align:center;color:#0f172a;transition:border-color .2s,box-shadow .2s}
        .input-auth:hover{border-color:#cbd5e1;background:#f1f5f9}
        .input-auth:focus{outline:none;border-color:#0B3D91;box-shadow:0 0 0 3px rgba(11,61,145,.12);background:#fff}
        .input-auth::placeholder{color:#94a3b8;letter-spacing:.2em;font-weight:600}
        .input-auth.has-error{border-color:#ef4444}
        .input-auth.has-error:focus{box-shadow:0 0 0 3px rgba(239,68,68,.12)}
    </style>
</head>
<body class="auth-bg text-white antialiased">
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

                    <div class="flex justify-center mb-4">
                        <span class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-acad-blue to-acad-navyMid text-white shadow-lg shadow-acad-blue/25">
                            <i class="fas fa-shield-halved text-xl"></i>
                        </span>
                    </div>

                    <h1 class="text-center font-black text-2xl sm:text-[1.65rem] text-acad-ink leading-tight mb-2">
                        <?php echo e(__('auth.two_factor_challenge_title')); ?>

                    </h1>
                    <p class="text-center text-slate-500 text-sm mb-8 leading-relaxed">
                        <?php if(!empty($useEmail)): ?>
                            <?php echo __('auth.two_factor_challenge_desc_email'); ?>

                        <?php else: ?>
                            <?php echo e(__('auth.two_factor_challenge_desc_app')); ?>

                        <?php endif; ?>
                    </p>

                    <?php if($errors->has('code')): ?>
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-rose-50 border border-rose-200/80 text-rose-800 text-sm font-semibold mb-5">
                            <i class="fas fa-circle-exclamation text-rose-500 shrink-0"></i>
                            <?php echo e($errors->first('code')); ?>

                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('two-factor.verify')); ?>" method="POST" class="space-y-5">
                        <?php echo csrf_field(); ?>
                        <div>
                            <label for="code" class="block text-sm font-bold text-acad-ink mb-2 text-center sm:text-<?php echo e($isRtl ? 'right' : 'left'); ?>">
                                <?php echo e(__('auth.two_factor_code_label')); ?>

                            </label>
                            <div class="relative">
                                <span class="absolute <?php echo e($isRtl ? 'right-4' : 'left-4'); ?> top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                    <i class="fas fa-key text-sm"></i>
                                </span>
                                <input type="text"
                                       name="code"
                                       id="code"
                                       inputmode="numeric"
                                       pattern="[0-9]*"
                                       maxlength="10"
                                       autocomplete="one-time-code"
                                       autofocus
                                       required
                                       class="input-auth <?php echo e($isRtl ? 'pr-11' : 'pl-11'); ?> <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> has-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       placeholder="••••••"
                                       dir="ltr">
                            </div>
                        </div>
                        <button type="submit" class="w-full flex items-center justify-center gap-2 rounded-xl bg-acad-yellow text-acad-blue font-black py-3.5 text-base shadow-lg shadow-acad-blue/10 hover:brightness-105 active:scale-[0.99] transition-all">
                            <span><?php echo e(__('auth.two_factor_confirm')); ?></span>
                            <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?> text-sm"></i>
                        </button>
                    </form>

                    <?php if(empty($useEmail)): ?>
                        <p class="text-xs text-slate-500 mt-6 text-center leading-relaxed">
                            <?php echo __('auth.two_factor_recovery_hint'); ?>

                        </p>
                    <?php else: ?>
                        <p class="text-xs text-slate-500 mt-6 text-center leading-relaxed">
                            <?php echo __('auth.two_factor_resend_hint', ['login_url' => route('login')]); ?>

                        </p>
                    <?php endif; ?>

                    <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                        <a href="<?php echo e(route('login')); ?>" class="inline-flex items-center gap-2 text-sm font-bold text-acad-blue hover:text-acad-cyan transition-colors">
                            <i class="fas fa-arrow-<?php echo e($isRtl ? 'right' : 'left'); ?> text-xs"></i>
                            <?php echo e(__('auth.two_factor_back_login')); ?>

                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4 text-sm">
                <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center gap-2 text-white/55 hover:text-acad-yellow transition-colors">
                    <i class="fas fa-arrow-<?php echo e($isRtl ? 'right' : 'left'); ?> text-xs"></i>
                    <?php echo e(__('auth.back_to_home')); ?>

                </a>
                <span class="hidden sm:inline text-white/25">|</span>
                <div class="flex items-center gap-2 text-white/45 text-xs max-w-xs text-center sm:text-start">
                    <i class="fas fa-lock text-acad-cyan/80 shrink-0"></i>
                    <span><?php echo e(__('auth.two_factor_security_note')); ?></span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\auth\two-factor\challenge.blade.php ENDPATH**/ ?>