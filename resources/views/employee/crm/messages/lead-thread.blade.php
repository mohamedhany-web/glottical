@extends('layouts.employee')

@section('title', 'محادثة العميل')
@section('header', 'CRM — '.$lead->name)

@section('content')
<div class="space-y-4 max-w-2xl">
  @include('partials.crm-employee-nav', ['role' => $role])

  <a href="{{ route('employee.crm.leads.show', $lead) }}" class="text-sm text-indigo-600 font-bold">← العودة لملف العميل</a>

  <div class="rounded-2xl border bg-white divide-y max-h-96 overflow-y-auto">
    @forelse($messages as $msg)
      <div class="p-4 text-sm">
        <div class="flex justify-between"><span class="font-bold">{{ $msg->sender?->name }}</span><span class="text-xs text-gray-500">{{ $msg->created_at?->format('Y-m-d H:i') }}</span></div>
        <p class="mt-2 whitespace-pre-wrap">{{ $msg->body }}</p>
      </div>
    @empty
      <p class="p-8 text-center text-gray-500">لا توجد رسائل على هذا العميل</p>
    @endforelse
  </div>

  @if($canSend)
  <form method="POST" action="{{ route('employee.crm.messages.store') }}" class="rounded-2xl border bg-white p-4 space-y-3">
    @csrf
    <input type="hidden" name="sales_lead_id" value="{{ $lead->id }}">
    <textarea name="body" rows="2" class="w-full rounded-lg border px-3 py-2 text-sm" required placeholder="اكتب رسالة للفريق حول هذا العميل"></textarea>
    <button class="px-4 py-2 rounded-xl bg-cyan-600 text-white font-bold text-sm">إرسال</button>
  </form>
  @endif
</div>
@endsection
