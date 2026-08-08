@extends('layouts.app')

@section('title', 'مكتبة الفيديوهات')
@section('page_title', 'مكتبة الفيديوهات')

@section('content')
<div class="space-y-4">
    <div>
        <h2 class="text-xl font-black text-[#1A2744]">🎬 مكتبة الفيديوهات</h2>
        <p class="text-sm text-[#6B7A99] mt-1">تسجيلات الحصص المتاحة لك.</p>
    </div>

    @php
        $videosEmpty = $videos instanceof \Illuminate\Contracts\Pagination\Paginator
            ? $videos->isEmpty()
            : collect($videos)->isEmpty();
    @endphp
    @if($videosEmpty)
        <div class="rounded-2xl border border-dashed border-[#E8EEF8] bg-white px-4 py-10 text-center text-sm text-[#6B7A99]">
            لا توجد فيديوهات منشورة بعد.
        </div>
    @else
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($videos as $video)
                <article class="rounded-2xl border border-[#E8EEF8] bg-white p-4">
                    <p class="text-xs font-bold text-[#0B3D91]">{{ $video->session?->course?->title ?: 'تسجيل' }}</p>
                    <h3 class="mt-1 font-black text-[#1A2744] truncate">{{ $video->session?->title ?: ('تسجيل #'.$video->id) }}</h3>
                    <p class="text-xs text-[#6B7A99] mt-1">{{ $video->session?->instructor?->name }}</p>
                    @if(Route::has('student.live-recordings.show'))
                        <a href="{{ route('student.live-recordings.show', $video) }}" class="mt-3 inline-flex h-9 items-center rounded-xl bg-[#F5B800] px-3 text-xs font-black text-[#072A66]">مشاهدة</a>
                    @endif
                </article>
            @endforeach
        </div>
        @if(method_exists($videos, 'links'))
            <div>{{ $videos->links() }}</div>
        @endif
    @endif
</div>
@endsection
