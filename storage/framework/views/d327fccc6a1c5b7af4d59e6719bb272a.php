<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $brand = config('app.name', 'Glottical');
    $footer = \App\Services\PublicFooterSettings::payload();
    $waUrl = $footer['whatsapp_url'] ?? '#';
    $heroPath = public_path('img/glottical/hero-Photoroom.png');
    $heroImg = file_exists($heroPath)
        ? asset('img/glottical/hero-Photoroom.png').'?v='.filemtime($heroPath)
        : asset('img/glottical/hero.png');
    $stats = $stats ?? ['courses' => 0, 'students' => 0, 'instructors' => 0];
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title><?php echo e(__('public.about_page_title')); ?> — <?php echo e($brand); ?></title>
  <meta name="description" content="<?php echo e(__('public.about_intro')); ?>">
  <meta name="theme-color" content="#0B3D91">
  <link rel="canonical" href="<?php echo e(route('public.about')); ?>">
  <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('partials.landing.head', ['landingCss' => ['theme', 'courses-catalog', 'about']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <style>
    .gl-about-page .sana-ab-hero {
      padding: clamp(36px, 6vw, 64px) 0 clamp(40px, 7vw, 72px);
    }
    .gl-about-page .sana-ab-hero__title {
      font-size: clamp(1.55rem, 3.8vw, 2.35rem);
      margin-bottom: 10px;
    }
    .gl-about-page .sana-ab-hero__mission {
      font-size: clamp(.92rem, 2vw, 1.05rem);
      font-weight: 700;
      margin-bottom: 10px;
    }
    .gl-about-page .sana-ab-hero__sub {
      font-size: .88rem;
      margin-bottom: 1.15rem;
    }
    .gl-about-page .sana-section { padding: clamp(36px, 5.5vw, 56px) 0; }
    .gl-about-page .sana-head { margin-bottom: 1.5rem !important; }
    .gl-about-page .sana-head__title { font-size: clamp(1.25rem, 2.6vw, 1.65rem); }
    .gl-about-page .sana-ab-story { gap: 1.5rem; }
    @media (min-width: 992px) {
      .gl-about-page .sana-ab-story { gap: 2rem; }
    }
    .gl-about-page .sana-ab-story__intro p { font-size: .86rem; margin-bottom: .85rem; }
    .gl-about-page .sana-ab-story__block { padding: .9rem 1rem; border-radius: 14px; }
    .gl-about-page .sana-ab-story__block h4 { font-size: .78rem; }
    .gl-about-page .sana-ab-story__block p { font-size: .8rem; }
    .gl-about-page .sana-ab-timeline__item { padding-bottom: 1.1rem; }
    .gl-about-page .sana-ab-timeline__item strong { font-size: .88rem; }
    .gl-about-page .sana-ab-timeline__item p { font-size: .76rem; }
    .gl-about-page .sana-ab-pillar { padding: 1.05rem .85rem; border-radius: 16px; }
    .gl-about-page .sana-ab-pillar__icon { width: 42px; height: 42px; border-radius: 12px; font-size: 1rem; margin-bottom: .65rem; }
    .gl-about-page .sana-ab-pillar strong { font-size: .8rem; }
    .gl-about-page .sana-ab-pillar span { font-size: .72rem; }
    .gl-about-page .sana-ab-vision__card { padding: 1rem; border-radius: 14px; gap: .75rem; }
    .gl-about-page .sana-ab-vision__icon { width: 40px; height: 40px; min-width: 40px; border-radius: 11px; font-size: .95rem; }
    .gl-about-page .sana-ab-vision__card strong { font-size: .84rem; }
    .gl-about-page .sana-ab-vision__card p { font-size: .74rem; }
    .gl-about-page .sana-ab-why__card { padding: 1.05rem .95rem; border-radius: 16px; }
    .gl-about-page .sana-ab-why__icon { width: 42px; height: 42px; border-radius: 12px; margin-bottom: .65rem; font-size: 1rem; }
    .gl-about-page .sana-ab-why__card strong { font-size: .86rem; }
    .gl-about-page .sana-ab-why__card p { font-size: .76rem; }
    .gl-about-page .sana-ab-metrics { grid-template-columns: repeat(3, 1fr); }
    @media (max-width: 575px) { .gl-about-page .sana-ab-metrics { grid-template-columns: 1fr; } }
    .gl-about-page .sana-ab-metric { padding: 1.1rem .75rem; border-radius: 16px; }
    .gl-about-page .sana-ab-metric strong { font-size: 1.35rem; }
    .gl-about-page .sana-ab-metric span { font-size: .72rem; }
    .gl-about-page .sana-ab-values { gap: .75rem; }
    .gl-about-page .sana-ab-value { padding: .95rem .85rem; border-radius: 14px; }
    .gl-about-page .sana-ab-hero-photo {
      position: relative; z-index: 2; width: min(100%, 340px); margin-inline: auto;
      filter: drop-shadow(0 18px 36px rgba(5,31,77,.35));
      animation: sanaAbSceneFloat 5s ease-in-out infinite;
    }
    .gl-about-page .sana-ab-hero-photo img {
      width: 100%; height: auto; display: block; object-fit: contain;
    }
    .gl-about-page .sana-ab-hero__actions { gap: .65rem; }
    .gl-about-page .sana-ab-hero__actions .sana-btn {
      padding: .7rem 1.15rem; font-size: .84rem;
    }
    .gl-about-page .sana-ab-final__box {
      padding: clamp(1.35rem, 3vw, 1.85rem);
      border-radius: 18px;
    }
    .gl-about-page .sana-ab-final__box h2 {
      font-size: clamp(1.15rem, 2.4vw, 1.45rem);
      margin-bottom: .4rem;
    }
    .gl-about-page .sana-ab-final__box > p {
      font-size: .86rem;
      margin-bottom: 1rem;
    }
  </style>
</head>
<body class="sana-home sana-courses-page gl-about-page">
<div id="sana-scroll-progress"></div>
<?php echo $__env->make('partials.landing.navbar', ['navActive' => 'about', 'navHero' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<main class="sana-about-page">

  <section class="sana-ab-hero">
    <div class="sana-container">
      <div class="sana-ab-hero__grid sana-reveal">
        <div class="sana-ab-hero__content">
          <span class="sana-ab-hero__eyebrow"><i class="fas fa-heart"></i> <?php echo e($isRtl ? 'قصة '.$brand : $brand.' story'); ?></span>
          <h1 class="sana-ab-hero__title">
            <?php echo e($brand); ?>

            <span class="hl"><?php echo e(__('public.about_hero_sub')); ?></span>
          </h1>
          <p class="sana-ab-hero__mission"><?php echo e(__('public.about_intro')); ?></p>
          <p class="sana-ab-hero__sub"><?php echo e(__('public.about_para2')); ?></p>
          <div class="sana-ab-hero__actions">
            <div class="sana-site-cta sana-site-cta--hero">
              <a href="<?php echo e(route('home')); ?>?open_trial=1" class="sana-btn sana-btn--yellow"><i class="fas fa-clipboard-check"></i> <?php echo e(__('landing.academy.free_trial_cta')); ?></a>
              <a href="<?php echo e($waUrl); ?>" class="sana-btn sana-btn--wa" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> <?php echo e($isRtl ? 'واتساب' : 'WhatsApp'); ?></a>
            </div>
            <a href="#story" class="sana-btn sana-btn--white-outline"><i class="fas fa-book-open"></i> <?php echo e($isRtl ? 'اكتشف قصتنا' : 'Discover our story'); ?></a>
          </div>
        </div>
        <div class="sana-ab-hero__visual">
          <div class="sana-ab-scene" aria-hidden="true">
            <div class="sana-ab-scene__deco">
              <span class="sana-ab-scene__glow"></span>
              <span class="sana-ab-scene__ring sana-ab-scene__ring--1"></span>
              <span class="sana-ab-scene__ring sana-ab-scene__ring--2"></span>
              <span class="sana-ab-scene__spark sana-ab-scene__spark--1">✦</span>
              <span class="sana-ab-scene__spark sana-ab-scene__spark--2">✦</span>
            </div>
            <div class="sana-ab-hero-photo">
              <img src="<?php echo e($heroImg); ?>" alt="<?php echo e($brand); ?>" width="420" height="480" loading="eager" decoding="async">
            </div>
            <div class="sana-ab-scene__chip sana-ab-scene__chip--1"><i class="fas fa-video"></i><span><?php echo e($isRtl ? 'حصص مباشرة' : 'Live sessions'); ?></span></div>
            <div class="sana-ab-scene__chip sana-ab-scene__chip--2"><i class="fas fa-layer-group"></i><span><?php echo e($isRtl ? 'مستويات واضحة' : 'Clear levels'); ?></span></div>
            <div class="sana-ab-scene__chip sana-ab-scene__chip--3"><i class="fas fa-user-group"></i><span><?php echo e($isRtl ? 'متابعة الأهل' : 'Parent follow-up'); ?></span></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="sana-section" id="story">
    <div class="sana-container">
      <div class="sana-head sana-head--center sana-reveal">
        <span class="sana-head__eyebrow"><?php echo e(__('public.about_heading')); ?></span>
        <h2 class="sana-head__title"><?php echo e($isRtl ? 'لماذا وُلدت' : 'Why'); ?> <span class="hl"><?php echo e($brand); ?></span><?php echo e($isRtl ? '؟' : '?'); ?></h2>
        <span class="sana-head__line"></span>
      </div>
      <div class="sana-ab-story">
        <div class="sana-ab-story__intro sana-reveal">
          <p><?php echo __('public.about_para1', ['brand' => '<strong>'.e($brand).'</strong>']); ?></p>
          <p><?php echo e(__('public.about_para2')); ?></p>
          <div class="sana-ab-story__blocks">
            <div class="sana-ab-story__block">
              <h4><i class="fas fa-circle-exclamation"></i> <?php echo e(__('public.about_challenge_title')); ?></h4>
              <p><?php echo e(__('public.about_challenge_desc')); ?></p>
            </div>
            <div class="sana-ab-story__block">
              <h4><i class="fas fa-lightbulb"></i> <?php echo e(__('public.about_vision_block_title')); ?></h4>
              <p><?php echo e(__('public.about_vision_block_desc')); ?></p>
            </div>
            <div class="sana-ab-story__block">
              <h4><i class="fas fa-chart-line"></i> <?php echo e(__('public.about_now_title')); ?></h4>
              <p><?php echo e(__('public.about_now_desc')); ?></p>
            </div>
          </div>
        </div>
        <div class="sana-ab-timeline sana-reveal">
          <div class="sana-ab-timeline__item">
            <span class="sana-ab-timeline__dot"></span>
            <span class="sana-ab-timeline__year"><?php echo e($isRtl ? 'الفكرة' : 'Idea'); ?></span>
            <strong><?php echo e($isRtl ? 'سؤال واحد' : 'One question'); ?></strong>
            <p><?php echo e($isRtl ? 'كيف نجعل تعلّم اللغة تجربة تُعاش — لا مجرد حفظ؟' : 'How do we make language learning lived — not just memorized?'); ?></p>
          </div>
          <div class="sana-ab-timeline__item">
            <span class="sana-ab-timeline__dot"></span>
            <span class="sana-ab-timeline__year"><?php echo e($isRtl ? 'البناء' : 'Build'); ?></span>
            <strong><?php echo e($isRtl ? 'تجربة متكاملة' : 'A complete experience'); ?></strong>
            <p><?php echo e($isRtl ? 'حصص مباشرة، متابعة لولي الأمر، وشهادات — في نظام واحد.' : 'Live sessions, parent follow-up, and certificates — in one system.'); ?></p>
          </div>
          <div class="sana-ab-timeline__item">
            <span class="sana-ab-timeline__dot"></span>
            <span class="sana-ab-timeline__year"><?php echo e($isRtl ? 'الآن' : 'Now'); ?></span>
            <strong><?php echo e($isRtl ? 'تعلّم حيّ' : 'Live learning'); ?></strong>
            <p><?php echo e($isRtl ? 'مجالان رئيسيان وتقييم مجاني — جاهز للانطلاق مع العائلة.' : 'Two main areas and a free assessment — ready for families to start.'); ?></p>
          </div>
          <div class="sana-ab-timeline__item">
            <span class="sana-ab-timeline__dot"></span>
            <span class="sana-ab-timeline__year"><?php echo e($isRtl ? 'التالي' : 'Next'); ?></span>
            <strong><?php echo e($isRtl ? 'نمو تدريجي' : 'Steady growth'); ?></strong>
            <p><?php echo e($isRtl ? 'نوسّع المحتوى والجداول بشفافية — حسب جاهزية الجودة.' : 'We expand content and schedules transparently — as quality is ready.'); ?></p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="sana-section sana-section--soft">
    <div class="sana-container">
      <div class="sana-head sana-head--center sana-reveal">
        <span class="sana-head__eyebrow"><?php echo e(__('public.our_mission')); ?></span>
        <h2 class="sana-head__title"><?php echo e($isRtl ? 'ما' : 'What we'); ?> <span class="hl"><?php echo e($isRtl ? 'نؤمن به' : 'believe'); ?></span></h2>
        <span class="sana-head__line"></span>
        <p class="sana-head__sub"><?php echo e(__('public.mission_intro')); ?></p>
      </div>
      <div class="sana-ab-pillars">
        <div class="sana-ab-pillar sana-reveal">
          <div class="sana-ab-pillar__icon"><i class="fas fa-video"></i></div>
          <strong><?php echo e(__('public.mission_1')); ?></strong>
          <span><?php echo e($isRtl ? 'المعلّم حاضر — والطالب يتدرّب فعلاً.' : 'The tutor is present — the student actually practices.'); ?></span>
        </div>
        <div class="sana-ab-pillar sana-reveal">
          <div class="sana-ab-pillar__icon"><i class="fas fa-route"></i></div>
          <strong><?php echo e(__('public.mission_2')); ?></strong>
          <span><?php echo e($isRtl ? 'خطوات واضحة بدل التشتّت بين المحتوى.' : 'Clear steps instead of content chaos.'); ?></span>
        </div>
        <div class="sana-ab-pillar sana-reveal">
          <div class="sana-ab-pillar__icon"><i class="fas fa-user-group"></i></div>
          <strong><?php echo e(__('public.mission_3')); ?></strong>
          <span><?php echo e($isRtl ? 'العائلة ترى الحضور والتقدّم بثقة.' : 'Families see attendance and progress with confidence.'); ?></span>
        </div>
        <div class="sana-ab-pillar sana-reveal">
          <div class="sana-ab-pillar__icon"><i class="fas fa-certificate"></i></div>
          <strong><?php echo e($isRtl ? 'شهادة عند الإتمام' : 'Certificate on completion'); ?></strong>
          <span><?php echo e($isRtl ? 'إثبات إنجاز واضح بعد إتمام متطلبات الكورس.' : 'Clear proof of achievement after course requirements.'); ?></span>
        </div>
      </div>
    </div>
  </section>

  <section class="sana-section">
    <div class="sana-container">
      <div class="sana-head sana-head--center sana-reveal">
        <span class="sana-head__eyebrow"><?php echo e(__('public.our_vision')); ?></span>
        <h2 class="sana-head__title"><?php echo e($isRtl ? 'المستقبل الذي' : 'The future we'); ?> <span class="hl"><?php echo e($isRtl ? 'نبنيه' : 'build'); ?></span></h2>
        <span class="sana-head__line"></span>
        <p class="sana-head__sub"><?php echo e(__('public.vision_text')); ?></p>
      </div>
      <div class="sana-ab-vision">
        <div class="sana-ab-vision__card sana-reveal">
          <span class="sana-ab-vision__icon"><i class="fas fa-comments"></i></span>
          <div>
            <strong><?php echo e($isRtl ? 'من الحفظ إلى الاستخدام' : 'From memorizing to using'); ?></strong>
            <p><?php echo e($isRtl ? 'حوار، ممارسة، وثقة في المواقف الحقيقية.' : 'Dialogue, practice, and confidence in real situations.'); ?></p>
          </div>
        </div>
        <div class="sana-ab-vision__card sana-reveal">
          <span class="sana-ab-vision__icon"><i class="fas fa-house-user"></i></span>
          <div>
            <strong><?php echo e($isRtl ? 'عائلة مشاركة' : 'An involved family'); ?></strong>
            <p><?php echo e($isRtl ? 'وليّ الأمر شريك في الرحلة — لا متفرّج قلق.' : 'Parents as partners — not anxious spectators.'); ?></p>
          </div>
        </div>
        <div class="sana-ab-vision__card sana-reveal">
          <span class="sana-ab-vision__icon"><i class="fas fa-graduation-cap"></i></span>
          <div>
            <strong><?php echo e($isRtl ? 'نجاح قابل للقياس' : 'Measurable success'); ?></strong>
            <p><?php echo e($isRtl ? 'تقدّم واضح، حضور، وشهادة عند الإتمام.' : 'Clear progress, attendance, and a certificate on completion.'); ?></p>
          </div>
        </div>
        <div class="sana-ab-vision__card sana-reveal">
          <span class="sana-ab-vision__icon"><i class="fas fa-globe"></i></span>
          <div>
            <strong><?php echo e($isRtl ? 'عربي وإنجليزي على منصة واحدة' : 'Arabic & English on one platform'); ?></strong>
            <p><?php echo e($isRtl ? 'مجالان رئيسيان بجودة واحدة وتجربة موحّدة.' : 'Two main areas with one quality bar and shared experience.'); ?></p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="sana-section sana-section--soft">
    <div class="sana-container">
      <div class="sana-head sana-head--center sana-reveal">
        <span class="sana-head__eyebrow"><?php echo e(__('public.why_platform')); ?></span>
        <h2 class="sana-head__title"><?php echo e($isRtl ? 'لماذا يختارنا' : 'Why families choose'); ?> <span class="hl"><?php echo e($isRtl ? 'الطلاب وأولياء الأمور' : 'us'); ?></span></h2>
        <span class="sana-head__line"></span>
        <p class="sana-head__sub"><?php echo e($isRtl ? 'مزايا حقيقية — لا شعارات فارغة.' : 'Real advantages — not empty slogans.'); ?></p>
      </div>
      <div class="sana-ab-why">
        <div class="sana-ab-why__card sana-reveal">
          <div class="sana-ab-why__icon"><i class="fas fa-video"></i></div>
          <strong><?php echo e(__('public.why_1_title')); ?></strong>
          <p><?php echo e(__('public.why_1_desc')); ?></p>
        </div>
        <div class="sana-ab-why__card sana-reveal">
          <div class="sana-ab-why__icon"><i class="fas fa-route"></i></div>
          <strong><?php echo e(__('public.why_2_title')); ?></strong>
          <p><?php echo e(__('public.why_2_desc')); ?></p>
        </div>
        <div class="sana-ab-why__card sana-reveal">
          <div class="sana-ab-why__icon"><i class="fas fa-chart-line"></i></div>
          <strong><?php echo e(__('public.why_3_title')); ?></strong>
          <p><?php echo e(__('public.why_3_desc')); ?></p>
        </div>
        <div class="sana-ab-why__card sana-reveal">
          <div class="sana-ab-why__icon"><i class="fas fa-certificate"></i></div>
          <strong><?php echo e(__('public.why_4_title')); ?></strong>
          <p><?php echo e(__('public.why_4_desc')); ?></p>
        </div>
        <div class="sana-ab-why__card sana-reveal">
          <div class="sana-ab-why__icon"><i class="fas fa-chalkboard-user"></i></div>
          <strong><?php echo e(__('public.value_3_title')); ?></strong>
          <p><?php echo e(__('public.value_3_desc')); ?></p>
        </div>
        <div class="sana-ab-why__card sana-reveal">
          <div class="sana-ab-why__icon"><i class="fas fa-comments"></i></div>
          <strong><?php echo e(__('public.value_1_title')); ?></strong>
          <p><?php echo e(__('public.value_1_desc')); ?></p>
        </div>
      </div>
    </div>
  </section>

  <section class="sana-section">
    <div class="sana-container">
      <div class="sana-head sana-head--center sana-reveal">
        <span class="sana-head__eyebrow"><?php echo e($isRtl ? 'أرقام المنصة' : 'Platform numbers'); ?></span>
        <h2 class="sana-head__title"><?php echo e($isRtl ? 'لمحة' : 'A snapshot'); ?> <span class="hl"><?php echo e($isRtl ? 'سريعة' : 'today'); ?></span></h2>
        <span class="sana-head__line"></span>
      </div>
      <div class="sana-ab-metrics sana-reveal">
        <div class="sana-ab-metric">
          <i class="fas fa-book-open"></i>
          <strong><?php echo e(number_format((int) ($stats['courses'] ?? 0))); ?></strong>
          <span><?php echo e(__('public.stat_courses')); ?></span>
        </div>
        <div class="sana-ab-metric">
          <i class="fas fa-user-graduate"></i>
          <strong><?php echo e(number_format((int) ($stats['students'] ?? 0))); ?></strong>
          <span><?php echo e(__('public.stat_students')); ?></span>
        </div>
        <div class="sana-ab-metric">
          <i class="fas fa-chalkboard-user"></i>
          <strong><?php echo e(number_format((int) ($stats['instructors'] ?? 0))); ?></strong>
          <span><?php echo e(__('public.stat_instructors')); ?></span>
        </div>
      </div>
    </div>
  </section>

  <section class="sana-section sana-section--soft">
    <div class="sana-container">
      <div class="sana-head sana-head--center sana-reveal">
        <span class="sana-head__eyebrow"><?php echo e(__('public.our_values')); ?></span>
        <h2 class="sana-head__title"><?php echo e($isRtl ? 'ما' : 'What'); ?> <span class="hl"><?php echo e($isRtl ? 'يميّزنا' : 'sets us apart'); ?></span></h2>
        <span class="sana-head__line"></span>
      </div>
      <div class="sana-ab-values">
        <div class="sana-ab-value sana-reveal">
          <div class="sana-ab-value__icon"><i class="fas fa-comments"></i></div>
          <strong><?php echo e(__('public.value_1_title')); ?></strong>
          <span><?php echo e(__('public.value_1_desc')); ?></span>
        </div>
        <div class="sana-ab-value sana-reveal">
          <div class="sana-ab-value__icon"><i class="fas fa-shield-halved"></i></div>
          <strong><?php echo e(__('public.value_2_title')); ?></strong>
          <span><?php echo e(__('public.value_2_desc')); ?></span>
        </div>
        <div class="sana-ab-value sana-reveal">
          <div class="sana-ab-value__icon"><i class="fas fa-chalkboard-user"></i></div>
          <strong><?php echo e(__('public.value_3_title')); ?></strong>
          <span><?php echo e(__('public.value_3_desc')); ?></span>
        </div>
      </div>
    </div>
  </section>

  <section class="sana-ab-final">
    <div class="sana-container sana-reveal">
      <div class="sana-ab-final__box">
        <h2><?php echo e(__('public.cta_about_title')); ?></h2>
        <p><?php echo e(__('public.cta_about_desc')); ?></p>
        <div class="sana-ab-final__actions">
          <div class="sana-site-cta sana-site-cta--center">
            <a href="<?php echo e(route('home')); ?>?open_trial=1" class="sana-btn sana-btn--yellow"><i class="fas fa-clipboard-check"></i> <?php echo e(__('landing.academy.free_trial_cta')); ?></a>
            <a href="<?php echo e($waUrl); ?>" class="sana-btn sana-btn--wa" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> <?php echo e($isRtl ? 'واتساب' : 'WhatsApp'); ?></a>
          </div>
          <a href="<?php echo e(route('public.courses')); ?>" class="sana-btn sana-btn--ghost-light" style="margin-top:10px"><?php echo e(__('public.browse_all_courses_btn')); ?> <i class="fas fa-compass"></i></a>
        </div>
      </div>
    </div>
  </section>

</main>

<?php echo $__env->make('partials.landing.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\public\about.blade.php ENDPATH**/ ?>