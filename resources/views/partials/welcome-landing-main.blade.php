@php
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
@endphp

<main>

{{-- ===== hero ===== --}}
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
            {{ $isRtl ? 'أكاديمية لغات · حصص مباشرة' : 'Language academy · Live sessions' }}
          </p>
          <h1 class="sana-hero__title">
            {{ $brand }}
            <span class="hl">{{ $isRtl ? 'اللغة لا تُحفظ… اللغة تُعاش' : 'Language isn’t memorized — it’s lived' }}</span>
          </h1>
          <p class="sana-hero__desc">{{ $isRtl
            ? 'تعلّم العربية والإنجليزية عبر حصص مباشرة (فردية وجماعية)، ومتابعة لولي الأمر — من التقييم المجاني حتى الشهادة.'
            : 'Learn Arabic and English through live 1:1 and group sessions, with parent follow-up — from free assessment to certificate.' }}</p>
          <div class="sana-hero__actions">
            <div class="sana-site-cta sana-site-cta--hero">
              <button type="button" data-open-free-trial class="sana-btn sana-btn--yellow sana-btn--lg">
                <i class="fas fa-clipboard-check"></i> {{ __($a.'.free_trial_cta') }}
              </button>
              <a href="{{ $waUrl }}" class="sana-btn sana-btn--wa sana-btn--lg" target="_blank" rel="noopener">
                <i class="fab fa-whatsapp"></i> {{ $isRtl ? 'تواصل عبر واتساب' : 'WhatsApp' }}
              </a>
            </div>
          </div>
          <ul class="sana-hero__trust" aria-label="{{ $isRtl ? 'لماذا Glottical' : 'Why Glottical' }}">
            <li><i class="fas fa-video"></i> {{ $isRtl ? 'تعليم مباشر هو الأساس' : 'Live learning first' }}</li>
            <li><i class="fas fa-layer-group"></i> {{ $isRtl ? 'مجالان: عربي/إسلامي · إنجليزي' : 'Two areas: Arabic/Islamic · English' }}</li>
            <li><i class="fas fa-user-group"></i> {{ $isRtl ? 'متابعة واضحة لولي الأمر' : 'Clear parent follow-up' }}</li>
          </ul>
          <div class="sana-hero__badges">
            <span class="sana-hero__badge"><i class="fas fa-shield-halved"></i> {{ $isRtl ? 'بيئة آمنة ومنظّمة' : 'Safe & structured' }}</span>
            <span class="sana-hero__badge"><i class="fas fa-chalkboard-user"></i> {{ $isRtl ? 'معلّمون متخصصون' : 'Specialist tutors' }}</span>
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
              <img src="{{ $heroImg }}" alt="{{ $brand }}" class="sana-hero-illus__char" width="1024" height="1024" loading="eager" decoding="async">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="sana-container">
    <div class="sana-hero-stats sana-hero-stats--trust sana-reveal" aria-label="{{ $isRtl ? 'أرقام المنصة' : 'Platform highlights' }}">
      <div class="sana-hero-stats__item">
        <span class="sana-hero-stats__icon"><i class="fas fa-video"></i></span>
        <div>
          <strong>{{ $isRtl ? 'حصص مباشرة أولًا' : 'Live sessions first' }}</strong>
          <span>{{ $isRtl ? 'التعلّم الحي هو العمود الفقري للمنصة' : 'Live teaching is the core of the platform' }}</span>
        </div>
      </div>
      <div class="sana-hero-stats__item">
        <span class="sana-hero-stats__icon"><i class="fas fa-users"></i></span>
        <div>
          <strong>{{ $isRtl ? 'فردي + مجموعات' : '1:1 + groups' }}</strong>
          <span>{{ $isRtl ? 'اختر الأسلوب الأنسب لمستواك وهدفك' : 'Choose the format that fits your goal' }}</span>
        </div>
      </div>
      <div class="sana-hero-stats__item">
        <span class="sana-hero-stats__icon"><i class="fas fa-chart-line"></i></span>
        <div>
          <strong>{{ $isRtl ? 'متابعة لولي الأمر' : 'Parent visibility' }}</strong>
          <span>{{ $isRtl ? 'حضور، تقدّم، وتقارير مفهومة بعد الحصص' : 'Attendance, progress, and clear session reports' }}</span>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ===== start journey — تصميم جديد ببراند Glottical ===== --}}
