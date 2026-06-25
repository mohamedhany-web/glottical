@php
    $thumbPath = $course->thumbnail ? str_replace('\\', '/', $course->thumbnail) : null;
    $thumbUrl = $thumbPath ? storage_asset($thumbPath) : null;
    $instName = $course->instructor->name ?? __('public.instructor_fallback');
    $rating = $course->rating !== null ? number_format((float) $course->rating, 1) : null;
    $dur = (int) ($course->duration_hours ?? 0);
    $durLabel = $dur > 0 ? $dur.' '.__($a.'.course_duration') : ($course->lessons_count ?? 0).' '.__('landing.lesson_single');
@endphp
<div class="netflix-item">
    <article class="card-course h-full rounded-2xl border border-slate-100 bg-white overflow-hidden shadow-sm hover:shadow-xl transition flex flex-col">
        <a href="{{ route('public.course.show', $course->id) }}" class="block flex flex-col flex-1">
            <div class="aspect-[16/10] bg-acad-gray overflow-hidden shrink-0 relative">
                @if($thumbUrl)
                    <div class="media-thumb-skeleton absolute inset-0" aria-hidden="true"></div>
                    <img src="{{ $thumbUrl }}" alt="" class="w-full h-full object-cover media-thumb-img" width="320" height="200" loading="lazy" decoding="async" sizes="(max-width:640px) 85vw, 320px" onload="this.classList.add('is-loaded');this.previousElementSibling?.remove();" onerror="this.style.display='none';this.previousElementSibling?.remove();">
                @else
                    <div class="w-full h-full flex items-center justify-center text-acad-blue/30"><i class="fas fa-image text-3xl"></i></div>
                @endif
            </div>
            <div class="p-4 flex-1 flex flex-col">
                <div class="flex flex-wrap items-center gap-1.5">
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-acad-blueSoft text-acad-blue">{{ $durLabel }}</span>
                    @if($course->isMonthlyBilling())
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700">{{ __('public.course_badge_monthly') }}</span>
                    @endif
                    @if($course->isOneToOne())
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-violet-100 text-violet-700">{{ __('public.course_badge_one_to_one') }}</span>
                    @endif
                </div>
                <h4 class="mt-2 font-black text-acad-ink leading-snug line-clamp-2 text-sm flex-1">{{ \Illuminate\Support\Str::limit($course->title, 56) }}</h4>
                <p class="mt-1 text-xs text-slate-500 truncate">{{ $instName }}</p>
                <div class="mt-2 flex items-center justify-between text-xs">
                    <span class="text-amber-500 font-bold">@if($rating)<i class="fas fa-star"></i> {{ $rating }}@else — @endif</span>
                    <x-advanced-course-card-price :course="$course" size="sm" />
                </div>
            </div>
        </a>
    </article>
</div>
