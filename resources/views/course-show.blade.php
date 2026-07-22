@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $thumbUrl = $course->thumbnail_url;
    $introVideoUrl = trim((string) ($course->video_url ?? ''));
    $introEmbedUrl = \App\Helpers\VideoHelper::getEmbedUrl($introVideoUrl);
    $introDirectVideo = \App\Helpers\VideoHelper::getDirectVideoUrl($introVideoUrl);
    $categoryDisplay = $course->courseCategory?->name ?? __('public.course_category_not_set');
    $isMonthly = $course->isMonthlyBilling();
    $checkoutPrice = $course->effectiveCheckoutPrice();
    $isPaid = $checkoutPrice > 0 && ! ($course->is_free ?? false);
    $hasPromo = $isPaid && $course->hasPromotionalPrice();
    $listPrice = $hasPromo ? $course->listPriceAmount() : 0;
    $savedAmount = $hasPromo ? max(0, $listPrice - $checkoutPrice) : 0;
    $discountPct = ($hasPromo && $listPrice > 0)
        ? (int) round((1 - ($checkoutPrice / $listPrice)) * 100)
        : 0;
    $instructorApproved = $course->instructor
        && \App\Models\InstructorProfile::where('user_id', $course->instructor->id)->where('status', 'approved')->exists();
    $subjectName = $course->academicSubject->name ?? __('public.course_category_not_set');
    $learnPoints = $course->what_you_learn
        ? array_values(array_filter(array_map('trim', explode("\n", $course->what_you_learn))))
        : [];
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    @php
        $courseOgImg = $thumbUrl ?? asset('images/og-image.jpg');
        $courseDesc = Str::limit(strip_tags($course->description ?? ''), 160);
        $courseTitle = ($course->title ?? __('public.course_detail_title')) . ' | Glottical';
        $courseUrl = url('/course/' . ($course->id ?? ''));
    @endphp
    <title>{{ $courseTitle }}</title>
    <meta name="title" content="{{ $courseTitle }}">
    <meta name="description" content="{{ $courseDesc }}">
    <meta name="keywords" content="{{ $course->title ?? 'كورس' }}, تعلم أونلاين, كورسات عربية, Glottical, {{ $categoryDisplay }}">
    <meta name="author" content="{{ ($course->instructor->name ?? null) ?? 'Glottical' }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">
    <meta name="theme-color" content="#0f5c57">
    <link rel="canonical" href="{{ $courseUrl }}">
    <link rel="alternate" hreflang="ar" href="{{ $courseUrl }}?lang=ar">
    <link rel="alternate" hreflang="en" href="{{ $courseUrl }}?lang=en">
    <link rel="alternate" hreflang="x-default" href="{{ $courseUrl }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ $courseUrl }}">
    <meta property="og:title" content="{{ $courseTitle }}">
    <meta property="og:description" content="{{ $courseDesc }}">
    <meta property="og:image" content="{{ $courseOgImg }}">
    <meta property="og:image:alt" content="{{ $course->title ?? 'كورس' }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="{{ $locale === 'ar' ? 'ar_AR' : 'en_US' }}">
    <meta property="og:site_name" content="Glottical">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@Glottical">
    <meta name="twitter:url" content="{{ $courseUrl }}">
    <meta name="twitter:title" content="{{ $courseTitle }}">
    <meta name="twitter:description" content="{{ $courseDesc }}">
    <meta name="twitter:image" content="{{ $courseOgImg }}">
    <meta name="twitter:image:alt" content="{{ $course->title ?? 'كورس' }}">
    @include('partials.seo-jsonld', ['jsonldType' => 'course', 'course' => $course])
    @include('partials.favicon-links')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    @include('partials.atheer-head')
</head>
<body class="font-sans antialiased">
@include('partials.atheer-home-header')

