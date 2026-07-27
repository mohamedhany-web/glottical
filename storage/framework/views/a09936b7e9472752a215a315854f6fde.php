
<?php
    $isRtl = app()->getLocale() === 'ar';
?>
<div class="announce-bar bg-ink text-white">
  <div class="container-wide flex h-10 items-center justify-center gap-2 sm:gap-3 text-center text-xs sm:text-sm">
    <span class="text-metal shrink-0">✦</span>
    <p class="min-w-0"><?php echo e($isRtl ? 'تقييم مستوى مجاني · حصص مباشرة · شهادة معتمدة' : 'Free level assessment · Live sessions · Certified learning'); ?></p>
    <button type="button" data-open-free-trial class="hidden underline underline-offset-4 opacity-90 transition hover:opacity-100 sm:inline shrink-0"><?php echo e($isRtl ? 'احجز الآن' : 'Book now'); ?></button>
  </div>
</div>

<header class="site-header-shell sticky top-0 z-50 border-b border-line/80 bg-surface/90 backdrop-blur-xl">
  <div class="container-wide flex h-16 min-w-0 items-center gap-2 md:h-20 md:gap-3">
    <button type="button" id="nav-toggle" class="inline-flex size-10 shrink-0 items-center justify-center rounded-xl transition hover:bg-canvas lg:hidden" aria-label="<?php echo e($isRtl ? 'القائمة' : 'Menu'); ?>" aria-expanded="false" aria-controls="mobile-nav">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 5h16M4 12h16M4 19h16"/></svg>
    </button>

    <a href="<?php echo e(url('/')); ?>" class="shrink-0 text-xl font-bold tracking-tight text-ink md:text-2xl xl:text-3xl">Glottical</a>

    <nav class="<?php echo e($isRtl ? 'mr-1' : 'ml-1'); ?> hidden min-w-0 items-center gap-0 lg:flex" aria-label="<?php echo e($isRtl ? 'التنقل الرئيسي' : 'Main'); ?>">
      <a class="nav-link rounded-lg px-2 py-2 text-sm text-ink-soft hover:bg-canvas hover:text-ink xl:px-3" href="<?php echo e(route('public.courses')); ?>"><?php echo e(__('landing.nav.courses')); ?></a>
      <a class="nav-link rounded-lg px-2 py-2 text-sm text-ink-soft hover:bg-canvas hover:text-ink xl:px-3" href="<?php echo e(route('public.courses')); ?>"><?php echo e(__('landing.nav.courses')); ?></a>
      <a class="nav-link rounded-lg px-2 py-2 text-sm text-ink-soft hover:bg-canvas hover:text-ink xl:px-3" href="<?php echo e(route('public.groups')); ?>"><?php echo e(__('landing.nav.groups')); ?></a>
      <a class="nav-link hidden rounded-lg px-2 py-2 text-sm text-ink-soft hover:bg-canvas hover:text-ink xl:inline-flex xl:px-3" href="<?php echo e(route('public.categories')); ?>"><?php echo e(__('landing.nav.categories')); ?></a>
      <a class="nav-link rounded-lg px-2 py-2 text-sm text-ink-soft hover:bg-canvas hover:text-ink xl:px-3" href="<?php echo e(route('public.instructors.index')); ?>"><?php echo e(__('landing.nav.instructors')); ?></a>
    </nav>

    <div class="mx-auto hidden min-w-0 max-w-md flex-1 xl:max-w-xl md:block">
      <form action="<?php echo e(route('public.courses')); ?>" method="get" class="relative">
        <svg class="pointer-events-none absolute top-1/2 <?php echo e($isRtl ? 'right-3' : 'left-3'); ?> size-4 -translate-y-1/2 text-muted" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        <input name="q" class="h-11 w-full rounded-xl border border-line bg-surface <?php echo e($isRtl ? 'pr-10 pl-4' : 'pl-10 pr-4'); ?> text-sm transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20 xl:h-12" placeholder="<?php echo e(__('landing.academy.search_placeholder')); ?>" aria-label="<?php echo e($isRtl ? 'بحث' : 'Search'); ?>" />
      </form>
    </div>

    <div class="ms-auto flex shrink-0 items-center gap-0.5 sm:gap-1">
      <button type="button" data-open-free-trial class="btn-press hidden h-10 items-center rounded-xl bg-accent px-3 text-sm font-medium text-white transition hover:bg-[#0d4f4a] xl:inline-flex xl:px-4">
        <?php echo e(__('landing.academy.free_trial_cta')); ?>

      </button>

      <?php if(auth()->guard()->check()): ?>
        <a href="<?php echo e(url('/dashboard')); ?>" class="inline-flex size-10 items-center justify-center rounded-xl transition hover:bg-canvas" aria-label="<?php echo e($isRtl ? 'حسابي' : 'Account'); ?>">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </a>
      <?php else: ?>
        <a href="<?php echo e(route('login')); ?>" class="inline-flex size-10 items-center justify-center rounded-xl transition hover:bg-canvas" aria-label="<?php echo e($isRtl ? 'تسجيل الدخول' : 'Login'); ?>">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </a>
        <a href="<?php echo e(route('register')); ?>" class="hidden h-10 items-center rounded-xl bg-accent px-3 text-sm font-medium text-white transition hover:bg-[#0d4f4a] sm:inline-flex xl:px-4"><?php echo e($isRtl ? 'إنشاء حساب' : 'Sign up'); ?></a>
      <?php endif; ?>

      <div class="ms-1 hidden items-center overflow-hidden rounded-xl border border-line text-xs font-semibold sm:flex">
        <a href="<?php echo e(url('/?lang=ar')); ?>" class="px-2.5 py-2 <?php echo e(app()->getLocale()==='ar' ? 'bg-accent text-white' : 'hover:bg-canvas'); ?>">ع</a>
        <a href="<?php echo e(url('/?lang=en')); ?>" class="px-2.5 py-2 <?php echo e(app()->getLocale()==='en' ? 'bg-accent text-white' : 'hover:bg-canvas'); ?>">EN</a>
      </div>
    </div>
  </div>

  <div id="mobile-nav" class="hidden border-t border-line bg-surface px-4 py-4 lg:hidden">
    <div class="mb-4">
      <form action="<?php echo e(route('public.courses')); ?>" method="get" class="relative">
        <svg class="pointer-events-none absolute top-1/2 <?php echo e($isRtl ? 'right-3' : 'left-3'); ?> size-4 -translate-y-1/2 text-muted" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        <input name="q" class="h-11 w-full rounded-xl border border-line bg-canvas <?php echo e($isRtl ? 'pr-10 pl-4' : 'pl-10 pr-4'); ?> text-sm" placeholder="<?php echo e($isRtl ? 'ابحث…' : 'Search…'); ?>" aria-label="<?php echo e($isRtl ? 'بحث' : 'Search'); ?>" />
      </form>
    </div>
    <div class="flex flex-col gap-0.5 text-sm font-medium">
      <a class="rounded-xl px-3 py-3 transition hover:bg-canvas" href="<?php echo e(route('public.courses')); ?>"><?php echo e(__('landing.nav.courses')); ?></a>
      <a class="rounded-xl px-3 py-3 transition hover:bg-canvas" href="<?php echo e(route('public.courses')); ?>"><?php echo e(__('landing.nav.courses')); ?></a>
      <a class="rounded-xl px-3 py-3 transition hover:bg-canvas" href="<?php echo e(route('public.groups')); ?>"><?php echo e(__('landing.nav.groups')); ?></a>
      <a class="rounded-xl px-3 py-3 transition hover:bg-canvas" href="<?php echo e(route('public.categories')); ?>"><?php echo e(__('landing.nav.categories')); ?></a>
      <a class="rounded-xl px-3 py-3 transition hover:bg-canvas" href="<?php echo e(route('public.instructors.index')); ?>"><?php echo e(__('landing.nav.instructors')); ?></a>
      <button type="button" data-open-free-trial class="rounded-xl px-3 py-3 text-start font-semibold text-accent transition hover:bg-canvas"><?php echo e(__('landing.academy.free_trial_cta')); ?></button>
      <?php if(auth()->guard()->guest()): ?>
        <a class="rounded-xl px-3 py-3 transition hover:bg-canvas" href="<?php echo e(route('login')); ?>"><?php echo e(__('landing.nav.login') ?? ($isRtl ? 'تسجيل الدخول' : 'Login')); ?></a>
        <a class="rounded-xl px-3 py-3 transition hover:bg-canvas" href="<?php echo e(route('register')); ?>"><?php echo e(__('landing.nav.register') ?? ($isRtl ? 'إنشاء حساب' : 'Register')); ?></a>
      <?php else: ?>
        <a class="rounded-xl px-3 py-3 transition hover:bg-canvas" href="<?php echo e(url('/dashboard')); ?>"><?php echo e($isRtl ? 'لوحتي' : 'Dashboard'); ?></a>
      <?php endif; ?>
      <a class="rounded-xl px-3 py-3 text-muted transition hover:bg-canvas hover:text-ink" href="<?php echo e(route('public.contact')); ?>"><?php echo e($isRtl ? 'تواصل معنا' : 'Contact'); ?></a>
    </div>
  </div>
</header>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\partials\atheer-home-header.blade.php ENDPATH**/ ?>