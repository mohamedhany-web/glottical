<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title><?php echo e(__('public.services_page_title')); ?> - <?php echo e(__('public.site_suffix')); ?></title>
    <meta name="description" content="<?php echo e(__('public.services_subtitle')); ?>">
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
        .line-clamp-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
        #scroll-progress{position:fixed;top:0;left:0;height:3px;width:0;background:linear-gradient(90deg,#FB5607,#FFE569);z-index:9999}
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
            <div class="max-w-4xl mx-auto text-center reveal">
                <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-bold mb-6" style="background:#FFE5F7;color:#283593;border:1px solid #f5c7e8">
                    <i class="fas fa-concierge-bell"></i> <?php echo e(__('public.services_page_title')); ?>

                </span>
                <h1 class="font-heading text-[2rem] sm:text-[2.8rem] lg:text-[3.35rem] leading-[1.22] font-black text-mx-indigo mb-5"><?php echo e(__('public.services_heading')); ?></h1>
                <p class="text-slate-600 text-base sm:text-lg leading-8 mb-7 max-w-3xl mx-auto"><?php echo e(__('public.services_subtitle')); ?></p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mt-8 reveal s2">
                <article class="rounded-2xl p-4 sm:p-5 border border-slate-200 bg-white text-center shadow-[0_10px_24px_-18px_rgba(31,42,122,.25)]">
                    <p class="text-3xl sm:text-4xl font-black text-mx-indigo"><?php echo e($services->count()); ?></p>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1"><?php echo e(__('public.services_count_label')); ?></p>
                </article>
                <article class="rounded-2xl p-4 sm:p-5 border border-slate-200 bg-[#FFE5F7] text-center shadow-[0_10px_24px_-18px_rgba(31,42,122,.25)]">
                    <p class="text-3xl sm:text-4xl font-black text-[#FB5607]"><i class="fas fa-check-circle"></i></p>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1"><?php echo e(__('public.services_quality_hint')); ?></p>
                </article>
                <article class="rounded-2xl p-4 sm:p-5 border border-slate-200 bg-[#fffbea] text-center shadow-[0_10px_24px_-18px_rgba(31,42,122,.25)]">
                    <p class="text-3xl sm:text-4xl font-black text-mx-indigo"><i class="fas fa-headset"></i></p>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1"><?php echo e(__('public.services_support_hint')); ?></p>
                </article>
            </div>
        </div>
    </section>

    <section class="py-14 sm:py-16 bg-white">
        <div class="container-1200">
            <?php if($services->count() > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('public.services.show', $service)); ?>" class="card-base hover-lift reveal s<?php echo e(($idx % 4) + 1); ?> p-0 overflow-hidden block group">
                    <div class="relative h-36 overflow-hidden flex items-center justify-center bg-slate-100">
                        <?php if($service->publicImageUrl()): ?>
                            <img src="<?php echo e($service->publicImageUrl()); ?>" alt="" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <?php else: ?>
                            <div class="text-[#283593]/80" style="background:linear-gradient(135deg,#e9edff,#f8f9ff);position:absolute;inset:0;display:flex;align-items:center;justify-content:center">
                                <i class="fas fa-layer-group text-5xl group-hover:scale-105 transition-transform duration-300"></i>
                            </div>
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent pointer-events-none"></div>
                    </div>
                    <div class="p-5">
                        <h2 class="font-heading text-lg font-extrabold text-mx-indigo leading-snug mb-2 line-clamp-2"><?php echo e($service->name); ?></h2>
                        <p class="text-sm text-slate-500 leading-7 line-clamp-2 mb-4">
                            <?php echo e(Str::limit(strip_tags($service->summary ?: $service->body), 120)); ?>

                        </p>
                        <div class="flex items-center justify-end pt-4 border-t border-slate-100">
                            <span class="text-[#FB5607] text-xs font-bold arrow-link"><?php echo e(__('public.services_read_more')); ?></span>
                        </div>
                    </div>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php else: ?>
            <div class="text-center py-20 reveal">
                <div class="max-w-md mx-auto card-base">
                    <div class="w-20 h-20 mx-auto rounded-2xl bg-[#FFE5F7] flex items-center justify-center text-[#283593] mb-5"><i class="fas fa-concierge-bell text-3xl"></i></div>
                    <h3 class="font-heading text-2xl font-black text-mx-indigo mb-3"><?php echo e(__('public.services_empty_title')); ?></h3>
                    <p class="text-slate-600 leading-8 mb-6"><?php echo e(__('public.services_empty_desc')); ?></p>
                    <a href="<?php echo e(route('home')); ?>" class="btn-primary inline-flex items-center justify-center gap-2"><?php echo e(__('public.home')); ?> <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?> text-xs"></i></a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="pt-14 sm:pt-18 pb-10 sm:pb-12" style="background:linear-gradient(180deg,#f4f7ff 0%,#ffffff 100%)">
        <div class="container-1200">
            <div class="reveal rounded-[28px] border border-slate-200 bg-white shadow-[0_20px_44px_-26px_rgba(31,42,122,.28)] px-6 sm:px-10 py-10 sm:py-12 text-center">
                <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-bold mb-5" style="background:#FFE5F7;color:#283593"><i class="fas fa-question-circle"></i> <?php echo e(__('public.services_cta_badge')); ?></span>
                <h2 class="font-heading text-2xl sm:text-4xl font-black text-mx-indigo mb-4"><?php echo e(__('public.services_cta_title')); ?></h2>
                <p class="text-slate-600 text-base sm:text-lg max-w-2xl mx-auto leading-8 mb-7"><?php echo e(__('public.services_cta_text')); ?></p>
                <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4">
                    <a href="<?php echo e(route('public.contact')); ?>" class="btn-primary inline-flex items-center justify-center gap-2"><?php echo e(__('public.contact_us')); ?> <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?> text-xs"></i></a>
                    <a href="<?php echo e(route('public.courses')); ?>" class="btn-secondary inline-flex items-center justify-center gap-2 !bg-[#283593] !text-white !border-[#283593] hover:!bg-[#1f2a7a]"><?php echo e(__('public.browse_courses')); ?></a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php echo $__env->make('components.unified-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
(function(){
    function progress(){var s=window.pageYOffset||document.documentElement.scrollTop,h=document.documentElement.scrollHeight-window.innerHeight,p=h>0?(s/h)*100:0,b=document.getElementById('scroll-progress');if(b)b.style.width=p+'%';}
    window.addEventListener('scroll',progress,{passive:true});
    function reveal(){var els=document.querySelectorAll('.reveal');if(!els.length)return;var io=new IntersectionObserver(function(entries){entries.forEach(function(e){if(e.isIntersecting){e.target.classList.add('revealed');io.unobserve(e.target);}});},{threshold:.12,rootMargin:'0px 0px -50px 0px'});els.forEach(function(el){io.observe(el)});}
    if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',reveal);}else{reveal();}
})();
</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\public\services\index.blade.php ENDPATH**/ ?>