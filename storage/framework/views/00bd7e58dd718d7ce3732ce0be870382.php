<?php $__env->startSection('title', __('auth.login')); ?>

<?php $__env->startSection('body_attrs', 'x-data="{ showPassword: false }"'); ?>

<?php $__env->startSection('nav_action'); ?>
  <a href="<?php echo e(route('register')); ?>" class="gl-auth-nav-link"><?php echo e(__('auth.register')); ?></a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php $isRtl = app()->getLocale() === 'ar'; ?>
<div class="gl-auth-card">
  <div class="gl-auth-brand"><?php echo e(config('app.name', 'Glottical')); ?></div>
  <h1 class="gl-auth-title"><?php echo e(__('auth.welcome_back')); ?><br><em><?php echo e($isRtl ? 'إلى Glottical' : 'to Glottical'); ?></em></h1>
  <p class="gl-auth-lead"><?php echo e(__('auth.enter_credentials')); ?></p>

  <form action="<?php echo e(route('login')); ?>" method="POST" novalidate>
    <?php echo csrf_field(); ?>

    <?php if(session('status')): ?>
      <div class="gl-auth-alert gl-auth-alert--ok"><?php echo e(session('status')); ?></div>
    <?php endif; ?>
    <?php if(session('warning')): ?>
      <div class="gl-auth-alert gl-auth-alert--warn"><?php echo e(session('warning')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
      <div class="gl-auth-alert gl-auth-alert--err"><?php echo e($errors->first()); ?></div>
    <?php endif; ?>

    <div class="gl-auth-sr-only" aria-hidden="true">
      <label>website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
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
          autofocus
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
          autocomplete="current-password"
          placeholder="••••••••"
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

    <div class="gl-auth-row">
      <label class="gl-auth-check">
        <input type="checkbox" name="remember">
        <span><?php echo e(__('auth.remember')); ?></span>
      </label>
      <a href="<?php echo e(route('password.request')); ?>" class="gl-auth-link"><?php echo e(__('auth.forgot_password')); ?></a>
    </div>

    <button type="submit" class="gl-auth-submit">
      <span><?php echo e(__('auth.login')); ?></span>
      <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?>" aria-hidden="true"></i>
    </button>
  </form>

  <div class="gl-auth-foot">
    <?php echo e(__('auth.no_account_question')); ?>

    <a href="<?php echo e(route('register')); ?>" class="gl-auth-link"><?php echo e(__('auth.no_account_register_now')); ?></a>
  </div>

  <div class="gl-auth-trust">
    <span><i class="fas fa-video"></i> <?php echo e($isRtl ? 'حصص مباشرة' : 'Live sessions'); ?></span>
    <span><i class="fas fa-shield-halved"></i> <?php echo e($isRtl ? 'دخول آمن' : 'Secure login'); ?></span>
    <span><i class="fas fa-graduation-cap"></i> <?php echo e($isRtl ? 'حتى الشهادة' : 'To certification'); ?></span>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth-landing', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\auth\login.blade.php ENDPATH**/ ?>