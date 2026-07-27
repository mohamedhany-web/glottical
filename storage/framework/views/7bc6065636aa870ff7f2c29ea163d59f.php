<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $activeDelivery = $delivery ?? null;
    $activeCategoryId = (int) ($categoryId ?? 0);
    $searchQuery = $searchQuery ?? '';
    $courses = $courseModels ?? collect();
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title><?php echo e(__('public.courses_page_title')); ?> — Glottical</title>
  <meta name="description" content="<?php echo e(__('public.courses_subtitle')); ?>">
  <link rel="canonical" href="<?php echo e(route('public.courses')); ?>">
  <link rel="alternate" hreflang="ar" href="<?php echo e(url('/courses')); ?>?lang=ar">
  <link rel="alternate" hreflang="en" href="<?php echo e(url('/courses')); ?>?lang=en">
  <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('partials.seo-jsonld', ['jsonldType' => 'website'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('partials.landing.head', ['landingCss' => ['theme', 'courses-catalog']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <style>
    .gl-catalog-panel{
      margin:1.15rem 0 1.65rem;padding:1rem 1.05rem 1.1rem;
      background:#fff;border:1.5px solid #D7DDE6;border-radius:18px;
      box-shadow:0 10px 28px -20px rgba(11,61,145,.28);
    }
    .gl-search{
      display:grid;grid-template-columns:minmax(0,1fr) auto;gap:.65rem;align-items:stretch;
      margin:0 0 1rem;padding-bottom:1rem;border-bottom:1px solid #E8EEF8;
    }
    .gl-search input{
      width:100%;min-width:0;height:3rem;border-radius:14px;border:1.5px solid #D7DDE6;
      padding:0 1rem;font:600 .95rem Tajawal,sans-serif;background:#F4F7FC;color:#0B1220;
    }
    .gl-search input:focus{outline:none;border-color:#0B3D91;box-shadow:0 0 0 3px rgba(11,61,145,.12);background:#fff}
    .gl-search button{
      height:3rem;padding:0 1.35rem;border:none;border-radius:14px;
      background:var(--gold,#F5B800);color:var(--p-dark,#072A66);
      font:800 .9rem Tajawal,sans-serif;cursor:pointer;white-space:nowrap;
    }
    .gl-filter-block{display:grid;gap:.55rem}
    .gl-filter-block + .gl-filter-block{margin-top:.85rem;padding-top:.85rem;border-top:1px solid #E8EEF8}
    .gl-filter-block__label{
      margin:0;font:800 .72rem/1 Tajawal,sans-serif;color:#5B6577;
      letter-spacing:.02em;text-transform:none;
    }
    .gl-filter-block__chips{
      display:flex;flex-wrap:wrap;align-items:center;gap:.45rem .5rem;
    }
    .gl-chip{
      display:inline-flex;align-items:center;justify-content:center;
      min-height:2.35rem;padding:.4rem .95rem;border-radius:999px;
      border:1.5px solid #D7DDE6;background:#fff;
      font:700 .8rem/1.2 Tajawal,sans-serif;text-decoration:none!important;color:#0B1220;
      transition:background .15s ease,border-color .15s ease,color .15s ease;
    }
    .gl-chip:hover{border-color:rgba(11,61,145,.35);color:#0B3D91}
    .gl-chip.is-on{background:#0B3D91;border-color:#0B3D91;color:#fff}
    .gl-chip.is-on:hover{color:#fff;background:#072A66;border-color:#072A66}
    .gl-grid{display:grid;gap:1.1rem;grid-template-columns:repeat(auto-fill,minmax(230px,1fr))}
    .gl-grid .card-lift,.gl-grid article{background:#fff}
    @media (max-width:520px){
      .gl-search{grid-template-columns:1fr}
      .gl-search button{width:100%}
    }
  </style>
</head>
<body class="sana-home sana-courses-page">
<div id="sana-scroll-progress"></div>
<?php echo $__env->make('partials.landing.navbar', ['navActive' => 'courses', 'navSolid' => true, 'navHero' => false], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="sana-cat-page">
  <section class="sana-cat-hero" id="cat-hero">
    <div class="sana-container sana-cat-hero__inner sana-reveal">
      <div class="sana-cat-hero__breadcrumb">
        <a href="<?php echo e(route('home')); ?>"><?php echo e($isRtl ? 'الرئيسية' : 'Home'); ?></a>
        <span>/</span>
        <span><?php echo e(__('landing.nav.courses')); ?></span>
      </div>
      <h1 class="sana-cat-hero__title"><?php echo e(__('public.courses_page_title')); ?></h1>
      <p class="sana-cat-hero__desc"><?php echo e(__('public.courses_subtitle')); ?></p>
      <p class="sana-cat-hero__stats"><span class="sana-cat-hero__stat"><i class="fas fa-book-open"></i> <?php echo e(number_format($courses->count())); ?> <?php echo e($isRtl ? 'كورساً' : 'courses'); ?></span></p>
    </div>
  </section>

  <section class="sana-container" style="padding-bottom:4rem">
    <div class="gl-catalog-panel sana-reveal">
      <form action="<?php echo e(route('public.courses')); ?>" method="get" class="gl-search" role="search">
        <?php if($activeDelivery): ?><input type="hidden" name="delivery" value="<?php echo e($activeDelivery); ?>"><?php endif; ?>
        <?php if($activeCategoryId > 0): ?><input type="hidden" name="category" value="<?php echo e($activeCategoryId); ?>"><?php endif; ?>
        <input type="search" name="q" value="<?php echo e($searchQuery); ?>" placeholder="<?php echo e(__('public.search_course_placeholder') ?? ($isRtl ? 'ابحث عن كورس…' : 'Search…')); ?>" aria-label="<?php echo e($isRtl ? 'بحث الكورسات' : 'Search courses'); ?>">
        <button type="submit"><?php echo e($isRtl ? 'بحث' : 'Search'); ?></button>
      </form>

      <div class="gl-filter-block">
        <p class="gl-filter-block__label"><?php echo e($isRtl ? 'نوع التعلّم' : 'Delivery type'); ?></p>
        <div class="gl-filter-block__chips" role="group" aria-label="<?php echo e($isRtl ? 'نوع التعلّم' : 'Delivery type'); ?>">
          <a href="<?php echo e(route('public.courses', array_filter(['q' => $searchQuery ?: null, 'category' => $activeCategoryId ?: null]))); ?>" class="gl-chip <?php echo e(! $activeDelivery ? 'is-on' : ''); ?>"><?php echo e(__('public.courses_filter_all') ?? ($isRtl ? 'الكل' : 'All')); ?></a>
          <a href="<?php echo e(route('public.courses', array_filter(['delivery' => 'group', 'q' => $searchQuery ?: null, 'category' => $activeCategoryId ?: null]))); ?>" class="gl-chip <?php echo e($activeDelivery === 'group' ? 'is-on' : ''); ?>"><?php echo e(__('public.courses_filter_group') ?? ($isRtl ? 'جماعي' : 'Group')); ?></a>
          <a href="<?php echo e(route('public.courses', array_filter(['delivery' => 'one_to_one', 'q' => $searchQuery ?: null, 'category' => $activeCategoryId ?: null]))); ?>" class="gl-chip <?php echo e($activeDelivery === 'one_to_one' ? 'is-on' : ''); ?>"><?php echo e(__('public.courses_filter_one_to_one') ?? ($isRtl ? 'فردي' : '1:1')); ?></a>
        </div>
      </div>

      <?php if(($courseFilterCategories ?? collect())->isNotEmpty()): ?>
        <div class="gl-filter-block">
          <p class="gl-filter-block__label"><?php echo e($isRtl ? 'التصنيف' : 'Category'); ?></p>
          <div class="gl-filter-block__chips" role="group" aria-label="<?php echo e($isRtl ? 'التصنيف' : 'Category'); ?>">
            <a href="<?php echo e(route('public.courses', array_filter(['delivery' => $activeDelivery, 'q' => $searchQuery ?: null]))); ?>" class="gl-chip <?php echo e($activeCategoryId === 0 ? 'is-on' : ''); ?>"><?php echo e($isRtl ? 'كل التصنيفات' : 'All categories'); ?></a>
            <?php $__currentLoopData = $courseFilterCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <a href="<?php echo e(route('public.courses', array_filter(['category' => $cat->id, 'delivery' => $activeDelivery, 'q' => $searchQuery ?: null]))); ?>" class="gl-chip <?php echo e($activeCategoryId === (int) $cat->id ? 'is-on' : ''); ?>"><?php echo e($cat->name); ?></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <div class="gl-grid">
      <?php $__empty_1 = true; $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
          $thumb = $course->thumbnail_url ?: 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=600&q=80';
          $url = route('public.course.show', $course->id);
          $price = $course->is_free ? (__('landing.free') ?? 'مجاني') : (number_format((float) ($course->price_after_discount ?? $course->price ?? 0), 0).' '.__('landing.currency'));
        ?>
        <a href="<?php echo e($url); ?>" class="sana-reveal" style="text-decoration:none;color:inherit;background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 14px 40px -18px rgba(11,61,145,.28);display:flex;flex-direction:column">
          <div style="aspect-ratio:4/5;background:#e8eef8;overflow:hidden"><img src="<?php echo e($thumb); ?>" alt="<?php echo e($course->title); ?>" style="width:100%;height:100%;object-fit:cover" loading="lazy"></div>
          <div style="padding:14px 16px 18px;display:flex;flex-direction:column;gap:6px;flex:1">
            <p style="margin:0;font-size:.75rem;color:#64748b"><?php echo e($course->instructor->name ?? ''); ?></p>
            <h3 style="margin:0;font:800 .92rem/1.45 Cairo,Tajawal,sans-serif;color:#0B1220"><?php echo e($course->title); ?></h3>
            <p style="margin:auto 0 0;font:800 .95rem Tajawal,sans-serif;color:var(--p)"><?php echo e($price); ?></p>
          </div>
        </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p style="grid-column:1/-1;color:var(--muted);font-weight:700"><?php echo e($isRtl ? 'لا توجد كورسات مطابقة.' : 'No matching courses.'); ?></p>
      <?php endif; ?>
    </div>

    <div class="sana-reveal" style="margin-top:2.5rem;text-align:center">
      <a href="<?php echo e(url('/?open_trial=1')); ?>" class="sana-btn sana-btn--yellow sana-btn--lg"><i class="fas fa-clipboard-check"></i> <?php echo e(__('landing.academy.free_trial_cta')); ?></a>
    </div>
  </section>
</main>

<?php echo $__env->make('partials.landing.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\courses.blade.php ENDPATH**/ ?>