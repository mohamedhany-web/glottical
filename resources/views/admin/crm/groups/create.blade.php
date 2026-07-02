@extends('layouts.admin')

@section('title', 'مجموعة CRM')
@section('header', 'CRM — مجموعة جديدة')

@section('content')
<form method="POST" action="{{ route('admin.crm.groups.store') }}" class="max-w-lg space-y-4 rounded-2xl bg-white border p-6">
    @csrf
    <div>
        <label class="block text-sm font-semibold mb-1">اسم المجموعة</label>
        <input name="name" class="w-full rounded-lg border px-3 py-2" required>
    </div>
    <div>
        <label class="block text-sm font-semibold mb-1">قائد الفريق</label>
        <select name="team_leader_id" class="w-full rounded-lg border px-3 py-2">
            <option value="">—</option>
            @foreach($leaders as $l)<option value="{{ $l->id }}">{{ $l->name }}</option>@endforeach
        </select>
    </div>
    <button class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-bold">حفظ</button>
</form>
@endsection
