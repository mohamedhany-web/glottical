<?php
    $a = $a ?? 'landing.academy';
    $isRtl = $isRtl ?? (app()->getLocale() === 'ar');
    $brand = config('app.name', 'Glottical');
    $footer = \App\Services\PublicFooterSettings::payload();
    $waUrl = $footer['whatsapp_url'] ?? 'https://wa.me/201044610507';
    $featuredList = ($featuredCourses ?? collect())->take(8);
    $instructorsSample = ($topInstructors ?? $featuredInstructors ?? collect())->take(4);
    if ($instructorsSample->isEmpty() && isset($featuredList)) {
        $instructorsSample = $featuredList->map(fn ($c) => $c->instructor)->filter()->unique('id')->take(4);
    }
    $heroPath = public_path('img/glottical/hero-Photoroom.png');
    $heroImg = file_exists($heroPath)
        ? asset('img/glottical/hero-Photoroom.png').'?v='.filemtime($heroPath)
        : asset('img/glottical/hero.png');
    $booksImg = asset('img/sanua/landing-hero-3d-books.png');
    $faqBoyImg = asset('img/sanua/landing-hero-boy.png');
    $coursesUrl = route('public.courses');
    $groupsUrl = route('public.groups');
    $aboutUrl = route('public.about');
    $instructorsUrl = route('public.instructors.index');
?>

<main>


