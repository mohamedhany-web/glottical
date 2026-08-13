@extends('layouts.admin')

@section('title', 'المكتبات والمناهج')
@section('page_title', 'المكتبات والمناهج')

@section('content')
@php
    $cards = [
        [
            'title' => 'مكتبة الماتريال',
            'desc' => 'كتب PDF وعروض وHTML وألعاب — مكتبة آمنة داخل المنصة',
            'stat' => $stats['materials_total'],
            'meta' => $stats['materials_visible'].' ظاهر · '.($stats['materials_total'] - $stats['materials_visible']).' مخفي',
            'icon' => 'fa-book-open',
            'href' => route('admin.libraries.materials.index'),
            'cta' => 'إدارة الماتريال',
        ],
        [
            'title' => 'مكتبة الفيديوهات',
            'desc' => 'فيديوهات أطفال ومسلسلات إسلامية — بديل آمن عن يوتيوب المفتوح',
            'stat' => $stats['videos_ready'],
            'meta' => $stats['videos_published'].' منشور · '.($stats['video_folders'] ?? 0).' مجلد',
            'icon' => 'fa-film',
            'href' => route('admin.libraries.videos.index'),
            'cta' => 'إدارة الفيديوهات',
        ],
        [
            'title' => 'مجلدات المكتبة',
            'desc' => 'تصنيف المحتوى (كتب/ألعاب/أطفال/إسلامي) + معلم × سنة',
            'stat' => $stats['video_folders'] ?? 0,
            'meta' => 'تنظيم المدرسة التفاعلية',
            'icon' => 'fa-folder-open',
            'href' => route('admin.libraries.folders.index'),
            'cta' => 'إدارة المجلدات',
        ],
        [
            'title' => 'المناهج',
            'desc' => 'سنوات المدرسة والمواد ومناهج الكورسات',
            'stat' => $stats['years'],
            'meta' => $stats['subjects'].' مادة · '.$stats['courses'].' كورس · '.$stats['curriculum_items'].' عنصر',
            'icon' => 'fa-sitemap',
            'href' => route('admin.libraries.curriculum.index'),
            'cta' => 'إدارة المناهج',
        ],
    ];
@endphp

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-muted">التعليم · مكتبات الطلاب</p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">مركز المكتبات والمناهج</h2>
            <p class="mt-1 text-sm text-muted">تحكم كامل بما يظهر للطالب في مكتبة الماتريال والفيديوهات ومسارات المناهج.</p>
        </div>
    </section>

    @include('admin.partials.workflow-guide', [
        'title' => 'المكتبات والمناهج باختصار',
        'body' => 'هنا تنظيم المحتوى الذي يراه الطالب في مكتبته — وليس تسكين الطلاب في فصول حية. المجلدات تصنّف الفيديوهات، والمناهج ترتب أقسام الكورس.',
        'steps' => [
            'الماتريال: ارفع الملفات وحدد الظهور للطالب.',
            'الفيديوهات: أدر التسجيلات واربطها بمجلدات للتصفح.',
            'المناهج: رتّب سنوات/مواد/عناصر المنهج داخل البرامج.',
        ],
    ])

    @if(session('success'))
        <div class="rounded-2xl border border-line bg-surface px-4 py-3 text-sm text-ink">{{ session('success') }}</div>
    @endif

    <div class="grid gap-4 md:grid-cols-3">
        @foreach($cards as $card)
            <a href="{{ $card['href'] }}" class="group rounded-2xl border border-line bg-surface p-5 shadow-soft transition hover:border-accent/40 hover:shadow-md">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-accent/10 text-accent">
                        <i class="fas {{ $card['icon'] }}"></i>
                    </div>
                    <span class="text-3xl font-semibold text-ink">{{ number_format($card['stat']) }}</span>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-ink group-hover:text-accent">{{ $card['title'] }}</h3>
                <p class="mt-1 text-sm text-muted">{{ $card['desc'] }}</p>
                <p class="mt-3 text-xs font-medium text-ink-soft">{{ $card['meta'] }}</p>
                <span class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-accent">
                    {{ $card['cta'] }} <i class="fas fa-arrow-left text-xs"></i>
                </span>
            </a>
        @endforeach
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="flex items-center justify-between border-b border-line px-4 py-3">
                <h3 class="font-semibold text-ink">أحدث الماتريال</h3>
                <a href="{{ route('admin.libraries.materials.create') }}" class="text-sm font-medium text-accent hover:underline">رفع ملف</a>
            </div>
            <div class="divide-y divide-line">
                @forelse($recentMaterials as $m)
                    <div class="flex items-center justify-between gap-3 px-4 py-3 text-sm">
                        <div class="min-w-0">
                            <div class="truncate font-medium text-ink">{{ $m->title ?: $m->file_name }}</div>
                            <div class="truncate text-xs text-muted">{{ $m->lecture?->course?->title }} · {{ $m->lecture?->title }}</div>
                        </div>
                        <span class="shrink-0 rounded-full px-2 py-0.5 text-[11px] font-bold {{ $m->is_visible_to_student ? 'bg-success/10 text-success' : 'bg-canvas-muted text-muted' }}">
                            {{ $m->is_visible_to_student ? 'ظاهر' : 'مخفي' }}
                        </span>
                    </div>
                @empty
                    <p class="px-4 py-8 text-center text-sm text-muted">لا ماتريال بعد.</p>
                @endforelse
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="flex items-center justify-between border-b border-line px-4 py-3">
                <h3 class="font-semibold text-ink">أحدث فيديوهات المكتبة</h3>
                <a href="{{ route('admin.libraries.videos.create') }}" class="text-sm font-medium text-accent hover:underline">إضافة فيديو</a>
            </div>
            <div class="divide-y divide-line">
                @forelse($recentVideos as $v)
                    <div class="flex items-center justify-between gap-3 px-4 py-3 text-sm">
                        <div class="min-w-0">
                            <div class="truncate font-medium text-ink">{{ $v->title }}</div>
                            <div class="truncate text-xs text-muted">{{ $v->folder?->displayName() ?: 'عام' }} · {{ $v->sourceLabel() }}</div>
                        </div>
                        <span class="shrink-0 rounded-full px-2 py-0.5 text-[11px] font-bold {{ $v->is_published ? 'bg-success/10 text-success' : 'bg-canvas-muted text-muted' }}">
                            {{ $v->is_published ? 'منشور' : 'مسودة' }}
                        </span>
                    </div>
                @empty
                    <p class="px-4 py-8 text-center text-sm text-muted">لا فيديوهات بعد.</p>
                @endforelse
            </div>
        </article>
    </div>

    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="text-xs text-muted">أقسام المناهج</div>
            <div class="mt-1 text-2xl font-semibold text-ink">{{ number_format($stats['sections']) }}</div>
        </div>
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="text-xs text-muted">عناصر المنهج</div>
            <div class="mt-1 text-2xl font-semibold text-ink">{{ number_format($stats['curriculum_items']) }}</div>
        </div>
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="text-xs text-muted">كورسات</div>
            <div class="mt-1 text-2xl font-semibold text-ink">{{ number_format($stats['courses']) }}</div>
        </div>
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="text-xs text-muted">مواد المدرسة</div>
            <div class="mt-1 text-2xl font-semibold text-ink">{{ number_format($stats['subjects']) }}</div>
        </div>
    </div>
</div>
@endsection
