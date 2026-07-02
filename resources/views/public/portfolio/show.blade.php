@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $isEn = $locale === 'en';
    $u = $project->user;
    $marketing = $u->publicPortfolioMarketingFields();
    $aboutDisplay = trim((string) ($marketing['about'] ?? '')) !== ''
        ? $marketing['about']
        : ($u->bio ? Str::limit(strip_tags((string) $u->bio), 400) : null);
    $skillItems = collect(preg_split('/[\n\r,،]+/u', (string) ($marketing['skills'] ?? ''), -1, PREG_SPLIT_NO_EMPTY))
        ->map(fn ($s) => trim($s))
        ->filter()
        ->take(24);
    $typeLabel = $project->project_type
        ? (\App\Models\PortfolioProject::projectTypeLabels()[$project->project_type] ?? $project->project_type)
        : null;
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title>{{ $project->title }} — {{ $u->name ?? __('public.student_fallback') }} | {{ __('public.site_suffix') }}</title>
    <meta name="description" content="{{ Str::limit(strip_tags($project->description ?? $aboutDisplay ?? ''), 160) }}">
    <meta name="theme-color" content="#283593">
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
                    mx: {
                        navy: '#283593',
                        indigo: '#1F2A7A',
                        orange: '#FB5607',
                        cream: '#FFF7ED',
                        rose: '#FFE5F7',
                        gold: '#FFE569',
                        soft: '#F7F8FF'
                    }
                },
                fontFamily: {
                    heading: ['Cairo','Tajawal','IBM Plex Sans Arabic','sans-serif'],
                    body: ['Cairo','IBM Plex Sans Arabic','Tajawal','sans-serif'],
                }
            }
        }
    };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        [x-cloak]{display:none !important}
        body,button,input,textarea,select,optgroup{font-family:'Cairo','IBM Plex Sans Arabic','Tajawal',system-ui,sans-serif}
        h1,h2,h3,h4,h5,h6,.font-heading{font-family:'Cairo','Tajawal','IBM Plex Sans Arabic',sans-serif}
        i[class*="fa-"],.fas,.far,.fab,.fa{font-style:normal;font-variant:normal;text-rendering:auto;-webkit-font-smoothing:antialiased}
        html{scroll-behavior:smooth;overflow-x:hidden}
        body{overflow-x:hidden;background:#fff;min-height:100vh;display:flex;flex-direction:column}
        .container-1200{max-width:1200px;margin-inline:auto;padding-inline:24px}
        @media (max-width: 768px){.container-1200{padding-inline:16px}}
        .reveal{opacity:0;transform:translateY(26px);transition:opacity .6s ease,transform .6s ease}
        .reveal.revealed{opacity:1;transform:translateY(0)}
        .s1{transition-delay:.06s}.s2{transition-delay:.12s}.s3{transition-delay:.18s}.s4{transition-delay:.24s}
        .btn-primary-mx{padding:12px 22px;border-radius:14px;font-weight:800;color:#fff;background:#FB5607;transition:transform .2s ease,box-shadow .2s ease;display:inline-flex;align-items:center;justify-content:center;gap:.5rem}
        .btn-primary-mx:hover{transform:scale(1.02);box-shadow:0 12px 28px -10px rgba(251,86,7,.45)}
        .btn-secondary-mx{padding:12px 22px;border-radius:14px;font-weight:700;border:2px solid #d6daea;color:#1F2A7A;background:#fff;transition:background .2s ease,border-color .2s ease;display:inline-flex;align-items:center;justify-content:center;gap:.5rem}
        .btn-secondary-mx:hover{background:#f8f9ff;border-color:#c8cfe8}
        .card-mx{border-radius:20px;border:1px solid #eceef8;background:#fff;box-shadow:0 8px 28px -18px rgba(31,42,122,.28)}
        .hover-lift-mx{transition:transform .25s ease,box-shadow .25s ease}
        .hover-lift-mx:hover{transform:translateY(-3px);box-shadow:0 18px 40px -22px rgba(31,42,122,.35)}
        #scroll-progress{position:fixed;top:0;left:0;height:3px;width:0;background:linear-gradient(90deg,#FB5607,#FFE569);z-index:9999;transition:width .1s linear}
        .navbar-spacer{display:block!important}
        #navbar,#navbar.nav-transparent,#navbar.nav-solid{
            background:rgba(31,42,122,.92)!important;
            backdrop-filter:blur(12px)!important;
            -webkit-backdrop-filter:blur(12px)!important;
            border-bottom:1px solid rgba(255,255,255,.08)!important;
        }
        .teacher-spotlight-ring{box-shadow:0 0 0 4px rgba(255,255,255,.35),0 24px 56px -16px rgba(0,0,0,.45)}
        .teacher-panel-pattern{background-image:radial-gradient(circle at 1px 1px,rgba(255,255,255,.12) 1px,transparent 0);background-size:22px 22px}
        .teacher-glow{position:absolute;border-radius:50%;filter:blur(80px);opacity:.45;pointer-events:none}
        /* عربي: لا تستخدم letter-spacing أو uppercase على عناوين المهارات — تسبب فصل الحروف في بعض المتصفحات */
        .portfolio-skill-chip{
            word-break:normal;
            overflow-wrap:break-word;
            line-height:1.5;
        }
    </style>
</head>
<body class="font-body text-slate-800 bg-white">
<div id="scroll-progress"></div>
@include('components.unified-navbar')

<main class="flex-1">
    {{-- عنوان الصفحة + مسار تفاعلي --}}
    <section class="pt-8 sm:pt-10 pb-6 sm:pb-8 relative overflow-hidden" style="background:radial-gradient(circle at 12% 80%,rgba(255,229,247,.65),transparent 28%),radial-gradient(circle at 88% 20%,rgba(40,53,147,.10),transparent 30%),linear-gradient(180deg,#f4f6ff 0%,#fbfbff 55%,#ffffff 100%)">
        <div class="absolute inset-0 pointer-events-none opacity-40" style="background-image:radial-gradient(circle at 1px 1px,rgba(40,53,147,.08) 1px,transparent 0);background-size:30px 30px"></div>
        <div class="container-1200 relative z-10">
            <nav class="reveal text-sm text-slate-600 flex items-center gap-2 flex-wrap mb-6">
                <a href="{{ url('/') }}" class="hover:text-mx-orange font-semibold transition-colors">{{ __('public.home') }}</a>
                <i class="fas fa-chevron-{{ $isRtl ? 'left' : 'right' }} text-[9px] text-slate-400"></i>
                <a href="{{ route('public.portfolio.index') }}" class="hover:text-mx-orange font-semibold transition-colors">{{ __('public.portfolio_page_title') }}</a>
                <i class="fas fa-chevron-{{ $isRtl ? 'left' : 'right' }} text-[9px] text-slate-400"></i>
                <span class="text-mx-indigo font-bold">{{ Str::limit($project->title, 48) }}</span>
            </nav>

            <div class="reveal s1 flex flex-wrap items-center gap-2 mb-4">
                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-black uppercase tracking-wide" style="background:#FFE5F7;color:#283593;border:1px solid #f5c7e8">
                    <i class="fas fa-chalkboard-teacher"></i> Glottical
                </span>
                @if($typeLabel)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1 text-xs font-bold text-mx-indigo border border-slate-200 shadow-sm">{{ $typeLabel }}</span>
                @endif
                @if($project->advancedCourse)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-mx-soft px-3 py-1 text-xs font-bold text-mx-navy border border-slate-200/80">
                        <i class="fas fa-graduation-cap text-mx-orange"></i>{{ $project->advancedCourse->title }}
                    </span>
                @endif
                @if($project->academicYear)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-700 border border-slate-200">
                        <i class="fas fa-bookmark text-mx-navy"></i>{{ $project->academicYear->name }}
                    </span>
                @endif
            </div>

            <h1 class="reveal s2 font-heading text-[1.65rem] sm:text-[2.15rem] lg:text-[2.65rem] leading-[1.2] font-black text-mx-indigo max-w-4xl">
                {{ $project->title }}
            </h1>
            @if($project->description)
                <p class="reveal s3 mt-4 text-slate-600 text-base sm:text-lg leading-8 max-w-3xl">{{ Str::limit(strip_tags($project->description), 220) }}</p>
            @endif
        </div>
    </section>

    {{-- بطاقة المعلّم — عمودان متوازنان (صورة / هوية + محتوى بعرض كامل) --}}
    <section class="pb-10 sm:pb-14 -mt-2">
        <div class="container-1200">
            <div class="reveal rounded-[28px] overflow-hidden border border-slate-200/90 shadow-[0_24px_60px_-20px_rgba(31,42,122,.35)] hover-lift-mx bg-white">
                <div class="h-1.5 w-full bg-gradient-to-l from-mx-orange via-mx-gold to-mx-navy"></div>
                <div class="grid grid-cols-1 lg:grid-cols-2 lg:min-h-[420px]">
                    {{-- لوحة بصرية: تملأ نصف العرض بالكامل --}}
                    <div class="relative order-2 lg:order-1 flex flex-col items-center justify-center px-8 py-12 lg:py-16 bg-gradient-to-br from-[#1a237e] via-mx-navy to-mx-indigo teacher-panel-pattern text-white overflow-hidden">
                        <div class="teacher-glow w-72 h-72 bg-mx-orange top-[-20%] {{ $isRtl ? 'left-[-10%]' : 'right-[-10%]' }}"></div>
                        <div class="teacher-glow w-64 h-64 bg-purple-500 bottom-[-15%] {{ $isRtl ? 'right-[-5%]' : 'left-[-5%]' }}"></div>
                        <div class="relative z-10 w-full max-w-sm flex flex-col items-center text-center">
                            <div class="relative mb-6">
                                @if($u->public_portfolio_marketing_photo_url)
                                    <img src="{{ $u->public_portfolio_marketing_photo_url }}" alt="{{ $u->name }}" class="w-40 h-40 sm:w-48 sm:h-48 rounded-[28px] object-cover teacher-spotlight-ring border-4 border-white/25" width="192" height="192">
                                @else
                                    <div class="w-40 h-40 sm:w-48 sm:h-48 rounded-[28px] teacher-spotlight-ring border-4 border-white/25 bg-white/10 backdrop-blur-sm flex items-center justify-center">
                                        <span class="text-5xl sm:text-6xl font-black text-white drop-shadow-lg">{{ mb_substr($u->name ?? 'م', 0, 1) }}</span>
                                    </div>
                                @endif
                                <span class="absolute -bottom-3 left-1/2 -translate-x-1/2 whitespace-nowrap inline-flex items-center gap-1.5 rounded-full bg-mx-orange text-white text-[11px] sm:text-xs font-black px-4 py-1.5 shadow-lg shadow-black/20">
                                    <i class="fas fa-certificate text-[11px]"></i> {{ __('public.portfolio_page_title') }}
                                </span>
                            </div>
                            <p class="text-white/85 text-sm font-bold leading-relaxed">{{ $isEn ? 'Verified educator on Glottical' : 'معلّم ضمن منصة Glottical' }}</p>
                            @if($typeLabel || $project->academicYear)
                                <div class="mt-5 flex flex-wrap justify-center gap-2 w-full">
                                    @if($typeLabel)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 backdrop-blur px-3 py-1 text-xs font-bold border border-white/20">{{ $typeLabel }}</span>
                                    @endif
                                    @if($project->academicYear)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 backdrop-blur px-3 py-1 text-xs font-bold border border-white/20"><i class="fas fa-bookmark text-mx-gold text-[10px]"></i>{{ $project->academicYear->name }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    {{-- نصوص ومهارات: بعرض العمود بالكامل --}}
                    <div class="order-1 lg:order-2 flex flex-col justify-center px-6 py-10 sm:px-10 sm:py-12 lg:px-12 w-full min-w-0 bg-gradient-to-b from-white to-mx-soft/40">
                        <div class="w-full max-w-none {{ $isRtl ? 'text-right' : 'text-left' }}">
                            <p class="inline-flex items-center gap-2 rounded-full bg-mx-cream text-mx-orange text-xs font-black px-3 py-1 mb-4 border border-orange-100">
                                <i class="fas fa-user-tie"></i> {{ $isEn ? 'About the author' : 'عن صاحب العرض' }}
                            </p>
                            <h2 class="font-heading text-3xl sm:text-4xl font-black text-mx-indigo leading-tight mb-3 break-words">{{ $u->name ?? __('public.student_fallback') }}</h2>
                            @if(!empty($marketing['headline']))
                                <p class="text-xl sm:text-2xl font-bold text-mx-navy leading-snug mb-6 border-s-4 border-mx-orange ps-4">{{ $marketing['headline'] }}</p>
                            @else
                                <p class="text-lg font-semibold text-slate-500 mb-6">{{ __('public.portfolio_heading') }}</p>
                            @endif
                            @if($aboutDisplay)
                                <div class="text-slate-700 leading-[1.85] text-base sm:text-lg whitespace-pre-line w-full">{{ $aboutDisplay }}</div>
                            @endif
                            @if($skillItems->isNotEmpty())
                                <div class="mt-8">
                                    <p class="text-xs font-black text-mx-indigo mb-3 opacity-80 {{ $isEn ? 'uppercase tracking-wide' : 'normal-case tracking-normal' }}">{{ $isEn ? 'Skills & focus' : 'المهارات والمجالات' }}</p>
                                    <div class="flex flex-wrap gap-2 w-full {{ $isRtl ? 'justify-end' : 'justify-start' }}">
                                        @foreach($skillItems as $sk)
                                            <span class="portfolio-skill-chip inline-block align-middle rounded-2xl bg-white border-2 border-slate-200/90 px-4 py-2 text-sm font-bold text-mx-indigo shadow-sm max-w-full">{{ $sk }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <div class="mt-10 flex flex-col sm:flex-row flex-wrap gap-3 w-full {{ $isRtl ? 'sm:justify-end' : 'sm:justify-start' }}">
                                @if(!empty($marketing['intro_video_url']))
                                    <a href="{{ $marketing['intro_video_url'] }}" target="_blank" rel="noopener noreferrer" class="btn-primary-mx text-sm px-6 py-3.5 justify-center sm:flex-1 sm:max-w-[240px]">
                                        <i class="fas fa-play-circle text-lg"></i>
                                        {{ $isEn ? 'Watch intro' : 'شاهد التعريف' }}
                                    </a>
                                @endif
                                <a href="{{ route('public.portfolio.index') }}" class="btn-secondary-mx text-sm px-6 py-3.5 justify-center sm:flex-1 sm:max-w-[240px] border-2 border-mx-navy/20">
                                    <i class="fas fa-th-large text-mx-orange text-lg"></i>
                                    {{ __('public.portfolio_page_title') }}
                                </a>
                            </div>
                            @if($project->published_at || $project->advancedCourse)
                                <div class="mt-10 pt-8 border-t border-slate-200/90 flex flex-wrap items-center gap-4 text-sm text-slate-600">
                                    @if($project->published_at)
                                        <span class="inline-flex items-center gap-2 font-bold">
                                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-mx-cream text-mx-orange"><i class="fas fa-calendar-check"></i></span>
                                            {{ $isEn ? 'Published' : 'تاريخ النشر' }}: <span class="text-mx-indigo">{{ $project->published_at->translatedFormat('j F Y') }}</span>
                                        </span>
                                    @endif
                                    @if($project->advancedCourse)
                                        <span class="inline-flex items-center gap-2 font-bold min-w-0 {{ $isRtl ? 'sm:mr-auto' : 'sm:ml-auto' }}">
                                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-mx-soft text-mx-navy"><i class="fas fa-graduation-cap"></i></span>
                                            <span class="text-mx-indigo truncate max-w-[220px] sm:max-w-md">{{ $project->advancedCourse->title }}</span>
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- محتوى المشروع --}}
    <section class="pb-16 sm:pb-20 bg-white">
        <div class="container-1200">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">
                <div class="lg:col-span-8 space-y-8">
                    @if($project->content_type === \App\Models\PortfolioProject::CONTENT_VIDEO && $project->video_url)
                        @php $embed = $project->videoEmbedUrl(); @endphp
                        <div class="reveal card-mx overflow-hidden aspect-video bg-black">
                            @if($embed)
                                <iframe src="{{ $embed }}" class="w-full h-full min-h-[220px]" title="{{ $project->title }}" allowfullscreen></iframe>
                            @else
                                <div class="w-full h-full min-h-[220px] flex items-center justify-center p-8">
                                    <a href="{{ $project->video_url }}" target="_blank" rel="noopener noreferrer" class="btn-primary-mx">
                                        <i class="fas fa-play"></i> {{ $isEn ? 'Watch video' : 'مشاهدة الفيديو' }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        @php $heroImg = \App\Services\PortfolioImageStorage::publicUrl($project->preview_image_path); @endphp
                        @if($heroImg)
                            <div class="reveal card-mx overflow-hidden">
                                <img src="{{ $heroImg }}" alt="{{ $project->title }}" class="w-full object-cover max-h-[480px]" loading="lazy">
                            </div>
                        @endif
                    @endif

                    @if($project->content_type === \App\Models\PortfolioProject::CONTENT_GALLERY && $project->images->count() > 0)
                        <div class="reveal s1 grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4">
                            @foreach($project->images as $img)
                                @php $gUrl = \App\Services\PortfolioImageStorage::publicUrl($img->image_path); @endphp
                                @if($gUrl)
                                    <button type="button" class="card-mx overflow-hidden hover-lift-mx p-0 border-0 bg-transparent cursor-pointer focus:outline-none focus:ring-2 focus:ring-mx-orange rounded-2xl" onclick="window.open('{{ $gUrl }}','_blank')">
                                        <img src="{{ $gUrl }}" alt="" class="w-full h-36 sm:h-44 object-cover" loading="lazy">
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    @if($project->description)
                        <div class="reveal card-mx p-6 sm:p-8">
                            <div class="flex items-center gap-3 mb-5">
                                <span class="w-12 h-12 rounded-2xl bg-mx-cream flex items-center justify-center text-mx-orange text-xl"><i class="fas fa-align-right"></i></span>
                                <h3 class="font-heading text-xl sm:text-2xl font-black text-mx-indigo">{{ $isEn ? 'About this work' : 'عن هذا العرض' }}</h3>
                            </div>
                            <div class="text-slate-600 leading-8 text-base whitespace-pre-line">{{ $project->description }}</div>
                        </div>
                    @endif

                    @if($project->content_type === \App\Models\PortfolioProject::CONTENT_TEXT && $project->content_text)
                        <div class="reveal card-mx p-6 sm:p-8 border-rose-100" style="background:linear-gradient(135deg,#fffdfb 0%,#fff 100%)">
                            <div class="flex items-center gap-3 mb-5">
                                <span class="w-12 h-12 rounded-2xl bg-mx-rose flex items-center justify-center text-mx-navy text-xl"><i class="fas fa-pen-fancy"></i></span>
                                <h3 class="font-heading text-xl sm:text-2xl font-black text-mx-indigo">{{ $isEn ? 'Details' : 'التفاصيل' }}</h3>
                            </div>
                            <div class="text-slate-700 leading-8 text-base whitespace-pre-line">{{ $project->content_text }}</div>
                        </div>
                    @endif

                    @if($project->content_type === \App\Models\PortfolioProject::CONTENT_LINK && $project->project_url)
                        <div class="reveal card-mx p-6 sm:p-8 text-center">
                            <a href="{{ $project->project_url }}" target="_blank" rel="noopener noreferrer" class="btn-primary-mx text-base px-8 py-4">
                                <i class="fas fa-external-link-alt"></i> {{ __('public.view_project') }}
                            </a>
                        </div>
                    @endif
                </div>

                <aside class="lg:col-span-4 space-y-6">
                    <div class="reveal lg:sticky lg:top-28 space-y-6">
                        <div class="card-mx overflow-hidden hover-lift-mx">
                            <div class="bg-gradient-to-l from-mx-navy to-mx-indigo px-5 py-4">
                                <h3 class="font-heading text-lg font-black text-white flex items-center gap-2">
                                    <i class="fas fa-link"></i> {{ $isEn ? 'Links' : 'روابط سريعة' }}
                                </h3>
                            </div>
                            <div class="p-5 space-y-3">
                                @if($project->project_url)
                                    <a href="{{ $project->project_url }}" target="_blank" rel="noopener noreferrer" class="btn-primary-mx w-full text-sm py-3.5">
                                        <i class="fas fa-external-link-alt"></i> {{ __('public.view_project') }}
                                    </a>
                                @endif
                                @if($project->github_url)
                                    <a href="{{ $project->github_url }}" target="_blank" rel="noopener noreferrer" class="btn-secondary-mx w-full text-sm py-3.5">
                                        <i class="fab fa-github text-lg"></i> GitHub
                                    </a>
                                @endif
                                @if(!$project->project_url && !$project->github_url)
                                    <p class="text-center text-slate-400 text-sm py-2">{{ $isEn ? 'No external links' : 'لا توجد روابط خارجية' }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="card-mx p-5 space-y-3">
                            @if($project->advancedCourse)
                                <div class="flex justify-between gap-3 items-start p-3 rounded-xl bg-mx-soft text-sm">
                                    <span class="text-slate-600 flex items-center gap-2 shrink-0 font-bold"><i class="fas fa-graduation-cap text-mx-orange"></i> {{ $isEn ? 'Course' : 'الكورس' }}</span>
                                    <span class="font-black text-mx-indigo text-end leading-snug">{{ $project->advancedCourse->title }}</span>
                                </div>
                            @endif
                            @if($project->academicYear)
                                <div class="flex justify-between gap-3 items-center p-3 rounded-xl bg-white border border-slate-100 text-sm">
                                    <span class="text-slate-600 flex items-center gap-2 font-bold"><i class="fas fa-bookmark text-mx-navy"></i> {{ $isEn ? 'Path' : 'المسار' }}</span>
                                    <span class="font-black text-mx-indigo">{{ $project->academicYear->name }}</span>
                                </div>
                            @endif
                            @if($project->published_at)
                                <div class="flex justify-between gap-3 items-center p-3 rounded-xl bg-white border border-slate-100 text-sm">
                                    <span class="text-slate-600 flex items-center gap-2 font-bold"><i class="fas fa-calendar text-mx-orange"></i> {{ $isEn ? 'Published' : 'النشر' }}</span>
                                    <span class="font-black text-mx-indigo">{{ $project->published_at->format('Y/m/d') }}</span>
                                </div>
                            @endif
                        </div>

                        <a href="{{ route('public.portfolio.index') }}" class="btn-secondary-mx w-full py-4 text-sm font-black border-2 border-mx-navy/15 hover:border-mx-orange/40">
                            <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }} text-mx-orange"></i>
                            {{ __('public.back_to_gallery') }}
                        </a>
                    </div>
                </aside>
            </div>

            @if($related->count() > 0)
                <div class="mt-16 sm:mt-24 reveal">
                    <div class="text-center mb-10">
                        <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-black mb-4" style="background:#FFE5F7;color:#283593;border:1px solid #f5c7e8">Glottical</span>
                        <h2 class="font-heading text-2xl sm:text-3xl font-black text-mx-indigo">{{ __('public.other_projects_same_path') }}</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        @foreach($related as $idx => $r)
                            <a href="{{ route('public.portfolio.show', $r->id) }}" class="reveal card-mx hover-lift-mx flex gap-4 p-4 sm:p-5 group s{{ min($idx + 1, 4) }}">
                                @php $relThumb = \App\Services\PortfolioImageStorage::publicUrl($r->preview_image_path); @endphp
                                @if($relThumb)
                                    <div class="w-28 h-28 flex-shrink-0 rounded-2xl overflow-hidden bg-slate-100 ring-2 ring-slate-100 group-hover:ring-mx-orange/30 transition-all">
                                        <img src="{{ $relThumb }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                    </div>
                                @else
                                    <div class="w-28 h-28 flex-shrink-0 rounded-2xl bg-gradient-to-br from-mx-navy to-mx-orange flex items-center justify-center">
                                        <i class="fas fa-image text-white/80 text-2xl"></i>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0 flex flex-col justify-center">
                                    <h4 class="font-black text-mx-indigo group-hover:text-mx-orange transition-colors leading-snug mb-2 line-clamp-2">{{ $r->title }}</h4>
                                    <div class="flex items-center gap-2 text-sm text-slate-600">
                                        @if($r->user->public_portfolio_marketing_photo_url)
                                            <img src="{{ $r->user->public_portfolio_marketing_photo_url }}" alt="" class="w-8 h-8 rounded-lg object-cover ring-1 ring-slate-200">
                                        @else
                                            <span class="w-8 h-8 rounded-lg bg-mx-soft flex items-center justify-center text-xs font-black text-mx-navy">{{ mb_substr($r->user->name ?? 'م', 0, 1) }}</span>
                                        @endif
                                        <span class="font-bold truncate">{{ $r->user->name ?? __('public.student_fallback') }}</span>
                                    </div>
                                </div>
                                <span class="self-center w-10 h-10 rounded-xl bg-mx-soft group-hover:bg-mx-cream flex items-center justify-center flex-shrink-0 transition-colors">
                                    <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }} text-sm text-mx-orange"></i>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
</main>

@include('components.unified-footer')

<style>.line-clamp-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}</style>
<script>
(function(){
    function bar(){
        var s = window.pageYOffset || document.documentElement.scrollTop;
        var h = document.documentElement.scrollHeight - window.innerHeight;
        var b = document.getElementById('scroll-progress');
        if (b) b.style.width = (h > 0 ? (s / h) * 100 : 0) + '%';
    }
    window.addEventListener('scroll', bar, { passive: true });
    function reveal(){
        var nodes = document.querySelectorAll('.reveal');
        if (!nodes.length) return;
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (en.isIntersecting) {
                    en.target.classList.add('revealed');
                    io.unobserve(en.target);
                }
            });
        }, { threshold: 0.06, rootMargin: '0px 0px -32px 0px' });
        nodes.forEach(function (el) { io.observe(el); });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', reveal);
    else reveal();
})();
</script>
</body>
</html>