<section class="sana-hero-wrap" id="top">
  <div class="sana-hero">
    <div class="sana-hero__bg-deco" aria-hidden="true">
      <div class="sana-hero__glow sana-hero__glow--1"></div>
      <div class="sana-hero__glow sana-hero__glow--2"></div>
      <div class="sana-hero__glow sana-hero__glow--3"></div>
      <div class="sana-hero__glow sana-hero__glow--4"></div>
      <div class="sana-hero__dotgrid"></div>
      <span class="sana-hero__bokeh sana-hero__bokeh--1"></span>
      <span class="sana-hero__bokeh sana-hero__bokeh--2"></span>
      <span class="sana-hero__bokeh sana-hero__bokeh--3"></span>
      <span class="sana-hero__bokeh sana-hero__bokeh--4"></span>
      <span class="sana-hero__geo sana-hero__geo--1"></span>
      <span class="sana-hero__geo sana-hero__geo--2"></span>
      <span class="sana-hero__geo sana-hero__geo--3"></span>
      <span class="sana-hero__cross sana-hero__cross--1"></span>
      <span class="sana-hero__cross sana-hero__cross--2"></span>
      <svg class="sana-hero__wave" viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true">
        <path d="M0,64 C240,120 480,0 720,48 C960,96 1200,24 1440,64 L1440,120 L0,120 Z" fill="rgba(255,255,255,0.04)"/>
        <path d="M0,88 C360,40 720,100 1080,56 C1260,36 1380,72 1440,80 L1440,120 L0,120 Z" fill="rgba(11,61,145,0.12)"/>
      </svg>
      <svg class="sana-hero__arc sana-hero__arc--1" viewBox="0 0 200 200" aria-hidden="true">
        <circle cx="100" cy="100" r="80" fill="none" stroke="rgba(255,255,255,0.07)" stroke-width="1.5" stroke-dasharray="6 10"/>
      </svg>
      <svg class="sana-hero__arc sana-hero__arc--2" viewBox="0 0 120 120" aria-hidden="true">
        <circle cx="60" cy="60" r="48" fill="none" stroke="rgba(245,184,0,0.18)" stroke-width="1.5" stroke-dasharray="4 8"/>
      </svg>
    </div>
    <div class="sana-container sana-hero__container">
      <div class="sana-hero__grid">
        <div class="sana-hero__content sana-reveal">
          <p class="sana-hero__eyebrow">
            <span class="sana-hero__eyebrow-dot"></span>
            <?php echo e($isRtl ? 'أكاديمية لغات · حصص مباشرة' : 'Language academy · Live sessions'); ?>

          </p>
          <h1 class="sana-hero__title">
            <?php echo e($brand); ?>

            <span class="hl"><?php echo e($isRtl ? 'اللغة لا تُحفظ… اللغة تُعاش' : 'Language isn’t memorized — it’s lived'); ?></span>
          </h1>
          <p class="sana-hero__desc"><?php echo e($isRtl
            ? 'تعلّم العربية والإنجليزية عبر حصص مباشرة (فردية وجماعية)، ومتابعة لولي الأمر — من التقييم المجاني حتى الشهادة.'
            : 'Learn Arabic and English through live 1:1 and group sessions, with parent follow-up — from free assessment to certificate.'); ?></p>
          <div class="sana-hero__actions">
            <div class="sana-site-cta sana-site-cta--hero">
              <button type="button" data-open-free-trial class="sana-btn sana-btn--yellow sana-btn--lg">
                <i class="fas fa-clipboard-check"></i> <?php echo e(__($a.'.free_trial_cta')); ?>

              </button>
              <a href="<?php echo e($waUrl); ?>" class="sana-btn sana-btn--wa sana-btn--lg" target="_blank" rel="noopener">
                <i class="fab fa-whatsapp"></i> <?php echo e($isRtl ? 'تواصل عبر واتساب' : 'WhatsApp'); ?>

              </a>
            </div>
          </div>
          <ul class="sana-hero__trust" aria-label="<?php echo e($isRtl ? 'لماذا Glottical' : 'Why Glottical'); ?>">
            <li><i class="fas fa-video"></i> <?php echo e($isRtl ? 'تعليم مباشر هو الأساس' : 'Live learning first'); ?></li>
            <li><i class="fas fa-layer-group"></i> <?php echo e($isRtl ? 'مجالان: عربي/إسلامي · إنجليزي' : 'Two areas: Arabic/Islamic · English'); ?></li>
            <li><i class="fas fa-user-group"></i> <?php echo e($isRtl ? 'متابعة واضحة لولي الأمر' : 'Clear parent follow-up'); ?></li>
          </ul>
          <div class="sana-hero__badges">
            <span class="sana-hero__badge"><i class="fas fa-shield-halved"></i> <?php echo e($isRtl ? 'بيئة آمنة ومنظّمة' : 'Safe & structured'); ?></span>
            <span class="sana-hero__badge"><i class="fas fa-chalkboard-user"></i> <?php echo e($isRtl ? 'معلّمون متخصصون' : 'Specialist tutors'); ?></span>
          </div>
        </div>
        <div class="sana-hero__visual sana-reveal" aria-hidden="true">
          <div class="sana-hero-illus">
            <div class="sana-hero-illus__deco" aria-hidden="true">
              <span class="sana-hero-illus__glow"></span>
              <span class="sana-hero-illus__ring sana-hero-illus__ring--1"></span>
              <span class="sana-hero-illus__ring sana-hero-illus__ring--2"></span>
              <span class="sana-hero-illus__blob sana-hero-illus__blob--1"></span>
              <span class="sana-hero-illus__blob sana-hero-illus__blob--2"></span>
              <span class="sana-hero-illus__spark sana-hero-illus__spark--1">✦</span>
              <span class="sana-hero-illus__spark sana-hero-illus__spark--2">✦</span>
              <span class="sana-hero-illus__star sana-hero-illus__star--1">⭐</span>
              <span class="sana-hero-illus__float sana-hero-illus__float--bulb">💡</span>
            </div>
            <div class="sana-hero-illus__char-wrap">
              <img src="<?php echo e($heroImg); ?>" alt="<?php echo e($brand); ?>" class="sana-hero-illus__char" width="1024" height="1024" loading="eager" decoding="async">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="sana-container">
    <div class="sana-hero-stats sana-hero-stats--trust sana-reveal" aria-label="<?php echo e($isRtl ? 'أرقام المنصة' : 'Platform highlights'); ?>">
      <div class="sana-hero-stats__item">
        <span class="sana-hero-stats__icon"><i class="fas fa-video"></i></span>
        <div>
          <strong><?php echo e($isRtl ? 'حصص مباشرة أولًا' : 'Live sessions first'); ?></strong>
          <span><?php echo e($isRtl ? 'التعلّم الحي هو العمود الفقري للمنصة' : 'Live teaching is the core of the platform'); ?></span>
        </div>
      </div>
      <div class="sana-hero-stats__item">
        <span class="sana-hero-stats__icon"><i class="fas fa-users"></i></span>
        <div>
          <strong><?php echo e($isRtl ? 'فردي + مجموعات' : '1:1 + groups'); ?></strong>
          <span><?php echo e($isRtl ? 'اختر الأسلوب الأنسب لمستواك وهدفك' : 'Choose the format that fits your goal'); ?></span>
        </div>
      </div>
      <div class="sana-hero-stats__item">
        <span class="sana-hero-stats__icon"><i class="fas fa-chart-line"></i></span>
        <div>
          <strong><?php echo e($isRtl ? 'متابعة لولي الأمر' : 'Parent visibility'); ?></strong>
          <span><?php echo e($isRtl ? 'حضور، تقدّم، وتقارير مفهومة بعد الحصص' : 'Attendance, progress, and clear session reports'); ?></span>
        </div>
      </div>
    </div>
  </div>
