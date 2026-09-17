{{-- بطاقة رأي — شريط رئيسية (عرض ثابت) أو شبكة صفحة الآراء ($fluid) — ألوان Glottical --}}
@php
    /** @var \App\Models\SiteTestimonial $t */
    $fluid = $fluid ?? false;
    $featured = $t->is_featured;
    $widthClass = $fluid
        ? 'w-full'
        : ($featured
            ? 'min-w-[min(92vw,380px)] max-w-[min(92vw,380px)] sm:min-w-[360px] sm:max-w-[360px]'
            : 'min-w-[min(88vw,300px)] max-w-[min(88vw,300px)] sm:min-w-[280px] sm:max-w-[280px]');
    $initial = mb_substr($t->author_name ?: 'ر', 0, 1, 'UTF-8');
@endphp
<article class="{{ $widthClass }} {{ $fluid ? '' : 'flex-shrink-0' }} overflow-hidden rounded-2xl flex flex-col border shadow-[0_14px_40px_-22px_rgba(75,58,120,.35)] {{ $featured ? 'border-[#4B3A78] bg-[#4B3A78] text-white' : 'border-[#E8DFC8] bg-white' }}">
    @if($t->isImageType() && $t->publicImageUrl())
        <div class="flex aspect-[4/3] min-h-[10.5rem] max-h-[15rem] w-full items-center justify-center overflow-hidden sm:max-h-[17rem] {{ $featured ? 'bg-white/10' : 'bg-[#FFFBE6]' }}">
            <img src="{{ $t->publicImageUrl() }}" alt="" class="h-auto max-h-full w-auto max-w-full object-contain object-center" loading="lazy" decoding="async">
        </div>
    @endif
    <div class="flex flex-1 flex-col p-5">
        @if($t->body)
            <p class="flex-1 text-sm leading-8 {{ $featured ? 'text-white/95' : 'text-[#5B6577]' }}">
                @if($t->isImageType())
                    {{ Str::limit(strip_tags($t->body), 160) }}
                @else
                    «{{ Str::limit(strip_tags($t->body), 260) }}»
                @endif
            </p>
        @endif
        @if($t->author_name || $t->role_label)
            <div class="mt-4 flex items-center gap-3 border-t pt-4 {{ $featured ? 'border-white/20' : 'border-[#F3E9FF]' }}">
                <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-xl text-sm font-black {{ $featured ? 'bg-[#FFB7A5]/20 text-[#FFB7A5]' : 'bg-[#F3E9FF] text-[#4B3A78]' }}">{{ $initial }}</span>
                <div class="min-w-0">
                    @if($t->author_name)
                        <p class="truncate text-sm font-bold {{ $featured ? 'text-[#FFB7A5]' : 'text-[#2E234A]' }}">{{ $t->author_name }}</p>
                    @endif
                    @if($t->role_label)
                        <p class="mt-0.5 truncate text-xs {{ $featured ? 'text-white/75' : 'text-[#5B6577]' }}">{{ $t->role_label }}</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</article>
