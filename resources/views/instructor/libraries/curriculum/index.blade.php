@extends('layouts.app')

@section('title', 'مكتبة المناهج')
@section('header', 'مكتبة المناهج')

@section('content')
@php
    $field = 'h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-800 focus:border-[#0B3D91] focus:outline-none focus:ring-2 focus:ring-[#0B3D91]/20';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-slate-500">لوحة المعلم · عرض كامل بدون اشتراك باقة</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">مكتبة المناهج التفاعلية</h2>
            <p class="mt-1 max-w-2xl text-sm text-slate-500">
                مناهج الأكاديمية للعرض داخل المنصة. لا رفع من هنا — الرفع من لوحة الإدارة. التسجيل وحده لا يفتح هذه الصفحة.
            </p>
        </div>
    </section>

    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
        <i class="fas fa-info-circle ml-1"></i>
        هذه المكتبة للتصفح والعرض فقط. ملفاتك الخاصة لطلابك تُرفع من مكتبة الماتريال أو الفيديو.
    </div>

    <form method="GET" class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-4">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="بحث في عنوان أو مادة" class="{{ $field }}">
        <select name="category_id" class="{{ $field }}">
            <option value="">كل التصنيفات</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected((string) request('category_id') === (string) $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        <select name="language" class="{{ $field }}">
            <option value="">كل اللغات</option>
            <option value="ar" @selected(request('language') === 'ar')>العربية</option>
            <option value="en" @selected(request('language') === 'en')>English</option>
            <option value="fr" @selected(request('language') === 'fr')>Français</option>
        </select>
        <button class="inline-flex h-10 items-center justify-center rounded-xl bg-[#0B3D91] px-4 text-sm font-medium text-white">تصفية</button>
    </form>

    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
        @forelse($items as $item)
            <a href="{{ route('instructor.libraries.curriculum.show', $item) }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:border-[#0B3D91]/40">
                <div class="flex flex-wrap items-center gap-2">
                    @if($item->category)
                        <span class="rounded-full bg-[#0B3D91]/10 px-2 py-0.5 text-[11px] font-semibold text-[#0B3D91]">{{ $item->category->name }}</span>
                    @endif
                    @if($item->subject)
                        <span class="text-xs text-slate-500">{{ $item->subject }}</span>
                    @endif
                </div>
                <div class="mt-2 font-semibold text-slate-900">{{ $item->title }}</div>
                @if($item->description)
                    <p class="mt-1 line-clamp-2 text-sm text-slate-500">{{ \Illuminate\Support\Str::limit($item->description, 110) }}</p>
                @endif
                <div class="mt-3 text-xs font-semibold text-[#0B3D91]">فتح المنهج ←</div>
            </a>
        @empty
            <p class="col-span-full rounded-2xl border border-dashed border-slate-200 px-4 py-12 text-center text-slate-500">لا مناهج تفاعلية منشورة بعد.</p>
        @endforelse
    </div>

    @if(method_exists($items, 'hasPages') && $items->hasPages())
        <div>{{ $items->links() }}</div>
    @endif

    @if(($teachingCourses ?? collect())->isNotEmpty())
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-4 py-3">
                <h3 class="font-semibold text-slate-900">هيكل كورساتك</h3>
                <p class="mt-0.5 text-xs text-slate-500">أقسام الكورسات المسندة لك — مرجع منفصل عن المناهج التفاعلية.</p>
            </div>
            <ul class="divide-y divide-slate-100">
                @foreach($teachingCourses as $course)
                    <li class="flex flex-wrap items-center justify-between gap-2 px-4 py-3">
                        <div>
                            <div class="font-medium text-slate-900">{{ $course->title }}</div>
                            <div class="text-xs text-slate-500">
                                {{ $course->academicSubject?->academicYear?->name ?? '—' }}
                                · {{ $course->academicSubject?->name ?? '—' }}
                                · {{ $course->sections_count }} قسم
                            </div>
                        </div>
                        <a href="{{ route('instructor.libraries.curriculum.course', $course) }}" class="text-sm font-semibold text-[#0B3D91] hover:underline">عرض الهيكل</a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
@endsection