</section>


<section class="gl-start sana-section" id="paths">
  <div class="sana-container">
    <div class="gl-start__head sana-reveal">
      <span class="gl-start__eyebrow"><i class="fas fa-rocket"></i> <?php echo e($isRtl ? 'كيف تبدأ؟' : 'How to start'); ?></span>
      <h2 class="gl-start__title"><?php echo e($isRtl ? 'من التقييم… إلى الحصة الأولى' : 'From assessment… to your first session'); ?></h2>
      <p class="gl-start__sub"><?php echo e($isRtl
        ? 'ثلاث خطوات واضحة للطالب وولي الأمر — بدون تخمين، وبدون التزام قبل ما نفهم مستواك.'
        : 'Three clear steps for learners and parents — no guesswork, no commitment before we understand your level.'); ?></p>
    </div>

    <div class="gl-start__tracks sana-reveal" aria-label="<?php echo e($isRtl ? 'مجالا المنصة' : 'Learning areas'); ?>">
      <a href="<?php echo e($coursesUrl); ?>" class="gl-start__track gl-start__track--ar">
        <span class="gl-start__track-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
        </span>
        <div class="gl-start__track-copy">
          <strong><?php echo e($isRtl ? 'العربي والإسلامي' : 'Arabic & Islamic'); ?></strong>
          <span><?php echo e($isRtl ? 'القرآن · التجويد · العربية · الدراسات الإسلامية' : 'Quran · Tajweed · Arabic · Islamic studies'); ?></span>
        </div>
        <span class="gl-start__track-arrow" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="<?php echo e($isRtl ? 'M19 12H5M12 19l-7-7 7-7' : 'M5 12h14M12 5l7 7-7 7'); ?>"/></svg>
        </span>
      </a>
      <a href="<?php echo e($coursesUrl); ?>" class="gl-start__track gl-start__track--en">
        <span class="gl-start__track-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
        </span>
        <div class="gl-start__track-copy">
          <strong><?php echo e($isRtl ? 'اللغة الإنجليزية' : 'English'); ?></strong>
          <span><?php echo e($isRtl ? 'محادثة · أعمال · IELTS · TOEFL' : 'Conversation · Business · IELTS · TOEFL'); ?></span>
        </div>
        <span class="gl-start__track-arrow" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="<?php echo e($isRtl ? 'M19 12H5M12 19l-7-7 7-7' : 'M5 12h14M12 5l7 7-7 7'); ?>"/></svg>
        </span>
      </a>
    </div>

    <div class="gl-start__steps sana-reveal" aria-label="<?php echo e($isRtl ? 'خطوات البدء' : 'Start steps'); ?>">
      <article class="gl-start__step">
        <span class="gl-start__step-num">01</span>
        <div class="gl-start__step-icon"><i class="fas fa-clipboard-check"></i></div>
        <h3><?php echo e($isRtl ? 'احجز تقييم مستوى مجاني' : 'Book a free level assessment'); ?></h3>
        <p><?php echo e($isRtl ? 'حصة قصيرة نحدد فيها مستواك الحقيقي وهدفك من التعلّم.' : 'A short session to place your real level and learning goal.'); ?></p>
      </article>
      <article class="gl-start__step">
        <span class="gl-start__step-num">02</span>
        <div class="gl-start__step-icon"><i class="fas fa-user-check"></i></div>
        <h3><?php echo e($isRtl ? 'نرشّح المسار والمعلّم' : 'We match path & tutor'); ?></h3>
        <p><?php echo e($isRtl ? 'فردي أو مجموعة، ومسار عربي/إسلامي أو إنجليزي — حسب احتياجك.' : '1:1 or group, Arabic/Islamic or English — based on your needs.'); ?></p>
      </article>
      <article class="gl-start__step">
        <span class="gl-start__step-num">03</span>
        <div class="gl-start__step-icon"><i class="fas fa-graduation-cap"></i></div>
        <h3><?php echo e($isRtl ? 'ابدأ الحصص وتابع التقدّم' : 'Start & track progress'); ?></h3>
        <p><?php echo e($isRtl ? 'حصص مباشرة داخل المنصة، مع متابعة لولي الأمر بعد كل مرحلة.' : 'Live sessions on the platform, with parent visibility after each stage.'); ?></p>
      </article>
    </div>

    <div class="gl-start__cta sana-reveal">
      <div class="sana-site-cta">
        <button type="button" data-open-free-trial class="sana-btn sana-btn--yellow sana-btn--lg">
          <i class="fas fa-clipboard-check"></i> <?php echo e(__($a.'.free_trial_cta')); ?>

        </button>
        <a href="<?php echo e($waUrl); ?>" class="sana-btn sana-btn--wa sana-btn--lg" target="_blank" rel="noopener">
          <i class="fab fa-whatsapp"></i> <?php echo e($isRtl ? 'تواصل عبر واتساب' : 'WhatsApp'); ?>

        </a>
      </div>
      <a href="<?php echo e($aboutUrl); ?>" class="gl-start__link"><i class="fas fa-circle-info"></i> <?php echo e($isRtl ? 'تعرّف على طريقة عمل المنصة' : 'See how the platform works'); ?></a>
    </div>
  </div>
