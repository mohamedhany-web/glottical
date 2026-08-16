@php
    $pt = $presentationTitle ?? 'عرض تفاعلي';
    $mode = $mode ?? 'office';
    $hasAnimationVideo = !empty($hasAnimationVideo) && !empty($animationVideoUrl);
    $isNative = $mode === 'native' && !empty($manifestUrl);
    $defaultPane = $hasAnimationVideo ? 'animation' : ($isNative ? 'slides' : 'office');
    $viewer = auth()->user();
    $useStudentChrome = $viewer && $viewer->isStudent();
    $backUrl = $itemShowUrl ?? route('curriculum-library.show', $item);
@endphp

@extends($useStudentChrome ? 'layouts.student-timeline' : 'layouts.app')

@section('title', $pt.' — '.$item->title)
@section('header', $item->title)
@section('enable-content-protection', ($isNative || $hasAnimationVideo) ? 'true' : '')

@push('styles')
    @if($isNative)
        <link rel="stylesheet" href="{{ vasset('css/curriculum-slide-player.css') }}">
    @endif
    @unless($useStudentChrome)
        <link rel="stylesheet" href="{{ route('assets.student-timeline.css') }}?v=st-pres-1">
    @endunless
@endpush

@section('content')
@if($useStudentChrome)
    @include('partials.student-timeline-top', [
        'locale' => app()->getLocale(),
        'pageTitle' => $pt,
        'crumbs' => [
            ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
            ['label' => __('student_timeline.family_library_title'), 'url' => route('student.library.home')],
            ['label' => __('student_timeline.nav_library_curriculum'), 'url' => route('student.library.curriculum')],
            ['label' => $item->title, 'url' => $backUrl],
            ['label' => $pt, 'url' => null],
        ],
    ])
@endif

