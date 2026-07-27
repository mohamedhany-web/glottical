<?php
    $isRtl = app()->getLocale() === 'ar';
    $footer = \App\Services\PublicFooterSettings::payload();
    $waUrl = $footer['whatsapp_url'] ?? 'https://wa.me/201044610507';
    $navHero = $navHero ?? true;
    $navSolid = $navSolid ?? false;
    $active = $navActive ?? '';
?>
<header id="sana-nav" class="sana-nav <?php echo e($navSolid ? 'is-solid' : ($navHero ? 'sana-nav--hero' : 'is-solid')); ?>">
  <div class="sana-container">
    <div class="sana-nav__inner">
      <a href="<?php echo e(route('home')); ?>" class="sana-nav__brand">
        <span class="sana-nav__logo-text"><?php echo e(config('app.name', 'Glottical')); ?></span>
      </a>
      <nav class="sana-nav__links" aria-label="<?php echo e($isRtl ? 'القائمة' : 'Main'); ?>">
        <a href="<?php echo e(route('home')); ?>" class="<?php echo e($active === 'home' ? 'is-active' : ''); ?>"><?php echo e($isRtl ? 'الرئيسية' : 'Home'); ?></a>
        <a href="<?php echo e(route('public.courses')); ?>" class="sana-nav__path sana-nav__path--family <?php echo e($active === 'courses' ? 'is-active' : ''); ?>"><?php echo e(__('landing.nav.courses')); ?></a>
        <a href="<?php echo e(route('public.groups')); ?>" class="<?php echo e($active === 'groups' ? 'is-active' : ''); ?>"><?php echo e(__('landing.nav.groups')); ?></a>
        <a href="<?php echo e(route('public.contact')); ?>" class="<?php echo e($active === 'contact' ? 'is-active' : ''); ?>"><?php echo e($isRtl ? 'تواصل معنا' : 'Contact'); ?></a>
      </nav>
      <div class="sana-nav__actions">
        <a href="<?php echo e(route('login')); ?>" class="sana-nav__login"><?php echo e(__('landing.nav.login')); ?></a>
        <?php if(request()->routeIs('home')): ?>
          <button type="button" data-open-free-trial class="sana-nav__signup"><?php echo e(__('landing.academy.free_trial_cta')); ?></button>
        <?php else: ?>
          <a href="<?php echo e(route('home')); ?>?open_trial=1" class="sana-nav__signup"><?php echo e(__('landing.academy.free_trial_cta')); ?></a>
        <?php endif; ?>
      </div>
      <button type="button" id="sana-mobile-toggle" class="sana-nav__burger" aria-expanded="false" aria-controls="sana-mobile-menu" aria-label="<?php echo e($isRtl ? 'فتح القائمة' : 'Open menu'); ?>">
        <i class="fas fa-bars" aria-hidden="true"></i>
      </button>
    </div>
    <div id="sana-mobile-menu" class="sana-nav__mobile" aria-hidden="true">
      <a href="<?php echo e(route('home')); ?>" class="<?php echo e($active === 'home' ? 'is-active' : ''); ?>"><?php echo e($isRtl ? 'الرئيسية' : 'Home'); ?></a>
      <a href="<?php echo e(route('public.courses')); ?>" class="sana-nav__path sana-nav__path--family <?php echo e($active === 'courses' ? 'is-active' : ''); ?>"><?php echo e(__('landing.nav.courses')); ?></a>
      <a href="<?php echo e(route('public.instructors.index')); ?>"><?php echo e(__('landing.nav.instructors')); ?></a>
      <a href="<?php echo e(route('public.groups')); ?>" class="<?php echo e($active === 'groups' ? 'is-active' : ''); ?>"><?php echo e(__('landing.nav.groups')); ?></a>
      <a href="<?php echo e(route('public.contact')); ?>" class="<?php echo e($active === 'contact' ? 'is-active' : ''); ?>"><?php echo e($isRtl ? 'تواصل معنا' : 'Contact'); ?></a>
      <a href="<?php echo e(route('login')); ?>"><?php echo e(__('landing.nav.login')); ?></a>
      <?php if(request()->routeIs('home')): ?>
        <button type="button" data-open-free-trial class="sana-nav__signup sana-nav__signup--block"><?php echo e(__('landing.academy.free_trial_cta')); ?></button>
      <?php else: ?>
        <a href="<?php echo e(route('home')); ?>?open_trial=1" class="sana-nav__signup sana-nav__signup--block"><?php echo e(__('landing.academy.free_trial_cta')); ?></a>
      <?php endif; ?>
      <a href="<?php echo e($waUrl); ?>" class="sana-nav__signup sana-nav__signup--block sana-nav__signup--wa" target="_blank" rel="noopener"><?php echo e($isRtl ? 'تواصل عبر واتساب' : 'WhatsApp'); ?></a>
    </div>
  </div>
</header>
<div id="sana-mobile-backdrop" class="sana-nav__backdrop" aria-hidden="true"></div>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\partials\landing\navbar.blade.php ENDPATH**/ ?>