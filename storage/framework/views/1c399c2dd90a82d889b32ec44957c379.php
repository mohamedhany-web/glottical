<?php $isRtl = app()->getLocale() === 'ar'; ?>
<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>التحقق بخطوتين — <?php echo e(config('app.name', 'Muallimx')); ?></title>
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

        .input-2fa{font-size:1.35rem;font-weight:800;letter-spacing:0.35em;text-align:center;padding-top:1rem;padding-bottom:1rem}

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
            <div class="absolute top-[-15%] <?php echo e($isRtl ? 'left-[-8%]' : 'right-[-8%]'); ?> w-[500px] h-[500px] rounded-full bg-[#283593]/10 blur-[100px] float-slow"></div>
            <div class="absolute bottom-[-10%] <?php echo e($isRtl ? 'right-[-5%]' : 'left-[-5%]'); ?> w-[400px] h-[400px] rounded-full bg-[#FB5607]/10 blur-[80px] float-delayed"></div>

            <div class="relative z-10 max-w-md px-10 text-center">
                <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center gap-3 mb-10 group">
                    <div class="w-12 h-12 rounded-xl bg-[#FB5607] flex items-center justify-center shadow-lg shadow-orange-500/25 group-hover:shadow-orange-500/40 transition-shadow">
                        <span class="text-white font-black text-xl">M</span>
                    </div>
                    <span class="text-mx-indigo font-extrabold text-2xl">Muallimx</span>
                </a>

                <h1 class="font-heading text-3xl xl:text-4xl font-black text-mx-indigo leading-tight mb-5">
                    تحقق من هويتك
                    <br><span class="text-gradient">بخطوة أمان سريعة</span>
                </h1>
                <p class="text-slate-600 text-base leading-relaxed mb-10">
                    هذه الخطوة تحمي حسابك وتتماشى مع نفس تجربة المنصة التي تراها في الصفحة الرئيسية وتسجيل الدخول.
                </p>

                <div class="space-y-4">
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
                        <span class="w-10 h-10 rounded-xl bg-[#FFE5F7] flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-envelope text-[#FB5607]"></i>
                        </span>
                        <div class="text-<?php echo e($isRtl ? 'right' : 'left'); ?>">
                            <p class="text-mx-indigo font-bold text-sm">رمز إلى بريدك</p>
                            <p class="text-slate-500 text-xs">صالح لدقائق — راجع البريد المزعج إن لزم</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
                        <span class="w-10 h-10 rounded-xl bg-[#EFF2FF] flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-shield-halved text-[#283593]"></i>
                        </span>
                        <div class="text-<?php echo e($isRtl ? 'right' : 'left'); ?>">
                            <p class="text-mx-indigo font-bold text-sm">حماية الجلسة</p>
                            <p class="text-slate-500 text-xs">لا يُفتح لوحة التحكم دون الرمز الصحيح</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
                        <span class="w-10 h-10 rounded-xl bg-[#FFF7ED] flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-fingerprint text-[#FB5607]"></i>
                        </span>
                        <div class="text-<?php echo e($isRtl ? 'right' : 'left'); ?>">
                            <p class="text-mx-indigo font-bold text-sm">هوية Muallimx</p>
                            <p class="text-slate-500 text-xs">ألوان وتجربة موحّدة مع بقية الموقع</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="flex-1 flex flex-col items-center justify-center px-5 sm:px-8 py-10 lg:py-0 bg-white relative overflow-y-auto">
            <div class="lg:hidden w-full max-w-md mb-8">
                <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-400 to-blue-600 flex items-center justify-center shadow-lg">
                        <span class="text-white font-black text-lg">M</span>
                    </div>
                    <span class="text-navy-950 font-extrabold text-xl" style="font-family:Tajawal,sans-serif">Muallimx</span>
                </a>
            </div>

            <div class="w-full max-w-md">
                <div class="text-center lg:text-<?php echo e($isRtl ? 'right' : 'left'); ?> mb-8">
                    <div class="inline-flex lg:hidden items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-[#283593] to-[#1F2A7A] text-white shadow-lg shadow-indigo-500/25 mb-4">
                        <i class="fas fa-shield-halved text-xl"></i>
                    </div>
                    <h2 class="font-heading text-2xl sm:text-3xl font-black text-mx-indigo mb-2">
                        التحقق بخطوتين
                    </h2>
                    <?php if(!empty($useEmail)): ?>
                        <p class="text-slate-500 text-sm sm:text-base leading-relaxed">
                            أرسلنا رمزاً مكوّناً من <strong class="text-slate-700">6 أرقام</strong> إلى بريدك. أدخل الرمز للمتابعة.
                        </p>
                    <?php else: ?>
                        <p class="text-slate-500 text-sm sm:text-base leading-relaxed">
                            أدخل الرمز المكوّن من 6 أرقام من تطبيق المصادقة أو البريد.
                        </p>
                    <?php endif; ?>
                </div>

                <?php if($errors->has('code')): ?>
                    <div class="flex items-center gap-3 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-medium mb-5">
                        <i class="fas fa-circle-exclamation text-rose-500 flex-shrink-0"></i>
                        <?php echo e($errors->first('code')); ?>

                    </div>
                <?php endif; ?>

                <form action="<?php echo e(route('two-factor.verify')); ?>" method="POST" class="space-y-5">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label for="code" class="block text-sm font-bold text-navy-950 mb-2">رمز التحقق</label>
                        <div class="relative">
                            <span class="absolute <?php echo e($isRtl ? 'right-4' : 'left-4'); ?> top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fas fa-key text-sm"></i></span>
                            <input type="text"
                                   name="code"
                                   id="code"
                                   inputmode="numeric"
                                   pattern="[0-9]*"
                                   maxlength="10"
                                   autocomplete="one-time-code"
                                   autofocus
                                   required
                                   class="input-field input-2fa <?php echo e($isRtl ? 'pr-11' : 'pl-11'); ?> <?php $__errorArgs = ['code'];
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
                    <button type="submit" class="btn-login flex items-center justify-center gap-2">
                        <span>تأكيد والمتابعة</span>
                        <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?> text-sm"></i>
                    </button>
                </form>

                <?php if(empty($useEmail)): ?>
                    <p class="text-xs text-slate-500 mt-6 text-center leading-relaxed">
                        إذا فقدت جهازك، استخدم أحد <strong class="text-slate-600">رموز الاسترداد</strong> التي حصلت عليها عند التفعيل.
                    </p>
                <?php else: ?>
                    <p class="text-xs text-slate-500 mt-6 text-center leading-relaxed">
                        لم يصلك الرمز؟ تحقق من البريد المزعج، أو
                        <a href="<?php echo e(route('login')); ?>" class="font-bold text-[#283593] hover:text-[#1F2A7A]">أعد تسجيل الدخول</a>
                        لإرسال رمز جديد.
                    </p>
                <?php endif; ?>

                <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                    <a href="<?php echo e(route('login')); ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-[#283593] hover:text-[#1F2A7A] transition-colors">
                        <i class="fas fa-arrow-<?php echo e($isRtl ? 'right' : 'left'); ?> text-xs"></i>
                        العودة لتسجيل الدخول
                    </a>
                </div>
                <div class="mt-4 text-center">
                    <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fas fa-house text-xs"></i>
                        الصفحة الرئيسية
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\auth\two-factor\challenge.blade.php ENDPATH**/ ?>