<section class="gl-start sana-section" id="paths">
  <div class="sana-container">
    <div class="gl-start__head sana-reveal">
      <span class="gl-start__eyebrow"><i class="fas fa-rocket"></i> {{ $isRtl ? 'كيف تبدأ؟' : 'How to start' }}</span>
      <h2 class="gl-start__title">{{ $isRtl ? 'من التقييم… إلى الحصة الأولى' : 'From assessment… to your first session' }}</h2>
      <p class="gl-start__sub">{{ $isRtl
        ? 'ثلاث خطوات واضحة للطالب وولي الأمر — بدون تخمين، وبدون التزام قبل ما نفهم مستواك.'
        : 'Three clear steps for learners and parents — no guesswork, no commitment before we understand your level.' }}</p>
    </div>

    <div class="gl-start__tracks sana-reveal" aria-label="{{ $isRtl ? 'مجالا المنصة' : 'Learning areas' }}">
      <a href="{{ $coursesUrl }}" class="gl-start__track gl-start__track--ar">
        <span class="gl-start__track-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
        </span>
        <div class="gl-start__track-copy">
          <strong>{{ $isRtl ? 'العربي والإسلامي' : 'Arabic & Islamic' }}</strong>
          <span>{{ $isRtl ? 'القرآن · التجويد · العربية · الدراسات الإسلامية' : 'Quran · Tajweed · Arabic · Islamic studies' }}</span>
        </div>
        <span class="gl-start__track-arrow" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $isRtl ? 'M19 12H5M12 19l-7-7 7-7' : 'M5 12h14M12 5l7 7-7 7' }}"/></svg>
        </span>
      </a>
      <a href="{{ $coursesUrl }}" class="gl-start__track gl-start__track--en">
        <span class="gl-start__track-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
        </span>
        <div class="gl-start__track-copy">
          <strong>{{ $isRtl ? 'اللغة الإنجليزية' : 'English' }}</strong>
          <span>{{ $isRtl ? 'محادثة · أعمال · IELTS · TOEFL' : 'Conversation · Business · IELTS · TOEFL' }}</span>
        </div>
        <span class="gl-start__track-arrow" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $isRtl ? 'M19 12H5M12 19l-7-7 7-7' : 'M5 12h14M12 5l7 7-7 7' }}"/></svg>
        </span>
      </a>
    </div>

    <div class="gl-start__steps sana-reveal" aria-label="{{ $isRtl ? 'خطوات البدء' : 'Start steps' }}">
      <article class="gl-start__step">
        <span class="gl-start__step-num">01</span>
        <div class="gl-start__step-icon"><i class="fas fa-clipboard-check"></i></div>
        <h3>{{ $isRtl ? 'احجز تقييم مستوى مجاني' : 'Book a free level assessment' }}</h3>
        <p>{{ $isRtl ? 'حصة قصيرة نحدد فيها مستواك الحقيقي وهدفك من التعلّم.' : 'A short session to place your real level and learning goal.' }}</p>
      </article>
      <article class="gl-start__step">
        <span class="gl-start__step-num">02</span>
        <div class="gl-start__step-icon"><i class="fas fa-user-check"></i></div>
        <h3>{{ $isRtl ? 'نرشّح المسار والمعلّم' : 'We match path & tutor' }}</h3>
        <p>{{ $isRtl ? 'فردي أو مجموعة، ومسار عربي/إسلامي أو إنجليزي — حسب احتياجك.' : '1:1 or group, Arabic/Islamic or English — based on your needs.' }}</p>
      </article>
      <article class="gl-start__step">
        <span class="gl-start__step-num">03</span>
        <div class="gl-start__step-icon"><i class="fas fa-graduation-cap"></i></div>
        <h3>{{ $isRtl ? 'ابدأ الحصص وتابع التقدّم' : 'Start & track progress' }}</h3>
        <p>{{ $isRtl ? 'حصص مباشرة داخل المنصة، مع متابعة لولي الأمر بعد كل مرحلة.' : 'Live sessions on the platform, with parent visibility after each stage.' }}</p>
      </article>
    </div>

    <div class="gl-start__cta sana-reveal">
      <div class="sana-site-cta">
        <button type="button" data-open-free-trial class="sana-btn sana-btn--yellow sana-btn--lg">
          <i class="fas fa-clipboard-check"></i> {{ __($a.'.free_trial_cta') }}
        </button>
        <a href="{{ $waUrl }}" class="sana-btn sana-btn--wa sana-btn--lg" target="_blank" rel="noopener">
          <i class="fab fa-whatsapp"></i> {{ $isRtl ? 'تواصل عبر واتساب' : 'WhatsApp' }}
        </a>
      </div>
      <a href="{{ $aboutUrl }}" class="gl-start__link"><i class="fas fa-circle-info"></i> {{ $isRtl ? 'تعرّف على طريقة عمل المنصة' : 'See how the platform works' }}</a>
    </div>
  </div>
