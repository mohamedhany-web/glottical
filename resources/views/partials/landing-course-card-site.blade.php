@php
    $thumbUrl = $course->thumbnail_url;
    if (! $thumbUrl) {
        $fallbackPool = [
            'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1546410531-bb4caa6b139d?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=900&q=80',
            'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=900&q=80',
        ];
        $thumbUrl = $fallbackPool[abs(crc32((string) ($course->id ?? 0))) % count($fallbackPool)];
    }
    $instName = $course->instructor->name ?? '';
    $rating = $course->rating !== null ? number_format((float) $course->rating, 1) : '4.8';
    $reviews = max(12, abs(crc32((string) $course->id)) % 320);
    $price = $course->is_free ? 0.0 : (float) ($course->price ?? 0);
    $after = $course->is_free ? 0.0 : (float) ($course->price_after_discount ?? $price);
    $hasDiscount = ! $course->is_free && $after > 0 && $price > $after;
    $discountPct = $hasDiscount ? (int) round((1 - ($after / $price)) * 100) : 0;
    $currency = __('landing.currency');
    $showBadge = $badge ?? null;
    $url = route('public.course.show', $course->id);
@endphp
<article class="group card-lift flex h-full flex-col overflow-hidden rounded-2xl bg-surface shadow-soft">
  <div class="relative aspect-[4/5] overflow-hidden bg-canvas-muted">
    <a href="{{ $url }}" class="absolute inset-0">
      <img src="{{ $thumbUrl }}" alt="{{ $course->title }}" class="img-zoom h-full w-full object-cover" loading="lazy" decoding="async" width="400" height="500">
    </a>
    <div class="absolute inset-x-3 top-3 flex items-start justify-between gap-2">
      <div class="flex flex-wrap gap-1.5">
        @if($showBadge)
          <span class="inline-flex items-center rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent">{{ $showBadge }}</span>
        @elseif(!empty($course->is_featured))
          <span class="inline-flex items-center rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent">{{ __('landing.featured_badge') }}</span>
        @endif
        @if($course->is_free)
          <span class="inline-flex items-center rounded-lg bg-[#f4eadc] px-2.5 py-1 text-xs font-medium text-[#7a5c2e]">{{ __('landing.free') }}</span>
        @elseif($hasDiscount && $discountPct > 0)
          <span class="inline-flex items-center rounded-lg bg-[#f4eadc] px-2.5 py-1 text-xs font-medium text-[#7a5c2e]">{{ app()->getLocale()==='ar' ? "خصم {$discountPct}%" : "{$discountPct}% off" }}</span>
        @endif
      </div>
      <a href="{{ $url }}" class="inline-flex size-9 sm:size-10 items-center justify-center rounded-full bg-surface/90 shadow-soft backdrop-blur" aria-label="{{ $course->title }}">
        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
      </a>
    </div>
    <div class="course-card-actions absolute inset-x-2 sm:inset-x-3 bottom-2 sm:bottom-3 translate-y-3 opacity-0 transition duration-300 group-hover:translate-y-0 group-hover:opacity-100 max-md:translate-y-0 max-md:opacity-100">
      <div class="flex gap-1.5 sm:gap-2">
        <a href="{{ $url }}" class="btn-press inline-flex h-9 sm:h-10 flex-1 items-center justify-center gap-1 rounded-xl bg-accent text-xs sm:text-sm font-medium text-white">{{ __('landing.academy.stream_play') }}</a>
        <a href="{{ $url }}" class="btn-press inline-flex h-9 sm:h-10 flex-1 items-center justify-center rounded-xl border border-line bg-surface text-xs sm:text-sm font-medium">{{ __('landing.academy.modal_enroll') }}</a>
      </div>
    </div>
  </div>
  <div class="flex flex-1 flex-col gap-1.5 sm:gap-2 p-3 sm:p-4">
    @if($instName !== '')
      <p class="text-[11px] sm:text-xs text-muted truncate">{{ $instName }}</p>
    @endif
    <a href="{{ $url }}" class="line-clamp-2 text-xs sm:text-sm font-medium leading-5 sm:leading-6 text-ink transition hover:text-accent">{{ $course->title }}</a>
    <p class="flex items-center gap-1.5 text-xs sm:text-sm text-muted">
      <svg class="size-3.5 fill-metal text-metal shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
      <span class="font-medium text-ink">{{ $rating }}</span>
      <span>({{ $reviews }})</span>
    </p>
    <p class="text-sm sm:text-base font-semibold text-ink mt-auto">
      @if($course->is_free)
        {{ __('landing.free') }}
      @else
        {{ number_format($after, 0) }} {{ $currency }}
        @if($hasDiscount)
          <span class="text-xs sm:text-sm font-normal text-muted line-through">{{ number_format($price, 0) }} {{ $currency }}</span>
        @endif
      @endif
    </p>
  </div>
</article>
