@extends('layouts.admin')

@section('title', 'مكتبة الماتريال')
@section('page_title', 'مكتبة الماتريال')

@section('content')
@php
    $field = 'h-10 w-full rounded-xl border border-line bg-surface px-3 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-muted"><a href="{{ route('admin.libraries.index') }}" class="hover:text-accent">المكتبات</a> · ماتريال</p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">مكتبة الماتريال</h2>
            <p class="mt-1 text-sm text-muted">
                كل ملفات المحاضرات تُرفع على
                <strong>{{ ($stats['storage_disk'] ?? 'r2') === 'r2' ? 'Cloudflare R2' : ($stats['storage_disk'] ?? 'التخزين') }}</strong>
                وتظهر للطلاب في «مكتبة الماتريال».
            </p>
        </div>
        <a href="{{ route('admin.libraries.materials.create') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
            <i class="fas fa-upload text-xs"></i> رفع ماتريال
        </a>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-line bg-surface px-4 py-3 text-sm text-ink">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl border border-danger/20 bg-danger/5 px-4 py-3 text-sm text-danger">{{ session('error') }}</div>
    @endif

    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><div class="text-xs text-muted">الإجمالي</div><div class="mt-1 text-2xl font-semibold">{{ $stats['total'] }}</div></div>
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><div class="text-xs text-muted">ظاهر للطلاب</div><div class="mt-1 text-2xl font-semibold text-success">{{ $stats['visible'] }}</div></div>
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><div class="text-xs text-muted">مخفي</div><div class="mt-1 text-2xl font-semibold">{{ $stats['hidden'] }}</div></div>
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft"><div class="text-xs text-muted">كورسات مرتبطة</div><div class="mt-1 text-2xl font-semibold">{{ $stats['courses'] }}</div></div>
    </div>

    <form method="GET" class="grid gap-3 rounded-2xl border border-line bg-surface p-4 shadow-soft md:grid-cols-5">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="بحث بالعنوان أو الملف أو المحاضرة" class="{{ $field }} md:col-span-2">
        <select name="course_id" class="{{ $field }}">
            <option value="">كل الكورسات</option>
            @foreach($courses as $c)
                <option value="{{ $c->id }}" @selected((string) request('course_id') === (string) $c->id)>{{ $c->title }}</option>
            @endforeach
        </select>
        <select name="visibility" class="{{ $field }}">
            <option value="">كل الظهور</option>
            <option value="visible" @selected(request('visibility') === 'visible')>ظاهر</option>
            <option value="hidden" @selected(request('visibility') === 'hidden')>مخفي</option>
        </select>
        <button class="btn-press h-10 rounded-xl bg-ink px-4 text-sm font-medium text-white">تصفية</button>
    </form>

    <form method="POST" action="{{ route('admin.libraries.materials.bulk-visibility') }}" id="bulkForm">
        @csrf
        <input type="hidden" name="visible" id="bulkVisible" value="1">
        <div class="mb-3 flex flex-wrap gap-2">
            <button type="submit" onclick="document.getElementById('bulkVisible').value='1'" class="btn-press inline-flex h-9 items-center rounded-xl border border-line bg-surface px-3 text-xs font-medium">إظهار المحدد</button>
            <button type="submit" onclick="document.getElementById('bulkVisible').value='0'" class="btn-press inline-flex h-9 items-center rounded-xl border border-line bg-surface px-3 text-xs font-medium">إخفاء المحدد</button>
        </div>

        <div class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <table class="min-w-full text-sm">
                <thead class="bg-canvas-muted text-xs text-muted">
                    <tr>
                        <th class="px-3 py-3"><input type="checkbox" id="checkAll"></th>
                        <th class="px-4 py-3 text-start">الملف</th>
                        <th class="px-4 py-3 text-start">المحاضرة / الكورس</th>
                        <th class="px-4 py-3 text-start">الظهور</th>
                        <th class="px-4 py-3 text-start">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials as $m)
                        <tr class="border-t border-line">
                            <td class="px-3 py-3"><input type="checkbox" name="ids[]" value="{{ $m->id }}" class="row-check"></td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-ink">{{ $m->title ?: $m->file_name }}</div>
                                <div class="text-xs text-muted">{{ $m->file_name }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div>{{ $m->lecture?->title ?: '—' }}</div>
                                <div class="text-xs text-muted">{{ $m->lecture?->course?->title }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <button type="submit" form="toggle-{{ $m->id }}" class="rounded-full px-2.5 py-1 text-xs font-bold {{ $m->is_visible_to_student ? 'bg-success/10 text-success' : 'bg-canvas-muted text-muted' }}">
                                    {{ $m->is_visible_to_student ? 'ظاهر' : 'مخفي' }}
                                </button>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.libraries.materials.download', $m) }}" class="text-accent hover:underline">تحميل</a>
                                    <a href="{{ route('admin.libraries.materials.edit', $m) }}" class="text-ink-soft hover:underline">تعديل</a>
                                    <button type="submit" form="destroy-{{ $m->id }}" class="text-danger hover:underline" onclick="return confirm('حذف الملف؟')">حذف</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-10 text-center text-muted">لا توجد ملفات.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <div>{{ $materials->links() }}</div>

    @foreach($materials as $m)
        <form id="toggle-{{ $m->id }}" method="POST" action="{{ route('admin.libraries.materials.toggle', $m) }}" class="hidden">@csrf</form>
        <form id="destroy-{{ $m->id }}" method="POST" action="{{ route('admin.libraries.materials.destroy', $m) }}" class="hidden">@csrf @method('DELETE')</form>
    @endforeach
</div>

@push('scripts')
<script>
document.getElementById('checkAll')?.addEventListener('change', function () {
    document.querySelectorAll('.row-check').forEach(function (el) { el.checked = this.checked; }.bind(this));
});
</script>
@endpush
@endsection
