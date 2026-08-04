@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'حصصي الخاصة' : 'My Private Lessons')

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
@endphp
<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 pb-10">
    <section class="rounded-2xl border border-[#E8EEF8] bg-white p-5 shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <p class="text-xs font-bold text-[#8A6A00] mb-1">👨‍🏫 {{ $isRtl ? 'حصصي الخاصة' : 'My Private Lessons' }}</p>
        <h1 class="text-xl font-extrabold text-[#0B1220] dark:text-white">{{ $isRtl ? 'الحصص الخاصة' : 'Private Lessons' }}</h1>
        <p class="mt-1 text-sm text-[#5B6577] dark:text-gray-400">
            {{ $isRtl
                ? 'منفصلة تمامًا عن مدرستي (Islamic Foundations) — مدة الحصة 50 دقيقة.'
                : 'Completely separate from My School (Islamic Foundations) — each lesson is 50 minutes.' }}
        </p>
        @if($reception && $reception->status === 'pending')
            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                {{ $isRtl ? 'جارٍ تجهيز استقبالك على المنصة. سيصلك ترحيب ومتابعة من الفريق قريبًا.' : 'We are preparing your onboarding. A welcome from the team is on the way.' }}
            </div>
        @endif
        <div class="mt-4 flex flex-wrap gap-2">
            <a href="{{ route('public.courses') }}" class="inline-flex items-center rounded-xl bg-[#F5B800] px-4 py-2 text-xs font-extrabold text-[#072A66]">
                {{ $isRtl ? 'تصفّح المعلمين' : 'Browse teachers' }}
            </a>
            @if(Route::has('student.school.index'))
            <a href="{{ route('student.school.index') }}" class="inline-flex items-center rounded-xl border border-[#E8EEF8] px-4 py-2 text-xs font-bold text-[#0B3D91]">
                🏫 {{ $isRtl ? 'مدرستي' : 'My School' }}
            </a>
            @endif
        </div>
    </section>

    <section class="rounded-2xl border border-[#E8EEF8] bg-white overflow-hidden shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="px-5 py-4 border-b border-[#E8EEF8] dark:border-gray-700 flex items-center justify-between gap-3">
            <h2 class="font-extrabold text-[#0B1220] dark:text-white">{{ $isRtl ? 'الحصص القادمة' : 'Upcoming Lessons' }}</h2>
            @if(Route::has('student.private-messages.index'))
            <a href="{{ route('student.private-messages.index') }}" class="text-sm font-bold text-[#0B3D91]">{{ $isRtl ? 'رسائل المعلم' : 'Teacher messages' }}</a>
            @endif
        </div>
        <div class="divide-y divide-[#E8EEF8] dark:divide-gray-700">
            @forelse($sessions as $session)
                @php
                    $dur = (int) ($session->duration_minutes ?: 50);
                    if ($dur !== 50) { $dur = 50; }
                    $awaiting = $session->isAwaitingTeacherStart();
                    $ends = $session->scheduled_at ? $session->scheduled_at->copy()->addMinutes($dur) : null;
                @endphp
                <div class="px-5 py-4 flex flex-wrap items-center justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="inline-flex items-center rounded-full bg-[#FFF6D6] px-2.5 py-0.5 text-[11px] font-bold text-[#8A6A00]">{{ $isRtl ? 'حصة خاصة' : 'Private Lesson' }}</span>
                            <span class="text-xs font-bold text-[#8A94A6]">{{ $dur }} {{ $isRtl ? 'دقيقة' : 'min' }}</span>
                        </div>
                        <h3 class="font-bold text-[#0B1220] dark:text-white truncate">
                            {{ $session->course->title ?? ($isRtl ? 'حصة خاصة' : 'Private lesson') }}
                            @if($session->instructor)
                                <span class="text-[#5B6577] font-semibold">· {{ $session->instructor->name }}</span>
                            @endif
                        </h3>
                        <p class="text-sm text-[#5B6577] dark:text-gray-400">
                            @if($session->scheduled_at)
                                {{ $session->scheduled_at->timezone(config('app.timezone'))->format('l, M j') }}
                                · {{ $session->scheduled_at->format('g:i A') }}@if($ends) – {{ $ends->format('g:i A') }}@endif
                            @else
                                {{ $isRtl ? 'بانتظار الجدولة' : 'Awaiting schedule' }}
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($awaiting)
                            <span class="rounded-xl bg-amber-100 px-3 py-2 text-xs font-extrabold text-amber-800">{{ $isRtl ? 'الآن يبدأ المعلم' : 'Teacher starting soon' }}</span>
                        @elseif($session->status === 'scheduled' && $session->classroomMeeting)
                            <a href="{{ route('student.classroom.room', $session->classroomMeeting) }}" class="rounded-xl bg-[#F5B800] px-4 py-2 text-xs font-extrabold text-[#072A66]">{{ $isRtl ? 'دخول الحصة' : 'Join Class' }}</a>
                        @else
                            <span class="rounded-xl bg-[#F4F7FC] px-3 py-2 text-xs font-bold text-[#5B6577]">{{ \App\Models\OneToOneSession::statusLabels()[$session->status] ?? $session->status }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="px-5 py-8 text-sm text-[#8A94A6]">{{ $isRtl ? 'لا توجد حصص خاصة بعد.' : 'No private lessons yet.' }}</p>
            @endforelse
        </div>
        @if($sessions->hasPages())
            <div class="px-5 py-3 border-t border-[#E8EEF8] dark:border-gray-700">{{ $sessions->links() }}</div>
        @endif
    </section>
</div>
@endsection
