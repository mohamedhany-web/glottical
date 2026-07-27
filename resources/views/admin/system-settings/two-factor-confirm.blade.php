@extends('layouts.admin')

@section('title', 'تأكيد تفعيل المصادقة الثنائية')
@section('page_title', 'تأكيد تفعيل 2FA')
@section('header', 'تأكيد تفعيل المصادقة الثنائية')

@section('content')
@php
    $input = 'h-14 w-full max-w-xs rounded-xl border border-line bg-surface px-4 text-center text-2xl font-semibold tracking-[0.35em] text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
@endphp

<div class="mx-auto max-w-2xl space-y-5 pb-8">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">إعدادات النظام · المصادقة الثنائية</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">تأكيد تفعيل المصادقة الثنائية</h2>
            <p class="mt-1 text-sm text-muted">أدخل رمز التحقق المرسل إلى بريدك لإكمال التفعيل</p>
        </div>
        <a href="{{ route('admin.system-settings.edit') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
            <i class="fas fa-arrow-right text-xs"></i>
            رجوع للإعدادات
        </a>
    </section>

    <section class="rounded-2xl border border-line bg-surface p-5 shadow-soft sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                <span class="inline-flex size-12 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent">
                    <i class="fas fa-envelope-open-text"></i>
                </span>
                <div class="min-w-0 space-y-2">
                    <p class="text-xs font-medium text-muted">التحقق عبر البريد</p>
                    <p class="text-sm leading-7 text-ink">
                        لتفعيل <span class="font-semibold">إلزام المصادقة الثنائية لحسابات الأدمن</span>، أدخل الرمز المكوّن من 6 أرقام الذي أُرسل إلى بريدك.
                    </p>
                    @if($userEmail)
                        <p class="text-xs font-medium text-muted" dir="ltr">{{ $userEmail }}</p>
                    @endif
                </div>
            </div>
    </section>

    @if(session('success'))
        <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft" role="status">
            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent"><i class="fas fa-check text-sm"></i></span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-danger/20 bg-danger/5 p-4 text-sm text-danger shadow-soft">
            <ul class="list-inside list-disc space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <article class="rounded-2xl border border-line bg-surface p-6 shadow-soft sm:p-8">
        <form method="post" action="{{ route('admin.system-settings.two-factor.confirm.submit') }}" class="space-y-5">
            @csrf
            <div>
                <label for="code" class="mb-1.5 block text-xs font-medium text-muted">رمز التحقق</label>
                <input type="text" name="code" id="code" value="{{ old('code') }}" required maxlength="10" autocomplete="one-time-code" inputmode="numeric"
                       class="{{ $input }}" placeholder="000000" dir="ltr">
                <p class="mt-2 text-xs text-muted">الرمز صالح لمدة 15 دقيقة.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-6 text-sm font-medium text-white transition hover:bg-accent/90">
                    <i class="fas fa-check"></i>
                    تأكيد التفعيل
                </button>
                <a href="{{ route('admin.system-settings.edit') }}" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line bg-surface px-5 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                    إلغاء والعودة
                </a>
            </div>
        </form>

        <div class="mt-6 border-t border-line pt-5">
            <p class="mb-3 text-xs text-muted">لم يصلك الرمز؟</p>
            <form method="post" action="{{ route('admin.system-settings.two-factor.resend') }}" class="inline">
                @csrf
                <button type="submit" class="text-sm font-medium text-accent transition hover:opacity-80">
                    <i class="fas fa-redo ms-1"></i>
                    إعادة إرسال الرمز
                </button>
            </form>
        </div>
    </article>
</div>
@endsection