</section>


<section class="sana-section sana-section--white" id="features">
  <div class="sana-container">
    <div class="sana-head sana-reveal">
      <h2 class="sana-head__title"><?php echo e($isRtl ? 'لماذا' : 'Why'); ?> <span class="hl"><?php echo e($brand); ?>؟</span></h2>
      <span class="sana-head__line"></span>
      <p class="sana-head__sub" style="margin-top:12px;color:var(--muted);max-width:42rem"><?php echo e($isRtl
        ? 'منصة لغات مبنية على الممارسة الحية — مش الحفظ فقط.'
        : 'A language platform built on lived practice — not memorization alone.'); ?></p>
    </div>
    <div class="sana-features-m">
      <article class="sana-feature-m sana-reveal">
        <div class="sana-feature-m__icon" style="background:#E8EEF8">🎥</div>
        <h3><?php echo e($isRtl ? 'تعليم مباشر' : 'Live teaching'); ?></h3>
        <p><?php echo e($isRtl ? 'حصص حية مع معلّم — فردية أو مجموعات منظّمة داخل دفعات.' : 'Live sessions with a tutor — 1:1 or structured group cohorts.'); ?></p>
      </article>
      <article class="sana-feature-m sana-reveal">
        <div class="sana-feature-m__icon" style="background:#DBEAFE">🗣️</div>
        <h3><?php echo e($isRtl ? 'تعلّم بالغمر' : 'Immersive practice'); ?></h3>
        <p><?php echo e($isRtl ? 'محادثة وممارسة يومية بدل الحفظ التقليدي — اللغة تُعاش.' : 'Conversation and daily practice instead of rote memorization.'); ?></p>
      </article>
      <article class="sana-feature-m sana-reveal">
        <div class="sana-feature-m__icon" style="background:#FFF6D6">👨‍👩‍👧</div>
        <h3><?php echo e($isRtl ? 'لوحة ولي الأمر' : 'Parent dashboard'); ?></h3>
        <p><?php echo e($isRtl ? 'حضور وتقدّم وتقارير واضحة بعد الحصص — بدون تعقيد.' : 'Clear attendance, progress, and reports after sessions.'); ?></p>
      </article>
      <article class="sana-feature-m sana-reveal">
        <div class="sana-feature-m__icon" style="background:#E8EEF8">🗂️</div>
        <h3><?php echo e($isRtl ? 'مسارات تعليمية' : 'Learning paths'); ?></h3>
        <p><?php echo e($isRtl ? 'مسار عربي/إسلامي ومسار إنجليزي — أهداف ومراحل واضحة.' : 'Arabic/Islamic and English tracks with clear stages.'); ?></p>
      </article>
      <article class="sana-feature-m sana-reveal">
        <div class="sana-feature-m__icon" style="background:#D1FAE5">📜</div>
        <h3><?php echo e($isRtl ? 'شهادات رقمية' : 'Digital certificates'); ?></h3>
        <p><?php echo e($isRtl ? 'شهادة إتمام قابلة للتحقق بعد إنجاز المسار.' : 'Verifiable completion certificates after finishing a path.'); ?></p>
      </article>
      <article class="sana-feature-m sana-reveal">
        <div class="sana-feature-m__icon" style="background:#FFEDD5">🎬</div>
        <h3><?php echo e($isRtl ? 'مكتبة مساندة' : 'Support library'); ?></h3>
        <p><?php echo e($isRtl ? 'محتوى مسجّل يكمّل الحصص المباشرة عند الحاجة.' : 'Recorded content that supports live sessions when needed.'); ?></p>
      </article>
    </div>
  </div>
