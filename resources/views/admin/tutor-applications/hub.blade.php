@extends('layouts.app')

@section('title', 'لوحة التوظيف')
@section('header', 'توظيف المعلمين')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">لوحة التوظيف</h1>
                <p class="mt-1 text-sm text-slate-500">مراجعة طلبات التقديم، عرض بيانات المتقدمين كاملة، ثم تفعيل حساب المعلم.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ $applyUrl }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-[#0B3D91] px-4 py-2.5 text-sm font-bold text-white">
                    <i class="fas fa-external-link-alt"></i> لينك التقديم العام
                </a>
                <button type="button" onclick="navigator.clipboard.writeText(@js($applyUrl))" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i class="fas fa-copy"></i> نسخ اللينك
                </button>
            </div>
        </div>
        <div class="mt-4 rounded-xl bg-slate-50 border border-slate-100 px-4 py-3 text-sm" dir="ltr">
            <span class="font-semibold text-slate-500">Apply URL:</span>
            <a href="{{ $applyUrl }}" class="text-sky-700 font-semibold underline break-all" target="_blank">{{ $applyUrl }}</a>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3">
        @foreach([
            ['label' => 'مسودات', 'value' => $stats['draft'] ?? 0, 'href' => route('admin.tutor-applications.index', ['status' => 'draft']), 'tone' => 'slate'],
            ['label' => 'قيد المراجعة', 'value' => $stats['pending'], 'href' => route('admin.tutor-applications.index', ['status' => 'pending']), 'tone' => 'amber'],
            ['label' => 'بانتظار التفعيل', 'value' => $stats['approved'], 'href' => route('admin.tutor-applications.index', ['status' => 'approved']), 'tone' => 'sky'],
            ['label' => 'مفعّلون', 'value' => $stats['activated'], 'href' => route('admin.tutor-applications.activated'), 'tone' => 'emerald'],
            ['label' => 'مرفوض', 'value' => $stats['rejected'], 'href' => route('admin.tutor-applications.index', ['status' => 'rejected']), 'tone' => 'rose'],
            ['label' => 'معلمون نشطون', 'value' => $stats['instructors'], 'href' => route('admin.tutor-applications.activated'), 'tone' => 'indigo'],
        ] as $card)
            <a href="{{ $card['href'] }}" class="rounded-2xl border border-slate-200 bg-white p-4 hover:border-sky-200 hover:shadow-sm transition">
                <p class="text-xs font-semibold text-slate-500">{{ $card['label'] }}</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">{{ $card['value'] }}</p>
            </a>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h2 class="font-bold text-slate-800">طلبات بانتظار المراجعة</h2>
                <a href="{{ route('admin.tutor-applications.index', ['status' => 'pending']) }}" class="text-sm font-semibold text-sky-700">الكل</a>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse($recentPending as $app)
                    <li class="px-5 py-3 flex items-center justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-800">{{ $app->full_name }}</p>
                            <p class="text-xs text-slate-500">{{ $app->headline }} · {{ $app->created_at?->diffForHumans() }}</p>
                        </div>
                        <a href="{{ route('admin.tutor-applications.show', $app) }}" class="text-sm font-bold text-sky-700">مراجعة</a>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-slate-500 text-sm">لا توجد طلبات جديدة.</li>
                @endforelse
            </ul>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h2 class="font-bold text-slate-800">مقبولون — بانتظار تفعيل الحساب</h2>
                <a href="{{ route('admin.tutor-applications.index', ['status' => 'approved']) }}" class="text-sm font-semibold text-sky-700">الكل</a>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse($awaitingActivation as $app)
                    <li class="px-5 py-3 flex items-center justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-800">{{ $app->full_name }}</p>
                            <p class="text-xs text-slate-500" dir="ltr">{{ $app->email }}</p>
                        </div>
                        <a href="{{ route('admin.tutor-applications.show', $app) }}" class="inline-flex rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white">تفعيل</a>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-slate-500 text-sm">لا يوجد طلبات بانتظار التفعيل.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-5 text-sm text-slate-600 leading-7">
        <p class="font-bold text-slate-800 mb-1">مسار العمل</p>
        <ol class="list-decimal pr-5 space-y-1">
            <li>المتقدم يسجّل <strong>إيميل + كلمة مرور</strong> فيُنشأ حسابه فوراً ويُوجَّه لإكمال البيانات.</li>
            <li>يكمل الصورة والهوية والشهادات والفيديو ويرسل للمراجعة.</li>
            <li>تراجع الإدارة الطلب من <strong>مراجعة الطلبات</strong> ثم <strong>قبول</strong>.</li>
            <li><strong>تفعيل الملف العام</strong> يظهر المعلم للطلاب (بدون إنشاء كلمة مرور جديدة).</li>
        </ol>
    </div>
</div>
@endsection
