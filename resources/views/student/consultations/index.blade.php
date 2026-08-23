@extends('layouts.student-timeline')

@section('title', 'طلبات الاستشارة')

@section('content')
<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 pb-10">
    <div class="flex flex-wrap items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('dashboard') }}" class="hover:text-sky-600 dark:hover:text-sky-400 font-medium">{{ __('auth.dashboard') }}</a>
        <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
        <span class="text-gray-900 dark:text-gray-200 font-semibold">طلبات الاستشارة</span>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">طلبات الاستشارة</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">الدفع على حسابات المنصة، مراجعة الإدارة، ثم الموعد</p>
        </div>
        <a href="{{ route('public.instructors.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold shadow-sm">تصفح المدربين</a>
    </div>

    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/50 text-xs text-gray-600 dark:text-gray-400 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-right">المدرب</th>
                        <th class="px-4 py-3 text-right">المبلغ</th>
                        <th class="px-4 py-3 text-right">الحالة</th>
                        <th class="px-4 py-3 text-right">الموعد</th>
                        <th class="px-4 py-3 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($requests as $r)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/30">
                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ $r->instructor->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ number_format($r->price_amount, 2) }} {{ __('public.currency_egp') }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-1 rounded-md bg-gray-100 dark:bg-gray-700 text-xs font-medium">{{ $r->statusLabel() }}</span></td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $r->scheduled_at?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td class="px-4 py-3"><a href="{{ route('consultations.show', $r) }}" class="text-sky-600 dark:text-sky-400 font-semibold hover:underline">تفاصيل</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">لا توجد طلبات بعد</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">{{ $requests->links() }}</div>
    </div>
</div>
@endsection
