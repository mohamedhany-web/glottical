@extends('layouts.app')

@section('title', 'بيانات طلب معلم')
@section('header', 'مراجعة طلب التوظيف')

@section('content')
@php
    $videoUrl = $application->introVideoDisplayUrl();
    $embed = $videoUrl ? \App\Helpers\VideoHelper::getEmbedUrl($videoUrl) : null;
    $direct = $videoUrl ? \App\Helpers\VideoHelper::getDirectVideoUrl($videoUrl) : null;
    if (! $direct && $application->introVideoFileUrl()) {
        $direct = $application->introVideoFileUrl();
    }
    $genderLabel = match ($application->gender) {
        'male' => 'ذكر',
        'female' => 'أنثى',
        default => '—',
    };
@endphp
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-800 font-semibold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-rose-800 font-semibold">{{ session('error') }}</div>
    @endif

    @if(session('activated_email'))
        <div class="rounded-2xl border border-emerald-300 bg-emerald-50 p-5 space-y-2">
            <p class="font-bold text-emerald-900">تم تفعيل الملف العام — المعلم يسجّل بنفس الإيميل وكلمة المرور التي أنشأها عند التقديم:</p>
            <p class="font-mono font-bold" dir="ltr">{{ session('activated_email') }}</p>
            @if(session('activated_user_id'))
                <a href="{{ route('admin.users.edit', session('activated_user_id')) }}" class="inline-flex text-sm font-bold text-emerald-900 underline">فتح صفحة المستخدم</a>
            @endif
        </div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.tutor-applications.index') }}" class="text-sm font-semibold text-sky-700 hover:underline">← كل الطلبات</a>
            <a href="{{ route('admin.tutor-applications.hub') }}" class="text-sm font-semibold text-slate-600 hover:underline">لوحة التوظيف</a>
            <a href="{{ $applyUrl }}" target="_blank" class="text-sm font-semibold text-slate-600 hover:underline">لينك التقديم</a>
        </div>
        <span class="rounded-full px-3 py-1 text-xs font-semibold
            @if($application->status === 'activated') bg-emerald-100 text-emerald-700
            @elseif($application->status === 'approved') bg-sky-100 text-sky-700
            @elseif($application->status === 'rejected') bg-rose-100 text-rose-700
            @else bg-amber-100 text-amber-700 @endif">
            {{ $application->statusLabel() }}
        </span>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.25fr_0.75fr]">
        <div class="space-y-5">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 space-y-4">
                <div class="flex items-start gap-4">
                    @if($application->photoUrl())
                        <img src="{{ $application->photoUrl() }}" alt="" class="h-20 w-20 rounded-2xl object-cover border border-slate-200">
                    @endif
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">{{ $application->full_name }}</h2>
                        <p class="text-sky-700 font-semibold">{{ $application->headline }}</p>
                        <p class="text-xs text-slate-500 mt-1">رقم الطلب #{{ $application->id }} · {{ $application->created_at?->format('Y-m-d H:i') }}</p>
                    </div>
                </div>

                <h3 class="font-bold text-slate-800 border-t border-slate-100 pt-4">البيانات الشخصية كاملة</h3>
                <dl class="grid gap-3 text-sm sm:grid-cols-2">
                    <div class="rounded-xl bg-slate-50 px-3 py-2"><dt class="text-slate-500 text-xs">البريد</dt><dd dir="ltr" class="font-semibold">{{ $application->email }}</dd></div>
                    <div class="rounded-xl bg-slate-50 px-3 py-2"><dt class="text-slate-500 text-xs">الجوال / واتساب</dt><dd dir="ltr" class="font-semibold">{{ $application->phone ?: '—' }}</dd></div>
                    <div class="rounded-xl bg-slate-50 px-3 py-2"><dt class="text-slate-500 text-xs">النوع</dt><dd class="font-semibold">{{ $genderLabel }}</dd></div>
                    <div class="rounded-xl bg-slate-50 px-3 py-2"><dt class="text-slate-500 text-xs">الجنسية</dt><dd class="font-semibold">{{ $application->nationality ?: '—' }}</dd></div>
                    <div class="rounded-xl bg-slate-50 px-3 py-2"><dt class="text-slate-500 text-xs">الدولة / المدينة</dt><dd class="font-semibold">{{ $application->city ?: '—' }}</dd></div>
                    <div class="rounded-xl bg-slate-50 px-3 py-2"><dt class="text-slate-500 text-xs">المؤهل</dt><dd class="font-semibold">{{ $application->education ?: '—' }}</dd></div>
                    <div class="rounded-xl bg-slate-50 px-3 py-2"><dt class="text-slate-500 text-xs">سنوات الخبرة</dt><dd class="font-semibold">{{ $application->years_experience ?? '—' }}</dd></div>
                    <div class="rounded-xl bg-slate-50 px-3 py-2"><dt class="text-slate-500 text-xs">راجع بواسطة</dt><dd class="font-semibold">{{ $application->reviewedByUser->name ?? '—' }}</dd></div>
                </dl>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <h3 class="font-bold text-slate-800 mb-2">النبذة / السيرة</h3>
                <p class="text-sm leading-7 text-slate-600 whitespace-pre-line">{{ $application->bio }}</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <h3 class="font-bold text-slate-800 mb-2">الخبرات</h3>
                <p class="text-sm leading-7 text-slate-600 whitespace-pre-line">{{ $application->experience }}</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <h3 class="font-bold text-slate-800 mb-3">الفيديو التعريفي</h3>
                @if($embed)
                    <div class="aspect-video overflow-hidden rounded-xl bg-slate-900">
                        <iframe src="{{ $embed }}" class="h-full w-full" allowfullscreen></iframe>
                    </div>
                @elseif($direct)
                    <video controls class="w-full rounded-xl bg-slate-900" src="{{ $direct }}"></video>
                @elseif($videoUrl)
                    <a href="{{ $videoUrl }}" target="_blank" class="text-sky-700 font-semibold underline" dir="ltr">{{ $videoUrl }}</a>
                @else
                    <p class="text-slate-500 text-sm">لا يوجد فيديو.</p>
                @endif
            </div>

            @if($application->user)
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-sm">
                    <h3 class="font-bold text-emerald-900 mb-2">الحساب المفعّل</h3>
                    <p>المستخدم: <strong>{{ $application->user->name }}</strong></p>
                    <p dir="ltr">{{ $application->user->email }} · {{ $application->user->phone }}</p>
                    <p class="mt-1 text-emerald-800">تفعيل: {{ $application->activated_at?->format('Y-m-d H:i') }} بواسطة {{ $application->activatedByUser->name ?? '—' }}</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <a href="{{ route('admin.users.edit', $application->user) }}" class="rounded-lg bg-white border border-emerald-200 px-3 py-1.5 text-xs font-bold text-emerald-900">إدارة المستخدم</a>
                        <a href="{{ route('public.instructors.show', $application->user) }}" target="_blank" class="rounded-lg bg-white border border-emerald-200 px-3 py-1.5 text-xs font-bold text-emerald-900">الملف العام</a>
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-5">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 space-y-4">
                <h3 class="font-bold text-slate-800">المستندات المرفوعة</h3>
                @foreach([
                    ['label' => 'الصورة الشخصية', 'url' => $application->photoUrl(), 'pdf' => false],
                    ['label' => 'البطاقة / الجواز', 'url' => $application->idDocumentUrl(), 'pdf' => $application->idDocumentIsPdf()],
                    ['label' => 'الشهادة / الإجازة', 'url' => $application->certificateUrl(), 'pdf' => $application->certificateIsPdf()],
                ] as $doc)
                    <div>
                        <p class="text-xs font-semibold text-slate-500 mb-1">{{ $doc['label'] }}</p>
                        @if($doc['url'])
                            @if($doc['pdf'])
                                <a href="{{ $doc['url'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-sky-700">
                                    <i class="fas fa-file-pdf text-rose-600"></i> فتح ملف PDF
                                </a>
                            @else
                                <a href="{{ $doc['url'] }}" target="_blank" rel="noopener" class="block overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                                    <img src="{{ $doc['url'] }}" alt="{{ $doc['label'] }}" class="max-h-48 w-full object-contain" loading="lazy">
                                </a>
                            @endif
                        @else
                            <p class="text-sm text-slate-400">غير مرفوع</p>
                        @endif
                    </div>
                @endforeach
            </div>

            @if($application->status === 'pending')
                <div class="rounded-2xl border border-slate-200 bg-white p-5 space-y-3">
                    <h3 class="font-bold text-slate-800">١) قبول الطلب بعد المراجعة</h3>
                    <p class="text-xs text-slate-500">بعد القبول يمكنك تفعيل حساب المعلم في الخطوة التالية.</p>
                    <form method="POST" action="{{ route('admin.tutor-applications.approve', $application) }}">
                        @csrf
                        <button class="w-full rounded-xl bg-sky-600 py-2.5 text-sm font-bold text-white">قبول الطلب</button>
                    </form>
                    <form method="POST" action="{{ route('admin.tutor-applications.reject', $application) }}" class="space-y-2">
                        @csrf
                        <textarea name="admin_notes" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="سبب الرفض (اختياري)">{{ old('admin_notes', $application->admin_notes) }}</textarea>
                        <button class="w-full rounded-xl bg-rose-600 py-2.5 text-sm font-bold text-white">رفض الطلب</button>
                    </form>
                </div>
            @endif

            @if($application->canActivateAccount())
                <div class="rounded-2xl border-2 border-emerald-300 bg-emerald-50 p-5 space-y-3">
                    <h3 class="font-bold text-emerald-900">٢) تفعيل الملف العام</h3>
                    <p class="text-sm text-emerald-800 leading-6">
                        الحساب موجود مسبقاً (أنشأه المتقدم عند التسجيل). التفعيل يعتمد الملف التعريفي ويظهر المعلم للطلاب.
                    </p>
                    @if($application->user)
                        <p class="text-xs text-emerald-900" dir="ltr">{{ $application->user->email }}</p>
                    @endif
                    <form method="POST" action="{{ route('admin.tutor-applications.activate', $application) }}" onsubmit="return confirm('تأكيد تفعيل الملف العام؟')">
                        @csrf
                        <button class="w-full rounded-xl bg-emerald-600 py-3 text-sm font-bold text-white">
                            <i class="fas fa-user-check ml-1"></i> تفعيل الملف العام الآن
                        </button>
                    </form>
                </div>
            @endif

            @if($application->admin_notes && $application->status === 'rejected')
                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <h3 class="font-bold text-slate-800 mb-2">ملاحظات الرفض</h3>
                    <p class="text-sm text-slate-600 whitespace-pre-line">{{ $application->admin_notes }}</p>
                </div>
            @endif

            @unless($application->isActivated())
                <form method="POST" action="{{ route('admin.tutor-applications.destroy', $application) }}" onsubmit="return confirm('حذف هذا الطلب نهائياً؟')">
                    @csrf
                    @method('DELETE')
                    <button class="w-full rounded-xl border border-rose-200 py-2.5 text-sm font-bold text-rose-700">حذف الطلب</button>
                </form>
            @endunless
        </div>
    </div>
</div>
@endsection
