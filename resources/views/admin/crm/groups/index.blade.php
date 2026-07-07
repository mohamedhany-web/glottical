@extends('layouts.admin')

@section('title', 'مجموعات CRM')
@section('header', 'CRM — المجموعات')

@section('content')
<div class="space-y-4">
    @include('partials.crm-admin-nav')
    <div class="flex justify-end">
        <a href="{{ route('admin.crm.groups.create') }}" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-bold">مجموعة جديدة</a>
    </div>
    <div class="grid gap-4">
        @foreach($groups as $group)
            <div class="rounded-2xl bg-white border p-5">
                <div class="flex justify-between">
                    <div>
                        <h3 class="font-bold text-lg">{{ $group->name }}</h3>
                        <p class="text-sm text-slate-500">قائد الفريق: {{ $group->teamLeader?->name ?? '—' }} — {{ $group->leads_count }} Lead</p>
                    </div>
                    <a href="{{ route('admin.crm.groups.edit', $group) }}" class="text-sky-600 text-sm font-bold">إدارة</a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
