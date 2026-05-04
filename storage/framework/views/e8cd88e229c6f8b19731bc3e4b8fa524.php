<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $teacherPortfolioLabel = $locale === 'ar' ? 'بورتفوليو المعلّمين' : 'Teachers Portfolio';
    $teacherPortfolioSubtitle = $locale === 'ar'
        ? 'استعرض بروفايلات المعلّمين ومشاريعهم المنشورة داخل المنصّة.'
        : 'Browse teacher profiles and their published portfolio projects.';
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title><?php echo e($teacherPortfolioLabel); ?> - <?php echo e(__('public.site_suffix')); ?></title>
    <meta name="description" content="<?php echo e($teacherPortfolioSubtitle); ?>">
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
                    <i class="fas fa-code"></i> <?php echo e($teacherPortfolioLabel); ?>

                </span>
                <h1 class="font-heading text-[2rem] sm:text-[2.8rem] lg:text-[3.35rem] leading-[1.22] font-black text-mx-indigo mb-5"><?php echo e(__('public.portfolio_heading')); ?></h1>
                <p class="text-slate-600 text-base sm:text-lg leading-8 mb-7 max-w-3xl mx-auto"><?php echo e($teacherPortfolioSubtitle); ?></p>
            </div>

            <?php if($learningPaths->count() > 0): ?>
            <div class="flex flex-wrap justify-center gap-2 sm:gap-3 reveal s2">
                <a href="<?php echo e(route('public.portfolio.index', array_filter(['q' => $search, 'sort' => $sort]))); ?>" class="px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl text-xs sm:text-sm font-bold border transition <?php echo e(!$categoryId ? 'bg-[#283593] text-white border-[#283593]' : 'bg-white text-[#1F2A7A] border-slate-200 hover:bg-slate-50'); ?>">
                    <i class="fas fa-th-large <?php echo e($isRtl ? 'ml-1.5' : 'mr-1.5'); ?> text-[11px]"></i><?php echo e(__('public.all')); ?>

                </a>
                <?php $__currentLoopData = $learningPaths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $path): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('public.portfolio.index', array_filter(['path' => $path->id, 'q' => $search, 'sort' => $sort]))); ?>" class="px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl text-xs sm:text-sm font-bold border transition <?php echo e((string)$categoryId === (string)$path->id ? 'bg-[#283593] text-white border-[#283593]' : 'bg-white text-[#1F2A7A] border-slate-200 hover:bg-slate-50'); ?>"><?php echo e($path->name); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>

            <form method="GET" action="<?php echo e(route('public.portfolio.index')); ?>" class="reveal s3 mt-5">
                <?php if($categoryId): ?>
                    <input type="hidden" name="path" value="<?php echo e($categoryId); ?>">
                <?php endif; ?>
                <div class="mx-auto max-w-4xl card-base !p-4 sm:!p-5">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                        <div class="md:col-span-7">
                            <label for="portfolio-search" class="sr-only">Search</label>
                            <div class="relative">
                                <i class="fas fa-search absolute top-1/2 -translate-y-1/2 <?php echo e($isRtl ? 'right-4' : 'left-4'); ?> text-slate-400 text-xs"></i>
                                <input id="portfolio-search" type="text" name="q" value="<?php echo e($search); ?>" placeholder="<?php echo e($isRtl ? 'ابحث باسم المعلّم أو نبذة...' : 'Search by teacher name or bio...'); ?>" class="w-full rounded-xl border border-slate-200 bg-white py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#283593]/20 <?php echo e($isRtl ? 'pr-10 pl-3' : 'pl-10 pr-3'); ?>">
                            </div>
                        </div>
                        <div class="md:col-span-3">
                            <label for="portfolio-sort" class="sr-only">Sort</label>
                            <select id="portfolio-sort" name="sort" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#283593]/20">
                                <option value="most_projects" <?php echo e($sort === 'most_projects' ? 'selected' : ''); ?>><?php echo e($isRtl ? 'الأكثر مشاريع' : 'Most projects'); ?></option>
                                <option value="recent" <?php echo e($sort === 'recent' ? 'selected' : ''); ?>><?php echo e($isRtl ? 'الأحدث' : 'Most recent'); ?></option>
                                <option value="name" <?php echo e($sort === 'name' ? 'selected' : ''); ?>><?php echo e($isRtl ? 'الاسم (أ-ي)' : 'Name (A-Z)'); ?></option>
                            </select>
                        </div>
                        <div class="md:col-span-2 flex gap-2">
                            <button type="submit" class="w-full rounded-xl bg-[#283593] text-white text-sm font-bold py-3 hover:bg-[#1f2a7a] transition"><?php echo e($isRtl ? 'تطبيق' : 'Apply'); ?></button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mt-8 reveal s4 max-w-2xl mx-auto">
                <article class="rounded-2xl p-4 sm:p-5 border border-slate-200 bg-white text-center shadow-[0_10px_24px_-18px_rgba(31,42,122,.25)]">
                    <p class="text-3xl sm:text-4xl font-black text-mx-indigo"><?php echo e($teachersCount); ?></p>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1"><?php echo e($isRtl ? 'معلّم' : 'Teachers'); ?></p>
                </article>
                <article class="rounded-2xl p-4 sm:p-5 border border-slate-200 bg-[#fffbea] text-center shadow-[0_10px_24px_-18px_rgba(31,42,122,.25)]">
                    <p class="text-3xl sm:text-4xl font-black text-[#FB5607]"><?php echo e($teachers->count()); ?></p>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1">نتائج هذه الصفحة</p>
                </article>
            </div>
        </div>
    </section>

    <section class="py-14 sm:py-16 bg-white">
        <div class="container-1200">
            <?php if($teachers->count() > 0): ?>
            <div class="space-y-4 sm:space-y-5">
                <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $featuredProject = $teacher->portfolioProjects->first();
                    $marketing = $teacher->publicPortfolioMarketingFields();
                    $thumb = $featuredProject ? \App\Services\PortfolioImageStorage::publicUrl($featuredProject->preview_image_path) : null;
                    $bioSnippet = trim((string) ($marketing['headline'] ?? '')) !== ''
                        ? $marketing['headline']
                        : \Illuminate\Support\Str::limit(strip_tags((string) ($marketing['about'] ?? $teacher->bio ?? '')), 90);
                ?>
                <article class="card-base hover-lift reveal s<?php echo e(($idx % 4) + 1); ?> !p-0 overflow-hidden">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-0">
                        <a href="<?php echo e(route('public.portfolio.teacher', $teacher->id)); ?>" class="md:col-span-3 lg:col-span-2 relative min-h-[180px] block" style="background:linear-gradient(135deg,#e9edff,#f8f9ff)">
                            <?php if($teacher->public_portfolio_marketing_photo_url): ?>
                                <img src="<?php echo e($teacher->public_portfolio_marketing_photo_url); ?>" alt="<?php echo e($teacher->name); ?>" class="absolute inset-0 w-full h-full object-cover" loading="lazy">
                            <?php elseif($thumb): ?>
                                <img src="<?php echo e($thumb); ?>" alt="<?php echo e($teacher->name); ?>" class="absolute inset-0 w-full h-full object-cover" loading="lazy">
                            <?php else: ?>
                                <div class="absolute inset-0 flex items-center justify-center text-[#283593]/65">
                                    <i class="fas fa-user-graduate text-4xl"></i>
                                </div>
                            <?php endif; ?>
                        </a>

                        <div class="md:col-span-6 lg:col-span-7 p-5 sm:p-6">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <h3 class="font-heading text-xl font-extrabold text-mx-indigo leading-snug"><?php echo e($teacher->name ?? __('public.student_fallback')); ?></h3>
                                <?php if($featuredProject?->academicYear): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#EFF2FF] text-[#283593]"><?php echo e($featuredProject->academicYear->name); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if($featuredProject?->advancedCourse): ?>
                                <p class="text-xs text-[#FB5607] font-bold mb-3">
                                    <i class="fas fa-graduation-cap text-[10px] <?php echo e($isRtl ? 'ml-1' : 'mr-1'); ?>"></i><?php echo e($featuredProject->advancedCourse->title); ?>

                                </p>
                            <?php endif; ?>
                            <p class="text-sm text-slate-600 leading-7 mb-4"><?php echo e($bioSnippet); ?></p>
                            <div class="text-xs text-slate-500">
                                <?php echo e($isRtl ? 'آخر تحديث:' : 'Last update:'); ?> <?php echo e(optional($teacher->updated_at)->translatedFormat('j M Y')); ?>

                            </div>
                        </div>

                        <div class="md:col-span-3 p-5 sm:p-6 border-t md:border-t-0 md:border-s border-slate-100 flex flex-col justify-center gap-3 bg-slate-50/50">
                            <div class="rounded-xl bg-white border border-slate-200 p-3 text-center">
                                <p class="text-2xl font-black text-mx-indigo"><?php echo e($teacher->published_projects_count); ?></p>
                                <p class="text-xs text-slate-500"><?php echo e($isRtl ? 'إجمالي المشاريع' : 'Total projects'); ?></p>
                            </div>
                            <a href="<?php echo e(route('public.portfolio.teacher', $teacher->id)); ?>" class="w-full rounded-xl bg-[#FB5607] text-white text-sm font-bold py-2.5 text-center hover:bg-[#e94f06] transition">
                                <?php echo e($isRtl ? 'عرض البروفايل' : 'View profile'); ?>

                            </a>
                        </div>
                    </div>
                </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <?php if($teachers->hasPages()): ?>
            <div class="mt-10 flex justify-center">
                <nav class="flex items-center gap-2">
                    <?php if($teachers->onFirstPage()): ?>
                    <span class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center text-sm cursor-not-allowed"><i class="fas fa-chevron-<?php echo e($isRtl ? 'right' : 'left'); ?>"></i></span>
                    <?php else: ?>
                    <a href="<?php echo e($teachers->previousPageUrl()); ?>" class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-[#1F2A7A] hover:bg-slate-50 flex items-center justify-center text-sm"><i class="fas fa-chevron-<?php echo e($isRtl ? 'right' : 'left'); ?>"></i></a>
                    <?php endif; ?>

                    <?php $__currentLoopData = $teachers->getUrlRange(max(1, $teachers->currentPage() - 2), min($teachers->lastPage(), $teachers->currentPage() + 2)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($page == $teachers->currentPage()): ?>
                        <span class="w-10 h-10 rounded-xl bg-[#283593] text-white flex items-center justify-center text-sm font-bold"><?php echo e($page); ?></span>
                        <?php else: ?>
                        <a href="<?php echo e($url); ?>" class="w-10 h-10 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-[#1F2A7A] flex items-center justify-center text-sm"><?php echo e($page); ?></a>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php if($teachers->hasMorePages()): ?>
                    <a href="<?php echo e($teachers->nextPageUrl()); ?>" class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-[#1F2A7A] hover:bg-slate-50 flex items-center justify-center text-sm"><i class="fas fa-chevron-<?php echo e($isRtl ? 'left' : 'right'); ?>"></i></a>
                    <?php else: ?>
                    <span class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center text-sm cursor-not-allowed"><i class="fas fa-chevron-<?php echo e($isRtl ? 'left' : 'right'); ?>"></i></span>
                    <?php endif; ?>
                </nav>
            </div>
            <?php endif; ?>
            <?php else: ?>
            <div class="text-center py-20 reveal">
                <div class="max-w-md mx-auto card-base">
                    <div class="w-20 h-20 mx-auto rounded-2xl bg-[#FFE5F7] flex items-center justify-center text-[#283593] mb-5"><i class="fas fa-folder-open text-3xl"></i></div>
                    <h3 class="font-heading text-2xl font-black text-mx-indigo mb-3"><?php echo e(__('public.no_projects_yet')); ?></h3>
                    <p class="text-slate-600 leading-8 mb-6"><?php echo e(__('public.no_projects_desc')); ?></p>
                    <a href="<?php echo e(route('public.courses')); ?>" class="btn-primary inline-flex items-center justify-center gap-2"><?php echo e(__('public.browse_courses')); ?> <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?> text-xs"></i></a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="pt-14 sm:pt-18 pb-10 sm:pb-12" style="background:linear-gradient(180deg,#f4f7ff 0%,#ffffff 100%)">
        <div class="container-1200">
            <div class="reveal rounded-[28px] border border-slate-200 bg-white shadow-[0_20px_44px_-26px_rgba(31,42,122,.28)] px-6 sm:px-10 py-10 sm:py-12 text-center">
                <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-bold mb-5" style="background:#FFE5F7;color:#283593"><i class="fas fa-rocket"></i> لديك مشروع مميز؟</span>
                <h2 class="font-heading text-3xl sm:text-5xl font-black text-mx-indigo mb-4">شاركه الآن داخل معرض Muallimx</h2>
                <p class="text-slate-600 text-base sm:text-lg max-w-3xl mx-auto leading-8 mb-7">ابدأ التعلم، أنشئ مشروعك، ثم اعرضه بشكل احترافي ليراه الجميع.</p>
                <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4">
                    <a href="<?php echo e(route('register')); ?>" class="btn-primary inline-flex items-center justify-center gap-2">إنشاء حساب مجاني <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?> text-xs"></i></a>
                    <a href="<?php echo e(route('public.courses')); ?>" class="btn-secondary inline-flex items-center justify-center gap-2 !bg-[#283593] !text-white !border-[#283593] hover:!bg-[#1f2a7a]">استكشف البرامج</a>
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
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\public\portfolio\index.blade.php ENDPATH**/ ?>