</section>

{{-- ===== features ===== --}}
<section class="sana-section sana-section--white" id="features">
  <div class="sana-container">
    <div class="sana-head sana-reveal">
      <h2 class="sana-head__title">{{ $isRtl ? 'لماذا' : 'Why' }} <span class="hl">{{ $brand }}؟</span></h2>
      <span class="sana-head__line"></span>
      <p class="sana-head__sub" style="margin-top:12px;color:var(--muted);max-width:42rem">{{ $isRtl
        ? 'منصة لغات مبنية على الممارسة الحية — مش الحفظ فقط.'
        : 'A language platform built on lived practice — not memorization alone.' }}</p>
    </div>
    <div class="sana-features-m">
      <article class="sana-feature-m sana-reveal">
        <div class="sana-feature-m__icon" style="background:#E8EEF8">🎥</div>
        <h3>{{ $isRtl ? 'تعليم مباشر' : 'Live teaching' }}</h3>
        <p>{{ $isRtl ? 'حصص حية مع معلّم — فردية أو مجموعات منظّمة داخل دفعات.' : 'Live sessions with a tutor — 1:1 or structured group cohorts.' }}</p>
      </article>
      <article class="sana-feature-m sana-reveal">
        <div class="sana-feature-m__icon" style="background:#DBEAFE">🗣️</div>
        <h3>{{ $isRtl ? 'تعلّم بالغمر' : 'Immersive practice' }}</h3>
        <p>{{ $isRtl ? 'محادثة وممارسة يومية بدل الحفظ التقليدي — اللغة تُعاش.' : 'Conversation and daily practice instead of rote memorization.' }}</p>
      </article>
      <article class="sana-feature-m sana-reveal">
        <div class="sana-feature-m__icon" style="background:#FFF6D6">👨‍👩‍👧</div>
        <h3>{{ $isRtl ? 'لوحة ولي الأمر' : 'Parent dashboard' }}</h3>
        <p>{{ $isRtl ? 'حضور وتقدّم وتقارير واضحة بعد الحصص — بدون تعقيد.' : 'Clear attendance, progress, and reports after sessions.' }}</p>
      </article>
      <article class="sana-feature-m sana-reveal">
        <div class="sana-feature-m__icon" style="background:#E8EEF8">🗂️</div>
        <h3>{{ $isRtl ? 'مسارات تعليمية' : 'Learning paths' }}</h3>
        <p>{{ $isRtl ? 'مسار عربي/إسلامي ومسار إنجليزي — أهداف ومراحل واضحة.' : 'Arabic/Islamic and English tracks with clear stages.' }}</p>
      </article>
      <article class="sana-feature-m sana-reveal">
        <div class="sana-feature-m__icon" style="background:#D1FAE5">📜</div>
        <h3>{{ $isRtl ? 'شهادات رقمية' : 'Digital certificates' }}</h3>
        <p>{{ $isRtl ? 'شهادة إتمام قابلة للتحقق بعد إنجاز المسار.' : 'Verifiable completion certificates after finishing a path.' }}</p>
      </article>
      <article class="sana-feature-m sana-reveal">
        <div class="sana-feature-m__icon" style="background:#FFEDD5">🎬</div>
        <h3>{{ $isRtl ? 'مكتبة مساندة' : 'Support library' }}</h3>
        <p>{{ $isRtl ? 'محتوى مسجّل يكمّل الحصص المباشرة عند الحاجة.' : 'Recorded content that supports live sessions when needed.' }}</p>
      </article>
    </div>
  </div>