</section>


<section class="sana-section" id="categories">
  <div class="sana-container">
    <div class="sana-head-row sana-reveal">
      <div class="sana-head">
        <h2 class="sana-head__title"><?php echo e($isRtl ? 'برامج' : 'Browse'); ?> <span class="hl"><?php echo e($isRtl ? 'المنصة' : 'programs'); ?></span></h2>
        <span class="sana-head__line"></span>
      </div>
      <a href="<?php echo e($coursesUrl); ?>" class="sana-link-more"><?php echo e($isRtl ? 'عرض الكل' : 'View all'); ?> <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?>"></i></a>
    </div>
    <div class="sana-cats-row sana-reveal">
      <a href="<?php echo e($coursesUrl); ?>" class="sana-cat-m" style="background:linear-gradient(145deg,#E8EEF8,#B8C9E8)"><span class="sana-cat-m__emoji">📖</span><span class="sana-cat-m__name"><?php echo e($isRtl ? 'عربي' : 'Arabic'); ?></span></a>
      <a href="<?php echo e($coursesUrl); ?>" class="sana-cat-m" style="background:linear-gradient(145deg,#FFF6D6,#FFE08A)"><span class="sana-cat-m__emoji">☪️</span><span class="sana-cat-m__name"><?php echo e($isRtl ? 'قرآن وتجويد' : 'Quran'); ?></span></a>
      <a href="<?php echo e($coursesUrl); ?>" class="sana-cat-m" style="background:linear-gradient(145deg,#DBEAFE,#93C5FD)"><span class="sana-cat-m__emoji">🌍</span><span class="sana-cat-m__name"><?php echo e($isRtl ? 'إنجليزي' : 'English'); ?></span></a>
      <a href="<?php echo e($coursesUrl); ?>" class="sana-cat-m" style="background:linear-gradient(145deg,#D1FAE5,#6EE7B7)"><span class="sana-cat-m__emoji">💼</span><span class="sana-cat-m__name"><?php echo e($isRtl ? 'أعمال' : 'Business'); ?></span></a>
      <a href="<?php echo e($coursesUrl); ?>" class="sana-cat-m" style="background:linear-gradient(145deg,#FFEDD5,#FDBA74)"><span class="sana-cat-m__emoji">🎓</span><span class="sana-cat-m__name">IELTS</span></a>
    </div>
  </div>
</section>


