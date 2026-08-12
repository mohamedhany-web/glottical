@extends('layouts.app')

@section('title', 'تسعير الكورس')
@section('header', 'تسعير الكورس')

@section('content')
@php
    $field = 'h-10 w-full rounded-xl border border-slate-200 px-3 text-sm';
@endphp
<div class="space-y-5 max-w-2xl">
    <section>
        <p class="text-xs text-slate-500">{{ $course->title }}</p>
        <h2 class="mt-1 text-2xl font-semibold">أسعار الكورس المسجّل</h2>
        <p class="mt-1 text-sm text-slate-500">الجنيه للداخل — الدولار للخارج. الطالب يختار العملة عند الدفع.</p>
    </section>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('instructor.courses.pricing.update', $course) }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-4">
        @csrf
        @method('PUT')
        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label class="text-xs text-slate-500">السعر بالجنيه (EGP)</label>
                <input type="number" step="0.01" min="0" name="price_egp" value="{{ old('price_egp', $course->price_egp ?? $course->price) }}" class="{{ $field }}">
            </div>
            <div>
                <label class="text-xs text-slate-500">بعد الخصم (EGP)</label>
                <input type="number" step="0.01" min="0" name="price_egp_after_discount" value="{{ old('price_egp_after_discount', $course->price_egp_after_discount ?? $course->price_after_discount) }}" class="{{ $field }}">
            </div>
            <div>
                <label class="text-xs text-slate-500">السعر بالدولار (USD)</label>
                <input type="number" step="0.01" min="0" name="price_usd" value="{{ old('price_usd', $course->price_usd) }}" class="{{ $field }}">
            </div>
            <div>
                <label class="text-xs text-slate-500">بعد الخصم (USD)</label>
                <input type="number" step="0.01" min="0" name="price_usd_after_discount" value="{{ old('price_usd_after_discount', $course->price_usd_after_discount) }}" class="{{ $field }}">
            </div>
        </div>
        <button class="h-10 rounded-xl bg-[#0B3D91] px-5 text-sm font-semibold text-white">حفظ الأسعار</button>
    </form>
</div>
@endsection
