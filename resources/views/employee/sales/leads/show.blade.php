@extends('layouts.employee')

@section('title', 'Lead #' . $salesLead->id)
@section('header', 'عميل محتمل #' . $salesLead->id)

@section('content')
@php
    $open = !$salesLead->isConverted() && !$salesLead->isLost();
@endphp
<div class="space-y-6 max-w-4xl">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('employee.sales.leads.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-right"></i> القائمة
        </a>
        <div class="flex flex-wrap gap-2">
            @if($open)
                <a href="{{ route('employee.sales.leads.edit', $salesLead) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-800 hover:bg-gray-900 text-white text-sm font-bold">تعديل</a>
            @endif
            @if($open && ! $salesLead->assigned_to)
                <form method="POST" action="{{ route('employee.sales.leads.assign-me', $salesLead) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-2 rounded-lg bg-teal-100 hover:bg-teal-200 text-teal-900 text-sm font-bold">استلام العميل</button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-900 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="text-xl font-black text-gray-900">{{ $salesLead->name }}</h2>
                @if($salesLead->company)<p class="text-sm text-gray-600">{{ $salesLead->company }}</p>@endif
            </div>
            <div>
                @if($salesLead->status === \App\Models\SalesLead::STATUS_CONVERTED)
                    <span class="rounded-full bg-emerald-100 text-emerald-800 px-3 py-1 text-sm font-bold">{{ $salesLead->status_label }}</span>
                @elseif($salesLead->status === \App\Models\SalesLead::STATUS_LOST)
                    <span class="rounded-full bg-rose-100 text-rose-800 px-3 py-1 text-sm font-bold">{{ $salesLead->status_label }}</span>
                @else
                    <span class="rounded-full bg-amber-100 text-amber-800 px-3 py-1 text-sm font-bold">{{ $salesLead->status_label }}</span>
                @endif
            </div>
        </div>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div><dt class="text-gray-500 font-semibold">البريد</dt><dd class="text-gray-900">{{ $salesLead->email ?: '—' }}</dd></div>
            <div><dt class="text-gray-500 font-semibold">الهاتف</dt><dd class="text-gray-900">{{ $salesLead->phone ?: '—' }}</dd></div>
            <div><dt class="text-gray-500 font-semibold">المصدر</dt><dd class="text-gray-900">{{ $salesLead->source_label }}</dd></div>
            <div><dt class="text-gray-500 font-semibold">كورس الاهتمام</dt><dd class="text-gray-900">{{ $salesLead->interestedCourse?->title ?? '—' }}</dd></div>
            <div><dt class="text-gray-500 font-semibold">أنشأه</dt><dd class="text-gray-900">{{ $salesLead->creator?->name ?? '—' }}</dd></div>
            <div><dt class="text-gray-500 font-semibold">المسؤول الحالي</dt><dd class="text-gray-900">{{ $salesLead->assignedTo?->name ?? '—' }}</dd></div>
        </dl>
        @if($salesLead->notes)
            <div>
                <p class="text-xs font-semibold text-gray-500 mb-1">ملاحظات</p>
                <div class="rounded-lg bg-gray-50 border border-gray-100 p-3 text-sm text-gray-800 whitespace-pre-wrap">{{ $salesLead->notes }}</div>
            </div>
        @endif
        @if($salesLead->isConverted())
            <div class="rounded-lg border border-emerald-200 bg-emerald-50/50 p-4 text-sm space-y-2">
                <p class="font-bold text-emerald-900">بيانات التحويل</p>
                @if($salesLead->converted_at)<p class="text-emerald-800">تاريخ: {{ $salesLead->converted_at->format('Y-m-d H:i') }}</p>@endif
                @if($salesLead->linkedUser)
                    <p>مستخدم منصة: <span class="font-bold text-gray-900">{{ $salesLead->linkedUser->name }}</span>
                        @if($salesLead->linkedUser->email)<span class="text-gray-600"> — {{ $salesLead->linkedUser->email }}</span>@endif
                    </p>
                @endif
                @if($salesLead->convertedOrder)
                    <p>طلب مرتبط: <a href="{{ route('employee.sales.orders.show', $salesLead->convertedOrder) }}" class="font-bold text-teal-700 hover:underline">#{{ $salesLead->convertedOrder->id }}</a>
                        — {{ $salesLead->convertedOrder->course?->title ?? '—' }}</p>
                @endif
            </div>
        @endif
        @if($salesLead->isLost() && $salesLead->lost_reason)
            <div class="rounded-lg border border-rose-200 bg-rose-50/50 p-4 text-sm">
                <p class="font-bold text-rose-900 mb-1">سبب الخسارة</p>
                <p class="text-rose-800 whitespace-pre-wrap">{{ $salesLead->lost_reason }}</p>
            </div>
        @endif
    </div>

    @if($open)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6" x-data="{ mode: {{ json_encode(old('mode', 'order')) }} }">
            <h3 class="text-base font-bold text-gray-900 mb-4">تحويل إلى عميل فعلي</h3>
            <form method="POST" action="{{ route('employee.sales.leads.convert', $salesLead) }}" class="space-y-4">
                @csrf
                <div class="space-y-2 text-sm">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="mode" value="order" x-model="mode" class="text-teal-600 focus:ring-teal-500">
                        <span>ربط بطلب موجود (رقم الطلب)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="mode" value="user" x-model="mode" class="text-teal-600 focus:ring-teal-500">
                        <span>ربط بمستخدم مسجّل (معرّف المستخدم)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="mode" value="manual" x-model="mode" class="text-teal-600 focus:ring-teal-500">
                        <span>تحويل يدوي (بدون ربط طلب/مستخدم)</span>
                    </label>
                </div>
                <div x-show="mode === 'order'" x-cloak class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-600">رقم الطلب</label>
                    <input type="number" name="order_id" value="{{ old('order_id') }}" min="1" class="w-full max-w-xs rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="مثال: 120">
                    @error('order_id')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div x-show="mode === 'user'" x-cloak class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-600">معرّف المستخدم (من لوحة الإدارة)</label>
                    <input type="number" name="user_id" value="{{ old('user_id') }}" min="1" class="w-full max-w-xs rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    @error('user_id')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">ملاحظة على التحويل (اختياري)</label>
                    <textarea name="conversion_note" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ old('conversion_note') }}</textarea>
                </div>
                @error('mode')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">تسجيل التحويل</button>
            </form>
        </div>

        <div class="bg-white rounded-xl border border-rose-200 shadow-sm p-6">
            <h3 class="text-base font-bold text-rose-900 mb-3">تسجيل خسارة</h3>
            <form method="POST" action="{{ route('employee.sales.leads.lost', $salesLead) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">السبب <span class="text-rose-600">*</span></label>
                    <textarea name="lost_reason" required rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ old('lost_reason') }}</textarea>
                    @error('lost_reason')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold">حفظ كخاسر</button>
            </form>
        </div>
    @endif
</div>
@endsection
