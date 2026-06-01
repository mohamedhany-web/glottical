@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title>{{ __('public.instructors_page_title') }} - {{ __('public.site_suffix') }}</title>
    <meta name="title" content="{{ __('public.instructors_page_title') }} - {{ __('public.site_suffix') }}">
    <meta name="description" content="{{ __('public.instructors_subtitle') }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">
    <meta name="theme-color" content="#0d1528">
    <link rel="canonical" href="{{ url('/instructors') }}">
    <link rel="alternate" hreflang="ar" href="{{ url('/instructors') }}?lang=ar">
    <link rel="alternate" hreflang="en" href="{{ url('/instructors') }}?lang=en">
    <link rel="alternate" hreflang="x-default" href="{{ url('/instructors') }}">
    @include('partials.favicon-links')
    @include('partials.seo-jsonld', ['jsonldType' => 'website'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://images.unsplash.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Tajawal:wght@400;500;700;800&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        acad: {
                            blue: '#0B3D91',
                            blueDark: '#072a66',
                            cyan: '#00A3C4',
                            yellow: '#F5B800',
                            navy: '#0d1528',
                            navyMid: '#1a2d4d',
                            neon: '#00d4ff',
                        },
                    },
                    fontFamily: {
                        sans: ['Cairo', 'Tajawal', 'IBM Plex Sans Arabic', 'system-ui', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"></noscript>

    @include('partials.public-academy-surface')
    <style>
        .line-clamp-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
        .reveal.s1{transition-delay:.06s}.reveal.s2{transition-delay:.12s}.reveal.s3{transition-delay:.18s}.reveal.s4{transition-delay:.24s}
    </style>
</head>
<body class="page-academy font-sans antialiased text-white">
    <div id="scroll-progress" class="fixed top-0 left-0 h-[3px] w-0 z-[100000] bg-gradient-to-l from-acad-yellow to-acad-cyan"></div>

    @include('components.unified-navbar')

    <main class="flex-1">
        <section class="-mt-14 sm:-mt-[60px] pt-24 sm:pt-28 lg:pt-32 pb-10 sm:pb-12 overflow-hidden relative">
            <div class="absolute inset-0 bg-acad-navy"></div>
            <div class="absolute inset-0 opacity-[0.2] bg-cover bg-center"
                 style="background-image:url('https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=2400&q=82')"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-acad-navy via-acad-navy/88 to-acad-navy/30"></div>
            <div class="absolute inset-0 pattern-dots opacity-[0.14] pointer-events-none"></div>
            <div class="container-acad relative z-10">
                <div class="text-center max-w-4xl mx-auto reveal">
                    <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-extrabold mb-6 glass-panel text-white border border-white/10">
                        <i class="fas fa-chalkboard-teacher text-[12px] opacity-80"></i> {{ __('public.instructors_page_title') }}
                    </span>
                    <h1 class="text-[2rem] sm:text-[2.8rem] lg:text-[3.35rem] leading-[1.18] font-black text-white mb-5 font-display">
                        {{ __('public.instructors_heading') }}
                    </h1>
                    <p class="text-white/70 text-base sm:text-lg leading-8 max-w-3xl mx-auto mb-8">
                        {{ __('public.instructors_subtitle') }}
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 max-w-xl mx-auto">
                        <article class="rounded-2xl p-4 sm:p-5 border border-white/10 glass-panel text-center">
                            <p class="text-3xl sm:text-4xl font-black text-white">{{ $profiles->count() }}</p>
                            <p class="text-xs sm:text-sm text-white/55 mt-1">{{ __('public.instructors_stat_certified') }}</p>
                        </article>
                        <article class="rounded-2xl p-4 sm:p-5 border border-white/10 glass-panel text-center">
                            <p class="text-3xl sm:text-4xl font-black text-acad-yellow">{{ $profiles->sum('courses_count') }}</p>
                            <p class="text-xs sm:text-sm text-white/55 mt-1">{{ __('public.instructors_stat_courses') }}</p>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-14 sm:py-20 relative">
            <div class="absolute inset-0 bg-gradient-to-b from-acad-navy via-acad-navyMid/50 to-acad-navy pointer-events-none"></div>
            <div class="container-acad relative z-10">
                <div class="text-center max-w-3xl mx-auto mb-12 reveal">
                    <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-bold mb-4 glass-panel border border-white/10 text-acad-cyan">
                        {{ __('public.instructors_section_badge') }}
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-black text-white mb-3 font-display">
                        {{ __('public.instructors_section_title') }}
                        <span class="text-acad-yellow">{{ __('public.instructors_section_title_accent') }}</span>
                    </h2>
                    <p class="text-white/65 leading-8">{{ __('public.instructors_section_sub') }}</p>
                </div>

                @if($profiles->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                        @foreach($profiles as $idx => $p)
                            <div class="reveal card-stream flex flex-col overflow-hidden {{ 's'.min($idx + 1, 4) }}">
                                <a href="{{ route('public.instructors.show', $p->user) }}" class="block flex-1 min-h-0 group">
                                    <div class="relative aspect-[4/3] overflow-hidden bg-acad-navyMid/80">
                                        @if($p->photo_path)
                                            <img src="{{ $p->photo_url }}" alt="{{ $p->user->name }}"
                                                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out"
                                                 onerror="this.style.display='none';this.nextElementSibling.classList.remove('hidden')">
                                            <div class="hidden absolute inset-0 flex items-center justify-center bg-acad-navyMid">
                                                <div class="w-24 h-24 rounded-full bg-white/10 flex items-center justify-center">
                                                    <i class="fas fa-user text-white/50 text-4xl"></i>
                                                </div>
                                            </div>
                                        @else
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <div class="w-24 h-24 rounded-full bg-white/10 flex items-center justify-center">
                                                    <i class="fas fa-user text-white/50 text-4xl"></i>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 bg-gradient-to-t from-acad-navy via-transparent to-transparent opacity-90"></div>
                                        @if($p->courses_count > 0)
                                            <span class="absolute top-3 {{ $isRtl ? 'right' : 'left' }}-3 px-3 py-1.5 rounded-full bg-acad-yellow text-acad-blue text-[11px] font-black flex items-center gap-1.5 shadow-lg">
                                                <i class="fas fa-book-open text-[9px]"></i>
                                                {{ $p->courses_count }} {{ $p->courses_count > 1 ? __('public.instructors_course_many') : __('public.instructors_course_one') }}
                                            </span>
                                        @endif
                                        @if(! empty($p->marketing_featured_today))
                                            <span class="absolute top-3 {{ $isRtl ? 'left' : 'right' }}-3 px-3 py-1.5 rounded-full bg-acad-cyan text-acad-navy text-[11px] font-black shadow-lg">
                                                <i class="fas fa-bolt text-[9px]"></i> {{ __('public.instructors_featured_badge') }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="p-5 sm:p-6">
                                        <h3 class="text-xl font-black text-white mb-1.5 group-hover:text-acad-yellow transition-colors">
                                            {{ $p->user->name }}
                                        </h3>
                                        <p class="text-sm text-acad-cyan font-semibold mb-3">
                                            {{ $p->headline ?? __('public.instructor_fallback') }}
                                        </p>
                                        @if(count($p->skills_list) > 0)
                                            <div class="flex flex-wrap gap-1.5 mb-4">
                                                @foreach(array_slice($p->skills_list, 0, 3) as $skill)
                                                    <span class="px-2.5 py-1 rounded-lg bg-white/8 text-white/80 text-[11px] font-medium border border-white/10">{{ $skill }}</span>
                                                @endforeach
                                                @if(count($p->skills_list) > 3)
                                                    <span class="px-2.5 py-1 rounded-lg bg-acad-yellow/20 text-acad-yellow text-[11px] font-bold">+{{ count($p->skills_list) - 3 }}</span>
                                                @endif
                                            </div>
                                        @endif
                                        @if($p->bio)
                                            <p class="text-[13px] text-white/55 leading-relaxed line-clamp-2 mb-4">{{ $p->bio }}</p>
                                        @endif
                                        <div class="flex items-center justify-between pt-4 border-t border-white/10">
                                            <div class="flex items-center gap-2 text-xs text-white/50">
                                                <i class="fas fa-check-circle text-acad-cyan"></i>
                                                <span class="font-semibold">{{ __('public.instructors_verified') }}</span>
                                            </div>
                                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-acad-yellow text-acad-blue font-black text-[12px] group-hover:brightness-110 transition-all">
                                                {{ __('public.view_instructor_profile') }}
                                                <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }} text-[9px]"></i>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                                @if(isset($consultationSetting) && $consultationSetting->is_active)
                                    <div class="px-5 sm:px-6 pb-5 pt-1 border-t border-white/10">
                                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                                            <span class="text-[11px] text-white/55 font-medium">{{ __('public.instructors_consult_label') }} — <strong class="text-white">{{ number_format($p->effectiveConsultationPriceEgp(), 2) }}</strong> {{ $isRtl ? 'ج.م' : 'EGP' }}</span>
                                            @auth
                                                @if(auth()->user()->isStudent())
                                                    <a href="{{ route('consultations.create', $p->user) }}"
                                                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-acad-cyan hover:brightness-110 text-acad-navy text-xs font-black transition-all">
                                                        <i class="fas fa-comments text-[11px]"></i>
                                                        {{ __('public.instructors_consult_cta') }}
                                                    </a>
                                                @else
                                                    <span class="text-[11px] text-white/45">{{ __('public.instructors_consult_login_student') }}</span>
                                                @endif
                                            @else
                                                <a href="{{ route('login', ['redirect' => route('consultations.create', $p->user)]) }}"
                                                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-acad-cyan hover:brightness-110 text-acad-navy text-xs font-black transition-all">
                                                    <i class="fas fa-comments text-[11px]"></i>
                                                    {{ __('public.instructors_consult_cta') }}
                                                </a>
                                            @endauth
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-20 reveal">
                        <div class="max-w-md mx-auto glass-panel rounded-3xl border border-white/10 p-10">
                            <div class="w-24 h-24 rounded-3xl flex items-center justify-center mx-auto mb-6 bg-acad-yellow/15 text-acad-yellow">
                                <i class="fas fa-chalkboard-teacher text-4xl"></i>
                            </div>
                            <h3 class="text-2xl font-black text-white mb-3 font-display">{{ __('public.no_instructors') }}</h3>
                            <p class="text-white/60 mb-8 leading-relaxed">{{ __('public.instructors_empty_hint') }}</p>
                            <a href="{{ url('/') }}" class="btn-stream-primary inline-flex items-center gap-2">
                                <i class="fas fa-home"></i> {{ __('public.instructors_back_home') }}
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <section class="pt-12 sm:pt-16 pb-12 sm:pb-16 relative">
            <div class="container-acad relative z-10">
                <div class="reveal rounded-[28px] border border-white/10 glass-panel px-6 sm:px-10 py-10 sm:py-12 text-center overflow-hidden relative">
                    <div class="absolute inset-0 pointer-events-none opacity-[0.12] bg-cover bg-center"
                         style="background-image:url('https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=2000&q=82')"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-acad-navy/90 via-acad-navy/75 to-acad-blue/40 pointer-events-none"></div>
                    <span class="relative z-10 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-extrabold mb-5 glass-panel border border-white/10">
                        <i class="fas fa-rocket"></i> {{ __('public.instructors_cta_badge') }}
                    </span>
                    <h2 class="relative z-10 text-3xl sm:text-5xl font-black text-white mb-4 font-display">
                        {{ __('public.instructors_cta_title') }}
                        <span class="block text-acad-yellow mt-1">{{ __('public.instructors_cta_title_accent') }}</span>
                    </h2>
                    <p class="relative z-10 text-white/70 text-base sm:text-lg max-w-3xl mx-auto leading-8 mb-8">
                        {{ __('public.instructors_cta_desc') }}
                    </p>
                    <div class="relative z-10 flex flex-col sm:flex-row justify-center gap-3 sm:gap-4">
                        <a href="{{ route('register') }}" class="btn-stream-primary px-8 py-4 text-base">{{ __('public.instructors_cta_register') }}</a>
                        <a href="{{ route('public.courses') }}" class="btn-stream-secondary px-8 py-4 text-base">{{ __('public.instructors_cta_browse') }}</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('components.unified-footer')

    <script>
        (function () {
            function scrollProgress() {
                var s = window.pageYOffset || document.documentElement.scrollTop;
                var h = document.documentElement.scrollHeight - window.innerHeight;
                var p = h > 0 ? (s / h) * 100 : 0;
                var b = document.getElementById('scroll-progress');
                if (b) b.style.width = p + '%';
            }
            window.addEventListener('scroll', scrollProgress, { passive: true });
            scrollProgress();
            var els = document.querySelectorAll('.reveal');
            if (!els.length) return;
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (e) {
                    if (e.isIntersecting) {
                        e.target.classList.add('revealed');
                        io.unobserve(e.target);
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
            els.forEach(function (el) { io.observe(el); });
        })();
    </script>
</body>
</html>