</section>

{{-- ===== tracks / categories ===== --}}
<section class="sana-section" id="categories">
  <div class="sana-container">
    <div class="sana-head-row sana-reveal">
      <div class="sana-head">
        <h2 class="sana-head__title">{{ $isRtl ? 'برامج' : 'Browse' }} <span class="hl">{{ $isRtl ? 'المنصة' : 'programs' }}</span></h2>
        <span class="sana-head__line"></span>
      </div>
      <a href="{{ $coursesUrl }}" class="sana-link-more">{{ $isRtl ? 'عرض الكل' : 'View all' }} <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}"></i></a>
    </div>
    <div class="sana-cats-row sana-reveal">
      <a href="{{ $coursesUrl }}" class="sana-cat-m" style="background:linear-gradient(145deg,#E8EEF8,#B8C9E8)"><span class="sana-cat-m__emoji">📖</span><span class="sana-cat-m__name">{{ $isRtl ? 'عربي' : 'Arabic' }}</span></a>
      <a href="{{ $coursesUrl }}" class="sana-cat-m" style="background:linear-gradient(145deg,#FFF6D6,#FFE08A)"><span class="sana-cat-m__emoji">☪️</span><span class="sana-cat-m__name">{{ $isRtl ? 'قرآن وتجويد' : 'Quran' }}</span></a>
      <a href="{{ $coursesUrl }}" class="sana-cat-m" style="background:linear-gradient(145deg,#DBEAFE,#93C5FD)"><span class="sana-cat-m__emoji">🌍</span><span class="sana-cat-m__name">{{ $isRtl ? 'إنجليزي' : 'English' }}</span></a>
      <a href="{{ $coursesUrl }}" class="sana-cat-m" style="background:linear-gradient(145deg,#D1FAE5,#6EE7B7)"><span class="sana-cat-m__emoji">💼</span><span class="sana-cat-m__name">{{ $isRtl ? 'أعمال' : 'Business' }}</span></a>
      <a href="{{ $coursesUrl }}" class="sana-cat-m" style="background:linear-gradient(145deg,#FFEDD5,#FDBA74)"><span class="sana-cat-m__emoji">🎓</span><span class="sana-cat-m__name">IELTS</span></a>
    </div>
  </div>
</section>

