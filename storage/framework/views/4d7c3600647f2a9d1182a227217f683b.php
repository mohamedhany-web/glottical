<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $brand = config('app.name', 'Glottical');
    $footer = \App\Services\PublicFooterSettings::payload();
    $waUrl = $footer['whatsapp_url'] ?? '#';
    $thumbUrl = $course->thumbnail_url;
    $introVideoUrl = trim((string) ($course->video_url ?? ''));
    $introEmbedUrl = \App\Helpers\VideoHelper::getEmbedUrl($introVideoUrl);
    $introDirectVideo = \App\Helpers\VideoHelper::getDirectVideoUrl($introVideoUrl);
    $categoryDisplay = $course->courseCategory?->name ?? __('public.course_category_not_set');
    $isMonthly = $course->isMonthlyBilling();
    $checkoutPrice = $course->effectiveCheckoutPrice();
    $isPaid = $checkoutPrice > 0 && ! ($course->is_free ?? false);
    $hasPromo = $isPaid && $course->hasPromotionalPrice();
    $listPrice = $hasPromo ? $course->listPriceAmount() : 0;
    $savedAmount = $hasPromo ? max(0, $listPrice - $checkoutPrice) : 0;
    $discountPct = ($hasPromo && $listPrice > 0)
        ? (int) round((1 - ($checkoutPrice / $listPrice)) * 100)
        : 0;
    $instructorApproved = $course->instructor
        && \App\Models\InstructorProfile::where('user_id', $course->instructor->id)->where('status', 'approved')->exists();
    $subjectName = $course->academicSubject->name ?? __('public.course_category_not_set');
    $learnPoints = $course->what_you_learn
        ? array_values(array_filter(array_map('trim', explode("\n", $course->what_you_learn))))
        : [];
    $courseOgImg = $thumbUrl ?? asset('images/og-image.jpg');
    $courseDesc = \Illuminate\Support\Str::limit(strip_tags($course->description ?? ''), 160);
    $courseTitle = ($course->title ?? __('public.course_detail_title')).' | '.$brand;
    $courseUrl = url('/course/'.($course->id ?? ''));
    $isOneToOne = $course->isOneToOne();
    $from = $from ?? ($isOneToOne ? 'one_to_one' : 'groups');
    $groupsListUrl = $isOneToOne
        ? route('public.groups.one-to-one')
        : route('public.groups.courses');
    $groupsListLabel = $isOneToOne
        ? __('landing.groups_page.catalog_solo_title')
        : __('landing.groups_page.catalog_group_title');
    $deliveryLabel = $isOneToOne
        ? ($isRtl ? 'فردي 1:1' : '1:1 private')
        : ($isRtl ? 'جماعي' : 'Group');
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title><?php echo e($courseTitle); ?></title>
  <meta name="description" content="<?php echo e($courseDesc); ?>">
  <meta name="theme-color" content="#0B3D91">
  <link rel="canonical" href="<?php echo e($courseUrl); ?>">
  <meta property="og:type" content="article">
  <meta property="og:url" content="<?php echo e($courseUrl); ?>">
  <meta property="og:title" content="<?php echo e($courseTitle); ?>">
  <meta property="og:description" content="<?php echo e($courseDesc); ?>">
  <meta property="og:image" content="<?php echo e($courseOgImg); ?>">
  <meta property="og:site_name" content="<?php echo e($brand); ?>">
  <?php echo $__env->make('partials.seo-jsonld', ['jsonldType' => 'course', 'course' => $course], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('partials.landing.head', ['landingCss' => ['theme', 'courses-catalog']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <style>
    .gl-cs { background: var(--bg,#F4F7FC); }
    .gl-cs-wrap.sana-container {
      max-width: 1180px;
      padding-top: 14px;
      padding-bottom: 40px;
    }
    .gl-cs-crumb {
      display: flex; flex-wrap: wrap; align-items: center; gap: 6px;
      font-size: .72rem; font-weight: 700; color: #5B6577; margin-bottom: .65rem;
    }
    .gl-cs-crumb a { color: #0B3D91; text-decoration: none !important; }
    .gl-cs-crumb a:hover { text-decoration: underline !important; }
    .gl-cs-layout {
      display: grid; gap: .85rem;
    }
    @media (min-width: 992px) {
      .gl-cs-layout {
        grid-template-columns: minmax(0, 1.35fr) minmax(300px, 360px);
        gap: 1rem;
        align-items: start;
      }
    }
    .gl-cs-media {
      position: relative; overflow: hidden; border-radius: 14px;
      background: #0B1220; aspect-ratio: 16/9;
      box-shadow: 0 10px 24px -16px rgba(11,61,145,.32);
    }
    .gl-cs-media iframe,
    .gl-cs-media video,
    .gl-cs-media img {
      position: absolute; inset: 0; width: 100%; height: 100%;
      border: 0; object-fit: cover; display: block;
    }
    .gl-cs-media video { object-fit: contain; background: #0B1220; }
    .gl-cs-media__empty {
      position: absolute; inset: 0; display: grid; place-items: center;
      background: linear-gradient(145deg,#051F4D,#0B3D91); color: rgba(255,255,255,.35); font-size: 2.2rem;
    }
    .gl-cs-badge {
      position: absolute; top: 10px; inset-inline-start: 10px; z-index: 2;
      display: inline-flex; align-items: center; gap: 5px;
      padding: 4px 9px; border-radius: 999px;
      background: rgba(255,255,255,.95); color: #0B3D91;
      font-size: .68rem; font-weight: 800;
    }
    .gl-cs-panel {
      background: #fff; border: 1.5px solid #D7DDE6; border-radius: 14px;
      padding: .8rem .85rem .9rem;
      box-shadow: 0 8px 22px -16px rgba(11,61,145,.28);
    }
    @media (min-width: 992px) {
      .gl-cs-panel { position: sticky; top: 84px; }
    }
    .gl-cs-instructor {
      display: inline-flex; align-items: center; gap: 6px;
      color: #0B3D91; font-size: .74rem; font-weight: 800;
      text-decoration: none !important; margin-bottom: .3rem;
    }
    .gl-cs-title {
      margin: 0 0 .4rem; font-family: Cairo,Tajawal,sans-serif;
      font-size: clamp(1.05rem, 1.8vw, 1.22rem); font-weight: 900;
      color: #0B1220; line-height: 1.3;
    }
    .gl-cs-tags { display: flex; flex-wrap: wrap; gap: .3rem; margin-bottom: .4rem; }
    .gl-cs-tag {
      display: inline-flex; align-items: center; padding: 3px 8px;
      border-radius: 999px; font-size: .66rem; font-weight: 800;
      background: #E8EEF8; color: #0B3D91;
    }
    .gl-cs-tag--gold { background: #FFF6D6; color: #9A7200; }
    .gl-cs-tag--green { background: #D1FAE5; color: #047857; }
    .gl-cs-tag--red { background: #FEE2E2; color: #B91C1C; }
    .gl-cs-lead {
      margin: 0 0 .55rem; font-size: .76rem; line-height: 1.55; color: #5B6577; font-weight: 600;
      display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
    }
    .gl-cs-price-box {
      background: #F4F7FC; border: 1.5px solid #C5D4F0; border-radius: 10px;
      padding: .6rem .7rem; margin-bottom: .55rem;
    }
    .gl-cs-price {
      margin: 0; font-size: 1.2rem; font-weight: 900; color: #0B3D91; line-height: 1.2;
    }
    .gl-cs-price small { font-size: .8rem; font-weight: 700; color: #5B6577; }
    .gl-cs-price-old {
      margin: .25rem 0 0; font-size: .82rem; color: #94A3B8;
      text-decoration: line-through; font-weight: 700;
    }
    .gl-cs-save {
      display: inline-flex; margin-top: .35rem; padding: 3px 8px; border-radius: 999px;
      background: #D1FAE5; color: #047857; font-size: .66rem; font-weight: 800;
    }
    .gl-cs-note { margin: .4rem 0 0; font-size: .72rem; color: #5B6577; font-weight: 600; }
    .gl-cs-access {
      display: flex; align-items: center; gap: 6px; margin: .4rem 0 0;
      font-size: .7rem; font-weight: 800; color: #047857;
    }
    .gl-cs-access i { width: 7px; height: 7px; border-radius: 50%; background: #10B981; display: block; }
    .gl-cs-specs {
      display: grid; grid-template-columns: 1fr 1fr; gap: .4rem; margin-bottom: .55rem;
    }
    .gl-cs-spec {
      background: #F4F7FC; border: 1px solid #E2E8F0; border-radius: 8px; padding: .45rem .55rem;
    }
    .gl-cs-spec dt { margin: 0; font-size: .6rem; font-weight: 700; color: #5B6577; }
    .gl-cs-spec dd { margin: .1rem 0 0; font-size: .72rem; font-weight: 800; color: #0B1220; }
    .gl-cs-actions { display: flex; flex-wrap: wrap; gap: .4rem; margin-bottom: .5rem; }
    .gl-cs-actions .sana-btn {
      flex: 1; min-width: 120px; justify-content: center; text-align: center;
      border: 0; cursor: pointer; font-family: inherit;
      padding: .55rem .85rem; font-size: .78rem; min-height: 0;
    }
    .gl-cs-actions form { flex: 1; min-width: 120px; display: flex; }
    .gl-cs-actions form .sana-btn { width: 100%; }
    .gl-cs-back {
      display: inline-flex; align-items: center; gap: 6px;
      color: #0B3D91; font-size: .76rem; font-weight: 800; text-decoration: none !important;
    }
    .gl-cs-back:hover { color: #072A66; }
    .gl-cs-trust {
      display: grid; grid-template-columns: repeat(3, 1fr); gap: .35rem; margin-top: .55rem;
    }
    .gl-cs-trust__item {
      display: flex; flex-direction: column; align-items: center; text-align: center; gap: 4px;
      padding: .45rem .3rem; border-radius: 8px; background: #F4F7FC; border: 1px solid #E8EEF8;
    }
    .gl-cs-trust__icon {
      width: 24px; height: 24px; border-radius: 7px;
      display: grid; place-items: center; background: #E8EEF8; color: #0B3D91; font-size: .7rem;
    }
    .gl-cs-trust__item strong { display: block; font-size: .62rem; font-weight: 900; color: #0B1220; line-height: 1.3; }
    .gl-cs-trust__item span { display: none; }
    .gl-cs-sections {
      display: grid; gap: .75rem; margin-top: 1rem;
    }
    @media (min-width: 992px) {
      .gl-cs-sections { grid-template-columns: 1.2fr .8fr; gap: .85rem; }
    }
    .gl-cs-card {
      background: #fff; border: 1.5px solid #D7DDE6; border-radius: 14px;
      padding: .95rem 1rem 1.05rem;
      box-shadow: 0 8px 22px -18px rgba(11,61,145,.28);
    }
    .gl-cs-card h2 {
      margin: 0 0 .65rem; font-family: Cairo,Tajawal,sans-serif;
      font-size: 1rem; font-weight: 900; color: #0B1220;
    }
    .gl-cs-card h3 {
      margin: .9rem 0 .4rem; font-size: .84rem; font-weight: 900; color: #0B1220;
    }
    .gl-cs-card p {
      margin: 0; font-size: .8rem; line-height: 1.7; color: #5B6577; font-weight: 600; white-space: pre-line;
    }
    .gl-cs-card .box {
      margin-top: .3rem; background: #F4F7FC; border-radius: 10px; padding: .7rem .8rem;
      font-size: .78rem; line-height: 1.7; color: #5B6577; font-weight: 600; white-space: pre-line;
    }
    .gl-cs-learn { list-style: none; margin: 0; padding: 0; display: grid; gap: .4rem; }
    .gl-cs-learn li {
      display: flex; gap: 8px; align-items: flex-start;
      font-size: .78rem; color: #5B6577; font-weight: 600; line-height: 1.5;
    }
    .gl-cs-learn li i { color: #0B3D91; margin-top: 2px; flex-shrink: 0; font-size: .8rem; }
    .gl-cs-table { width: 100%; border-collapse: collapse; font-size: .78rem; }
    .gl-cs-table th, .gl-cs-table td { padding: .55rem 0; border-bottom: 1px solid #E8EEF8; text-align: start; vertical-align: top; }
    .gl-cs-table tr:last-child th, .gl-cs-table tr:last-child td { border-bottom: 0; }
    .gl-cs-table th { width: 42%; color: #5B6577; font-weight: 700; }
    .gl-cs-table td { color: #0B1220; font-weight: 800; }
    .gl-cs-table a { color: #0B3D91; text-decoration: none !important; }
    .gl-cs-related { margin-top: 1.5rem; }
    .gl-cs-related__head {
      display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between;
      gap: .55rem; margin-bottom: .75rem;
    }
    .gl-cs-related__head h2 {
      margin: 0; font-family: Cairo,Tajawal,sans-serif; font-size: 1.05rem; font-weight: 900; color: #0B1220;
    }
    .gl-cs-related__head p { margin: .15rem 0 0; font-size: .74rem; color: #5B6577; font-weight: 600; }
    .gl-cs-related__grid {
      display: grid; gap: .7rem; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    }
    .gl-cs-rel {
      background: #fff; border: 1.5px solid #D7DDE6; border-radius: 14px; overflow: hidden;
      text-decoration: none !important; color: inherit;
      transition: transform .2s ease, border-color .2s ease;
    }
    .gl-cs-rel:hover { transform: translateY(-2px); border-color: rgba(11,61,145,.3); }
    .gl-cs-rel__media { aspect-ratio: 16/10; background: #E8EEF8; overflow: hidden; }
    .gl-cs-rel__media img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .gl-cs-rel__body { padding: .65rem .75rem .8rem; }
    .gl-cs-rel__body h3 { margin: 0 0 .2rem; font-size: .82rem; font-weight: 900; color: #0B1220; line-height: 1.35; }
    .gl-cs-rel__body p { margin: 0; font-size: .7rem; color: #5B6577; font-weight: 600; }
    .gl-cs-cta {
      margin-top: 1.5rem;
      border-radius: 16px; padding: 1.1rem 1.25rem;
      background:
        radial-gradient(circle at 90% 0%, rgba(245,184,0,.2), transparent 42%),
        linear-gradient(145deg, #051F4D 0%, #0B3D91 55%, #1A56B0 100%);
      color: #fff; box-shadow: 0 14px 36px -18px rgba(11,61,145,.4);
    }
    .gl-cs-cta__inner {
      display: flex; flex-wrap: wrap; gap: .85rem 1.15rem; align-items: center; justify-content: space-between;
    }
    .gl-cs-cta h2 { margin: 0 0 .25rem; font-family: Cairo,Tajawal,sans-serif; font-size: 1.1rem; font-weight: 900; }
    .gl-cs-cta p { margin: 0; color: rgba(255,255,255,.82); font-size: .78rem; line-height: 1.6; font-weight: 600; max-width: 30rem; }
    .gl-cs-cta__actions { display: flex; flex-wrap: wrap; gap: .5rem; }
    .gl-cs-cta__actions .sana-btn { padding: .65rem 1rem; font-size: .82rem; }
    .gl-cs-flash {
      display: flex; align-items: flex-start; gap: .6rem;
      margin-bottom: .75rem; padding: .7rem .85rem; border-radius: 12px;
      background: #fff; border: 1.5px solid #D7DDE6; font-size: .8rem; font-weight: 700; color: #0B1220;
    }
    .gl-cs-flash--ok { border-color: #A7F3D0; background: #ECFDF5; }
    .gl-cs-flash--err { border-color: #FECACA; background: #FEF2F2; }
    .gl-cs-flash--info { border-color: #C5D4F0; background: #E8EEF8; }
    .gl-cs-delivery {
      margin: 0 0 .85rem;
      padding: .7rem .8rem;
      border-radius: 12px;
      background: #EEF3FB;
      border: 1.5px solid #C5D4F0;
    }
    .gl-cs-delivery--solo {
      background: #FFF8E6;
      border-color: #F5D76E;
    }
    .gl-cs-delivery__title {
      display: flex; align-items: center; gap: 8px;
      margin: 0 0 .35rem;
      font-size: .78rem; font-weight: 900; color: #0B3D91;
    }
    .gl-cs-delivery--solo .gl-cs-delivery__title { color: #9A7200; }
    .gl-cs-delivery p {
      margin: 0;
      font-size: .74rem; line-height: 1.55; font-weight: 600; color: #5B6577;
    }
    .gl-cs-flash button {
      margin-inline-start: auto; border: 0; background: transparent; cursor: pointer;
      font-size: 1rem; color: #5B6577; line-height: 1;
    }
  </style>
</head>
<body class="sana-home sana-courses-page gl-cs">
<div id="sana-scroll-progress"></div>
<?php echo $__env->make('partials.landing.navbar', ['navActive' => 'courses', 'navSolid' => true, 'navHero' => false], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="sana-cat-page">
  <div class="sana-container gl-cs-wrap">

    <?php if(session('success')): ?>
      <div class="gl-cs-flash gl-cs-flash--ok" data-flash>
        <span>✓</span>
        <p style="margin:0;flex:1"><?php echo e(session('success')); ?></p>
        <button type="button" data-flash-close aria-label="<?php echo e($isRtl ? 'إغلاق' : 'Close'); ?>">×</button>
      </div>
    <?php endif; ?>
    <?php if(session('info')): ?>
      <div class="gl-cs-flash gl-cs-flash--info" data-flash>
        <span>i</span>
        <p style="margin:0;flex:1"><?php echo e(session('info')); ?></p>
        <button type="button" data-flash-close aria-label="<?php echo e($isRtl ? 'إغلاق' : 'Close'); ?>">×</button>
      </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
      <div class="gl-cs-flash gl-cs-flash--err" data-flash>
        <span>!</span>
        <p style="margin:0;flex:1"><?php echo e(session('error')); ?></p>
        <button type="button" data-flash-close aria-label="<?php echo e($isRtl ? 'إغلاق' : 'Close'); ?>">×</button>
      </div>
    <?php endif; ?>

    <nav class="gl-cs-crumb" aria-label="<?php echo e($isRtl ? 'مسار التنقل' : 'Breadcrumb'); ?>">
      <a href="<?php echo e(route('home')); ?>"><?php echo e(__('public.home')); ?></a>
      <span>/</span>
      <a href="<?php echo e(route('public.groups')); ?>"><?php echo e(__('landing.nav.groups')); ?></a>
      <span>/</span>
      <a href="<?php echo e($groupsListUrl); ?>"><?php echo e($groupsListLabel); ?></a>
      <span>/</span>
      <span><?php echo e(\Illuminate\Support\Str::limit($course->title ?? '', 42)); ?></span>
    </nav>

    <div class="gl-cs-layout">
      <div>
        <div class="gl-cs-media">
          <?php if($introEmbedUrl): ?>
            <iframe src="<?php echo e($introEmbedUrl); ?>" title="<?php echo e(__('public.course_intro_video')); ?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen; web-share" allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
          <?php elseif($introDirectVideo): ?>
            <video src="<?php echo e($introDirectVideo); ?>" controls playsinline preload="metadata" poster="<?php echo e($thumbUrl); ?>"><?php echo e(__('public.course_intro_video_unsupported')); ?></video>
          <?php elseif($thumbUrl): ?>
            <img src="<?php echo e($thumbUrl); ?>" alt="<?php echo e($course->title); ?>" width="900" height="560">
          <?php else: ?>
            <div class="gl-cs-media__empty">✦</div>
          <?php endif; ?>
          <?php if($course->is_featured ?? false): ?>
            <span class="gl-cs-badge"><?php echo e(__('public.featured_course_badge')); ?></span>
          <?php else: ?>
            <span class="gl-cs-badge"><?php echo e($deliveryLabel); ?></span>
          <?php endif; ?>
        </div>
      </div>

      <aside class="gl-cs-panel">
        <?php if($course->instructor): ?>
          <?php if($instructorApproved): ?>
            <a href="<?php echo e(route('public.instructors.show', $course->instructor)); ?>" class="gl-cs-instructor">
              <i class="fas fa-chalkboard-user"></i> <?php echo e($course->instructor->name); ?>

            </a>
          <?php else: ?>
            <p class="gl-cs-instructor" style="margin:0 0 .55rem"><i class="fas fa-chalkboard-user"></i> <?php echo e($course->instructor->name); ?></p>
          <?php endif; ?>
        <?php endif; ?>

        <h1 class="gl-cs-title"><?php echo e($course->title ?? __('public.course_title_fallback')); ?></h1>

        <div class="gl-cs-tags">
          <span class="gl-cs-tag <?php echo e($isOneToOne ? 'gl-cs-tag--gold' : ''); ?>"><?php echo e($deliveryLabel); ?></span>
          <?php if($discountPct > 0): ?>
            <span class="gl-cs-tag gl-cs-tag--red"><?php echo e($isRtl ? "خصم {$discountPct}%" : "{$discountPct}% off"); ?></span>
          <?php endif; ?>
          <?php if($course->is_featured ?? false): ?>
            <span class="gl-cs-tag gl-cs-tag--gold"><?php echo e(__('public.featured_course_badge')); ?></span>
          <?php endif; ?>
          <?php if(! $isPaid): ?>
            <span class="gl-cs-tag gl-cs-tag--green"><?php echo e(__('public.free_price')); ?></span>
          <?php endif; ?>
        </div>

        <div class="gl-cs-delivery <?php echo e($isOneToOne ? 'gl-cs-delivery--solo' : ''); ?>">
          <p class="gl-cs-delivery__title">
            <i class="fas <?php echo e($isOneToOne ? 'fa-user' : 'fa-users'); ?>"></i>
            <?php echo e($isOneToOne ? __('landing.groups_page.solo_label') : __('landing.groups_page.group_label')); ?>

          </p>
          <p>
            <?php if($isOneToOne): ?>
              <?php echo e($isRtl
                ? 'جلسات فردية مباشرة مع المدرّس المعيَّن — خطة ومواعيد حسب مستواك.'
                : 'Live 1:1 sessions with the assigned tutor — plan and schedule around your level.'); ?>

              <?php if($course->instructor): ?>
                <?php echo e($isRtl ? ' المدرّس:' : ' Tutor:'); ?> <strong><?php echo e($course->instructor->name); ?></strong>.
              <?php endif; ?>
              <?php if($isMonthly): ?>
                <?php echo e($isRtl ? ' الاشتراك شهري.' : ' Monthly billing.'); ?>

              <?php endif; ?>
            <?php else: ?>
              <?php echo e($isRtl
                ? 'تعلّم جماعي منظّم مع زملاء ومدرّس — جلسات مشتركة ومسار موحّد.'
                : 'Organised group learning with peers and a tutor — shared sessions and a clear path.'); ?>

              <?php if($course->instructor): ?>
                <?php echo e($isRtl ? ' المدرّس المسؤول:' : ' Lead tutor:'); ?> <strong><?php echo e($course->instructor->name); ?></strong>.
              <?php endif; ?>
            <?php endif; ?>
          </p>
        </div>

        <?php if($course->description): ?>
          <p class="gl-cs-lead"><?php echo e(\Illuminate\Support\Str::limit(strip_tags($course->description), 160)); ?></p>
        <?php endif; ?>

        <div class="gl-cs-price-box">
          <?php if($isPaid): ?>
            <p class="gl-cs-price">
              <?php echo e(number_format($checkoutPrice, 0)); ?>

              <small><?php echo e(__('public.currency_egp')); ?><?php if($isMonthly): ?> / <?php echo e(__('public.per_month')); ?><?php endif; ?></small>
            </p>
            <?php if($hasPromo): ?>
              <p class="gl-cs-price-old"><?php echo e(number_format($listPrice, 0)); ?> <?php echo e(__('public.currency_egp')); ?></p>
              <?php if($savedAmount > 0): ?>
                <span class="gl-cs-save"><?php echo e($isRtl ? 'وفّرت '.number_format($savedAmount, 0).' '.__('public.currency_egp') : 'Save '.number_format($savedAmount, 0).' '.__('public.currency_egp')); ?></span>
              <?php endif; ?>
            <?php endif; ?>
            <?php if($isMonthly && $course->isOneToOne() && $course->instructor): ?>
              <p class="gl-cs-note"><?php echo e(__('public.one_to_one_with')); ?> <?php echo e($course->instructor->name); ?></p>
            <?php elseif($isMonthly): ?>
              <p class="gl-cs-note"><?php echo e(__('public.checkout_monthly_notice')); ?></p>
            <?php endif; ?>
          <?php else: ?>
            <p class="gl-cs-price" style="color:#047857"><?php echo e(__('public.free_price')); ?></p>
          <?php endif; ?>
          <p class="gl-cs-access"><i></i> <?php echo e($isRtl ? 'وصول فوري بعد التفعيل' : 'Instant access after activation'); ?></p>
        </div>

        <dl class="gl-cs-specs">
          <div class="gl-cs-spec">
            <dt><?php echo e($isRtl ? 'نوع التعلّم' : 'Delivery'); ?></dt>
            <dd><?php echo e($deliveryLabel); ?></dd>
          </div>
          <div class="gl-cs-spec">
            <dt><?php echo e(__('public.duration')); ?></dt>
            <dd><?php echo e($course->duration_hours ?? 0); ?> <?php echo e(__('public.hours')); ?></dd>
          </div>
          <div class="gl-cs-spec">
            <dt><?php echo e(__('public.lectures_count_label')); ?></dt>
            <dd><?php echo e($course->lessons_count ?? 0); ?></dd>
          </div>
          <div class="gl-cs-spec">
            <dt><?php echo e(__('public.course_category_label')); ?></dt>
            <dd><?php echo e($categoryDisplay); ?></dd>
          </div>
        </dl>

        <div class="gl-cs-actions">
          <?php if(auth()->guard()->check()): ?>
            <?php if($isEnrolled ?? false): ?>
              <a href="<?php echo e(route('my-courses.show', $course)); ?>" class="sana-btn sana-btn--yellow"><?php echo e(__('public.start_learning_now')); ?></a>
            <?php elseif($isPaid): ?>
              <a href="<?php echo e(route('public.course.checkout', $course->id)); ?>" class="sana-btn sana-btn--yellow"><?php echo e(__('public.buy_now')); ?></a>
            <?php else: ?>
              <form action="<?php echo e(route('public.course.enroll.free', $course->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="sana-btn sana-btn--yellow"><?php echo e(__('public.register_free')); ?></button>
              </form>
            <?php endif; ?>
          <?php endif; ?>
          <?php if(auth()->guard()->guest()): ?>
            <?php if($isPaid): ?>
              <a href="<?php echo e(route('register', ['redirect' => route('public.course.checkout', $course->id)])); ?>" class="sana-btn sana-btn--yellow"><?php echo e(__('public.buy_now')); ?></a>
            <?php else: ?>
              <a href="<?php echo e(route('register', ['redirect' => route('public.course.show', $course->id)])); ?>" class="sana-btn sana-btn--yellow"><?php echo e(__('public.register_free')); ?></a>
            <?php endif; ?>
          <?php endif; ?>
          <a href="<?php echo e($waUrl); ?>" class="sana-btn sana-btn--wa" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> <?php echo e($isRtl ? 'واتساب' : 'WhatsApp'); ?></a>
        </div>

        <a href="<?php echo e($groupsListUrl); ?>" class="gl-cs-back">
          <i class="fas fa-arrow-<?php echo e($isRtl ? 'right' : 'left'); ?>"></i>
          <?php echo e($groupsListLabel); ?>

        </a>

        <div class="gl-cs-trust">
          <div class="gl-cs-trust__item">
            <span class="gl-cs-trust__icon"><i class="fas fa-shield-halved"></i></span>
            <div>
              <strong><?php echo e(__('public.checkout_trust_secure')); ?></strong>
              <span><?php echo e(__('public.secure_checkout_badge')); ?></span>
            </div>
          </div>
          <div class="gl-cs-trust__item">
            <span class="gl-cs-trust__icon"><i class="fas fa-bolt"></i></span>
            <div>
              <strong><?php echo e(__('public.checkout_trust_fast')); ?></strong>
              <span><?php echo e($isRtl ? 'يُفعَّل الوصول بعد إتمام الطلب' : 'Access unlocks after order completion'); ?></span>
            </div>
          </div>
          <div class="gl-cs-trust__item">
            <span class="gl-cs-trust__icon"><i class="fas fa-certificate"></i></span>
            <div>
              <strong><?php echo e(__('public.checkout_benefit_certificate')); ?></strong>
              <span><?php echo e($isRtl ? 'عند إتمام متطلبات الكورس' : 'Upon completing course requirements'); ?></span>
            </div>
          </div>
        </div>
      </aside>
    </div>

    <div class="gl-cs-sections">
      <article class="gl-cs-card">
        <h2><?php echo e(__('public.about_course')); ?></h2>
        <p><?php echo e($course->description ?? __('public.course_desc_fallback')); ?></p>

        <?php if($course->objectives): ?>
          <h3><?php echo e(__('public.course_objectives')); ?></h3>
          <div class="box"><?php echo e($course->objectives); ?></div>
        <?php endif; ?>

        <?php if(count($learnPoints)): ?>
          <h3><?php echo e(__('public.what_you_learn')); ?></h3>
          <ul class="gl-cs-learn">
            <?php $__currentLoopData = $learnPoints; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li><i class="fas fa-check-circle"></i><span><?php echo e($point); ?></span></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        <?php endif; ?>
      </article>

      <div style="display:grid;gap:1rem">
        <article class="gl-cs-card">
          <h2><?php echo e($isRtl ? 'تفاصيل الكورس' : 'Course details'); ?></h2>
          <table class="gl-cs-table">
            <tbody>
              <tr>
                <th><?php echo e($isRtl ? 'نوع التعلّم' : 'Delivery'); ?></th>
                <td><?php echo e($deliveryLabel); ?></td>
              </tr>
              <tr>
                <th><?php echo e(__('public.course_category_label')); ?></th>
                <td><?php echo e($categoryDisplay); ?></td>
              </tr>
              <tr>
                <th><?php echo e(__('public.subject_label')); ?></th>
                <td><?php echo e($subjectName); ?></td>
              </tr>
              <tr>
                <th><?php echo e(__('public.duration')); ?></th>
                <td><?php echo e($course->duration_hours ?? 0); ?> <?php echo e(__('public.hours')); ?></td>
              </tr>
              <tr>
                <th><?php echo e(__('public.lectures_count_label')); ?></th>
                <td><?php echo e($course->lessons_count ?? 0); ?></td>
              </tr>
              <?php if($course->instructor): ?>
                <tr>
                  <th><?php echo e(__('public.instructor_label')); ?></th>
                  <td>
                    <?php if($instructorApproved): ?>
                      <a href="<?php echo e(route('public.instructors.show', $course->instructor)); ?>"><?php echo e($course->instructor->name); ?></a>
                    <?php else: ?>
                      <?php echo e($course->instructor->name); ?>

                    <?php endif; ?>
                  </td>
                </tr>
              <?php endif; ?>
              <tr>
                <th><?php echo e($isRtl ? 'نوع الاشتراك' : 'Billing'); ?></th>
                <td>
                  <?php if(! $isPaid): ?>
                    <?php echo e(__('public.free_price')); ?>

                  <?php elseif($isMonthly): ?>
                    <?php echo e(__('public.checkout_monthly_price_label')); ?>

                  <?php else: ?>
                    <?php echo e(__('public.checkout_benefit_lifetime')); ?>

                  <?php endif; ?>
                </td>
              </tr>
            </tbody>
          </table>
        </article>

        <?php if($course->requirements): ?>
          <article class="gl-cs-card">
            <h2><?php echo e(__('public.requirements')); ?></h2>
            <div class="box"><?php echo e($course->requirements); ?></div>
          </article>
        <?php endif; ?>
      </div>
    </div>

    <?php if(isset($relatedCourses) && $relatedCourses->isNotEmpty()): ?>
      <section class="gl-cs-related">
        <div class="gl-cs-related__head">
          <div>
            <p><?php echo e($isRtl ? 'قد يعجبك أيضاً' : 'You may also like'); ?></p>
            <h2><?php echo e($isRtl ? 'كورسات ذات صلة' : 'Related courses'); ?></h2>
          </div>
          <a href="<?php echo e(route('public.courses')); ?>" class="sana-link-more"><?php echo e(__('public.all_courses')); ?> <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?>"></i></a>
        </div>
        <div class="gl-cs-related__grid">
          <?php $__currentLoopData = $relatedCourses->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $related): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $rThumb = $related->thumbnail_url ?: 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=600&q=80';
            ?>
            <a href="<?php echo e(route('public.course.show', $related->id)); ?>" class="gl-cs-rel">
              <div class="gl-cs-rel__media"><img src="<?php echo e($rThumb); ?>" alt="<?php echo e($related->title); ?>" loading="lazy"></div>
              <div class="gl-cs-rel__body">
                <h3><?php echo e($related->title); ?></h3>
                <p><?php echo e($related->instructor->name ?? ($isRtl ? 'معلّم على المنصة' : 'Platform tutor')); ?></p>
              </div>
            </a>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </section>
    <?php endif; ?>

    <section class="gl-cs-cta">
      <div class="gl-cs-cta__inner">
        <div>
          <h2><?php echo e($isRtl ? 'جاهز للانطلاق؟' : 'Ready to start?'); ?></h2>
          <p><?php echo e($isRtl ? 'سجّل الآن وابدأ التعلّم بخطوات واضحة — أو احجز تقييم مستوى مجاني إن كنت غير متأكد.' : 'Enroll now and start with clear steps — or book a free level assessment if you’re unsure.'); ?></p>
        </div>
        <div class="gl-cs-cta__actions">
          <?php if(auth()->guard()->check()): ?>
            <?php if($isEnrolled ?? false): ?>
              <a href="<?php echo e(route('my-courses.show', $course)); ?>" class="sana-btn sana-btn--yellow sana-btn--lg"><?php echo e(__('public.start_learning_now')); ?></a>
            <?php elseif($isPaid): ?>
              <a href="<?php echo e(route('public.course.checkout', $course->id)); ?>" class="sana-btn sana-btn--yellow sana-btn--lg"><?php echo e(__('public.buy_now')); ?></a>
            <?php else: ?>
              <form action="<?php echo e(route('public.course.enroll.free', $course->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="sana-btn sana-btn--yellow sana-btn--lg"><?php echo e(__('public.register_free')); ?></button>
              </form>
            <?php endif; ?>
          <?php else: ?>
            <a href="<?php echo e(route('register', ['redirect' => $isPaid ? route('public.course.checkout', $course->id) : route('public.course.show', $course->id)])); ?>" class="sana-btn sana-btn--yellow sana-btn--lg">
              <?php echo e($isPaid ? __('public.buy_now') : __('public.register_free_now')); ?>

            </a>
          <?php endif; ?>
          <a href="<?php echo e(route('home')); ?>?open_trial=1" class="sana-btn sana-btn--wa sana-btn--lg"><i class="fas fa-clipboard-check"></i> <?php echo e(__('landing.academy.free_trial_cta')); ?></a>
        </div>
      </div>
    </section>
  </div>
</main>

<?php echo $__env->make('partials.landing.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<script>
  document.querySelectorAll('[data-flash-close]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var wrap = btn.closest('[data-flash]');
      if (wrap) wrap.remove();
    });
  });
</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\course-show.blade.php ENDPATH**/ ?>