<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $profiles = $profiles ?? collect();
    $featuredCourses = $featuredCourses ?? collect();
    $consultationOn = isset($consultationSetting) && $consultationSetting->is_active;
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title><?php echo e(__('public.instructors_page_title')); ?> — Glottical</title>
  <meta name="description" content="<?php echo e(__('public.instructors_subtitle')); ?>">
  <link rel="canonical" href="<?php echo e(route('public.instructors.index')); ?>">
  <link rel="alternate" hreflang="ar" href="<?php echo e(url('/instructors')); ?>?lang=ar">
  <link rel="alternate" hreflang="en" href="<?php echo e(url('/instructors')); ?>?lang=en">
  <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('partials.seo-jsonld', ['jsonldType' => 'website'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
  <?php echo $__env->make('partials.atheer-head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <meta name="theme-color" content="#0f5c57">
</head>
<body class="font-sans antialiased">
<?php echo $__env->make('partials.atheer-home-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="page-enter">
  <section class="container-wide py-10 md:py-14">
    <nav class="mb-6" aria-label="<?php echo e($isRtl ? 'مسار التنقل' : 'Breadcrumb'); ?>">
      <ol class="flex flex-wrap items-center gap-2 text-sm text-muted">
        <li><a href="<?php echo e(url('/')); ?>" class="transition hover:text-ink"><?php echo e($isRtl ? 'الرئيسية' : 'Home'); ?></a></li>
        <li aria-hidden="true" class="text-line">/</li>
        <li class="font-medium text-ink" aria-current="page"><?php echo e(__('landing.nav.instructors')); ?></li>
      </ol>
    </nav>
    <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
      <div class="max-w-2xl space-y-3">
        <p class="text-sm font-medium text-accent"><?php echo e($isRtl ? 'مدربون موثوقون' : 'Trusted instructors'); ?></p>
        <h1 class="text-balance text-3xl font-semibold tracking-tight text-ink md:text-4xl"><?php echo e(__('public.instructors_page_title')); ?></h1>
        <p class="text-base leading-8 text-muted"><?php echo e(__('public.instructors_subtitle')); ?></p>
      </div>
      <div class="flex flex-wrap gap-6 text-sm text-muted">
        <p><span class="font-semibold text-ink"><?php echo e(number_format($profiles->count())); ?></span> <?php echo e($isRtl ? 'مدرباً' : 'instructors'); ?></p>
        <p><span class="font-semibold text-ink"><?php echo e(number_format((int) $profiles->sum('courses_count'))); ?></span> <?php echo e($isRtl ? 'كورساً' : 'courses'); ?></p>
      </div>
    </div>
  </section>

  
  <?php if($profiles->isNotEmpty()): ?>
  <section class="container-wide pb-10 md:pb-12">
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-8">
      <?php $__currentLoopData = $profiles->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('public.instructors.show', $p->user)); ?>" class="group flex h-28 flex-col items-center justify-center gap-2 rounded-2xl border border-line bg-surface px-2 text-center shadow-soft transition hover:border-accent/30 hover:bg-accent-soft hover:shadow-lift card-lift">
          <span class="text-sm font-bold text-ink-soft transition group-hover:text-accent line-clamp-2"><?php echo e(\Illuminate\Support\Str::limit($p->user->name ?? '—', 22)); ?></span>
          <span class="text-xs text-muted line-clamp-1"><?php echo e(\Illuminate\Support\Str::limit($p->headline ?? __('public.instructor_fallback'), 24)); ?></span>
        </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </section>
  <?php endif; ?>

  <section class="bg-surface py-16 md:py-20">
    <div class="container-wide">
      <div class="mb-8 max-w-2xl space-y-3 md:mb-10">
        <p class="text-sm font-medium text-accent"><?php echo e($isRtl ? 'ملفات المدربين' : 'Instructor profiles'); ?></p>
        <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl"><?php echo e($isRtl ? 'تعرّف على من يعلّمك' : 'Meet who will teach you'); ?></h2>
        <p class="text-base leading-8 text-muted"><?php echo e($isRtl ? 'كل مدرب بهوية واضحة — خبرة، تخصصات، وكورسات يمكنك البدء بها فوراً.' : 'Each instructor has a clear identity — expertise, skills, and courses you can start now.'); ?></p>
      </div>

      <?php if($profiles->isNotEmpty()): ?>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          <?php $__currentLoopData = $profiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $url = route('public.instructors.show', $p->user);
              $skills = is_array($p->skills_list ?? null) ? array_slice($p->skills_list, 0, 3) : [];
            ?>
            <article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-line bg-canvas shadow-soft card-lift">
              <a href="<?php echo e($url); ?>" class="relative block aspect-[4/3] overflow-hidden bg-canvas-muted">
                <?php if($p->photo_url): ?>
                  <img src="<?php echo e($p->photo_url); ?>" alt="<?php echo e($p->user->name); ?>" class="img-zoom absolute inset-0 h-full w-full object-cover" loading="lazy" width="640" height="480">
                <?php else: ?>
                  <span class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-accent to-ink text-4xl text-white/35">✦</span>
                <?php endif; ?>
                <div class="absolute inset-0 bg-gradient-to-t from-ink/70 via-transparent to-transparent"></div>
                <div class="absolute inset-x-3 top-3 flex items-start justify-between gap-2">
                  <?php if(($p->courses_count ?? 0) > 0): ?>
                    <span class="inline-flex items-center rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent"><?php echo e($p->courses_count); ?> <?php echo e($isRtl ? 'كورس' : 'courses'); ?></span>
                  <?php else: ?>
                    <span></span>
                  <?php endif; ?>
                  <?php if(! empty($p->marketing_featured_today)): ?>
                    <span class="inline-flex items-center rounded-lg bg-[#f4eadc] px-2.5 py-1 text-xs font-medium text-[#7a5c2e]"><?php echo e(__('public.instructors_featured_badge')); ?></span>
                  <?php endif; ?>
                </div>
              </a>
              <div class="flex flex-1 flex-col gap-3 p-5">
                <div>
                  <h3 class="text-lg font-semibold text-ink transition group-hover:text-accent">
                    <a href="<?php echo e($url); ?>"><?php echo e($p->user->name); ?></a>
                  </h3>
                  <p class="mt-1 text-sm font-medium text-accent"><?php echo e($p->headline ?? __('public.instructor_fallback')); ?></p>
                </div>
                <?php if($skills !== []): ?>
                  <div class="flex flex-wrap gap-1.5">
                    <?php $__currentLoopData = $skills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <span class="rounded-lg border border-line bg-surface px-2.5 py-1 text-[11px] font-medium text-ink-soft"><?php echo e($skill); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </div>
                <?php endif; ?>
                <?php if($p->bio): ?>
                  <p class="line-clamp-2 text-sm leading-6 text-muted"><?php echo e($p->bio); ?></p>
                <?php endif; ?>
                <div class="mt-auto flex flex-wrap items-center gap-2 border-t border-line pt-4">
                  <a href="<?php echo e($url); ?>" class="btn-press inline-flex h-10 flex-1 items-center justify-center rounded-xl bg-accent px-4 text-sm font-medium text-white transition hover:bg-[#0d4f4a]"><?php echo e(__('public.view_instructor_profile')); ?></a>
                  <?php if($consultationOn): ?>
                    <?php if(auth()->guard()->check()): ?>
                      <?php if(auth()->user()->isStudent()): ?>
                        <a href="<?php echo e(route('consultations.create', $p->user)); ?>" class="inline-flex h-10 items-center justify-center rounded-xl border border-line bg-surface px-3 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent"><?php echo e(__('public.instructors_consult_cta')); ?></a>
                      <?php endif; ?>
                    <?php else: ?>
                      <a href="<?php echo e(route('login', ['redirect' => route('consultations.create', $p->user)])); ?>" class="inline-flex h-10 items-center justify-center rounded-xl border border-line bg-surface px-3 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent"><?php echo e(__('public.instructors_consult_cta')); ?></a>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
                <?php if($consultationOn): ?>
                  <p class="text-xs text-muted"><?php echo e(__('public.instructors_consult_label')); ?> — <span class="font-semibold text-ink"><?php echo e(number_format($p->effectiveConsultationPriceEgp(), 0)); ?></span> <?php echo e($isRtl ? 'ج.م' : 'EGP'); ?></p>
                <?php endif; ?>
              </div>
            </article>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      <?php else: ?>
        <div class="rounded-2xl border border-line bg-canvas p-10 text-center shadow-soft">
          <p class="text-base text-muted"><?php echo e($isRtl ? 'لا يوجد مدربون معتمدون للعرض حالياً.' : 'No approved instructors to show yet.'); ?></p>
          <a href="<?php echo e(route('public.courses')); ?>" class="btn-press mt-4 inline-flex h-11 items-center rounded-xl bg-accent px-5 text-sm font-medium text-white transition hover:bg-[#0d4f4a]"><?php echo e(__('landing.view_all_courses')); ?></a>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <?php if($featuredCourses->isNotEmpty()): ?>
  <section class="container-wide py-16 md:py-20">
    <div class="mb-8 flex flex-col gap-4 md:mb-10 md:flex-row md:items-end md:justify-between">
      <div class="max-w-2xl space-y-3">
        <p class="text-sm font-medium text-accent"><?php echo e($isRtl ? 'من المدربين' : 'From instructors'); ?></p>
        <h2 class="text-balance text-2xl font-semibold tracking-tight text-ink md:text-3xl"><?php echo e($isRtl ? 'كورسات مختارة من مدرّبينا' : 'Courses from our instructors'); ?></h2>
        <p class="text-base leading-8 text-muted"><?php echo e($isRtl ? 'ابدأ من كورس واضح بمعايير Glottical — تقييم وسعر وتسجيل سريع.' : 'Start with a clear course — rating, price, and quick enroll.'); ?></p>
      </div>
      <a href="<?php echo e(route('public.courses')); ?>" class="inline-flex h-10 shrink-0 items-center rounded-xl border border-line px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent"><?php echo e(__('landing.view_all_courses')); ?></a>
    </div>
    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 lg:gap-5">
      <?php $__currentLoopData = $featuredCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('partials.landing-course-card-site', [
          'course' => $course,
          'badge' => $i === 0 ? (__('landing.featured_badge') ?? ($isRtl ? 'مميّز' : 'Featured')) : null,
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </section>
  <?php endif; ?>

  <section class="container-wide pb-16 md:pb-20">
    <div class="relative overflow-hidden rounded-3xl bg-ink px-6 py-10 text-white shadow-soft sm:px-10 md:py-12">
      <div class="pointer-events-none absolute -top-20 <?php echo e($isRtl ? '-left-16' : '-right-16'); ?> size-56 rounded-full bg-accent/25 blur-3xl"></div>
      <div class="pointer-events-none absolute -bottom-24 <?php echo e($isRtl ? '-right-10' : '-left-10'); ?> size-48 rounded-full bg-metal/20 blur-3xl"></div>
      <div class="relative flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <div class="max-w-xl space-y-3">
          <p class="text-sm font-medium text-metal"><?php echo e($isRtl ? 'هل تحتاج توجيهاً شخصياً؟' : 'Need personal guidance?'); ?></p>
          <h2 class="text-balance text-2xl font-semibold md:text-3xl"><?php echo e($isRtl ? 'احجز تقييم مستوى مجاني وسنربطك بالمدرب الأنسب' : 'Book a free assessment — we’ll match you with the right instructor'); ?></h2>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row md:shrink-0">
          <a href="<?php echo e(url('/?open_trial=1')); ?>" class="btn-press inline-flex h-12 items-center justify-center rounded-xl bg-accent px-6 text-sm font-medium text-white transition hover:bg-[#0d4f4a]"><?php echo e(__('landing.academy.free_trial_cta')); ?></a>
          <a href="<?php echo e(route('public.courses')); ?>" class="btn-press inline-flex h-12 items-center justify-center rounded-xl border border-white/20 bg-white/5 px-6 text-sm font-medium transition hover:bg-white/10"><?php echo e(__('landing.nav.courses')); ?></a>
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
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\instructors\index.blade.php ENDPATH**/ ?>