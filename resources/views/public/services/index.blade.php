@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title>{{ __('public.services_page_title') }} - {{ __('public.site_suffix') }}</title>
    <meta name="description" content="{{ __('public.services_subtitle') }}">
    <meta name="theme-color" content="#050b18">
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
                            navy: '#050b18',
                            navyMid: '#0f1f3a',
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
        .arrow-link::after{content:'\f177';font-family:'Font Awesome 6 Free';font-weight:900;margin-inline-start:8px}
        [dir='ltr'] .arrow-link::after{content:'\f178'}
    </style>
</head>
<body class="page-academy font-sans antialiased text-white">
    <div id="scroll-progress" class="fixed top-0 left-0 h-[3px] w-0 z-[100000] bg-gradient-to-l from-acad-yellow to-acad-cyan"></div>
    @include('components.unified-navbar')

    <main class="flex-1">
        <section class="-mt-14 sm:-mt-[60px] pt-24 sm:pt-28 lg:pt-32 pb-10 sm:pb-12 overflow-hidden relative">
            <div class="absolute inset-0 bg-acad-navy"></div>
            <div class="absolute inset-0 opacity-[0.2] bg-cover bg-center"
                 style="background-image:url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=2400&q=82')"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-acad-navy via-acad-navy/88 to-acad-navy/30"></div>
            <div class="absolute inset-0 pattern-dots opacity-[0.14] pointer-events-none"></div>
            <div class="container-acad relative z-10">
                <div class="max-w-4xl mx-auto text-center reveal">
                    <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-extrabold mb-6 glass-panel text-white border border-white/10">
                        <i class="fas fa-concierge-bell text-[12px] opacity-80"></i> {{ __('public.services_page_title') }}
                    </span>
                    <h1 class="text-[2rem] sm:text-[2.8rem] lg:text-[3.35rem] leading-[1.18] font-black text-white mb-5 font-display">
                        {{ __('public.services_heading') }}
                    </h1>
                    <p class="text-white/70 text-base sm:text-lg leading-8 mb-7 max-w-3xl mx-auto">{{ __('public.services_subtitle') }}</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mt-8 max-w-4xl mx-auto reveal s2">
                    <article class="rounded-2xl p-4 sm:p-5 border border-white/10 glass-panel text-center">
                        <p class="text-3xl sm:text-4xl font-black text-white">{{ $services->count() }}</p>
                        <p class="text-xs sm:text-sm text-white/55 mt-1">{{ __('public.services_count_label') }}</p>
                    </article>
                    <article class="rounded-2xl p-4 sm:p-5 border border-white/10 glass-panel text-center">
                        <p class="text-3xl sm:text-4xl font-black text-acad-yellow"><i class="fas fa-check-circle"></i></p>
                        <p class="text-xs sm:text-sm text-white/55 mt-1">{{ __('public.services_quality_hint') }}</p>
                    </article>
                    <article class="rounded-2xl p-4 sm:p-5 border border-white/10 glass-panel text-center">
                        <p class="text-3xl sm:text-4xl font-black text-acad-cyan"><i class="fas fa-headset"></i></p>
                        <p class="text-xs sm:text-sm text-white/55 mt-1">{{ __('public.services_support_hint') }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="py-14 sm:py-16 relative">
            <div class="absolute inset-0 bg-gradient-to-b from-acad-navy via-acad-navyMid/40 to-acad-navy pointer-events-none"></div>
            <div class="container-acad relative z-10">
                @if($services->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                        @foreach($services as $idx => $service)
                            <a href="{{ route('public.services.show', $service) }}" class="card-stream reveal block group overflow-hidden s{{ ($idx % 4) + 1 }}">
                                <div class="relative h-36 overflow-hidden flex items-center justify-center bg-acad-navyMid/80">
                                    @if($service->publicImageUrl())
                                        <img src="{{ $service->publicImageUrl() }}" alt="" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-acad-blue/40 to-acad-navyMid">
                                            <i class="fas fa-layer-group text-5xl text-white/40 group-hover:scale-105 transition-transform duration-300"></i>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-gradient-to-t from-acad-navy via-transparent to-transparent pointer-events-none"></div>
                                </div>
                                <div class="p-5">
                                    <h2 class="text-lg font-extrabold text-white leading-snug mb-2 line-clamp-2 group-hover:text-acad-yellow transition-colors">{{ $service->name }}</h2>
                                    <p class="text-sm text-white/55 leading-7 line-clamp-2 mb-4">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($service->summary ?: $service->body), 120) }}
                                    </p>
                                    <div class="flex items-center justify-end pt-4 border-t border-white/10">
                                        <span class="text-acad-cyan text-xs font-bold arrow-link">{{ __('public.services_read_more') }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-20 reveal">
                        <div class="max-w-md mx-auto glass-panel rounded-3xl border border-white/10 p-10">
                            <div class="w-20 h-20 mx-auto rounded-2xl bg-acad-yellow/15 flex items-center justify-center text-acad-yellow mb-5">
                                <i class="fas fa-concierge-bell text-3xl"></i>
                            </div>
                            <h3 class="text-2xl font-black text-white mb-3 font-display">{{ __('public.services_empty_title') }}</h3>
                            <p class="text-white/65 leading-8 mb-6">{{ __('public.services_empty_desc') }}</p>
                            <a href="{{ route('home') }}" class="btn-stream-primary inline-flex items-center gap-2">{{ __('public.home') }} <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }} text-xs"></i></a>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <section class="pt-12 sm:pt-16 pb-12 sm:pb-16">
            <div class="container-acad">
                <div class="reveal rounded-[28px] border border-white/10 glass-panel px-6 sm:px-10 py-10 sm:py-12 text-center overflow-hidden relative">
                    <div class="absolute inset-0 pointer-events-none opacity-[0.1] bg-cover bg-center"
                         style="background-image:url('https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=2200&q=82')"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-acad-navy/92 to-acad-blue/35 pointer-events-none"></div>
                    <span class="relative z-10 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-extrabold mb-5 glass-panel border border-white/10">
                        <i class="fas fa-question-circle"></i> {{ __('public.services_cta_badge') }}
                    </span>
                    <h2 class="relative z-10 text-2xl sm:text-4xl font-black text-white mb-4 font-display">{{ __('public.services_cta_title') }}</h2>
                    <p class="relative z-10 text-white/70 text-base sm:text-lg max-w-2xl mx-auto leading-8 mb-7">{{ __('public.services_cta_text') }}</p>
                    <div class="relative z-10 flex flex-col sm:flex-row justify-center gap-3 sm:gap-4">
                        <a href="{{ route('public.contact') }}" class="btn-stream-primary">{{ __('public.contact_us') }} <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }} text-xs"></i></a>
                        <a href="{{ route('public.courses') }}" class="btn-stream-secondary">{{ __('public.browse_courses') }}</a>
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
            }, { threshold: 0.12, rootMargin: '0px 0px -50px 0px' });
            els.forEach(function (el) { io.observe(el); });
        })();
    </script>
</body>
</html>
