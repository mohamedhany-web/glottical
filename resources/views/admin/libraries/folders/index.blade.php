@extends('layouts.admin')

@section('title', 'مجلدات المكتبات')
@section('page_title', 'مجلدات المكتبات')

@section('content')
@php
    $kindLabel = [
        'videos' => 'فيديو',
        'materials' => 'ماتريال',
        'both' => 'فيديو + ماتريال',
    ];
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-muted"><a href="{{ route('admin.libraries.index') }}" class="hover:text-accent">مركز المكتبات</a></p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">مجلدات التصنيف</h2>
            <p class="mt-1 text-sm text-muted">فولدرات معلم × سنة للماتريال والفيديو، مع بوابة باقة المكتبات.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.libraries.videos.index') }}" class="btn-press inline-flex h-9 items-center rounded-xl border border-line px-4 text-sm">الفيديوهات</a>
            <a href="{{ route('admin.libraries.materials.index') }}" class="btn-press inline-flex h-9 items-center rounded-xl border border-line px-4 text-sm">الماتريال</a>
            <a href="{{ route('admin.libraries.folders.create') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                <i class="fas fa-folder-plus text-xs"></i> مجلد جديد
            </a>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse($folders as $folder)
            <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-accent/10 text-accent">
                            <i class="{{ $folder->icon }}"></i>
                        </span>
                        <div class="min-w-0">
                            <h3 class="truncate font-semibold text-ink">{{ $folder->name_ar }}</h3>
                            @if($folder->name_en)
                                <p class="truncate text-xs text-muted">{{ $folder->name_en }}</p>
                            @endif
                        </div>
                    </div>
                    <span class="rounded-full px-2 py-0.5 text-[11px] font-bold {{ $folder->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ $folder->is_active ? 'نشط' : 'مخفي' }}
                    </span>
                </div>
                <div class="mt-3 flex flex-wrap gap-2 text-[11px]">
                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-slate-700">{{ $kindLabel[$folder->kind] ?? $folder->kind }}</span>
                    @if($folder->academicYear)
                        <span class="rounded-full bg-blue-50 px-2 py-0.5 text-blue-700">{{ $folder->academicYear->name }}</span>
                    @endif
                    @if($folder->instructor)
                        <span class="rounded-full bg-amber-50 px-2 py-0.5 text-amber-800">{{ $folder->instructor->name }}</span>
                    @endif
                    <span class="rounded-full px-2 py-0.5 {{ $folder->requires_library_entitlement ? 'bg-rose-50 text-rose-700' : 'bg-emerald-50 text-emerald-700' }}">
                        {{ $folder->requires_library_entitlement ? 'يتطلب باقة' : 'مجاني' }}
                    </span>
                </div>
                @if($folder->description_ar)
                    <p class="mt-3 text-sm text-muted line-clamp-2">{{ $folder->description_ar }}</p>
                @endif
                <p class="mt-3 text-xs text-muted">
                    {{ (int) ($folder->library_videos_count ?? 0) }} فيديو مكتبة · {{ (int) $folder->materials_count }} ماتريال · ترتيب {{ $folder->sort_order }}
                </p>
                <div class="mt-4 flex flex-wrap gap-3 text-sm">
                    <a href="{{ route('admin.libraries.folders.edit', $folder) }}" class="text-accent hover:underline">تعديل</a>
                    @if(in_array($folder->kind, ['videos', 'both'], true))
                        <a href="{{ route('admin.libraries.videos.index', ['folder_id' => $folder->id]) }}" class="text-ink-soft hover:underline">الفيديوهات</a>
                    @endif
                    @if(in_array($folder->kind, ['materials', 'both'], true))
                        <a href="{{ route('admin.libraries.materials.index', ['folder_id' => $folder->id]) }}" class="text-ink-soft hover:underline">الماتريال</a>
                    @endif
                    <form method="POST" action="{{ route('admin.libraries.folders.destroy', $folder) }}" onsubmit="return confirm('حذف المجلد؟ المحتوى سيبقى بدون مجلد.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-danger hover:underline">حذف</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="sm:col-span-2 xl:col-span-3 rounded-2xl border border-dashed border-line bg-surface px-6 py-10 text-center text-sm text-muted">
                لا توجد مجلدات بعد. أنشئ أول مجلد لتنظيم المكتبات.
            </div>
        @endforelse
    </div>
</div>
@endsection
