@extends('layouts.employee')

@section('title', 'طلب #'.$order->id)
@section('header', 'طلب مبيعات #'.$order->id)

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-900 px-4 py-3 text-sm font-medium">{{ session('error') }}</div>
    @endif

    <div class="flex flex-wrap gap-3">
        <a href="{{ route('employee.sales.orders.index', request()->query()) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-right"></i> رجوع للقائمة
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">تفاصيل الطلب</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500 font-medium">الحالة</dt>
                        <dd class="font-bold mt-1">
                            @if($order->status === \App\Models\Order::STATUS_PENDING)
                                <span class="text-amber-700">قيد المراجعة</span>
                            @elseif($order->status === \App\Models\Order::STATUS_APPROVED)
                                <span class="text-emerald-700">معتمد</span>
                            @else
                                <span class="text-rose-700">مرفوض</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">المبلغ</dt>
                        <dd class="font-black text-lg tabular-nums mt-1">{{ number_format((float) $order->amount, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">الكورس</dt>
                        <dd class="font-semibold text-gray-900 mt-1">{{ $order->course?->title ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">طريقة الدفع</dt>
                        <dd class="font-semibold text-gray-900 mt-1">{{ $order->payment_method ?? '—' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500 font-medium">العميل</dt>
                        <dd class="font-semibold text-gray-900 mt-1">{{ $order->user?->name }} — {{ $order->user?->email }}</dd>
                        @if($order->user?->phone)
                            <dd class="text-gray-600 text-sm mt-1">{{ $order->user->phone }}</dd>
                        @endif
                    </div>
                    @if($order->notes)
                        <div class="sm:col-span-2">
                            <dt class="text-gray-500 font-medium">ملاحظات الطلب (من العميل)</dt>
                            <dd class="text-gray-800 mt-1 whitespace-pre-wrap">{{ $order->notes }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">سجل ملاحظات المبيعات</h2>
                <div class="space-y-4 max-h-96 overflow-y-auto mb-6">
                    @forelse($order->salesNotes as $note)
                        <div class="border-r-4 border-emerald-500 pr-4 py-2 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $note->body }}</p>
                            <p class="text-xs text-gray-500 mt-2">{{ $note->user?->name }} — {{ $note->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">لا توجد ملاحظات بعد.</p>
                    @endforelse
                </div>
                <form action="{{ route('employee.sales.orders.notes.store', $order) }}" method="POST" class="space-y-3">
                    @csrf
                    <label class="block text-sm font-medium text-gray-700">إضافة ملاحظة داخلية</label>
                    <textarea name="body" rows="3" required maxlength="5000" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500" placeholder="مكالمة، واتساب، تذكير، اعتراض…"></textarea>
                    @error('body')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                    <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">حفظ الملاحظة</button>
                </form>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-gradient-to-br from-emerald-50 to-white rounded-xl border border-emerald-200 p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-3">المندوب</h3>
                @if($order->salesOwner)
                    <p class="text-emerald-900 font-semibold">{{ $order->salesOwner->name }}</p>
                    @if((int) $order->sales_owner_id === (int) auth()->id())
                        <p class="text-xs text-emerald-700 mt-2">أنت مسؤول عن هذا الطلب.</p>
                    @endif
                @else
                    <p class="text-gray-600 text-sm mb-4">لا يوجد مندوب مسند.</p>
                @endif
                @if(!$order->sales_owner_id || (int) $order->sales_owner_id === (int) auth()->id())
                    <form action="{{ route('employee.sales.orders.claim', $order) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">
                            {{ $order->sales_owner_id && (int) $order->sales_owner_id === (int) auth()->id() ? 'تحديث وقت المتابعة' : 'استلام الطلب كمندوب' }}
                        </button>
                    </form>
                @else
                    <p class="text-xs text-amber-800 mt-2">مسند لمندوب آخر؛ يمكنك فقط عرض التفاصيل وإضافة ملاحظات للفريق.</p>
                @endif
                @if($order->sales_contacted_at)
                    <p class="text-xs text-gray-500 mt-3">آخر نشاط مسجل: {{ optional($order->sales_contacted_at)->format('Y-m-d H:i') ?? $order->sales_contacted_at }}</p>
                @endif
            </div>

            <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 text-xs text-slate-600">
                <p class="font-semibold text-slate-800 mb-1">للإدارة</p>
                <p>الموافقة على الدفع والتسجيل تتم من <strong>لوحة الإدارة → الطلبات</strong>. شارك الملاحظات هنا ليتابعها الفريق.</p>
            </div>
        </div>
    </div>
</div>
@endsection
