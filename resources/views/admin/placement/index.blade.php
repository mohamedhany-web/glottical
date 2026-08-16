@extends('layouts.admin')

@section('title', 'التسكين')
@section('page_title', 'نظام التسكين')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs text-muted">الطلاب والخدمات</p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">التسكين حسب المعلم والطالب والباقة</h2>
            <p class="mt-1 max-w-2xl text-sm text-muted">اختر طالباً لديه باقة نشطة، ثم معلماً له وقت متاح، ثم ثبّت الموعد — للنظام 1:1 أو المجموعات.</p>
        </div>
        <a href="{{ route('admin.placement.create') }}" class="btn-press inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
            <i class="fas fa-user-check"></i> تسكين جديد
        </a>
    </div>

    @include('admin.partials.workflow-guide', [
        'title' => 'ما هو التسكين؟',
        'body' => 'للحصص الفردية ثبّت شهرياً (موعدان أسبوعياً) بدل حجز حصة بحصة — أخف على الطالب والمعلم. الرصيد يُحجز عند التثبيت ويُخصم عند إكمال كل حصة.',
        'steps' => [
            'تأكد أن للطالب باقة نشطة أو رصيد قابل للحجز يكفي عدد الحصص.',
            'حدّث جداول توافر المعلم 1:1.',
            'من «تسكين جديد» اختر تثبيتاً شهرياً أو عدة مواعيد مع نفس المعلم.',
            'راجع الحصص المجدولة من قائمة 1:1، أو احذف التسكين المسجّل من القائمة أدناه.',
        ],
    ])

    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs font-medium text-muted">بانتظار موعد 1:1</p>
            <p class="mt-1 text-2xl font-semibold text-ink">{{ $pendingOneToOne }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs font-medium text-muted">حصص 1:1 قادمة</p>
            <p class="mt-1 text-2xl font-semibold text-ink">{{ $upcomingOneToOne }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs font-medium text-muted">حجوزات مجموعات قادمة</p>
            <p class="mt-1 text-2xl font-semibold text-ink">{{ $upcomingGroups }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs font-medium text-muted">وحدات قابلة للحجز</p>
            <p class="mt-1 text-2xl font-semibold text-ink">{{ $bookableCredits }}</p>
        </article>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <section class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
            <div class="mb-4 flex items-center justify-between gap-2">
                <h3 class="text-base font-semibold text-ink">أحدث تسكين 1:1</h3>
                <a href="{{ route('admin.one-to-one-sessions.index') }}" class="text-xs font-medium text-accent">الكل</a>
            </div>
            <div class="divide-y divide-line">
                @forelse($recentPrivate as $session)
                    <div class="flex items-center justify-between gap-3 py-3 text-sm">
                        <a href="{{ route('admin.one-to-one-sessions.show', $session) }}" class="min-w-0 flex-1 hover:bg-slate-50/60 rounded-lg px-1 py-0.5">
                            <p class="truncate font-medium text-ink">{{ $session->student?->name }} ← {{ $session->instructor?->name }}</p>
                            <p class="text-xs text-muted">{{ $session->statusLabel() }} · {{ optional($session->scheduled_at ?? $session->created_at)->format('Y-m-d H:i') }}</p>
                        </a>
                        @if($session->isOpenPlacement())
                            <form method="POST" action="{{ route('admin.placement.destroy-private', $session) }}" class="shrink-0"
                                  onsubmit="return confirm(@json($session->series_id ? 'حذف التسكين؟ سيتم إلغاء كل الحصص غير المكتملة في هذا التسكين وإرجاع الرصيد المحجوز.' : 'حذف هذا التسكين؟ سيُلغى الموعد ويُعاد الرصيد المحجوز.'));">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-danger hover:underline">حذف التسكين</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-muted">لا تسكين 1:1 بعد</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
            <div class="mb-4 flex items-center justify-between gap-2">
                <h3 class="text-base font-semibold text-ink">أحدث تسكين مجموعات</h3>
                <a href="{{ route('admin.tutoring-group-bookings.index') }}" class="text-xs font-medium text-accent">الكل</a>
            </div>
            <div class="divide-y divide-line">
                @forelse($recentGroups as $booking)
                    <div class="flex items-center justify-between gap-3 py-3 text-sm">
                        <a href="{{ route('admin.tutoring-group-bookings.show', $booking) }}" class="min-w-0 flex-1 hover:bg-slate-50/60 rounded-lg px-1 py-0.5">
                            <p class="truncate font-medium text-ink">{{ $booking->user?->name }} · {{ $booking->tutoringGroup?->title }}</p>
                            <p class="text-xs text-muted">{{ $booking->statusLabel() }} · {{ optional($booking->starts_at)->format('Y-m-d H:i') }}</p>
                        </a>
                        @if($booking->isOpenPlacement())
                            <form method="POST" action="{{ route('admin.placement.destroy-group', $booking) }}" class="shrink-0"
                                  onsubmit="return confirm('حذف تسكين المجموعة؟ سيُلغى الحجز ويُعاد الرصيد المحجوز.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-danger hover:underline">حذف التسكين</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-muted">لا حجوزات مجموعات بعد</p>
                @endforelse
            </div>
        </section>
    </div>

    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
        <a href="{{ route('admin.placement.create', ['mode' => 'private']) }}" class="rounded-2xl border border-accent/25 bg-accent/5 p-4 transition hover:border-accent/50">
            <p class="text-sm font-semibold text-ink"><i class="fas fa-user me-2 text-accent"></i>تسكين 1:1</p>
            <p class="mt-1 text-xs text-muted">طالب + معلم + موعد من جدول التوافر + باقة خاصة</p>
        </a>
        <a href="{{ route('admin.placement.create', ['mode' => 'group']) }}" class="rounded-2xl border border-line bg-surface p-4 transition hover:border-accent/40">
            <p class="text-sm font-semibold text-ink"><i class="fas fa-users me-2 text-accent"></i>تسكين مجموعة / فصل</p>
            <p class="mt-1 text-xs text-muted">طالب + مجموعة + موعد من جدول عمل المدرب + باقة جماعية</p>
        </a>
        @if(Route::has('admin.student-entitlements.create'))
            <a href="{{ route('admin.student-entitlements.create') }}" class="rounded-2xl border border-line bg-surface p-4 transition hover:border-accent/40">
                <p class="text-sm font-semibold text-ink"><i class="fas fa-coins me-2 text-accent"></i>منح باقة / رصيد</p>
                <p class="mt-1 text-xs text-muted">إذا الطالب غير مشترك — امنحه رصيداً ثم اسكنه</p>
            </a>
        @endif
        @if(Route::has('admin.tutoring-group-bookings.index'))
            <a href="{{ route('admin.tutoring-group-bookings.index') }}" class="rounded-2xl border border-line bg-surface p-4 transition hover:border-accent/40">
                <p class="text-sm font-semibold text-ink"><i class="fas fa-calendar-check me-2 text-accent"></i>تسكين الفصول والحجوزات</p>
                <p class="mt-1 text-xs text-muted">متابعة حجوزات المجموعات وإعادة التسكين</p>
            </a>
        @endif
        @if(Route::has('admin.tutoring-groups.index'))
            <a href="{{ route('admin.tutoring-groups.index', 'collective') }}" class="rounded-2xl border border-line bg-surface p-4 transition hover:border-accent/40">
                <p class="text-sm font-semibold text-ink"><i class="fas fa-school me-2 text-accent"></i>فصول المدرسة</p>
                <p class="mt-1 text-xs text-muted">إدارة الفصول الجماعية المرتبطة بالطلاب</p>
            </a>
        @endif
        @if(Route::has('admin.tutor-work-schedules.index'))
            <a href="{{ route('admin.tutor-work-schedules.index') }}" class="rounded-2xl border border-line bg-surface p-4 transition hover:border-accent/40">
                <p class="text-sm font-semibold text-ink"><i class="fas fa-calendar-week me-2 text-accent"></i>جداول عمل المدربين</p>
                <p class="mt-1 text-xs text-muted">أوقات التوافر اللازمة للتسكين</p>
            </a>
        @endif
    </div>
</div>
@endsection
