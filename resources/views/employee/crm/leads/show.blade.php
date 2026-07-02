@extends('layouts.employee')

@section('title', 'Lead #'.$lead->id)
@section('header', 'CRM — '.$lead->name)

@section('content')
<div class="space-y-6 max-w-3xl">
  @if(session('success'))<div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>@endif
  @if($errors->any())<div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm">{{ $errors->first() }}</div>@endif

  <a href="{{ route('employee.crm.leads.index') }}" class="text-sm text-sky-600 font-bold">← القائمة</a>

  <div class="rounded-2xl border bg-white p-6 space-y-3">
    <div class="flex justify-between"><h2 class="text-xl font-black">{{ $lead->name }}</h2><span class="text-sm font-bold bg-gray-100 px-2 py-1 rounded">{{ $lead->status_label }}</span></div>
    <p class="text-sm text-gray-600">مالك التسويق: <strong>{{ $lead->marketingOwner?->name }}</strong> (لا يتغير)</p>
    @if($lead->assignedTo)<p class="text-sm">سيلز: <strong>{{ $lead->assignedTo->name }}</strong></p>@endif
    @if($lead->notes)<div class="text-sm whitespace-pre-wrap bg-gray-50 rounded-lg p-3">{{ $lead->notes }}</div>@endif
  </div>

  @if($canEdit)
  <form method="POST" action="{{ route('employee.crm.leads.update', $lead) }}" class="rounded-2xl border bg-white p-6 space-y-3">
    @csrf @method('PUT')
    <input name="name" value="{{ $lead->name }}" class="w-full rounded-lg border px-3 py-2" required>
    <input name="email" value="{{ $lead->email }}" class="w-full rounded-lg border px-3 py-2">
    <input name="phone" value="{{ $lead->phone }}" class="w-full rounded-lg border px-3 py-2">
    <select name="source" class="w-full rounded-lg border px-3 py-2">
      @foreach(\App\Models\SalesLead::sourceLabels() as $k=>$l)<option value="{{ $k }}" @selected($lead->source===$k)>{{ $l }}</option>@endforeach
    </select>
    <button class="px-4 py-2 rounded-xl bg-sky-600 text-white font-bold text-sm">حفظ</button>
  </form>
  @endif

  @if(!empty($nextStatuses) && !$lead->isClosed())
  <form method="POST" action="{{ route('employee.crm.leads.transition', $lead) }}" class="rounded-2xl border bg-white p-6 space-y-3">
    @csrf
    <h3 class="font-bold">تحديث الحالة</h3>
    <select name="status" class="w-full rounded-lg border px-3 py-2" required>
      @foreach($nextStatuses as $st)<option value="{{ $st }}">{{ \App\Models\SalesLead::statusLabels()[$st] }}</option>@endforeach
    </select>
    <textarea name="note" rows="2" class="w-full rounded-lg border px-3 py-2" placeholder="ملاحظة"></textarea>
    <button class="px-4 py-2 rounded-xl bg-violet-600 text-white font-bold text-sm">تحديث</button>
  </form>
  @endif

  @if($canSeePayment && $lead->status === \App\Models\SalesLead::STATUS_PAYMENT_PENDING)
  <form method="POST" action="{{ route('employee.crm.leads.confirm-payment', $lead) }}">
    @csrf
    <button class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold text-sm">تأكيد الدفع (Finance)</button>
  </form>
  @endif

  <form method="POST" action="{{ route('employee.crm.leads.note', $lead) }}" class="rounded-2xl border bg-white p-6 space-y-3">
    @csrf
    <textarea name="note" rows="2" class="w-full rounded-lg border px-3 py-2" placeholder="إضافة ملاحظة" required></textarea>
    <button class="px-4 py-2 rounded-xl bg-slate-700 text-white font-bold text-sm">إضافة ملاحظة</button>
  </form>
</div>
@endsection
