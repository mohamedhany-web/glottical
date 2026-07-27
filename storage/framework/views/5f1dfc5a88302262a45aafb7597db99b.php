<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $brand = config('app.name', 'Glottical');
    $packages = $packages ?? collect();
    $tutoringGroups = $tutoringGroups ?? collect();
    $footer = \App\Services\PublicFooterSettings::payload();
    $waUrl = $footer['whatsapp_url'] ?? '#';
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title><?php echo e(__('public.pricing_page_title')); ?> — <?php echo e($brand); ?></title>
  <meta name="description" content="<?php echo e(__('public.pricing_meta_description')); ?>">
  <link rel="canonical" href="<?php echo e(route('public.pricing')); ?>">
  <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('partials.landing.head', ['landingCss' => ['theme', 'courses-catalog', 'pricing']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <style>
    .gl-prx-grid {
      display: grid;
      gap: 1.15rem;
      grid-template-columns: 1fr;
    }
    @media (min-width: 700px) {
      .gl-prx-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (min-width: 1024px) {
      .gl-prx-grid { grid-template-columns: repeat(3, 1fr); }
    }
    .gl-prx-card {
      display: flex;
      flex-direction: column;
      border-radius: 18px;
      border: 1.5px solid rgba(11, 61, 145, .12);
      background: #fff;
      box-shadow: 0 14px 36px -22px rgba(11, 61, 145, .35);
      overflow: hidden;
      transition: transform .2s ease, box-shadow .2s ease;
    }
    .gl-prx-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 22px 44px -18px rgba(11, 61, 145, .4);
    }
    .gl-prx-card.is-popular {
      border-color: rgba(245, 184, 0, .55);
      box-shadow: 0 18px 40px -16px rgba(245, 184, 0, .35);
    }
    .gl-prx-card__body {
      padding: 1.25rem 1.2rem 1.35rem;
      display: flex;
      flex-direction: column;
      flex: 1;
      gap: .75rem;
    }
    .gl-prx-card__badge {
      align-self: flex-start;
      font-size: .68rem;
      font-weight: 800;
      padding: .28rem .65rem;
      border-radius: 999px;
      background: var(--gold, #F5B800);
      color: var(--p-deep, #051F4D);
    }
    .gl-prx-card__title {
      font-family: Cairo, Tajawal, sans-serif;
      font-weight: 900;
      font-size: 1.15rem;
      color: var(--p-deep, #051F4D);
      line-height: 1.35;
      margin: 0;
    }
    .gl-prx-card__meta {
      font-size: .78rem;
      color: rgba(5, 31, 77, .55);
      margin: 0;
    }
    .gl-prx-card__price {
      font-family: Cairo, sans-serif;
      font-weight: 900;
      font-size: 1.65rem;
      color: var(--p, #0B3D91);
      margin: 0;
      line-height: 1.2;
    }
    .gl-prx-card__price span { font-size: .9rem; font-weight: 700; color: rgba(5, 31, 77, .45); }
    .gl-prx-card__old {
      font-size: .8rem;
      color: rgba(5, 31, 77, .4);
      text-decoration: line-through;
      margin: 0;
    }
    .gl-prx-card__desc {
      font-size: .84rem;
      line-height: 1.7;
      color: rgba(5, 31, 77, .62);
      margin: 0;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    .gl-prx-card__list {
      list-style: none;
      margin: 0;
      padding: 0;
      display: grid;
      gap: .4rem;
      flex: 1;
    }
    .gl-prx-card__list li {
      display: flex;
      gap: .45rem;
      align-items: flex-start;
      font-size: .8rem;
      color: rgba(5, 31, 77, .72);
    }
    .gl-prx-card__list i { color: #10b981; margin-top: .2rem; font-size: .7rem; }
    .gl-prx-empty {
      text-align: center;
      padding: 2.5rem 1rem;
      color: rgba(5, 31, 77, .5);
      font-size: .95rem;
    }
    .gl-prx-thumb {
      width: 56px;
      height: 56px;
      border-radius: 14px;
      overflow: hidden;
      flex-shrink: 0;
      background: rgba(11, 61, 145, .08);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--p, #0B3D91);
    }
    .gl-prx-thumb img { width: 100%; height: 100%; object-fit: cover; }
  </style>
</head>
<body class="sana-home sana-courses-page sana-pricing-page">
<div id="sana-scroll-progress"></div>
<?php echo $__env->make('partials.landing.navbar', ['navActive' => 'pricing', 'navSolid' => true, 'navHero' => false], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main>
  <section class="sana-prx-hero">
    <div class="sana-container sana-prx-hero__inner sana-reveal">
      <nav class="sana-cat-hero__breadcrumb" aria-label="breadcrumb" style="justify-content:center;margin-bottom:1rem">
        <a href="<?php echo e(route('home')); ?>"><?php echo e(__('public.home')); ?></a>
        <span aria-hidden="true">/</span>
        <span><?php echo e(__('public.pricing_page_title')); ?></span>
      </nav>
      <span class="sana-prx-hero__eyebrow"><i class="fas fa-tags"></i> <?php echo e(__('public.pricing_hero_kicker')); ?></span>
      <h1 class="sana-prx-hero__title">
        <?php echo e(__('public.pricing_hero_title')); ?>

        <span class="hl"><?php echo e(__('public.pricing_hero_accent')); ?></span>
      </h1>
      <p class="sana-prx-hero__sub"><?php echo e(__('public.pricing_hero_sub')); ?></p>
      <p class="sana-prx-pricing-note"><?php echo e(__('public.pricing_hero_note')); ?></p>
      <div class="sana-prx-hero__actions">
        <a href="#packages" class="sana-btn sana-btn--yellow sana-btn--lg"><i class="fas fa-box-open"></i> <?php echo e(__('public.pricing_packages_title')); ?></a>
        <a href="#tutoring-groups" class="sana-btn sana-btn--white-outline sana-btn--lg"><i class="fas fa-users"></i> <?php echo e(__('public.pricing_groups_title')); ?></a>
      </div>
    </div>
  </section>

  <section class="sana-section" id="packages">
    <div class="sana-container">
      <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:28px">
        <span class="sana-head__eyebrow"><?php echo e(__('public.pricing_packages_badge')); ?></span>
        <h2 class="sana-head__title"><?php echo e(__('public.pricing_packages_title')); ?></h2>
        <span class="sana-head__line"></span>
        <p class="sana-head__sub"><?php echo e(__('public.pricing_packages_sub')); ?></p>
      </div>

      <?php if($packages->isNotEmpty()): ?>
        <div class="gl-prx-grid">
          <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $cardBody = trim((string) ($package->card_summary ?? '')) !== ''
                  ? $package->card_summary
                  : ($package->description ?? '');
              $cardFeatures = collect($package->features ?? [])->map(fn ($f) => trim((string) $f))->filter()->values();
              $isPopular = (bool) $package->is_popular;
            ?>
            <article class="<?php echo \Illuminate\Support\Arr::toCssClasses(['gl-prx-card sana-reveal', 'is-popular' => $isPopular]); ?>">
              <div class="gl-prx-card__body">
                <?php if($isPopular): ?>
                  <span class="gl-prx-card__badge"><?php echo e(__('public.pricing_package_popular')); ?></span>
                <?php endif; ?>
                <div style="display:flex;gap:.85rem;align-items:flex-start">
                  <div class="gl-prx-thumb">
                    <?php if($package->thumbnail): ?>
                      <img src="<?php echo e(storage_asset($package->thumbnail)); ?>" alt="" loading="lazy">
                    <?php else: ?>
                      <i class="fas fa-graduation-cap"></i>
                    <?php endif; ?>
                  </div>
                  <div>
                    <h3 class="gl-prx-card__title"><?php echo e($package->name); ?></h3>
                    <?php if(($package->courses_count ?? 0) > 0): ?>
                      <p class="gl-prx-card__meta">
                        <i class="fas fa-book-open"></i>
                        <?php echo e(__('public.path_courses_count', ['count' => $package->courses_count])); ?>

                      </p>
                    <?php endif; ?>
                  </div>
                </div>

                <div>
                  <?php if($package->original_price && $package->original_price > $package->price): ?>
                    <p class="gl-prx-card__old"><?php echo e(number_format($package->original_price, 0)); ?> <?php echo e(__('public.currency_egp')); ?></p>
                  <?php endif; ?>
                  <p class="gl-prx-card__price">
                    <?php if($package->price > 0): ?>
                      <?php echo e(number_format($package->price, 0)); ?> <span><?php echo e(__('public.currency_egp')); ?></span>
                    <?php else: ?>
                      <?php echo e(__('public.free_price')); ?>

                    <?php endif; ?>
                  </p>
                </div>

                <?php if($cardBody !== ''): ?>
                  <p class="gl-prx-card__desc"><?php echo e($cardBody); ?></p>
                <?php endif; ?>

                <?php if($cardFeatures->isNotEmpty()): ?>
                  <ul class="gl-prx-card__list">
                    <?php $__currentLoopData = $cardFeatures->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <li><i class="fas fa-check"></i><span><?php echo e($feature); ?></span></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </ul>
                <?php endif; ?>

                <a href="<?php echo e(route('public.package.show', $package->slug)); ?>"
                   class="sana-btn <?php echo e($isPopular ? 'sana-btn--yellow' : 'sana-btn--purple'); ?>"
                   style="margin-top:auto;justify-content:center">
                  <i class="fas fa-<?php echo e($package->price > 0 ? 'shopping-cart' : 'eye'); ?>"></i>
                  <?php echo e($package->price > 0 ? __('public.pricing_package_buy') : __('public.view_details')); ?>

                </a>
              </div>
            </article>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      <?php else: ?>
        <p class="gl-prx-empty sana-reveal"><?php echo e(__('public.pricing_no_packages')); ?></p>
      <?php endif; ?>
    </div>
  </section>

  <section class="sana-section sana-section--soft" id="tutoring-groups">
    <div class="sana-container">
      <div class="sana-head sana-head--center sana-reveal" style="margin-bottom:28px">
        <span class="sana-head__eyebrow"><?php echo e(__('public.pricing_groups_badge')); ?></span>
        <h2 class="sana-head__title"><?php echo e(__('public.pricing_groups_title')); ?></h2>
        <span class="sana-head__line"></span>
        <p class="sana-head__sub"><?php echo e(__('public.pricing_groups_sub')); ?></p>
      </div>

      <?php if($tutoringGroups->isNotEmpty()): ?>
        <div class="gl-prx-grid">
          <?php $__currentLoopData = $tutoringGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $img = $group->imageUrl();
              $isFeatured = (bool) $group->is_featured;
            ?>
            <article class="<?php echo \Illuminate\Support\Arr::toCssClasses(['gl-prx-card sana-reveal', 'is-popular' => $isFeatured]); ?>">
              <div class="gl-prx-card__body">
                <?php if($isFeatured): ?>
                  <span class="gl-prx-card__badge"><?php echo e(__('public.pricing_package_popular')); ?></span>
                <?php endif; ?>
                <div style="display:flex;gap:.85rem;align-items:flex-start">
                  <div class="gl-prx-thumb">
                    <?php if($img): ?>
                      <img src="<?php echo e($img); ?>" alt="" loading="lazy">
                    <?php else: ?>
                      <i class="fas fa-<?php echo e($group->isIndividual() ? 'user' : 'users'); ?>"></i>
                    <?php endif; ?>
                  </div>
                  <div>
                    <h3 class="gl-prx-card__title"><?php echo e($group->title); ?></h3>
                    <p class="gl-prx-card__meta">
                      <?php echo e($group->typeLabel()); ?>

                      <?php if($group->instructor): ?>
                        · <?php echo e($group->instructor->name); ?>

                      <?php endif; ?>
                    </p>
                  </div>
                </div>

                <p class="gl-prx-card__price">
                  <?php if($group->price !== null && (float) $group->price > 0): ?>
                    <?php echo e(number_format((float) $group->price, 0)); ?>

                    <span><?php echo e($group->currency ?: __('public.currency_egp')); ?></span>
                  <?php else: ?>
                    <?php echo e(__('public.pricing_groups_price_contact')); ?>

                  <?php endif; ?>
                </p>

                <?php if(filled($group->description)): ?>
                  <p class="gl-prx-card__desc"><?php echo e($group->description); ?></p>
                <?php endif; ?>

                <ul class="gl-prx-card__list">
                  <?php if($group->duration_minutes): ?>
                    <li><i class="fas fa-clock"></i><span><?php echo e(__('public.pricing_groups_duration', ['minutes' => $group->duration_minutes])); ?></span></li>
                  <?php endif; ?>
                  <?php if($group->capacity): ?>
                    <li><i class="fas fa-user-group"></i><span><?php echo e(__('public.pricing_groups_capacity', ['count' => $group->capacity])); ?></span></li>
                  <?php endif; ?>
                </ul>

                <a href="<?php echo e(route('public.groups.show', $group->slug)); ?>"
                   class="sana-btn <?php echo e($isFeatured ? 'sana-btn--yellow' : 'sana-btn--purple'); ?>"
                   style="margin-top:auto;justify-content:center">
                  <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?>"></i>
                  <?php echo e(__('public.pricing_groups_cta')); ?>

                </a>
              </div>
            </article>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      <?php else: ?>
        <p class="gl-prx-empty sana-reveal"><?php echo e(__('public.pricing_no_groups')); ?></p>
      <?php endif; ?>
    </div>
  </section>

  <section class="sana-section">
    <div class="sana-container">
      <div class="sana-ab-final__box sana-reveal" style="text-align:center;padding:clamp(1.5rem,3vw,2.25rem);border-radius:22px;background:linear-gradient(135deg,#0B3D91,#072A66);color:#fff">
        <h2 style="font-family:Cairo,sans-serif;font-weight:900;font-size:clamp(1.25rem,2.5vw,1.75rem);margin:0 0 .65rem"><?php echo e(__('public.pricing_footer_cta_title')); ?></h2>
        <p style="opacity:.75;max-width:36rem;margin:0 auto 1.15rem;line-height:1.7"><?php echo e(__('public.pricing_footer_cta_sub')); ?></p>
        <div style="display:flex;flex-wrap:wrap;gap:.65rem;justify-content:center">
          <a href="<?php echo e(route('register')); ?>" class="sana-btn sana-btn--yellow"><?php echo e(__('public.register_free')); ?></a>
          <a href="<?php echo e(route('public.contact')); ?>" class="sana-btn sana-btn--white-outline"><?php echo e(__('public.pricing_footer_contact')); ?></a>
          <a href="<?php echo e($waUrl); ?>" class="sana-btn sana-btn--wa" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> WhatsApp</a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php echo $__env->make('partials.landing.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<script src="<?php echo e(versioned_asset('js/landing/site.js')); ?>" defer></script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\public\pricing.blade.php ENDPATH**/ ?>