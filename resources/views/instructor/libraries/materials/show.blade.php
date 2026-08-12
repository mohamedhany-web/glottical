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
        <h3 class="text-base font-semibold mb-3">رفع ملف (PDF / PPT / …)</h3>
        <form method="POST" action="{{ route('instructor.libraries.materials.upload', $folder) }}" enctype="multipart/form-data" class="grid gap-3 md:grid-cols-4">
            @csrf
            <input type="text" name="title" placeholder="عنوان العرض" class="h-10 rounded-xl border border-slate-200 px-3 text-sm">
            <input type="file" name="file" required class="h-10 rounded-xl border border-slate-200 px-3 text-sm file:mr-2">
            <label class="inline-flex items-center gap-2 text-sm h-10"><input type="checkbox" name="is_visible_to_student" value="1" checked> ظاهر للطالب</label>
            <button class="h-10 rounded-xl bg-[#0B3D91] px-4 text-sm font-semibold text-white">رفع</button>
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
                    <tr><td colspan="3" class="px-4 py-8 text-center text-slate-500">لا ملفات بعد.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
