@php
    $thumbPath = $course->thumbnail ? str_replace('\\', '/', $course->thumbnail) : null;
    $thumbUrl = $thumbPath ? asset('storage/'.$thumbPath) : null;
    $instName = $course->instructor->name ?? __('public.instructor_fallback');
    $rating = $course->rating !== null ? number_format((float) $course->rating, 1) : null;
    $dur = (int) ($course->duration_hours ?? 0);
    $durLabel = $dur > 0 ? $dur.' '.__($a.'.course_duration') : ($course->lessons_count ?? 0).' '.__('landing.lesson_single');
@endphp
<div class="netflix-item">
    <article class="card-course h-full rounded-2xl border border-slate-100 bg-white overflow-hidden shadow-sm hover:shadow-xl transition flex flex-col">
        <a href="{{ route('public.course.show', $course->id) }}" class="block flex flex-col flex-1">
            <div class="aspect-[16/10] bg-acad-gray overflow-hidden shrink-0">
                @if($thumbUrl)
                    <img src="{{ $thumbUrl }}" alt="" class="w-full h-full object-cover" loading="lazy" decoding="async">
                @else
                    <div class="w-full h-full flex items-center justify-center text-acad-blue/30"><i class="fas fa-image text-3xl"></i></div>
                @endif
            </div>
            <div class="p-4 flex-1 flex flex-col">
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-acad-blueSoft text-acad-blue w-fit">{{ $durLabel }}</span>
                <h4 class="mt-2 font-black text-acad-ink leading-snug line-clamp-2 text-sm flex-1">{{ \Illuminate\Support\Str::limit($course->title, 56) }}</h4>
                <p class="mt-1 text-xs text-slate-500 truncate">{{ $instName }}</p>
                <div class="mt-2 flex items-center justify-between text-xs">
                    <span class="text-amber-500 font-bold">@if($rating)<i class="fas fa-star"></i> {{ $rating }}@else — @endif</span>
                    <span class="text-acad-blue font-extrabold">@if($course->is_free){{ __('landing.free') }}@else{{ number_format((float) ($course->price_after_discount ?? $course->price ?? 0), 0) }}@endif</span>
                </div>
            </div>
        </a>
    </article>
</div>
