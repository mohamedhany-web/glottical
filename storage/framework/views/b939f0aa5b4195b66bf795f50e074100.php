<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title><?php echo e($siteService->name); ?> - <?php echo e(__('public.services_page_title')); ?> | <?php echo e(__('public.site_suffix')); ?></title>
    <meta name="description" content="<?php echo e(Str::limit(strip_tags($siteService->summary ?: $siteService->body), 160)); ?>">
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *{font-family:'Cairo','IBM Plex Sans Arabic','Tajawal',system-ui,sans-serif}
        h1,h2,h3,h4,h5,h6,.font-heading{font-family:'Cairo','Tajawal','IBM Plex Sans Arabic',sans-serif}
        html{scroll-behavior:smooth;overflow-x:hidden}
        body{overflow-x:hidden;background:#fff;min-height:100vh;display:flex;flex-direction:column}
        .container-1200{max-width:1200px;margin-inline:auto;padding-inline:24px}
        @media (max-width: 768px){.container-1200{padding-inline:16px}}
        #scroll-progress{position:fixed;top:0;left:0;height:3px;width:0;background:linear-gradient(90deg,#FB5607,#FFE569);z-index:9999}
        .navbar-spacer{display:block!important}
        #navbar,#navbar.nav-transparent,#navbar.nav-solid{
            background:rgba(31,42,122,.92)!important;
            backdrop-filter:blur(12px)!important;
            -webkit-backdrop-filter:blur(12px)!important;
            border-bottom:1px solid rgba(255,255,255,.08)!important;
        }
        .service-prose{line-height:1.9;color:#334155;font-size:1.05rem}
        .service-prose p{margin-bottom:1rem}
    </style>
</head>
<body class="font-body text-slate-800">
<div id="scroll-progress"></div>
<?php echo $__env->make('components.unified-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="flex-1">
    <section class="pt-10 sm:pt-14 pb-10 sm:pb-14 overflow-hidden relative" style="background:radial-gradient(circle at 12% 80%,rgba(255,229,247,.55),transparent 30%),linear-gradient(180deg,#f4f6ff 0%,#ffffff 100%)">
        <div class="container-1200 relative z-10">
            <nav class="text-sm text-slate-500 mb-8 flex items-center gap-2 flex-wrap">
                <a href="<?php echo e(route('home')); ?>" class="hover:text-mx-indigo transition-colors"><?php echo e(__('public.home')); ?></a>
                <i class="fas fa-chevron-<?php echo e($isRtl ? 'left' : 'right'); ?> text-[8px] text-slate-400"></i>
                <a href="<?php echo e(route('public.services.index')); ?>" class="hover:text-mx-indigo transition-colors"><?php echo e(__('public.services_page_title')); ?></a>
                <i class="fas fa-chevron-<?php echo e($isRtl ? 'left' : 'right'); ?> text-[8px] text-slate-400"></i>
                <span class="text-mx-indigo font-semibold"><?php echo e(Str::limit($siteService->name, 48)); ?></span>
            </nav>

            <div class="max-w-3xl">
                <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-bold mb-5" style="background:#FFE5F7;color:#283593;border:1px solid #f5c7e8">
                    <i class="fas fa-concierge-bell"></i> <?php echo e(__('public.services_page_title')); ?>

                </span>
                <?php if($siteService->publicImageUrl()): ?>
                <div class="mb-8 rounded-[24px] overflow-hidden border border-slate-200 shadow-[0_16px_40px_-24px_rgba(31,42,122,.35)] max-w-2xl">
                    <img src="<?php echo e($siteService->publicImageUrl()); ?>" alt="" class="w-full h-auto max-h-[320px] object-cover">
                </div>
                <?php endif; ?>
                <h1 class="font-heading text-3xl sm:text-4xl lg:text-[2.75rem] font-black text-mx-indigo leading-tight mb-6"><?php echo e($siteService->name); ?></h1>
                <?php if($siteService->summary): ?>
                <p class="text-lg text-slate-600 leading-8 mb-8"><?php echo e($siteService->summary); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="pb-16 sm:pb-20 bg-white">
        <div class="container-1200">
            <article class="max-w-3xl rounded-[24px] border border-slate-200 bg-white shadow-[0_16px_40px_-24px_rgba(31,42,122,.35)] p-6 sm:p-10">
                <div class="service-prose">
                    <?php echo nl2br(e($siteService->body)); ?>

                </div>
            </article>

            <?php if($others->count() > 0): ?>
            <div class="mt-14">
                <h2 class="font-heading text-xl font-black text-mx-indigo mb-5"><?php echo e(__('public.services_more_title')); ?></h2>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php $__currentLoopData = $others; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('public.services.show', $o)); ?>" class="rounded-2xl border border-slate-200 p-4 hover:border-[#283593]/30 hover:shadow-lg transition-all bg-slate-50/50 hover:bg-white">
                        <h3 class="font-heading font-bold text-mx-indigo mb-2"><?php echo e($o->name); ?></h3>
                        <p class="text-sm text-slate-500 line-clamp-2"><?php echo e(Str::limit(strip_tags($o->summary ?: $o->body), 90)); ?></p>
                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="mt-12 flex flex-wrap gap-3">
                <a href="<?php echo e(route('public.services.index')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-200 text-mx-indigo font-bold hover:bg-slate-50 transition-colors">
                    <i class="fas fa-arrow-<?php echo e($isRtl ? 'right' : 'left'); ?> text-xs"></i>
                    <?php echo e(__('public.services_back_to_list')); ?>

                </a>
                <a href="<?php echo e(route('public.contact')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-bold transition-all" style="background:#FB5607">
                    <?php echo e(__('public.contact_us')); ?>

                    <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?> text-xs"></i>
                </a>
            </div>
        </div>
    </section>
</main>

<?php echo $__env->make('components.unified-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
(function(){
    function progress(){var s=window.pageYOffset||document.documentElement.scrollTop,h=document.documentElement.scrollHeight-window.innerHeight,p=h>0?(s/h)*100:0,b=document.getElementById('scroll-progress');if(b)b.style.width=p+'%';}
    window.addEventListener('scroll',progress,{passive:true});progress();
})();
</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\public\services\show.blade.php ENDPATH**/ ?>