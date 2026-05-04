<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $itemTitle = isset($course) ? ($course->title ?? 'الكورس') : (isset($learningPath) ? ($learningPath->name ?? 'الطلب') : 'الطلب');
    $thumbUrl = null;
    if (isset($course) && ($course->thumbnail ?? null)) {
        $thumbUrl = asset('storage/' . str_replace('\\', '/', $course->thumbnail));
    }
    $platformLogoUrl = $platformLogoUrl ?? \App\Services\AdminPanelBranding::logoPublicUrl();
    $appName = config('app.name', 'Muallimx');
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title>إتمام الطلب - <?php echo e($itemTitle); ?> - <?php echo e(config('app.name')); ?></title>
    <meta name="theme-color" content="#283593">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config={theme:{extend:{colors:{navy:{50:'#f0f4ff',100:'#dbe4ff',200:'#bac8ff',300:'#91a7ff',400:'#748ffc',500:'#5c7cfa',600:'#4c6ef5',700:'#4263eb',800:'#3b5bdb',900:'#364fc7',950:'#283593'},brand:{50:'#FFF3E0',100:'#FFE0B2',200:'#FFCC80',300:'#FFB74D',400:'#FFA726',500:'#FB5607',600:'#E04D00',700:'#BF360C',800:'#8D2600',900:'#5D1A00'},mx:{navy:'#283593',indigo:'#1F2A7A',orange:'#FB5607',rose:'#FFE5F7',gold:'#FFE569',soft:'#F7F8FF',cream:'#FFF7ED'}},fontFamily:{heading:['Cairo','Tajawal','IBM Plex Sans Arabic','sans-serif'],body:['Cairo','IBM Plex Sans Arabic','Tajawal','sans-serif']}}}}
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        [x-cloak]{display:none!important}
        *{font-family:'Cairo','IBM Plex Sans Arabic','Tajawal',system-ui,sans-serif}
        h1,h2,h3,h4,h5,h6,.font-heading{font-family:'Cairo','Tajawal','IBM Plex Sans Arabic',sans-serif}
        html{scroll-behavior:smooth;overflow-x:hidden!important}
        body{overflow-x:hidden!important;background:#fff;min-height:100vh;display:flex;flex-direction:column}
        body>*{flex-shrink:0}
        .container-1200{max-width:1200px;margin-inline:auto;padding-inline:24px}
        @media (max-width:768px){.container-1200{padding-inline:16px}}
        .reveal{opacity:0;transform:translateY(40px);transition:opacity .8s cubic-bezier(.16,1,.3,1),transform .8s cubic-bezier(.16,1,.3,1)}
        .reveal.revealed{opacity:1;transform:translateY(0)}
        .stagger-1{transition-delay:.05s}.stagger-2{transition-delay:.1s}.stagger-3{transition-delay:.15s}
        @media(max-width:768px){.reveal{transition-duration:.5s}.stagger-1,.stagger-2,.stagger-3{transition-delay:0s}}
        .btn-primary{position:relative;overflow:hidden;transition:all .4s cubic-bezier(.16,1,.3,1)}
        .btn-primary::before{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.2),transparent);transition:left .6s}
        .btn-primary:hover::before{left:100%}
        .btn-primary:hover{transform:translateY(-2px);box-shadow:0 20px 40px -12px rgba(251,86,7,.35)}
        .btn-outline{transition:all .3s cubic-bezier(.16,1,.3,1)}
        .btn-outline:hover{transform:translateY(-2px);box-shadow:0 10px 30px -10px rgba(15,23,42,.2)}
        .card-checkout{transition:all .4s cubic-bezier(.16,1,.3,1)}
        .card-checkout:hover{box-shadow:0 25px 50px -20px rgba(31,42,122,.18)}
        .line-clamp-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
        #scroll-progress{position:fixed;top:0;left:0;width:0%;height:3px;background:linear-gradient(90deg,#FB5607,#FFE569);z-index:9999;transition:width .1s linear}
        #navbar,#navbar.nav-transparent,#navbar.nav-solid{background:rgba(31,42,122,.92)!important;backdrop-filter:blur(12px)!important;-webkit-backdrop-filter:blur(12px)!important;border-bottom:1px solid rgba(255,255,255,.08)!important}
        .navbar-spacer{display:block!important}
        .input-checkout{width:100%;border-radius:1rem;border:1px solid #e2e8f0;padding:.875rem 1rem;transition:border-color .2s,box-shadow .2s}
        .input-checkout:focus{outline:none;border-color:#1F2A7A;box-shadow:0 0 0 3px rgba(40,53,147,.12)}
        .checkout-steps{display:flex;flex-direction:column;gap:1.25rem}
        @media(min-width:640px){.checkout-steps{flex-direction:row;align-items:center;gap:0}}
        .checkout-step-connector{display:none}
        @media(min-width:640px){.checkout-step-connector{display:block;flex:1;min-width:1rem;height:2px;background:linear-gradient(90deg,#cbd5e1,#e2e8f0);align-self:center;border-radius:1px}}
    </style>
</head>
<body class="bg-white text-slate-800 antialiased font-body" x-data="{ isSubmitting: false }">
    <div id="scroll-progress"></div>
    <?php echo $__env->make('components.unified-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="flex-1">
        
        <section class="pt-10 sm:pt-14 lg:pt-16 pb-10 sm:pb-12 overflow-hidden relative" style="background:radial-gradient(circle at 12% 80%,rgba(255,229,247,.65),transparent 28%),radial-gradient(circle at 88% 20%,rgba(40,53,147,.10),transparent 30%),linear-gradient(180deg,#f4f6ff 0%,#fbfbff 55%,#ffffff 100%)">
            <div class="absolute inset-0 pointer-events-none opacity-40" style="background-image:radial-gradient(circle at 1px 1px,rgba(40,53,147,.08) 1px,transparent 0);background-size:30px 30px"></div>
            <div class="container-1200 relative z-10">
                
                <div class="reveal flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 mb-6 border-b border-slate-200/70">
                    <div class="flex items-center gap-4 min-w-0">
                        <?php if($platformLogoUrl): ?>
                            <a href="<?php echo e(url('/')); ?>" class="shrink-0 rounded-xl bg-white/90 p-2 ring-1 ring-slate-200/80 shadow-sm hover:ring-mx-navy/20 transition-shadow" title="<?php echo e($appName); ?>">
                                <img src="<?php echo e($platformLogoUrl); ?>" alt="<?php echo e($appName); ?>" class="h-10 sm:h-11 w-auto max-w-[200px] object-contain object-center" width="200" height="44" loading="eager" decoding="async">
                            </a>
                        <?php else: ?>
                            <a href="<?php echo e(url('/')); ?>" class="shrink-0 flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-mx-navy to-mx-indigo text-white font-black text-lg shadow-md ring-1 ring-white/20" title="<?php echo e($appName); ?>"><?php echo e(Str::substr($appName, 0, 1)); ?></a>
                        <?php endif; ?>
                        <div class="min-w-0 border-slate-200 sm:border-s sm:ps-4">
                            <p class="text-[11px] uppercase tracking-wider text-slate-500 font-bold"><?php echo e(__('public.checkout_brand_kicker')); ?></p>
                            <p class="font-heading text-lg sm:text-xl font-black text-mx-indigo truncate"><?php echo e($appName); ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-slate-600 shrink-0">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-white border border-slate-200 text-mx-navy"><i class="fas fa-bag-shopping"></i></span>
                        <span class="font-semibold text-navy-950"><?php echo e(__('public.checkout_page_label')); ?></span>
                    </div>
                </div>

                <nav class="reveal text-sm text-slate-500 mb-8 flex items-center gap-2 flex-wrap" aria-label="مسار التنقل">
                    <a href="<?php echo e(url('/')); ?>" class="hover:text-mx-indigo transition-colors"><?php echo e(__('public.home')); ?></a>
                    <i class="fas fa-chevron-<?php echo e($isRtl ? 'left' : 'right'); ?> text-[8px] text-slate-400"></i>
                    <a href="<?php echo e(route('public.courses')); ?>" class="hover:text-mx-indigo transition-colors"><?php echo e(__('public.courses')); ?></a>
                    <?php if(isset($course)): ?>
                        <i class="fas fa-chevron-<?php echo e($isRtl ? 'left' : 'right'); ?> text-[8px] text-slate-400"></i>
                        <a href="<?php echo e(route('public.course.show', $course->id)); ?>" class="hover:text-mx-indigo transition-colors"><?php echo e(Str::limit($course->title ?? '', 36)); ?></a>
                    <?php endif; ?>
                    <i class="fas fa-chevron-<?php echo e($isRtl ? 'left' : 'right'); ?> text-[8px] text-slate-400"></i>
                    <span class="text-mx-indigo font-semibold"><?php echo e(__('public.checkout_breadcrumb_current')); ?></span>
                </nav>

                
                <div class="checkout-steps reveal mb-10 lg:mb-12" aria-label="<?php echo e(__('public.checkout_steps_label')); ?>">
                    <div class="flex items-center gap-3 shrink-0">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white text-sm font-black shadow-sm ring-2 ring-emerald-200">1</span>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-emerald-700"><?php echo e(__('public.checkout_step_1_title')); ?></p>
                            <p class="text-[11px] text-slate-500 leading-snug"><?php echo e(__('public.checkout_step_1_desc')); ?></p>
                        </div>
                    </div>
                    <span class="checkout-step-connector mx-2 sm:mx-3" aria-hidden="true"></span>
                    <div class="flex items-center gap-3 shrink-0">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-brand-600 text-white text-sm font-black shadow-md ring-2 ring-orange-200">2</span>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-brand-700"><?php echo e(__('public.checkout_step_2_title')); ?></p>
                            <p class="text-[11px] text-slate-500 leading-snug"><?php echo e(__('public.checkout_step_2_desc')); ?></p>
                        </div>
                    </div>
                    <span class="checkout-step-connector mx-2 sm:mx-3" aria-hidden="true"></span>
                    <div class="flex items-center gap-3 shrink-0">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-200 text-slate-600 text-sm font-black">3</span>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-600"><?php echo e(__('public.checkout_step_3_title')); ?></p>
                            <p class="text-[11px] text-slate-500 leading-snug"><?php echo e(__('public.checkout_step_3_desc')); ?></p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-12 items-center">
                    <div class="lg:col-span-3 reveal">
                        <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-bold mb-5 shadow-sm" style="background:#FFE5F7;color:#283593;border:1px solid #f5c7e8">
                            <i class="fas fa-credit-card"></i>
                            <?php echo e(isset($course) ? __('public.secure_checkout_badge') : 'صفحة الدفع'); ?>

                        </span>
                        <h1 class="font-heading text-3xl sm:text-4xl lg:text-[2.75rem] font-black text-mx-indigo leading-[1.15] mb-4">
                            إتمام الطلب
                        </h1>
                        <p class="text-slate-600 text-base sm:text-lg leading-relaxed max-w-2xl mb-6">
                            خطوة أخيرة لشراء
                            <span class="font-bold text-mx-indigo"><?php echo e($itemTitle); ?></span>
                            — نفس تجربة المنصة الآمنة والواضحة.
                        </p>
                        <div class="flex flex-wrap gap-3">
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-mx-indigo text-sm font-semibold shadow-sm">
                                <i class="fas fa-shield-halved text-brand-500"></i>
                                دفع موثوق
                            </span>
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-mx-indigo text-sm font-semibold shadow-sm">
                                <i class="fas fa-bolt text-amber-500"></i>
                                تفعيل سريع بعد الموافقة
                            </span>
                        </div>
                    </div>
                    <div class="lg:col-span-2 reveal stagger-2">
                        <?php if($thumbUrl): ?>
                            <div class="card-checkout rounded-3xl overflow-hidden border border-slate-200 shadow-[0_10px_24px_-18px_rgba(31,42,122,.25)] ring-1 ring-slate-200/80 aspect-[4/3] bg-slate-100">
                                <img src="<?php echo e($thumbUrl); ?>" alt="" class="w-full h-full object-cover">
                            </div>
                        <?php else: ?>
                            <div class="card-checkout rounded-3xl border border-slate-200 shadow-[0_10px_24px_-18px_rgba(31,42,122,.25)] ring-1 ring-slate-200/80 p-8 flex flex-col justify-center min-h-[220px]" style="background:linear-gradient(145deg,#fff 0%,#f4f6ff 50%,#fff5f0 100%)">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-500 to-mx-navy flex items-center justify-center text-white text-2xl shadow-lg mb-4">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <p class="font-heading text-xl font-black text-mx-indigo leading-snug"><?php echo e(Str::limit($itemTitle, 80)); ?></p>
                                <p class="text-sm text-slate-500 mt-2"><?php echo e(isset($course) ? ($course->academicSubject->name ?? '') : ''); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        
        <section class="py-12 md:py-20 bg-mx-soft">
            <div class="container-1200">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 xl:gap-12">
                    
                    <aside class="lg:col-span-4 xl:col-span-4 order-2 lg:order-1">
                        <div class="reveal card-checkout sticky top-24 rounded-3xl bg-white border border-slate-200 p-6 sm:p-8 shadow-[0_10px_24px_-18px_rgba(31,42,122,.25)] ring-1 ring-slate-200/80">
                            <div class="flex items-center justify-between gap-3 mb-5 pb-5 border-b border-slate-100">
                                <div class="flex items-center gap-3 min-w-0">
                                    <?php if($platformLogoUrl): ?>
                                        <img src="<?php echo e($platformLogoUrl); ?>" alt="" class="h-8 sm:h-9 w-auto max-w-[120px] object-contain opacity-95 shrink-0" loading="lazy" decoding="async">
                                    <?php else: ?>
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-mx-soft text-mx-navy font-black text-sm"><?php echo e(Str::substr($appName, 0, 1)); ?></span>
                                    <?php endif; ?>
                                    <div class="min-w-0">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide"><?php echo e($appName); ?></p>
                                        <p class="text-xs text-slate-500 truncate"><?php echo e(__('public.checkout_summary_subtitle')); ?></p>
                                    </div>
                                </div>
                                <span class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center text-brand-500 shrink-0"><i class="fas fa-receipt"></i></span>
                            </div>
                            <h3 class="font-heading text-lg font-black text-navy-950 mb-6">
                                <?php echo e(__('public.checkout_order_summary_title')); ?>

                            </h3>
                            <div class="flex items-start gap-4 mb-6 pb-6 border-b border-slate-100">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-500 to-mx-navy flex items-center justify-center flex-shrink-0 shadow-lg shadow-brand-600/20">
                                    <?php if(isset($course)): ?>
                                        <i class="fas fa-graduation-cap text-white text-xl"></i>
                                    <?php else: ?>
                                        <i class="fas fa-route text-white text-xl"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-navy-950 text-base line-clamp-2 leading-snug">
                                        <?php if(isset($course)): ?><?php echo e($course->title); ?><?php elseif(isset($learningPath)): ?><?php echo e($learningPath->name); ?><?php else: ?> الطلب <?php endif; ?>
                                    </h4>
                                    <p class="text-sm text-slate-500 mt-1">
                                        <?php if(isset($course)): ?><?php echo e($course->academicSubject->name ?? 'غير محدد'); ?><?php else: ?> مسار تعليمي شامل <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <?php
                                $baseCoursePrice = isset($course) ? (float) $course->effectivePurchasePrice() : (float) (isset($learningPath) ? ($learningPath->price ?? 0) : 0);
                                $studentBal = isset($studentWalletBalance) ? (float) $studentWalletBalance : 0;
                            ?>
                            <div class="space-y-3 mb-6" id="checkout-pricing-summary"
                                 data-base-price="<?php echo e($baseCoursePrice); ?>"
                                 data-student-balance="<?php echo e($studentBal); ?>"
                                 data-has-course="<?php echo e(isset($course) ? '1' : '0'); ?>">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-slate-600">السعر الأساسي</span>
                                    <span class="font-bold text-brand-600 text-lg" id="sum-original">
                                        <?php echo e(number_format($baseCoursePrice, 2)); ?>

                                        <span class="text-slate-500 text-sm font-medium"><?php echo e(__('public.currency_egp')); ?></span>
                                    </span>
                                </div>
                                <div class="hidden flex justify-between items-center text-sm text-emerald-700" id="sum-coupon-row">
                                    <span>خصم الكوبون</span>
                                    <span class="font-bold" id="sum-coupon">—</span>
                                </div>
                                <div class="hidden flex justify-between items-center text-sm text-sky-700" id="sum-wallet-row">
                                    <span>رصيد المحفظة</span>
                                    <span class="font-bold" id="sum-wallet">—</span>
                                </div>
                                <div class="flex justify-between items-center pt-4 border-t-2 border-slate-100">
                                    <span class="font-bold text-navy-950">المستحق للدفع</span>
                                    <span class="text-2xl font-black text-brand-600" id="sum-final">
                                        <?php echo e(number_format($baseCoursePrice, 2)); ?>

                                        <span class="text-slate-500 text-base font-medium"><?php echo e(__('public.currency_egp')); ?></span>
                                    </span>
                                </div>
                            </div>
                            <ul class="space-y-3 text-sm text-slate-600">
                                <li class="flex items-center gap-3"><span class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0"><i class="fas fa-check text-xs"></i></span> وصول مدى الحياة</li>
                                <li class="flex items-center gap-3"><span class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0"><i class="fas fa-check text-xs"></i></span> شهادة إتمام</li>
                                <li class="flex items-center gap-3"><span class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0"><i class="fas fa-check text-xs"></i></span> دعم فني</li>
                            </ul>
                        </div>
                    </aside>

                    
                    <div class="lg:col-span-8 xl:col-span-8 order-1 lg:order-2">
                        <div class="reveal card-checkout rounded-3xl bg-white border border-slate-200 p-6 sm:p-8 lg:p-10 shadow-[0_10px_24px_-18px_rgba(31,42,122,.25)] ring-1 ring-slate-200/80">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-8 pb-6 border-b border-slate-100">
                                <div class="flex items-start gap-4">
                                    <span class="w-12 h-12 rounded-2xl bg-mx-navy/10 flex items-center justify-center text-mx-navy text-xl shrink-0"><i class="fas fa-wallet"></i></span>
                                    <div>
                                        <h2 class="font-heading text-2xl sm:text-3xl font-black text-navy-950 leading-tight"><?php echo e(__('public.checkout_payment_section_title')); ?></h2>
                                        <p class="text-slate-500 text-sm mt-2 max-w-xl leading-relaxed"><?php echo e(__('public.checkout_payment_section_desc')); ?></p>
                                    </div>
                                </div>
                                <?php if($platformLogoUrl): ?>
                                    <div class="hidden sm:flex items-center justify-center rounded-2xl bg-mx-soft px-4 py-3 ring-1 ring-slate-200/60 shrink-0">
                                        <img src="<?php echo e($platformLogoUrl); ?>" alt="" class="h-7 w-auto max-w-[100px] object-contain opacity-90" loading="lazy">
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if(session('error')): ?>
                                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 flex items-start gap-3 shadow-sm">
                                    <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
                                    <p class="text-red-800 text-sm font-semibold flex-1"><?php echo e(session('error')); ?></p>
                                </div>
                            <?php endif; ?>
                            <?php if($errors->any()): ?>
                                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 shadow-sm">
                                    <ul class="list-disc list-inside space-y-1 text-red-800 text-sm font-medium">
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            <?php if(session('success')): ?>
                                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 flex items-center gap-3 shadow-sm">
                                    <i class="fas fa-check-circle text-emerald-600"></i>
                                    <p class="text-emerald-800 text-sm font-semibold"><?php echo e(session('success')); ?></p>
                                </div>
                            <?php endif; ?>
                            <?php if(session('info')): ?>
                                <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 flex items-center gap-3 shadow-sm">
                                    <i class="fas fa-info-circle text-amber-600"></i>
                                    <p class="text-amber-900 text-sm font-semibold"><?php echo e(session('info')); ?></p>
                                </div>
                            <?php endif; ?>

                            <?php if(isset($course)): ?>
                                <?php
                                    $checkoutHasWalletBalance = isset($studentWalletBalance) && (float) $studentWalletBalance > 0;
                                ?>
                                <div class="mb-8 rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-5 sm:p-6 shadow-sm space-y-4"
                                     id="checkout-discount-panel"
                                     data-quote-url="<?php echo e(route('public.course.checkout.quote', $course->id)); ?>"
                                     data-has-wallet="<?php echo e($checkoutHasWalletBalance ? '1' : '0'); ?>">
                                    <h3 class="font-heading text-lg font-black text-navy-950 flex items-center gap-2">
                                        <i class="fas fa-tags text-brand-500"></i>
                                        <?php if($checkoutHasWalletBalance): ?>
                                            كوبون الخصم ورصيد المحفظة
                                        <?php else: ?>
                                            كوبون الخصم
                                        <?php endif; ?>
                                    </h3>
                                    <p class="text-xs text-slate-600 leading-relaxed">
                                        <?php if($checkoutHasWalletBalance): ?>
                                            أضف كوبوناً صالحاً (من التسويق) و/أو استخدم رصيد محفظتك على المنصة. يُخصم الكوبون أولاً ثم رصيد المحفظة من المبلغ المتبقي.
                                        <?php else: ?>
                                            أدخل كوبوناً صالحاً من التسويق إن وُجد، ثم اضغط «تحديث السعر».
                                        <?php endif; ?>
                                    </p>
                                    <?php if($checkoutHasWalletBalance): ?>
                                        <p class="text-xs font-semibold text-sky-700">رصيد محفظتك الحالي: <?php echo e(number_format($studentWalletBalance, 2)); ?> <?php echo e(__('public.currency_egp')); ?></p>
                                    <?php endif; ?>
                                    <div class="grid grid-cols-1 <?php echo e($checkoutHasWalletBalance ? 'sm:grid-cols-2' : ''); ?> gap-4">
                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-1">كود الكوبون</label>
                                            <input type="text" id="checkout_coupon_code" dir="ltr" autocomplete="off"
                                                   class="input-checkout uppercase font-mono text-sm"
                                                   placeholder="مثال: SAVE10">
                                        </div>
                                        <?php if($checkoutHasWalletBalance): ?>
                                            <div>
                                                <label class="block text-sm font-bold text-slate-700 mb-1">مبلغ من المحفظة (ج.م)</label>
                                                <input type="number" id="checkout_wallet_credit" step="0.01" min="0"
                                                       value="0"
                                                       max="<?php echo e(max(0, $studentWalletBalance ?? 0)); ?>"
                                                       class="input-checkout">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <button type="button" id="checkout_apply_pricing"
                                                class="inline-flex items-center gap-2 rounded-xl bg-mx-navy text-white px-5 py-2.5 text-sm font-bold hover:opacity-95 transition-opacity">
                                            <i class="fas fa-rotate"></i> تحديث السعر
                                        </button>
                                        <span id="checkout_pricing_msg" class="text-sm font-medium text-slate-600 hidden"></span>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php
                                $fawaterakActive = !empty($fawaterakUseGateway) && isset($course);
                                $fawaterakMis = !empty($fawaterakMisconfigured) && isset($course);
                                $fawaterakIntegration = $fawaterakIntegration ?? 'iframe';
                            ?>

                            <?php if($fawaterakMis): ?>
                                <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 shadow-sm">
                                    <p class="text-sm font-bold text-rose-900 mb-2 flex items-center gap-2">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        إعدادات الدفع غير مكتملة
                                    </p>
                                    <p class="text-sm text-rose-800 leading-7">
                                        تم تفعيل بوابة فواتيرك من إعدادات النظام، لكن إعدادات الربط غير مكتملة على الخادم.
                                        <?php if($fawaterakIntegration === 'api'): ?>
                                            أضف
                                            <code class="text-xs bg-white/80 px-1 rounded" dir="ltr">FAWATERAK_API_TOKEN</code>
                                            (Bearer من لوحة فواتيرك) واختيارياً
                                            <code class="text-xs bg-white/80 px-1 rounded" dir="ltr">FAWATERAK_API_BASE_URL</code>
                                            إن اختلف عن العنوان الافتراضي للبيئة.
                                        <?php else: ?>
                                            أضف
                                            <code class="text-xs bg-white/80 px-1 rounded" dir="ltr">FAWATERAK_VENDOR_KEY</code>
                                            و
                                            <code class="text-xs bg-white/80 px-1 rounded" dir="ltr">FAWATERAK_PROVIDER_KEY</code>
                                            لوضع الإطار (IFrame).
                                        <?php endif; ?>
                                        ثم شغّل
                                        <code class="text-xs bg-white/80 px-1 rounded" dir="ltr">php artisan config:clear</code>.
                                    </p>
                                </div>
                                <a href="<?php echo e(route('orders.index')); ?>" class="btn-outline inline-flex items-center justify-center gap-2 bg-white border-2 border-slate-200 text-mx-indigo px-6 py-3.5 rounded-2xl font-bold">
                                    <i class="fas fa-arrow-<?php echo e($isRtl ? 'right' : 'left'); ?> text-sm"></i>
                                    رجوع
                                </a>
                            <?php elseif($fawaterakActive && $fawaterakIntegration === 'api'): ?>
                                <div class="mb-6 rounded-2xl border border-sky-200 bg-gradient-to-br from-sky-50 to-white px-5 py-4 shadow-sm">
                                    <p class="text-sm font-bold text-sky-900 mb-1 flex items-center gap-2">
                                        <i class="fas fa-lock text-sky-600"></i>
                                        الدفع الإلكتروني عبر فواتيرك (API)
                                    </p>
                                    <p class="text-sm text-sky-800/90 leading-relaxed">
                                        اختر وسيلة الدفع من القائمة ثم تابع؛ قد يُطلب التحويل لصفحة فواتيرك أو عرض رمز (فوري / محفظة) حسب الوسيلة.
                                    </p>
                                </div>
                                <div id="fawaterk-api-error" class="hidden mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 text-sm font-medium"></div>
                                <div id="fawaterk-api-loading" class="mb-6 flex items-center gap-3 text-slate-600 text-sm">
                                    <i class="fas fa-spinner fa-spin text-mx-navy"></i>
                                    جاري تحميل وسائل الدفع...
                                </div>
                                <div id="fawaterk-api-methods" class="hidden mb-6 grid grid-cols-1 sm:grid-cols-2 gap-3"></div>
                                <div id="fawaterk-api-wallet-wrap" class="hidden mb-6">
                                    <label class="block text-sm font-bold text-slate-700 mb-2">رقم المحفظة (ميزا / محافظ — عند الحاجة)</label>
                                    <input type="text" id="fawaterk-api-wallet" dir="ltr" class="input-checkout" placeholder="01xxxxxxxxx" autocomplete="tel">
                                </div>
                                <div id="fawaterk-api-result" class="hidden mb-6 rounded-2xl border border-slate-200 bg-slate-50 p-5 text-sm text-slate-800 space-y-2"></div>
                                <button type="button" id="fawaterk-api-pay-btn" disabled
                                        class="btn-primary w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-l from-brand-500 to-brand-600 text-white px-8 py-4 rounded-2xl font-bold shadow-xl shadow-brand-600/25 disabled:opacity-50 disabled:cursor-not-allowed mb-6">
                                    <i class="fas fa-arrow-left text-sm"></i>
                                    متابعة الدفع
                                </button>
                                <p class="text-xs text-slate-500 text-center mb-6 flex items-center justify-center gap-2">
                                    <i class="fas fa-shield-halved text-mx-navy"></i>
                                    الاتصال بفواتيرك من خادم المنصة — لا حاجة لتحميل سكربت خارجي على هذه الصفحة
                                </p>
                            <?php elseif($fawaterakActive): ?>
                                <div class="mb-6 rounded-2xl border border-sky-200 bg-gradient-to-br from-sky-50 to-white px-5 py-4 shadow-sm">
                                    <p class="text-sm font-bold text-sky-900 mb-1 flex items-center gap-2">
                                        <i class="fas fa-lock text-sky-600"></i>
                                        الدفع الإلكتروني عبر فواتيرك
                                    </p>
                                    <p class="text-sm text-sky-800/90 leading-relaxed">
                                        اختر وسيلة الدفع داخل الإطار أدناه. بعد نجاح العملية يُفعَّل الكورس على حسابك تلقائياً.
                                    </p>
                                </div>
                                <div id="fawaterk-checkout-error" class="hidden mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 text-sm font-medium"></div>
                                <div id="fawaterkDivId" class="min-h-[520px] w-full rounded-2xl border border-slate-200 bg-white shadow-inner overflow-hidden mb-6 ring-1 ring-slate-200/60"></div>
                                <p class="text-xs text-slate-500 text-center mb-6 flex items-center justify-center gap-2">
                                    <i class="fas fa-shield-halved text-mx-navy"></i>
                                    معالجة الدفع تتم عبر بوابة فواتيرك المعتمدة
                                </p>
                                <a href="<?php echo e(route('orders.index')); ?>" class="btn-outline inline-flex items-center justify-center gap-2 bg-white border-2 border-slate-200 text-mx-indigo px-6 py-3.5 rounded-2xl font-bold hover:border-slate-300">
                                    <i class="fas fa-arrow-<?php echo e($isRtl ? 'right' : 'left'); ?> text-sm"></i>
                                    رجوع
                                </a>
                            <?php else: ?>
                                <div class="mb-6 rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-white px-5 py-4 shadow-sm">
                                    <p class="text-sm font-bold text-amber-900 mb-1 flex items-center gap-2">
                                        <i class="fas fa-circle-info"></i>
                                        الدفع اليدوي
                                    </p>
                                    <p class="text-sm text-amber-900/85 leading-relaxed">
                                        ارفع إيصال التحويل وسيظهر الطلب في صفحة الطلبات حتى تتم مراجعته والموافقة عليه.
                                    </p>
                                </div>

                                <form action="<?php echo e(isset($course) ? route('public.course.checkout.complete', $course->id) : (isset($learningPath) ? route('public.learning-path.checkout.complete', Str::slug($learningPath->name)) : '#')); ?>" method="POST" enctype="multipart/form-data" @submit="isSubmitting = true" x-data="{paymentMethod:'bank_transfer'}" id="manual-checkout-form">
                                    <?php echo csrf_field(); ?>
                                    <?php if(isset($course)): ?>
                                        <input type="hidden" name="coupon_code" id="form_coupon_code" value="<?php echo e(old('coupon_code', '')); ?>">
                                        <input type="hidden" name="wallet_credit" id="form_wallet_credit" value="<?php echo e(old('wallet_credit', '0')); ?>">
                                    <?php endif; ?>
                                    <div class="space-y-5 mb-8">
                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-2">طريقة الدفع</label>
                                            <select name="payment_method" x-model="paymentMethod" class="input-checkout" required>
                                                <option value="bank_transfer">تحويل بنكي / محفظة</option>
                                                <option value="cash">دفع نقدي</option>
                                                <option value="other">طريقة أخرى</option>
                                            </select>
                                        </div>

                                        <div x-show="paymentMethod === 'bank_transfer'" x-cloak>
                                            <label class="block text-sm font-bold text-slate-700 mb-2">اختر حساب التحويل</label>
                                            <select name="wallet_id" class="input-checkout" :required="paymentMethod === 'bank_transfer'">
                                                <option value="">اختر الحساب</option>
                                                <?php $__currentLoopData = ($wallets ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wallet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($wallet->id); ?>">
                                                        <?php echo e($wallet->name ?? 'حساب منصة'); ?> — <?php echo e($wallet->account_number ?? $wallet->phone ?? 'بدون رقم'); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-2">إيصال الدفع</label>
                                            <input type="file" name="payment_proof" accept="image/*" required
                                                   class="w-full rounded-2xl border border-slate-200 px-4 py-3 file:me-3 file:rounded-xl file:border-0 file:bg-mx-rose file:px-4 file:py-2 file:text-sm file:font-semibold file:text-mx-navy hover:file:bg-pink-100 transition-colors">
                                            <p class="mt-2 text-xs text-slate-500">الصيغ المسموحة: JPG, PNG — حتى 40 ميجابايت.</p>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-bold text-slate-700 mb-2">ملاحظات (اختياري)</label>
                                            <textarea name="notes" rows="3" class="input-checkout resize-y min-h-[100px]" placeholder="أي تفاصيل إضافية عن التحويل"></textarea>
                                        </div>
                                    </div>

                                    <div class="flex flex-col sm:flex-row gap-4">
                                        <button type="submit" :disabled="isSubmitting"
                                                class="btn-primary flex-1 inline-flex items-center justify-center gap-2 bg-gradient-to-l from-brand-500 to-brand-600 text-white px-8 py-4 rounded-2xl font-bold shadow-xl shadow-brand-600/25 disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none">
                                            <i class="fas fa-file-upload" x-show="!isSubmitting"></i>
                                            <i class="fas fa-spinner fa-spin" x-show="isSubmitting" x-cloak></i>
                                            <span x-text="isSubmitting ? 'جاري إرسال الطلب...' : 'إرسال الطلب ورفع الإيصال'"></span>
                                        </button>
                                        <a href="<?php echo e(route('orders.index')); ?>"
                                           :class="{ 'pointer-events-none opacity-50': isSubmitting }"
                                           class="btn-outline inline-flex items-center justify-center gap-2 bg-white border-2 border-slate-200 text-mx-indigo px-8 py-4 rounded-2xl font-bold hover:bg-slate-50">
                                            <i class="fas fa-arrow-<?php echo e($isRtl ? 'right' : 'left'); ?>"></i>
                                            إلغاء
                                        </a>
                                    </div>
                                    <p class="mt-6 text-xs text-slate-500 text-center flex items-center justify-center gap-2">
                                        <i class="fas fa-shield-halved text-mx-navy"></i>
                                        يظهر الطلب في صفحة الطلبات ويتم التفعيل بعد الموافقة
                                    </p>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php echo $__env->make('components.unified-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script>
    (function(){
        function scrollProgress(){
            var s=window.pageYOffset||document.documentElement.scrollTop;
            var h=document.documentElement.scrollHeight-window.innerHeight;
            var b=document.getElementById('scroll-progress');
            if(b) b.style.width=(h>0?(s/h)*100:0)+'%';
        }
        window.addEventListener('scroll',scrollProgress,{passive:true});
        function initReveal(){
            var t=document.querySelectorAll('.reveal');
            if(!t.length)return;
            var o=new IntersectionObserver(function(e){
                e.forEach(function(n){if(n.isIntersecting){n.target.classList.add('revealed');o.unobserve(n.target);}});
            },{threshold:.08,rootMargin:'0px 0px -40px 0px'});
            t.forEach(function(el){o.observe(el);});
        }
        if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',function(){scrollProgress();initReveal();});
        else{scrollProgress();initReveal();}
    })();
    </script>
    <?php if(isset($course)): ?>
    <script>
    (function(){
        var panel = document.getElementById('checkout-discount-panel');
        var summary = document.getElementById('checkout-pricing-summary');
        if (!panel || !summary) return;
        var quoteUrl = panel.getAttribute('data-quote-url');
        var meta = document.querySelector('meta[name="csrf-token"]');
        var csrf = (meta && meta.getAttribute('content')) || '';
        function el(id){ return document.getElementById(id); }
        function fmt(n){
            var x = parseFloat(n);
            if (isNaN(x)) x = 0;
            return x.toLocaleString('ar-EG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        function setMsg(text, isErr) {
            var m = el('checkout_pricing_msg');
            if (!m) return;
            m.textContent = text || '';
            m.classList.toggle('hidden', !text);
            m.classList.toggle('text-red-600', !!isErr);
            m.classList.toggle('text-emerald-700', !isErr && !!text);
        }
        function updateSummary(data) {
            var base = parseFloat(summary.getAttribute('data-base-price')) || 0;
            if (!data || !data.ok) {
                el('sum-original').innerHTML = fmt(base) + ' <span class="text-slate-500 text-sm font-medium"><?php echo e(__('public.currency_egp')); ?></span>';
                el('sum-final').innerHTML = fmt(base) + ' <span class="text-slate-500 text-base font-medium"><?php echo e(__('public.currency_egp')); ?></span>';
                el('sum-coupon-row').classList.add('hidden');
                el('sum-wallet-row').classList.add('hidden');
                return;
            }
            el('sum-original').innerHTML = fmt(data.original_amount) + ' <span class="text-slate-500 text-sm font-medium"><?php echo e(__('public.currency_egp')); ?></span>';
            if (data.discount_amount > 0) {
                el('sum-coupon-row').classList.remove('hidden');
                el('sum-coupon').textContent = '− ' + fmt(data.discount_amount) + ' <?php echo e(__('public.currency_egp')); ?>';
            } else {
                el('sum-coupon-row').classList.add('hidden');
            }
            if (data.wallet_credit_amount > 0) {
                el('sum-wallet-row').classList.remove('hidden');
                el('sum-wallet').textContent = '− ' + fmt(data.wallet_credit_amount) + ' <?php echo e(__('public.currency_egp')); ?>';
            } else {
                el('sum-wallet-row').classList.add('hidden');
            }
            el('sum-final').innerHTML = fmt(data.final_amount) + ' <span class="text-slate-500 text-base font-medium"><?php echo e(__('public.currency_egp')); ?></span>';
        }
        function quote() {
            setMsg('', false);
            var fd = new FormData();
            fd.append('_token', csrf);
            var c = el('checkout_coupon_code');
            var w = el('checkout_wallet_credit');
            fd.append('coupon_code', c ? (c.value || '').trim() : '');
            fd.append('wallet_credit', w && w.value !== '' ? w.value : '0');
            return fetch(quoteUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: fd,
                credentials: 'same-origin'
            }).then(function(r){
                return r.json().then(function(j){ return { ok: r.ok, status: r.status, data: j }; });
            }).then(function(res){
                if (res.ok && res.data && res.data.ok) {
                    updateSummary(res.data);
                    setMsg('تم تحديث السعر.', false);
                    var hfC = el('form_coupon_code');
                    var hfW = el('form_wallet_credit');
                    if (hfC) hfC.value = (el('checkout_coupon_code').value || '').trim();
                    if (hfW) {
                        var wIn = el('checkout_wallet_credit');
                        hfW.value = wIn && wIn.value !== '' ? wIn.value : '0';
                    }
                    if (typeof window.muallimxOnCheckoutPricingUpdated === 'function') {
                        try { window.muallimxOnCheckoutPricingUpdated(res.data); } catch (e) {}
                    }
                    return res.data;
                }
                var msg = (res.data && res.data.message) ? res.data.message : 'تعذّر حساب السعر.';
                setMsg(msg, true);
                updateSummary(null);
                return null;
            }).catch(function(){
                setMsg('خطأ في الاتصال.', true);
                updateSummary(null);
                return null;
            });
        }
        var btn = el('checkout_apply_pricing');
        if (btn) btn.addEventListener('click', function(e){ e.preventDefault(); quote(); });
        var form = el('manual-checkout-form');
        if (form) {
            form.addEventListener('submit', function(){
                var c = el('checkout_coupon_code');
                var w = el('checkout_wallet_credit');
                if (el('form_coupon_code')) el('form_coupon_code').value = c ? (c.value || '').trim() : '';
                if (el('form_wallet_credit')) el('form_wallet_credit').value = w && w.value !== '' ? w.value : '0';
            });
        }
        var oc = el('checkout_coupon_code');
        var ow = el('checkout_wallet_credit');
        if (el('form_coupon_code') && el('form_coupon_code').value && oc) oc.value = el('form_coupon_code').value;
        if (ow && el('form_wallet_credit') && el('form_wallet_credit').value) ow.value = el('form_wallet_credit').value;
        quote();
    })();
    </script>
    <?php endif; ?>
    <?php if(!empty($fawaterakUseGateway) && isset($course) && empty($fawaterakMisconfigured) && ($fawaterakIntegration ?? 'iframe') === 'iframe'): ?>
    <script>
    (function(){
        var prepareUrl = <?php echo json_encode(route('public.course.checkout.fawaterak.prepare', $course->id), 512) ?>;
        var meta = document.querySelector('meta[name="csrf-token"]');
        var token = (meta && meta.getAttribute('content')) || <?php echo json_encode(csrf_token(), 15, 512) ?>;
        var errEl = document.getElementById('fawaterk-checkout-error');
        function showErr(msg) {
            if (!errEl) { alert(msg); return; }
            errEl.textContent = msg;
            errEl.classList.remove('hidden');
        }
        function waitForFawaterkFn(resolve, reject) {
            window.requestAnimationFrame(function() {
                if (typeof fawaterkCheckout === 'function') {
                    resolve();
                } else {
                    setTimeout(function() {
                        if (typeof fawaterkCheckout === 'function') {
                            resolve();
                        } else {
                            reject(new Error('no_fn'));
                        }
                    }, 80);
                }
            });
        }
        function loadScriptTag(url) {
            return new Promise(function(resolve, reject) {
                var s = document.createElement('script');
                s.src = url;
                s.async = true;
                s.onload = function() { waitForFawaterkFn(resolve, reject); };
                s.onerror = function() { reject(new Error('network')); };
                document.head.appendChild(s);
            });
        }
        function loadScriptViaBlob(url) {
            return fetch(url, { credentials: 'same-origin', cache: 'no-store' })
                .then(function(r) {
                    if (!r.ok) {
                        throw new Error('fetch ' + r.status);
                    }
                    return r.text();
                })
                .then(function(code) {
                    if (!code || code.trim().indexOf('<') === 0) {
                        throw new Error('not_js');
                    }
                    var blob = new Blob([code], { type: 'application/javascript' });
                    var blobUrl = URL.createObjectURL(blob);
                    return new Promise(function(resolve, reject) {
                        var s = document.createElement('script');
                        s.onload = function() {
                            URL.revokeObjectURL(blobUrl);
                            waitForFawaterkFn(resolve, reject);
                        };
                        s.onerror = function() {
                            URL.revokeObjectURL(blobUrl);
                            reject(new Error('blob_load'));
                        };
                        s.src = blobUrl;
                        document.head.appendChild(s);
                    });
                });
        }
        function loadScript(src) {
            var sep = src.indexOf('?') >= 0 ? '&' : '?';
            var url = src + sep + '_fk=' + Date.now();
            return loadScriptTag(url).catch(function() {
                return loadScriptViaBlob(url);
            });
        }
        function parseJsonSafe(text) {
            try { return JSON.parse(text); } catch (e) { return null; }
        }
        function run() {
            var fd = new FormData();
            fd.append('_token', token);
            var cEl = document.getElementById('checkout_coupon_code');
            var wEl = document.getElementById('checkout_wallet_credit');
            fd.append('coupon_code', cEl ? (cEl.value || '').trim() : '');
            fd.append('wallet_credit', wEl && wEl.value !== '' ? wEl.value : '0');
            fetch(prepareUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: fd,
                credentials: 'same-origin'
            })
            .then(function(r) {
                return r.text().then(function(text) {
                    var data = parseJsonSafe(text);
                    return { ok: r.ok, status: r.status, data: data, raw: text };
                });
            })
            .then(function(res) {
                if (res.status === 401) {
                    showErr('انتهت الجلسة أو لم يُعاد تسجيل الدخول. حدّث الصفحة وسجّل الدخول ثم أعد المحاولة.');
                    return;
                }
                if (res.status === 419) {
                    showErr('انتهت صلاحية الجلسة الأمنية (CSRF). حدّث الصفحة بالكامل (F5) ثم أعد المحاولة.');
                    return;
                }
                if (!res.data) {
                    var rawLower = (res.raw || '').toLowerCase();
                    if (rawLower.indexOf('csrf') !== -1 || rawLower.indexOf('login') !== -1 || rawLower.indexOf('تسجيل الدخول') !== -1 || rawLower.indexOf('<!doctype') !== -1) {
                        showErr('انتهت جلسة تسجيل الدخول أو أُعيد توجيهك لصفحة أخرى. حدّث الصفحة (F5) وسجّل الدخول من جديد ثم افتح صفحة الدفع.');
                        return;
                    }
                    showErr('استجابة غير متوقعة من الخادم (رمز HTTP ' + res.status + '). راجع سجلات الخادم (laravel.log) أو إعدادات الجلسة على الإنتاج.');
                    return;
                }
                if (!res.ok) {
                    showErr(res.data.message || ('تعذّر تجهيز الدفع (رمز ' + res.status + ').'));
                    return;
                }
                if (res.data.mode === 'completed' && res.data.redirect) {
                    window.location.href = res.data.redirect;
                    return;
                }
                if ((res.data.mode && res.data.mode !== 'iframe') || !res.data.pluginScriptUrl || !res.data.pluginConfig) {
                    showErr('استجابة غير صالحة من الخادم (تأكد أن FAWATERAK_INTEGRATION=iframe).');
                    return;
                }
                return loadScript(res.data.pluginScriptUrl)
                    .then(function() {
                        // إلزامي: سكربت فواتيرك يستدعي getEnvUrl() وhandlePaymentProcess() ويعتمد على المتغير العام pluginConfig (ليس الوسيط فقط)
                        var cfg = res.data.pluginConfig;
                        window.pluginConfig = cfg;
                        fawaterkCheckout(cfg);
                    })
                    .catch(function(err) {
                        var msg;
                        if (err && err.message === 'no_fn') {
                            msg = 'وصل ملف فواتيرك لكن لم تُعرَّف الدالة fawaterkCheckout. راجع Console (CSP أو حظر إضافة).';
                        } else if (err && (err.name === 'ReferenceError' || (err.message && err.message.indexOf('pluginConfig') !== -1))) {
                            msg = 'خطأ في تهيئة فواتيرك: ' + (err.message || err.name) + '. إن ظهر بعد تحديث اليوم أبلغ الدعم.';
                        } else if (err && err.message && err.message.indexOf('network') === -1) {
                            msg = 'تعذّر تشغيل الدفع: ' + err.message;
                        } else {
                            msg = 'تعذّر تحميل ملف الدفع. جرّب بدون إضافات حجب، أو تعطيل الكاش في Network. المسار: /js/checkout-pay-widget.v1.js';
                        }
                        showErr(msg);
                    });
            })
            .catch(function() {
                showErr('تعذّر إكمال الطلب مع الخادم (انقطاع الشبكة أو خطأ غير متوقع). حدّث الصفحة (F5) أو راجع تبويب Network في أدوات المطوّر.');
            });
        }
        window.muallimxOnCheckoutPricingUpdated = run;
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', run);
        else run();
    })();
    </script>
    <?php endif; ?>

    <?php if(!empty($fawaterakUseGateway) && isset($course) && empty($fawaterakMisconfigured) && ($fawaterakIntegration ?? 'iframe') === 'api'): ?>
    <script>
    (function(){
        var prepareUrl = <?php echo json_encode(route('public.course.checkout.fawaterak.prepare', $course->id), 512) ?>;
        var methodsUrl = <?php echo json_encode(route('public.course.checkout.fawaterak.methods', $course->id), 512) ?>;
        var payUrl = <?php echo json_encode(route('public.course.checkout.fawaterak.pay', $course->id), 512) ?>;
        var meta = document.querySelector('meta[name="csrf-token"]');
        var token = (meta && meta.getAttribute('content')) || <?php echo json_encode(csrf_token(), 15, 512) ?>;
        var errEl = document.getElementById('fawaterk-api-error');
        var loadEl = document.getElementById('fawaterk-api-loading');
        var methodsEl = document.getElementById('fawaterk-api-methods');
        var payBtn = document.getElementById('fawaterk-api-pay-btn');
        var resultEl = document.getElementById('fawaterk-api-result');
        var walletWrap = document.getElementById('fawaterk-api-wallet-wrap');
        var walletInput = document.getElementById('fawaterk-api-wallet');
        var selectedId = null;

        function showErr(msg) {
            if (!errEl) { alert(msg); return; }
            errEl.textContent = msg;
            errEl.classList.remove('hidden');
        }
        function parseJsonSafe(text) {
            try { return JSON.parse(text); } catch (e) { return null; }
        }
        function renderMethods(list) {
            if (!methodsEl) return;
            methodsEl.innerHTML = '';
            list.forEach(function(m) {
                var id = m.paymentId;
                var name = (document.documentElement.getAttribute('dir') === 'rtl' && m.name_ar) ? m.name_ar : (m.name_en || m.name_ar || ('#' + id));
                var card = document.createElement('button');
                card.type = 'button';
                card.className = 'flex items-center gap-4 p-4 rounded-2xl border-2 border-slate-200 bg-white text-start hover:border-mx-navy/40 transition-colors ring-1 ring-slate-100';
                card.setAttribute('data-pid', String(id));
                if (m.logo && typeof m.logo === 'string') {
                    var img = document.createElement('img');
                    img.src = m.logo;
                    img.alt = '';
                    img.className = 'h-10 w-auto object-contain shrink-0';
                    img.loading = 'lazy';
                    card.appendChild(img);
                } else {
                    var ph = document.createElement('span');
                    ph.className = 'w-10 h-10 rounded-xl bg-mx-soft flex items-center justify-center text-mx-navy shrink-0';
                    ph.innerHTML = '<i class="fas fa-credit-card"></i>';
                    card.appendChild(ph);
                }
                var title = document.createElement('span');
                title.className = 'font-bold text-navy-950 flex-1 min-w-0';
                title.textContent = name;
                card.appendChild(title);
                card.addEventListener('click', function() {
                    methodsEl.querySelectorAll('button').forEach(function(b) {
                        b.classList.remove('border-mx-navy', 'ring-2', 'ring-mx-navy/25');
                        b.classList.add('border-slate-200');
                    });
                    card.classList.remove('border-slate-200');
                    card.classList.add('border-mx-navy', 'ring-2', 'ring-mx-navy/25');
                    selectedId = id;
                    if (payBtn) payBtn.disabled = false;
                });
                methodsEl.appendChild(card);
            });
            methodsEl.classList.remove('hidden');
            if (walletWrap) walletWrap.classList.remove('hidden');
        }
        function showPaymentResult(pd) {
            if (!resultEl || !pd) return;
            resultEl.classList.remove('hidden');
            var html = '';
            if (pd.redirectTo) {
                window.location.href = pd.redirectTo;
                return;
            }
            if (pd.fawryCode) html += '<p><strong>رمز فوري:</strong> <span dir="ltr">' + pd.fawryCode + '</span></p>';
            if (pd.expireDate) html += '<p class="text-slate-600 text-xs">ينتهي: ' + pd.expireDate + '</p>';
            if (pd.meezaReference != null) html += '<p><strong>مرجع ميزا:</strong> ' + pd.meezaReference + '</p>';
            if (pd.meezaQrCode) html += '<p class="break-all text-xs" dir="ltr">' + pd.meezaQrCode + '</p>';
            if (pd.amanCode) html += '<p><strong>أمان:</strong> ' + pd.amanCode + '</p>';
            if (pd.masaryCode) html += '<p><strong>مصاري:</strong> ' + pd.masaryCode + '</p>';
            if (!html) html = '<pre class="text-xs whitespace-pre-wrap break-all" dir="ltr">' + JSON.stringify(pd, null, 2) + '</pre>';
            resultEl.innerHTML = '<p class="font-bold text-mx-indigo mb-2">أكمل الدفع حسب التعليمات:</p>' + html +
                '<p class="text-xs text-slate-500 mt-3">بعد الدفع قد تُعاد إلى الموقع تلقائياً؛ إن لم يحدث ذلك حدّث صفحة الطلبات.</p>';
        }
        function run() {
            var fd = new FormData();
            fd.append('_token', token);
            var cEl = document.getElementById('checkout_coupon_code');
            var wEl = document.getElementById('checkout_wallet_credit');
            fd.append('coupon_code', cEl ? (cEl.value || '').trim() : '');
            fd.append('wallet_credit', wEl && wEl.value !== '' ? wEl.value : '0');
            fetch(prepareUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: fd,
                credentials: 'same-origin'
            })
            .then(function(r) { return r.text().then(function(t) { return { ok: r.ok, status: r.status, data: parseJsonSafe(t), raw: t }; }); })
            .then(function(res) {
                if (res.status === 401) { showErr('انتهت الجلسة. سجّل الدخول ثم أعد فتح الصفحة.'); return; }
                if (res.status === 419) { showErr('انتهت صلاحية الجلسة (CSRF). حدّث الصفحة (F5).'); return; }
                if (!res.data || !res.ok) {
                    showErr((res.data && res.data.message) || 'تعذّر تجهيز الطلب.');
                    return;
                }
                if (res.data.mode !== 'api') {
                    showErr('الخادم ليس في وضع API. ضبط FAWATERAK_INTEGRATION=api في .env');
                    return;
                }
                return fetch(methodsUrl, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                });
            })
            .then(function(r) {
                if (!r) return;
                return r.text().then(function(t) { return { ok: r.ok, status: r.status, data: parseJsonSafe(t), raw: t }; });
            })
            .then(function(res) {
                if (!res) return;
                if (loadEl) loadEl.classList.add('hidden');
                if (!res.ok || !res.data || res.data.status !== 'success' || !Array.isArray(res.data.data)) {
                    showErr((res.data && res.data.message) || 'تعذّر جلب وسائل الدفع.');
                    return;
                }
                renderMethods(res.data.data);
            })
            .catch(function() {
                if (loadEl) loadEl.classList.add('hidden');
                showErr('تعذّر الاتصال بالخادم.');
            });
        }

        window.muallimxOnCheckoutPricingUpdated = function () {
            if (loadEl) loadEl.classList.remove('hidden');
            if (methodsEl) {
                methodsEl.classList.add('hidden');
                methodsEl.innerHTML = '';
            }
            if (walletWrap) walletWrap.classList.add('hidden');
            if (resultEl) {
                resultEl.classList.add('hidden');
                resultEl.innerHTML = '';
            }
            if (payBtn) {
                payBtn.disabled = true;
            }
            selectedId = null;
            if (errEl) errEl.classList.add('hidden');
            run();
        };

        if (payBtn) {
            payBtn.addEventListener('click', function() {
                if (!selectedId) return;
                errEl && errEl.classList.add('hidden');
                payBtn.disabled = true;
                var body = { payment_method_id: selectedId };
                var w = walletInput && walletInput.value ? walletInput.value.trim() : '';
                if (w) body.mobile_wallet_number = w;
                fetch(payUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(body)
                })
                .then(function(r) { return r.text().then(function(t) { return { ok: r.ok, status: r.status, data: parseJsonSafe(t), raw: t }; }); })
                .then(function(res) {
                    payBtn.disabled = false;
                    if (res.status === 401 || res.status === 419) {
                        showErr('انتهت الجلسة. حدّث الصفحة وسجّل الدخول.');
                        return;
                    }
                    if (!res.data) { showErr('استجابة غير متوقعة من الخادم.'); return; }
                    if (!res.ok) {
                        showErr(res.data.message || 'تعذّر بدء الدفع.');
                        return;
                    }
                    var pd = res.data.data && res.data.data.payment_data;
                    showPaymentResult(pd);
                })
                .catch(function() {
                    payBtn.disabled = false;
                    showErr('تعذّر إكمال الطلب.');
                });
            });
        }

        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', run);
        else run();
    })();
    </script>
    <?php endif; ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\public\checkout.blade.php ENDPATH**/ ?>