{{-- ===== courses ===== --}}
<section class="sana-section sana-section--white" id="courses">
  <div class="sana-container">
    <div class="sana-head-row sana-reveal">
      <div class="sana-head">
        <h2 class="sana-head__title">{{ $isRtl ? 'برامج' : 'Featured' }} <span class="hl">{{ $isRtl ? 'مميزة' : 'programs' }}</span></h2>
        <span class="sana-head__line"></span>
      </div>
      <a href="{{ $coursesUrl }}" class="sana-link-more">{{ $isRtl ? 'عرض الكل' : 'View all' }} <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}"></i></a>
    </div>
    <div class="sana-courses-m">
      @forelse($featuredList as $course)
        @php
          $thumb = $course->thumbnail_url ?: $booksImg;
          $url = route('public.course.show', $course->id);
          $instName = $course->instructor->name ?? ($isRtl ? 'معلّم' : 'Tutor');
          $initial = mb_substr($instName, 0, 1) ?: 'م';
          $catName = $course->courseCategory->name ?? ($course->category->name ?? ($isRtl ? 'برنامج' : 'Program'));
        @endphp
        <article class="sana-course-card sana-course-card--featured sana-reveal">
          <div class="sana-course-card__media">
            <a href="{{ $url }}" class="sana-course-card__img-link" tabindex="-1" aria-hidden="true">
              <img src="{{ $thumb }}" alt="{{ $course->title }}" loading="lazy">
            </a>
            <div class="sana-course-card__shine" aria-hidden="true"></div>
            <div class="sana-course-card__badges">
              <span class="sana-course-card__badge sana-course-card__badge--subject">{{ $catName }}</span>
              @if(!empty($course->level))
                <span class="sana-course-card__badge sana-course-card__badge--level">{{ $course->level }}</span>
              @endif
            </div>
          </div>
          <div class="sana-course-card__body">
            <h3 class="sana-course-card__title"><a href="{{ $url }}">{{ $course->title }}</a></h3>
            <p class="sana-course-card__desc">{{ \Illuminate\Support\Str::limit(strip_tags($course->description ?? ($isRtl ? 'برنامج لغوي مباشر.' : 'Live language program.')), 90) }}</p>
            <div class="sana-course-card__instructor">
              <span class="sana-course-card__avatar sana-course-card__avatar--initial">{{ $initial }}</span>
              <div class="sana-course-card__instructor-info">
                <span class="sana-course-card__instructor-label">{{ $isRtl ? 'المعلّم' : 'Tutor' }}</span>
                <span class="sana-course-card__instructor-name">{{ $instName }}</span>
              </div>
            </div>
          </div>
        </article>
      @empty
        <article class="sana-course-card sana-course-card--featured sana-reveal">
          <div class="sana-course-card__media">
            <a href="{{ $coursesUrl }}" class="sana-course-card__img-link" tabindex="-1" aria-hidden="true">
              <img src="{{ $booksImg }}" alt="" loading="lazy">
            </a>
            <div class="sana-course-card__shine" aria-hidden="true"></div>
            <div class="sana-course-card__badges">
              <span class="sana-course-card__badge sana-course-card__badge--subject">{{ $isRtl ? 'قريباً' : 'Soon' }}</span>
            </div>
          </div>
          <div class="sana-course-card__body">
            <h3 class="sana-course-card__title"><a href="{{ $coursesUrl }}">{{ $isRtl ? 'البرامج قريباً' : 'Programs coming soon' }}</a></h3>
            <p class="sana-course-card__desc">{{ $isRtl ? 'احجز تقييم مستوى مجاني لنرشّح لك المسار المناسب.' : 'Book a free assessment so we can recommend the right path.' }}</p>
            <div class="sana-course-card__instructor">
              <span class="sana-course-card__avatar sana-course-card__avatar--initial">G</span>
              <div class="sana-course-card__instructor-info">
                <span class="sana-course-card__instructor-label">{{ $brand }}</span>
                <span class="sana-course-card__instructor-name">{{ $isRtl ? 'المنصة' : 'Platform' }}</span>
              </div>
            </div>
          </div>
        </article>
      @endforelse
    </div>
  </div>
</section>

