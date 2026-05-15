@extends('layouts.public')

@php
    $brand = config('app.name');
@endphp

@section('title', __('public.faq_page_title') . ' - ' . __('public.site_suffix'))
@section('meta_description', __('public.faq_meta_description', ['brand' => $brand]))
@section('meta_keywords', __('public.faq_meta_keywords', ['brand' => $brand]))
@section('canonical_url', url('/faq'))

@push('styles')
<style>
    .faq-acc-item {
        transition: box-shadow 0.25s ease, border-color 0.25s ease, transform 0.2s ease;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(15, 31, 58, 0.55);
    }
    .faq-acc-item:hover {
        border-color: rgba(245, 184, 0, 0.35);
        box-shadow: 0 14px 36px -18px rgba(0, 0, 0, 0.45);
    }
    .faq-acc-item.is-open {
        border-color: rgba(0, 212, 255, 0.35);
        box-shadow: 0 16px 40px -20px rgba(0, 163, 196, 0.2);
    }
    .faq-chevron {
        transition: transform 0.25s ease;
    }
    .faq-acc-item.is-open .faq-chevron {
        transform: rotate(180deg);
    }
    .filter-btn-faq.is-active {
        background: #f5b800;
        color: #0b3d91;
        border-color: rgba(245, 184, 0, 0.9);
        box-shadow: 0 8px 22px -10px rgba(245, 184, 0, 0.45);
    }
    .filter-btn-faq:not(.is-active) {
        background: rgba(255, 255, 255, 0.06);
        color: rgba(255, 255, 255, 0.88);
        border: 1px solid rgba(255, 255, 255, 0.12);
    }
    .filter-btn-faq:not(.is-active):hover {
        border-color: rgba(0, 212, 255, 0.35);
        background: rgba(255, 255, 255, 0.1);
    }
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<section class="-mt-14 sm:-mt-[60px] pt-24 sm:pt-28 lg:pt-32 pb-10 sm:pb-12 overflow-hidden relative">
    <div class="absolute inset-0 bg-acad-navy"></div>
    <div class="absolute inset-0 opacity-[0.2] bg-cover bg-center" style="background-image:url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=2400&q=82')"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-acad-navy via-acad-navy/88 to-acad-navy/35"></div>
    <div class="absolute inset-0 pattern-dots opacity-[0.12] pointer-events-none"></div>
    <div class="container-acad relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-14 items-center">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-extrabold mb-5 glass-panel border border-white/10 text-white">
                    <i class="fas fa-circle-question text-acad-yellow"></i> {{ __('public.faq_hero_highlight') }}
                </span>
                <h1 class="text-[1.75rem] sm:text-[2.35rem] lg:text-[2.85rem] leading-[1.15] font-black text-white mb-4 font-display">
                    {{ __('public.faq_page_title') }}
                </h1>
                <p class="text-white/70 text-base sm:text-lg leading-8 mb-6 max-w-xl">
                    {{ __('public.faq_hero_sub') }}
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('public.contact') }}" class="btn-stream-primary px-6 py-3">
                        <i class="fas fa-envelope"></i> {{ __('public.contact_page_title') }}
                    </a>
                    <a href="{{ route('public.help') }}" class="btn-stream-secondary px-6 py-3">
                        <i class="fas fa-life-ring"></i> {{ __('public.help_page_title') }}
                    </a>
                </div>
            </div>
            <div class="relative min-h-[220px] lg:min-h-[280px]">
                <div class="absolute inset-0 rounded-[32px] glass-panel border border-white/15 shadow-2xl"></div>
                <div class="relative p-6 sm:p-8 text-white h-full flex flex-col justify-center gap-4">
                    <div class="flex items-start gap-3 rounded-2xl bg-white/8 backdrop-blur-sm px-4 py-3 border border-white/12">
                        <span class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 text-lg bg-acad-yellow text-acad-blue">
                            <i class="fas fa-magnifying-glass"></i>
                        </span>
                        <div>
                            <p class="font-bold text-sm text-white">{{ __('public.faq_page_title') }}</p>
                            <p class="text-sm text-white/75 leading-relaxed">{{ __('public.faq_sidebar_hint') }}</p>
                        </div>
                    </div>
                    <div class="flex gap-3 flex-wrap">
                        <a href="{{ route('public.courses') }}" class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-bold bg-white/10 border border-white/15 hover:bg-white/18 transition-colors">
                            <i class="fas fa-chalkboard-user"></i> {{ __('public.courses_page_title') }}
                        </a>
                        <a href="{{ route('public.certificates') }}" class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-bold bg-white/10 border border-white/15 hover:bg-white/18 transition-colors">
                            <i class="fas fa-certificate"></i> {{ __('public.certificates_page_title') }}
                        </a>
                        <a href="{{ route('public.pricing') }}" class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-bold bg-white/10 border border-white/15 hover:bg-white/18 transition-colors">
                            <i class="fas fa-credit-card"></i> {{ __('public.pricing_page_title') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@php
    $defaultGrouped = collect($defaultFaqs ?? [])->groupBy('category');
    $hasDbFaqs = isset($faqs) && $faqs->isNotEmpty();
@endphp

<section id="faq-main" class="pt-4 pb-14 sm:pb-16 relative">
    <div class="absolute inset-0 bg-gradient-to-b from-acad-navy via-acad-navyMid/20 to-acad-navy pointer-events-none"></div>
    <div class="container-acad relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12">
            {{-- عمود جانبي: فلاتر + روابط --}}
            <aside class="lg:col-span-4 xl:col-span-3 lg:sticky lg:top-28 lg:self-start space-y-6">
                @if(isset($categories) && $categories->isNotEmpty())
                <div class="rounded-[24px] border border-white/10 glass-panel p-5 sm:p-6">
                    <p class="text-xs font-bold uppercase tracking-wide text-acad-cyan mb-3">{{ __('public.faq_sidebar_categories') }}</p>
                    <div class="flex flex-col gap-2">
                        <button type="button" class="filter-btn filter-btn-faq is-active w-full text-start rounded-xl px-4 py-2.5 text-sm font-bold transition-all" data-category="all">
                            {{ __('public.faq_filter_all') }}
                        </button>
                        @foreach($categories as $cat)
                        <button type="button" class="filter-btn filter-btn-faq w-full text-start rounded-xl px-4 py-2.5 text-sm font-semibold transition-all hover:border-acad-cyan/40" data-category="{{ $cat }}">
                            {{ $cat }}
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="rounded-[24px] overflow-hidden border border-white/12 glass-panel">
                    <div class="p-5 sm:p-6 text-white">
                        <p class="text-xs font-bold uppercase tracking-wide text-white/70 mb-4">{{ __('public.faq_quick_links') }}</p>
                        <ul class="space-y-2">
                            <li>
                                <a href="{{ route('public.contact') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold bg-white/10 hover:bg-white/20 transition-colors">
                                    <i class="fas fa-paper-plane text-acad-yellow"></i> {{ __('public.contact_page_title') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('public.help') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold bg-white/10 hover:bg-white/20 transition-colors">
                                    <i class="fas fa-book-open text-acad-yellow"></i> {{ __('public.help_page_title') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('public.privacy') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold bg-white/10 hover:bg-white/20 transition-colors">
                                    <i class="fas fa-shield-halved text-acad-yellow"></i> {{ __('public.privacy_page_title') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </aside>

            {{-- الأسئلة --}}
            <div class="lg:col-span-8 xl:col-span-9 space-y-10 min-w-0">
                @if(isset($categories) && $categories->isNotEmpty())
                <div class="lg:hidden flex flex-wrap gap-2">
                    <button type="button" class="filter-btn filter-btn-faq is-active px-4 py-2 rounded-xl text-sm font-bold" data-category="all">{{ __('public.faq_filter_all') }}</button>
                    @foreach($categories as $cat)
                    <button type="button" class="filter-btn filter-btn-faq px-4 py-2 rounded-xl text-sm font-semibold" data-category="{{ $cat }}">{{ $cat }}</button>
                    @endforeach
                </div>
                @endif

                @if($hasDbFaqs)
                @foreach($faqs as $categoryName => $categoryFaqs)
                <div class="faq-block" data-category="{{ $categoryName ?? 'general' }}">
                    @if($categoryName)
                    <div class="flex items-center gap-3 mb-5">
                        <span class="w-1.5 h-8 rounded-full shrink-0 bg-gradient-to-b from-acad-yellow to-acad-cyan"></span>
                        <h2 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2 font-display">
                            <i class="fas fa-layer-group text-acad-cyan"></i>
                            {{ $categoryName }}
                        </h2>
                    </div>
                    @endif
                    <div class="space-y-3">
                        @foreach($categoryFaqs as $faq)
                        <div class="faq-acc-item rounded-2xl overflow-hidden" x-data="{ open: false }" :class="{ 'is-open': open }">
                            <button type="button" @click="open = !open" class="w-full px-5 sm:px-6 py-4 text-start flex items-center justify-between gap-4 hover:bg-white/6 transition-colors">
                                <span class="text-base font-bold text-white flex-1 min-w-0">{{ $faq->question }}</span>
                                <i class="fas fa-chevron-down faq-chevron text-acad-cyan flex-shrink-0"></i>
                            </button>
                            <div x-show="open" x-cloak class="border-t border-white/10">
                                <div class="px-5 sm:px-6 py-4 text-white/75 leading-relaxed text-sm sm:text-base">
                                    {!! nl2br(e($faq->answer)) !!}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
                @endif

                @if($defaultGrouped->isNotEmpty())
                <div id="default" class="faq-block default-faqs" data-category="default">
                    <div class="flex items-center gap-3 mb-5">
                        <span class="w-1.5 h-8 rounded-full shrink-0 bg-gradient-to-b from-acad-cyan to-acad-yellow"></span>
                        <h2 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2 font-display">
                            <i class="fas fa-graduation-cap text-acad-yellow"></i>
                            {{ __('public.faq_section_platform', ['brand' => $brand]) }}
                        </h2>
                    </div>
                    <div class="space-y-8">
                        @foreach($defaultGrouped as $catName => $items)
                        <div>
                            @if($catName)
                            <h3 class="text-base font-bold text-white/80 mb-3 flex items-center gap-2">
                                <i class="fas fa-tag text-acad-cyan text-sm"></i>
                                {{ $catName }}
                            </h3>
                            @endif
                            <div class="space-y-3">
                                @foreach($items as $item)
                                <div class="faq-acc-item rounded-2xl overflow-hidden" x-data="{ open: false }" :class="{ 'is-open': open }">
                                    <button type="button" @click="open = !open" class="w-full px-5 sm:px-6 py-4 text-start flex items-center justify-between gap-4 hover:bg-white/6 transition-colors">
                                        <span class="text-base font-bold text-white flex-1 min-w-0">{{ $item['question'] }}</span>
                                        <i class="fas fa-chevron-down faq-chevron text-acad-cyan flex-shrink-0"></i>
                                    </button>
                                    <div x-show="open" x-cloak class="border-t border-white/10">
                                        <div class="px-5 sm:px-6 py-4 text-white/75 leading-relaxed text-sm sm:text-base">
                                            {!! nl2br(e($item['answer'] ?? '')) !!}
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(!$hasDbFaqs && $defaultGrouped->isEmpty())
                <div class="text-center py-16 rounded-[28px] border border-dashed border-white/20 glass-panel">
                    <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-4 text-acad-blue text-3xl bg-acad-yellow">
                        <i class="fas fa-question"></i>
                    </div>
                    <p class="text-white/70 text-lg font-medium mb-5">{{ __('public.faq_empty_title') }}</p>
                    <a href="{{ route('public.contact') }}" class="btn-stream-primary px-6 py-3">
                        <i class="fas fa-envelope"></i>
                        {{ __('public.faq_empty_cta') }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<section class="pt-6 sm:pt-8 pb-14 sm:pb-16 relative">
    <div class="absolute inset-0 bg-acad-navy pointer-events-none"></div>
    <div class="container-acad relative z-10">
        <div class="rounded-[28px] border border-white/10 glass-panel px-6 sm:px-10 py-10 sm:py-12 text-center overflow-hidden relative">
            <div class="absolute inset-0 pointer-events-none opacity-15 bg-cover bg-center" style="background-image:url('https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=2200&q=82')"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-acad-navy/92 to-acad-blue/35 pointer-events-none"></div>
            <span class="relative z-10 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-extrabold mb-5 glass-panel border border-white/10">
                <i class="fas fa-headset text-acad-yellow"></i> {{ __('public.support') }}
            </span>
            <h3 class="relative z-10 text-2xl sm:text-3xl font-black mb-3 text-white font-display">{{ __('public.faq_cta_title') }}</h3>
            <p class="relative z-10 text-white/70 text-base sm:text-lg max-w-2xl mx-auto leading-8 mb-8">
                {{ __('public.faq_cta_desc') }}
            </p>
            <div class="relative z-10 flex flex-col sm:flex-row justify-center gap-3 sm:gap-4">
                <a href="{{ route('public.contact') }}" class="btn-stream-primary px-8 py-3.5">
                    <i class="fas fa-paper-plane"></i> {{ __('public.faq_cta_btn') }}
                </a>
                <a href="{{ route('home') }}" class="btn-stream-secondary px-8 py-3.5">
                    <i class="fas fa-home"></i> {{ __('public.home') }}
                </a>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var buttons = document.querySelectorAll('.filter-btn');
    var blocks = document.querySelectorAll('.faq-block');
    if (buttons.length === 0 || blocks.length === 0) return;
    buttons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var cat = this.getAttribute('data-category');
            buttons.forEach(function(b) {
                if (b.getAttribute('data-category') === cat) {
                    b.classList.add('is-active');
                } else {
                    b.classList.remove('is-active');
                }
            });
            blocks.forEach(function(block) {
                var blockCat = block.getAttribute('data-category');
                block.style.display = (cat === 'all' || blockCat === cat) ? '' : 'none';
            });
        });
    });
});
</script>
@endpush
@endsection
