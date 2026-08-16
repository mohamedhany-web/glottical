@extends('layouts.app')

@section('title', $libraryVideo->title)
@section('header', $libraryVideo->title)

@section('content')
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-slate-500">
                <a href="{{ route('instructor.libraries.videos.index') }}" class="hover:text-[#0B3D91]">مكتبة الفيديو</a>
                · {{ $isOwn ? 'فيديوهاتك' : 'أكاديمية (عرض فقط)' }}
            </p>
            <h2 class="mt-1 text-2xl font-semibold text-slate-900">{{ $libraryVideo->title }}</h2>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('instructor.libraries.videos.index') }}" class="inline-flex h-9 items-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700">رجوع</a>
            @if($isOwn)
                <a href="{{ route('instructor.libraries.videos.edit', $libraryVideo) }}" class="inline-flex h-9 items-center rounded-xl bg-[#0B3D91] px-4 text-sm font-semibold text-white">تعديل</a>
            @endif
        </div>
    </section>

    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="aspect-video bg-black">
            @if($embedUrl)
                <iframe
                    src="{{ $embedUrl }}"
                    title="{{ $libraryVideo->title }}"
                    class="h-full w-full"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen; web-share"
                    allowfullscreen
                    referrerpolicy="strict-origin-when-cross-origin"
                ></iframe>
            @elseif($directUrl)
                <video class="h-full w-full" controls playsinline preload="metadata" @if($thumbnail) poster="{{ $thumbnail }}" @endif controlslist="nodownload">
                    <source src="{{ $directUrl }}" type="{{ $libraryVideo->mime_type ?: 'video/mp4' }}">
                </video>
            @else
                <div class="flex h-full items-center justify-center text-sm text-white/80">تعذر تشغيل الفيديو.</div>
            @endif
        </div>
        @if($libraryVideo->description)
            <p class="px-4 py-3 text-sm text-slate-600">{{ $libraryVideo->description }}</p>
        @endif
    </article>
</div>
@endsection
