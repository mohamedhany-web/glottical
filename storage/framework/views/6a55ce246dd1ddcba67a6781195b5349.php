<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $g = 'landing.groups_page';
    $brand = config('app.name', 'Glottical');
    $footer = \App\Services\PublicFooterSettings::payload();
    $waUrl = $footer['whatsapp_url'] ?? '#';
    $fallbackImg = 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=800&q=80';
    $groups = $groups ?? $courses ?? collect();
    $count = (int) ($oneToOneCount ?? $groups->total());
    $countLabel = $count === 1
        ? __($g.'.courses_count_one')
        : __($g.'.courses_count', ['count' => $count]);
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title><?php echo e(__($g.'.catalog_solo_title')); ?> — <?php echo e($brand); ?></title>
  <meta name="description" content="<?php echo e(__($g.'.catalog_solo_desc')); ?>">
  <meta name="theme-color" content="#0B3D91">
  <link rel="canonical" href="<?php echo e(route('public.groups.one-to-one')); ?>">
  <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('partials.landing.head', ['landingCss' => ['theme', 'courses-catalog']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('partials.landing.groups-catalog-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<body class="sana-home sana-courses-page gl-gc-page">
<div id="sana-scroll-progress"></div>
<?php echo $__env->make('partials.landing.navbar', ['navActive' => 'groups', 'navSolid' => true, 'navHero' => false], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="sana-cat-page">
  <section class="sana-cat-hero">
    <div class="sana-container sana-cat-hero__inner sana-reveal">
      <div class="sana-cat-hero__breadcrumb">
        <a href="<?php echo e(route('home')); ?>"><?php echo e($isRtl ? 'الرئيسية' : 'Home'); ?></a>
        <span>/</span>
        <a href="<?php echo e(route('public.groups')); ?>"><?php echo e(__($g.'.title')); ?></a>
        <span>/</span>
        <span><?php echo e(__($g.'.catalog_solo_title')); ?></span>
      </div>
      <h1 class="sana-cat-hero__title">
        <?php echo e(__($g.'.catalog_solo_title')); ?>

        <span class="hl">1:1</span>
      </h1>
      <p class="sana-cat-hero__desc"><?php echo e(__($g.'.catalog_solo_desc')); ?></p>
      <p class="sana-cat-hero__stats">
        <span class="sana-cat-hero__stat"><i class="fas fa-user"></i> <?php echo e($countLabel); ?></span>
        <span class="sana-cat-hero__stat"><i class="fas fa-calendar"></i> <?php echo e($isRtl ? 'حسب جدول المدرب' : 'Tutor schedule'); ?></span>
      </p>
    </div>
  </section>

  <div class="sana-container gl-gc-body">
    <?php if($groups->isNotEmpty()): ?>
      <div class="gl-gc-grid sana-reveal">
        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php $thumb = $item->imageUrl() ?: $fallbackImg; ?>
          <a href="<?php echo e(route('public.groups.show', $item->slug)); ?>" class="gl-gc-card">
            <div class="gl-gc-card__media">
              <img src="<?php echo e($thumb); ?>" alt="<?php echo e($item->title); ?>" loading="lazy" width="600" height="375">
              <span class="gl-gc-card__badge"><i class="fas fa-user"></i> 1:1</span>
            </div>
            <div class="gl-gc-card__body">
              <h2><?php echo e($item->title); ?></h2>
              <p class="gl-gc-card__meta">
                <i class="fas fa-chalkboard-user"></i>
                <?php echo e($item->instructor->name ?? ($isRtl ? 'معلّم على المنصة' : 'Platform tutor')); ?>

              </p>
              <p class="gl-gc-card__meta"><i class="fas fa-clock"></i> <?php echo e($item->duration_minutes); ?> <?php echo e($isRtl ? 'دقيقة' : 'min'); ?></p>
              <div class="gl-gc-card__foot">
                <span class="gl-gc-card__price"><?php echo e($item->formattedPrice()); ?></span>
                <span class="gl-gc-card__cta"><?php echo e(__($g.'.details_cta')); ?> <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?>"></i></span>
              </div>
            </div>
          </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <?php if($groups->hasPages()): ?>
        <div class="gl-gc-pager"><?php echo e($groups->withQueryString()->links()); ?></div>
      <?php endif; ?>
    <?php else: ?>
      <div class="gl-gc-empty sana-reveal">
        <p><?php echo e(__($g.'.empty_solo')); ?></p>
        <a href="<?php echo e(route('public.groups')); ?>" class="sana-btn sana-btn--yellow"><?php echo e(__($g.'.title')); ?></a>
      </div>
    <?php endif; ?>

    <div class="gl-gc-band sana-reveal">
      <div class="gl-gc-band__inner">
        <div>
          <h2><?php echo e(__($g.'.cta_title')); ?></h2>
          <p><?php echo e(__($g.'.catalog_solo_cta_sub')); ?></p>
        </div>
        <div class="gl-gc-band__actions">
          <a href="<?php echo e(route('home')); ?>?open_trial=1" class="sana-btn sana-btn--yellow"><i class="fas fa-clipboard-check"></i> <?php echo e(__($g.'.cta_trial')); ?></a>
          <a href="<?php echo e(route('public.groups.courses')); ?>" class="sana-btn sana-btn--wa"><i class="fas fa-users"></i> <?php echo e(__($g.'.view_all_group')); ?></a>
          <a href="<?php echo e($waUrl); ?>" class="sana-btn" style="background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.28)" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> WhatsApp</a>
        </div>
      </div>
    </div>
  </div>
</main>

<?php echo $__env->make('partials.landing.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\public\groups-one-to-one.blade.php ENDPATH**/ ?>