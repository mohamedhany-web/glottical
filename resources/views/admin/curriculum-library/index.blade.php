@extends('layouts.admin')

@section('title', 'مكتبة المناهج التفاعلية')
@section('page_title', 'مكتبة المناهج التفاعلية')

@section('content')
<div class="space-y-5">
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-muted"><a href="{{ route('admin.libraries.index') }}" class="hover:text-accent">المكتبات</a> · مناهج تفاعلية</p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">عناصر المناهج التفاعلية</h2>
            <p class="mt-1 text-sm text-muted">إدارة محتوى Manahij X الجاهز للمعلمين والعرض داخل الفصل — بجانب هيكل الكورسات الحالي.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.curriculum-library.categories') }}" class="btn-press inline-flex h-9 items-center rounded-xl border border-line px-3 text-sm">التصنيفات</a>
            <a href="{{ route('admin.curriculum-library.items.create') }}" class="btn-press inline-flex h-9 items-center rounded-xl bg-accent px-3 text-sm font-medium text-white">إضافة عنصر</a>
        </div>
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <form method="GET" class="flex flex-wrap items-center gap-3 border-b border-line p-4">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="بحث في العنوان أو الوصف أو المادة..."
                   class="h-10 w-64 rounded-xl border border-line bg-surface px-3 text-sm">
            <select name="category_id" class="h-10 rounded-xl border border-line bg-surface px-3 text-sm">
                <option value="">كل التصنيفات</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-press h-10 rounded-xl bg-ink px-4 text-sm font-medium text-white"><i class="fas fa-search ml-1"></i> بحث</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-canvas-muted/40 text-sm font-semibold text-muted">
                    <tr>
                        <th class="px-4 py-3">العنوان</th>
                        <th class="px-4 py-3">التصنيف</th>
                        <th class="px-4 py-3">المادة / المرحلة</th>
                        <th class="px-4 py-3">الحالة</th>
                        <th class="px-4 py-3">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($items as $item)
                        <tr class="hover:bg-canvas-muted/30">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.curriculum-library.items.edit', $item) }}" class="font-semibold text-ink hover:text-accent">{{ $item->title }}</a>
                            </td>
                            <td class="px-4 py-3 text-muted">{{ $item->category?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-muted">{{ $item->subject ?? '—' }} @if($item->grade_level) / {{ $item->grade_level }} @endif</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $item->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $item->is_active ? 'نشط' : 'معطل' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.curriculum-library.items.structure', $item) }}" class="mr-2 text-sm font-semibold text-sky-600 hover:text-sky-800">هيكل</a>
                                <a href="{{ route('admin.curriculum-library.items.edit', $item) }}" class="text-sm font-semibold text-accent hover:underline">تعديل</a>
                                <form action="{{ route('admin.curriculum-library.items.destroy', $item) }}" method="POST" class="mr-2 inline-block" onsubmit="return confirm('حذف هذا العنصر؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-semibold text-rose-600 hover:text-rose-800">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-muted">لا توجد عناصر. <a href="{{ route('admin.curriculum-library.items.create') }}" class="font-semibold text-accent">أضف أول عنصر</a></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($items->hasPages())
            <div class="border-t border-line px-6 py-4">{{ $items->withQueryString()->links() }}</div>
        @endif
    </article>
</div>
@endsection
