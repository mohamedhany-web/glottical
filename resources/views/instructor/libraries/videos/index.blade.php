@extends('layouts.app')

@section('title', 'مكتبة الفيديو')
@section('header', 'مكتبة الفيديو')

@section('content')
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-slate-500">لوحة المعلم</p>
            <h2 class="mt-1 text-2xl font-semibold text-slate-900">مكتبة الفيديو</h2>
            <p class="mt-1 text-sm text-slate-500">فيديوهات الأكاديمية للعرض، وفيديوهاتك تظهر لطلابك فقط — لا لمعلمين آخرين.</p>
        </div>
        <a href="{{ route('instructor.libraries.videos.create') }}" class="inline-flex h-10 items-center gap-2 rounded-xl bg-[#0B3D91] px-4 text-sm font-semibold text-white">
            <i class="fas fa-plus text-xs"></i> إضافة فيديو
        </a>
    </section>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
    @endif

    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="text-base font-semibold text-slate-900 mb-3">مجلد جديد (اختياري)</h3>
        <form method="POST" action="{{ route('instructor.libraries.videos.folders.store') }}" class="grid gap-3 md:grid-cols-5">
            @csrf
            <input type="text" name="name_ar" required placeholder="اسم المجلد (عربي)" class="h-10 rounded-xl border border-slate-200 px-3 text-sm">
            <input type="text" name="name_en" placeholder="Name (EN)" class="h-10 rounded-xl border border-slate-200 px-3 text-sm">
            <select name="academic_year_id" class="h-10 rounded-xl border border-slate-200 px-3 text-sm">
                <option value="">بدون سنة</option>
                @foreach($years as $y)
                    <option value="{{ $y->id }}">{{ $y->name }}</option>
                @endforeach
            </select>
            <select name="content_theme" class="h-10 rounded-xl border border-slate-200 px-3 text-sm">
                @foreach(\App\Support\FamilyLibraryThemes::labels('ar') as $key => $themeLabel)
                    <option value="{{ $key }}" @selected($key === 'kids')>{{ $themeLabel }}</option>
                @endforeach
            </select>
            <button class="h-10 rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-800 hover:border-[#0B3D91]/40">إنشاء مجلد</button>
        </form>
        @if($folders->isNotEmpty())
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach($folders as $folder)
                    <span class="inline-flex items-center gap-2 rounded-full bg-slate-50 px-3 py-1 text-xs text-slate-600 border border-slate-200">
                        {{ $folder->displayName() }}
                        <em class="not-italic text-slate-400">{{ (int) $folder->library_videos_count }}</em>
                    </span>
                @endforeach
            </div>
        @endif
    </article>

    @if(($academyVideos ?? collect())->isNotEmpty())
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-4 py-3">
            <h3 class="font-semibold text-slate-900">فيديوهات الأكاديمية</h3>
            <p class="mt-0.5 text-xs text-slate-500">عرض فقط — لا تعديل ولا حذف. لا تظهر فيديوهات معلمين آخرين.</p>
        </div>
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-xs text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-start">العنوان</th>
                    <th class="px-4 py-3 text-start">المجلد</th>
                    <th class="px-4 py-3 text-start">المصدر</th>
                    <th class="px-4 py-3 text-end">عرض</th>
                </tr>
            </thead>
            <tbody>
                @foreach($academyVideos as $video)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-3 font-semibold text-slate-900">{{ $video->title }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $video->folder?->displayName() ?: '—' }}</td>
                        <td class="px-4 py-3">{{ $video->sourceLabel() }}</td>
                        <td class="px-4 py-3 text-end">
                            <a href="{{ route('instructor.libraries.videos.watch', $video) }}" class="font-semibold text-[#0B3D91] hover:underline">مشاهدة</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-4 py-3">
            <h3 class="font-semibold text-slate-900">فيديوهاتك لطلابك</h3>
        </div>
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-xs text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-start">العنوان</th>
                    <th class="px-4 py-3 text-start">المجلد</th>
                    <th class="px-4 py-3 text-start">المصدر</th>
                    <th class="px-4 py-3 text-start">النشر</th>
                    <th class="px-4 py-3 text-start">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($videos as $video)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-3 font-semibold text-slate-900">{{ $video->title }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $video->folder?->displayName() ?: '—' }}</td>
                        <td class="px-4 py-3">{{ $video->sourceLabel() }}</td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('instructor.libraries.videos.toggle', $video) }}">
                                @csrf
                                <button class="rounded-full px-2.5 py-1 text-xs font-bold {{ $video->is_published ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $video->is_published ? 'منشور لطلابك' : 'مسودة' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('instructor.libraries.videos.watch', $video) }}" class="text-[#0B3D91] hover:underline">مشاهدة</a>
                                <a href="{{ route('instructor.libraries.videos.edit', $video) }}" class="text-[#0B3D91] hover:underline">تعديل</a>
                                <form method="POST" action="{{ route('instructor.libraries.videos.destroy', $video) }}" onsubmit="return confirm('حذف الفيديو؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-rose-600 hover:underline">حذف</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-slate-500">لا توجد فيديوهات بعد. أضف رابطاً أو ارفع ملفاً لطلابك.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($videos, 'hasPages') && $videos->hasPages())
        <div>{{ $videos->links() }}</div>
    @endif
</div>
@endsection
