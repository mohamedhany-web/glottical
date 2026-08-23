@extends('layouts.student-timeline')

@section('title', __('student.dashboard_title'))

@push('styles')
<style>
    .kids-home {
        --k-blue: #0B3D91;
        --k-gold: #F5B800;
        --k-sky: #7EC8FF;
        --k-mint: #7AD9B0;
        --k-coral: #FF8FAB;
        --k-cream: #FFF8E8;
        --k-ink: #1A2744;
        --k-muted: #6B7A99;
    }
    .kids-hero {
        border-radius: 28px;
        background:
            radial-gradient(circle at 12% 20%, rgba(126,200,255,.55), transparent 42%),
            radial-gradient(circle at 88% 10%, rgba(245,184,0,.45), transparent 36%),
            radial-gradient(circle at 70% 90%, rgba(122,217,176,.35), transparent 40%),
            linear-gradient(135deg, #fff 0%, #F4F7FC 100%);
        border: 2px solid #E3ECFF;
        padding: 1.25rem 1.35rem 1.4rem;
        position: relative;
        overflow: hidden;
    }
    .kids-hero__cloud {
        position: absolute; border-radius: 999px; background: rgba(255,255,255,.7);
        filter: blur(1px); pointer-events: none;
    }
    .kids-week {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: .55rem;
    }
    @media (max-width: 900px) {
        .kids-week { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    .kids-day {
        border-radius: 22px;
        border: 2px solid #E8EEF8;
        background: #fff;
        min-height: 150px;
        padding: .7rem .65rem .85rem;
        transition: transform .18s ease, box-shadow .18s ease;
    }
    .kids-day.is-today {
        border-color: var(--k-gold);
        box-shadow: 0 12px 28px -14px rgba(245,184,0,.55);
        background: linear-gradient(180deg, #FFF9E8, #fff);
        transform: translateY(-2px);
    }
    .kids-day__badge {
        display: inline-flex; align-items: center; justify-content: center;
        width: 2rem; height: 2rem; border-radius: 999px;
        font-weight: 900; font-size: .85rem; color: #fff;
        background: var(--k-blue);
    }
    .kids-day.is-today .kids-day__badge { background: var(--k-gold); color: #072A66; }
    .kids-slot {
        display: block;
        margin-top: .45rem;
        border-radius: 14px;
        padding: .45rem .5rem;
        text-decoration: none !important;
        color: inherit;
        border: 1.5px solid transparent;
        font-size: .72rem;
        line-height: 1.25;
        transition: transform .12s ease, filter .12s ease;
    }
    .kids-slot:hover { transform: scale(1.03); filter: brightness(1.03); }
    .kids-slot--gold { background: #FFF3C4; border-color: #F5B80055; }
    .kids-slot--blue { background: #DCECFF; border-color: #0B3D9133; }
    .kids-slot--teal { background: #D7F5E8; border-color: #2FAE7A44; }
    .kids-slot__time { font-weight: 900; color: var(--k-ink); display: block; }
    .kids-slot__title { font-weight: 800; color: #334; display: block; margin-top: 2px; }
    .kids-hub {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .85rem;
    }
    @media (min-width: 768px) {
        .kids-hub { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    .kids-hub__card {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: .75rem; text-align: center;
        min-height: 148px; padding: 1.1rem .8rem;
        border-radius: 28px; text-decoration: none !important; color: inherit;
        border: 2px solid #E8EEF8; background: #fff;
        box-shadow: 0 10px 24px -18px rgba(11,61,145,.35);
        transition: transform .18s ease, box-shadow .18s ease;
    }
    .kids-hub__card:hover {
        transform: translateY(-4px) rotate(-.4deg);
        box-shadow: 0 18px 34px -16px rgba(11,61,145,.35);
    }
    .kids-hub__icon {
        width: 4.25rem; height: 4.25rem; border-radius: 24px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1.65rem; color: #fff;
    }
    .kids-hub__icon--materials { background: linear-gradient(135deg, #7EC8FF, #0B3D91); }
    .kids-hub__icon--videos { background: linear-gradient(135deg, #FF8FAB, #F5B800); }
    .kids-hub__icon--assignments { background: linear-gradient(135deg, #7AD9B0, #0B8F6A); }
    .kids-hub__icon--lectures { background: linear-gradient(135deg, #F5B800, #E08900); }
    .kids-empty {
        margin-top: .55rem; border-radius: 14px; padding: .55rem;
        background: #F7FAFF; color: var(--k-muted); font-size: .68rem; font-weight: 700;
        text-align: center;
    }
</style>
@endpush

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $weekDays = $weekDays ?? collect();
    $todayItems = $todayItems ?? collect();
    $nextAppointment = $nextAppointment ?? null;
@endphp

<div class="kids-home space-y-5">
    <section class="kids-hero">
        <span class="kids-hero__cloud" style="width:90px;height:36px;top:12px;{{ $isRtl ? 'left' : 'right' }}:18%;"></span>
        <span class="kids-hero__cloud" style="width:60px;height:26px;bottom:18px;{{ $isRtl ? 'right' : 'left' }}:8%;"></span>
        <div class="relative flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <p class="text-xs font-black tracking-wide text-[#0B3D91]/80 mb-1">
                    {{ $isRtl ? '📅 أسبوعي الجميل' : '📅 My fun week' }}
                </p>
                <h1 class="text-2xl sm:text-[30px] font-black text-[#1A2744] leading-tight">
                    {{ __('student.welcome_name', ['name' => auth()->user()->name]) }}
                </h1>
                <p class="mt-1 text-sm font-semibold text-[#6B7A99]">
                    @if($nextAppointment)
                        {{ $isRtl ? 'موعدك القادم:' : 'Next up:' }}
                        {{ $nextAppointment->title }}
                        · {{ $nextAppointment->starts_at?->format('g:i A') }}
                    @else
                        {{ $isRtl ? 'اضغط على أي موعد للدخول إلى المحاضرة.' : 'Tap any slot to join your class.' }}
                    @endif
                </p>
            </div>
            @if($todayItems->isNotEmpty())
                <div class="inline-flex items-center gap-2 rounded-full bg-[#F5B800] text-[#072A66] px-3.5 py-2 text-xs font-black shadow-sm">
                    <i class="fas fa-star"></i>
                    {{ $todayItems->count() }} {{ $isRtl ? 'موعد اليوم' : 'today' }}
                </div>
            @endif
        </div>
    </section>

    <section>
        <div class="flex items-center justify-between mb-3 px-1">
            <h2 class="text-base font-black text-[#1A2744]">{{ $isRtl ? 'تقويمي الأسبوعي' : 'Weekly calendar' }}</h2>
            <p class="text-[11px] font-bold text-[#6B7A99]">{{ $isRtl ? 'إشعار قبل الموعد بـ 30 دقيقة' : 'Reminder 30 min before' }}</p>
        </div>
        <div class="kids-week">
            @foreach($weekDays as $day)
                <article class="kids-day {{ $day->is_today ? 'is-today' : '' }}">
                    <div class="flex items-center justify-between gap-1">
                        <span class="kids-day__badge">{{ $day->date->format('j') }}</span>
                        <div class="text-end min-w-0">
                            <p class="text-[11px] font-black text-[#1A2744] truncate">{{ $day->short }}</p>
                            @if($day->is_today)
                                <p class="text-[10px] font-extrabold text-[#8A6A00]">{{ $isRtl ? 'اليوم' : 'Today' }}</p>
                            @endif
                        </div>
                    </div>

                    @forelse($day->items as $slot)
                        @php
                            $tone = match($slot->color ?? 'blue') {
                                'gold' => 'kids-slot--gold',
                                'teal' => 'kids-slot--teal',
                                default => 'kids-slot--blue',
                            };
                            $href = $slot->join_url
                                ?: (Route::has('student.schedule.join')
                                    ? route('student.schedule.join', ['type' => $slot->type, 'id' => $slot->ref_id])
                                    : '#');
                        @endphp
                        <a href="{{ $href }}" class="kids-slot {{ $tone }}" title="{{ $isRtl ? 'دخول المحاضرة' : 'Join class' }}">
                            <span class="kids-slot__time">
                                <i class="far fa-clock text-[10px] opacity-70"></i>
                                {{ $slot->starts_at?->format('g:i A') }}
                            </span>
                            <span class="kids-slot__title truncate">{{ $slot->title }}</span>
                            <span class="block text-[10px] font-bold text-[#6B7A99] truncate mt-0.5">{{ $slot->subtitle }}</span>
                        </a>
                    @empty
                        <div class="kids-empty">{{ $isRtl ? 'لا مواعيد' : 'Free' }}</div>
                    @endforelse
                </article>
            @endforeach
        </div>
    </section>

    <section>
        <h2 class="text-base font-black text-[#1A2744] mb-3 px-1">{{ $isRtl ? 'مكتبتي السريعة' : 'Quick library' }}</h2>
        <div class="kids-hub">
            <a href="{{ route('student.library.materials') }}" class="kids-hub__card">
                <span class="kids-hub__icon kids-hub__icon--materials"><i class="fas fa-book-open"></i></span>
                <div>
                    <p class="text-sm font-black text-[#1A2744]">{{ $isRtl ? 'مكتبة الماتريال' : 'Materials' }}</p>
                    <p class="text-[11px] font-bold text-[#6B7A99] mt-0.5">{{ $isRtl ? 'ملفات ودروس' : 'Files & notes' }}</p>
                </div>
            </a>
            <a href="{{ route('student.library.videos') }}" class="kids-hub__card">
                <span class="kids-hub__icon kids-hub__icon--videos"><i class="fas fa-film"></i></span>
                <div>
                    <p class="text-sm font-black text-[#1A2744]">{{ $isRtl ? 'مكتبة الفيديوهات' : 'Videos' }}</p>
                    <p class="text-[11px] font-bold text-[#6B7A99] mt-0.5">{{ $isRtl ? 'تسجيلات الحصص' : 'Class recordings' }}</p>
                </div>
            </a>
            <a href="{{ route('student.assignments.index') }}" class="kids-hub__card">
                <span class="kids-hub__icon kids-hub__icon--assignments"><i class="fas fa-pencil-alt"></i></span>
                <div>
                    <p class="text-sm font-black text-[#1A2744]">{{ $isRtl ? 'واجباتي' : 'Assignments' }}</p>
                    <p class="text-[11px] font-bold text-[#6B7A99] mt-0.5">{{ $isRtl ? 'حل وسلّم' : 'Do & submit' }}</p>
                </div>
            </a>
            <a href="{{ route('student.lectures.index') }}" class="kids-hub__card">
                <span class="kids-hub__icon kids-hub__icon--lectures"><i class="fas fa-chalkboard"></i></span>
                <div>
                    <p class="text-sm font-black text-[#1A2744]">{{ $isRtl ? 'محاضراتي' : 'My lectures' }}</p>
                    <p class="text-[11px] font-bold text-[#6B7A99] mt-0.5">{{ $isRtl ? 'خاصة ومجموعات' : 'Private & groups' }}</p>
                </div>
            </a>
        </div>
    </section>

    @if(student_ui('show_school', true) || student_ui('show_private_lessons', true))
        <section class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @if(student_ui('show_school', true) && Route::has('student.classes.index'))
                <a href="{{ route('student.classes.index') }}" class="rounded-2xl border-2 border-[#E8EEF8] bg-white px-4 py-4 no-underline text-inherit hover:border-[#0B3D91]/30 transition">
                    <p class="text-xs font-black text-[#0B3D91]">🏫 {{ $isRtl ? 'فصولي' : 'My classes' }}</p>
                    <p class="mt-1 text-sm font-bold text-[#6B7A99]">{{ $isRtl ? 'الجداول والانضمام للفصل' : 'Schedules & join class' }}</p>
                </a>
            @endif
            @if(student_ui('show_private_lessons', true) && Route::has('student.private-lectures.index'))
                <a href="{{ route('student.private-lectures.index') }}" class="rounded-2xl border-2 border-[#E8EEF8] bg-white px-4 py-4 no-underline text-inherit hover:border-[#F5B800]/50 transition">
                    <p class="text-xs font-black text-[#8A6A00]">👨‍🏫 {{ $isRtl ? 'حصصي الخاصة' : 'Private lessons' }}</p>
                    <p class="mt-1 text-sm font-bold text-[#6B7A99]">{{ $isRtl ? 'معلمك ومواعيدك' : 'Your teacher & slots' }}</p>
                </a>
            @endif
        </section>
    @endif
</div>
@endsection
