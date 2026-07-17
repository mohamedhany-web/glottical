@extends('layouts.admin')

@section('title', 'حجوزات الحصة المجانية - ' . config('app.name'))

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="fas fa-calendar-check text-amber-500 ml-3"></i>
                حجوزات الحصة المجانية
            </h1>
            <p class="text-gray-600">مراجعة حجوزات تقييم المستوى (30 دقيقة) من الصفحة الرئيسية</p>
        </div>
        <a href="{{ route('admin.free-trial-bookings.availability') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#0B3D91] text-white font-bold hover:bg-[#072a66] transition shadow-lg">
            <i class="fas fa-clock"></i>
            ضبط أوقات الأسبوع
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 font-semibold">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
        @foreach([
            ['label' => 'الإجمالي', 'value' => $stats['total'], 'color' => 'sky', 'icon' => 'fa-inbox'],
            ['label' => 'مؤكد', 'value' => $stats['confirmed'], 'color' => 'emerald', 'icon' => 'fa-check-circle'],
            ['label' => 'قادم', 'value' => $stats['upcoming'], 'color' => 'amber', 'icon' => 'fa-hourglass-half'],
            ['label' => 'اليوم', 'value' => $stats['today'], 'color' => 'indigo', 'icon' => 'fa-calendar-day'],
            ['label' => 'مكتمل', 'value' => $stats['completed'], 'color' => 'blue', 'icon' => 'fa-flag-checkered'],
            ['label' => 'ملغي', 'value' => $stats['cancelled'], 'color' => 'rose', 'icon' => 'fa-ban'],
        ] as $card)
            <div class="bg-white rounded-2xl shadow p-4 border-r-4 border-{{ $card['color'] }}-500">
                <p class="text-xs text-gray-500 font-medium mb-1">{{ $card['label'] }}</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($card['value']) }}</p>
                <i class="fas {{ $card['icon'] }} text-{{ $card['color'] }}-500 mt-2"></i>
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 border border-gray-200">
        <form method="get" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 mb-1">بحث</label>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="اسم / بريد / هاتف / هدف"
                       class="w-full rounded-xl border-gray-300 focus:ring-amber-400 focus:border-amber-400">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">الحالة</label>
                <select name="status" class="w-full rounded-xl border-gray-300">
                    <option value="">الكل</option>
                    <option value="confirmed" @selected(request('status')==='confirmed')>مؤكد</option>
                    <option value="completed" @selected(request('status')==='completed')>مكتمل</option>
                    <option value="cancelled" @selected(request('status')==='cancelled')>ملغي</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">من تاريخ</label>
                <input type="date" name="from" value="{{ request('from') }}" class="w-full rounded-xl border-gray-300">
            </div>
            <div class="flex gap-2">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-gray-500 mb-1">إلى</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="w-full rounded-xl border-gray-300">
                </div>
                <button type="submit" class="self-end px-4 py-2.5 rounded-xl bg-amber-400 text-[#0B3D91] font-bold hover:brightness-105">تصفية</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-right">#</th>
                        <th class="px-4 py-3 text-right">الطالب</th>
                        <th class="px-4 py-3 text-right">التواصل</th>
                        <th class="px-4 py-3 text-right">الموعد</th>
                        <th class="px-4 py-3 text-right">المدة</th>
                        <th class="px-4 py-3 text-right">الحالة</th>
                        <th class="px-4 py-3 text-right">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bookings as $b)
                        @php
                            $statusCls = match($b->status) {
                                'completed' => 'bg-blue-100 text-blue-800',
                                'cancelled' => 'bg-rose-100 text-rose-800',
                                default => 'bg-emerald-100 text-emerald-800',
                            };
                            $statusLabel = match($b->status) {
                                'completed' => 'مكتمل',
                                'cancelled' => 'ملغي',
                                default => 'مؤكد',
                            };
                        @endphp
                        <tr class="hover:bg-amber-50/40">
                            <td class="px-4 py-3 font-bold text-gray-500">{{ $b->id }}</td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-900">{{ $b->name }}</div>
                                @if($b->goal)
                                    <div class="text-xs text-gray-500 mt-0.5">{{ \Illuminate\Support\Str::limit($b->goal, 40) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                @if($b->email)<div><i class="fas fa-envelope text-gray-400 ml-1"></i>{{ $b->email }}</div>@endif
                                @if($b->phone)<div><i class="fas fa-phone text-gray-400 ml-1"></i>{{ $b->phone }}</div>@endif
                            </td>
                            <td class="px-4 py-3 font-semibold text-gray-900 whitespace-nowrap">
                                {{ $b->starts_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-4 py-3">{{ $b->duration_minutes }} د</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold {{ $statusCls }}">{{ $statusLabel }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.free-trial-bookings.show', $b) }}" class="text-[#0B3D91] font-bold hover:underline">عرض</a>
                                    <form method="post" action="{{ route('admin.free-trial-bookings.destroy', $b) }}" onsubmit="return confirm('حذف هذا الحجز؟');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-rose-600 font-bold hover:underline">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-500">لا توجد حجوزات بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bookings->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">{{ $bookings->links() }}</div>
        @endif
    </div>
</div>
@endsection