{{-- ===== teachers ===== --}}
<section class="sana-section" id="instructors">
  <div class="sana-container">
    <div class="sana-head-row sana-reveal">
      <div class="sana-head">
        <h2 class="sana-head__title">{{ $isRtl ? 'معلّمو' : 'Meet our' }} <span class="hl">{{ $brand }}</span></h2>
        <span class="sana-head__line"></span>
      </div>
      <a href="{{ $instructorsUrl }}" class="sana-link-more">{{ $isRtl ? 'عرض الكل' : 'View all' }} <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}"></i></a>
    </div>
    <div class="sana-teachers-m">
      @forelse($instructorsSample as $inst)
        @php
          $name = is_object($inst) ? ($inst->name ?? '') : '';
          $id = is_object($inst) ? ($inst->id ?? null) : null;
          $href = $id ? route('public.instructors.show', $id) : $instructorsUrl;
          $initial = mb_substr($name, 0, 1) ?: 'م';
        @endphp
        <a href="{{ $href }}" class="sana-teacher-m sana-reveal" style="text-decoration:none;color:inherit">
          <div class="sana-teacher-m__ring"><span class="av">{{ $initial }}</span></div>
          <h3>{{ $name !== '' ? $name : ($isRtl ? 'معلّم على المنصة' : 'Platform tutor') }}</h3>
          <p class="role">{{ $isRtl ? 'حصص مباشرة · متابعة منتظمة' : 'Live sessions · steady coaching' }}</p>
          <span class="sana-teacher-m__book"><i class="fas fa-calendar-check"></i> {{ $isRtl ? 'عرض الملف' : 'View profile' }}</span>
        </a>
      @empty
        <a href="{{ $instructorsUrl }}" class="sana-teacher-m sana-reveal" style="text-decoration:none;color:inherit">
          <div class="sana-teacher-m__ring"><span class="av">G</span></div>
          <h3>{{ $brand }}</h3>
          <p class="role">{{ $isRtl ? 'المعلّمون قريباً' : 'Tutors coming soon' }}</p>
          <span class="sana-teacher-m__book"><i class="fas fa-calendar-check"></i> {{ $isRtl ? 'تصفّح القائمة' : 'Browse list' }}</span>
        </a>
      @endforelse
    </div>
  </div>
</section>

{{-- ===== certificates ===== --}}
<section class="sana-section sana-section--white" id="achievements">
  <div class="sana-container">
    <div class="sana-achieve-box sana-reveal">
      <div class="sana-achieve-box__glow sana-achieve-box__glow--1"></div>
      <div class="sana-achieve-box__glow sana-achieve-box__glow--2"></div>
      <div class="sana-achieve-box__inner">
        <div class="sana-achieve-box__content">
          <span class="sana-achieve-box__tag"><i class="fas fa-certificate"></i> {{ $isRtl ? 'شهادات إتمام · قابلة للتحقق' : 'Verifiable certificates' }}</span>
          <h2 class="sana-achieve-box__title">{{ $isRtl ? 'أنهِ المسار…' : 'Finish the path…' }} <span class="hl">{{ $isRtl ? 'واحتفل بالإنجاز' : 'celebrate the win' }}</span></h2>
          <p class="sana-achieve-box__desc">{{ $isRtl
            ? 'بعد إتمام المسار التعليمي تُصدَر شهادة رقمية باسمك — قابلة للتحقق والمشاركة.'
            : 'After completing a learning path, a digital certificate is issued in your name — verifiable and shareable.' }}</p>
          <ul class="sana-achieve-box__highlights">
            <li><i class="fas fa-check-circle"></i> {{ $isRtl ? 'شهادة رقمية بعد إتمام المسار' : 'Digital certificate on path completion' }}</li>
            <li><i class="fas fa-qrcode"></i> {{ $isRtl ? 'رمز تحقق ومشاركة آمنة' : 'Secure verify & share' }}</li>
            <li><i class="fas fa-shield-halved"></i> {{ $isRtl ? 'بهوية '.$brand.' الرسمية' : 'With official '.$brand.' branding' }}</li>
          </ul>
        </div>
        <div class="sana-achieve-box__visual" aria-hidden="true">
          <div class="sana-cert-mock">
            <div class="sana-cert-mock__corner sana-cert-mock__corner--tl"></div>
            <div class="sana-cert-mock__corner sana-cert-mock__corner--tr"></div>
            <div class="sana-cert-mock__corner sana-cert-mock__corner--bl"></div>
            <div class="sana-cert-mock__corner sana-cert-mock__corner--br"></div>
            <div class="sana-cert-mock__seal"><i class="fas fa-star"></i></div>
            <p class="sana-cert-mock__label">{{ $isRtl ? 'شهادة إتمام' : 'Certificate' }}</p>
            <h3 class="sana-cert-mock__brand">{{ $brand }}</h3>
            <div class="sana-cert-mock__line"></div>
            <p class="sana-cert-mock__to">{{ $isRtl ? 'تُمنح هذه الشهادة لـ' : 'Awarded to' }}</p>
            <p class="sana-cert-mock__name">{{ $isRtl ? 'الطالب المتميّز' : 'Outstanding student' }}</p>
            <p class="sana-cert-mock__course">{{ $isRtl ? 'لإتمام المسار التعليمي بنجاح' : 'For successfully completing the path' }}</p>
            <div class="sana-cert-mock__footer">
              <span><i class="fas fa-qrcode"></i> {{ $isRtl ? 'قابلة للتحقق' : 'Verifiable' }}</span>
              <span><i class="fas fa-shield-halved"></i> {{ $isRtl ? 'صادرة من المنصة' : 'Issued by platform' }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ===== FAQ ===== --}}