<section class="sana-section sana-section--white" id="courses">
  <div class="sana-container">
    <div class="sana-head-row sana-reveal">
      <div class="sana-head">
        <h2 class="sana-head__title"><?php echo e($isRtl ? 'برامج' : 'Featured'); ?> <span class="hl"><?php echo e($isRtl ? 'مميزة' : 'programs'); ?></span></h2>
        <span class="sana-head__line"></span>
      </div>
      <a href="<?php echo e($coursesUrl); ?>" class="sana-link-more"><?php echo e($isRtl ? 'عرض الكل' : 'View all'); ?> <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?>"></i></a>
    </div>
    <div class="sana-courses-m">
      <?php $__empty_1 = true; $__currentLoopData = $featuredList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
          $thumb = $course->thumbnail_url ?: $booksImg;
          $url = route('public.course.show', $course->id);
          $instName = $course->instructor->name ?? ($isRtl ? 'معلّم' : 'Tutor');
          $initial = mb_substr($instName, 0, 1) ?: 'م';
          $catName = $course->courseCategory->name ?? ($course->category->name ?? ($isRtl ? 'برنامج' : 'Program'));
        ?>
        <article class="sana-course-card sana-course-card--featured sana-reveal">
          <div class="sana-course-card__media">
            <a href="<?php echo e($url); ?>" class="sana-course-card__img-link" tabindex="-1" aria-hidden="true">
              <img src="<?php echo e($thumb); ?>" alt="<?php echo e($course->title); ?>" loading="lazy">
            </a>
            <div class="sana-course-card__shine" aria-hidden="true"></div>
            <div class="sana-course-card__badges">
              <span class="sana-course-card__badge sana-course-card__badge--subject"><?php echo e($catName); ?></span>
              <?php if(!empty($course->level)): ?>
                <span class="sana-course-card__badge sana-course-card__badge--level"><?php echo e($course->level); ?></span>
              <?php endif; ?>
            </div>
          </div>
          <div class="sana-course-card__body">
            <h3 class="sana-course-card__title"><a href="<?php echo e($url); ?>"><?php echo e($course->title); ?></a></h3>
            <p class="sana-course-card__desc"><?php echo e(\Illuminate\Support\Str::limit(strip_tags($course->description ?? ($isRtl ? 'برنامج لغوي مباشر.' : 'Live language program.')), 90)); ?></p>
            <div class="sana-course-card__instructor">
              <span class="sana-course-card__avatar sana-course-card__avatar--initial"><?php echo e($initial); ?></span>
              <div class="sana-course-card__instructor-info">
                <span class="sana-course-card__instructor-label"><?php echo e($isRtl ? 'المعلّم' : 'Tutor'); ?></span>
                <span class="sana-course-card__instructor-name"><?php echo e($instName); ?></span>
              </div>
            </div>
          </div>
        </article>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <article class="sana-course-card sana-course-card--featured sana-reveal">
          <div class="sana-course-card__media">
            <a href="<?php echo e($coursesUrl); ?>" class="sana-course-card__img-link" tabindex="-1" aria-hidden="true">
              <img src="<?php echo e($booksImg); ?>" alt="" loading="lazy">
            </a>
            <div class="sana-course-card__shine" aria-hidden="true"></div>
            <div class="sana-course-card__badges">
              <span class="sana-course-card__badge sana-course-card__badge--subject"><?php echo e($isRtl ? 'قريباً' : 'Soon'); ?></span>
            </div>
          </div>
          <div class="sana-course-card__body">
            <h3 class="sana-course-card__title"><a href="<?php echo e($coursesUrl); ?>"><?php echo e($isRtl ? 'البرامج قريباً' : 'Programs coming soon'); ?></a></h3>
            <p class="sana-course-card__desc"><?php echo e($isRtl ? 'احجز تقييم مستوى مجاني لنرشّح لك المسار المناسب.' : 'Book a free assessment so we can recommend the right path.'); ?></p>
            <div class="sana-course-card__instructor">
              <span class="sana-course-card__avatar sana-course-card__avatar--initial">G</span>
              <div class="sana-course-card__instructor-info">
                <span class="sana-course-card__instructor-label"><?php echo e($brand); ?></span>
                <span class="sana-course-card__instructor-name"><?php echo e($isRtl ? 'المنصة' : 'Platform'); ?></span>
              </div>
            </div>
          </div>
        </article>
      <?php endif; ?>
    </div>
  </div>
</section>


