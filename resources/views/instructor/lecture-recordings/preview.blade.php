@extends('layouts.app')

@section('title', 'معاينة تسجيل — '.$lecture->title)
@section('header', 'معاينة التسجيل')

@section('content')
<div class="space-y-4 max-w-4xl">
    <a href="{{ route('instructor.lecture-recordings.index') }}" class="text-sm text-[#0B3D91] font-semibold">← رجوع</a>
    <h2 class="text-xl font-semibold">{{ $lecture->title }}</h2>
    <div class="aspect-video overflow-hidden rounded-2xl bg-slate-900">
        @if($embedUrl)
            <iframe src="{{ $embedUrl }}" class="h-full w-full" allowfullscreen allow="accelerometer; autoplay; encrypted-media; picture-in-picture"></iframe>
        @elseif($directUrl || $fileUrl)
            <video class="h-full w-full" controls playsinline controlsList="nodownload" oncontextmenu="return false;">
                <source src="{{ $directUrl ?: $fileUrl }}">
            </video>
        @else
            <div class="grid h-full place-items-center text-white/60">لا يوجد تسجيل للمعاينة</div>
        @endif
    </div>
</div>
@endsection
