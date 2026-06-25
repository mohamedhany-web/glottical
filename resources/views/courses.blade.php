@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $initialCategoryId = isset($categoryId) && (int) $categoryId > 0 ? (string) (int) $categoryId : '';
    $activeDelivery = $delivery ?? null;
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title>{{ __('public.courses_page_title') }} - {{ __('public.site_suffix') }}</title>
    <meta name="title"       content="{{ __('public.courses_page_title') }} - {{ __('public.site_suffix') }}">
    <meta name="description" content="{{ __('public.courses_subtitle') }}">
    <meta name="keywords"    content="كورسات ألماني, كورسات إنجليزي, كول سنتر, سوق العمل, ألمانيا, {{ config('app.name', 'Glottical') }}">
    <meta name="author"      content="{{ config('app.name', 'Glottical') }}">
    <meta name="robots"      content="index, follow, max-image-preview:large, max-snippet:-1">
    <meta name="theme-color" content="#0d1528">
    <link rel="canonical"    href="{{ url('/courses') }}">
    <link rel="alternate" hreflang="ar"        href="{{ url('/courses') }}?lang=ar">
    <link rel="alternate" hreflang="en"        href="{{ url('/courses') }}?lang=en">
    <link rel="alternate" hreflang="x-default" href="{{ url('/courses') }}">
    <!-- Open Graph -->
    <meta property="og:type"             content="website">
    <meta property="og:url"              content="{{ url('/courses') }}">
    <meta property="og:title"            content="{{ __('public.courses_page_title') }} - {{ config('app.name', 'Glottical') }}">
    <meta property="og:description"      content="{{ __('public.courses_subtitle') }}">
    <meta property="og:image"            content="{{ asset('images/og-image.jpg') }}">
    <meta property="og:image:alt"        content="كورسات {{ config('app.name', 'Glottical') }}">
    <meta property="og:image:width"      content="1200">
    <meta property="og:image:height"     content="630">
    <meta property="og:locale"           content="{{ $locale === 'ar' ? 'ar_AR' : 'en_US' }}">
    <meta property="og:locale:alternate" content="{{ $locale === 'ar' ? 'en_US' : 'ar_AR' }}">
    <meta property="og:site_name"        content="{{ config('app.name', 'Glottical') }}">
    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:site"        content="@Glottical">
    <meta name="twitter:url"         content="{{ url('/courses') }}">
    <meta name="twitter:title"       content="{{ __('public.courses_page_title') }} - {{ config('app.name', 'Glottical') }}">
    <meta name="twitter:description" content="{{ __('public.courses_subtitle') }}">
    <meta name="twitter:image"       content="{{ asset('images/og-image.jpg') }}">
    <meta name="twitter:image:alt"   content="كورسات {{ config('app.name', 'Glottical') }}">
    @include('partials.favicon-links')
    <!-- BreadcrumbList JSON-LD -->
    <script type="application/ld+json">
    {"@@context":"https://schema.org","@@type":"BreadcrumbList","itemListElement":[{"@@type":"ListItem","position":1,"name":"الرئيسية","item":"{{ url('/') }}"},{"@@type":"ListItem","position":2,"name":"الكورسات","item":"{{ url('/courses') }}"}]}
    </script>
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
                            blueSoft: '#E8EEF8',
                            cyan: '#00A3C4',
                            yellow: '#F5B800',
                            yellowSoft: '#FFF8E1',
                            gray: '#F4F6FA',
                            ink: '#1a2d4d',
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
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"></noscript>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
      [x-cloak]{display:none!important}
      html{scroll-behavior:smooth;overflow-x:hidden}
      body{overflow-x:hidden;background:linear-gradient(180deg,#0d1528 0%,#121f38 45%,#0d1528 100%);min-height:100vh;display:flex;flex-direction:column;color:#e8eef8;font-size:16px;line-height:1.65}
      .font-display{font-family:'Cairo','Tajawal','IBM Plex Sans Arabic',system-ui,sans-serif}
      h1,h2,h3,h4,h5,h6,.font-heading{font-family:'Cairo','Tajawal','IBM Plex Sans Arabic',sans-serif}
      .container-acad{max-width:1280px;margin-inline:auto;padding-inline:clamp(16px,4vw,28px)}

      .reveal{opacity:0;transform:translateY(22px);transition:opacity .6s ease,transform .6s ease}
      .reveal.revealed{opacity:1;transform:translateY(0)}
      .s1{transition-delay:.06s}.s2{transition-delay:.12s}.s3{transition-delay:.18s}.s4{transition-delay:.24s}

      .glass-panel{background:rgba(15,31,58,.72);border:1px solid rgba(255,255,255,.12);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px)}
      .btn-primary{padding:12px 24px;border-radius:16px;font-weight:800;color:#0B3D91;background:#F5B800;transition:transform .2s ease,filter .2s ease,box-shadow .2s ease}
      .btn-primary:hover{transform:translateY(-1px) scale(1.02);filter:brightness(1.05);box-shadow:0 18px 40px -18px rgba(245,184,0,.55)}
      .btn-secondary{padding:12px 24px;border-radius:16px;border:1px solid rgba(255,255,255,.18);color:#fff;background:rgba(255,255,255,.06);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);transition:background .2s ease}
      .btn-secondary:hover{background:rgba(255,255,255,.10)}

      .pattern-dots{background-image:radial-gradient(circle at 1px 1px,rgba(255,255,255,.06) 1px,transparent 0);background-size:24px 24px}
      .netflix-row{display:flex;flex-direction:row;flex-wrap:nowrap;align-items:stretch;gap:1rem;overflow-x:auto;overflow-y:visible;scroll-snap-type:x mandatory;scrollbar-width:thin;scrollbar-color:rgba(0,212,255,.5) rgba(5,11,24,.82);scroll-behavior:smooth;-webkit-overflow-scrolling:touch;padding:8px 4px 16px;margin-inline:-4px;width:100%;max-width:100%}
      .netflix-row::-webkit-scrollbar{height:8px}
      .netflix-row::-webkit-scrollbar-track{background:rgba(5,11,24,.9);border-radius:999px;margin-inline:2px}
      .netflix-row::-webkit-scrollbar-thumb{background:linear-gradient(90deg,rgba(245,184,0,.55),rgba(0,212,255,.45));border-radius:999px;border:2px solid rgba(5,11,24,.85)}
      .netflix-row::-webkit-scrollbar-thumb:hover{background:linear-gradient(90deg,rgba(245,184,0,.7),rgba(0,212,255,.55))}
      .netflix-row::-webkit-scrollbar-button{width:0;height:0;display:none}
      .netflix-row::-webkit-scrollbar-corner{background:transparent}
      .netflix-item{scroll-snap-align:start;flex:0 0 auto;width:min(17.5rem,82vw);min-width:min(17.5rem,82vw);max-width:20rem}
      @media(min-width:640px){.netflix-item{width:18.5rem;min-width:18.5rem;max-width:19.5rem}}
      @media(min-width:1024px){.netflix-item{width:20rem;min-width:20rem;max-width:20rem}}
      .stream-card{border-radius:14px;overflow:hidden;background:#1a2d4d;border:1px solid rgba(255,255,255,.10);box-shadow:0 18px 40px -26px rgba(0,0,0,.65);transition:transform .25s ease, box-shadow .25s ease, border-color .25s ease}
      .stream-card:hover{transform:scale(1.05);border-color:rgba(245,184,0,.55);box-shadow:0 0 0 2px rgba(245,184,0,.55),0 24px 60px -28px rgba(0,212,255,.35)}
      .line-clamp-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
      .media-thumb-skeleton{position:absolute;inset:0;background:linear-gradient(110deg,#152a4a 8%,#1e3a5f 18%,#152a4a 33%);background-size:200% 100%;animation:mediaShimmer 1.2s linear infinite}
      @keyframes mediaShimmer{to{background-position-x:-200%}}
      .media-thumb-img{opacity:0;transition:opacity .35s ease}
      .media-thumb-img.is-loaded{opacity:1}
    </style>
</head>
<body class="font-sans text-white antialiased font-display bg-acad-navy"
      x-data="{
        searchQuery: '',
        selectedCategoryId: @js($initialCategoryId),
        courses: @js($courses ?? []),
        get filteredCourses() {
          const q = this.searchQuery.toLowerCase().trim();
          const cat = this.selectedCategoryId;
          return this.courses.filter(c => {
            const matchQ = !q || (c.title && c.title.toLowerCase().includes(q)) || (c.description && c.description.toLowerCase().includes(q));
            const matchC = !cat || String(c.course_category_id || '') === String(cat);
            return matchQ && matchC;
          });
        }
      }">
<div id="scroll-progress" class="fixed top-0 left-0 h-[3px] w-0 z-[100000] bg-gradient-to-l from-acad-yellow to-acad-cyan"></div>

@include('components.unified-navbar')

<main class="flex-1">
  {{-- HERO (streaming catalog) --}}
  <section class="-mt-14 sm:-mt-[60px] pt-24 sm:pt-28 lg:pt-32 pb-10 sm:pb-12 overflow-hidden relative">
    <div class="absolute inset-0 bg-[#0d1528]"></div>
    <div class="absolute inset-0 opacity-[0.22] bg-cover bg-center"
         style="background-image:url('https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=2400&q=82')"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-[#0d1528] via-[#0d1528]/85 to-[#0d1528]/25"></div>
    <div class="absolute inset-0 pattern-dots opacity-[0.14] pointer-events-none"></div>
    <div class="container-acad relative z-10">
      <div class="max-w-4xl mx-auto text-center reveal">
        <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-extrabold mb-6 glass-panel text-white border border-white/10">
          <i class="fas fa-film text-[12px] opacity-80"></i> {{ __('public.courses_page_title') }}
        </span>
        <h1 class="text-[2rem] sm:text-[2.8rem] lg:text-[3.35rem] leading-[1.18] font-black text-white mb-5 font-display">
          {{ __('public.courses_page_title') }}
          <span class="block text-acad-yellow">{{ __('public.browse_courses') }}</span>
        </h1>
        <p class="text-white/70 text-base sm:text-lg leading-8 mb-7 max-w-3xl mx-auto">{{ __('public.courses_subtitle') }}</p>

        <div class="grid sm:grid-cols-[1fr_180px] gap-3 max-w-3xl mx-auto">
          <div class="flex items-center gap-3 rounded-2xl glass-panel px-4 py-3 border border-white/12">
            <i class="fas fa-search text-acad-cyan/90"></i>
            <input type="text" x-model="searchQuery" placeholder="{{ __('public.search_course_placeholder') }}" class="flex-1 bg-transparent border-0 outline-none text-white placeholder:text-white/45 font-semibold">
          </div>
          <div class="relative">
            <select x-model="selectedCategoryId" class="w-full rounded-2xl glass-panel px-4 py-3 {{ $isRtl?'pl-10':'pr-10' }} text-white focus:outline-none border border-white/12">
              <option value="">{{ __('public.all_course_categories') }}</option>
              @foreach($courseFilterCategories ?? [] as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
              @endforeach
            </select>
            <i class="fas fa-chevron-down absolute {{ $isRtl?'left':'right' }}-3 top-1/2 -translate-y-1/2 text-white/50 text-xs"></i>
          </div>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-2 mt-6 reveal s1">
          <a href="{{ route('public.courses') }}"
             class="px-4 py-2 rounded-full text-sm font-extrabold border transition {{ !$activeDelivery ? 'bg-acad-yellow text-[#0B3D91] border-acad-yellow' : 'glass-panel border-white/12 text-white/80 hover:text-white' }}">
            {{ __('public.courses_filter_all') }}
          </a>
          <a href="{{ route('public.courses', ['delivery' => 'group']) }}"
             class="px-4 py-2 rounded-full text-sm font-extrabold border transition {{ $activeDelivery === 'group' ? 'bg-acad-cyan text-[#0B3D91] border-acad-cyan' : 'glass-panel border-white/12 text-white/80 hover:text-white' }}">
            <i class="fas fa-users text-xs {{ $isRtl ? 'ml-1' : 'mr-1' }}"></i>{{ __('public.courses_filter_group') }}
          </a>
          <a href="{{ route('public.courses', ['delivery' => 'one_to_one']) }}"
             class="px-4 py-2 rounded-full text-sm font-extrabold border transition {{ $activeDelivery === 'one_to_one' ? 'bg-violet-400 text-[#0B3D91] border-violet-400' : 'glass-panel border-white/12 text-white/80 hover:text-white' }}">
            <i class="fas fa-user-graduate text-xs {{ $isRtl ? 'ml-1' : 'mr-1' }}"></i>{{ __('public.courses_filter_one_to_one') }}
            @if(($oneToOneCount ?? 0) > 0)
              <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full text-[10px] bg-white/15 {{ $isRtl ? 'mr-1' : 'ml-1' }}">{{ $oneToOneCount }}</span>
            @endif
          </a>
        </div>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4 mt-10 max-w-2xl mx-auto reveal s2">
        <article class="rounded-2xl p-4 sm:p-5 border border-white/10 glass-panel text-center">
          <p class="text-3xl sm:text-4xl font-black text-white" x-text="courses.length">0</p>
          <p class="text-xs sm:text-sm text-white/55 mt-1">{{ __('public.courses_stats_available') }}</p>
        </article>
        <article class="rounded-2xl p-4 sm:p-5 border border-white/10 glass-panel text-center">
          <p class="text-3xl sm:text-4xl font-black text-acad-yellow" x-text="courses.filter(c=>c.is_featured).length">0</p>
          <p class="text-xs sm:text-sm text-white/55 mt-1">{{ __('public.courses_stats_featured') }}</p>
        </article>
        <article class="rounded-2xl p-4 sm:p-5 border border-white/10 glass-panel text-center hidden sm:block">
          <p class="text-3xl sm:text-4xl font-black text-violet-300" x-text="courses.filter(c=>c.is_one_to_one).length">0</p>
          <p class="text-xs sm:text-sm text-white/55 mt-1">{{ __('public.courses_filter_one_to_one') }}</p>
        </article>
      </div>
    </div>
  </section>

  {{-- COURSES (Netflix rows) --}}
  <section class="py-14 sm:py-16">
    <div class="container-acad">
      <div class="flex items-end justify-between mb-7 gap-4">
        <div class="reveal max-w-2xl">
          <h2 class="text-3xl sm:text-4xl font-black text-white mb-2 font-display">{{ __('public.courses_section_title') }}</h2>
          <p class="text-white/60">{{ __('public.courses_section_subtitle') }}</p>
        </div>
      </div>

      <div x-data="{
            get rows() {
              const list = this.filteredCourses || [];
              const q = (this.searchQuery || '').trim();
              if (q) return [{ title: '{{ addslashes(__('landing.academy.search_results_row')) }}', items: list }];
              const map = new Map();
              list.forEach(c => {
                const title = (c.course_category && c.course_category.name) || (c.academic_subject && c.academic_subject.name) || '{{ addslashes(__('public.all_course_categories')) }}';
                if (!map.has(title)) map.set(title, []);
                map.get(title).push(c);
              });
              return Array.from(map.entries()).map(([title, items]) => ({ title, items }));
            }
          }">
        <template x-if="rows && rows.length">
          <div class="space-y-10">
            <template x-for="row in rows" :key="row.title">
              <div class="reveal">
                <div class="flex items-center gap-3 mb-4">
                  <span class="h-1 w-8 rounded-full bg-gradient-to-l from-acad-yellow to-acad-cyan shrink-0"></span>
                  <h3 class="text-lg sm:text-xl font-black text-white tracking-tight" x-text="row.title"></h3>
                </div>
                <div class="netflix-row" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
                  <template x-for="course in row.items" :key="course.id">
                    <a class="netflix-item stream-card block"
                       :href="'{{ url('/course') }}/' + course.id">
                      <div class="relative aspect-video bg-slate-900/50">
                        <template x-if="course.thumbnail">
                          <div class="absolute inset-0">
                            <div class="media-thumb-skeleton" :data-skeleton-for="course.id"></div>
                            <img :src="course.thumbnail"
                                 :alt="course.title"
                                 width="400"
                                 height="225"
                                 sizes="(max-width: 640px) 85vw, 400px"
                                 decoding="async"
                                 loading="lazy"
                                 class="absolute inset-0 h-full w-full object-cover media-thumb-img"
                                 x-init="$el.addEventListener('load', () => { $el.classList.add('is-loaded'); $el.previousElementSibling?.remove(); })"
                                 @@error="$el.style.display='none'; $el.previousElementSibling?.remove();">
                          </div>
                        </template>
                        <div class="absolute inset-0 bg-gradient-to-t from-[#0d1528] via-transparent to-transparent opacity-90"></div>
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 transition duration-300 bg-black/50">
                          <span class="px-5 py-2.5 rounded-full bg-acad-yellow text-[#0B3D91] font-black text-sm shadow-lg">
                            <i class="fas fa-play text-xs me-1"></i> {{ __('landing.academy.stream_play') }}
                          </span>
                        </div>
                        <template x-if="course.is_featured">
                          <span class="absolute top-3 {{ $isRtl?'right':'left' }}-3 text-[10px] font-black px-2 py-1 rounded-md bg-acad-yellow text-[#0B3D91]">{{ __('public.featured_badge') }}</span>
                        </template>
                        <template x-if="course.is_one_to_one">
                          <span class="absolute top-3 {{ $isRtl?'left':'right' }}-3 text-[10px] font-black px-2 py-1 rounded-md bg-violet-400 text-[#0B3D91]">{{ __('public.course_badge_one_to_one') }}</span>
                        </template>
                      </div>
                      <div class="p-4 text-start">
                        <p class="text-[11px] font-bold text-white/55 mb-1" x-text="(course.instructor && course.instructor.name) ? course.instructor.name : ''"></p>
                        <h4 class="font-black text-white text-sm sm:text-base leading-snug line-clamp-2" x-text="course.title || '{{ addslashes(__('public.no_title_fallback')) }}'"></h4>
                        <div class="mt-2 flex items-center justify-between text-xs font-bold text-white/55">
                          <span x-text="(course.lectures_count || 0) + ' {{ addslashes(__('public.lecture_single')) }}'"></span>
                          <span class="text-acad-cyan">{{ __('public.view_details') }}</span>
                        </div>
                      </div>
                    </a>
                  </template>
                </div>
              </div>
            </template>
          </div>
        </template>
      </div>

      <div x-show="filteredCourses && filteredCourses.length === 0" x-cloak class="text-center py-16 reveal">
        <h3 class="text-2xl font-black text-white mb-2 font-display">{{ __('public.no_results') }}</h3>
        <p class="text-white/60 mb-6">{{ __('public.no_results_hint') }}</p>
        <button @click="searchQuery=''; selectedCategoryId='';" type="button" class="btn-secondary">{{ __('public.courses_reset_search') }}</button>
      </div>
    </div>
  </section>

  {{-- CTA --}}
  <section class="pt-14 sm:pt-18 pb-10 sm:pb-12">
    <div class="container-acad">
      <div class="reveal rounded-[28px] border border-white/10 glass-panel px-6 sm:px-10 py-10 sm:py-12 text-center overflow-hidden relative">
        <div class="absolute inset-0 pointer-events-none opacity-[0.16] bg-cover bg-center" style="background-image:url('https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=2000&q=82')"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#0d1528]/70 via-transparent to-[#0B3D91]/35 pointer-events-none"></div>
        <span class="relative z-10 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-extrabold mb-5 glass-panel border border-white/10">
          <i class="fas fa-rocket"></i> {{ __('public.courses_cta_badge') }}
        </span>
        <h2 class="relative z-10 text-3xl sm:text-5xl font-black text-white mb-4 font-display">{{ __('public.courses_cta_title') }}</h2>
        <p class="relative z-10 text-white/70 text-base sm:text-lg max-w-3xl mx-auto leading-8 mb-7">{{ __('public.courses_cta_desc') }}</p>
        <div class="relative z-10 flex flex-col sm:flex-row justify-center gap-3 sm:gap-4">
          <a href="{{ route('register') }}" class="btn-primary inline-flex items-center justify-center gap-2">{{ __('public.register_free_now') }}</a>
          <a href="{{ route('login') }}" class="btn-secondary inline-flex items-center justify-center gap-2">{{ __('public.have_account') }}</a>
        </div>
      </div>
    </div>
  </section>
</main>

@include('components.unified-footer')

<script>
(function(){
  'use strict';
  function progress(){var s=window.pageYOffset||document.documentElement.scrollTop,h=document.documentElement.scrollHeight-window.innerHeight,p=h>0?(s/h)*100:0,b=document.getElementById('scroll-progress');if(b)b.style.width=p+'%';}
  window.addEventListener('scroll',progress,{passive:true});
  function reveal(){var els=document.querySelectorAll('.reveal');if(!els.length)return;var io=new IntersectionObserver(function(entries){entries.forEach(function(e){if(e.isIntersecting){e.target.classList.add('revealed');io.unobserve(e.target);}});},{threshold:.12,rootMargin:'0px 0px -50px 0px'});els.forEach(function(el){io.observe(el)});}
  if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',reveal);}else{reveal();}
})();
</script>
</body>
</html>
