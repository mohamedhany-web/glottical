@extends('layouts.app')

@section('title', 'محاضراتي')
@section('page_title', 'محاضراتي')

@section('content')
@php $isRtl = app()->getLocale() === 'ar'; @endphp
<div class="space-y-5">
    <div>
        <h2 class="text-xl font-black text-[#1A2744]">{{ $isRtl ? 'محاضراتي' : 'My lectures' }}</h2>
        <p class="text-sm text-[#6B7A99] mt-1">{{ $isRtl ? 'خاصة ومجموعات — اضغط للدخول.' : 'Private & groups — tap to join.' }}</p>
    </div>

    <section class="overflow-hidden rounded-2xl border border-[#E8EEF8] bg-white">
        <div class="border-b border-[#E8EEF8] px-4 py-3 font-black text-sm text-[#8A6A00]">👨‍🏫 {{ $isRtl ? 'خاصة' : 'Private' }}</div>
        <ul class="divide-y divide-[#E8EEF8]">
            @forelse($private as $session)
                <li class="px-4 py-3 flex flex-wrap items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="font-bold text-[#1A2744] truncate">{{ $session->course?->title ?: 'حصة خاصة' }}</p>
                        <p class="text-xs text-[#6B7A99]">{{ $session->scheduled_at?->format('Y-m-d H:i') }} · {{ $session->instructor?->name }}</p>
                    </div>
                    <a href="{{ route('student.schedule.join', ['type' => 'private', 'id' => $session->id]) }}" class="text-sm font-bold text-[#0B3D91] hover:underline">دخول</a>
                </li>
            @empty
                <li class="px-4 py-8 text-center text-sm text-[#6B7A99]">لا حصص خاصة بعد.</li>
            @endforelse
        </ul>
    </section>

    <section class="overflow-hidden rounded-2xl border border-[#E8EEF8] bg-white">
        <div class="border-b border-[#E8EEF8] px-4 py-3 font-black text-sm text-[#0B3D91]">👥 {{ $isRtl ? 'مجموعات / فصول' : 'Groups / classes' }}</div>
        <ul class="divide-y divide-[#E8EEF8]">
            @forelse($classes as $session)
                <li class="px-4 py-3 flex flex-wrap items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="font-bold text-[#1A2744] truncate">{{ $session->displayTitle() }}</p>
                        <p class="text-xs text-[#6B7A99]">{{ $session->starts_at?->format('Y-m-d H:i') }} · {{ $session->cohort?->title }}</p>
                    </div>
                    <a href="{{ route('student.schedule.join', ['type' => 'class', 'id' => $session->id]) }}" class="text-sm font-bold text-[#0B3D91] hover:underline">دخول</a>
                </li>
            @empty
                <li class="px-4 py-8 text-center text-sm text-[#6B7A99]">لا محاضرات جماعية بعد.</li>
            @endforelse
        </ul>
    </section>
</div>
@endsection
