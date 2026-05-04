<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title><?php echo e(__('public.courses_page_title')); ?> - <?php echo e(__('public.site_suffix')); ?></title>
    <meta name="title"       content="<?php echo e(__('public.courses_page_title')); ?> - <?php echo e(__('public.site_suffix')); ?>">
    <meta name="description" content="<?php echo e(__('public.courses_subtitle')); ?>">
    <meta name="keywords"    content="كورسات أونلاين, تعلم أونلاين, دورات تعليمية, تدريب معلمين, Muallimx, كورسات عربية">
    <meta name="author"      content="Muallimx">
    <meta name="robots"      content="index, follow, max-image-preview:large, max-snippet:-1">
    <meta name="theme-color" content="#283593">
    <link rel="canonical"    href="<?php echo e(url('/courses')); ?>">
    <link rel="alternate" hreflang="ar"        href="<?php echo e(url('/courses')); ?>?lang=ar">
    <link rel="alternate" hreflang="en"        href="<?php echo e(url('/courses')); ?>?lang=en">
    <link rel="alternate" hreflang="x-default" href="<?php echo e(url('/courses')); ?>">
    <!-- Open Graph -->
    <meta property="og:type"             content="website">
    <meta property="og:url"              content="<?php echo e(url('/courses')); ?>">
    <meta property="og:title"            content="<?php echo e(__('public.courses_page_title')); ?> - Muallimx">
    <meta property="og:description"      content="<?php echo e(__('public.courses_subtitle')); ?>">
    <meta property="og:image"            content="<?php echo e(asset('images/og-image.jpg')); ?>">
    <meta property="og:image:alt"        content="كورسات Muallimx">
    <meta property="og:image:width"      content="1200">
    <meta property="og:image:height"     content="630">
    <meta property="og:locale"           content="<?php echo e($locale === 'ar' ? 'ar_AR' : 'en_US'); ?>">
    <meta property="og:locale:alternate" content="<?php echo e($locale === 'ar' ? 'en_US' : 'ar_AR'); ?>">
    <meta property="og:site_name"        content="Muallimx">
    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:site"        content="@Muallimx">
    <meta name="twitter:url"         content="<?php echo e(url('/courses')); ?>">
    <meta name="twitter:title"       content="<?php echo e(__('public.courses_page_title')); ?> - Muallimx">
    <meta name="twitter:description" content="<?php echo e(__('public.courses_subtitle')); ?>">
    <meta name="twitter:image"       content="<?php echo e(asset('images/og-image.jpg')); ?>">
    <meta name="twitter:image:alt"   content="كورسات Muallimx">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <!-- BreadcrumbList JSON-LD -->
    <script type="application/ld+json">
    {"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"الرئيسية","item":"<?php echo e(url('/')); ?>"},{"@type":"ListItem","position":2,"name":"الكورسات","item":"<?php echo e(url('/courses')); ?>"}]}
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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
      [x-cloak]{display:none!important}
      *{font-family:'Cairo','IBM Plex Sans Arabic','Tajawal',system-ui,sans-serif}
      h1,h2,h3,h4,h5,h6,.font-heading{font-family:'Cairo','Tajawal','IBM Plex Sans Arabic',sans-serif}
      html{scroll-behavior:smooth;overflow-x:hidden}
      body{overflow-x:hidden;background:#fff;min-height:100vh;display:flex;flex-direction:column}

      .container-1200{max-width:1200px;margin-inline:auto;padding-inline:24px}
      @media (max-width:768px){.container-1200{padding-inline:16px}}

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
    </style>
</head>
<body class="font-body text-slate-800"
      x-data="{
        searchQuery: '',
        selectedCategoryId: '',
        courses: <?php echo \Illuminate\Support\Js::from($courses ?? [])->toHtml() ?>,
        get filteredCourses() {
          const q = this.searchQuery.toLowerCase().trim();
          const cat = this.selectedCategoryId;
          return this.courses.filter(c => {
            const matchQ = !q || (c.title && c.title.toLowerCase().includes(q)) || (c.description && c.description.toLowerCase().includes(q));
            const matchC = !cat || String(c.course_category_id || '') === String(cat);
            return matchQ && matchC;
          });
        }
      }">
<div id="scroll-progress"></div>

<?php echo $__env->make('components.unified-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="flex-1">
  
  <section class="pt-10 sm:pt-14 lg:pt-16 pb-10 sm:pb-12 overflow-hidden relative" style="background:radial-gradient(circle at 12% 80%,rgba(255,229,247,.65),transparent 28%),radial-gradient(circle at 88% 20%,rgba(40,53,147,.10),transparent 30%),linear-gradient(180deg,#f4f6ff 0%,#fbfbff 55%,#ffffff 100%)">
    <div class="absolute inset-0 pointer-events-none opacity-40" style="background-image:radial-gradient(circle at 1px 1px,rgba(40,53,147,.08) 1px,transparent 0);background-size:30px 30px"></div>
    <div class="container-1200 relative z-10">
      <div class="max-w-4xl mx-auto text-center reveal">
        <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-bold mb-6" style="background:#FFE5F7;color:#283593;border:1px solid #f5c7e8">
          <i class="fas fa-graduation-cap"></i> <?php echo e(__('public.courses_page_title')); ?>

        </span>
        <h1 class="font-heading text-[2rem] sm:text-[2.8rem] lg:text-[3.35rem] leading-[1.22] font-black text-mx-indigo mb-5">
          <?php echo e(__('public.courses_hero')); ?>

          <span class="block" style="color:#FB5607"><?php echo e(__('public.courses_hero_highlight')); ?></span>
        </h1>
        <p class="text-slate-600 text-base sm:text-lg leading-8 mb-7 max-w-3xl mx-auto"><?php echo e(__('public.courses_subtitle')); ?></p>

        <div class="grid sm:grid-cols-[1fr_180px] gap-3 max-w-3xl mx-auto">
          <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3">
            <i class="fas fa-search text-slate-400"></i>
            <input type="text" x-model="searchQuery" placeholder="<?php echo e(__('public.search_course_placeholder')); ?>" class="flex-1 bg-transparent border-0 outline-none text-mx-indigo placeholder-slate-400">
          </div>
          <div class="relative">
            <select x-model="selectedCategoryId" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 <?php echo e($isRtl?'pl-10':'pr-10'); ?> text-mx-indigo focus:outline-none">
              <option value=""><?php echo e(__('public.all_course_categories')); ?></option>
              <?php $__currentLoopData = $courseFilterCategories ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->name); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <i class="fas fa-chevron-down absolute <?php echo e($isRtl?'left':'right'); ?>-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3 sm:gap-4 mt-10 max-w-xl mx-auto reveal s2">
        <article class="rounded-2xl p-4 sm:p-5 border border-slate-200 bg-white text-center shadow-[0_10px_24px_-18px_rgba(31,42,122,.25)]">
          <p class="text-3xl sm:text-4xl font-black text-mx-indigo" x-text="courses.length">0</p>
          <p class="text-xs sm:text-sm text-slate-600 mt-1"><?php echo e(__('public.courses_stats_available')); ?></p>
        </article>
        <article class="rounded-2xl p-4 sm:p-5 border border-slate-200 bg-[#f8faff] text-center shadow-[0_10px_24px_-18px_rgba(31,42,122,.25)]">
          <p class="text-3xl sm:text-4xl font-black text-mx-indigo" x-text="courses.filter(c=>c.is_featured).length">0</p>
          <p class="text-xs sm:text-sm text-slate-700 mt-1"><?php echo e(__('public.courses_stats_featured')); ?></p>
        </article>
      </div>
    </div>
  </section>

  
  <section class="py-14 sm:py-16 bg-white">
    <div class="container-1200">
      <div class="flex items-end justify-between mb-7 gap-4">
        <div class="reveal max-w-2xl">
          <h2 class="font-heading text-3xl sm:text-4xl font-black text-mx-indigo mb-2"><?php echo e(__('public.courses_section_title')); ?></h2>
          <p class="text-slate-600"><?php echo e(__('public.courses_section_subtitle')); ?></p>
        </div>
      </div>

      <template x-if="filteredCourses && filteredCourses.length > 0">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
          <template x-for="(course, index) in filteredCourses" :key="course.id">
            <a :href="'<?php echo e(url('/course')); ?>/' + course.id" class="card-base hover-lift block reveal" :class="'s' + (Math.min((index % 4) + 1,4))">
              <div class="rounded-xl h-44 mb-4 relative overflow-hidden" style="background:linear-gradient(135deg,#e9edff,#f8f9ff)">
                <template x-if="course.thumbnail">
                  <img :src="'<?php echo e(asset('storage')); ?>/' + course.thumbnail" :alt="course.title" class="absolute inset-0 w-full h-full object-cover">
                </template>
                <template x-if="course.is_featured">
                  <span class="absolute top-3 <?php echo e($isRtl?'right':'left'); ?>-3 text-[11px] font-bold px-3 py-1 rounded-full" style="background:#FFE569;color:#5c4500"><?php echo e(__('public.featured_badge')); ?></span>
                </template>
              </div>

              <template x-if="(course.course_category && course.course_category.name) || (course.academic_subject && course.academic_subject.name)">
                <span class="inline-block text-[11px] font-bold px-3 py-1 rounded-full mb-3" style="background:#FFE5F7;color:#283593" x-text="(course.course_category && course.course_category.name) || (course.academic_subject && course.academic_subject.name)"></span>
              </template>

              <h3 class="font-heading text-lg font-extrabold text-mx-indigo leading-snug mb-2 line-clamp-2" x-text="course.title || '<?php echo e(addslashes(__('public.no_title_fallback'))); ?>'"></h3>
              <p class="text-sm text-slate-500 mb-4 line-clamp-2" x-text="(course.description || '').substring(0,120) + ((course.description && course.description.length>120) ? '...' : '')"></p>

              <div class="flex items-center justify-between text-sm mb-4">
                <span class="text-slate-500" x-text="(course.lectures_count || 0) + ' <?php echo e(__('public.lecture_single')); ?>'"></span>
                <span class="text-slate-500" x-text="course.duration_hours ? course.duration_hours + ' <?php echo e(__('public.hours')); ?>' : ''"></span>
              </div>

              <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <div>
                  <template x-if="course.is_free || (!course.price || course.price <= 0)">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-600 font-bold text-sm"><?php echo e(__('public.free_price')); ?></span>
                  </template>
                  <template x-if="!course.is_free && course.price > 0 && course.has_promo_price && course.sale_price < course.price">
                    <div class="flex flex-col gap-0.5">
                      <span class="text-xs text-slate-400 line-through tabular-nums"><span x-text="course.price"></span> <?php echo e(__('public.currency_egp')); ?></span>
                      <div><span class="text-xl font-black text-mx-orange tabular-nums" x-text="course.sale_price"></span> <span class="text-xs text-slate-400"><?php echo e(__('public.currency_egp')); ?></span></div>
                    </div>
                  </template>
                  <template x-if="!course.is_free && course.price > 0 && (!course.has_promo_price || course.sale_price >= course.price)">
                    <div><span class="text-xl font-black text-mx-orange tabular-nums" x-text="course.sale_price != null ? course.sale_price : course.price"></span> <span class="text-xs text-slate-400"><?php echo e(__('public.currency_egp')); ?></span></div>
                  </template>
                </div>
                <span class="btn-secondary !py-2 !px-4 !text-sm"><?php echo e(__('public.view_details')); ?></span>
              </div>
            </a>
          </template>
        </div>
      </template>

      <div x-show="filteredCourses && filteredCourses.length === 0" x-cloak class="text-center py-16 reveal">
        <h3 class="font-heading text-2xl font-black text-mx-indigo mb-2"><?php echo e(__('public.no_results')); ?></h3>
        <p class="text-slate-500 mb-6"><?php echo e(__('public.no_results_hint')); ?></p>
        <button @click="searchQuery=''; selectedCategoryId='';" class="btn-secondary">إعادة تعيين البحث</button>
      </div>
    </div>
  </section>

  
  <section class="pt-14 sm:pt-18 pb-10 sm:pb-12" style="background:linear-gradient(180deg,#f4f7ff 0%,#ffffff 100%)">
    <div class="container-1200">
      <div class="reveal rounded-[28px] border border-slate-200 bg-white shadow-[0_20px_44px_-26px_rgba(31,42,122,.28)] px-6 sm:px-10 py-10 sm:py-12 text-center">
        <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-bold mb-5" style="background:#FFE5F7;color:#283593">
          <i class="fas fa-rocket"></i> ابدأ رحلتك الآن
        </span>
        <h2 class="font-heading text-3xl sm:text-5xl font-black text-mx-indigo mb-4">جاهز لاختيار كورسك المناسب؟</h2>
        <p class="text-slate-600 text-base sm:text-lg max-w-3xl mx-auto leading-8 mb-7">سجّل الآن وابدأ التعلم بخطوات واضحة وأدوات عملية.</p>
        <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4">
          <a href="<?php echo e(route('register')); ?>" class="btn-primary inline-flex items-center justify-center gap-2"><?php echo e(__('public.register_free_now')); ?></a>
          <a href="<?php echo e(route('login')); ?>" class="btn-secondary inline-flex items-center justify-center gap-2"><?php echo e(__('public.have_account')); ?></a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php echo $__env->make('components.unified-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
(function(){
  'use strict';
  function progress(){var s=window.pageYOffset||document.documentElement.scrollTop,h=document.documentElement.scrollHeight-window.innerHeight,p=h>0?(s/h)*100:0,b=document.getElementById('scroll-progress');if(b)b.style.width=p+'%';}
  window.addEventListener('scroll',progress,{passive:true});
  function reveal(){var els=document.querySelectorAll('.reveal');if(!els.length)return;var io=new IntersectionObserver(function(entries){entries.forEach(function(e){if(e.isIntersecting){e.target.classList.add('revealed');io.unobserve(e.target);}});},{threshold:.12,rootMargin:'0px 0px -50px 0px'});els.forEach(function(el){io.observe(el)});}
  if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',reveal);}else{reveal();}
})();
</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\courses.blade.php ENDPATH**/ ?>