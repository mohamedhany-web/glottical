<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $name = $profile->user->name ?? __('public.instructor_fallback');
    $headline = $profile->headline_clean ?: __('public.instructor_fallback');
    $bioClean = $profile->bio_clean;
    $skills = $profile->skills_list ?? [];
    $experiences = $profile->experience_list ?? [];
    $instrPageTitle = $name.' — '.$headline.' | '.config('app.name');
    $instrPageDesc = \Illuminate\Support\Str::limit($bioClean ?: $headline, 160);
    $instrPageImg = ($profile->photo_url ?? null) ?: asset('images/og-image.jpg');
    $instrPageUrl = route('public.instructors.show', $profile->user);
    $consultationOn = isset($consultationSetting) && $consultationSetting->is_active;
    $courses = $courses ?? collect();
    $groupCourses = $groupCourses ?? collect();
    $oneToOneCourses = $oneToOneCourses ?? collect();
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title><?php echo e($instrPageTitle); ?></title>
  <meta name="description" content="<?php echo e($instrPageDesc); ?>">
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">
  <meta name="theme-color" content="#0f5c57">
  <link rel="canonical" href="<?php echo e($instrPageUrl); ?>">
  <link rel="alternate" hreflang="ar" href="<?php echo e($instrPageUrl); ?>?lang=ar">
  <link rel="alternate" hreflang="en" href="<?php echo e($instrPageUrl); ?>?lang=en">
  <meta property="og:type" content="profile">
  <meta property="og:url" content="<?php echo e($instrPageUrl); ?>">
  <meta property="og:title" content="<?php echo e($instrPageTitle); ?>">
  <meta property="og:description" content="<?php echo e($instrPageDesc); ?>">
  <meta property="og:image" content="<?php echo e($instrPageImg); ?>">
  <meta property="og:locale" content="<?php echo e($locale === 'ar' ? 'ar_AR' : 'en_US'); ?>">
  <meta property="og:site_name" content="<?php echo e(config('app.name')); ?>">
  <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('partials.seo-jsonld', ['jsonldType' => 'instructor', 'profile' => $profile], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
  <?php echo $__env->make('partials.atheer-head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<body class="font-sans antialiased">
<?php echo $__env->make('partials.atheer-home-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="page-enter pb-16 md:pb-20">
  <nav class="container-wide py-6 md:py-8" aria-label="<?php echo e($isRtl ? 'مسار التنقل' : 'Breadcrumb'); ?>">
    <ol class="flex flex-wrap items-center gap-2 text-sm text-muted">
      <li><a href="<?php echo e(url('/')); ?>" class="transition hover:text-ink"><?php echo e($isRtl ? 'الرئيسية' : 'Home'); ?></a></li>
      <li aria-hidden="true" class="text-line">/</li>
      <li><a href="<?php echo e(route('public.instructors.index')); ?>" class="transition hover:text-ink"><?php echo e(__('landing.nav.instructors')); ?></a></li>
      <li aria-hidden="true" class="text-line">/</li>
      <li class="font-medium text-ink" aria-current="page"><?php echo e($name); ?></li>
    </ol>
  </nav>

  
  <section class="container-wide grid gap-8 lg:grid-cols-[280px_1fr] lg:items-start lg:gap-12 xl:grid-cols-[300px_1fr]">
    <div class="mx-auto w-full max-w-[240px] sm:max-w-[260px] lg:mx-0 lg:max-w-none">
      <div class="relative aspect-square overflow-hidden rounded-3xl bg-canvas-muted shadow-soft">
        <?php if($profile->photo_url): ?>
          <img src="<?php echo e($profile->photo_url); ?>" alt="<?php echo e($name); ?>" class="h-full w-full object-cover" width="400" height="400">
        <?php else: ?>
          <span class="flex h-full w-full items-center justify-center bg-gradient-to-br from-accent to-ink text-4xl text-white/35">✦</span>
        <?php endif; ?>
        <span class="absolute <?php echo e($isRtl ? 'right-3' : 'left-3'); ?> top-3 rounded-lg bg-surface/95 px-2.5 py-1 text-[11px] font-semibold text-accent shadow-soft backdrop-blur-sm"><?php echo e(__('public.instructors_verified')); ?></span>
      </div>
    </div>

    <div class="space-y-6 lg:sticky lg:top-28 lg:self-start">
      <div class="space-y-3">
        <p class="text-sm font-medium text-accent"><?php echo e(__('landing.nav.instructors')); ?></p>
        <h1 class="text-balance text-3xl font-semibold leading-tight text-ink md:text-4xl"><?php echo e($name); ?></h1>
        <p class="text-lg font-medium text-ink-soft"><?php echo e($headline); ?></p>
        <?php if($bioClean): ?>
          <p class="text-base leading-8 text-muted line-clamp-4"><?php echo e($bioClean); ?></p>
        <?php endif; ?>
      </div>

      <div class="flex flex-wrap gap-2">
        <span class="inline-flex items-center rounded-xl border border-line bg-surface px-3.5 py-2 text-sm font-medium text-ink-soft">
          <?php echo e($courses->count()); ?> <?php echo e($courses->count() === 1 ? __('public.instructors_course_one') : __('public.instructors_course_many')); ?>

        </span>
        <?php if(count($skills) > 0): ?>
          <span class="inline-flex items-center rounded-xl border border-line bg-surface px-3.5 py-2 text-sm font-medium text-ink-soft"><?php echo e(count($skills)); ?> <?php echo e(__('public.skills')); ?></span>
        <?php endif; ?>
        <?php if(count($experiences) > 0): ?>
          <span class="inline-flex items-center rounded-xl border border-line bg-surface px-3.5 py-2 text-sm font-medium text-ink-soft"><?php echo e(count($experiences)); ?> <?php echo e(__('public.experience')); ?></span>
        <?php endif; ?>
      </div>

      <div class="rounded-2xl border border-line bg-surface p-5 shadow-soft space-y-4">
        <div class="flex flex-wrap gap-3">
          <?php if($courses->count() > 0): ?>
            <a href="#instructor-courses" class="btn-press inline-flex h-12 flex-1 items-center justify-center rounded-xl bg-accent px-5 text-sm font-medium text-white transition hover:bg-[#0d4f4a]"><?php echo e(__('public.instructor_courses')); ?></a>
          <?php endif; ?>
          <?php if($consultationOn): ?>
            <?php if(auth()->guard()->check()): ?>
              <?php if(auth()->user()->isStudent()): ?>
                <a href="<?php echo e(route('consultations.create', $profile->user)); ?>" class="btn-press inline-flex h-12 flex-1 items-center justify-center rounded-xl border border-line px-5 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent"><?php echo e(__('public.instructors_consult_cta')); ?></a>
              <?php endif; ?>
            <?php else: ?>
              <a href="<?php echo e(route('login', ['redirect' => route('consultations.create', $profile->user)])); ?>" class="btn-press inline-flex h-12 flex-1 items-center justify-center rounded-xl border border-line px-5 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent"><?php echo e(__('public.instructors_consult_cta')); ?></a>
            <?php endif; ?>
          <?php endif; ?>
        </div>
        <?php if($consultationOn): ?>
          <p class="text-sm text-muted"><?php echo e(__('public.instructors_consult_label')); ?> — <span class="font-semibold text-ink"><?php echo e(number_format($profile->effectiveConsultationPriceEgp(), 0)); ?></span> <?php echo e(__('public.currency_egp')); ?> · <?php echo e($profile->effectiveConsultationDurationMinutes()); ?> <?php echo e($isRtl ? 'دقيقة' : 'min'); ?></p>
        <?php endif; ?>
      </div>

      <?php if(count($skills) > 0): ?>
        <div>
          <p class="mb-3 text-sm font-semibold text-ink"><?php echo e(__('public.skills')); ?></p>
          <div class="flex flex-wrap gap-2">
            <?php $__currentLoopData = $skills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <span class="rounded-xl border border-line bg-canvas px-3 py-1.5 text-sm font-medium text-ink-soft"><?php echo e($skill); ?></span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <section class="container-wide mt-16 md:mt-20 grid gap-10 lg:grid-cols-[1.4fr_0.6fr] lg:gap-12">
    <div class="space-y-10">
      <?php if($bioClean): ?>
        <article class="rounded-2xl border border-line bg-surface p-6 shadow-soft sm:p-8">
          <p class="mb-3 text-sm font-medium text-accent"><?php echo e($isRtl ? 'عن المدرب' : 'About'); ?></p>
          <h2 class="mb-4 text-2xl font-semibold text-ink"><?php echo e(__('public.instructor_bio_title')); ?></h2>
          <div class="whitespace-pre-line text-base leading-8 text-muted"><?php echo e($bioClean); ?></div>
        </article>
      <?php endif; ?>

      <?php if(count($experiences) > 0 || $profile->experience): ?>
        <article class="rounded-2xl border border-line bg-surface p-6 shadow-soft sm:p-8">
          <p class="mb-3 text-sm font-medium text-accent"><?php echo e($isRtl ? 'المسار المهني' : 'Background'); ?></p>
          <h2 class="mb-5 text-2xl font-semibold text-ink"><?php echo e(__('public.experience')); ?></h2>
          <?php if(count($experiences) > 0): ?>
            <ul class="space-y-3">
              <?php $__currentLoopData = $experiences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="flex gap-3 rounded-xl border border-line bg-canvas px-4 py-3 text-sm leading-7 text-ink-soft">
                  <span class="mt-1.5 size-2 shrink-0 rounded-full bg-accent" aria-hidden="true"></span>
                  <span><?php echo e($item); ?></span>
                </li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          <?php else: ?>
            <p class="whitespace-pre-line text-base leading-8 text-muted"><?php echo e($profile->sanitizedText($profile->experience)); ?></p>
          <?php endif; ?>
        </article>
      <?php endif; ?>

      <div id="instructor-courses" class="scroll-mt-28 space-y-10">
        <?php if($oneToOneCourses->isNotEmpty()): ?>
          <section>
            <div class="mb-6 max-w-2xl space-y-2">
              <p class="text-sm font-medium text-accent"><?php echo e($isRtl ? 'تعلّم فردي' : 'Private learning'); ?></p>
              <h2 class="text-2xl font-semibold text-ink md:text-3xl"><?php echo e(__('public.instructor_one_to_one_courses')); ?></h2>
              <p class="text-base leading-8 text-muted"><?php echo e(__('public.instructor_one_to_one_sub')); ?></p>
            </div>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:gap-5">
              <?php $__currentLoopData = $oneToOneCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('partials.landing-course-card-site', [
                  'course' => $course,
                  'badge' => $isRtl ? 'فردي' : '1:1',
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          </section>
        <?php endif; ?>

        <?php if($groupCourses->isNotEmpty()): ?>
          <section>
            <div class="mb-6 max-w-2xl space-y-2">
              <p class="text-sm font-medium text-accent"><?php echo e($isRtl ? 'كورسات جماعية' : 'Group courses'); ?></p>
              <h2 class="text-2xl font-semibold text-ink md:text-3xl"><?php echo e(__('public.instructor_courses')); ?></h2>
            </div>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:gap-5">
              <?php $__currentLoopData = $groupCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('partials.landing-course-card-site', ['course' => $course], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          </section>
        <?php elseif($courses->isEmpty()): ?>
          <div class="rounded-2xl border border-line bg-surface p-10 text-center shadow-soft">
            <p class="text-base text-muted"><?php echo e(__('public.instructor_no_courses_yet')); ?></p>
            <a href="<?php echo e(route('public.courses')); ?>" class="btn-press mt-4 inline-flex h-11 items-center rounded-xl bg-accent px-5 text-sm font-medium text-white transition hover:bg-[#0d4f4a]"><?php echo e(__('landing.view_all_courses')); ?></a>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <aside class="space-y-5 lg:sticky lg:top-28 lg:self-start">
      <div class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
        <h3 class="mb-4 text-base font-semibold text-ink"><?php echo e(__('public.instructor_quick_info')); ?></h3>
        <dl class="space-y-3 text-sm">
          <div class="flex items-center justify-between gap-3 rounded-xl bg-canvas px-3 py-2.5">
            <dt class="text-muted"><?php echo e(__('public.instructors_course_many')); ?></dt>
            <dd class="font-semibold text-ink"><?php echo e($courses->count()); ?></dd>
          </div>
          <div class="flex items-center justify-between gap-3 rounded-xl bg-canvas px-3 py-2.5">
            <dt class="text-muted"><?php echo e(__('public.skills')); ?></dt>
            <dd class="font-semibold text-ink"><?php echo e(count($skills)); ?></dd>
          </div>
          <div class="flex items-center justify-between gap-3 rounded-xl bg-canvas px-3 py-2.5">
            <dt class="text-muted"><?php echo e(__('public.instructor_status_label')); ?></dt>
            <dd class="font-semibold text-success"><?php echo e(__('public.instructors_verified')); ?></dd>
          </div>
        </dl>
        <div class="mt-4 space-y-2">
          <a href="<?php echo e(route('public.courses')); ?>" class="btn-press inline-flex h-11 w-full items-center justify-center rounded-xl bg-accent text-sm font-medium text-white transition hover:bg-[#0d4f4a]"><?php echo e(__('landing.view_all_courses')); ?></a>
          <?php if($oneToOneCourses->isNotEmpty()): ?>
            <a href="<?php echo e(route('public.courses', ['delivery' => 'one_to_one'])); ?>" class="inline-flex h-11 w-full items-center justify-center rounded-xl border border-line text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent"><?php echo e(__('public.nav_one_to_one_courses') ?? ($isRtl ? 'دروس فردية' : '1:1 courses')); ?></a>
          <?php endif; ?>
          <a href="<?php echo e(route('public.instructors.index')); ?>" class="inline-flex h-11 w-full items-center justify-center rounded-xl border border-line text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent"><?php echo e(__('public.all_instructors_link') ?? ($isRtl ? 'كل المدربين' : 'All instructors')); ?></a>
        </div>
      </div>
    </aside>
  </section>

  <section class="container-wide mt-16 md:mt-20">
    <div class="relative overflow-hidden rounded-3xl bg-ink px-6 py-10 text-white shadow-soft sm:px-10 md:py-12">
      <div class="pointer-events-none absolute -top-20 <?php echo e($isRtl ? '-left-16' : '-right-16'); ?> size-56 rounded-full bg-accent/25 blur-3xl"></div>
      <div class="pointer-events-none absolute -bottom-24 <?php echo e($isRtl ? '-right-10' : '-left-10'); ?> size-48 rounded-full bg-metal/20 blur-3xl"></div>
      <div class="relative flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <div class="max-w-xl space-y-3">
          <p class="text-sm font-medium text-metal"><?php echo e($isRtl ? 'جاهز للبدء؟' : 'Ready to start?'); ?></p>
          <h2 class="text-balance text-2xl font-semibold md:text-3xl"><?php echo e($isRtl ? 'احجز تقييم مستوى مجاني أو تصفّح كورسات '. $name : 'Book a free assessment or browse courses by '.$name); ?></h2>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row md:shrink-0">
          <a href="<?php echo e(url('/?open_trial=1')); ?>" class="btn-press inline-flex h-12 items-center justify-center rounded-xl bg-accent px-6 text-sm font-medium text-white transition hover:bg-[#0d4f4a]"><?php echo e(__('landing.academy.free_trial_cta')); ?></a>
          <a href="<?php echo e(route('register')); ?>" class="btn-press inline-flex h-12 items-center justify-center rounded-xl border border-white/20 bg-white/5 px-6 text-sm font-medium transition hover:bg-white/10"><?php echo e(__('landing.nav.register') ?? ($isRtl ? 'إنشاء حساب' : 'Sign up')); ?></a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php echo $__env->make('partials.atheer-home-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
  document.querySelectorAll('[data-open-free-trial]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      window.location.href = <?php echo e(\Illuminate\Support\Js::from(url('/?open_trial=1'))); ?>;
    });
  });
</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\instructors\show.blade.php ENDPATH**/ ?>