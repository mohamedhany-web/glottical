@extends('layouts.admin')

@section('title', 'أوقات الحصة المجانية - ' . config('app.name'))

@section('content')
@php
    $fmtTime = function ($t) {
        if ($t instanceof \Carbon\CarbonInterface) {
            return $t->format('H:i');
        }
        return substr((string) $t, 0, 5);
    };
@endphp
<div class="p-6 bg-gray-50 min-h-screen max-w-5xl mx-auto">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="fas fa-clock text-[#0B3D91] ml-3"></i>
                أوقات الحصة المجانية الأسبوعية
            </h1>
            <p class="text-gray-600">حدد نوافذ التوفر التي تظهر في تقويم الصفحة الرئيسية (مدة الشريحة غالباً 30 دقيقة)</p>
        </div>
        <a href="{{ route('admin.free-trial-bookings.index') }}" class="inline-flex items-center gap-2 text-[#0B3D91] font-bold hover:underline">
            <i class="fas fa-list"></i> قائمة الحجوزات
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 font-semibold">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm">
            <ul class="list-disc mr-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 mb-8">
        <h2 class="text-lg font-black text-gray-900 mb-4">إضافة نافذة جديدة</h2>
        <form method="post" action="{{ route('admin.free-trial-bookings.availability.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">اليوم</label>
                <select name="day_of_week" class="w-full rounded-xl border-gray-300" required>
                    @foreach($dayNames as $num => $label)
                        <option value="{{ $num }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">من</label>
                <input type="time" name="start_time" value="10:00" class="w-full rounded-xl border-gray-300" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">إلى</label>
                <input type="time" name="end_time" value="18:00" class="w-full rounded-xl border-gray-300" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">مدة الشريحة</label>
                <select name="slot_duration_minutes" class="w-full rounded-xl border-gray-300">
                    <option value="30" selected>30 دقيقة</option>
                    <option value="15">15 دقيقة</option>
                    <option value="45">45 دقيقة</option>
                    <option value="60">60 دقيقة</option>
                </select>
            </div>
            <div class="flex items-center gap-2 pb-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" id="new_active" class="rounded border-gray-300 text-amber-500" checked>
                <label for="new_active" class="text-sm font-semibold text-gray-700">نشط</label>
            </div>
            <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#F5B800] text-[#0B3D91] font-black hover:brightness-105">إضافة</button>
        </form>
    </div>

    <div class="space-y-3">
        @forelse($windows as $w)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4">
                <div class="flex flex-col lg:flex-row lg:items-end gap-3">
                    <form method="post" action="{{ route('admin.free-trial-bookings.availability.update', $w) }}" class="flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">اليوم</label>
                            <select name="day_of_week" class="w-full rounded-xl border-gray-300 text-sm">
                                @foreach($dayNames as $num => $label)
                                    <option value="{{ $num }}" @selected((int) $w->day_of_week === $num)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">من</label>
                            <input type="time" name="start_time" value="{{ $fmtTime($w->start_time) }}" class="w-full rounded-xl border-gray-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">إلى</label>
                            <input type="time" name="end_time" value="{{ $fmtTime($w->end_time) }}" class="w-full rounded-xl border-gray-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">المدة</label>
                            <select name="slot_duration_minutes" class="w-full rounded-xl border-gray-300 text-sm">
                                @foreach([15, 30, 45, 60] as $d)
                                    <option value="{{ $d }}" @selected((int) $w->slot_duration_minutes === $d)>{{ $d }} د</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <label class="inline-flex items-center gap-2 text-sm font-semibold">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-amber-500" @checked($w->is_active)>
                                نشط
                            </label>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-[#0B3D91] text-white text-sm font-bold">حفظ</button>
                        </div>
                    </form>
                    <form method="post" action="{{ route('admin.free-trial-bookings.availability.destroy', $w) }}" onsubmit="return confirm('حذف هذه النافذة؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 rounded-xl border border-rose-300 text-rose-700 text-sm font-bold hover:bg-rose-50">حذف</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-dashed border-gray-300 px-4 py-10 text-center text-gray-500">
                لا توجد نوافذ — أضف أول نافذة أعلاه.
            </div>
        @endforelse
    </div>
</div>
@endsection
