@extends('layouts.admin')

@section('title', 'مكتبة الفيديوهات')
@section('page_title', 'مكتبة الفيديوهات')

@section('content')
@php
    $field = 'h-10 w-full rounded-xl border border-line bg-surface px-3 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-muted"><a href="{{ route('admin.libraries.index') }}" class="hover:text-accent">المكتبات</a> · فيديو</p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">مكتبة الفيديوهات</h2>
            <p class="mt-1 text-sm text-muted">تسجيلات البث المباشر + تسجيلات المحاضرات الظاهرة للطلاب.</p>
        </div>
        <a href="{{ route('admin.libraries.videos.create') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
            <i class="fas fa-video text-xs"></i> إضافة تسجيل بث
        </a>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-line bg-surface px-4 py-3 text-sm text-ink">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl border border-danger/20 bg-danger/5 px-4 py-3 text-sm text-danger">{{ session('error') }}</div>
    @endif

    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><div class="text-xs text-muted">تسجيلات بث</div><div class="mt-1 text-2xl font-semibold">{{ $stats['live_total'] }}</div></div>
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><div class="text-xs text-muted">جاهزة</div><div class="mt-1 text-2xl font-semibold">{{ $stats['live_ready'] }}</div></div>
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><div class="text-xs text-muted">منشورة</div><div class="mt-1 text-2xl font-semibold text-success">{{ $stats['live_published'] }}</div></div>
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><div class="text-xs text-muted">محاضرات بتسجيل</div><div class="mt-1 text-2xl font-semibold">{{ $stats['lecture_videos'] }}</div></div>
    </div>

    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.libraries.videos.index', ['tab' => 'all'] + request()->except('tab')) }}" class="rounded-xl px-3 py-1.5 text-xs font-bold {{ ($tab ?? 'all') === 'all' ? 'bg-accent text-white' : 'border border-line bg-surface text-ink' }}">الكل</a>
        <a href="{{ route('admin.libraries.videos.index', ['tab' => 'live'] + request()->except('tab')) }}" class="rounded-xl px-3 py-1.5 text-xs font-bold {{ ($tab ?? '') === 'live' ? 'bg-accent text-white' : 'border border-line bg-surface text-ink' }}">بث مباشر</a>
        <a href="{{ route('admin.libraries.videos.index', ['tab' => 'lectures'] + request()->except('tab')) }}" class="rounded-xl px-3 py-1.5 text-xs font-bold {{ ($tab ?? '') === 'lectures' ? 'bg-accent text-white' : 'border border-line bg-surface text-ink' }}">محاضرات</a>
    </div>

    <form method="GET" class="grid gap-3 rounded-2xl border border-line bg-surface p-4 shadow-soft md:grid-cols-5">
        <input type="hidden" name="tab" value="{{ $tab ?? 'all' }}">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="بحث بالعنوان" class="{{ $field }} md:col-span-2">
        <select name="published" class="{{ $field }}">
            <option value="">كل النشر</option>
            <option value="1" @selected(request('published') === '1')>منشور</option>
            <option value="0" @selected(request('published') === '0')>مسودة</option>
        </select>
        <select name="status" class="{{ $field }}">
            <option value="">كل الحالات</option>
            <option value="ready" @selected(request('status') === 'ready')>جاهز</option>
            <option value="processing" @selected(request('status') === 'processing')>قيد المعالجة</option>
            <option value="failed" @selected(request('status') === 'failed')>فشل</option>
        </select>
        <button class="btn-press h-10 rounded-xl bg-ink px-4 text-sm font-medium text-white">تصفية</button>
    </form>

    @if(($tab ?? 'all') !== 'lectures')
    <div class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-3 text-sm font-semibold text-ink">تسجيلات البث المباشر</div>
        <table class="min-w-full text-sm">
            <thead class="bg-canvas-muted text-xs text-muted">
                <tr>
                    <th class="px-4 py-3 text-start">العنوان</th>
                    <th class="px-4 py-3 text-start">الجلسة / الكورس</th>
                    <th class="px-4 py-3 text-start">الحالة</th>
                    <th class="px-4 py-3 text-start">النشر</th>
                    <th class="px-4 py-3 text-start">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($liveRecordings as $r)
                    <tr class="border-t border-line">
                        <td class="px-4 py-3 font-semibold text-ink">{{ $r->title ?: 'تسجيل #'.$r->id }}</td>
                        <td class="px-4 py-3">
                            <div>{{ $r->session?->title ?: '—' }}</div>
                            <div class="text-xs text-muted">{{ $r->session?->course?->title }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs font-bold">{{ $r->status }}</td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.libraries.videos.toggle', $r) }}">@csrf
                                <button class="rounded-full px-2.5 py-1 text-xs font-bold {{ $r->is_published ? 'bg-success/10 text-success' : 'bg-canvas-muted text-muted' }}">
                                    {{ $r->is_published ? 'منشور' : 'مسودة' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-2">
                                @if($r->getUrl())
                                    <a href="{{ $r->getUrl() }}" target="_blank" rel="noopener" class="text-accent hover:underline">فتح</a>
                                @endif
                                <a href="{{ route('admin.libraries.videos.edit', $r) }}" class="text-ink-soft hover:underline">تعديل</a>
                                <form method="POST" action="{{ route('admin.libraries.videos.destroy', $r) }}" onsubmit="return confirm('حذف التسجيل؟')">@csrf @method('DELETE')
                                    <button class="text-danger hover:underline">حذف</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-muted">لا توجد تسجيلات بث.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-line px-4 py-3">{{ $liveRecordings->links() }}</div>
    </div>
    @endif

    @if(($tab ?? 'all') !== 'live')
    <div class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-3 text-sm font-semibold text-ink">تسجيلات المحاضرات</div>
        <table class="min-w-full text-sm">
            <thead class="bg-canvas-muted text-xs text-muted">
                <tr>
                    <th class="px-4 py-3 text-start">المحاضرة</th>
                    <th class="px-4 py-3 text-start">الكورس</th>
                    <th class="px-4 py-3 text-start">الرابط</th>
                    <th class="px-4 py-3 text-start">تحديث سريع</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lectureVideos as $lec)
                    <tr class="border-t border-line">
                        <td class="px-4 py-3 font-semibold">{{ $lec->title }}</td>
                        <td class="px-4 py-3 text-muted">{{ $lec->course?->title }}</td>
                        <td class="px-4 py-3">
                            @if($lec->recording_url)
                                <a href="{{ $lec->recording_url }}" target="_blank" rel="noopener" class="text-accent hover:underline">فتح</a>
                            @elseif($lec->recording_file_path)
                                <span class="text-xs text-muted">ملف: {{ $lec->recording_file_path }}</span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.libraries.videos.lecture.update', $lec) }}" class="flex flex-wrap items-center gap-2">
                                @csrf @method('PUT')
                                <input type="url" name="recording_url" value="{{ $lec->recording_url }}" placeholder="https://…" class="h-9 min-w-[12rem] flex-1 rounded-lg border border-line px-2 text-xs">
                                <button class="btn-press h-9 rounded-lg bg-ink px-3 text-xs text-white">حفظ</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-muted">لا محاضرات بتسجيل.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-line px-4 py-3">{{ $lectureVideos->links() }}</div>
    </div>
    @endif
</div>
@endsection