<section class="sana-section" id="instructors">
  <div class="sana-container">
    <div class="sana-head-row sana-reveal">
      <div class="sana-head">
        <h2 class="sana-head__title"><?php echo e($isRtl ? 'معلّمو' : 'Meet our'); ?> <span class="hl"><?php echo e($brand); ?></span></h2>
        <span class="sana-head__line"></span>
      </div>
      <a href="<?php echo e($instructorsUrl); ?>" class="sana-link-more"><?php echo e($isRtl ? 'عرض الكل' : 'View all'); ?> <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?>"></i></a>
    </div>
    <div class="sana-teachers-m">
      <?php $__empty_1 = true; $__currentLoopData = $instructorsSample; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
          $name = is_object($inst) ? ($inst->name ?? '') : '';
          $id = is_object($inst) ? ($inst->id ?? null) : null;
          $href = $id ? route('public.instructors.show', $id) : $instructorsUrl;
          $initial = mb_substr($name, 0, 1) ?: 'م';
        ?>
        <a href="<?php echo e($href); ?>" class="sana-teacher-m sana-reveal" style="text-decoration:none;color:inherit">
          <div class="sana-teacher-m__ring"><span class="av"><?php echo e($initial); ?></span></div>
          <h3><?php echo e($name !== '' ? $name : ($isRtl ? 'معلّم على المنصة' : 'Platform tutor')); ?></h3>
          <p class="role"><?php echo e($isRtl ? 'حصص مباشرة · متابعة منتظمة' : 'Live sessions · steady coaching'); ?></p>
          <span class="sana-teacher-m__book"><i class="fas fa-calendar-check"></i> <?php echo e($isRtl ? 'عرض الملف' : 'View profile'); ?></span>
        </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <a href="<?php echo e($instructorsUrl); ?>" class="sana-teacher-m sana-reveal" style="text-decoration:none;color:inherit">
          <div class="sana-teacher-m__ring"><span class="av">G</span></div>
          <h3><?php echo e($brand); ?></h3>
          <p class="role"><?php echo e($isRtl ? 'المعلّمون قريباً' : 'Tutors coming soon'); ?></p>
          <span class="sana-teacher-m__book"><i class="fas fa-calendar-check"></i> <?php echo e($isRtl ? 'تصفّح القائمة' : 'Browse list'); ?></span>
        </a>
      <?php endif; ?>
    </div>
  </div>
</section>


