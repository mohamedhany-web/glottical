<?php $isRtl = app()->getLocale() === 'ar'; ?>
<footer class="mt-24 border-t border-line bg-ink text-white">
  <div class="container-wide py-16 md:py-20">
    <div class="grid gap-12 lg:grid-cols-[1.15fr_2fr]">
      <div class="space-y-5">
        <p class="text-3xl font-bold">Glottical</p>
        <p class="max-w-sm text-sm leading-8 text-white/70"><?php echo e(__('landing.academy.identity_sub')); ?></p>
        <button type="button" data-open-free-trial class="inline-flex items-center gap-2 rounded-xl border border-white/15 bg-white/5 px-4 py-2.5 text-sm font-medium transition hover:bg-white/10">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-metal" aria-hidden="true"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
          <?php echo e(__('landing.academy.free_trial_cta')); ?>

        </button>
      </div>
      <div class="grid gap-10 sm:grid-cols-3">
        <div>
          <p class="mb-4 text-sm font-semibold"><?php echo e($isRtl ? 'تعلّم' : 'Learn'); ?></p>
          <nav class="space-y-3 text-sm text-white/65" aria-label="<?php echo e($isRtl ? 'روابط التعلم' : 'Learn links'); ?>">
            <a class="block transition hover:text-white" href="<?php echo e(route('public.courses')); ?>"><?php echo e(__('landing.nav.courses')); ?></a>
            <a class="block transition hover:text-white" href="<?php echo e(route('public.courses')); ?>"><?php echo e(__('landing.nav.courses')); ?></a>
            <a class="block transition hover:text-white" href="<?php echo e(route('public.groups')); ?>"><?php echo e(__('landing.nav.groups')); ?></a>
            <a class="block transition hover:text-white" href="<?php echo e(route('public.categories')); ?>"><?php echo e(__('landing.nav.categories')); ?></a>
            <a class="block transition hover:text-white" href="<?php echo e(route('public.instructors.index')); ?>"><?php echo e(__('landing.nav.instructors')); ?></a>
          </nav>
        </div>
        <div>
          <p class="mb-4 text-sm font-semibold"><?php echo e($isRtl ? 'للطلاب' : 'Students'); ?></p>
          <nav class="space-y-3 text-sm text-white/65">
            <a class="block transition hover:text-white" href="<?php echo e(route('register')); ?>"><?php echo e($isRtl ? 'إنشاء حساب' : 'Sign up'); ?></a>
            <a class="block transition hover:text-white" href="<?php echo e(route('login')); ?>"><?php echo e($isRtl ? 'تسجيل الدخول' : 'Login'); ?></a>
            <button type="button" data-open-free-trial class="block transition hover:text-white text-start"><?php echo e(__('landing.academy.free_trial_cta')); ?></button>
            <a class="block transition hover:text-white" href="<?php echo e(route('public.contact')); ?>"><?php echo e($isRtl ? 'تواصل معنا' : 'Contact'); ?></a>
          </nav>
        </div>
        <div>
          <p class="mb-4 text-sm font-semibold">Glottical</p>
          <nav class="space-y-3 text-sm text-white/65">
            <a class="block transition hover:text-white" href="<?php echo e(route('public.about')); ?>"><?php echo e($isRtl ? 'من نحن' : 'About'); ?></a>
            <a class="block transition hover:text-white" href="<?php echo e(route('public.contact')); ?>"><?php echo e($isRtl ? 'الدعم' : 'Support'); ?></a>
            <?php if(auth()->guard()->check()): ?>
              <a class="block transition hover:text-white" href="<?php echo e(url('/dashboard')); ?>"><?php echo e($isRtl ? 'لوحتي' : 'Dashboard'); ?></a>
            <?php endif; ?>
          </nav>
        </div>
      </div>
    </div>
  </div>
  <div class="border-t border-white/10">
    <div class="container-wide flex flex-wrap items-center justify-between gap-4 py-6 text-xs text-white/50">
      <p>© <?php echo e(date('Y')); ?> Glottical. <?php echo e($isRtl ? 'جميع الحقوق محفوظة.' : 'All rights reserved.'); ?></p>
      <div class="flex flex-wrap gap-4">
        <a href="<?php echo e(route('public.contact')); ?>" class="transition hover:text-white/80"><?php echo e($isRtl ? 'الخصوصية' : 'Privacy'); ?></a>
        <a href="<?php echo e(route('public.contact')); ?>" class="transition hover:text-white/80"><?php echo e($isRtl ? 'الشروط' : 'Terms'); ?></a>
      </div>
    </div>
  </div>
</footer>
<script>
document.getElementById('nav-toggle')?.addEventListener('click', function () {
  var nav = document.getElementById('mobile-nav');
  if (!nav) return;
  var open = !nav.classList.contains('hidden');
  nav.classList.toggle('hidden', open);
  this.setAttribute('aria-expanded', open ? 'false' : 'true');
});
</script>
<?php echo $__env->make('partials.unregister-service-worker', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\partials\atheer-home-footer.blade.php ENDPATH**/ ?>