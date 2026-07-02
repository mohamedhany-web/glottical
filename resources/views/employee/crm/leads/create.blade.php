@extends('layouts.employee')

@section('title', 'Lead جديد')
@section('header', 'CRM — Lead جديد')

@section('content')
<form method="POST" action="{{ route('employee.crm.leads.store') }}" class="max-w-xl space-y-4 rounded-2xl border bg-white p-6">
  @csrf
  <input name="name" placeholder="الاسم *" class="w-full rounded-lg border px-3 py-2" required value="{{ old('name') }}">
  <input name="email" type="email" placeholder="البريد" class="w-full rounded-lg border px-3 py-2" value="{{ old('email') }}">
  <input name="phone" placeholder="الهاتف" class="w-full rounded-lg border px-3 py-2" value="{{ old('phone') }}">
  <select name="source" class="w-full rounded-lg border px-3 py-2" required>
    @foreach(\App\Models\SalesLead::sourceLabels() as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach
  </select>
  <select name="interested_advanced_course_id" class="w-full rounded-lg border px-3 py-2">
    <option value="">كورس الاهتمام</option>
    @foreach($courses as $c)<option value="{{ $c->id }}">{{ $c->title }}</option>@endforeach
  </select>
  <textarea name="notes" rows="3" class="w-full rounded-lg border px-3 py-2" placeholder="ملاحظات"></textarea>
  <button class="px-5 py-2.5 rounded-xl bg-teal-600 text-white font-bold">حفظ Lead</button>
</form>
@endsection
