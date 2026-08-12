@extends('layouts.admin')

@section('title', 'تحكم المعلمين')
@section('page_title', 'تحكم المعلمين')

@section('content')
@php
    $field = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-muted">الإدارة · المعلمين</p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">مركز تحكم المعلمين</h2>
            <p class="mt-1 text-sm text-muted">ضبط بيانات كل معلم وجدوله والسيشنز والحجوزات من مكان واحد.</p>
        </div>
        @if(Route::has('admin.academy-instructors.index'))
            <a href="{{ route('admin.academy-instructors.index') }}" class="btn-press inline-flex h-9 items-center rounded-xl border border-line px-4 text-sm">مدربو الأكاديمية (توصيف)</a>
        @endif
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-line bg-surface px-4 py-3 text-sm text-ink">{{ session('success') }}</div>
    @endif

    <div class="grid gap-3 sm:grid-cols-3">
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="text-xs text-muted">الإجمالي</div>
            <div class="mt-1 text-2xl font-semibold tabular-nums">{{ $summary['total'] }}</div>
        </div>
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="text-xs text-muted">مفعّل</div>
            <div class="mt-1 text-2xl font-semibold tabular-nums text-success">{{ $summary['active'] }}</div>
        </div>
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="text-xs text-muted">معطّل</div>
            <div class="mt-1 text-2xl font-semibold tabular-nums">{{ $summary['inactive'] }}</div>
        </div>
    </div>

    <form method="GET" class="grid gap-3 rounded-2xl border border-line bg-surface p-4 shadow-soft md:grid-cols-4">
        <input type="search" name="q" value="{{ $search }}" placeholder="بحث بالاسم / البريد / الهاتف" class="{{ $field }} md:col-span-2">
        <select name="status" class="{{ $field }}">
            <option value="all" @selected($status === 'all')>كل الحالات</option>
            <option value="active" @selected($status === 'active')>مفعّل</option>
            <option value="inactive" @selected($status === 'inactive')>معطّل</option>
        </select>
        <button class="btn-press h-11 rounded-xl bg-ink px-4 text-sm font-medium text-white">تصفية</button>
    </form>

    <div class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <table class="min-w-full text-sm">
            <thead class="bg-canvas-muted text-xs text-muted">
                <tr>
                    <th class="px-4 py-3 text-start">المعلم</th>
                    <th class="px-4 py-3 text-start">الحالة</th>
                    <th class="px-4 py-3 text-start">كورسات</th>
                    <th class="px-4 py-3 text-start">1:1 مفتوحة</th>
                    <th class="px-4 py-3 text-start">حجوزات قادمة</th>
                    <th class="px-4 py-3 text-start"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($instructors as $ins)
                    <tr class="border-t border-line hover:bg-canvas/50">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-ink">{{ $ins->name }}</div>
                            <div class="text-xs text-muted">{{ $ins->email ?: '—' }} · {{ $ins->phone ?: 'بدون هاتف' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $ins->is_active ? 'bg-success/10 text-success' : 'bg-canvas-muted text-muted' }}">
                                {{ $ins->is_active ? 'مفعّل' : 'معطّل' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 tabular-nums">{{ (int) ($courseCounts[$ins->id] ?? 0) }}</td>
                        <td class="px-4 py-3 tabular-nums">{{ (int) ($sessionCounts[$ins->id] ?? 0) }}</td>
                        <td class="px-4 py-3 tabular-nums">{{ (int) ($bookingCounts[$ins->id] ?? 0) }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.teachers.show', $ins) }}" class="text-accent font-semibold hover:underline">فتح التحكم</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-muted">لا يوجد معلمون مطابقون.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $instructors->links() }}</div>
</div>
@endsection
