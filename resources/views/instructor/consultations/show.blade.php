@extends('layouts.app')

@section('title', __('instructor.cons_show_title'))
@section('page_title', __('instructor.cons_show_title'))

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.consultations.index') }}">{{ __('instructor.cons_title') }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ $consultation->student->name ?? __('instructor.pm_student_fallback') }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-comments su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.cons_show_heading', ['name' => $consultation->student->name ?? __('instructor.pm_student_fallback')]) }}
            </h1>
        </div>
        <div class="su-page-head__actions">
            <span class="su-chip">{{ $consultation->statusLabel() }}</span>
            <a href="{{ route('instructor.consultations.index') }}" class="su-btn">
                <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.cons_back') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(34,197,94,.35);background:rgba(34,197,94,.08);color:#15803d;font-size:13px">
            {{ session('success') }}
        </div>
    @endif

    <section class="su-card">
        <div class="su-meta-list">
            <div class="su-meta-row">
                <span class="su-meta-ico su-soft-1"><i class="fas fa-money-bill-wave" aria-hidden="true"></i></span>
                <span>{{ __('instructor.cons_amount') }}:</span>
                <strong class="tabular-nums">{{ number_format($consultation->price_amount, 2) }} ج.م</strong>
            </div>
            <div class="su-meta-row">
                <span class="su-meta-ico su-soft-2"><i class="fas fa-clock" aria-hidden="true"></i></span>
                <span>{{ __('instructor.cons_duration') }}:</span>
                <strong>{{ (int) $consultation->duration_minutes }} {{ __('instructor.o1o_minutes') }}</strong>
            </div>
        </div>

        @if($consultation->student_message)
            <div style="margin-top:16px;padding-top:16px;border-top:0.5px solid var(--su-line)">
                <div style="font-size:12px;font-weight:500;color:var(--su-ink-40);margin-bottom:6px">{{ __('instructor.cons_student_request') }}</div>
                <p style="margin:0;font-size:13px;color:var(--su-ink);white-space:pre-line">{{ $consultation->student_message }}</p>
            </div>
        @endif

        @if($consultation->status === \App\Models\ConsultationRequest::STATUS_SCHEDULED && $consultation->classroomMeeting)
            @php $m = $consultation->classroomMeeting; $joinUrl = url('classroom/join/'.$m->code); @endphp
            <div class="su-card su-soft-3" style="margin-top:20px;padding:16px">
                <p style="margin:0 0 8px;font-size:14px;font-weight:600;color:var(--su-ink)">
                    {{ __('instructor.cons_appointment') }}:
                    <x-app-datetime :at="$consultation->scheduled_at" />
                </p>
                <p style="margin:0 0 12px;font-size:12px;color:var(--su-ink-40);word-break:break-all">
                    {{ __('instructor.cons_guest_link') }}: {{ $joinUrl }}
                </p>
                <div style="display:flex;flex-wrap:wrap;gap:8px">
                    <a href="{{ route('instructor.classroom.show', $m) }}" class="su-btn">
                        <i class="fas fa-cog" aria-hidden="true"></i>
                        {{ __('instructor.cons_room_settings') }}
                    </a>
                    @if(!$m->ended_at)
                        <a href="{{ route('instructor.classroom.room', $m) }}" class="su-btn su-btn--ok">
                            <i class="fas fa-video" aria-hidden="true"></i>
                            {{ __('instructor.cons_enter_room') }}
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </section>
</div>
@endsection
