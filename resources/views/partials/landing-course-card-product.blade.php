@php
    $thumbPath = $course->thumbnail ? str_replace('\\', '/', $course->thumbnail) : null;
    $thumbUrl = $thumbPath ? storage_asset($thumbPath) : null;
    $instName = $course->instructor->name ?? __('public.instructor_fallback');
    $rating = $course->rating !== null ? number_format((float) $course->rating, 1) : null;
    $ratingNum = $course->rating !== null ? (float) $course->rating : 0;
    $dur = (int) ($course->duration_hours ?? 0);
    $durLabel = $dur > 0 ? $dur.' '.__($a.'.course_duration') : ($course->lessons_count ?? 0).' '.__('landing.lesson_single');
    $catSlug = optional($course->courseCategory)->name ? \Illuminate\Support\Str::slug($course->courseCategory->name) : '';
    $priceNum = $course->is_free ? 0.0 : (float) ($course->price_after_discount ?? $course->price ?? 0);
    $showDiscount = ! $course->is_free && $course->price_after_discount && $course->price && (float) $course->price_after_discount < (float) $course->price;
@endphp
<article class="card-course reveal rounded-2xl border border-slate-100 bg-white overflow-hidden relative group"
    data-course-card
    data-id="{{ $course->id }}"
    data-level="{{ $course->level ?? 'beginner' }}"
    data-price="{{ $priceNum }}"
    data-free="{{ $course->is_free ? '1' : '0' }}"
    data-duration="{{ $dur }}"
    data-rating="{{ $ratingNum }}"
    data-category="{{ $catSlug }}"
    data-lang="{{ strtolower((string) ($course->language ?? '')) }}">
    <button type="button" class="wishlist-btn absolute top-3 {{ $isRtl ? 'left-3' : 'right-3' }} z-10 w-10 h-10 rounded-full bg-white/95 shadow-md border border-slate-100 flex items-center justify-center text-slate-400 hover:text-rose-500 transition"
        data-wishlist-toggle="{{ $course->id }}"
        aria-label="{{ __($a.'.wishlist_add') }}">
        <i class="fas fa-heart"></i>
    </button>
    <div class="relative aspect-[16/10] bg-acad-gray overflow-hidden">
        @if($thumbUrl)
            <img src="{{ $thumbUrl }}" alt="{{ $course->title }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-105" loading="lazy" decoding="async">
        @else
            <div class="w-full h-full flex items-center justify-center text-acad-blue/30"><i class="fas fa-image text-4xl"></i></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-acad-blue/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-4 gap-2">
            <button type="button" class="quick-view-btn px-4 py-2 rounded-xl bg-white text-acad-blue font-extrabold text-sm shadow-lg hover:scale-105 transition" data-quick-view="{{ $course->id }}">{{ __($a.'.quick_view') }}</button>
        </div>
    </div>
    <div class="p-5">
        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-block text-[11px] font-bold px-2 py-0.5 rounded-md bg-acad-blueSoft text-acad-blue">{{ $durLabel }}</span>
            @if($course->is_free)
                <span class="text-[11px] font-black px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700">{{ __('landing.free') }}</span>
            @elseif($showDiscount)
                @php $pctOff = (float) $course->price > 0 ? (int) round((1 - (float) $course->price_after_discount / (float) $course->price) * 100) : 0; @endphp
                <span class="text-[11px] font-black px-2 py-0.5 rounded-md bg-rose-100 text-rose-700">-{{ max(0, min(99, $pctOff)) }}%</span>
            @endif
        </div>
        <h3 class="mt-3 font-black text-lg text-acad-ink leading-snug line-clamp-2">
            <a href="{{ route('public.course.show', $course->id) }}" class="hover:text-acad-cyan transition">{{ \Illuminate\Support\Str::limit($course->title, 70) }}</a>
        </h3>
        <p class="mt-1 text-sm text-slate-500">{{ $instName }}</p>
        <div class="mt-3 flex items-center justify-between gap-2">
            <span class="text-amber-500 font-bold text-sm">
                @if($rating !== null)<i class="fas fa-star"></i> {{ $rating }}@else<span class="text-slate-400 text-xs">{{ __('public.no_rating_yet') }}</span>@endif
            </span>
            <span class="text-acad-blue font-black text-sm tabular-nums">
                @if($course->is_free)
                    {{ __('landing.free') }}
                @else
                    {{ $fmt((int) $priceNum) }} {{ __('landing.currency') }}
                @endif
            </span>
        </div>
        <a href="{{ route('public.course.show', $course->id) }}" class="mt-4 w-full inline-flex justify-center items-center gap-2 py-2.5 rounded-xl bg-acad-yellow text-acad-blue font-extrabold text-sm hover:brightness-105 transition shadow-sm">{{ __($a.'.course_enroll') }}</a>
    </div>
</article>
