@php
    $cfg = is_array($playerConfig ?? null) ? $playerConfig : [];
    $transition = $cfg['transition'] ?? 'fade';
    $autoplayMs = (int) ($cfg['autoplay_ms'] ?? 0);
    $aspectW = max(1, (int) ($slideWidth ?? 1410));
    $aspectH = max(1, (int) ($slideHeight ?? 900));
@endphp

<div id="mx-slide-player"
     class="mx-slide-player"
     dir="rtl"
     role="region"
     aria-label="عارض الشرائح"
     data-manifest-url="{{ $manifestUrl }}"
     data-transition="{{ $transition }}"
     data-autoplay-ms="{{ $autoplayMs }}"
     data-slide-count="{{ (int) ($slideCount ?? 0) }}"
     style="--mx-slide-aspect: {{ $aspectW }} / {{ $aspectH }};">

    <div class="mx-sp-layout">
        <aside class="mx-sp-thumbs" aria-label="صور مصغّرة للشرائح">
            <div class="mx-sp-thumbs-scroll" data-mx-thumbs role="list"></div>
        </aside>

        <div class="mx-sp-main">
            <div class="mx-sp-toolbar" role="toolbar" aria-label="أدوات العرض">
                <div class="mx-sp-toolbar-group">
                    <button type="button" class="mx-sp-btn" data-mx-prev aria-label="الشريحة السابقة">
                        <i class="fas fa-chevron-right" aria-hidden="true"></i>
                    </button>
                    <span class="mx-sp-status" data-mx-status aria-live="polite">—</span>
                    <button type="button" class="mx-sp-btn" data-mx-next aria-label="الشريحة التالية">
                        <i class="fas fa-chevron-left" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="mx-sp-toolbar-group">
                    <button type="button" class="mx-sp-btn" data-mx-zoom-out aria-label="تصغير">
                        <i class="fas fa-search-minus" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="mx-sp-btn mx-sp-btn-label" data-mx-zoom-label aria-label="إعادة التكبير" title="إعادة التكبير">100%</button>
                    <button type="button" class="mx-sp-btn" data-mx-zoom-in aria-label="تكبير">
                        <i class="fas fa-search-plus" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="mx-sp-btn" data-mx-laser aria-pressed="false" aria-label="مؤشر ليزر">
                        <i class="fas fa-circle" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="mx-sp-btn" data-mx-autoplay aria-pressed="false" aria-label="تشغيل تلقائي">
                        <i class="fas fa-play" aria-hidden="true"></i>
                    </button>
                    <label class="mx-sp-select-wrap">
                        <span class="sr-only">انتقال الشرائح</span>
                        <select class="mx-sp-select" data-mx-transition aria-label="نوع الانتقال">
                            <option value="fade" @selected($transition === 'fade')>تلاشي</option>
                            <option value="slide" @selected($transition === 'slide')>انزلاق</option>
                            <option value="none" @selected($transition === 'none')>بدون</option>
                        </select>
                    </label>
                    <button type="button" class="mx-sp-btn" data-mx-fullscreen aria-label="ملء الشاشة">
                        <i class="fas fa-expand" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <div class="mx-sp-stage-wrap" data-mx-stage-wrap>
                <div class="mx-sp-stage" data-mx-stage tabindex="0" aria-label="منصة الشريحة">
                    <div class="mx-sp-viewport" data-mx-viewport>
                        <div class="mx-sp-canvas" data-mx-canvas>
                            <img data-mx-slide-img alt="" class="mx-sp-slide-img" draggable="false">
                        </div>
                    </div>
                    <div class="mx-sp-laser-layer" data-mx-laser-layer aria-hidden="true"></div>
                    <div class="mx-sp-loading" data-mx-loading aria-hidden="true">
                        <span class="mx-sp-spinner" aria-hidden="true"></span>
                        <span>جارٍ التحميل…</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
