<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $teacherPortfolioLabel = $locale === 'ar' ? 'بورتفوليو المعلّمين' : 'Teachers Portfolio';
    $marketing = $teacher->publicPortfolioMarketingFields();
    $headline = trim((string) ($marketing['headline'] ?? ''));
    $about = trim((string) ($marketing['about'] ?? ''));
    $aboutDisplay = $about !== '' ? $about : \Illuminate\Support\Str::limit(strip_tags((string) ($teacher->bio ?? '')), 260);
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title><?php echo e($teacher->name); ?> | <?php echo e($teacherPortfolioLabel); ?></title>
    <meta name="theme-color" content="#283593">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        *{font-family:'Cairo','IBM Plex Sans Arabic','Tajawal',system-ui,sans-serif}
        h1,h2,h3,h4,h5,h6,.font-heading{font-family:'Cairo','Tajawal','IBM Plex Sans Arabic',sans-serif}
        html{scroll-behavior:smooth;overflow-x:hidden}
        body{overflow-x:hidden;background:#fff;min-height:100vh;display:flex;flex-direction:column}
        .container-1200{max-width:1200px;margin-inline:auto;padding-inline:24px}
        @media (max-width: 768px){.container-1200{padding-inline:16px}}
        .card-base{border-radius:18px;padding:20px;box-shadow:0 8px 24px -18px rgba(31,42,122,.25);border:1px solid #eceef8;background:#fff}
        .hover-lift{transition:transform .25s ease,box-shadow .25s ease}
        .hover-lift:hover{transform:translateY(-4px) scale(1.01);box-shadow:0 20px 35px -20px rgba(31,42,122,.35)}
        .line-clamp-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
        #scroll-progress{position:fixed;top:0;left:0;height:3px;width:0;background:linear-gradient(90deg,#FB5607,#FFE569);z-index:9999}
        .navbar-spacer{display:block!important}
        #navbar,#navbar.nav-transparent,#navbar.nav-solid{
            background:rgba(31,42,122,.92)!important;
            backdrop-filter:blur(12px)!important;
            -webkit-backdrop-filter:blur(12px)!important;
            border-bottom:1px solid rgba(255,255,255,.08)!important;
        }
    </style>
</head>
<body class="text-slate-800">
<div id="scroll-progress"></div>
<?php echo $__env->make('components.unified-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="flex-1">
    <section class="pt-10 sm:pt-14 lg:pt-16 pb-10 sm:pb-12 overflow-hidden relative" style="background:radial-gradient(circle at 12% 80%,rgba(255,229,247,.65),transparent 28%),radial-gradient(circle at 88% 20%,rgba(40,53,147,.10),transparent 30%),linear-gradient(180deg,#f4f6ff 0%,#fbfbff 55%,#ffffff 100%)">
        <div class="absolute inset-0 pointer-events-none opacity-40" style="background-image:radial-gradient(circle at 1px 1px,rgba(40,53,147,.08) 1px,transparent 0);background-size:30px 30px"></div>
        <div class="container-1200 relative z-10">
            <a href="<?php echo e(route('public.portfolio.index', array_filter(['path' => $categoryId]))); ?>" class="inline-flex items-center gap-2 text-sm font-bold text-[#1F2A7A] hover:text-[#FB5607] mb-5">
                <i class="fas fa-arrow-<?php echo e($isRtl ? 'right' : 'left'); ?>"></i>
                <?php echo e($teacherPortfolioLabel); ?>

            </a>

            <section class="card-base border-0 shadow-[0_20px_40px_-24px_rgba(31,42,122,.35)]">
                <div class="flex flex-col md:flex-row gap-5 md:items-center">
                    <div class="shrink-0">
                        <?php if($teacher->public_portfolio_marketing_photo_url): ?>
                            <img src="<?php echo e($teacher->public_portfolio_marketing_photo_url); ?>" alt="<?php echo e($teacher->name); ?>" class="w-24 h-24 rounded-2xl object-cover border-2 border-slate-100">
                        <?php else: ?>
                            <div class="w-24 h-24 rounded-2xl bg-[#283593] text-white flex items-center justify-center text-3xl font-black"><?php echo e(mb_substr($teacher->name ?? 'م', 0, 1)); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="min-w-0">
                        <h1 class="font-heading text-2xl sm:text-3xl font-black text-[#1F2A7A] mb-1"><?php echo e($teacher->name); ?></h1>
                        <?php if($headline !== ''): ?>
                            <p class="text-[#FB5607] font-bold mb-2"><?php echo e($headline); ?></p>
                        <?php endif; ?>
                        <?php if($aboutDisplay !== ''): ?>
                            <p class="text-sm sm:text-base text-slate-600 leading-7"><?php echo e($aboutDisplay); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
    </section>

    <section class="py-14 sm:py-16 bg-white">
        <div class="container-1200">
        <?php if($projects->count() > 0): ?>
            <section class="pt-1">
                <h2 class="font-heading text-2xl sm:text-3xl font-black text-[#1F2A7A] mb-5">مشاريع المعلّم</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                    <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $thumb = \App\Services\PortfolioImageStorage::publicUrl($project->preview_image_path);
                        ?>
                        <a href="<?php echo e(route('public.portfolio.show', $project->id)); ?>" class="card-base hover-lift p-0 overflow-hidden block group">
                            <div class="relative aspect-video overflow-hidden" style="background:linear-gradient(135deg,#e9edff,#f8f9ff)">
                                <?php if($thumb): ?>
                                    <img src="<?php echo e($thumb); ?>" alt="<?php echo e($project->title); ?>" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                <?php else: ?>
                                    <div class="absolute inset-0 flex items-center justify-center text-[#283593]/65"><i class="fas fa-code text-4xl"></i></div>
                                <?php endif; ?>
                            </div>
                            <div class="p-5">
                                <h3 class="font-heading font-extrabold text-[#1F2A7A] line-clamp-2 mb-2"><?php echo e($project->title); ?></h3>
                                <p class="text-sm text-slate-500 line-clamp-2 mb-3"><?php echo e(\Illuminate\Support\Str::limit(strip_tags($project->description ?? ''), 100)); ?></p>
                                <span class="text-[#FB5607] text-xs font-bold">فتح المشروع</span>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </section>

            <?php if($projects->hasPages()): ?>
                <div class="mt-8"><?php echo e($projects->links()); ?></div>
            <?php endif; ?>
        <?php else: ?>
            <section class="card-base text-center py-12">
                <p class="text-slate-600">لا توجد مشاريع منشورة حالياً لهذا المعلّم.</p>
            </section>
        <?php endif; ?>
        </div>
    </section>
</main>

<?php echo $__env->make('components.unified-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<script>
(function(){
    function progress(){
        var s=window.pageYOffset||document.documentElement.scrollTop;
        var h=document.documentElement.scrollHeight-window.innerHeight;
        var p=h>0?(s/h)*100:0;
        var b=document.getElementById('scroll-progress');
        if(b)b.style.width=p+'%';
    }
    window.addEventListener('scroll',progress,{passive:true});
    progress();
})();
</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\public\portfolio\teacher.blade.php ENDPATH**/ ?>