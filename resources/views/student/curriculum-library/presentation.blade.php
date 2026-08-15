@extends('layouts.app')

@php
    $pt = $presentationTitle ?? 'عرض تفاعلي';
    $mode = $mode ?? 'office';
    $hasAnimationVideo = !empty($hasAnimationVideo) && !empty($animationVideoUrl);
    $isNative = $mode === 'native' && !empty($manifestUrl);
    $defaultPane = $hasAnimationVideo ? 'animation' : ($isNative ? 'slides' : 'office');
@endphp

@section('title', $pt . ' - ' . $item->title)
@section('header', $item->title)
@section('enable-content-protection', ($isNative || $hasAnimationVideo) ? 'true' : '')

@push('styles')
    @if($isNative)
        <link rel="stylesheet" href="{{ vasset('css/curriculum-slide-player.css') }}">
    @endif
@endpush

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-4"
     data-mx-presentation-root
     data-default-pane="{{ $defaultPane }}"
     @if($hasAnimationVideo) data-has-animation-video="1" @endif>
    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('curriculum-library.show', $item) }}" class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-700 text-sm font-semibold">
            <i class="fas fa-arrow-right"></i> العودة لصفحة المنهج
        </a>
    </div>
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-lg font-black text-slate-800">{{ $pt }}</h1>
                <p class="text-xs text-slate-500 mt-1">العرض داخل المنصة فقط؛ التحميل غير متاح لهذا النوع.</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-100">
                <i class="fas fa-lock ml-1.5 text-[10px]"></i> بدون تحميل
            </span>
        </div>

        @if($hasAnimationVideo)
            <div class="px-4 pt-4 flex flex-wrap items-center gap-2" role="tablist" aria-label="وضع العرض">
                <button type="button"
                        data-mx-pane-btn="animation"
                        class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-xs font-black border transition
                               bg-indigo-600 text-white border-indigo-600"
                        aria-selected="true">
                    <i class="fas fa-film"></i> العرض بالحركات
                </button>
                <button type="button"
                        data-mx-pane-btn="slides"
                        class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-xs font-black border transition
                               bg-white text-slate-700 border-slate-200 hover:bg-slate-50"
                        aria-selected="false">
                    <i class="fas fa-images"></i> تصفح الشرائح
                </button>
            </div>
            <p data-mx-animation-hint class="px-4 pt-2 text-[11px] text-slate-500 leading-relaxed">
                فيديو الحركات يحافظ على حركات PowerPoint والصوت والتوقيت كما صُدّر من الملف الأصلي.
                للتنقل بين الشرائح يدوياً استخدم وضع «تصفح الشرائح».
            </p>
        @endif

        <div class="p-4 bg-slate-50">
            @if($hasAnimationVideo)
                <div id="mx-animation-pane"
                     data-mx-pane="animation"
                     class="{{ $defaultPane === 'animation' ? '' : 'hidden' }}"
                     @if($defaultPane !== 'animation') hidden aria-hidden="true" @endif>
                    <div class="relative w-full max-w-5xl mx-auto rounded-xl overflow-hidden bg-black aspect-video shadow-sm border border-slate-200">
                        <video id="mx-animation-video"
                               class="w-full h-full"
                               controls
                               playsinline
                               controlslist="nodownload"
                               disablepictureinpicture
                               preload="metadata"
                               src="{{ $animationVideoUrl }}">
                            متصفحك لا يدعم تشغيل الفيديو.
                        </video>
                    </div>
                    <p class="mt-2 text-[11px] text-slate-500 text-center">
                        إذا تعذّر تشغيل الفيديو سيتم التحويل تلقائياً إلى تصفح الشرائح.
                    </p>
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
        </div>
    </div>
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
                    btn.classList.toggle('bg-indigo-600', on);
                    btn.classList.toggle('text-white', on);
                    btn.classList.toggle('border-indigo-600', on);
                    btn.classList.toggle('bg-white', !on);
                    btn.classList.toggle('text-slate-700', !on);
                    btn.classList.toggle('border-slate-200', !on);
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
