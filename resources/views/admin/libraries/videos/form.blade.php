@extends('layouts.admin')

@section('title', $mode === 'create' ? 'إضافة تسجيل بث' : 'تعديل تسجيل بث')
@section('page_title', $mode === 'create' ? 'إضافة تسجيل بث' : 'تعديل تسجيل بث')

@section('content')
@php
    $field = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $label = 'mb-1.5 block text-xs font-medium text-muted';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-muted"><a href="{{ route('admin.libraries.videos.index') }}" class="hover:text-accent">مكتبة الفيديوهات</a></p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">{{ $mode === 'create' ? 'تسجيل بث جديد' : 'تعديل تسجيل' }}</h2>
        </div>
        <a href="{{ route('admin.libraries.videos.index') }}" class="btn-press inline-flex h-9 items-center rounded-xl border border-line px-4 text-sm">رجوع</a>
    </section>

    @if($errors->any())
        <div class="rounded-xl border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger">{{ $errors->first() }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger">{{ session('error') }}</div>
    @endif

    <form method="POST"
          action="{{ $mode === 'create' ? route('admin.libraries.videos.store') : route('admin.libraries.videos.update', $recording) }}"
          class="space-y-5">
        @csrf
        @if($mode === 'edit') @method('PUT') @endif

        <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft space-y-4">
            <div>
                <label class="{{ $label }}">جلسة البث *</label>
                <select name="session_id" required class="{{ $field }}">
                    <option value="">اختر جلسة…</option>
                    @foreach($sessions as $session)
                        <option value="{{ $session->id }}" @selected((string) old('session_id', $recording->session_id) === (string) $session->id)>
                            #{{ $session->id }} — {{ $session->title }}
                            @if($session->course) ({{ $session->course->title }}) @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $label }}">عنوان العرض</label>
                <input type="text" name="title" value="{{ old('title', $recording->title) }}" class="{{ $field }}" placeholder="اختياري — يُستخدم عنوان الجلسة">
            </div>
            <div>
                <label class="{{ $label }}">رابط خارجي (YouTube / Vimeo / …)</label>
                <input type="url" name="external_url" value="{{ old('external_url', $recording->external_url) }}" class="{{ $field }}" placeholder="https://…">
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="{{ $label }}">مسار الملف (R2 / storage)</label>
                    <input type="text" name="file_path" value="{{ old('file_path', $recording->file_path) }}" class="{{ $field }}" placeholder="live-recordings/session-1/rec.mp4">
                </div>
                <div>
                    <label class="{{ $label }}">قرص التخزين</label>
                    <select name="storage_disk" class="{{ $field }}">
                        @foreach(['public', 'r2', 'local'] as $disk)
                            <option value="{{ $disk }}" @selected(old('storage_disk', $recording->storage_disk ?: 'public') === $disk)>{{ $disk }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="{{ $label }}">المدة (ثوانٍ)</label>
                    <input type="number" name="duration_seconds" min="0" value="{{ old('duration_seconds', $recording->duration_seconds ?? 0) }}" class="{{ $field }}">
                </div>
                <div>
                    <label class="{{ $label }}">الحالة *</label>
                    <select name="status" required class="{{ $field }}">
                        @foreach(['ready' => 'جاهز', 'processing' => 'قيد المعالجة', 'failed' => 'فشل'] as $val => $lbl)
                            <option value="{{ $val }}" @selected(old('status', $recording->status ?: 'ready') === $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end pb-2">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $recording->is_published ?? true))>
                        منشور في مكتبة الطلاب
                    </label>
                </div>
            </div>
            <p class="text-xs text-muted">يجب إدخال رابط خارجي أو مسار ملف (أحدهما على الأقل).</p>
        </article>

        <button class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-semibold text-white">
            <i class="fas fa-save text-xs"></i> حفظ
        </button>
    </form>
</div>
@endsection
