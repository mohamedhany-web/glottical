<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title><?php echo e(__('public.instructors_page_title')); ?> - <?php echo e(__('public.site_suffix')); ?></title>
    <meta name="title"       content="<?php echo e(__('public.instructors_page_title')); ?> - <?php echo e(__('public.site_suffix')); ?>">
    <meta name="description" content="<?php echo e(__('public.instructors_subtitle')); ?>">
    <meta name="keywords"    content="مدربون أونلاين, معلمون محترفون, مدرب معتمد, Muallimx, تدريس أونلاين">
    <meta name="author"      content="Muallimx">
    <meta name="robots"      content="index, follow, max-image-preview:large, max-snippet:-1">
    <meta name="theme-color" content="#283593">
    <link rel="canonical"    href="<?php echo e(url('/instructors')); ?>">
    <link rel="alternate" hreflang="ar"        href="<?php echo e(url('/instructors')); ?>?lang=ar">
    <link rel="alternate" hreflang="en"        href="<?php echo e(url('/instructors')); ?>?lang=en">
    <link rel="alternate" hreflang="x-default" href="<?php echo e(url('/instructors')); ?>">
    <!-- Open Graph -->
    <meta property="og:type"             content="website">
    <meta property="og:url"              content="<?php echo e(url('/instructors')); ?>">
    <meta property="og:title"            content="<?php echo e(__('public.instructors_page_title')); ?> - Muallimx">
    <meta property="og:description"      content="<?php echo e(__('public.instructors_subtitle')); ?>">
    <meta property="og:image"            content="<?php echo e(asset('images/og-image.jpg')); ?>">
    <meta property="og:image:alt"        content="مدربو Muallimx">
    <meta property="og:image:width"      content="1200">
    <meta property="og:image:height"     content="630">
    <meta property="og:locale"           content="<?php echo e($locale === 'ar' ? 'ar_AR' : 'en_US'); ?>">
    <meta property="og:site_name"        content="Muallimx">
    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:site"        content="@Muallimx">
    <meta name="twitter:url"         content="<?php echo e(url('/instructors')); ?>">
    <meta name="twitter:title"       content="<?php echo e(__('public.instructors_page_title')); ?> - Muallimx">
    <meta name="twitter:description" content="<?php echo e(__('public.instructors_subtitle')); ?>">
    <meta name="twitter:image"       content="<?php echo e(asset('images/og-image.jpg')); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <!-- BreadcrumbList JSON-LD -->
    <script type="application/ld+json">
    {"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"الرئيسية","item":"<?php echo e(url('/')); ?>"},{"@type":"ListItem","position":2,"name":"المدربون","item":"<?php echo e(url('/instructors')); ?>"}]}
    </script>
    <?php echo $__env->make('partials.seo-jsonld', ['jsonldType' => 'website'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
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
    };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"></noscript>
    <style>
        [x-cloak]{display:none !important}
        *{font-family:'Cairo','IBM Plex Sans Arabic','Tajawal',system-ui,sans-serif}
        h1,h2,h3,h4,h5,h6,.font-heading{font-family:'Cairo','Tajawal','IBM Plex Sans Arabic',sans-serif}
        html{scroll-behavior:smooth;overflow-x:hidden}
        body{overflow-x:hidden;background:#fff;min-height:100vh;display:flex;flex-direction:column}
        body>*{flex-shrink:0}
        .container-1200{max-width:1200px;margin-inline:auto;padding-inline:24px}
        @media (max-width: 768px){.container-1200{padding-inline:16px}}
        .reveal{opacity:0;transform:translateY(26px);transition:opacity .6s ease,transform .6s ease}
        .reveal.revealed{opacity:1;transform:translateY(0)}
        .s1{transition-delay:.06s}.s2{transition-delay:.12s}.s3{transition-delay:.18s}.s4{transition-delay:.24s}
        .btn-primary{padding:12px 24px;border-radius:16px;font-weight:700;color:#fff;background:#FB5607;transition:transform .2s ease,box-shadow .2s ease}
        .btn-primary:hover{transform:scale(1.02);box-shadow:0 12px 28px -10px rgba(251,86,7,.45)}
        .btn-secondary{padding:12px 24px;border-radius:16px;border:1px solid #d6daea;color:#1F2A7A;background:#fff;transition:background .2s ease}
        .btn-secondary:hover{background:#f8f9ff}
        .card-base{border-radius:18px;padding:20px;box-shadow:0 8px 24px -18px rgba(31,42,122,.25);border:1px solid #eceef8;background:#fff}
        .hover-lift{transition:transform .25s ease,box-shadow .25s ease}
        .hover-lift:hover{transform:translateY(-4px) scale(1.01);box-shadow:0 20px 35px -20px rgba(31,42,122,.35)}
        #scroll-progress{position:fixed;top:0;left:0;height:3px;width:0;background:linear-gradient(90deg,#FB5607,#FFE569);z-index:9999}
        .line-clamp-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
        .line-clamp-3{display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
        .arrow-link::after{content:'\f177';font-family:'Font Awesome 6 Free';font-weight:900;margin-inline-start:8px}
        [dir='ltr'] .arrow-link::after{content:'\f178'}
        .navbar-spacer{display:block!important}
        #navbar,#navbar.nav-transparent,#navbar.nav-solid{
            background:rgba(31,42,122,.92)!important;
            backdrop-filter:blur(12px)!important;
            -webkit-backdrop-filter:blur(12px)!important;
            border-bottom:1px solid rgba(255,255,255,.08)!important;
        }
    </style>
</head>
<body class="font-body text-slate-800">
    <div id="scroll-progress"></div>
    <?php echo $__env->make('components.unified-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="flex-1">
        
        <section class="pt-10 sm:pt-14 lg:pt-16 pb-10 sm:pb-12 overflow-hidden relative" style="background:radial-gradient(circle at 12% 80%,rgba(255,229,247,.65),transparent 28%),radial-gradient(circle at 88% 20%,rgba(40,53,147,.10),transparent 30%),linear-gradient(180deg,#f4f6ff 0%,#fbfbff 55%,#ffffff 100%)">
            <div class="absolute inset-0 pointer-events-none opacity-40" style="background-image:radial-gradient(circle at 1px 1px,rgba(40,53,147,.08) 1px,transparent 0);background-size:30px 30px"></div>

            <div class="container-1200 relative z-10">
                <div class="text-center max-w-4xl mx-auto">
                    <div class="reveal">
                        <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-bold mb-6" style="background:#FFE5F7;color:#283593;border:1px solid #f5c7e8">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <?php echo e(__('public.instructors_page_title')); ?>

                        </span>
                    </div>
                    <h1 class="reveal s1 font-heading text-[2rem] sm:text-[2.8rem] lg:text-[3.35rem] leading-[1.22] font-black text-mx-indigo mb-5">
                        <?php echo e(__('public.instructors_heading')); ?>

                    </h1>
                    <p class="reveal s2 text-slate-600 text-base sm:text-lg leading-8 max-w-3xl mx-auto mb-7">
                        <?php echo e(__('public.instructors_subtitle')); ?>

                    </p>

                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mt-8 reveal s3 max-w-xl mx-auto">
                        <article class="rounded-2xl p-4 sm:p-5 border border-slate-200 bg-white text-center shadow-[0_10px_24px_-18px_rgba(31,42,122,.25)]">
                            <p class="text-3xl sm:text-4xl font-black text-mx-indigo"><?php echo e($profiles->count()); ?></p>
                            <p class="text-xs sm:text-sm text-slate-600 mt-1">مدرّب معتمد</p>
                        </article>
                        <article class="rounded-2xl p-4 sm:p-5 border border-slate-200 bg-[#FFE5F7] text-center shadow-[0_10px_24px_-18px_rgba(31,42,122,.25)]">
                            <p class="text-3xl sm:text-4xl font-black text-[#FB5607]"><?php echo e($profiles->sum('courses_count')); ?></p>
                            <p class="text-xs sm:text-sm text-slate-600 mt-1">كورس نشط</p>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        
        <section class="py-20 md:py-28 bg-white">
            <div class="container-1200">
                <div class="text-center max-w-3xl mx-auto mb-14 reveal">
                    <span class="inline-block px-4 py-1.5 rounded-full text-sm font-semibold mb-4" style="background:#FFE5F7;color:#283593">فريق التدريب</span>
                    <h2 class="font-heading text-3xl sm:text-4xl font-black text-mx-indigo mb-3">
                        تعرّف على
                        <span style="color:#FB5607">مدرّبينا</span>
                    </h2>
                    <p class="text-slate-600 leading-8">يتم ترتيب الظهور تلقائياً حسب مزايا الباقة التسويقية (الأولوية، الأيام المميزة، والمزايا المفعلة).</p>
                </div>

                <?php if($profiles->isNotEmpty()): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    <?php $__currentLoopData = $profiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="reveal s<?php echo e(min($idx + 1, 4)); ?> card-base hover-lift group !p-0 overflow-hidden flex flex-col">
                    <a href="<?php echo e(route('public.instructors.show', $p->user)); ?>" class="block flex-1 min-h-0">

                        
                        <div class="relative aspect-[4/3] overflow-hidden" style="background:linear-gradient(135deg,#e9edff,#f8f9ff)">
                            <?php if($p->photo_path): ?>
                                <img src="<?php echo e($p->photo_url); ?>" alt="<?php echo e($p->user->name); ?>"
                                     class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out"
                                     onerror="this.style.display='none';this.nextElementSibling.classList.remove('hidden')">
                                <div class="hidden absolute inset-0 flex items-center justify-center" style="background:linear-gradient(135deg,#e9edff,#f8f9ff)">
                                    <div class="w-24 h-24 rounded-full bg-[#283593]/10 flex items-center justify-center">
                                        <i class="fas fa-user text-[#283593]/60 text-4xl"></i>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-24 h-24 rounded-full bg-[#283593]/10 flex items-center justify-center">
                                        <i class="fas fa-user text-[#283593]/60 text-4xl"></i>
                                    </div>
                                </div>
                            <?php endif; ?>

                            
                            <div class="absolute inset-0 bg-gradient-to-t from-black/45 via-transparent to-transparent"></div>

                            
                            <?php if($p->courses_count > 0): ?>
                            <span class="absolute top-3 <?php echo e($isRtl?'right':'left'); ?>-3 px-3 py-1.5 rounded-full bg-[#283593] text-white text-[11px] font-bold flex items-center gap-1.5 shadow-lg">
                                <i class="fas fa-book-open text-[9px]"></i>
                                <?php echo e($p->courses_count); ?> <?php echo e($p->courses_count > 1 ? 'كورسات' : 'كورس'); ?>

                            </span>
                            <?php endif; ?>

                            <?php if(!empty($p->marketing_featured_today)): ?>
                            <span class="absolute top-3 <?php echo e($isRtl?'left':'right'); ?>-3 px-3 py-1.5 rounded-full bg-[#FB5607] text-white text-[11px] font-bold flex items-center gap-1.5 shadow-lg">
                                <i class="fas fa-bolt text-[9px]"></i>
                                Featured
                            </span>
                            <?php endif; ?>
                        </div>

                        
                        <div class="p-5 sm:p-6">
                            <h3 class="font-heading text-xl font-bold text-mx-indigo mb-1.5 group-hover:text-[#FB5607] transition-colors duration-300">
                                <?php echo e($p->user->name); ?>

                            </h3>
                            <p class="text-sm text-[#FB5607] font-medium mb-3">
                                <?php echo e($p->headline ?? __('public.instructor_fallback')); ?>

                            </p>

                            
                            <?php if(count($p->skills_list) > 0): ?>
                            <div class="flex flex-wrap gap-1.5 mb-4">
                                <?php $__currentLoopData = array_slice($p->skills_list, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="px-2.5 py-1 rounded-lg bg-slate-50 text-slate-600 text-[11px] font-medium border border-slate-100"><?php echo e($skill); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if(count($p->skills_list) > 3): ?>
                                <span class="px-2.5 py-1 rounded-lg bg-[#FFE5F7] text-[#283593] text-[11px] font-medium">+<?php echo e(count($p->skills_list) - 3); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>

                            
                            <?php if($p->bio): ?>
                            <p class="text-[13px] text-slate-500 leading-relaxed line-clamp-2 mb-4"><?php echo e($p->bio); ?></p>
                            <?php endif; ?>

                            
                            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                                <div class="flex items-center gap-2 text-xs text-slate-400">
                                    <i class="fas fa-check-circle text-emerald-500"></i>
                                    <span class="font-medium">مدرّب معتمد</span>
                                    <?php if(isset($p->marketing_priority_score)): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-600">
                                            <i class="fas fa-signal text-[10px]"></i>
                                            <?php echo e((int) $p->marketing_priority_score); ?>

                                        </span>
                                    <?php endif; ?>
                                </div>
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#283593] text-white font-bold text-[12px] shadow-lg group-hover:bg-[#1f2a7a] group-hover:scale-105 transition-all duration-300">
                                    عرض الملف
                                    <i class="fas fa-arrow-<?php echo e($isRtl?'left':'right'); ?> text-[9px]"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                        <?php if(isset($consultationSetting) && $consultationSetting->is_active): ?>
                        <div class="px-5 sm:px-6 pb-5 pt-1 border-t border-slate-50">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                                <span class="text-[11px] text-slate-500 font-medium">استشارة — <strong class="text-mx-indigo"><?php echo e(number_format($p->effectiveConsultationPriceEgp(), 2)); ?></strong> ج.م</span>
                                <?php if(auth()->guard()->check()): ?>
                                    <?php if(auth()->user()->isStudent()): ?>
                                        <a href="<?php echo e(route('consultations.create', $p->user)); ?>"
                                           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#FB5607] hover:bg-[#e84d00] text-white text-xs font-bold shadow-md transition-all">
                                            <i class="fas fa-comments text-[11px]"></i>
                                            طلب استشارة
                                        </a>
                                    <?php else: ?>
                                        <span class="text-[11px] text-slate-400">تسجيل الدخول كطالب لطلب استشارة</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <a href="<?php echo e(route('login', ['redirect' => route('consultations.create', $p->user)])); ?>"
                                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#FB5607] hover:bg-[#e84d00] text-white text-xs font-bold shadow-md transition-all">
                                        <i class="fas fa-comments text-[11px]"></i>
                                        طلب استشارة
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php else: ?>
                <div class="text-center py-20 reveal">
                    <div class="max-w-md mx-auto">
                        <div class="w-24 h-24 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-sm" style="background:#FFE5F7">
                            <i class="fas fa-chalkboard-teacher text-[#283593] text-4xl"></i>
                        </div>
                        <h3 class="font-heading text-2xl font-bold text-mx-indigo mb-3"><?php echo e(__('public.no_instructors')); ?></h3>
                        <p class="text-slate-500 mb-8 leading-relaxed">سيتم إضافة مدربين جدد قريباً</p>
                        <a href="<?php echo e(url('/')); ?>" class="btn-primary inline-flex items-center gap-2.5 !bg-[#FB5607] hover:!bg-[#e84d00] text-white px-7 py-3.5 rounded-2xl font-bold shadow-xl">
                            <i class="fas fa-home"></i>
                            العودة للرئيسية
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>

        
        <section class="pt-14 sm:pt-18 pb-10 sm:pb-12" style="background:linear-gradient(180deg,#f4f7ff 0%,#ffffff 100%)">
            <div class="container-1200">
                <div class="reveal rounded-[28px] border border-slate-200 bg-white shadow-[0_20px_44px_-26px_rgba(31,42,122,.28)] px-6 sm:px-10 py-10 sm:py-12 text-center">
                <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-bold mb-5" style="background:#FFE5F7;color:#283593"><i class="fas fa-rocket"></i> انضم لفريقنا</span>
                <h2 class="font-heading text-3xl sm:text-5xl font-black text-mx-indigo mb-4">
                    هل أنت مدرّب؟
                    <span style="color:#FB5607">انضم إلينا</span>
                </h2>
                <p class="text-slate-600 text-base sm:text-lg max-w-3xl mx-auto leading-8 mb-7">
                    شارك خبراتك مع آلاف المعلمين وساهم في بناء جيل من المعلمين المحترفين
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4">
                    <a href="<?php echo e(route('register')); ?>" class="btn-primary inline-flex items-center justify-center gap-3 !bg-[#FB5607] hover:!bg-[#e84d00] text-white font-bold text-base sm:text-lg px-8 py-4 rounded-2xl">
                        سجّل كمدرّب
                        <i class="fas fa-arrow-<?php echo e($isRtl?'left':'right'); ?> text-sm"></i>
                    </a>
                    <a href="<?php echo e(route('public.courses')); ?>" class="btn-secondary inline-flex items-center justify-center gap-3 !bg-[#283593] !text-white !border-[#283593] hover:!bg-[#1f2a7a] font-semibold text-base sm:text-lg px-8 py-4 rounded-2xl">
                        تصفّح الكورسات
                        <i class="fas fa-arrow-<?php echo e($isRtl?'left':'right'); ?> text-sm"></i>
                    </a>
                </div>
            </div>
            </div>
        </section>
    </main>

    <?php echo $__env->make('components.unified-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <script>
    (function(){
        function p(){var s=window.pageYOffset||document.documentElement.scrollTop,h=document.documentElement.scrollHeight-window.innerHeight,b=document.getElementById('scroll-progress');if(b)b.style.width=(h>0?(s/h)*100:0)+'%';}
        window.addEventListener('scroll',p,{passive:true});
        function r(){var t=document.querySelectorAll('.reveal');if(!t.length)return;var o=new IntersectionObserver(function(e){e.forEach(function(n){if(n.isIntersecting){n.target.classList.add('revealed');o.unobserve(n.target);}});},{threshold:.08,rootMargin:'0px 0px -40px 0px'});t.forEach(function(el){o.observe(el);});}
        if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',r);else r();
    })();
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\instructors\index.blade.php ENDPATH**/ ?>