<div class="st-pres"
     data-mx-presentation-root
     data-default-pane="{{ $defaultPane }}"
     @if($hasAnimationVideo) data-has-animation-video="1" @endif>

    @unless($useStudentChrome)
        <p class="st-pres-back">
            <a href="{{ $backUrl }}"><i class="fas fa-arrow-right" aria-hidden="true"></i> العودة لصفحة المنهج</a>
        </p>
    @endunless

    <section class="st-xp-head">
        <div>
            <p class="st-xp-kicker">{{ __('student_timeline.lib_manahij_title') }}</p>
            <h2>{{ $pt }}</h2>
            <p>العرض داخل المنصة فقط؛ التحميل غير متاح لهذا النوع.</p>
        </div>
        <div class="st-xp-actions">
            <span class="st-pres-lock"><i class="fas fa-lock" aria-hidden="true"></i> بدون تحميل</span>
            <a href="{{ $backUrl }}" class="st-pill st-pill--outline">{{ $useStudentChrome ? __('student_timeline.lib_manahij_title') : 'رجوع للمنهج' }}</a>
        </div>
    </section>

    <section class="st-cl-panel st-pres-panel" aria-label="{{ $pt }}">
        @if($hasAnimationVideo)
            <div class="st-pres-tabs" role="tablist" aria-label="وضع العرض">
                <button type="button"
                        data-mx-pane-btn="animation"
                        class="st-pill st-pill--solid"
                        aria-selected="true">
                    <i class="fas fa-film" aria-hidden="true"></i> العرض بالحركات
                </button>
                <button type="button"
                        data-mx-pane-btn="slides"
                        class="st-pill st-pill--outline"
                        aria-selected="false">
                    <i class="fas fa-images" aria-hidden="true"></i> تصفح الشرائح
                </button>
            </div>
            <p data-mx-animation-hint class="st-pres-hint">
                فيديو الحركات يحافظ على حركات PowerPoint والصوت والتوقيت كما صُدّر من الملف الأصلي.
                للتنقل بين الشرائح يدوياً استخدم وضع «تصفح الشرائح».
            </p>
        @endif

        @if($hasAnimationVideo)
            <div id="mx-animation-pane"
                 data-mx-pane="animation"
                 class="{{ $defaultPane === 'animation' ? '' : 'hidden' }}"
                 @if($defaultPane !== 'animation') hidden aria-hidden="true" @endif>
                <div class="st-pres-stage">
                    <video id="mx-animation-video"
                           class="st-pres-video"
                           controls
                           playsinline
                           controlslist="nodownload"
                           disablepictureinpicture
                           preload="metadata"
                           src="{{ $animationVideoUrl }}">
                        متصفحك لا يدعم تشغيل الفيديو.
                    </video>
                </div>
                <p class="st-pres-hint st-pres-hint--center">إذا تعذّر تشغيل الفيديو سيتم التحويل تلقائياً إلى تصفح الشرائح.</p>
            </div>
        @endif

        <div id="mx-slides-pane"
             data-mx-pane="slides"
             class="{{ ($hasAnimationVideo && $defaultPane === 'animation') ? 'hidden' : '' }}"
             @if($hasAnimationVideo && $defaultPane === 'animation') hidden aria-hidden="true" @endif>
            @if($isNative)
                @include('student.curriculum-library._slide-player', [
                    'manifestUrl' => $manifestUrl,
                    'slideCount' => $slideCount ?? null,
                    'slideWidth' => $slideWidth ?? null,
                    'slideHeight' => $slideHeight ?? null,
                    'playerConfig' => $playerConfig ?? [],
                ])
            @endif

            <div id="mx-office-fallback"
                 class="{{ $isNative ? 'hidden' : '' }}"
                 data-mx-office-fallback
                 @if($isNative) hidden aria-hidden="true" @endif>
                @include('student.curriculum-library._office-fallback', [
                    'canUseOfficeViewer' => $canUseOfficeViewer ?? false,
                    'embedUrl' => $embedUrl ?? null,
                    'publicUrl' => $isNative ? null : ($publicUrl ?? null),
                ])
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
    @if($isNative)
        <script src="{{ vasset('js/curriculum-slide-player.js') }}" defer></script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var root = document.querySelector('[data-mx-presentation-root]');
            var hasAnimation = root && root.getAttribute('data-has-animation-video') === '1';
            var slidePlayerMounted = false;

            function setActivePane(pane) {
                var panes = document.querySelectorAll('[data-mx-pane]');
                panes.forEach(function (el) {
                    var on = el.getAttribute('data-mx-pane') === pane;
                    el.classList.toggle('hidden', !on);
                    if (on) {
                        el.removeAttribute('hidden');
                        el.setAttribute('aria-hidden', 'false');
                    } else {
                        el.setAttribute('hidden', 'hidden');
                        el.setAttribute('aria-hidden', 'true');
                    }
                });
                document.querySelectorAll('[data-mx-pane-btn]').forEach(function (btn) {
                    var on = btn.getAttribute('data-mx-pane-btn') === pane;
                    btn.setAttribute('aria-selected', on ? 'true' : 'false');
                    btn.classList.toggle('is-active', on);
                    btn.classList.toggle('st-pill--solid', on);
                    btn.classList.toggle('st-pill--outline', !on);
                });
                var hint = document.querySelector('[data-mx-animation-hint]');
                if (hint) {
                    hint.classList.toggle('hidden', pane !== 'animation');
                }
                if (pane === 'slides') {
                    mountSlidePlayerOnce();
                    var video = document.getElementById('mx-animation-video');
                    if (video && !video.paused) {
                        try { video.pause(); } catch (e) {}
                    }
                }
            }

            function mountSlidePlayerOnce() {
                if (slidePlayerMounted) return;
                var playerRoot = document.getElementById('mx-slide-player');
                if (!playerRoot) return;
                if (!window.MXCurriculumSlidePlayer) {
                    var fb = document.getElementById('mx-office-fallback');
                    if (playerRoot) playerRoot.classList.add('hidden');
                    if (fb) {
                        fb.classList.remove('hidden');
                        fb.removeAttribute('hidden');
                        fb.setAttribute('aria-hidden', 'false');
                    }
                    slidePlayerMounted = true;
                    return;
                }
                window.MXCurriculumSlidePlayer.mount(playerRoot, {
                    manifestUrl: playerRoot.getAttribute('data-manifest-url'),
                    initialIndex: 1,
                    rtl: true,
                    fallbackSelector: '#mx-office-fallback',
                    features: {
                        thumbs: true,
                        keyboard: true,
                        fullscreen: true,
                        zoom: true,
                        laser: true,
                        autoplay: true,
                        transitions: true
                    },
                    defaults: {
                        transition: playerRoot.getAttribute('data-transition') || 'fade',
                        autoplayMs: parseInt(playerRoot.getAttribute('data-autoplay-ms') || '0', 10) || 0,
                        minZoom: 1,
                        maxZoom: 3.5
                    }
                });
                slidePlayerMounted = true;
            }

            document.querySelectorAll('[data-mx-pane-btn]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    setActivePane(btn.getAttribute('data-mx-pane-btn'));
                });
            });

            var video = document.getElementById('mx-animation-video');
            if (video) {
                video.addEventListener('error', function () {
                    setActivePane('slides');
                });
            }

            var defaultPane = root ? (root.getAttribute('data-default-pane') || 'slides') : 'slides';
            if (hasAnimation && defaultPane === 'animation') {
                // Video pane shown by default; mount slide player lazily on switch.
            } else {
                mountSlidePlayerOnce();
            }
        });
    </script>
@endpush
