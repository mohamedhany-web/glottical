@extends('layouts.admin')

@section('title', 'منح رصيد - Glottical')
@section('page_title', 'منح رصيد حصص')

@section('content')
@php $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink'; $labelClass='mb-1.5 block text-xs font-medium text-muted'; @endphp
<div class="mx-auto max-w-xl space-y-5">
    <a href="{{ route('admin.student-entitlements.index') }}" class="text-sm text-accent">← رجوع</a>
    @if(session('error'))
        <div class="rounded-2xl border border-danger/30 bg-danger/5 px-4 py-3 text-sm font-medium text-danger">{{ session('error') }}</div>
    @endif
    <form method="POST" action="{{ route('admin.student-entitlements.store') }}" class="space-y-4 rounded-2xl border border-line bg-surface p-5 shadow-soft">
        @csrf
        <div>
            <label class="{{ $labelClass }}">الطالب *</label>
            <select name="user_id" required class="{{ $fieldClass }}">
                <option value="">اختر…</option>
                @foreach($students as $s)
                    <option value="{{ $s->id }}" @selected(old('user_id')==$s->id)>{{ $s->name }} — {{ $s->email }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $labelClass }}">النطاق *</label>
            <select name="scope" class="{{ $fieldClass }}">
                @foreach($scopes as $k=>$v)
                    <option value="{{ $k }}" @selected(old('scope','global')===$k)>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $labelClass }}">عدد الحصص *</label>
            <input type="number" name="units" min="1" value="{{ old('units', 4) }}" required class="{{ $fieldClass }}">
        </div>
        <div>
            <label class="{{ $labelClass }}">تقييد الرصيد بمجموعة محددة</label>
            <select name="tutoring_group_id" class="{{ $fieldClass }}">
                <option value="">غير مقيد — صالح لكل المجموعات المتوافقة</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}" @selected((string) old('tutoring_group_id') === (string) $group->id)>
                        {{ $group->title }} — {{ $group->typeLabel() }}
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-[11px] text-muted">إذا اخترت مجموعة، لن يستطيع الطالب صرف هذا الرصيد في مجموعة أخرى.</p>
        </div>
        <div>
            <label class="{{ $labelClass }}">الصلاحية بالأيام</label>
            <input type="number" name="duration_days" min="1" value="{{ old('duration_days', 60) }}" class="{{ $fieldClass }}">
        </div>
        <div>
            <label class="{{ $labelClass }}">ملاحظات</label>
            <input type="text" name="notes" value="{{ old('notes') }}" class="{{ $fieldClass }}">
        </div>
        <button class="btn-press h-10 w-full rounded-xl bg-accent text-sm font-medium text-white">منح الرصيد</button>
    </form>
</div>
@endsection
