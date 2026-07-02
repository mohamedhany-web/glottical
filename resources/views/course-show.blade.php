@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $thumbUrl = $course->thumbnail_url;
    $introVideoUrl = trim((string)($course->video_url ?? ''));
    $introEmbedUrl = \App\Helpers\VideoHelper::getEmbedUrl($introVideoUrl);
    $introDirectVideo = \App\Helpers\VideoHelper::getDirectVideoUrl($introVideoUrl);
    $categoryDisplay = $course->courseCategory?->name ?? __('public.course_category_not_set');
    $isMonthly = $course->isMonthlyBilling();
    $checkoutPrice = $course->effectiveCheckoutPrice();
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    @php
        $courseOgImg  = $thumbUrl ?? asset('images/og-image.jpg');
        $courseDesc   = Str::limit(strip_tags($course->description ?? ''), 160);
        $courseTitle  = ($course->title ?? __('public.course_detail_title')) . ' | Glottical';
        $courseUrl    = url('/course/' . ($course->id ?? ''));
    @endphp
    <title>{{ $courseTitle }}</title>
    <meta name="title"       content="{{ $courseTitle }}">
    <meta name="description" content="{{ $courseDesc }}">
    <meta name="keywords"    content="{{ $course->title ?? 'كورس' }}, تعلم أونلاين, كورسات عربية, Glottical, {{ $categoryDisplay }}">
    <meta name="author"      content="{{ ($course->instructor->name ?? null) ?? 'Glottical' }}">
    <meta name="robots"      content="index, follow, max-image-preview:large, max-snippet:-1">
    <meta name="theme-color" content="#0d1528">
    <link rel="canonical"    href="{{ $courseUrl }}">
    <link rel="alternate" hreflang="ar"        href="{{ $courseUrl }}?lang=ar">
    <link rel="alternate" hreflang="en"        href="{{ $courseUrl }}?lang=en">
    <link rel="alternate" hreflang="x-default" href="{{ $courseUrl }}">
    <!-- Open Graph -->
    <meta property="og:type"             content="article">
    <meta property="og:url"              content="{{ $courseUrl }}">
    <meta property="og:title"            content="{{ $courseTitle }}">
    <meta property="og:description"      content="{{ $courseDesc }}">
    <meta property="og:image"            content="{{ $courseOgImg }}">
    <meta property="og:image:alt"        content="{{ $course->title ?? 'كورس' }}">
    <meta property="og:image:width"      content="1200">
    <meta property="og:image:height"     content="630">
    <meta property="og:locale"           content="{{ $locale === 'ar' ? 'ar_AR' : 'en_US' }}">
    <meta property="og:site_name"        content="Glottical">
    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:site"        content="@Glottical">
    <meta name="twitter:url"         content="{{ $courseUrl }}">
    <meta name="twitter:title"       content="{{ $courseTitle }}">
    <meta name="twitter:description" content="{{ $courseDesc }}">
    <meta name="twitter:image"       content="{{ $courseOgImg }}">
    <meta name="twitter:image:alt"   content="{{ $course->title ?? 'كورس' }}">
    @include('partials.seo-jsonld', ['jsonldType' => 'course', 'course' => $course])
    @include('partials.favicon-links')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    acad: {
                        blue: '#0B3D91',
                        blueDark: '#072a66',
                        blueSoft: '#E8EEF8',
                        cyan: '#00A3C4',
                        yellow: '#F5B800',
                        yellowSoft: '#FFF8E1',
                        ink: '#1a2d4d',
                        navy: '#0d1528',
                        navyMid: '#1a2d4d',
                        neon: '#00d4ff',
                    },
                    navy: { 50:'#f0f4ff',100:'#dbe4ff',200:'#bac8ff',300:'#91a7ff',400:'#748ffc',500:'#5c7cfa',600:'#4c6ef5',700:'#4263eb',800:'#3b5bdb',900:'#364fc7',950:'#283593' },
                    brand: { 500:'#FB5607',600:'#E04D00' },
                    mx: { navy:'#283593', indigo:'#1F2A7A', orange:'#FB5607' },
                },
                fontFamily: {
                    sans: ['Cairo','Tajawal','IBM Plex Sans Arabic','system-ui','sans-serif'],
                    heading: ['Cairo','Tajawal','IBM Plex Sans Arabic','sans-serif'],
                    body: ['Cairo','IBM Plex Sans Arabic','Tajawal','sans-serif'],
                },
            },
        },
    };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        [x-cloak]{display:none!important}
        *{font-family:'Cairo','IBM Plex Sans Arabic','Tajawal',system-ui,sans-serif}
        h1,h2,h3,h4,h5,h6,.font-heading{font-family:'Cairo','Tajawal','IBM Plex Sans Arabic',sans-serif}
        html{scroll-behavior:smooth;overflow-x:hidden!important}
        body.page-course-public{overflow-x:hidden!important;background:linear-gradient(180deg,#eef2f8 0%,#f5f7fb 40%,#fafbfd 100%);min-height:100vh;display:flex;flex-direction:column;color:#1e293b}
        body.page-course-public>*{flex-shrink:0}
        .pattern-dots-course{background-image:radial-gradient(circle at 1px 1px,rgba(11,61,145,.06) 1px,transparent 0);background-size:22px 22px}

        .container-1200{max-width:1280px;margin-inline:auto;padding-inline:clamp(16px,4vw,28px)}
        .reveal{opacity:0;transform:translateY(28px);transition:opacity .65s ease,transform .65s ease}
        .reveal.revealed{opacity:1;transform:translateY(0)}
        .stagger-1{transition-delay:.05s}.stagger-2{transition-delay:.1s}.stagger-3{transition-delay:.15s}.stagger-4{transition-delay:.2s}

        .glass-panel-light{background:rgba(255,255,255,.92);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);border:1px solid rgba(11,61,145,.08)}
        .text-gradient-acad{background:linear-gradient(135deg,#0B3D91 0%,#00A3C4 55%,#F5B800 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
        .btn-primary{position:relative;overflow:hidden;transition:all .3s cubic-bezier(.16,1,.3,1)}
        .btn-primary:hover{transform:translateY(-2px)}
        .btn-outline{transition:all .25s ease}
        .btn-outline:hover{transform:translateY(-1px);box-shadow:0 8px 24px -12px rgba(11,61,145,.2)}
        .card-hover{transition:all .35s cubic-bezier(.16,1,.3,1)}
        .card-hover:hover{transform:translateY(-4px);box-shadow:0 20px 44px -18px rgba(11,61,145,.12)}
        #scroll-progress{position:fixed;top:0;left:0;width:0%;height:3px;background:linear-gradient(90deg,#F5B800,#00A3C4);z-index:99999;transition:width .1s linear}
        .line-clamp-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
        .line-clamp-3{display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
        @media(max-width:768px){.reveal{transition-duration:.45s}.stagger-1,.stagger-2,.stagger-3,.stagger-4{transition-delay:0s}}
    </style>
</head>
<body class="page-course-public antialiased font-body">
    <div id="scroll-progress"></div>
    @include('components.unified-navbar')
    <style>.navbar-spacer{display:block}</style>

    <main class="flex-1">
        {{-- Flash messages --}}
        @foreach(['success' => 'emerald', 'info' => 'sky', 'error' => 'red'] as $type => $color)
            @if(session($type))
            <div class="max-w-5xl mx-auto px-5 sm:px-8 pt-20 sm:pt-24 pb-2" x-data="{s:true}" x-show="s" @if($type!=='error') x-init="setTimeout(()=>s=false,6000)" @endif>
                <div class="rounded-2xl border border-{{ $color }}-200/80 bg-white/95 backdrop-blur-sm px-5 py-4 flex items-center gap-3 shadow-sm">
                    <i class="fas fa-{{ $type==='success'?'check-circle':($type==='info'?'info-circle':'exclamation-circle') }} text-{{ $color }}-600"></i>
                    <p class="text-{{ $color }}-800 font-semibold flex-1">{{ session($type) }}</p>
                    <button @click="s=false" class="text-{{ $color }}-600 hover:text-{{ $color }}-800"><i class="fas fa-times"></i></button>
                </div>
            </div>
            @endif
        @endforeach

        {{-- HERO: شريط داكن متناسق مع الرئيسية، ثم محتوى فاتح أسفله --}}
        <section class="relative -mt-14 sm:-mt-[60px] pt-16 sm:pt-20 pb-10 sm:pb-14 overflow-hidden text-white">
            @if($thumbUrl)
                <div class="absolute inset-0 bg-cover bg-center scale-105" style="background-image:url('{{ $thumbUrl }}')"></div>
            @else
                <div class="absolute inset-0 bg-gradient-to-br from-acad-blue to-acad-navy"></div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-acad-navy via-acad-navy/88 to-acad-navy/55"></div>
            <div class="absolute inset-0 opacity-[0.18] pattern-dots-course pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-acad-yellow/50 to-transparent pointer-events-none"></div>

            <div class="container-1200 relative z-10">
                <nav class="reveal text-sm text-white/60 mb-6 sm:mb-8 flex items-center gap-2 flex-wrap pt-2">
                    <a href="{{ url('/') }}" class="hover:text-acad-yellow transition-colors">{{ __('public.home') }}</a>
                    <i class="fas fa-chevron-{{ $isRtl?'left':'right' }} text-[8px] text-white/35"></i>
                    <a href="{{ route('public.courses') }}" class="hover:text-acad-yellow transition-colors">{{ __('public.courses') }}</a>
                    <i class="fas fa-chevron-{{ $isRtl?'left':'right' }} text-[8px] text-white/35"></i>
                    <span class="text-white font-semibold">{{ Str::limit($course->title ?? '', 48) }}</span>
                </nav>

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-12 items-start">
                    <div class="lg:col-span-3 reveal">
                        @if($course->is_featured ?? false)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-acad-yellow/95 text-acad-blue text-xs font-black mb-4 shadow-lg shadow-black/20">
                                <i class="fas fa-star"></i>
                                {{ __('public.featured_course_badge') }}
                            </span>
                        @endif

                        <h1 class="font-heading text-3xl sm:text-4xl lg:text-[2.65rem] font-black text-white leading-[1.12] mb-4 drop-shadow-sm">
                            {{ $course->title ?? __('public.course_title_fallback') }}
                        </h1>

                        <p class="text-white/75 text-base sm:text-lg leading-relaxed line-clamp-3 mb-7 max-w-2xl">
                            {{ $course->description ?? __('public.course_desc_fallback') }}
                        </p>

                        <div class="flex flex-wrap gap-2 mb-8">
                            @php
                            $heroBadges = [
                                ['icon'=>'fa-chalkboard-teacher','label'=>($course->lessons_count ?? 0).' '.__('public.lecture_single')],
                                ['icon'=>'fa-clock','label'=>($course->duration_hours ?? 0).' '.__('public.hours')],
                                ['icon'=>'fa-folder-open','label'=>$categoryDisplay],
                            ];
                            @endphp
                            @foreach($heroBadges as $badge)
                            <div class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white/10 border border-white/15 text-white text-sm font-semibold backdrop-blur-sm">
                                <i class="fas {{ $badge['icon'] }} text-acad-yellow"></i>
                                <span>{{ $badge['label'] }}</span>
                            </div>
                            @endforeach
                        </div>

                        @if($course->instructor)
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/15 flex items-center justify-center">
                                    <i class="fas fa-chalkboard-teacher text-acad-cyan"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-white/55 font-medium">{{ __('public.instructor_label') }}</p>
                                    @if(\App\Models\InstructorProfile::where('user_id', $course->instructor->id)->where('status', 'approved')->exists())
                                        <a href="{{ route('public.instructors.show', $course->instructor) }}" class="text-white font-bold hover:text-acad-yellow transition-colors">{{ $course->instructor->name }}</a>
                                    @else
                                        <span class="text-white font-bold">{{ $course->instructor->name }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="flex flex-wrap gap-3">
                            @auth
                                @if($isEnrolled ?? false)
                                    <a href="{{ route('my-courses.show', $course) }}" class="inline-flex items-center gap-2.5 bg-acad-yellow text-acad-blue px-7 py-3.5 rounded-xl font-black shadow-lg shadow-black/25 hover:brightness-105 transition text-base">
                                        <i class="fas fa-play-circle"></i> {{ __('public.start_learning_now') }}
                                    </a>
                                @elseif($checkoutPrice > 0 && !($course->is_free ?? false))
                                    <a href="{{ route('public.course.checkout', $course->id) }}" class="inline-flex items-center gap-2.5 bg-acad-yellow text-acad-blue px-7 py-3.5 rounded-xl font-black shadow-lg shadow-black/25 hover:brightness-105 transition text-base">
                                        <i class="fas fa-shopping-cart"></i> {{ __('public.buy_now') }}
                                    </a>
                                @else
                                    <form action="{{ route('public.course.enroll.free', $course->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-2.5 bg-emerald-500 hover:bg-emerald-600 text-white px-7 py-3.5 rounded-xl font-black shadow-lg transition text-base cursor-pointer">
                                            <i class="fas fa-gift"></i> {{ __('public.register_free') }}
                                        </button>
                                    </form>
                                @endif
                            @endauth
                            @guest
                                @if($checkoutPrice > 0 && !($course->is_free ?? false))
                                    <a href="{{ route('register', ['redirect' => route('public.course.checkout', $course->id)]) }}" class="inline-flex items-center gap-2.5 bg-acad-yellow text-acad-blue px-7 py-3.5 rounded-xl font-black shadow-lg shadow-black/25 hover:brightness-105 transition text-base">
                                        <i class="fas fa-shopping-cart"></i> {{ __('public.buy_now') }}
                                    </a>
                                @else
                                    <a href="{{ route('register', ['redirect' => route('public.course.show', $course->id)]) }}" class="inline-flex items-center gap-2.5 bg-emerald-500 hover:bg-emerald-600 text-white px-7 py-3.5 rounded-xl font-black shadow-lg transition text-base">
                                        <i class="fas fa-gift"></i> {{ __('public.register_free') }}
                                    </a>
                                @endif
                            @endguest
                            <a href="{{ route('public.courses') }}" class="inline-flex items-center gap-2 border-2 border-white/25 text-white hover:bg-white/10 px-6 py-3.5 rounded-xl font-bold text-base transition">
                                <i class="fas fa-arrow-{{ $isRtl?'right':'left' }} text-sm"></i>
                                {{ __('public.all_courses') }}
                            </a>
                        </div>
                    </div>

                    <div class="lg:col-span-2 reveal stagger-2">
                        @if($introEmbedUrl)
                        <div class="rounded-2xl overflow-hidden border border-white/20 shadow-2xl shadow-black/40 ring-1 ring-white/10 bg-acad-navy">
                            <div class="relative w-full aspect-video max-h-[min(70vh,520px)]">
                                <iframe src="{{ $introEmbedUrl }}" title="{{ __('public.course_intro_video') }}"
                                    class="absolute inset-0 w-full h-full"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen; web-share"
                                    allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
                            </div>
                        </div>
                        @elseif($introDirectVideo)
                        <div class="rounded-2xl overflow-hidden border border-white/20 shadow-2xl shadow-black/40 bg-black ring-1 ring-white/10">
                            <div class="relative w-full aspect-video max-h-[min(70vh,520px)]">
                                <video src="{{ $introDirectVideo }}" controls playsinline webkit-playsinline preload="metadata" poster="{{ $thumbUrl }}" class="absolute inset-0 w-full h-full object-contain bg-black">
                                    {{ __('public.course_intro_video_unsupported') }}
                                </video>
                            </div>
                        </div>
                        @elseif($thumbUrl)
                        <div class="rounded-2xl overflow-hidden border border-white/20 shadow-2xl shadow-black/40 aspect-video bg-acad-navy ring-1 ring-white/10">
                            <img src="{{ $thumbUrl }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- تفاصيل الكورس — سطح فاتح متناسق مع الرئيسية (بطاقات بيضاء وهوامش ملونة خفيفة) --}}
        <section class="relative py-16 md:py-24 border-t border-acad-blue/10">
            <div class="absolute inset-0 pointer-events-none opacity-50 pattern-dots-course"></div>
            <div class="container-1200 relative z-10">

                <div class="reveal grid grid-cols-2 sm:grid-cols-4 gap-4 mb-14">
                    @php
                    $infoCards = [
                        ['icon'=>'fa-clock','label'=>__('public.duration'),'value'=>($course->duration_hours ?? 0).' '.__('public.hours')],
                        ['icon'=>'fa-chalkboard-teacher','label'=>__('public.lectures_count_label'),'value'=>($course->lessons_count ?? 0).' '.__('public.lecture_single')],
                        ['icon'=>'fa-folder-open','label'=>__('public.course_category_label'),'value'=>$categoryDisplay],
                        ['icon'=>'fa-book','label'=>__('public.subject_label'),'value'=>$course->academicSubject->name ?? __('public.course_category_not_set')],
                    ];
                    @endphp
                    @foreach($infoCards as $idx => $ic)
                    <div class="card-hover rounded-2xl glass-panel-light p-5 sm:p-6 shadow-sm text-center hover:border-acad-cyan/25">
                        <div class="w-12 h-12 rounded-xl bg-acad-blueSoft flex items-center justify-center mx-auto mb-3">
                            <i class="fas {{ $ic['icon'] }} text-acad-blue text-xl"></i>
                        </div>
                        <p class="font-heading text-xl sm:text-2xl font-black text-acad-ink mb-1">{{ $ic['value'] }}</p>
                        <p class="text-xs text-slate-500 font-medium">{{ $ic['label'] }}</p>
                    </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-10">
                    {{-- Main content --}}
                    <div class="lg:col-span-2 space-y-8">
                        {{-- About --}}
                        <div class="reveal card-hover rounded-2xl glass-panel-light p-6 sm:p-8 shadow-sm hover:border-acad-cyan/20">
                            <div class="flex items-center gap-3 mb-5">
                                <div class="w-11 h-11 rounded-xl bg-acad-blueSoft flex items-center justify-center"><i class="fas fa-info-circle text-acad-blue text-xl"></i></div>
                                <h2 class="font-heading text-2xl font-black text-acad-ink">{{ __('public.about_course') }}</h2>
                            </div>
                            <div class="text-slate-600 leading-relaxed text-base">
                                <p>{{ $course->description ?? __('public.course_desc_fallback') }}</p>
                                @if($course->objectives)
                                <div class="mt-6">
                                    <h3 class="font-heading text-lg font-bold text-acad-ink mb-3">{{ __('public.course_objectives') }}</h3>
                                    <div class="bg-gradient-to-br from-acad-blueSoft/90 to-sky-50/50 rounded-2xl p-6 border border-acad-blue/10">
                                        <p class="whitespace-pre-line text-slate-700">{{ $course->objectives }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- What you'll learn --}}
                        @if($course->what_you_learn)
                        <div class="reveal stagger-1 card-hover rounded-2xl glass-panel-light p-6 sm:p-8 shadow-sm hover:border-emerald-200/60">
                            <div class="flex items-center gap-3 mb-5">
                                <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center"><i class="fas fa-graduation-cap text-emerald-600 text-xl"></i></div>
                                <h2 class="font-heading text-2xl font-black text-acad-ink">{{ __('public.what_you_learn') }}</h2>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach(array_filter(explode("\n", $course->what_you_learn)) as $point)
                                <div class="flex items-start gap-3 p-4 rounded-xl bg-gradient-to-br from-emerald-50/80 to-acad-blueSoft/40 border border-emerald-100/70 hover:border-acad-cyan/25 transition-colors">
                                    <i class="fas fa-check-circle text-emerald-600 mt-0.5 flex-shrink-0"></i>
                                    <span class="text-slate-700 text-sm leading-relaxed">{{ trim($point) }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Requirements --}}
                        @if($course->requirements)
                        <div class="reveal stagger-2 card-hover rounded-2xl glass-panel-light p-6 sm:p-8 shadow-sm hover:border-amber-200/70">
                            <div class="flex items-center gap-3 mb-5">
                                <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center"><i class="fas fa-list-check text-amber-600 text-xl"></i></div>
                                <h2 class="font-heading text-2xl font-black text-acad-ink">{{ __('public.requirements') }}</h2>
                            </div>
                            <div class="bg-gradient-to-br from-amber-50/70 to-slate-50/40 rounded-2xl p-6 border border-amber-100/60">
                                <p class="text-slate-700 whitespace-pre-line leading-relaxed">{{ $course->requirements }}</p>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Sidebar --}}
                    <div class="lg:col-span-1">
                        <div class="reveal sticky top-24 space-y-6">
                            <div class="card-hover rounded-2xl glass-panel-light shadow-lg overflow-hidden border border-acad-blue/10">
                                <div class="bg-gradient-to-l from-acad-blue via-acad-blue to-acad-blueDark p-5 text-center relative overflow-hidden">
                                    <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_80%_0%,rgba(0,212,255,.5),transparent_45%)]"></div>
                                    <div class="relative">
                                    @if($checkoutPrice > 0 && !($course->is_free ?? false))
                                        @if($isMonthly)
                                            <div class="text-3xl font-black text-white tabular-nums">{{ number_format($checkoutPrice, 0) }} <span class="text-lg font-medium text-white/80">{{ __('public.currency_egp') }} / {{ __('public.per_month') }}</span></div>
                                            @if($course->isOneToOne() && $course->instructor)
                                                <p class="text-xs text-white/70 mt-2">{{ __('public.one_to_one_with') }} {{ $course->instructor->name }}</p>
                                            @endif
                                        @elseif($course->hasPromotionalPrice())
                                            <div class="text-sm text-white/80 line-through mb-1 tabular-nums">{{ number_format($course->listPriceAmount(), 0) }} {{ __('public.currency_egp') }}</div>
                                            <div class="text-3xl font-black text-white tabular-nums">{{ number_format($checkoutPrice, 0) }} <span class="text-lg font-medium text-white/80">{{ __('public.currency_egp') }}</span></div>
                                        @else
                                            <div class="text-3xl font-black text-white tabular-nums">{{ number_format($checkoutPrice, 0) }} <span class="text-lg font-medium text-white/80">{{ __('public.currency_egp') }}</span></div>
                                        @endif
                                    @else
                                        <div class="text-2xl font-black text-white flex items-center justify-center gap-2"><i class="fas fa-gift text-xl"></i>{{ __('public.free_price') }}</div>
                                    @endif
                                    </div>
                                </div>
                                <div class="p-6">
                                    <dl class="space-y-3 mb-6">
                                        <div class="flex justify-between items-center p-3 bg-acad-blueSoft/50 rounded-xl text-sm border border-acad-blue/5">
                                            <span class="text-slate-500 flex items-center gap-2"><i class="fas fa-clock text-acad-cyan"></i> {{ __('public.duration') }}</span>
                                            <span class="font-bold text-acad-ink">{{ $course->duration_hours ?? 0 }} {{ __('public.hours') }}</span>
                                        </div>
                                        <div class="flex justify-between items-center p-3 bg-acad-blueSoft/50 rounded-xl text-sm border border-acad-blue/5">
                                            <span class="text-slate-500 flex items-center gap-2"><i class="fas fa-chalkboard-teacher text-acad-blue"></i> {{ __('public.lectures_count_label') }}</span>
                                            <span class="font-bold text-acad-ink">{{ $course->lessons_count ?? 0 }}</span>
                                        </div>
                                        <div class="flex justify-between items-center p-3 bg-acad-blueSoft/50 rounded-xl text-sm border border-acad-blue/5">
                                            <span class="text-slate-500 flex items-center gap-2"><i class="fas fa-folder-open text-acad-cyan"></i> {{ __('public.course_category_label') }}</span>
                                            <span class="font-bold text-acad-ink">{{ $categoryDisplay }}</span>
                                        </div>
                                    </dl>
                                    @auth
                                        @if($isEnrolled ?? false)
                                            <a href="{{ route('my-courses.show', $course) }}" class="block w-full text-center py-3.5 rounded-xl bg-acad-yellow text-acad-blue font-black shadow-md hover:brightness-105 transition">
                                                <i class="fas fa-play-circle {{ $isRtl?'ml-2':'mr-2' }}"></i>{{ __('public.start_learning_now') }}
                                            </a>
                                        @elseif($checkoutPrice > 0 && !($course->is_free ?? false))
                                            <a href="{{ route('public.course.checkout', $course->id) }}" class="block w-full text-center py-3.5 rounded-xl bg-acad-yellow text-acad-blue font-black shadow-md hover:brightness-105 transition">
                                                <i class="fas fa-shopping-cart {{ $isRtl?'ml-2':'mr-2' }}"></i>{{ __('public.buy_now') }}
                                            </a>
                                        @else
                                            <form action="{{ route('public.course.enroll.free', $course->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="block w-full text-center py-3.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-black shadow-md cursor-pointer transition">
                                                    <i class="fas fa-gift {{ $isRtl?'ml-2':'mr-2' }}"></i>{{ __('public.register_free') }}
                                                </button>
                                            </form>
                                        @endif
                                    @endauth
                                    @guest
                                        @if($checkoutPrice > 0 && !($course->is_free ?? false))
                                            <a href="{{ route('register', ['redirect' => route('public.course.checkout', $course->id)]) }}" class="block w-full text-center py-3.5 rounded-xl bg-acad-yellow text-acad-blue font-black shadow-md hover:brightness-105 transition">
                                                <i class="fas fa-shopping-cart {{ $isRtl?'ml-2':'mr-2' }}"></i>{{ __('public.buy_now') }}
                                            </a>
                                        @else
                                            <a href="{{ route('register', ['redirect' => route('public.course.show', $course->id)]) }}" class="block w-full text-center py-3.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-black shadow-md transition">
                                                <i class="fas fa-gift {{ $isRtl?'ml-2':'mr-2' }}"></i>{{ __('public.register_free') }}
                                            </a>
                                        @endif
                                    @endguest
                                </div>
                            </div>

                            @if(isset($relatedCourses) && $relatedCourses->isNotEmpty())
                            <div class="rounded-2xl glass-panel-light p-6 shadow-sm border border-acad-blue/8">
                                <h3 class="font-heading text-lg font-bold text-acad-ink mb-4 flex items-center gap-2">
                                    <i class="fas fa-bookmark text-acad-cyan"></i>
                                    كورسات ذات صلة
                                </h3>
                                <div class="space-y-3">
                                    @foreach($relatedCourses->take(3) as $related)
                                        @php $relThumb = $related->thumbnail ? str_replace('\\','/', $related->thumbnail) : null; @endphp
                                        <a href="{{ route('public.course.show', $related->id) }}" class="flex gap-3 p-3 rounded-xl border border-slate-200/80 hover:border-acad-yellow/45 hover:shadow-md transition-all duration-300 group bg-white/60">
                                            <div class="w-16 h-16 flex-shrink-0 rounded-xl bg-gradient-to-br from-acad-blue to-acad-navy overflow-hidden flex items-center justify-center">
                                                @if($relThumb)
                                                    <img src="{{ storage_asset($relThumb) }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                                @else
                                                    <i class="fas fa-book text-white/80 text-lg"></i>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-bold text-acad-ink text-sm group-hover:text-acad-blue transition-colors line-clamp-2 leading-snug">{{ $related->title }}</h4>
                                                <div class="mt-1 block text-start">
                                                    <x-advanced-course-card-price :course="$related" size="sm" class="!items-start" />
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- CTA — فاتح مع لمسة ألوان الأكاديمية --}}
        <section class="relative py-16 md:py-20 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-white via-acad-blueSoft/25 to-acad-blueSoft/40 pointer-events-none"></div>
            <div class="container-1200 text-center reveal relative z-10">
                <h2 class="font-heading text-3xl sm:text-4xl md:text-[2.75rem] font-black mb-4 leading-tight text-gradient-acad">
                    جاهز للانطلاق في هذا الكورس؟
                </h2>
                <p class="text-lg text-slate-600 leading-relaxed mb-10 font-medium max-w-2xl mx-auto">
                    سجّل الآن وابدأ التعلم بخطوات واضحة وتجربة احترافية متكاملة.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @auth
                        <a href="{{ route('my-courses.show', $course) }}" class="inline-flex items-center justify-center gap-3 bg-acad-yellow text-acad-blue font-black text-lg px-8 py-4 rounded-xl shadow-lg shadow-acad-blue/15 hover:brightness-105 transition">
                            {{ __('public.start_learning_now') }} <i class="fas fa-arrow-{{ $isRtl?'left':'right' }} text-sm"></i>
                        </a>
                    @endauth
                    @guest
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-3 bg-acad-yellow text-acad-blue font-black text-lg px-8 py-4 rounded-xl shadow-lg shadow-acad-blue/15 hover:brightness-105 transition">
                            {{ __('public.register_free_now') }} <i class="fas fa-arrow-{{ $isRtl?'left':'right' }} text-sm"></i>
                        </a>
                    @endguest
                    <a href="{{ route('public.courses') }}" class="btn-outline inline-flex items-center justify-center gap-3 bg-white/90 border-2 border-acad-blue/15 hover:border-acad-cyan/40 text-acad-ink font-semibold text-lg px-8 py-4 rounded-xl backdrop-blur-sm">
                        {{ __('public.all_courses') }} <i class="fas fa-arrow-{{ $isRtl?'left':'right' }} text-sm"></i>
                    </a>
                </div>
            </div>
        </section>
    </main>

    @include('components.unified-footer')
    <script>
    (function(){
        function p(){var s=window.pageYOffset||document.documentElement.scrollTop,h=document.documentElement.scrollHeight-window.innerHeight,b=document.getElementById('scroll-progress');if(b)b.style.width=(h>0?(s/h)*100:0)+'%';}
        window.addEventListener('scroll',p,{passive:true});
        function r(){var t=document.querySelectorAll('.reveal');if(!t.length)return;var o=new IntersectionObserver(function(e){e.forEach(function(n){if(n.isIntersecting){n.target.classList.add('revealed');o.unobserve(n.target);}});},{threshold:.08,rootMargin:'0px 0px -40px 0px'});t.forEach(function(el){o.observe(el);});}
        if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',r);else r();
    })();
    </script>
</body>
</html>
