@extends('layouts.admin')

@section('title', 'إدارة المدفوعات - ' . config('app.name'))
@section('header', 'إدارة المدفوعات')

@section('content')
@php
    $statCards = [
        ['label' => 'إجمالي المدفوعات', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'fas fa-money-bill-wave', 'bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'description' => 'كل المدفوعات'],
        ['label' => 'مكتملة', 'value' => number_format($stats['completed'] ?? 0), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'تمت بنجاح'],
        ['label' => 'معلقة', 'value' => number_format($stats['pending'] ?? 0), 'icon' => 'fas fa-hourglass-half', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'description' => 'في انتظار المعالجة'],
        ['label' => 'إجمالي المبلغ', 'value' => number_format($stats['total_amount'] ?? 0, 2) . ' ج.م', 'icon' => 'fas fa-coins', 'bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'description' => 'قيمة المكتملة'],
    ];
    $statusBadges = [
        'completed' => ['label' => 'مكتملة', 'classes' => 'bg-emerald-100 text-emerald-700 border border-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-800/50'],
        'pending' => ['label' => 'معلقة', 'classes' => 'bg-amber-100 text-amber-700 border border-amber-200 dark:bg-amber-900/40 dark:text-amber-200 dark:border-amber-800/50'],
        'processing' => ['label' => 'قيد المعالجة', 'classes' => 'bg-blue-100 text-blue-700 border border-blue-200 dark:bg-sky-900/40 dark:text-sky-300 dark:border-sky-800/50'],
        'failed' => ['label' => 'فاشلة', 'classes' => 'bg-rose-100 text-rose-700 border border-rose-200 dark:bg-rose-900/40 dark:text-rose-300 dark:border-rose-800/50'],
        'cancelled' => ['label' => 'ملغاة', 'classes' => 'bg-slate-100 text-slate-700 border border-slate-200 dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600'],
        'refunded' => ['label' => 'مستردة', 'classes' => 'bg-orange-100 text-orange-700 border border-orange-200 dark:bg-orange-900/40 dark:text-orange-300 dark:border-orange-800/50'],
    ];
    $paymentMethodLabels = [
        'cash' => 'نقدي',
        'card' => 'بطاقة',
        'bank_transfer' => 'تحويل بنكي',
        'online' => 'دفع إلكتروني',
        'wallet' => 'محفظة',
        'other' => 'أخرى',
    ];
@endphp

<div class="space-y-6">
    {{-- 1. الهيدر + إحصائيات --}}
    <section class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-600 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900 dark:text-slate-100">لوحة إدارة المدفوعات</h2>
                    <p class="text-xs text-slate-600 dark:text-slate-400">متابعة المدفوعات وطرق الدفع وحالة المعالجة.</p>
                </div>
            </div>
            <a href="{{ route('admin.payments.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-emerald-500 rounded-xl shadow hover:from-emerald-700 hover:to-emerald-600 transition-all">
                <i class="fas fa-plus"></i>
                إضافة دفعة
            </a>
        </div>
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 p-4">
            @foreach ($statCards as $card)
                <div class="rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800/80 p-4 shadow-sm hover:shadow transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-600 dark:text-slate-400 truncate">{{ $card['label'] }}</p>
                            <p class="text-xl font-black text-slate-900 dark:text-slate-100 truncate">{{ $card['value'] }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-lg {{ $card['bg'] }} dark:ring-1 dark:ring-white/10 flex items-center justify-center {{ $card['text'] }} flex-shrink-0">
                            <i class="{{ $card['icon'] }} text-sm"></i>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 truncate">{{ $card['description'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- 2. البحث والفلترة --}}
    <section class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-900/60">
            <h3 class="text-base font-black text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <i class="fas fa-filter text-emerald-600 dark:text-emerald-400"></i>
                البحث والفلترة
            </h3>
            <p class="text-xs text-slate-600 dark:text-slate-400">حسب الحالة أو رقم الدفعة أو بيانات العميل.</p>
        </div>
        <div class="p-4">
            <form method="GET" action="{{ route('admin.payments.index') }}" id="filterForm" class="flex flex-col gap-3 sm:flex-row sm:items-end sm:flex-wrap">
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">البحث</label>
                    <input type="text" name="search" value="{{ request('search') }}" maxlength="255" placeholder="رقم الدفعة، اسم العميل، هاتف، مرجع" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                </div>
                <div class="w-full sm:w-auto min-w-[160px]">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">الحالة</label>
                    <select name="status" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500">
                        <option value="">جميع الحالات</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فاشلة</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                        <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>مستردة</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition-colors">
                        <i class="fas fa-search"></i>
                        تطبيق
                    </button>
                    @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 dark:border-slate-600 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700" title="مسح الفلتر">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    {{-- 3. قائمة المدفوعات --}}
    <section class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-600 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-slate-900 dark:text-slate-100">المدفوعات</h3>
                <p class="text-xs text-slate-600 dark:text-slate-400">من الأحدث إلى الأقدم.</p>
            </div>
            <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-900/35 px-2.5 py-1 rounded-lg border border-emerald-200 dark:border-emerald-800/50">{{ $payments->total() }} دفعة</span>
        </div>
        <div class="overflow-x-auto">
            <div class="p-3 space-y-1.5">
                @forelse ($payments as $payment)
                    <div class="rounded-lg border border-slate-200 dark:border-slate-600 bg-slate-50/50 dark:bg-slate-900/40 hover:border-emerald-200 dark:hover:border-emerald-700/50 hover:bg-white dark:hover:bg-slate-800/90 transition-all overflow-hidden">
                        <div class="px-3 py-2.5">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                                <div class="w-9 h-9 flex-shrink-0 flex items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400">
                                    <i class="fas fa-money-bill-wave text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0 space-y-1">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <div>
                                            <p class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $payment->payment_number }}</p>
                                            <p class="text-xs text-slate-600 dark:text-slate-400">{{ $payment->user->name ?? '—' }} · {{ $payment->user->phone ?? $payment->user->email ?? '—' }}</p>
                                            @if($payment->reference_number)
                                                <p class="text-xs text-slate-500 dark:text-slate-500">مرجع: {{ $payment->reference_number }}</p>
                                            @endif
                                        </div>
                                        @php $badge = $statusBadges[$payment->status] ?? ['label' => $payment->status, 'classes' => 'bg-slate-100 text-slate-700 border border-slate-200 dark:bg-slate-700 dark:text-slate-200 dark:border-slate-600']; @endphp
                                        <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-semibold {{ $badge['classes'] }}">
                                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                            {{ $badge['label'] }}
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-600 dark:text-slate-400">
                                        <span><i class="fas fa-coins text-emerald-500 dark:text-emerald-400 ml-0.5"></i> <strong>{{ number_format($payment->amount, 2) }}</strong> ج.م</span>
                                        <span><i class="fas fa-credit-card text-blue-500 dark:text-sky-400 ml-0.5"></i> {{ $paymentMethodLabels[$payment->payment_method] ?? $payment->payment_method }}</span>
                                        <span><i class="fas fa-calendar text-slate-500 dark:text-slate-500 ml-0.5"></i> {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y') : $payment->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <a href="{{ route('admin.payments.show', $payment) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-slate-600 text-sm" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800/80 p-10 text-center">
                        <div class="w-14 h-14 mx-auto mb-3 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-400 dark:text-slate-400">
                            <i class="fas fa-money-bill-wave text-xl"></i>
                        </div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">لا توجد مدفوعات</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">لم يتم تسجيل أي مدفوعات أو لا توجد نتائج للفلتر.</p>
                        <a href="{{ route('admin.payments.create') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition-colors">
                            <i class="fas fa-plus"></i>
                            إضافة دفعة
                        </a>
                    </div>
                @endforelse
            </div>
            @if($payments->hasPages())
                <div class="border-t border-slate-200 dark:border-slate-600 px-4 py-3">
                    {{ $payments->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </section>
</div>

<script>
document.getElementById('filterForm') && document.getElementById('filterForm').addEventListener('submit', function() {
    var q = this.querySelector('input[name="search"]');
    if (q) q.value = (q.value || '').replace(/[<>'"&]/g, '').trim();
});
</script>
@endsection
