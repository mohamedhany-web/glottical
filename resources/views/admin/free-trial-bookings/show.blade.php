@extends('layouts.admin')

@section('title', 'تفاصيل حجز الحصة المجانية - ' . config('app.name'))

@section('content')
<div class="p-6 bg-gray-50 min-h-screen max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between gap-3">
        <a href="{{ route('admin.free-trial-bookings.index') }}" class="text-[#0B3D91] font-bold hover:underline">
            <i class="fas fa-arrow-right ml-1"></i> رجوع للقائمة
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 font-semibold">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="h-1.5 bg-gradient-to-l from-[#F5B800] via-[#00A3C4] to-[#0B3D91]"></div>
        <div class="p-6 space-y-6">
            <div>
                <p class="text-xs font-black uppercase tracking-widest text-amber-600 mb-1">حصة تجريبية — {{ $booking->duration_minutes }} دقيقة</p>
                <h1 class="text-2xl font-black text-gray-900">{{ $booking->name }}</h1>
                <p class="text-sm text-gray-500 mt-1">حجز #{{ $booking->id }} · {{ $booking->created_at?->diffForHumans() }}</p>
            </div>

            <div class="grid sm:grid-cols-2 gap-4 text-sm">
                <div class="rounded-xl bg-gray-50 p-4 border border-gray-100">
                    <p class="text-xs font-bold text-gray-500 mb-1">الموعد</p>
                    <p class="font-bold text-gray-900 text-lg">{{ $booking->starts_at?->format('Y-m-d H:i') }}</p>
                    <p class="text-gray-500 mt-1">حتى {{ $booking->ends_at?->format('H:i') }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 p-4 border border-gray-100">
                    <p class="text-xs font-bold text-gray-500 mb-1">التواصل</p>
                    <p class="font-semibold text-gray-900">{{ $booking->email ?: '—' }}</p>
                    <p class="text-gray-700 mt-1">{{ $booking->phone ?: '—' }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 p-4 border border-gray-100 sm:col-span-2">
                    <p class="text-xs font-bold text-gray-500 mb-1">هدف التعلم</p>
                    <p class="text-gray-800">{{ $booking->goal ?: '—' }}</p>
                </div>
                @if($booking->user)
                <div class="rounded-xl bg-gray-50 p-4 border border-gray-100 sm:col-span-2">
                    <p class="text-xs font-bold text-gray-500 mb-1">حساب مسجّل</p>
                    <p class="font-semibold text-gray-900">{{ $booking->user->name }} ({{ $booking->user->email }})</p>
                </div>
                @endif
            </div>

            <form method="post" action="{{ route('admin.free-trial-bookings.update-status', $booking) }}" class="space-y-4 border-t border-gray-100 pt-5">
                @csrf
                @method('PATCH')
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">الحالة</label>
                        <select name="status" class="w-full rounded-xl border-gray-300">
                            <option value="confirmed" @selected($booking->status==='confirmed')>مؤكد</option>
                            <option value="completed" @selected($booking->status==='completed')>مكتمل</option>
                            <option value="cancelled" @selected($booking->status==='cancelled')>ملغي</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">ملاحظات داخلية</label>
                    <textarea name="notes" rows="3" class="w-full rounded-xl border-gray-300" placeholder="ملاحظات للمتابعة…">{{ old('notes', $booking->notes) }}</textarea>
                </div>
                        <div class="flex flex-wrap gap-3 items-center">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#F5B800] text-[#0B3D91] font-black hover:brightness-105">حفظ التحديث</button>
                        </div>
            </form>
            <form method="post" action="{{ route('admin.free-trial-bookings.destroy', $booking) }}" class="mt-3" onsubmit="return confirm('حذف الحجز نهائياً؟');">
                @csrf @method('DELETE')
                <button type="submit" class="px-5 py-2.5 rounded-xl border border-rose-300 text-rose-700 font-bold hover:bg-rose-50">حذف</button>
            </form>
        </div>
    </div>
</div>
@endsection
