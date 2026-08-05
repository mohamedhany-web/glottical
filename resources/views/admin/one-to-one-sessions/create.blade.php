@extends('layouts.admin')

@section('title', 'تسكين حصة خاصة')
@section('page_title', 'تسكين حصة 1:1')

@section('content')
@php
    $field = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $label = 'mb-1.5 block text-xs font-medium text-muted';
@endphp
<div class="mx-auto max-w-3xl space-y-5">
    <div class="flex items-end justify-between gap-3">
        <div>
            <p class="text-xs text-muted">الحصص الخاصة · تسكين يدوي</p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">تسكين طالب مع معلم 1:1</h2>
            <p class="mt-1 text-sm text-muted">يعمل مباشرة من رصيد «حصص خاصة» أو الرصيد العام ولا يشترط تسجيل الطالب في كورس.</p>
        </div>
        <a href="{{ route('admin.one-to-one-sessions.index') }}" class="text-sm font-medium text-accent">رجوع</a>
    </div>

    @if(session('error'))
        <div class="rounded-xl border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.one-to-one-sessions.store') }}" class="space-y-5 rounded-2xl border border-line bg-surface p-5 shadow-soft">
        @csrf
        <div>
            <label class="{{ $label }}">الطالب والرصيد *</label>
            <select name="student_service_entitlement_id" required class="{{ $field }}">
                <option value="">اختر…</option>
                @foreach($entitlements as $entitlement)
                    <option value="{{ $entitlement->id }}" @selected((string) old('student_service_entitlement_id') === (string) $entitlement->id)>
                        {{ $entitlement->user?->name }} — {{ \App\Services\StudentEntitlementService::bookableUnitsLeft($entitlement) }} حصة متاحة
                        — {{ \App\Models\ServicePackage::scopes()[$entitlement->scope] ?? $entitlement->scope }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $label }}">المعلم *</label>
            <select name="instructor_id" required class="{{ $field }}">
                <option value="">اختر…</option>
                @foreach($instructors as $instructor)
                    <option value="{{ $instructor->id }}" @selected((string) old('instructor_id') === (string) $instructor->id)>{{ $instructor->name }} — {{ $instructor->email }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="{{ $label }}">الموعد (اختياري)</label>
            <input type="datetime-local" name="scheduled_at" min="{{ now()->format('Y-m-d\TH:i') }}" value="{{ old('scheduled_at') }}" class="{{ $field }}" dir="ltr">
            <p class="mt-1 text-[11px] text-muted">اتركه فارغاً ليختار الطالب أو المعلم الموعد لاحقاً. عند تحديده يتحقق النظام من جدول المعلم وينشئ غرفة Live.</p>
        </div>
        <div>
            <label class="{{ $label }}">ملاحظات داخلية</label>
            <textarea name="notes" rows="3" class="w-full rounded-xl border border-line px-4 py-3 text-sm">{{ old('notes') }}</textarea>
        </div>
        <button class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-accent text-sm font-medium text-white">
            <i class="fas fa-user-check"></i> إنشاء التسكين
        </button>
    </form>
</div>
@endsection
