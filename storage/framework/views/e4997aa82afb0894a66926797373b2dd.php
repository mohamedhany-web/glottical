<?php
    $isRtl = app()->getLocale() === 'ar';
    $footer = \App\Services\PublicFooterSettings::payload();
    $waUrl = $footer['whatsapp_url'] ?? '#';
    $brand = config('app.name', 'Glottical');
?>
<footer class="sana-foot-m">
  <div class="sana-container">
    <div class="sana-foot-m__grid">
      <div class="sana-foot-m__brand">
        <a href="<?php echo e(route('home')); ?>" class="sana-foot-m__logo"><span><?php echo e($brand); ?></span></a>
        <p><?php echo e($footer['blurb'] ?? __('landing.academy.identity_sub')); ?></p>
      </div>
      <div>
        <h4><?php echo e($isRtl ? 'تصفّح' : 'Explore'); ?></h4>
        <ul>
          <li><a href="<?php echo e(route('public.groups')); ?>"><?php echo e(__('landing.nav.groups')); ?></a></li>
          <li><a href="<?php echo e(route('public.courses')); ?>"><?php echo e(__('landing.nav.courses')); ?></a></li>
          <li><a href="<?php echo e(route('public.instructors.index')); ?>"><?php echo e(__('landing.nav.instructors')); ?></a></li>
          <li><a href="<?php echo e(route('public.categories')); ?>"><?php echo e(__('landing.nav.categories')); ?></a></li>
        </ul>
      </div>
      <div>
        <h4><?php echo e($isRtl ? 'روابط مهمة' : 'Links'); ?></h4>
        <ul>
          <li><a href="<?php echo e(route('public.pricing')); ?>"><?php echo e(__('landing.nav.pricing')); ?></a></li>
          <li><a href="<?php echo e(route('public.contact')); ?>"><?php echo e($isRtl ? 'اتصل بنا' : 'Contact'); ?></a></li>
          <li><a href="<?php echo e(route('register')); ?>"><?php echo e(__('landing.nav.register')); ?></a></li>
        </ul>
      </div>
      <div>
        <h4><?php echo e($isRtl ? 'تواصل معنا' : 'Contact'); ?></h4>
        <ul>
          <li><a href="<?php echo e($waUrl); ?>" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> <?php echo e($isRtl ? 'واتساب' : 'WhatsApp'); ?></a></li>
          <?php if(!empty($footer['email'])): ?>
            <li><a href="mailto:<?php echo e($footer['email']); ?>" dir="ltr"><?php echo e($footer['email']); ?></a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
    <div class="sana-foot-m__bottom">
      <p>&copy; <?php echo e(date('Y')); ?> <?php echo e($brand); ?>. <?php echo e($isRtl ? 'جميع الحقوق محفوظة.' : 'All rights reserved.'); ?></p>
    </div>
  </div>
</footer>
<script src="<?php echo e(versioned_asset('js/landing/site.js')); ?>" defer></script>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\partials\landing\footer.blade.php ENDPATH**/ ?>