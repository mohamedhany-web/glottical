<?php $isRtl = app()->getLocale() === 'ar'; ?>
<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(__('auth.forgot_password')); ?> — <?php echo e(config('app.name')); ?></title>
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
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"></noscript>

    <style>
        *{font-family:'Cairo','IBM Plex Sans Arabic','Tajawal',system-ui,sans-serif;margin:0;padding:0;box-sizing:border-box}
        h1,h2,h3,h4,.font-heading{font-family:'Cairo','Tajawal','IBM Plex Sans Arabic',sans-serif}
        html,body{height:100%;overflow:hidden}
        @media(max-width:1023px){html,body{overflow:auto;height:auto}}

        @keyframes float-slow{0%,100%{transform:translateY(0) rotate(0deg)}50%{transform:translateY(-18px) rotate(2deg)}}
        @keyframes float-delayed{0%,100%{transform:translateY(0)}50%{transform:translateY(-12px) rotate(-1.5deg)}}
        .float-slow{animation:float-slow 8s ease-in-out infinite}
        .float-delayed{animation:float-delayed 10s ease-in-out infinite 2s}

        .text-gradient{background:linear-gradient(135deg,#FB5607 0%,#283593 70%,#1F2A7A 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}

        .input-field{background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:14px;padding:14px 16px;font-size:15px;font-weight:500;color:#0f172a;transition:all .25s ease;width:100%}
        .input-field:hover{border-color:#cbd5e1;background:#f1f5f9}
        .input-field:focus{outline:none;border-color:#283593;box-shadow:0 0 0 3px rgba(40,53,147,.12);background:#fff}
        .input-field::placeholder{color:#94a3b8}
        .input-field.has-error{border-color:#ef4444}
        .input-field.has-error:focus{box-shadow:0 0 0 3px rgba(239,68,68,.12)}

        .btn-login{position:relative;overflow:hidden;background:#FB5607;color:#fff;border:none;border-radius:14px;padding:15px;font-size:16px;font-weight:700;cursor:pointer;transition:all .3s ease;width:100%}
        .btn-login:hover{transform:translateY(-1px);box-shadow:0 12px 32px -8px rgba(251,86,7,.4)}
        .btn-login::before{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.15),transparent);transition:left .5s}
        .btn-login:hover::before{left:100%}
    </style>
</head>
<body class="bg-white">
    <div class="flex min-h-screen lg:h-screen">

        
        <div class="hidden lg:flex lg:w-[55%] relative items-center justify-center overflow-hidden" style="background:radial-gradient(circle at 12% 80%,rgba(255,229,247,.45),transparent 32%),radial-gradient(circle at 88% 20%,rgba(40,53,147,.12),transparent 34%),linear-gradient(180deg,#f4f6ff 0%,#fbfbff 60%,#ffffff 100%)">
            <div class="absolute inset-0 opacity-40" style="background-image:radial-gradient(circle at 1px 1px,rgba(40,53,147,.08) 1px,transparent 0);background-size:30px 30px"></div>
            <div class="absolute top-[-15%] <?php echo e($isRtl?'left-[-8%]':'right-[-8%]'); ?> w-[500px] h-[500px] rounded-full bg-[#283593]/10 blur-[100px] float-slow"></div>
            <div class="absolute bottom-[-10%] <?php echo e($isRtl?'right-[-5%]':'left-[-5%]'); ?> w-[400px] h-[400px] rounded-full bg-[#FB5607]/10 blur-[80px] float-delayed"></div>

            <div class="relative z-10 max-w-md px-10 text-center">
                <?php echo $__env->make('partials.auth-brand-link', ['size' => 'lg'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                <h1 class="font-heading text-3xl xl:text-4xl font-black text-mx-indigo leading-tight mb-5">
                    استعادة الوصول
                    <br><span class="text-gradient">لحسابك بأمان</span>
                </h1>
                <p class="text-slate-600 text-base leading-relaxed mb-10">
                    أدخل بريدك المسجّل وسنرسل لك رابطاً لإعادة تعيين كلمة المرور. الرابط صالح لمدة محدودة.
                </p>

                <div class="space-y-4">
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
                        <span class="w-10 h-10 rounded-xl bg-[#FFE5F7] flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-envelope-open-text text-[#283593]"></i>
                        </span>
                        <div class="text-<?php echo e($isRtl?'right':'left'); ?>">
                            <p class="text-mx-indigo font-bold text-sm">تحقق من صندوق الوارد</p>
                            <p class="text-slate-500 text-xs">والبريد غير المرغوب (Spam) أحياناً</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
                        <span class="w-10 h-10 rounded-xl bg-[#EFF2FF] flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-shield-halved text-[#FB5607]"></i>
                        </span>
                        <div class="text-<?php echo e($isRtl?'right':'left'); ?>">
                            <p class="text-mx-indigo font-bold text-sm">رابط آمن لمرة واحدة</p>
                            <p class="text-slate-500 text-xs">لا تشارك الرابط مع أحد</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
                        <span class="w-10 h-10 rounded-xl bg-[#FFF7ED] flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-key text-[#FB5607]"></i>
                        </span>
                        <div class="text-<?php echo e($isRtl?'right':'left'); ?>">
                            <p class="text-mx-indigo font-bold text-sm">كلمة مرور قوية</p>
                            <p class="text-slate-500 text-xs">بعد الدخول يُفضّل تفعيل التحقق بخطوتين إن توفر</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="flex-1 flex flex-col items-center justify-center px-5 sm:px-8 py-10 lg:py-0 bg-white relative overflow-y-auto">

            <div class="lg:hidden w-full max-w-md">
                <?php echo $__env->make('partials.auth-brand-link', ['size' => 'sm', 'fallback' => 'gradient', 'mb' => 'mb-8'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>

            <div class="w-full max-w-md">
                <div class="text-center lg:text-<?php echo e($isRtl?'right':'left'); ?> mb-8">
                    <h2 class="font-heading text-2xl sm:text-3xl font-black text-mx-indigo mb-2">
                        <?php echo e(__('auth.forgot_password')); ?>

                    </h2>
                    <p class="text-slate-500 text-sm sm:text-base"><?php echo e(__('auth.forgot_password_help')); ?></p>
                </div>

                <?php if(session('status')): ?>
                    <div class="mb-6 flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
                        <i class="fas fa-check-circle text-emerald-500 flex-shrink-0"></i>
                        <?php echo e(session('status')); ?>

                    </div>
                <?php endif; ?>

                <?php if($errors->any()): ?>
                    <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-sm font-medium">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <p><?php echo e($err); ?></p>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('password.email')); ?>" class="space-y-5">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label for="email" class="block text-sm font-bold text-navy-950 mb-2"><?php echo e(__('auth.email')); ?></label>
                        <div class="relative">
                            <span class="absolute <?php echo e($isRtl?'right-4':'left-4'); ?> top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fas fa-envelope text-sm"></i></span>
                            <input type="email" name="email" id="email" value="<?php echo e(old('email')); ?>" required autocomplete="email" autofocus
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
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1.5 text-xs text-red-500 font-medium"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <button type="submit" class="btn-login flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane text-sm"></i>
                        <span><?php echo e(__('auth.send_reset_link')); ?></span>
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                    <a href="<?php echo e(route('login')); ?>" class="inline-flex items-center gap-2 text-sm font-bold text-[#283593] hover:text-[#1F2A7A] transition-colors">
                        <i class="fas fa-arrow-<?php echo e($isRtl?'right':'left'); ?> text-xs"></i>
                        <?php echo e(__('auth.go_to_login')); ?>

                    </a>
                </div>

                <div class="mt-6 text-center">
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
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\auth\forgot-password.blade.php ENDPATH**/ ?>