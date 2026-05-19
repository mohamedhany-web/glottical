@php
    $thumbUrl = $course->thumbnail_url;
    $instName = $course->instructor->name ?? '';
    $rating = $course->rating !== null ? number_format((float) $course->rating, 1) : null;
    $dur = (int) ($course->duration_hours ?? 0);
    $durLabel = $dur > 0 ? $dur.' '.__($a.'.course_duration') : ($course->lessons_count ?? 0).' '.__('landing.lesson_single');
    $lvlKey = match ($course->level ?? 'beginner') {
        'intermediate' => $a.'.level_intermediate',
        'advanced' => $a.'.level_advanced',
        default => $a.'.level_beginner',
    };
    $lvlLabel = __($lvlKey);
@endphp
<div class="stream-card-wrap netflix-item shrink-0">
    <article class="stream-card group relative rounded-xl overflow-hidden bg-[#0f1f3a] border border-white/10 shadow-lg outline-none transition-all duration-300 hover:scale-[1.06] hover:z-20 hover:shadow-[0_0_0_2px_rgba(245,184,0,0.65),0_24px_48px_-12px_rgba(0,163,196,0.35)] flex flex-col">
        <a href="{{ route('public.course.show', $course->id) }}" class="flex flex-col h-full min-h-0 text-start">
            {{-- منطقة الصورة فقط: زر التشغيل يُوسَّط داخلها ولا يتقاطع مع الشريط النصي --}}
            <div class="relative aspect-video w-full shrink-0 bg-[#152a4a] overflow-hidden">
                @if($thumbUrl)
                    <img src="{{ $thumbUrl }}"
                         alt="{{ $course->title }}"
                         width="960"
                         height="540"
                         class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105 group-hover:brightness-110"
                         sizes="(max-width: 640px) 85vw, 400px"
                         loading="lazy"
                         decoding="async"
                         onerror="this.style.display='none';this.nextElementSibling?.classList.remove('hidden');">
                    <div class="hidden absolute inset-0 flex items-center justify-center text-white/25 bg-[#152a4a]"><i class="fas fa-play-circle text-5xl"></i></div>
                @else
                    <div class="absolute inset-0 flex items-center justify-center text-white/25"><i class="fas fa-play-circle text-5xl"></i></div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-[#050b18]/80 via-transparent to-transparent pointer-events-none"></div>
                <div class="absolute inset-0 z-10 flex items-center justify-center opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-300 bg-[#050b18]/50 backdrop-blur-[2px] pointer-events-none group-hover:pointer-events-auto">
                    <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-acad-yellow text-acad-blue font-black text-sm shadow-lg scale-95 group-hover:scale-100 transition pointer-events-none"><i class="fas fa-play text-xs"></i>{{ __($a.'.stream_play') }}</span>
                </div>
            </div>
            {{-- شريط معلومات تحت الصورة (ثابت، لا تداخل مع طبقة الـ hover) --}}
            <div class="relative z-20 p-3 pt-3 border-t border-white/10 bg-[#0a1628]">
                <div class="flex flex-wrap items-center gap-1.5 mb-1.5">
                    <span class="text-[10px] font-black px-2 py-0.5 rounded-md bg-white/15 text-white backdrop-blur-sm border border-white/10">{{ $durLabel }}</span>
                    <span class="text-[10px] font-black px-2 py-0.5 rounded-md bg-[#00A3C4]/25 text-[#7ee8ff] border border-[#00A3C4]/30">{{ $lvlLabel }}</span>
                </div>
                <h3 class="font-black text-white text-sm sm:text-base leading-snug line-clamp-2">{{ \Illuminate\Support\Str::limit($course->title, 72) }}</h3>
                @if($instName !== '')
                    <p class="text-[11px] text-white/65 font-semibold mt-0.5 truncate">{{ $instName }}</p>
                @endif
                <p class="text-amber-400 text-xs font-bold mt-1">
                    @if($rating !== null)<i class="fas fa-star"></i> {{ $rating }}@else<span class="text-white/40">{{ __('public.no_rating_yet') }}</span>@endif
                </p>
            </div>
        </a>
    </article>
</div>
