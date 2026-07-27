<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $g = 'landing.groups_page';
    $brand = config('app.name', 'Glottical');
    $footer = \App\Services\PublicFooterSettings::payload();
    $waUrl = $footer['whatsapp_url'] ?? '#';
    $groupCount = (int) ($groupCount ?? 0);
    $oneToOneCount = (int) ($oneToOneCount ?? 0);
    $groupCourses = $groupCourses ?? collect();
    $oneToOneCourses = $oneToOneCourses ?? collect();
    $groupCountLabel = $groupCount === 1
        ? __($g.'.courses_count_one')
        : __($g.'.courses_count', ['count' => $groupCount]);
    $soloCountLabel = $oneToOneCount === 1
        ? __($g.'.courses_count_one')
        : __($g.'.courses_count', ['count' => $oneToOneCount]);
    $groupImg = 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80';
    $soloImg = 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=1200&q=80';
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title><?php echo e(__($g.'.meta_title')); ?> — <?php echo e($brand); ?></title>
  <meta name="description" content="<?php echo e(__($g.'.meta_desc')); ?>">
  <meta name="theme-color" content="#0B3D91">
  <link rel="canonical" href="<?php echo e(route('public.groups')); ?>">
  <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('partials.landing.head', ['landingCss' => ['theme', 'courses-catalog']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <style>
    .gl-groups-page { background: var(--bg, #F4F7FC); }
    .gl-groups-page .sana-cat-hero {
      padding: clamp(28px, 4.5vw, 44px) 0 clamp(32px, 5vw, 48px);
    }
    .gl-groups-page .sana-cat-hero__desc { margin-bottom: 16px; }
    .gl-groups-page .sana-cat-hero__stats { margin-bottom: 0; gap: 10px 14px; }
    .gl-groups-body {
      padding-top: clamp(24px, 3.5vw, 36px);
      padding-bottom: clamp(40px, 6vw, 64px);
    }
    .gl-groups-modes {
      display: grid;
      gap: 1rem;
      margin-bottom: clamp(1.5rem, 3vw, 2.25rem);
    }
    @media (min-width: 900px) {
      .gl-groups-modes { grid-template-columns: 1fr 1fr; gap: 1.15rem; }
    }
    .gl-groups-mode {
      position: relative;
      overflow: hidden;
      border-radius: 18px;
      min-height: 260px;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      text-decoration: none !important;
      color: #fff;
      box-shadow: 0 16px 40px -20px rgba(11,61,145,.4);
      border: 1.5px solid rgba(11,61,145,.12);
      transition: transform .22s ease, box-shadow .22s ease;
    }
    .gl-groups-mode:hover {
      transform: translateY(-3px);
      box-shadow: 0 22px 48px -18px rgba(11,61,145,.45);
    }
    .gl-groups-mode__media {
      position: absolute; inset: 0;
    }
    .gl-groups-mode__media img {
      width: 100%; height: 100%; object-fit: cover; display: block;
      transition: transform .45s ease;
    }
    .gl-groups-mode:hover .gl-groups-mode__media img { transform: scale(1.04); }
    .gl-groups-mode__shade {
      position: absolute; inset: 0;
      background:
        linear-gradient(180deg, rgba(5,31,77,.15) 0%, rgba(5,31,77,.55) 45%, rgba(5,31,77,.92) 100%);
    }
    .gl-groups-mode__body {
      position: relative; z-index: 1;
      padding: 1.15rem 1.2rem 1.25rem;
    }
    .gl-groups-mode__count {
      display: inline-flex; align-items: center; gap: 6px;
      margin-bottom: .45rem; padding: 4px 10px; border-radius: 999px;
      background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.2);
      font-size: .7rem; font-weight: 800; color: #FFE9A8;
    }
    .gl-groups-mode h2 {
      margin: 0 0 .4rem;
      font-family: Cairo,Tajawal,sans-serif;
      font-size: clamp(1.15rem, 2.2vw, 1.4rem);
      font-weight: 900; line-height: 1.3;
    }
    .gl-groups-mode p {
      margin: 0 0 .85rem;
      font-size: .8rem; line-height: 1.6; font-weight: 600;
      color: rgba(255,255,255,.85); max-width: 34ch;
    }
    .gl-groups-mode__cta {
      display: inline-flex; align-items: center; gap: 8px;
      padding: .55rem 1rem; border-radius: 999px;
      background: linear-gradient(180deg, #FFD24D, #F5B800);
      color: #0B1220; font-size: .78rem; font-weight: 900;
    }
    .gl-groups-compare {
      display: grid; gap: 1rem;
      margin-bottom: clamp(1.5rem, 3vw, 2.25rem);
    }
    @media (min-width: 900px) {
      .gl-groups-compare { grid-template-columns: 1fr 1fr; gap: 1.15rem; }
    }
    .gl-groups-compare__card {
      background: #fff;
      border: 1.5px solid #D7DDE6;
      border-radius: 16px;
      padding: 1.1rem 1.15rem 1.2rem;
      box-shadow: 0 10px 28px -20px rgba(11,61,145,.28);
    }
    .gl-groups-compare__head {
      display: flex; align-items: center; gap: .65rem;
      margin-bottom: .85rem;
    }
    .gl-groups-compare__icon {
      width: 40px; height: 40px; border-radius: 12px; flex-shrink: 0;
      display: grid; place-items: center;
      background: #E8EEF8; color: #0B3D91; font-size: .95rem;
    }
    .gl-groups-compare__icon--gold {
      background: #FFF6D6; color: #9A7200;
    }
    .gl-groups-compare__head h3 {
      margin: 0;
      font-size: 1rem; font-weight: 900; color: #0B1220;
    }
    .gl-groups-compare__list {
      list-style: none; margin: 0; padding: 0;
      display: grid; gap: .5rem;
    }
    .gl-groups-compare__list li {
      display: flex; gap: 10px; align-items: flex-start;
      font-size: .8rem; line-height: 1.55; font-weight: 600; color: #5B6577;
    }
    .gl-groups-compare__list li i {
      color: #0B3D91; margin-top: 3px; flex-shrink: 0; font-size: .78rem;
    }
    .gl-groups-compare__card--solo .gl-groups-compare__list li i { color: #C99200; }
    .gl-groups-steps {
      display: grid; gap: .85rem;
      margin-bottom: clamp(1.75rem, 3.5vw, 2.5rem);
    }
    @media (min-width: 768px) {
      .gl-groups-steps { grid-template-columns: repeat(3, 1fr); }
    }
    .gl-groups-step {
      background: #fff;
      border: 1.5px solid #D7DDE6;
      border-radius: 14px;
      padding: 1rem 1.05rem;
      box-shadow: 0 8px 22px -18px rgba(11,61,145,.25);
    }
    .gl-groups-step__num {
      display: inline-flex; align-items: center; justify-content: center;
      min-width: 28px; height: 28px; padding: 0 8px; border-radius: 999px;
      background: #0B3D91; color: #fff;
      font-size: .72rem; font-weight: 900; margin-bottom: .55rem;
    }
    .gl-groups-step h3 {
      margin: 0 0 .35rem;
      font-size: .9rem; font-weight: 900; color: #0B1220;
    }
    .gl-groups-step p {
      margin: 0;
      font-size: .76rem; line-height: 1.6; font-weight: 600; color: #5B6577;
    }
    .gl-groups-sec { margin-bottom: clamp(1.75rem, 3.5vw, 2.5rem); }
    .gl-groups-sec__head {
      display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between;
      gap: .75rem; margin-bottom: 1rem;
    }
    .gl-groups-sec__head h2 {
      margin: 0;
      font-family: Cairo,Tajawal,sans-serif;
      font-size: clamp(1.1rem, 2.2vw, 1.35rem);
      font-weight: 900; color: #0B1220;
    }
    .gl-groups-sec__head p {
      margin: .2rem 0 0;
      font-size: .8rem; color: #5B6577; font-weight: 600;
    }
    .gl-groups-grid {
      display: grid; gap: .85rem;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    }
    .gl-groups-card {
      background: #fff;
      border: 1.5px solid #D7DDE6;
      border-radius: 14px;
      overflow: hidden;
      text-decoration: none !important;
      color: inherit;
      transition: transform .2s ease, border-color .2s ease;
      box-shadow: 0 8px 22px -18px rgba(11,61,145,.25);
    }
    .gl-groups-card:hover {
      transform: translateY(-2px);
      border-color: rgba(11,61,145,.28);
    }
    .gl-groups-card__media {
      position: relative;
      aspect-ratio: 16/10;
      background: #E8EEF8;
      overflow: hidden;
    }
    .gl-groups-card__media img {
      width: 100%; height: 100%; object-fit: cover; display: block;
    }
    .gl-groups-card__badge {
      position: absolute; top: 10px; inset-inline-start: 10px;
      padding: 4px 9px; border-radius: 999px;
      background: rgba(11,61,145,.92); color: #fff;
      font-size: .66rem; font-weight: 800;
    }
    .gl-groups-card__badge--gold {
      background: linear-gradient(180deg, #FFD24D, #F5B800);
      color: #0B1220;
    }
    .gl-groups-card__body { padding: .7rem .8rem .85rem; }
    .gl-groups-card__body h3 {
      margin: 0 0 .25rem;
      font-size: .84rem; font-weight: 900; color: #0B1220; line-height: 1.35;
    }
    .gl-groups-card__body p {
      margin: 0;
      font-size: .72rem; color: #5B6577; font-weight: 600;
    }
    .gl-groups-empty {
      text-align: center;
      padding: 1.5rem 1rem;
      border-radius: 14px;
      background: #fff;
      border: 1.5px dashed #D7DDE6;
      color: #5B6577;
      font-size: .86rem;
      font-weight: 700;
    }
    .gl-groups-band {
      border-radius: 18px;
      padding: clamp(1.25rem, 3vw, 1.75rem);
      background:
        radial-gradient(circle at 90% 0%, rgba(245,184,0,.18), transparent 42%),
        linear-gradient(145deg, #051F4D 0%, #0B3D91 55%, #1A56B0 100%);
      color: #fff;
      box-shadow: 0 18px 44px -18px rgba(11,61,145,.45);
    }
    .gl-groups-band__inner {
      display: flex; flex-wrap: wrap; gap: 1rem 1.35rem;
      align-items: center; justify-content: space-between;
    }
    .gl-groups-band h2 {
      margin: 0 0 .3rem;
      font-family: Cairo,Tajawal,sans-serif;
      font-size: clamp(1.1rem, 2.2vw, 1.35rem);
      font-weight: 900;
    }
    .gl-groups-band p {
      margin: 0;
      font-size: .82rem; line-height: 1.6; font-weight: 600;
      color: rgba(255,255,255,.82); max-width: 34rem;
    }
    .gl-groups-band__actions {
      display: flex; flex-wrap: wrap; gap: .55rem;
    }
    .gl-groups-band__actions .sana-btn {
      padding: .65rem 1.05rem; font-size: .82rem;
    }
    .gl-groups-sec-intro {
      margin-bottom: 1rem;
    }
    .gl-groups-sec-intro .sana-head__eyebrow { margin-bottom: 4px; }
    .gl-groups-sec-intro .sana-head__title {
      font-size: clamp(1.15rem, 2.4vw, 1.45rem);
      margin: 0 0 .35rem;
    }
    .gl-groups-sec-intro .sana-head__sub {
      margin: 0; font-size: .82rem; max-width: 40rem;
    }
  </style>
</head>
<body class="sana-home sana-courses-page gl-groups-page">
<div id="sana-scroll-progress"></div>
<?php echo $__env->make('partials.landing.navbar', ['navActive' => 'groups', 'navSolid' => true, 'navHero' => false], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="sana-cat-page">
  <section class="sana-cat-hero">
    <div class="sana-container sana-cat-hero__inner sana-reveal">
      <div class="sana-cat-hero__breadcrumb">
        <a href="<?php echo e(route('home')); ?>"><?php echo e($isRtl ? 'الرئيسية' : 'Home'); ?></a>
        <span>/</span>
        <span><?php echo e(__($g.'.title')); ?></span>
      </div>
      <h1 class="sana-cat-hero__title">
        <?php echo e(__($g.'.title')); ?>

        <span class="hl"><?php echo e($isRtl ? 'جماعي أو فردي' : 'group or 1:1'); ?></span>
      </h1>
      <p class="sana-cat-hero__desc"><?php echo e(__($g.'.intro')); ?></p>
      <p class="sana-cat-hero__stats">
        <span class="sana-cat-hero__stat"><i class="fas fa-users"></i> <?php echo e($groupCountLabel); ?> <?php echo e($isRtl ? 'جماعي' : 'group'); ?></span>
        <span class="sana-cat-hero__stat"><i class="fas fa-user"></i> <?php echo e($soloCountLabel); ?> <?php echo e($isRtl ? 'فردي' : '1:1'); ?></span>
        <span class="sana-cat-hero__stat"><i class="fas fa-video"></i> <?php echo e($isRtl ? 'حصص مباشرة' : 'Live sessions'); ?></span>
      </p>
    </div>
  </section>

  <div class="sana-container gl-groups-body">

    <div class="gl-groups-modes">
      <a href="<?php echo e(route('public.groups.courses')); ?>" class="gl-groups-mode sana-reveal">
        <div class="gl-groups-mode__media">
          <img src="<?php echo e($groupImg); ?>" alt="<?php echo e(__($g.'.group_tile_title')); ?>" width="900" height="560" loading="eager">
          <span class="gl-groups-mode__shade" aria-hidden="true"></span>
        </div>
        <div class="gl-groups-mode__body">
          <?php if($groupCount > 0): ?>
            <span class="gl-groups-mode__count"><i class="fas fa-book-open"></i> <?php echo e($groupCountLabel); ?></span>
          <?php endif; ?>
          <h2><?php echo e(__($g.'.group_tile_title')); ?></h2>
          <p><?php echo e(__($g.'.group_tile_sub')); ?></p>
          <span class="gl-groups-mode__cta"><?php echo e(__($g.'.group_tile_cta')); ?> <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?>"></i></span>
        </div>
      </a>

      <a href="<?php echo e(route('public.groups.one-to-one')); ?>" class="gl-groups-mode sana-reveal">
        <div class="gl-groups-mode__media">
          <img src="<?php echo e($soloImg); ?>" alt="<?php echo e(__($g.'.solo_tile_title')); ?>" width="900" height="560" loading="eager">
          <span class="gl-groups-mode__shade" aria-hidden="true"></span>
        </div>
        <div class="gl-groups-mode__body">
          <?php if($oneToOneCount > 0): ?>
            <span class="gl-groups-mode__count"><i class="fas fa-book-open"></i> <?php echo e($soloCountLabel); ?></span>
          <?php endif; ?>
          <h2><?php echo e(__($g.'.solo_tile_title')); ?></h2>
          <p><?php echo e(__($g.'.solo_tile_sub')); ?></p>
          <span class="gl-groups-mode__cta"><?php echo e(__($g.'.solo_tile_cta')); ?> <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?>"></i></span>
        </div>
      </a>
    </div>

    <div class="gl-groups-sec-intro sana-reveal">
      <span class="sana-head__eyebrow"><?php echo e(__($g.'.compare_kicker')); ?></span>
      <h2 class="sana-head__title"><?php echo e(__($g.'.compare_title')); ?></h2>
      <p class="sana-head__sub"><?php echo e(__($g.'.compare_sub')); ?></p>
    </div>
    <div class="gl-groups-compare">
      <article class="gl-groups-compare__card sana-reveal">
        <div class="gl-groups-compare__head">
          <span class="gl-groups-compare__icon"><i class="fas fa-users"></i></span>
          <h3><?php echo e(__($g.'.group_label')); ?></h3>
        </div>
        <ul class="gl-groups-compare__list">
          <?php $__currentLoopData = __($g.'.group_points'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><i class="fas fa-check-circle"></i><span><?php echo e($point); ?></span></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </article>
      <article class="gl-groups-compare__card gl-groups-compare__card--solo sana-reveal">
        <div class="gl-groups-compare__head">
          <span class="gl-groups-compare__icon gl-groups-compare__icon--gold"><i class="fas fa-user"></i></span>
          <h3><?php echo e(__($g.'.solo_label')); ?></h3>
        </div>
        <ul class="gl-groups-compare__list">
          <?php $__currentLoopData = __($g.'.solo_points'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><i class="fas fa-check-circle"></i><span><?php echo e($point); ?></span></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </article>
    </div>

    <div class="gl-groups-sec-intro sana-reveal">
      <span class="sana-head__eyebrow"><?php echo e(__($g.'.join_kicker')); ?></span>
      <h2 class="sana-head__title"><?php echo e(__($g.'.join_title')); ?></h2>
      <p class="sana-head__sub"><?php echo e(__($g.'.join_sub')); ?></p>
    </div>
    <div class="gl-groups-steps">
      <div class="gl-groups-step sana-reveal">
        <span class="gl-groups-step__num">01</span>
        <h3><?php echo e(__($g.'.step1_title')); ?></h3>
        <p><?php echo e(__($g.'.step1_desc')); ?></p>
      </div>
      <div class="gl-groups-step sana-reveal">
        <span class="gl-groups-step__num">02</span>
        <h3><?php echo e(__($g.'.step2_title')); ?></h3>
        <p><?php echo e(__($g.'.step2_desc')); ?></p>
      </div>
      <div class="gl-groups-step sana-reveal">
        <span class="gl-groups-step__num">03</span>
        <h3><?php echo e(__($g.'.step3_title')); ?></h3>
        <p><?php echo e(__($g.'.step3_desc')); ?></p>
      </div>
    </div>

    <section class="gl-groups-sec">
      <div class="gl-groups-sec__head sana-reveal">
        <div>
          <p class="sana-head__eyebrow" style="margin:0 0 4px"><?php echo e(__($g.'.from_groups_kicker')); ?></p>
          <h2><?php echo e(__($g.'.from_groups_title')); ?></h2>
          <p><?php echo e(__($g.'.from_groups_sub')); ?></p>
        </div>
        <a href="<?php echo e(route('public.groups.courses')); ?>" class="sana-link-more">
          <?php echo e(__($g.'.view_all_group')); ?> <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?>"></i>
        </a>
      </div>
      <?php if($groupCourses->isNotEmpty()): ?>
        <div class="gl-groups-grid sana-reveal">
          <?php $__currentLoopData = $groupCourses->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $thumb = $item->imageUrl() ?: $groupImg;
            ?>
            <a href="<?php echo e(route('public.groups.show', $item->slug)); ?>" class="gl-groups-card">
              <div class="gl-groups-card__media">
                <img src="<?php echo e($thumb); ?>" alt="<?php echo e($item->title); ?>" loading="lazy">
                <span class="gl-groups-card__badge"><?php echo e($isRtl ? 'جماعي' : 'Group'); ?></span>
              </div>
              <div class="gl-groups-card__body">
                <h3><?php echo e($item->title); ?></h3>
                <p><?php echo e($item->instructor->name ?? ($isRtl ? 'معلّم على المنصة' : 'Platform tutor')); ?></p>
              </div>
            </a>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      <?php else: ?>
        <div class="gl-groups-empty sana-reveal"><?php echo e(__($g.'.empty_group')); ?></div>
      <?php endif; ?>
    </section>

    <section class="gl-groups-sec">
      <div class="gl-groups-sec__head sana-reveal">
        <div>
          <p class="sana-head__eyebrow" style="margin:0 0 4px"><?php echo e(__($g.'.from_solo_kicker')); ?></p>
          <h2><?php echo e(__($g.'.from_solo_title')); ?></h2>
          <p><?php echo e(__($g.'.from_solo_sub')); ?></p>
        </div>
        <a href="<?php echo e(route('public.groups.one-to-one')); ?>" class="sana-link-more">
          <?php echo e(__($g.'.view_all_solo')); ?> <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?>"></i>
        </a>
      </div>
      <?php if($oneToOneCourses->isNotEmpty()): ?>
        <div class="gl-groups-grid sana-reveal">
          <?php $__currentLoopData = $oneToOneCourses->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $thumb = $item->imageUrl() ?: $soloImg;
            ?>
            <a href="<?php echo e(route('public.groups.show', $item->slug)); ?>" class="gl-groups-card">
              <div class="gl-groups-card__media">
                <img src="<?php echo e($thumb); ?>" alt="<?php echo e($item->title); ?>" loading="lazy">
                <span class="gl-groups-card__badge gl-groups-card__badge--gold">1:1</span>
              </div>
              <div class="gl-groups-card__body">
                <h3><?php echo e($item->title); ?></h3>
                <p><?php echo e($item->instructor->name ?? ($isRtl ? 'معلّم على المنصة' : 'Platform tutor')); ?></p>
              </div>
            </a>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      <?php else: ?>
        <div class="gl-groups-empty sana-reveal"><?php echo e(__($g.'.empty_solo')); ?></div>
      <?php endif; ?>
    </section>

    <div class="gl-groups-band sana-reveal">
      <div class="gl-groups-band__inner">
        <div>
          <h2><?php echo e(__($g.'.cta_title')); ?></h2>
          <p><?php echo e(__($g.'.cta_sub')); ?></p>
        </div>
        <div class="gl-groups-band__actions">
          <a href="<?php echo e(route('home')); ?>?open_trial=1" class="sana-btn sana-btn--yellow"><i class="fas fa-clipboard-check"></i> <?php echo e(__($g.'.cta_trial')); ?></a>
          <a href="<?php echo e(route('public.courses')); ?>" class="sana-btn sana-btn--wa"><i class="fas fa-book-open"></i> <?php echo e(__($g.'.cta_courses')); ?></a>
          <a href="<?php echo e($waUrl); ?>" class="sana-btn sana-btn--ghost-light" target="_blank" rel="noopener" style="background:rgba(255,255,255,.1);color:#fff;border:1px solid rgba(255,255,255,.28)"><i class="fab fa-whatsapp"></i> <?php echo e($isRtl ? 'واتساب' : 'WhatsApp'); ?></a>
        </div>
      </div>
    </div>
  </div>
</main>

<?php echo $__env->make('partials.landing.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\public\groups.blade.php ENDPATH**/ ?>