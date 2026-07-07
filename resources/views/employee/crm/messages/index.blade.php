@extends('layouts.employee')

@section('title', 'تواصل CRM')
@section('header', 'CRM — التواصل')

@section('content')
<div class="space-y-4">
  @include('partials.crm-employee-nav', ['role' => $role])

  @if(session('success'))<div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>@endif
  @if($errors->any())<div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm">{{ $errors->first() }}</div>@endif

  @if($canSend)
  <form method="POST" action="{{ route('employee.crm.messages.store') }}" enctype="multipart/form-data" class="rounded-2xl border bg-white p-5 space-y-3">
    @csrf
    <h3 class="font-bold">رسالة جديدة</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
      <select name="recipient_id" class="rounded-lg border px-3 py-2 text-sm" id="msg-recipient">
        <option value="">رسالة مباشرة لـ...</option>
        @foreach($contacts as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
      </select>
      <select name="crm_group_id" class="rounded-lg border px-3 py-2 text-sm" id="msg-group">
        <option value="">أو قناة فريق...</option>
        @foreach($groups as $g)<option value="{{ $g->id }}">{{ $g->name }}</option>@endforeach
      </select>
      <select name="sales_lead_id" class="rounded-lg border px-3 py-2 text-sm" id="msg-lead">
        <option value="">أو على عميل...</option>
        @foreach($leads as $l)<option value="{{ $l->id }}">{{ $l->name }}</option>@endforeach
      </select>
    </div>
    <textarea name="body" rows="3" class="w-full rounded-lg border px-3 py-2 text-sm" placeholder="اكتب رسالتك..." required></textarea>
    <div class="flex flex-wrap gap-3 items-center">
      <input type="file" name="attachment" class="text-sm">
      <button class="px-4 py-2 rounded-xl bg-cyan-600 text-white font-bold text-sm">إرسال</button>
    </div>
    <p class="text-xs text-gray-500">اختر قناة واحدة فقط: موظف مباشر، فريق، أو عميل محتمل.</p>
  </form>
  @endif

  <div class="rounded-2xl border bg-white divide-y max-h-[32rem] overflow-y-auto">
    @forelse($messages as $msg)
      <div class="p-4 text-sm {{ $msg->recipient_id === auth()->id() && !$msg->read_at ? 'bg-cyan-50' : '' }}">
        <div class="flex justify-between gap-2 mb-1">
          <span class="font-bold">{{ $msg->sender?->name }}</span>
          <span class="text-xs text-gray-500">{{ $msg->created_at?->format('Y-m-d H:i') }}</span>
        </div>
        @if($msg->recipient)<p class="text-xs text-gray-500">إلى: {{ $msg->recipient->name }}</p>@endif
        @if($msg->group)<p class="text-xs text-indigo-600">قناة الفريق: {{ $msg->group->name }}</p>@endif
        @if($msg->lead)<p class="text-xs text-violet-600">عميل: <a href="{{ route('employee.crm.messages.lead', $msg->lead) }}" class="underline">{{ $msg->lead->name }}</a></p>@endif
        <p class="mt-2 whitespace-pre-wrap">{{ $msg->body }}</p>
        @if($msg->attachment_path)
          <a href="{{ route('employee.crm.messages.attachment', $msg->id) }}" class="text-xs text-indigo-600 font-bold mt-1 inline-block">مرفق: {{ $msg->attachment_name }}</a>
        @endif
      </div>
    @empty
      <p class="p-8 text-center text-gray-500">لا توجد رسائل بعد</p>
    @endforelse
  </div>
  <div>{{ $messages->links() }}</div>
</div>
@endsection
