@extends('layouts.admin')

@section('title', 'تعديل مجموعة')
@section('header', 'CRM — '.$group->name)

@section('content')
<div class="space-y-6 max-w-3xl">
    @if(session('success'))<div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>@endif

    <form method="POST" action="{{ route('admin.crm.groups.update', $group) }}" class="rounded-2xl bg-white border p-6 space-y-4">
        @csrf @method('PUT')
        <input name="name" value="{{ $group->name }}" class="w-full rounded-lg border px-3 py-2" required>
        <select name="team_leader_id" class="w-full rounded-lg border px-3 py-2">
            <option value="">قائد الفريق</option>
            @foreach($leaders as $l)<option value="{{ $l->id }}" @selected($group->team_leader_id==$l->id)>{{ $l->name }}</option>@endforeach
        </select>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked($group->is_active)> نشطة</label>
        <button class="px-5 py-2 rounded-xl bg-indigo-600 text-white font-bold">حفظ</button>
    </form>

    <div class="rounded-2xl bg-white border p-6 space-y-4">
        <h3 class="font-bold">إضافة عضو</h3>
        <form method="POST" action="{{ route('admin.crm.groups.members.store', $group) }}" class="grid sm:grid-cols-3 gap-3">
            @csrf
            <select name="user_id" class="rounded-lg border px-3 py-2 text-sm" required>
                <optgroup label="تسويق">
                    @foreach($marketingUsers as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach
                </optgroup>
                <optgroup label="مبيعات">
                    @foreach($salesUsers as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach
                </optgroup>
            </select>
            <select name="role" class="rounded-lg border px-3 py-2 text-sm">
                <option value="marketing">تسويق</option>
                <option value="sales">مبيعات</option>
            </select>
            <button class="px-4 py-2 rounded-xl bg-sky-600 text-white text-sm font-bold">إضافة</button>
        </form>
        <ul class="divide-y text-sm">
            @foreach($group->members->where('is_active', true) as $m)
                <li class="flex justify-between py-2">
                    <span>{{ $m->user?->name }} ({{ $m->role }})</span>
                    <form method="POST" action="{{ route('admin.crm.groups.members.destroy', [$group, $m]) }}">@csrf @method('DELETE')
                        <button class="text-rose-600 text-xs font-bold">إلغاء</button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
