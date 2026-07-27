<?php $__env->startSection('title', __('auth.register')); ?>
<?php $__env->startSection('main_class', 'gl-auth-main--top'); ?>

<?php $__env->startSection('body_attrs', 'x-data="{ showPassword: false, showPasswordConfirm: false }"'); ?>

<?php $__env->startSection('nav_action'); ?>
  <a href="<?php echo e(route('login')); ?>" class="gl-auth-nav-link"><?php echo e(__('auth.login')); ?></a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php
  $isRtl = app()->getLocale() === 'ar';
  $phoneCountries = $phoneCountries ?? config('phone_countries.countries', []);
  $defaultCountry = $defaultCountry ?? collect($phoneCountries)->firstWhere('code', config('phone_countries.default_country', 'SA'));
?>
<div class="gl-auth-card gl-auth-card--wide">
  <div class="gl-auth-brand"><?php echo e(config('app.name', 'Glottical')); ?></div>
  <h1 class="gl-auth-title"><?php echo e(__('auth.create_account_btn')); ?><br><em><?php echo e($isRtl ? 'مع Glottical' : 'with Glottical'); ?></em></h1>
  <p class="gl-auth-lead"><?php echo e(__('auth.register_subtitle')); ?></p>

  <div class="gl-auth-alert gl-auth-alert--info"><?php echo e(__('auth.register_portal_note')); ?></div>

  <?php if(! empty($pendingReferralCode)): ?>
    <div class="gl-auth-alert gl-auth-alert--ok">
      <strong><?php echo e(__('auth.referral_invite_title')); ?></strong><br>
      <?php echo e(__('auth.referral_invite_body', ['code' => $pendingReferralCode])); ?>

    </div>
  <?php endif; ?>

  <?php if($errors->any()): ?>
    <div class="gl-auth-alert gl-auth-alert--err"><?php echo e($errors->first()); ?></div>
  <?php endif; ?>

  <form action="<?php echo e(route('register')); ?>" method="POST" novalidate>
    <?php echo csrf_field(); ?>
    <input type="hidden" name="referral_code" value="<?php echo e(old('referral_code', $pendingReferralCode ?? '')); ?>">

    <div class="gl-auth-field">
      <label for="name"><?php echo e(__('auth.full_name')); ?></label>
      <div class="gl-auth-input-wrap">
        <span class="gl-auth-icon" aria-hidden="true"><i class="fas fa-user"></i></span>
        <input
          type="text"
          name="name"
          id="name"
          value="<?php echo e(old('name')); ?>"
          required
          autocomplete="name"
          placeholder="<?php echo e(__('auth.enter_full_name')); ?>"
          class="gl-auth-input has-icon <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> has-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
        >
      </div>
      <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="gl-auth-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="gl-auth-field">
      <label><?php echo e(__('auth.phone_number')); ?></label>
      <div class="gl-auth-phone <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> has-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
        <select name="country_code" required dir="ltr" aria-label="<?php echo e(__('auth.country_code_aria')); ?>">
          <?php $__currentLoopData = $phoneCountries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($c['dial_code']); ?>" <?php echo e(old('country_code', $defaultCountry['dial_code'] ?? '+966') === $c['dial_code'] ? 'selected' : ''); ?>>
              <?php echo e($c['dial_code']); ?> <?php echo e($isRtl ? ($c['name_ar'] ?? '') : ($c['name_en'] ?? $c['name_ar'] ?? '')); ?>

            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <input
          type="tel"
          name="phone"
          value="<?php echo e(old('phone')); ?>"
          required
          placeholder="5xxxxxxxx"
          dir="ltr"
          autocomplete="tel-national"
          aria-label="<?php echo e(__('auth.phone_aria')); ?>"
        >
      </div>
      <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="gl-auth-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="gl-auth-field">
      <label for="email"><?php echo e(__('auth.email')); ?></label>
      <div class="gl-auth-input-wrap">
        <span class="gl-auth-icon" aria-hidden="true"><i class="fas fa-envelope"></i></span>
        <input
          type="email"
          name="email"
          id="email"
          value="<?php echo e(old('email')); ?>"
          required
          autocomplete="email"
          dir="ltr"
          placeholder="you@example.com"
          class="gl-auth-input has-icon <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> has-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
        >
      </div>
      <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="gl-auth-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="gl-auth-field">
      <label for="password"><?php echo e(__('auth.password')); ?></label>
      <div class="gl-auth-input-wrap">
        <span class="gl-auth-icon" aria-hidden="true"><i class="fas fa-lock"></i></span>
        <input
          :type="showPassword ? 'text' : 'password'"
          name="password"
          id="password"
          required
          autocomplete="new-password"
          placeholder="<?php echo e(__('auth.enter_strong_password')); ?>"
          class="gl-auth-input has-icon <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> has-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
          style="padding-inline-end:3rem"
        >
        <button type="button" class="gl-auth-pw-btn" @click="showPassword = !showPassword">
          <span x-text="showPassword ? '<?php echo e($isRtl ? 'إخفاء' : 'Hide'); ?>' : '<?php echo e($isRtl ? 'إظهار' : 'Show'); ?>'"></span>
        </button>
      </div>
      <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="gl-auth-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="gl-auth-field">
      <label for="password_confirmation"><?php echo e(__('auth.password_confirmation')); ?></label>
      <div class="gl-auth-input-wrap">
        <span class="gl-auth-icon" aria-hidden="true"><i class="fas fa-lock"></i></span>
        <input
          :type="showPasswordConfirm ? 'text' : 'password'"
          name="password_confirmation"
          id="password_confirmation"
          required
          autocomplete="new-password"
          placeholder="<?php echo e(__('auth.reenter_password')); ?>"
          class="gl-auth-input has-icon"
          style="padding-inline-end:3rem"
        >
        <button type="button" class="gl-auth-pw-btn" @click="showPasswordConfirm = !showPasswordConfirm">
          <span x-text="showPasswordConfirm ? '<?php echo e($isRtl ? 'إخفاء' : 'Hide'); ?>' : '<?php echo e($isRtl ? 'إظهار' : 'Show'); ?>'"></span>
        </button>
      </div>
    </div>

    <label class="gl-auth-terms">
      <input type="checkbox" id="terms" required>
      <span>
        <?php echo e(__('auth.agree_terms')); ?>

        <a href="<?php echo e(route('public.terms')); ?>" class="gl-auth-link"><?php echo e(__('auth.terms_of_use')); ?></a>
        <?php echo e(__('auth.and')); ?>

        <a href="<?php echo e(route('public.privacy')); ?>" class="gl-auth-link"><?php echo e(__('auth.privacy_policy')); ?></a>
      </span>
    </label>

    <button type="submit" class="gl-auth-submit">
      <i class="fas fa-user-plus" aria-hidden="true"></i>
      <span><?php echo e(__('auth.create_account_btn')); ?></span>
    </button>
  </form>

  <div class="gl-auth-foot">
    <?php echo e(__('auth.already_have_account')); ?>

    <a href="<?php echo e(route('login')); ?>" class="gl-auth-link"><?php echo e(__('auth.login')); ?></a>
  </div>

  <div class="gl-auth-trust">
    <span><i class="fas fa-clipboard-check"></i> <?php echo e($isRtl ? 'تقييم مستوى مجاني' : 'Free level assessment'); ?></span>
    <span><i class="fas fa-users"></i> <?php echo e($isRtl ? 'جماعي وفردي' : 'Group & 1:1'); ?></span>
    <span><i class="fas fa-user-group"></i> <?php echo e($isRtl ? 'متابعة لولي الأمر' : 'Parent follow-up'); ?></span>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth-landing', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\auth\register.blade.php ENDPATH**/ ?>