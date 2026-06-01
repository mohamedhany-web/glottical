@extends('layouts.public')

@php
    $brand = config('app.name');
@endphp

@section('title', __('public.testimonials_page_title') . ' - ' . __('public.site_suffix'))
@section('meta_description', __('public.home_testimonials_sub'))
@section('meta_keywords', 'آراء, شهادات, ' . $brand . ', متعلمين, كول سنتر, ألمانيا')
@section('canonical_url', url('/testimonials'))

@section('content')
<section class="pt-24 sm:pt-28 lg:pt-32 pb-10 sm:pb-12 overflow-hidden relative" style="background:radial-gradient(circle at 12% 80%,rgba(255,229,247,.65),transparent 28%),radial-gradient(circle at 88% 20%,rgba(40,53,147,.10),transparent 30%),linear-gradient(180deg,#f4f6ff 0%,#fbfbff 55%,#ffffff 100%)">
    <div class="w-full max-w-[1200px] mx-auto px-6 sm:px-8 relative z-10 text-center">
        <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-bold mb-5" style="background:#FFE5F7;color:#283593;border:1px solid #f5c7e8">
            <i class="fas fa-quote-right"></i> {{ __('public.testimonials_page_title') }}
        </span>
        <h1 class="text-[1.85rem] sm:text-[2.5rem] lg:text-[3rem] leading-[1.15] font-black text-[#1F2A7A] dark:text-white mb-4" style="font-family:Tajawal,Cairo,sans-serif">{{ __('public.home_testimonials_heading') }}</h1>
        <p class="text-slate-600 dark:text-slate-400 text-base sm:text-lg max-w-2xl mx-auto leading-8">{{ __('public.home_testimonials_sub') }}</p>
    </div>
</section>

<section class="py-12 sm:py-16 bg-white dark:bg-slate-800">
    <div class="w-full max-w-[1200px] mx-auto px-6 sm:px-8">
        @if($testimonials->isEmpty())
            <div class="text-center py-16 rounded-[24px] border border-dashed border-slate-200 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/50">
                <p class="text-slate-600 dark:text-slate-400">{{ __('public.home_testimonials_empty') }}</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($testimonials as $t)
                    @include('partials.home-testimonial-card', ['t' => $t, 'fluid' => true])
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