<section class="sana-section sana-section--white" id="achievements">
  <div class="sana-container">
    <div class="sana-achieve-box sana-reveal">
      <div class="sana-achieve-box__glow sana-achieve-box__glow--1"></div>
      <div class="sana-achieve-box__glow sana-achieve-box__glow--2"></div>
      <div class="sana-achieve-box__inner">
        <div class="sana-achieve-box__content">
          <span class="sana-achieve-box__tag"><i class="fas fa-certificate"></i> <?php echo e($isRtl ? 'شهادات إتمام · قابلة للتحقق' : 'Verifiable certificates'); ?></span>
          <h2 class="sana-achieve-box__title"><?php echo e($isRtl ? 'أنهِ المسار…' : 'Finish the path…'); ?> <span class="hl"><?php echo e($isRtl ? 'واحتفل بالإنجاز' : 'celebrate the win'); ?></span></h2>
          <p class="sana-achieve-box__desc"><?php echo e($isRtl
            ? 'بعد إتمام المسار التعليمي تُصدَر شهادة رقمية باسمك — قابلة للتحقق والمشاركة.'
            : 'After completing a learning path, a digital certificate is issued in your name — verifiable and shareable.'); ?></p>
          <ul class="sana-achieve-box__highlights">
            <li><i class="fas fa-check-circle"></i> <?php echo e($isRtl ? 'شهادة رقمية بعد إتمام المسار' : 'Digital certificate on path completion'); ?></li>
            <li><i class="fas fa-qrcode"></i> <?php echo e($isRtl ? 'رمز تحقق ومشاركة آمنة' : 'Secure verify & share'); ?></li>
            <li><i class="fas fa-shield-halved"></i> <?php echo e($isRtl ? 'بهوية '.$brand.' الرسمية' : 'With official '.$brand.' branding'); ?></li>
          </ul>
        </div>
        <div class="sana-achieve-box__visual" aria-hidden="true">
          <div class="sana-cert-mock">
            <div class="sana-cert-mock__corner sana-cert-mock__corner--tl"></div>
            <div class="sana-cert-mock__corner sana-cert-mock__corner--tr"></div>
            <div class="sana-cert-mock__corner sana-cert-mock__corner--bl"></div>
            <div class="sana-cert-mock__corner sana-cert-mock__corner--br"></div>
            <div class="sana-cert-mock__seal"><i class="fas fa-star"></i></div>
            <p class="sana-cert-mock__label"><?php echo e($isRtl ? 'شهادة إتمام' : 'Certificate'); ?></p>
            <h3 class="sana-cert-mock__brand"><?php echo e($brand); ?></h3>
            <div class="sana-cert-mock__line"></div>
            <p class="sana-cert-mock__to"><?php echo e($isRtl ? 'تُمنح هذه الشهادة لـ' : 'Awarded to'); ?></p>
            <p class="sana-cert-mock__name"><?php echo e($isRtl ? 'الطالب المتميّز' : 'Outstanding student'); ?></p>
            <p class="sana-cert-mock__course"><?php echo e($isRtl ? 'لإتمام المسار التعليمي بنجاح' : 'For successfully completing the path'); ?></p>
            <div class="sana-cert-mock__footer">
              <span><i class="fas fa-qrcode"></i> <?php echo e($isRtl ? 'قابلة للتحقق' : 'Verifiable'); ?></span>
              <span><i class="fas fa-shield-halved"></i> <?php echo e($isRtl ? 'صادرة من المنصة' : 'Issued by platform'); ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<section class="sana-section" id="faq">
  <div class="sana-container">
    <div class="sana-head sana-reveal">
      <h2 class="sana-head__title"><?php echo e($isRtl ? 'الأسئلة' : 'FAQ'); ?> <span class="hl"><?php echo e($isRtl ? 'الشائعة' : ''); ?></span></h2>
      <span class="sana-head__line"></span>
    </div>
    <div class="sana-faq-m sana-reveal">
      <div class="sana-faq-m__visual">
        <img src="<?php echo e($faqBoyImg); ?>" alt="">
        <span class="bubble bubble--1">🤔</span>
        <span class="bubble bubble--2">💡</span>
      </div>
      <div class="sana-faq" id="sana-faq">
        <div class="sana-faq-item is-open">
          <button type="button" class="sana-faq-q"><?php echo e($isRtl ? 'ما فكرة Glottical؟' : 'What is Glottical about?'); ?> <i class="fas fa-chevron-down"></i></button>
          <div class="sana-faq-a"><?php echo e($isRtl ? 'منصة لغات تعتمد على الحصص المباشرة والممارسة الحية: «اللغة لا تُحفظ… اللغة تُعاش» — مع مسارات عربي/إسلامي وإنجليزي.' : 'A language platform built on live sessions and real practice: language isn’t memorized — it’s lived. Arabic/Islamic and English tracks.'); ?></div>
        </div>
        <div class="sana-faq-item">
          <button type="button" class="sana-faq-q"><?php echo e($isRtl ? 'كيف أبدأ؟' : 'How do I start?'); ?> <i class="fas fa-chevron-down"></i></button>
          <div class="sana-faq-a"><?php echo e($isRtl ? 'احجز تقييم مستوى مجاني — نحدد مستواك ونرشّح المسار والمعلّم أو المجموعة المناسبة قبل أي التزام.' : 'Book a free level assessment — we place you and recommend the right path, tutor, or group before any commitment.'); ?></div>
        </div>
        <div class="sana-faq-item">
          <button type="button" class="sana-faq-q"><?php echo e($isRtl ? 'هل الحصص فردية أم مجموعات؟' : 'Are sessions 1:1 or groups?'); ?> <i class="fas fa-chevron-down"></i></button>
          <div class="sana-faq-a"><?php echo e($isRtl ? 'الاثنان متاحان: حصص فردية ومجموعات جماعية بدفعات محدودة العدد.' : 'Both: private 1:1 sessions and group cohorts with capped seats.'); ?></div>
        </div>
        <div class="sana-faq-item">
          <button type="button" class="sana-faq-q"><?php echo e($isRtl ? 'كيف أتابع تقدّم ابني؟' : 'How do parents track progress?'); ?> <i class="fas fa-chevron-down"></i></button>
          <div class="sana-faq-a"><?php echo e($isRtl ? 'لوحة ولي الأمر تعرض الحضور والتقدّم والملخصات بعد الحصص بوضوح.' : 'The parent dashboard shows attendance, progress, and clear post-session summaries.'); ?></div>
        </div>
        <div class="sana-faq-item">
          <button type="button" class="sana-faq-q"><?php echo e($isRtl ? 'هل توجد شهادات؟' : 'Do you issue certificates?'); ?> <i class="fas fa-chevron-down"></i></button>
          <div class="sana-faq-a"><?php echo e($isRtl ? 'نعم — شهادات إتمام رقمية قابلة للتحقق بعد إنجاز المسار.' : 'Yes — digital verifiable certificates after path completion.'); ?></div>
        </div>
      </div>
    </div>
  </div>
</section>

</main>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\partials\welcome-landing-main.blade.php ENDPATH**/ ?>