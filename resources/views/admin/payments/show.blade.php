@extends('layouts.admin')

@section('title', 'تفاصيل الدفعة - ' . config('app.name'))
@section('header', 'تفاصيل الدفعة')

@section('content')
@php
    $statusBadges = [
        'completed' => ['label' => 'مكتملة', 'classes' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'],
        'pending' => ['label' => 'معلقة', 'classes' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-200'],
        'processing' => ['label' => 'قيد المعالجة', 'classes' => 'bg-blue-100 text-blue-700 dark:bg-sky-900/40 dark:text-sky-300'],
        'failed' => ['label' => 'فاشلة', 'classes' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300'],
        'cancelled' => ['label' => 'ملغاة', 'classes' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200'],
        'refunded' => ['label' => 'مستردة', 'classes' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300'],
    ];
    $paymentMethodLabels = [
        'cash' => ['label' => 'نقدي', 'icon' => 'fas fa-money-bill'],
        'card' => ['label' => 'بطاقة', 'icon' => 'fas fa-credit-card'],
        'bank_transfer' => ['label' => 'تحويل بنكي', 'icon' => 'fas fa-university'],
        'online' => ['label' => 'دفع إلكتروني', 'icon' => 'fas fa-globe'],
        'wallet' => ['label' => 'محفظة', 'icon' => 'fas fa-wallet'],
        'other' => ['label' => 'أخرى', 'icon' => 'fas fa-ellipsis-h'],
    ];
@endphp

<div class="space-y-10">
    <section class="rounded-3xl bg-white/95 dark:bg-slate-800/95 backdrop-blur border border-slate-200 dark:border-slate-600 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 dark:text-slate-500">دفعة رقم</p>
                <h2 class="mt-1 text-2xl font-bold text-slate-900 dark:text-slate-100">#{{ $payment->payment_number }}</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">أُنشئت في {{ $payment->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.payments.edit', $payment) }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 dark:border-slate-600 px-4 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300 transition hover:border-sky-300 dark:hover:border-sky-500 hover:text-sky-600 dark:hover:text-sky-400">
                    <i class="fas fa-edit"></i>
                    تعديل الدفعة
                </a>
                <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 dark:border-slate-600 px-4 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300 transition hover:bg-slate-50 dark:hover:bg-slate-700/80">
                    <i class="fas fa-arrow-right"></i>
                    العودة
                </a>
            </div>
        </div>

        <div class="px-5 py-6 sm:px-8 lg:px-12 space-y-6">
            <!-- معلومات الدفعة الرئيسية -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-600 bg-white/85 dark:bg-slate-900/70 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">معلومات الدفعة</h3>
                    <div class="flex items-center gap-3">
                        @php
                            $statusMeta = $statusBadges[$payment->status] ?? null;
                        @endphp
                        @if($statusMeta)
                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $statusMeta['classes'] }}">
                                <span class="h-2 w-2 rounded-full bg-current"></span>
                                {{ $statusMeta['label'] }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-600">
                            <span class="text-sm text-slate-500 dark:text-slate-400">المبلغ</span>
                            <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">
                                {{ number_format($payment->amount, 2) }} ج.م
                            </span>
                        </div>
                        @if(($payment->gateway_fee_amount ?? 0) > 0 || $payment->net_after_gateway_fee !== null)
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-600">
                            <span class="text-sm text-slate-500 dark:text-slate-400">عمولة البوابة (تقدير)</span>
                            <span class="text-sm font-semibold text-amber-700 dark:text-amber-300">
                                {{ number_format((float) ($payment->gateway_fee_amount ?? 0), 2) }} ج.م
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-600">
                            <span class="text-sm text-slate-500 dark:text-slate-400">صافي بعد العمولة</span>
                            <span class="text-sm font-semibold text-violet-800 dark:text-violet-300">
                                @php
                                    $netShow = $payment->net_after_gateway_fee !== null
                                        ? (float) $payment->net_after_gateway_fee
                                        : round((float) $payment->amount - (float) ($payment->gateway_fee_amount ?? 0), 2);
                                @endphp
                                {{ number_format($netShow, 2) }} ج.م
                            </span>
                        </div>
                        @endif
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-600">
                            <span class="text-sm text-slate-500 dark:text-slate-400">العملة</span>
                            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $payment->currency ?? 'EGP' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-600">
                            <span class="text-sm text-slate-500 dark:text-slate-400">طريقة الدفع</span>
                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200">
                                <i class="{{ $paymentMethodLabels[$payment->payment_method]['icon'] ?? 'fas fa-ellipsis-h' }} text-[10px]"></i>
                                {{ $paymentMethodLabels[$payment->payment_method]['label'] ?? $payment->payment_method }}
                            </span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-600">
                            <span class="text-sm text-slate-500 dark:text-slate-400">تاريخ الإنشاء</span>
                            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $payment->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        @if($payment->paid_at)
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-600">
                            <span class="text-sm text-slate-500 dark:text-slate-400">تاريخ الدفع</span>
                            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $payment->paid_at->format('Y-m-d H:i') }}</span>
                        </div>
                        @endif
                        @if($payment->processedBy)
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-600">
                            <span class="text-sm text-slate-500 dark:text-slate-400">معالج بواسطة</span>
                            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $payment->processedBy->name }}</span>
                        </div>
                        @endif
                        @if($payment->payment_gateway)
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-600">
                            <span class="text-sm text-slate-500 dark:text-slate-400">بوابة الدفع</span>
                            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $payment->payment_gateway }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- معلومات العميل -->
            @if($payment->user)
            <div class="rounded-2xl border border-slate-200 dark:border-slate-600 bg-slate-50/60 dark:bg-slate-900/50 p-6">
                <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100 mb-4">بيانات العميل</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="flex justify-between gap-6">
                        <dt class="text-slate-500 dark:text-slate-400">الاسم</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100">{{ $payment->user->name ?? 'غير معروف' }}</dd>
                    </div>
                    <div class="flex justify-between gap-6">
                        <dt class="text-slate-500 dark:text-slate-400">رقم الهاتف</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100">{{ $payment->user->phone ?? '—' }}</dd>
                    </div>
                    @if($payment->user->email)
                    <div class="flex justify-between gap-6">
                        <dt class="text-slate-500 dark:text-slate-400">البريد الإلكتروني</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100">{{ $payment->user->email }}</dd>
                    </div>
                    @endif
                    <div class="flex justify-between gap-6">
                        <dt class="text-slate-500 dark:text-slate-400">الدور</dt>
                        <dd class="font-semibold text-slate-900 dark:text-slate-100">
                            @if($payment->user->role == 'student') {{ __('admin.student_role_label') }}
                            @elseif($payment->user->role == 'instructor') مدرب
                            @elseif($payment->user->role == 'admin') إداري
                            @else {{ $payment->user->role }}
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
            @endif

            <!-- الروابط المرتبطة -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($payment->invoice)
                <div class="rounded-2xl border border-slate-200 dark:border-slate-600 bg-white/85 dark:bg-slate-900/70 p-6">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100 mb-4">الفاتورة المرتبطة</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500 dark:text-slate-400">رقم الفاتورة</span>
                            <a href="{{ route('admin.invoices.show', $payment->invoice) }}" class="text-sm font-semibold text-sky-600 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-300 transition-colors">
                                {{ $payment->invoice->invoice_number }}
                                <i class="fas fa-external-link-alt text-xs mr-1"></i>
                            </a>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500 dark:text-slate-400">المبلغ الإجمالي</span>
                            <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ number_format($payment->invoice->total_amount, 2) }} ج.م</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500 dark:text-slate-400">الحالة</span>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                                @if($payment->invoice->status == 'paid') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300
                                @elseif($payment->invoice->status == 'pending') bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-200
                                @else bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300
                                @endif">
                                {{ $payment->invoice->status == 'paid' ? 'مدفوعة' : ($payment->invoice->status == 'pending' ? 'معلقة' : 'متأخرة') }}
                            </span>
                        </div>
                    </div>
                </div>
                @endif

                @if($payment->transactions && $payment->transactions->count() > 0)
                <div class="rounded-2xl border border-slate-200 dark:border-slate-600 bg-white/85 dark:bg-slate-900/70 p-6">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100 mb-4">المعاملات المرتبطة</h3>
                    <div class="space-y-2">
                        @foreach($payment->transactions->take(3) as $transaction)
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-600 last:border-0">
                            <a href="{{ route('admin.transactions.show', $transaction) }}" class="text-sm font-semibold text-sky-600 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-300 transition-colors">
                                {{ $transaction->transaction_number ?? 'N/A' }}
                            </a>
                            <span class="text-sm text-slate-500 dark:text-slate-400">{{ number_format($transaction->amount, 2) }} ج.م</span>
                        </div>
                        @endforeach
                        @if($payment->transactions->count() > 3)
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">و {{ $payment->transactions->count() - 3 }} معاملة أخرى</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- معلومات إضافية -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($payment->reference_number)
                <div class="rounded-2xl border border-slate-200 dark:border-slate-600 bg-white/85 dark:bg-slate-900/70 p-6">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100 mb-3">رقم المرجع</h3>
                    <p class="text-sm font-mono text-slate-600 dark:text-slate-300">{{ $payment->reference_number }}</p>
                </div>
                @endif

                @if($payment->transaction_id)
                <div class="rounded-2xl border border-slate-200 dark:border-slate-600 bg-white/85 dark:bg-slate-900/70 p-6">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100 mb-3">رقم المعاملة</h3>
                    <p class="text-sm font-mono text-slate-600 dark:text-slate-300">{{ $payment->transaction_id }}</p>
                </div>
                @endif
            </div>

            <!-- الملاحظات -->
            @if($payment->notes)
            <div class="rounded-2xl border border-slate-200 dark:border-slate-600 bg-white/85 dark:bg-slate-900/70 p-6">
                <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100 mb-3">ملاحظات</h3>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">{{ $payment->notes }}</p>
            </div>
            @endif
        </div>
    </section>
</div>
@endsection

