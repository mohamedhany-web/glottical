@extends('layouts.app')

@section('title', $folder->displayName())
@section('header', $folder->displayName())

@section('content')
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-slate-500"><a href="{{ route('instructor.libraries.materials.index') }}" class="hover:text-[#0B3D91]">مكتبة الماتريال</a> · {{ $folder->academicYear->name ?? '' }}</p>
            <h2 class="mt-1 text-2xl font-semibold text-slate-900">{{ $folder->displayName() }}</h2>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $errors->first() }}</div>
    @endif

    @if($canManage ?? true)
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="text-base font-semibold mb-3">رفع محتوى آمن (PDF / PPT / HTML / ألعاب)</h3>
        <form method="POST" action="{{ route('instructor.libraries.materials.upload', $folder) }}" enctype="multipart/form-data" class="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
            @csrf
            <input type="text" name="title" placeholder="عنوان العرض" class="h-10 rounded-xl border border-slate-200 px-3 text-sm">
            <input type="text" name="description" placeholder="وصف مختصر للطالب" class="h-10 rounded-xl border border-slate-200 px-3 text-sm">
            <select name="content_theme" class="h-10 rounded-xl border border-slate-200 px-3 text-sm">
                @foreach(\App\Support\FamilyLibraryThemes::labels('ar') as $key => $themeLabel)
                    <option value="{{ $key }}" @selected(($folder->content_theme ?: 'general') === $key)>{{ $themeLabel }}</option>
                @endforeach
            </select>
            <select name="experience_mode" class="h-10 rounded-xl border border-slate-200 px-3 text-sm">
                <option value="download">تحميل</option>
                <option value="view">عرض داخل المنصة</option>
                <option value="play">لعب داخل المنصة</option>
            </select>
            <input type="file" name="file" required accept="{{ \App\Support\FamilyLibraryThemes::materialAcceptAttr() }}" class="h-10 rounded-xl border border-slate-200 px-3 text-sm file:mr-2">
            <label class="inline-flex items-center gap-2 text-sm h-10"><input type="checkbox" name="is_visible_to_student" value="1" checked> ظاهر للطالب</label>
            <button class="h-10 rounded-xl bg-[#0B3D91] px-4 text-sm font-semibold text-white md:col-span-2 lg:col-span-1">رفع</button>
        </form>
    </article>
    @else
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">مجلد إداري — عرض فقط. ارفع في فولدراتك الخاصة.</div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-xs text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-start">الملف</th>
                    <th class="px-4 py-3 text-start">التصنيف</th>
                    <th class="px-4 py-3 text-start">الظهور</th>
                    <th class="px-4 py-3 text-end">إجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($folder->materials as $m)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-900">{{ $m->title ?: $m->file_name }}</div>
                            <div class="text-xs text-slate-500">{{ $m->file_name }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs">{{ $m->themeLabel('ar') }} · {{ $m->experience_mode ?: 'download' }}</td>
                        <td class="px-4 py-3">{{ $m->is_visible_to_student ? 'ظاهر' : 'مخفي' }}</td>
                        <td class="px-4 py-3 text-end">
                            @if($canManage ?? true)
                            <form method="POST" action="{{ route('instructor.libraries.materials.destroy', [$folder, $m]) }}" onsubmit="return confirm('حذف الملف؟')">
                                @csrf
                                @method('DELETE')
                                <button class="text-rose-600 font-semibold">حذف</button>
                            </form>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">لا ملفات بعد.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
