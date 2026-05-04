<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
?>

<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title><?php echo e(__('public.pricing_page_title')); ?> - <?php echo e(__('public.site_suffix')); ?></title>
    <meta name="theme-color" content="#283593">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    navy: { 950:'#020617' },
                    brand: { 50:'#ecfeff',100:'#cffafe',200:'#a5f3fc',300:'#67e8f9',400:'#22d3ee',500:'#06b6d4',600:'#0891b2',700:'#0e7490',800:'#155e75',900:'#164e63' },
                    mx: {
                        navy: '#283593',
                        indigo: '#1F2A7A',
                        orange: '#FB5607',
                        cream: '#FFF7ED',
                        rose: '#FFE5F7',
                        gold: '#FFE569',
                        soft: '#F7F8FF'
                    }
                },
                fontFamily: {
                    heading: ['Cairo','Tajawal','IBM Plex Sans Arabic','sans-serif'],
                    body: ['Cairo','IBM Plex Sans Arabic','Tajawal','sans-serif'],
                }
            }
        }
    }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"></noscript>

    <style>
        *{font-family:'Cairo','IBM Plex Sans Arabic','Tajawal',system-ui,sans-serif}
        h1,h2,h3,h4,h5,h6,.font-heading{font-family:'Cairo','Tajawal','IBM Plex Sans Arabic',sans-serif}
        html{scroll-behavior:smooth;overflow-x:hidden}
        body{background:#fff;overflow-x:hidden;min-height:100vh;display:flex;flex-direction:column}
        .container-1200{max-width:1200px;margin-inline:auto;padding-inline:24px}
        @media (max-width: 768px){.container-1200{padding-inline:16px}}
        .card-hover{transition:transform .25s ease,box-shadow .25s ease}
        .card-hover:hover{transform:translateY(-4px) scale(1.01);box-shadow:0 20px 35px -20px rgba(31,42,122,.35)}
        .card-base{border-radius:18px;padding:20px;box-shadow:0 8px 24px -18px rgba(31,42,122,.25);border:1px solid #eceef8;background:#fff}
        .btn-primary{padding:12px 24px;border-radius:16px;font-weight:700;color:#fff;background:#FB5607;transition:transform .2s ease,box-shadow .2s ease}
        .btn-primary:hover{transform:scale(1.02);box-shadow:0 12px 28px -10px rgba(251,86,7,.45)}
        .btn-secondary{padding:12px 24px;border-radius:16px;border:1px solid #d6daea;color:#1F2A7A;background:#fff;transition:background .2s ease}
        .btn-secondary:hover{background:#f8f9ff}
        .navbar-spacer{display:block!important}
        #navbar,#navbar.nav-transparent,#navbar.nav-solid{
            background:rgba(31,42,122,.92)!important;
            backdrop-filter:blur(12px)!important;
            -webkit-backdrop-filter:blur(12px)!important;
            border-bottom:1px solid rgba(255,255,255,.08)!important;
        }
    </style>
</head>
<body class="font-body text-slate-800 antialiased">
    <?php echo $__env->make('components.unified-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="flex-1">
        <section class="pt-10 sm:pt-14 lg:pt-16 pb-10 sm:pb-12 overflow-hidden relative" style="background:radial-gradient(circle at 12% 80%,rgba(255,229,247,.65),transparent 28%),radial-gradient(circle at 88% 20%,rgba(40,53,147,.10),transparent 30%),linear-gradient(180deg,#f4f6ff 0%,#fbfbff 55%,#ffffff 100%)">
            <div class="absolute inset-0 pointer-events-none opacity-40" style="background-image:radial-gradient(circle at 1px 1px,rgba(40,53,147,.08) 1px,transparent 0);background-size:30px 30px"></div>
            <div class="container-1200 relative z-10 text-center">
                <div class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-bold mb-6" style="background:#FFE5F7;color:#283593;border:1px solid #f5c7e8">
                    <i class="fas fa-tags"></i>
                    خطط تسعير مصممة خصيصاً للمعلمين
                </div>

                <h1 class="font-heading text-[2rem] sm:text-[2.8rem] lg:text-[3.35rem] leading-[1.22] font-black text-mx-indigo mb-5">
                    الأسعار والباقات
                    <span class="block text-[#FB5607]">للمعلمين أونلاين</span>
                </h1>

                <p class="text-slate-600 text-base sm:text-lg leading-8 mb-7 max-w-3xl mx-auto">
                    ابدأ مسيرتك كمعلم أونلاين باستخدام أدوات احترافية ومناهج جاهزة وبروفايل مهني يفتح لك فرص عمل مع أكاديميات.
                </p>

                <p class="text-sm text-slate-500 max-w-2xl mx-auto">
                    جميع الأسعار بالجنيه المصري (ج.م) وتشمل أدوات AI، مكتبة مناهج، ودعم فني للمعلمين.
                </p>
            </div>
        </section>

        <!-- Teacher Plans Section (بيانات من إعدادات مزايا اشتراك المعلمين /admin/teacher-features) -->
        <section class="py-16 md:py-20 bg-white">
            <div class="container-1200">
                <div class="text-center mb-12">
                    <span class="inline-block px-4 py-1.5 rounded-full text-sm font-semibold mb-4" style="background:#FFE5F7;color:#283593">
                        باقات المعلمين
                    </span>
                    <h2 class="font-heading text-3xl md:text-4xl font-extrabold text-mx-indigo mb-4">
                        اختر الباقة المناسبة لطموحك كمعلم أونلاين
                    </h2>
                    <p class="text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed">
                        من بداية مشوارك وحتى بناء مسار مهني مستقر، كل باقة مصممة لتزيد دخلك وتوفّر وقتك في التحضير والمتابعة.
                    </p>
                </div>

                <?php
                    $planKeys = ['teacher_starter', 'teacher_pro'];
                    $billingPhrases = ['monthly' => 'جنيه شهريًا', 'quarterly' => 'جنيه / 3 شهور', 'yearly' => 'جنيه سنويًا'];
                ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-7 max-w-4xl mx-auto">
                    <?php $__currentLoopData = $planKeys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $planKey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $plan = $teacherPlans[$planKey] ?? null;
                            if (!$plan) continue;
                            $meta = [
                                'subtitle' => $plan['card_subtitle'] ?? '',
                                'badge' => trim((string) ($plan['card_badge'] ?? '')),
                                'priceHint' => $plan['card_price_hint'] ?? '',
                                'cta' => $plan['card_cta'] ?? 'ابدأ الآن',
                                'footer_note' => $plan['card_footer_note'] ?? '',
                            ];
                            $label = $plan['label'] ?? $planKey;
                            $price = (float) ($plan['price'] ?? 0);
                            $cycle = $plan['billing_cycle'] ?? 'monthly';
                            $cyclePhrase = $billingPhrases[$cycle] ?? 'جنيه';
                            $features = $plan['features'] ?? [];
                            $featureDescriptions = is_array($plan['feature_descriptions'] ?? null) ? $plan['feature_descriptions'] : [];
                            $isPro = $planKey === 'teacher_pro';
                        ?>
                        <div class="card-base card-hover !p-7 sm:!p-8 pt-10 flex flex-col relative
                            <?php if($isPro): ?> border-[#283593] ring-2 ring-[#283593]/10
                            <?php else: ?> border-slate-200
                            <?php endif; ?>">
                            <?php if($meta['badge'] !== ''): ?>
                                <div class="absolute top-3 left-4 bg-[#FB5607] text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg z-10"><?php echo e($meta['badge']); ?></div>
                            <?php endif; ?>
                            <div class="mb-4">
                                <h3 class="text-2xl font-black text-mx-indigo mb-1"><?php echo e($label); ?></h3>
                                <?php if($meta['subtitle'] !== ''): ?>
                                <p class="text-sm font-semibold text-[#283593]">
                                    <?php echo e($meta['subtitle']); ?>

                                </p>
                                <?php endif; ?>
                            </div>
                            <div class="mb-6">
                                <div class="text-3xl font-black text-mx-indigo mb-1">
                                    <?php echo e(number_format($price, 0)); ?> <span class="text-base sm:text-lg font-bold text-slate-600"><?php echo e($cyclePhrase); ?></span>
                                </div>
                                <?php if($meta['priceHint'] !== ''): ?>
                                    <p class="text-sm text-slate-500"><?php echo e($meta['priceHint']); ?></p>
                                <?php endif; ?>
                            </div>
                            <ul class="space-y-3 text-slate-700 mb-8 flex-1 text-sm">
                                <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $featureKey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle text-[#283593] ml-2 mt-1"></i>
                                        <div>
                                            <p class="font-semibold"><?php echo e(__("student.subscription_feature.{$featureKey}")); ?></p>
                                            <?php if(!empty($featureDescriptions[$featureKey])): ?>
                                                <p class="text-xs text-slate-500 mt-0.5"><?php echo e($featureDescriptions[$featureKey]); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <?php if($meta['footer_note'] !== ''): ?>
                                <div class="mb-4 px-3 py-2 rounded-xl text-sm font-semibold text-[#283593] bg-[#EFF2FF] border border-[#dbe4ff]"><?php echo e($meta['footer_note']); ?></div>
                            <?php endif; ?>
                            <a href="<?php echo e(route('public.subscription.checkout', $planKey)); ?>" class="w-full inline-flex items-center justify-center px-6 py-3 rounded-xl font-bold text-sm transition-colors
                                <?php if($isPro): ?> bg-[#283593] hover:bg-[#1f2a7a] text-white
                                <?php else: ?> btn-primary !bg-[#283593] hover:!bg-[#1f2a7a] text-white
                                <?php endif; ?>">
                                <?php echo e($meta['cta'] ?? 'ابدأ الآن'); ?>

                            </a>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </section>

        <!-- Existing Platform Packages -->
        <?php if(isset($packages) && $packages->count() > 0): ?>
        <section class="py-16 md:py-20 bg-slate-50/60 border-t border-slate-100">
            <div class="container-1200">
        <div class="text-center mb-12">
            <span class="inline-block px-4 py-1.5 rounded-full text-sm font-semibold mb-4" style="background:#FFE5F7;color:#283593">
                باقات المنصة
            </span>
            <h2 class="font-heading text-3xl md:text-4xl font-extrabold text-mx-indigo mb-4">
                باقات إضافية حسب احتياجك
            </h2>
            <p class="text-slate-600 max-w-2xl mx-auto leading-8">
                هذه الباقات متاحة بشكل إضافي بجانب باقات المعلمين الأساسية.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
            <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <!-- Package Card -->
            <div class="card-base card-hover !p-8 pt-10 relative <?php echo e($package->is_popular ? '!bg-[#283593] !border-[#283593] text-white' : ''); ?>">
                <?php if($package->is_popular): ?>
                <div class="absolute top-3 left-4 bg-[#FB5607] text-white text-xs font-bold px-3 py-1.5 rounded-full text-center shadow-lg z-10">الأكثر شعبية</div>
                <?php endif; ?>
                
                <div class="text-center mb-6 <?php echo e($package->is_popular ? 'mt-4' : ''); ?>">
                    <?php if($package->thumbnail): ?>
                    <div class="w-20 h-20 rounded-full overflow-hidden mx-auto mb-4 feature-icon-hover">
                        <img src="<?php echo e(asset('storage/' . $package->thumbnail)); ?>" alt="<?php echo e($package->name); ?>" class="w-full h-full object-cover" loading="lazy" decoding="async">
                    </div>
                    <?php else: ?>
                    <div class="w-20 h-20 <?php echo e($package->is_popular ? 'bg-white/20' : 'bg-[#283593]'); ?> rounded-full flex items-center justify-center mx-auto mb-4 feature-icon-hover">
                        <?php if($package->is_featured): ?>
                            <i class="fas fa-crown <?php echo e($package->is_popular ? 'text-white' : 'text-white'); ?> text-2xl"></i>
                        <?php elseif($package->is_popular): ?>
                            <i class="fas fa-star text-white text-2xl"></i>
                        <?php else: ?>
                            <i class="fas fa-box text-white text-2xl"></i>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <h3 class="text-2xl font-bold <?php echo e($package->is_popular ? 'text-white' : 'text-mx-indigo'); ?> mb-2"><?php echo e($package->name); ?></h3>
                    
                    <?php if($package->original_price && $package->original_price > $package->price): ?>
                    <div class="mb-2">
                        <span class="text-lg <?php echo e($package->is_popular ? 'text-white/70' : 'text-gray-400'); ?> line-through"><?php echo e(number_format($package->original_price, 2)); ?> ج.م</span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="text-5xl font-bold <?php echo e($package->is_popular ? 'text-white' : 'text-[#FB5607]'); ?> mb-2">
                        <?php if($package->price > 0): ?>
                            <?php echo e(number_format($package->price, 2)); ?> <span class="text-2xl">ج.م</span>
                        <?php else: ?>
                            <span class="text-2xl">مجاني</span>
                        <?php endif; ?>
                    </div>
                    
                    <?php
                        $cardBody = trim((string) ($package->card_summary ?? '')) !== ''
                            ? $package->card_summary
                            : ($package->description ?? '');
                    ?>
                    <?php if($cardBody !== ''): ?>
                    <p class="<?php echo e($package->is_popular ? 'text-white/85' : 'text-gray-600'); ?> text-sm leading-relaxed whitespace-pre-line line-clamp-6"><?php echo e($cardBody); ?></p>
                    <?php endif; ?>
                    
                    <?php if($package->courses_count > 0): ?>
                    <p class="text-sm <?php echo e($package->is_popular ? 'text-white/80' : 'text-gray-500'); ?> mt-2">
                        <i class="fas fa-graduation-cap ml-1"></i>
                        <?php echo e($package->courses_count); ?> كورس
                    </p>
                    <?php endif; ?>
                </div>
                
                <?php
                    $cardFeatures = collect($package->features ?? [])->map(fn ($f) => trim((string) $f))->filter()->values();
                ?>
                <?php if($cardFeatures->isNotEmpty()): ?>
                <ul class="space-y-4 mb-8">
                    <?php $__currentLoopData = $cardFeatures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="flex items-center <?php echo e($package->is_popular ? 'text-white' : 'text-gray-700'); ?>">
                        <i class="fas fa-check-circle <?php echo e($package->is_popular ? 'text-[#FFE569]' : 'text-[#283593]'); ?> ml-3 shrink-0"></i>
                        <span><?php echo e($feature); ?></span>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <?php endif; ?>
                
                <!-- CTA Button -->
                <?php if($package->price > 0): ?>
                <a href="<?php echo e(route('public.package.show', $package->slug)); ?>" class="<?php echo e($package->is_popular ? 'bg-white text-[#283593] hover:bg-slate-100' : 'btn-primary'); ?> font-bold py-3 px-6 rounded-xl transition-colors w-full text-center block">
                    <i class="fas fa-shopping-cart ml-2"></i>
                    اشتر الآن
                </a>
                <?php else: ?>
                <a href="<?php echo e(route('public.package.show', $package->slug)); ?>" class="<?php echo e($package->is_popular ? 'bg-white text-[#283593] hover:bg-slate-100' : 'btn-primary'); ?> font-bold py-3 px-6 rounded-xl transition-colors w-full text-center block">
                    <i class="fas fa-eye ml-2"></i>
                    عرض التفاصيل
                </a>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
            </div>
        </section>
        <?php endif; ?>
    </main>

<?php echo $__env->make('components.unified-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\public\pricing.blade.php ENDPATH**/ ?>