<main class="page-enter pb-24">
    @if (session('success'))
        <div class="container-wide pt-6" data-flash>
            <div class="flex items-start gap-3 rounded-2xl border border-line bg-surface px-5 py-4 shadow-soft">
                <span class="mt-0.5 inline-flex size-8 shrink-0 items-center justify-center rounded-xl bg-success/10 text-success">✓</span>
                <p class="flex-1 text-sm font-medium leading-7 text-ink">{{ session('success') }}</p>
                <button type="button" class="text-muted transition hover:text-ink" data-flash-close aria-label="{{ $isRtl ? 'إغلاق' : 'Close' }}">×</button>
            </div>
        </div>
    @endif
    @if (session('info'))
        <div class="container-wide pt-6" data-flash>
            <div class="flex items-start gap-3 rounded-2xl border border-line bg-surface px-5 py-4 shadow-soft">
                <span class="mt-0.5 inline-flex size-8 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent">i</span>
                <p class="flex-1 text-sm font-medium leading-7 text-ink">{{ session('info') }}</p>
                <button type="button" class="text-muted transition hover:text-ink" data-flash-close aria-label="{{ $isRtl ? 'إغلاق' : 'Close' }}">×</button>
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="container-wide pt-6" data-flash>
            <div class="flex items-start gap-3 rounded-2xl border border-line bg-surface px-5 py-4 shadow-soft">
                <span class="mt-0.5 inline-flex size-8 shrink-0 items-center justify-center rounded-xl bg-danger/10 text-danger">!</span>
                <p class="flex-1 text-sm font-medium leading-7 text-ink">{{ session('error') }}</p>
                <button type="button" class="text-muted transition hover:text-ink" data-flash-close aria-label="{{ $isRtl ? 'إغلاق' : 'Close' }}">×</button>
            </div>
        </div>
    @endif

    <nav class="container-wide py-6 md:py-8" aria-label="{{ $isRtl ? 'مسار التنقل' : 'Breadcrumb' }}">
        <ol class="flex flex-wrap items-center gap-2 text-sm text-muted">
            <li><a href="{{ url('/') }}" class="transition hover:text-ink">{{ __('public.home') }}</a></li>
            <li aria-hidden="true" class="text-line">/</li>
            <li><a href="{{ route('public.courses') }}" class="transition hover:text-ink">{{ __('public.courses') }}</a></li>
            @if ($course->courseCategory)
                <li aria-hidden="true" class="text-line">/</li>
                <li><a href="{{ route('public.categories') }}" class="transition hover:text-ink">{{ $categoryDisplay }}</a></li>
            @endif
            <li aria-hidden="true" class="text-line">/</li>
            <li class="font-medium text-ink" aria-current="page">{{ Str::limit($course->title ?? '', 48) }}</li>
        </ol>
    </nav>

    {{-- Hero: media + sticky purchase panel --}}
    <section class="container-wide grid gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:gap-14">
        <div class="space-y-4">
            <div class="relative overflow-hidden rounded-3xl bg-canvas-muted shadow-soft {{ ($introEmbedUrl || $introDirectVideo) ? 'aspect-video' : 'aspect-[4/5] sm:aspect-[4/5]' }}">
                @if ($introEmbedUrl)
                    <iframe
                        src="{{ $introEmbedUrl }}"
                        title="{{ __('public.course_intro_video') }}"
                        class="absolute inset-0 h-full w-full"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen; web-share"
                        allowfullscreen
                        loading="lazy"
                        referrerpolicy="strict-origin-when-cross-origin"
                    ></iframe>
                @elseif ($introDirectVideo)
                    <video
                        src="{{ $introDirectVideo }}"
                        controls
                        playsinline
                        webkit-playsinline
                        preload="metadata"
                        poster="{{ $thumbUrl }}"
                        class="absolute inset-0 h-full w-full object-contain bg-ink"
                    >{{ __('public.course_intro_video_unsupported') }}</video>
                @elseif ($thumbUrl)
                    <img src="{{ $thumbUrl }}" alt="{{ $course->title }}" class="h-full w-full object-cover" width="900" height="1125">
                @else
                    <span class="flex h-full w-full items-center justify-center bg-gradient-to-br from-accent to-ink text-5xl text-white/35">✦</span>
                @endif

                @if ($course->is_featured ?? false)
                    <span class="absolute {{ $isRtl ? 'right-4' : 'left-4' }} top-4 rounded-xl bg-surface/95 px-3 py-1.5 text-xs font-semibold text-accent shadow-soft backdrop-blur-sm">
                        {{ __('public.featured_course_badge') }}
                    </span>
                @endif
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="rounded-2xl border border-line bg-surface px-3 py-3 text-center shadow-soft">
                    <p class="text-lg font-semibold tabular-nums text-ink">{{ $course->lessons_count ?? 0 }}</p>
                    <p class="mt-0.5 text-[11px] text-muted">{{ __('public.lecture_single') }}</p>
                </div>
                <div class="rounded-2xl border border-line bg-surface px-3 py-3 text-center shadow-soft">
                    <p class="text-lg font-semibold tabular-nums text-ink">{{ $course->duration_hours ?? 0 }}</p>
                    <p class="mt-0.5 text-[11px] text-muted">{{ __('public.hours') }}</p>
                </div>
                <div class="rounded-2xl border border-line bg-surface px-3 py-3 text-center shadow-soft">
                    <p class="truncate text-sm font-semibold text-ink" title="{{ $categoryDisplay }}">{{ Str::limit($categoryDisplay, 14) }}</p>
                    <p class="mt-0.5 text-[11px] text-muted">{{ __('public.course_category_label') }}</p>
                </div>
            </div>
        </div>

        <div class="space-y-6 lg:sticky lg:top-28 lg:self-start">
            <div class="space-y-3">
                @if ($course->instructor)
                    @if ($instructorApproved)
                        <a href="{{ route('public.instructors.show', $course->instructor) }}" class="inline-flex text-sm font-medium text-accent transition hover:text-ink">{{ $course->instructor->name }}</a>
                    @else
                        <p class="text-sm font-medium text-accent">{{ $course->instructor->name }}</p>
                    @endif
                @endif

                <h1 class="text-balance text-3xl font-semibold leading-tight text-ink md:text-4xl">
                    {{ $course->title ?? __('public.course_title_fallback') }}
                </h1>

                <div class="flex flex-wrap items-center gap-2.5">
                    @if ($discountPct > 0)
                        <span class="rounded-lg bg-danger/10 px-2.5 py-1 text-xs font-semibold text-danger">
                            {{ $isRtl ? "خصم {$discountPct}%" : "{$discountPct}% off" }}
                        </span>
                    @endif
                    @if ($course->is_featured ?? false)
                        <span class="rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-semibold text-accent">{{ __('public.featured_course_badge') }}</span>
                    @endif
                    @if (! $isPaid)
                        <span class="rounded-lg bg-success/10 px-2.5 py-1 text-xs font-semibold text-success">{{ __('public.free_price') }}</span>
                    @endif
                </div>

                @if ($course->description)
                    <p class="text-base leading-8 text-muted line-clamp-3">{{ strip_tags($course->description) }}</p>
                @endif
            </div>

            {{-- Price box --}}
            <div class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                @if ($isPaid)
                    <div class="flex flex-wrap items-baseline gap-3">
                        <p class="text-3xl font-semibold tabular-nums text-ink">
                            {{ number_format($checkoutPrice, 0) }}
                            <span class="text-lg font-normal">{{ __('public.currency_egp') }}</span>
                            @if ($isMonthly)
                                <span class="text-base font-normal text-muted">/ {{ __('public.per_month') }}</span>
                            @endif
                        </p>
                        @if ($hasPromo)
                            <p class="text-base tabular-nums text-muted line-through">{{ number_format($listPrice, 0) }} {{ __('public.currency_egp') }}</p>
                            @if ($savedAmount > 0)
                                <span class="rounded-lg bg-success/10 px-2 py-0.5 text-xs font-semibold text-success">
                                    {{ $isRtl ? 'وفّرت '.number_format($savedAmount, 0).' '.__('public.currency_egp') : 'Save '.number_format($savedAmount, 0).' '.__('public.currency_egp') }}
                                </span>
                            @endif
                        @endif
                    </div>
                    @if ($isMonthly && $course->isOneToOne() && $course->instructor)
                        <p class="mt-3 text-sm text-muted">{{ __('public.one_to_one_with') }} {{ $course->instructor->name }}</p>
                    @elseif ($isMonthly)
                        <p class="mt-3 text-sm text-muted">{{ __('public.checkout_monthly_notice') }}</p>
                    @endif
                @else
                    <p class="flex items-center gap-2 text-2xl font-semibold text-success">
                        <span aria-hidden="true">✦</span>
                        {{ __('public.free_price') }}
                    </p>
                @endif

                <p class="mt-3 flex items-center gap-2 text-sm font-medium text-success">
                    <span class="inline-block size-2 rounded-full bg-success" aria-hidden="true"></span>
                    {{ $isRtl ? 'وصول فوري بعد التفعيل' : 'Instant access after activation' }}
                </p>
            </div>

            {{-- Specs chips --}}
            <dl class="grid gap-2 sm:grid-cols-2">
                <div class="rounded-xl border border-line bg-canvas px-4 py-3">
                    <dt class="text-xs text-muted">{{ __('public.duration') }}</dt>
                    <dd class="mt-1 text-sm font-semibold text-ink">{{ $course->duration_hours ?? 0 }} {{ __('public.hours') }}</dd>
                </div>
                <div class="rounded-xl border border-line bg-canvas px-4 py-3">
                    <dt class="text-xs text-muted">{{ __('public.lectures_count_label') }}</dt>
                    <dd class="mt-1 text-sm font-semibold text-ink">{{ $course->lessons_count ?? 0 }}</dd>
                </div>
                <div class="rounded-xl border border-line bg-canvas px-4 py-3">
                    <dt class="text-xs text-muted">{{ __('public.course_category_label') }}</dt>
                    <dd class="mt-1 text-sm font-semibold text-ink">{{ $categoryDisplay }}</dd>
                </div>
                <div class="rounded-xl border border-line bg-canvas px-4 py-3">
                    <dt class="text-xs text-muted">{{ __('public.subject_label') }}</dt>
                    <dd class="mt-1 text-sm font-semibold text-ink">{{ $subjectName }}</dd>
                </div>
            </dl>

            {{-- CTA --}}
            <div class="flex flex-wrap items-center gap-3">
                @auth
                    @if ($isEnrolled ?? false)
                        <a href="{{ route('my-courses.show', $course) }}" class="btn-press inline-flex h-12 flex-1 min-w-[180px] items-center justify-center gap-2 rounded-xl bg-accent px-6 text-sm font-semibold text-white shadow-[0_10px_24px_rgba(15,92,87,0.22)] transition hover:bg-[#0d4f4a]">
                            {{ __('public.start_learning_now') }}
                        </a>
                    @elseif ($isPaid)
                        <a href="{{ route('public.course.checkout', $course->id) }}" class="btn-press inline-flex h-12 flex-1 min-w-[180px] items-center justify-center gap-2 rounded-xl bg-accent px-6 text-sm font-semibold text-white shadow-[0_10px_24px_rgba(15,92,87,0.22)] transition hover:bg-[#0d4f4a]">
                            {{ __('public.buy_now') }}
                        </a>
                    @else
                        <form action="{{ route('public.course.enroll.free', $course->id) }}" method="POST" class="flex-1 min-w-[180px]">
                            @csrf
                            <button type="submit" class="btn-press inline-flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-accent px-6 text-sm font-semibold text-white shadow-[0_10px_24px_rgba(15,92,87,0.22)] transition hover:bg-[#0d4f4a]">
                                {{ __('public.register_free') }}
                            </button>
                        </form>
                    @endif
                @endauth
                @guest
                    @if ($isPaid)
                        <a href="{{ route('register', ['redirect' => route('public.course.checkout', $course->id)]) }}" class="btn-press inline-flex h-12 flex-1 min-w-[180px] items-center justify-center gap-2 rounded-xl bg-accent px-6 text-sm font-semibold text-white shadow-[0_10px_24px_rgba(15,92,87,0.22)] transition hover:bg-[#0d4f4a]">
                            {{ __('public.buy_now') }}
                        </a>
                    @else
                        <a href="{{ route('register', ['redirect' => route('public.course.show', $course->id)]) }}" class="btn-press inline-flex h-12 flex-1 min-w-[180px] items-center justify-center gap-2 rounded-xl bg-accent px-6 text-sm font-semibold text-white shadow-[0_10px_24px_rgba(15,92,87,0.22)] transition hover:bg-[#0d4f4a]">
                            {{ __('public.register_free') }}
                        </a>
                    @endif
                @endguest

                <a href="{{ route('public.courses') }}" class="inline-flex size-12 items-center justify-center rounded-xl border border-line bg-surface transition hover:border-accent hover:text-accent" aria-label="{{ __('public.all_courses') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                </a>
            </div>

            <a href="{{ route('public.courses') }}" class="inline-flex items-center gap-2 text-sm font-medium text-accent transition hover:text-ink">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="{{ $isRtl ? '' : 'rotate-180' }}" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                {{ __('public.all_courses') }}
            </a>

            {{-- Trust row --}}
            <div class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-line bg-canvas p-4">
                    <div class="mb-2 flex size-9 items-center justify-center rounded-xl bg-accent-soft text-accent">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-ink">{{ __('public.checkout_trust_secure') }}</p>
                    <p class="mt-1 text-xs leading-6 text-muted">{{ __('public.secure_checkout_badge') }}</p>
                </div>
                <div class="rounded-2xl border border-line bg-canvas p-4">
                    <div class="mb-2 flex size-9 items-center justify-center rounded-xl bg-accent-soft text-accent">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-ink">{{ __('public.checkout_trust_fast') }}</p>
                    <p class="mt-1 text-xs leading-6 text-muted">{{ $isRtl ? 'يُفعَّل الوصول بعد إتمام الطلب' : 'Access unlocks after order completion' }}</p>
                </div>
                <div class="rounded-2xl border border-line bg-canvas p-4">
                    <div class="mb-2 flex size-9 items-center justify-center rounded-xl bg-accent-soft text-accent">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-ink">{{ __('public.checkout_benefit_certificate') }}</p>
                    <p class="mt-1 text-xs leading-6 text-muted">{{ $isRtl ? 'عند إتمام متطلبات الكورس' : 'Upon completing course requirements' }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- About + specs --}}
    <section class="container-wide mt-16 md:mt-20 grid gap-8 lg:grid-cols-2">
        <article class="rounded-3xl border border-line bg-surface p-6 shadow-soft md:p-8">
            <h2 class="mb-4 text-xl font-semibold text-ink">{{ __('public.about_course') }}</h2>
            <p class="text-sm leading-8 text-muted whitespace-pre-line">{{ $course->description ?? __('public.course_desc_fallback') }}</p>

            @if ($course->objectives)
                <h3 class="mb-3 mt-8 text-sm font-semibold text-ink">{{ __('public.course_objectives') }}</h3>
                <p class="rounded-2xl bg-canvas p-4 text-sm leading-8 text-muted whitespace-pre-line">{{ $course->objectives }}</p>
            @endif

            @if (count($learnPoints))
                <h3 class="mb-3 mt-8 text-sm font-semibold text-ink">{{ __('public.what_you_learn') }}</h3>
                <ul class="space-y-2.5 text-sm text-muted">
                    @foreach ($learnPoints as $point)
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 shrink-0 text-success" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                            <span>{{ $point }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </article>

        <div class="space-y-8">
            <article class="rounded-3xl border border-line bg-surface p-6 shadow-soft md:p-8">
                <h2 class="mb-4 text-xl font-semibold text-ink">{{ $isRtl ? 'تفاصيل الكورس' : 'Course details' }}</h2>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-line">
                        <tr>
                            <th class="py-3 pe-4 text-start font-medium text-muted">{{ __('public.course_category_label') }}</th>
                            <td class="py-3 text-ink">{{ $categoryDisplay }}</td>
                        </tr>
                        <tr>
                            <th class="py-3 pe-4 text-start font-medium text-muted">{{ __('public.subject_label') }}</th>
                            <td class="py-3 text-ink">{{ $subjectName }}</td>
                        </tr>
                        <tr>
                            <th class="py-3 pe-4 text-start font-medium text-muted">{{ __('public.duration') }}</th>
                            <td class="py-3 text-ink">{{ $course->duration_hours ?? 0 }} {{ __('public.hours') }}</td>
                        </tr>
                        <tr>
                            <th class="py-3 pe-4 text-start font-medium text-muted">{{ __('public.lectures_count_label') }}</th>
                            <td class="py-3 text-ink">{{ $course->lessons_count ?? 0 }}</td>
                        </tr>
                        @if ($course->instructor)
                            <tr>
                                <th class="py-3 pe-4 text-start font-medium text-muted">{{ __('public.instructor_label') }}</th>
                                <td class="py-3 text-ink">
                                    @if ($instructorApproved)
                                        <a href="{{ route('public.instructors.show', $course->instructor) }}" class="font-medium text-accent transition hover:text-ink">{{ $course->instructor->name }}</a>
                                    @else
                                        {{ $course->instructor->name }}
                                    @endif
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <th class="py-3 pe-4 text-start font-medium text-muted">{{ $isRtl ? 'نوع الاشتراك' : 'Billing' }}</th>
                            <td class="py-3 text-ink">
                                @if (! $isPaid)
                                    {{ __('public.free_price') }}
                                @elseif ($isMonthly)
                                    {{ __('public.checkout_monthly_price_label') }}
                                @else
                                    {{ __('public.checkout_benefit_lifetime') }}
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </article>

            @if ($course->requirements)
                <article class="rounded-3xl border border-line bg-surface p-6 shadow-soft md:p-8">
                    <h2 class="mb-4 text-xl font-semibold text-ink">{{ __('public.requirements') }}</h2>
                    <p class="rounded-2xl bg-canvas p-4 text-sm leading-8 text-muted whitespace-pre-line">{{ $course->requirements }}</p>
                </article>
            @endif
        </div>
    </section>

    {{-- Related --}}
    @if (isset($relatedCourses) && $relatedCourses->isNotEmpty())
        <section class="container-wide mt-16 md:mt-20">
            <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
                <div class="space-y-2">
                    <p class="text-sm font-medium text-accent">{{ $isRtl ? 'قد يعجبك أيضاً' : 'You may also like' }}</p>
                    <h2 class="text-2xl font-semibold text-ink">{{ $isRtl ? 'كورسات ذات صلة' : 'Related courses' }}</h2>
                </div>
                <a href="{{ route('public.courses') }}" class="inline-flex h-10 items-center rounded-xl border border-line px-4 text-sm transition hover:border-accent hover:text-accent">
                    {{ __('public.all_courses') }}
                </a>
            </div>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-3">
                @foreach ($relatedCourses->take(3) as $related)
                    @include('partials.landing-course-card-site', ['course' => $related])
                @endforeach
            </div>
        </section>
    @endif

    {{-- Bottom CTA --}}
    <section class="container-wide mt-16 md:mt-20">
        <div class="relative overflow-hidden rounded-3xl bg-ink px-6 py-10 text-white shadow-lift md:px-10 md:py-12">
            <div class="pointer-events-none absolute inset-0 opacity-40" style="background:radial-gradient(ellipse at 20% 0%,rgba(15,92,87,.45),transparent 50%),radial-gradient(ellipse at 90% 100%,rgba(176,141,87,.25),transparent 45%)"></div>
            <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div class="max-w-xl space-y-3">
                    <p class="text-sm font-medium text-metal">{{ $isRtl ? 'جاهز للانطلاق؟' : 'Ready to start?' }}</p>
                    <h2 class="text-balance text-2xl font-semibold md:text-3xl">
                        {{ $isRtl ? 'سجّل الآن وابدأ التعلم بخطوات واضحة' : 'Enroll now and start learning with clear steps' }}
                    </h2>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row md:shrink-0">
                    @auth
                        @if ($isEnrolled ?? false)
                            <a href="{{ route('my-courses.show', $course) }}" class="btn-press inline-flex h-12 items-center justify-center rounded-xl bg-accent px-6 text-sm font-medium text-white transition hover:bg-[#0d4f4a]">{{ __('public.start_learning_now') }}</a>
                        @elseif ($isPaid)
                            <a href="{{ route('public.course.checkout', $course->id) }}" class="btn-press inline-flex h-12 items-center justify-center rounded-xl bg-accent px-6 text-sm font-medium text-white transition hover:bg-[#0d4f4a]">{{ __('public.buy_now') }}</a>
                        @else
                            <form action="{{ route('public.course.enroll.free', $course->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-press inline-flex h-12 w-full items-center justify-center rounded-xl bg-accent px-6 text-sm font-medium text-white transition hover:bg-[#0d4f4a]">{{ __('public.register_free') }}</button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('register', ['redirect' => $isPaid ? route('public.course.checkout', $course->id) : route('public.course.show', $course->id)]) }}" class="btn-press inline-flex h-12 items-center justify-center rounded-xl bg-accent px-6 text-sm font-medium text-white transition hover:bg-[#0d4f4a]">
                            {{ $isPaid ? __('public.buy_now') : __('public.register_free_now') }}
                        </a>
                    @endauth
                    <a href="{{ route('public.courses') }}" class="btn-press inline-flex h-12 items-center justify-center rounded-xl border border-white/20 bg-white/5 px-6 text-sm font-medium transition hover:bg-white/10">{{ __('public.all_courses') }}</a>
                </div>
            </div>
        </div>
    </section>
</main>

@include('partials.atheer-home-footer')

<script>
  document.querySelectorAll('[data-open-free-trial]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      window.location.href = {{ \Illuminate\Support\Js::from(url('/?open_trial=1')) }};
    });
  });
  document.querySelectorAll('[data-flash-close]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var wrap = btn.closest('[data-flash]');
      if (wrap) wrap.remove();
    });
  });
</script>
</body>
</html>
