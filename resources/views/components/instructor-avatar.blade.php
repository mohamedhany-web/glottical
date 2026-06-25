@props([
    'profile',
    'name' => '',
    'size' => 'md',
    'rounded' => 'full',
])

@php
    $photoUrl = $profile->photo_url ?? null;
    $sizeMap = [
        'sm' => 'w-16 h-16',
        'md' => 'w-24 h-24',
        'lg' => 'w-36 h-36 sm:w-44 sm:h-44',
        'xl' => 'w-36 h-36 sm:w-44 sm:h-44 lg:w-52 lg:h-52',
        'cover' => 'w-full h-full',
    ];
    $iconMap = [
        'sm' => 'text-xl',
        'md' => 'text-3xl',
        'lg' => 'text-5xl',
        'xl' => 'text-5xl',
        'cover' => 'text-4xl',
    ];
    $sizeClass = $sizeMap[$size] ?? $sizeMap['md'];
    $iconClass = $iconMap[$size] ?? $iconMap['md'];
    $radiusClass = $rounded === '2xl' ? 'rounded-2xl' : 'rounded-full';
@endphp

<div {{ $attributes->merge(['class' => "{$sizeClass} {$radiusClass} overflow-hidden bg-[#1a2d4d] relative"]) }}>
    @if($photoUrl)
        <img src="{{ $photoUrl }}"
             alt=""
             class="w-full h-full object-cover"
             loading="lazy"
             decoding="async"
             onerror="this.style.display='none';this.nextElementSibling?.classList.remove('hidden');">
        <div class="hidden absolute inset-0 flex items-center justify-center bg-[#1a2d4d]">
            <i class="fas fa-user text-white/35 {{ $iconClass }}"></i>
        </div>
    @else
        <div class="absolute inset-0 flex items-center justify-center">
            <i class="fas fa-user text-white/35 {{ $iconClass }}"></i>
        </div>
    @endif
</div>