<section class="sana-section" id="faq">
  <div class="sana-container">
    <div class="sana-head sana-reveal">
      <h2 class="sana-head__title">{{ $isRtl ? 'الأسئلة' : 'FAQ' }} <span class="hl">{{ $isRtl ? 'الشائعة' : '' }}</span></h2>
      <span class="sana-head__line"></span>
    </div>
    <div class="sana-faq-m sana-reveal">
      <div class="sana-faq-m__visual">
        <img src="{{ $faqBoyImg }}" alt="">
        <span class="bubble bubble--1">🤔</span>
        <span class="bubble bubble--2">💡</span>
      </div>
      <div class="sana-faq" id="sana-faq">
        <div class="sana-faq-item is-open">
          <button type="button" class="sana-faq-q">{{ $isRtl ? 'ما فكرة Glottical؟' : 'What is Glottical about?' }} <i class="fas fa-chevron-down"></i></button>
          <div class="sana-faq-a">{{ $isRtl ? 'منصة لغات تعتمد على الحصص المباشرة والممارسة الحية: «اللغة لا تُحفظ… اللغة تُعاش» — مع مسارات عربي/إسلامي وإنجليزي.' : 'A language platform built on live sessions and real practice: language isn’t memorized — it’s lived. Arabic/Islamic and English tracks.' }}</div>
        </div>
        <div class="sana-faq-item">
          <button type="button" class="sana-faq-q">{{ $isRtl ? 'كيف أبدأ؟' : 'How do I start?' }} <i class="fas fa-chevron-down"></i></button>
          <div class="sana-faq-a">{{ $isRtl ? 'احجز تقييم مستوى مجاني — نحدد مستواك ونرشّح المسار والمعلّم أو المجموعة المناسبة قبل أي التزام.' : 'Book a free level assessment — we place you and recommend the right path, tutor, or group before any commitment.' }}</div>
        </div>
        <div class="sana-faq-item">
          <button type="button" class="sana-faq-q">{{ $isRtl ? 'هل الحصص فردية أم مجموعات؟' : 'Are sessions 1:1 or groups?' }} <i class="fas fa-chevron-down"></i></button>
          <div class="sana-faq-a">{{ $isRtl ? 'الاثنان متاحان: حصص فردية ومجموعات جماعية بدفعات محدودة العدد.' : 'Both: private 1:1 sessions and group cohorts with capped seats.' }}</div>
        </div>
        <div class="sana-faq-item">
          <button type="button" class="sana-faq-q">{{ $isRtl ? 'كيف أتابع تقدّم ابني؟' : 'How do parents track progress?' }} <i class="fas fa-chevron-down"></i></button>
          <div class="sana-faq-a">{{ $isRtl ? 'لوحة ولي الأمر تعرض الحضور والتقدّم والملخصات بعد الحصص بوضوح.' : 'The parent dashboard shows attendance, progress, and clear post-session summaries.' }}</div>
        </div>
        <div class="sana-faq-item">
          <button type="button" class="sana-faq-q">{{ $isRtl ? 'هل توجد شهادات؟' : 'Do you issue certificates?' }} <i class="fas fa-chevron-down"></i></button>
          <div class="sana-faq-a">{{ $isRtl ? 'نعم — شهادات إتمام رقمية قابلة للتحقق بعد إنجاز المسار.' : 'Yes — digital verifiable certificates after path completion.' }}</div>
        </div>
      </div>
    </div>
  </div>
</section>

</main>
