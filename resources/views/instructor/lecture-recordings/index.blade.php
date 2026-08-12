@extends('layouts.app')

@section('title', 'تسجيل المحاضرات')
@section('header', 'تسجيل المحاضرات')

@section('content')
<div class="space-y-5">
    <section>
        <p class="text-xs font-medium text-slate-500">لوحة المعلم</p>
        <h2 class="mt-1 text-2xl font-semibold text-slate-900">تسجيل المحاضرات</h2>
        <p class="mt-1 text-sm text-slate-500">اربط رابط تشغيل (YouTube / Bunny / Vimeo) أو ارفع ملف فيديو — يشاهده الطالب داخل المنصة.</p>
    </section>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $errors->first() }}</div>
    @endif

    <div class="space-y-4">
        @forelse($lectures as $lecture)
            <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-slate-900">{{ $lecture->title }}</h3>
                        <p class="text-xs text-slate-500">{{ $lecture->course->title ?? '—' }}</p>
                        <p class="mt-1 text-xs">
                            @if($lecture->recording_url || $lecture->recording_file_path)
                                <span class="text-emerald-700 font-semibold">مسجّل</span>
                            @else
                                <span class="text-amber-700 font-semibold">بدون تسجيل</span>
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('instructor.lecture-recordings.preview', $lecture) }}" class="text-sm font-semibold text-[#0B3D91]">معاينة</a>
                </div>
                <form method="POST" action="{{ route('instructor.lecture-recordings.update', $lecture) }}" enctype="multipart/form-data" class="mt-3 grid gap-2 md:grid-cols-3">
                    @csrf
                    @method('PUT')
                    <input type="url" name="recording_url" value="{{ old('recording_url', $lecture->recording_url) }}" placeholder="رابط فيديو" class="h-10 rounded-xl border border-slate-200 px-3 text-sm md:col-span-2">
                    <input type="file" name="recording_file" accept="video/*" class="h-10 rounded-xl border border-slate-200 px-3 text-sm">
                    <label class="inline-flex items-center gap-2 text-xs text-slate-600"><input type="checkbox" name="clear_file" value="1"> حذف الملف المرفوع</label>
                    <button class="h-10 rounded-xl bg-[#0B3D91] px-4 text-sm font-semibold text-white md:col-span-2">حفظ التسجيل</button>
                </form>
            </article>
        @empty
            <p class="text-sm text-slate-500">لا محاضرات بعد.</p>
        @endforelse
    </div>

    {{ $lectures->links() }}

    @if($liveRecordings->isNotEmpty())
        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <h3 class="font-semibold text-slate-900 mb-3">تسجيلات الجلسات المباشرة</h3>
            <ul class="space-y-2 text-sm">
                @foreach($liveRecordings as $rec)
                    <li class="flex justify-between gap-3 border-b border-slate-100 pb-2">
                        <span>{{ $rec->title ?: ('تسجيل #'.$rec->id) }}</span>
                        <span class="text-xs text-slate-500">{{ $rec->is_published ? 'منشور' : 'مسودة' }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
@endsection
