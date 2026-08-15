@extends('layouts.admin')

@section('title', 'تصنيفات المناهج التفاعلية')
@section('page_title', 'تصنيفات المناهج التفاعلية')

@section('content')
<div class="space-y-5">
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-muted">
                <a href="{{ route('admin.libraries.index') }}" class="hover:text-accent">مكتبة الملفات</a>
                ·
                <a href="{{ route('admin.curriculum-library.index') }}" class="hover:text-accent">المناهج التفاعلية</a>
            </p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">تصنيفات المكتبة</h2>
            <p class="mt-1 text-sm text-muted">تنظيم عناصر المنهج تحت تصنيفات (رياضيات، علوم، لغة عربية…).</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.curriculum-library.index') }}" class="btn-press inline-flex h-9 items-center rounded-xl border border-line px-3 text-sm">العناصر</a>
            <a href="{{ route('admin.curriculum-library.categories.create') }}" class="btn-press inline-flex h-9 items-center rounded-xl bg-accent px-3 text-sm font-medium text-white">
                <i class="fas fa-plus ml-1"></i> إضافة تصنيف
            </a>
        </div>
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="divide-y divide-line">
            @forelse($categories as $cat)
                <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 hover:bg-canvas-muted/30">
                    <div class="min-w-0">
                        <h3 class="font-semibold text-ink">{{ $cat->name }}</h3>
                        @if($cat->description)
                            <p class="mt-0.5 text-sm text-muted">{{ \Illuminate\Support\Str::limit($cat->description, 90) }}</p>
                        @endif
                        <p class="mt-1 text-xs text-muted">{{ $cat->items_count }} عنصر · ترتيب {{ $cat->order }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $cat->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                            {{ $cat->is_active ? 'نشط' : 'معطل' }}
                        </span>
                        @if(!empty($cat->is_restricted))
                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800" title="يظهر فقط للمستخدمين المحددين">
                                <i class="fas fa-user-lock text-[10px]"></i> خاص ({{ $cat->restricted_users_count }})
                            </span>
                        @endif
                        <a href="{{ route('admin.curriculum-library.categories.edit', $cat) }}" class="text-sm font-semibold text-accent hover:underline">تعديل</a>
                        <form action="{{ route('admin.curriculum-library.categories.destroy', $cat) }}" method="POST" class="inline" onsubmit="return confirm('حذف التصنيف؟ العناصر لن تُحذف لكن ستفقد التصنيف.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm font-semibold text-rose-600 hover:text-rose-800">حذف</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="px-5 py-12 text-center text-muted">
                    لا توجد تصنيفات.
                    <a href="{{ route('admin.curriculum-library.categories.create') }}" class="font-semibold text-accent">أضف تصنيفاً</a>
                </div>
            @endforelse
        </div>
    </article>
</div>
@endsection
