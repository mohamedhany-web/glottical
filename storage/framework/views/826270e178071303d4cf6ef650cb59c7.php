<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $brand = config('app.name', 'Glottical');
    $footer = \App\Services\PublicFooterSettings::payload();
    $waUrl = $footer['whatsapp_url'] ?? '#';
    $imageUrl = $siteService->publicImageUrl();
    $summary = trim((string) ($siteService->summary ?? ''));
    $metaDesc = \Illuminate\Support\Str::limit(strip_tags($summary !== '' ? $summary : ($siteService->body ?? '')), 160);
    $pageTitle = $siteService->name.' — '.__('public.services_page_title').' | '.$brand;
    $pageUrl = route('public.services.show', $siteService);
    $chevron = $isRtl ? 'left' : 'right';
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title><?php echo e($pageTitle); ?></title>
  <meta name="description" content="<?php echo e($metaDesc); ?>">
  <meta name="theme-color" content="#0B3D91">
  <link rel="canonical" href="<?php echo e($pageUrl); ?>">
  <meta property="og:type" content="article">
  <meta property="og:url" content="<?php echo e($pageUrl); ?>">
  <meta property="og:title" content="<?php echo e($pageTitle); ?>">
  <meta property="og:description" content="<?php echo e($metaDesc); ?>">
  <?php if($imageUrl): ?>
    <meta property="og:image" content="<?php echo e($imageUrl); ?>">
  <?php endif; ?>
  <meta property="og:site_name" content="<?php echo e($brand); ?>">
  <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('partials.landing.head', ['landingCss' => ['theme', 'courses-catalog', 'subpages']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <style>
    .gl-svc {
      background:
        radial-gradient(ellipse 80% 50% at 100% 0%, rgba(11,61,145,.08), transparent 55%),
        radial-gradient(ellipse 60% 40% at 0% 30%, rgba(245,184,0,.07), transparent 50%),
        var(--bg, #F4F7FC);
      min-height: 60vh;
      /* clear fixed navbar (72px desktop / 64px mobile) */
      padding-top: 72px;
    }
    @media (max-width: 991px) {
      .gl-svc { padding-top: 64px; }
    }
    .gl-svc-wrap.sana-container {
      max-width: 1100px;
      padding-top: 20px;
      padding-bottom: 48px;
    }
    .gl-svc-crumb {
      display: flex; flex-wrap: wrap; align-items: center; gap: 6px;
      font-size: .72rem; font-weight: 700; color: #5B6577; margin-bottom: 1rem;
    }
    .gl-svc-crumb a { color: #0B3D91; text-decoration: none !important; }
    .gl-svc-crumb a:hover { text-decoration: underline !important; }
    .gl-svc-layout {
      display: grid; gap: 1rem;
    }
    @media (min-width: 992px) {
      .gl-svc-layout {
        grid-template-columns: minmax(0, 1.4fr) minmax(280px, 320px);
        gap: 1.15rem;
        align-items: start;
      }
    }
    .gl-svc-hero {
      position: relative; overflow: hidden; border-radius: 18px;
      background: linear-gradient(145deg, #051F4D, #0B3D91);
      min-height: 200px;
      box-shadow: 0 16px 40px -24px rgba(11,61,145,.45);
    }
    .gl-svc-hero--img { min-height: 0; aspect-ratio: 16 / 9; }
    .gl-svc-hero img {
      position: absolute; inset: 0; width: 100%; height: 100%;
      object-fit: cover; display: block;
    }
    .gl-svc-hero__shade {
      position: absolute; inset: 0;
      background: linear-gradient(180deg, transparent 35%, rgba(5,31,77,.72) 100%);
      pointer-events: none;
    }
    .gl-svc-hero__inner {
      position: relative; z-index: 1;
      padding: clamp(1.4rem, 4vw, 2.2rem);
      color: #fff;
    }
    .gl-svc-hero--img .gl-svc-hero__inner {
      position: absolute; inset: auto 0 0 0;
      padding: 1.1rem 1.25rem 1.25rem;
    }
    .gl-svc-eyebrow {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 5px 11px; border-radius: 999px;
      background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.2);
      font-size: .68rem; font-weight: 800; margin-bottom: .65rem;
    }
    .gl-svc-title {
      margin: 0; font-family: Cairo, Tajawal, sans-serif;
      font-size: clamp(1.35rem, 3.2vw, 1.95rem); font-weight: 900;
      line-height: 1.25; color: #fff;
    }
    .gl-svc-brand {
      display: block; margin-top: .35rem;
      font-family: Cairo, Tajawal, sans-serif;
      font-size: clamp(1rem, 2.2vw, 1.25rem); font-weight: 900;
      color: #F5B800; line-height: 1.2;
    }
    .gl-svc-lead {
      margin: .75rem 0 0; font-size: .88rem; line-height: 1.7;
      color: rgba(255,255,255,.88); font-weight: 600; max-width: 42rem;
    }
    .gl-svc-card {
      background: #fff; border: 1.5px solid #D7DDE6; border-radius: 16px;
      padding: 1.15rem 1.2rem 1.25rem;
      box-shadow: 0 10px 28px -20px rgba(11,61,145,.28);
    }
    .gl-svc-card h2 {
      margin: 0 0 .75rem; font-family: Cairo, Tajawal, sans-serif;
      font-size: 1.05rem; font-weight: 900; color: #0B1220;
    }
    .gl-svc-prose {
      font-size: .9rem; line-height: 1.85; color: #3D4656; font-weight: 600;
    }
    .gl-svc-prose p { margin: 0 0 1rem; }
    .gl-svc-prose p:last-child { margin-bottom: 0; }
    .gl-svc-side {
      background: #fff; border: 1.5px solid #D7DDE6; border-radius: 16px;
      padding: 1rem 1.05rem 1.1rem;
      box-shadow: 0 10px 28px -20px rgba(11,61,145,.28);
    }
    @media (min-width: 992px) {
      .gl-svc-side { position: sticky; top: 88px; }
    }
    .gl-svc-side h3 {
      margin: 0 0 .35rem; font-family: Cairo, Tajawal, sans-serif;
      font-size: .95rem; font-weight: 900; color: #0B1220;
    }
    .gl-svc-side > p {
      margin: 0 0 .85rem; font-size: .76rem; line-height: 1.55; color: #5B6577; font-weight: 600;
    }
    .gl-svc-side__actions {
      display: flex; flex-direction: column; gap: .45rem;
    }
    .gl-svc-side__actions .sana-btn {
      width: 100%; justify-content: center;
      padding: .65rem 1rem; font-size: .82rem; min-height: 0;
    }
    .gl-svc-trust {
      display: grid; gap: .4rem; margin-top: .85rem; padding-top: .85rem;
      border-top: 1px solid #E8EEF8;
    }
    .gl-svc-trust__row {
      display: flex; align-items: center; gap: 8px;
      font-size: .72rem; font-weight: 700; color: #5B6577;
    }
    .gl-svc-trust__row i {
      width: 28px; height: 28px; border-radius: 8px;
      display: grid; place-items: center;
      background: #E8EEF8; color: #0B3D91; font-size: .75rem; flex-shrink: 0;
    }
    .gl-svc-more { margin-top: 1.35rem; }
    .gl-svc-more__head {
      display: flex; flex-wrap: wrap; align-items: end; justify-content: space-between;
      gap: .5rem; margin-bottom: .85rem;
    }
    .gl-svc-more__head h2 {
      margin: 0; font-family: Cairo, Tajawal, sans-serif;
      font-size: 1.1rem; font-weight: 900; color: #0B1220;
    }
    .gl-svc-more__head a {
      font-size: .76rem; font-weight: 800; color: #0B3D91; text-decoration: none !important;
    }
    .gl-svc-more__grid {
      display: grid; gap: .65rem;
      grid-template-columns: 1fr;
    }
    @media (min-width: 640px) {
      .gl-svc-more__grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (min-width: 992px) {
      .gl-svc-more__grid { grid-template-columns: repeat(3, 1fr); }
    }
    .gl-svc-tile {
      display: flex; flex-direction: column; gap: .45rem;
      padding: .85rem; border-radius: 14px;
      background: #fff; border: 1.5px solid #D7DDE6;
      text-decoration: none !important; color: inherit;
      transition: border-color .2s, box-shadow .2s, transform .2s;
    }
    .gl-svc-tile:hover {
      border-color: rgba(11,61,145,.35);
      box-shadow: 0 12px 28px -20px rgba(11,61,145,.35);
      transform: translateY(-2px);
    }
    .gl-svc-tile__media {
      height: 96px; border-radius: 10px; overflow: hidden;
      background: linear-gradient(145deg, #051F4D, #0B3D91);
      display: grid; place-items: center; color: rgba(255,255,255,.4); font-size: 1.5rem;
    }
    .gl-svc-tile__media img {
      width: 100%; height: 100%; object-fit: cover; display: block;
    }
    .gl-svc-tile strong {
      display: block; font-size: .86rem; font-weight: 900; color: #0B1220; line-height: 1.35;
    }
    .gl-svc-tile span {
      display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
      font-size: .72rem; line-height: 1.5; color: #5B6577; font-weight: 600;
    }
    .gl-svc-final {
      margin-top: 1.5rem;
      border-radius: 18px;
      padding: clamp(1.25rem, 3vw, 1.75rem);
      background: linear-gradient(135deg, #051F4D 0%, #0B3D91 55%, #072A66 100%);
      color: #fff; text-align: center;
      box-shadow: 0 16px 40px -24px rgba(11,61,145,.5);
    }
    .gl-svc-final h2 {
      margin: 0 0 .4rem; font-family: Cairo, Tajawal, sans-serif;
      font-size: clamp(1.1rem, 2.4vw, 1.4rem); font-weight: 900;
    }
    .gl-svc-final > p {
      margin: 0 auto 1rem; max-width: 34rem;
      font-size: .84rem; line-height: 1.65; color: rgba(255,255,255,.78); font-weight: 600;
    }
    .gl-svc-final__actions {
      display: flex; flex-wrap: wrap; justify-content: center; gap: .55rem;
    }
    .gl-svc-final__actions .sana-btn {
      padding: .7rem 1.15rem; font-size: .84rem; min-height: 0;
    }
    .gl-svc-back {
      display: inline-flex; align-items: center; gap: 6px; margin-top: 1.15rem;
      font-size: .8rem; font-weight: 800; color: #0B3D91; text-decoration: none !important;
    }
    .gl-svc-back:hover { color: #072A66; }
  </style>
</head>
<body class="sana-home sana-courses-page gl-svc-page">
<div id="sana-scroll-progress"></div>
<?php echo $__env->make('partials.landing.navbar', ['navActive' => '', 'navSolid' => true, 'navHero' => false], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="gl-svc">
  <div class="sana-container gl-svc-wrap">
    <nav class="gl-svc-crumb" aria-label="breadcrumb">
      <a href="<?php echo e(route('home')); ?>"><?php echo e(__('public.home')); ?></a>
      <i class="fas fa-chevron-<?php echo e($chevron); ?> text-[8px] opacity-50"></i>
      <a href="<?php echo e(route('public.services.index')); ?>"><?php echo e(__('public.services_page_title')); ?></a>
      <i class="fas fa-chevron-<?php echo e($chevron); ?> text-[8px] opacity-50"></i>
      <span><?php echo e(\Illuminate\Support\Str::limit($siteService->name, 42)); ?></span>
    </nav>

    <div class="gl-svc-layout sana-reveal">
      <div>
        <header class="gl-svc-hero <?php echo e($imageUrl ? 'gl-svc-hero--img' : ''); ?>">
          <?php if($imageUrl): ?>
            <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($siteService->name); ?>" loading="eager" decoding="async">
            <div class="gl-svc-hero__shade" aria-hidden="true"></div>
          <?php endif; ?>
          <div class="gl-svc-hero__inner">
            <span class="gl-svc-eyebrow"><i class="fas fa-concierge-bell"></i> <?php echo e(__('public.services_page_title')); ?></span>
            <h1 class="gl-svc-title">
              <?php echo e($siteService->name); ?>

              <span class="gl-svc-brand"><?php echo e($brand); ?></span>
            </h1>
            <?php if($summary !== ''): ?>
              <p class="gl-svc-lead"><?php echo e($summary); ?></p>
            <?php endif; ?>
          </div>
        </header>

        <article class="gl-svc-card" style="margin-top:1rem">
          <h2><?php echo e($isRtl ? 'تفاصيل الخدمة' : 'Service details'); ?></h2>
          <div class="gl-svc-prose">
            <?php echo nl2br(e($siteService->body)); ?>

          </div>
        </article>

        <a href="<?php echo e(route('public.services.index')); ?>" class="gl-svc-back">
          <i class="fas fa-arrow-<?php echo e($isRtl ? 'right' : 'left'); ?> text-xs"></i>
          <?php echo e(__('public.services_back_to_list')); ?>

        </a>
      </div>

      <aside class="gl-svc-side sana-reveal">
        <h3><?php echo e($isRtl ? 'هل هذه الخدمة تناسبك؟' : 'Is this right for you?'); ?></h3>
        <p><?php echo e(__('public.services_cta_text')); ?></p>
        <div class="gl-svc-side__actions">
          <a href="<?php echo e($waUrl); ?>" class="sana-btn sana-btn--wa" target="_blank" rel="noopener">
            <i class="fab fa-whatsapp"></i> <?php echo e($isRtl ? 'واتساب' : 'WhatsApp'); ?>

          </a>
          <a href="<?php echo e(route('public.contact')); ?>" class="sana-btn sana-btn--yellow">
            <i class="fas fa-paper-plane"></i> <?php echo e(__('public.contact_us')); ?>

          </a>
          <a href="<?php echo e(route('home')); ?>?open_trial=1" class="sana-btn sana-btn--purple">
            <i class="fas fa-clipboard-check"></i> <?php echo e(__('landing.academy.free_trial_cta')); ?>

          </a>
        </div>
        <div class="gl-svc-trust">
          <div class="gl-svc-trust__row"><i class="fas fa-user-graduate"></i><span><?php echo e($isRtl ? 'معلّمون متخصصون' : 'Specialist tutors'); ?></span></div>
          <div class="gl-svc-trust__row"><i class="fas fa-comments"></i><span><?php echo e($isRtl ? 'متابعة ودعم مباشر' : 'Direct support'); ?></span></div>
          <div class="gl-svc-trust__row"><i class="fas fa-layer-group"></i><span><?php echo e($isRtl ? 'مستويات واضحة' : 'Clear levels'); ?></span></div>
        </div>
      </aside>
    </div>

    <?php if($others->count() > 0): ?>
      <section class="gl-svc-more sana-reveal">
        <div class="gl-svc-more__head">
          <h2><?php echo e(__('public.services_more_title')); ?></h2>
          <a href="<?php echo e(route('public.services.index')); ?>"><?php echo e(__('public.services_back_to_list')); ?> →</a>
        </div>
        <div class="gl-svc-more__grid">
          <?php $__currentLoopData = $others; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('public.services.show', $o)); ?>" class="gl-svc-tile">
              <div class="gl-svc-tile__media">
                <?php if($o->publicImageUrl()): ?>
                  <img src="<?php echo e($o->publicImageUrl()); ?>" alt="" loading="lazy" decoding="async">
                <?php else: ?>
                  <i class="fas fa-layer-group" aria-hidden="true"></i>
                <?php endif; ?>
              </div>
              <strong><?php echo e($o->name); ?></strong>
              <span><?php echo e(\Illuminate\Support\Str::limit(strip_tags($o->summary ?: $o->body), 90)); ?></span>
            </a>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </section>
    <?php endif; ?>

    <section class="gl-svc-final sana-reveal">
      <h2><?php echo e(__('public.services_cta_title')); ?></h2>
      <p><?php echo e(__('public.services_cta_text')); ?></p>
      <div class="gl-svc-final__actions">
        <a href="<?php echo e(route('home')); ?>?open_trial=1" class="sana-btn sana-btn--yellow">
          <i class="fas fa-clipboard-check"></i> <?php echo e(__('landing.academy.free_trial_cta')); ?>

        </a>
        <a href="<?php echo e($waUrl); ?>" class="sana-btn sana-btn--wa" target="_blank" rel="noopener">
          <i class="fab fa-whatsapp"></i> <?php echo e($isRtl ? 'واتساب' : 'WhatsApp'); ?>

        </a>
        <a href="<?php echo e(route('public.courses')); ?>" class="sana-btn sana-btn--white-outline">
          <?php echo e(__('public.browse_courses')); ?>

        </a>
      </div>
    </section>
  </div>
</main>

<?php echo $__env->make('partials.landing.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\public\services\show.blade.php ENDPATH**/ ?>