@extends('layouts.app')

@section('title', __('instructor.cons_title'))
@section('page_title', __('instructor.cons_title'))

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-comments su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.cons_title') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.cons_subtitle') }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.courses.index') }}" class="su-btn">
                <i class="fas fa-book" aria-hidden="true"></i>
                {{ __('instructor.courses') }}
            </a>
            @if(\Illuminate\Support\Facades\Route::has('instructor.calendar'))
                <a href="{{ route('instructor.calendar') }}" class="su-btn su-btn--primary">
                    <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                    {{ __('instructor.cons_my_calendar') }}
                </a>
            @endif
        </div>
    </div>

    <section class="su-card su-card--flush">
        <div class="su-table-wrap" style="border:0;border-radius:0;background:transparent">
            <table class="su-table">
                <thead>
                    <tr>
                        <th>{{ __('instructor.cons_student') }}</th>
                        <th>{{ __('instructor.cons_amount') }}</th>
                        <th>{{ __('instructor.cons_status') }}</th>
                        <th>{{ __('instructor.cons_when') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $r)
                        <tr>
                            <td><strong style="font-weight:600">{{ $r->student->name ?? '—' }}</strong></td>
                            <td class="tabular-nums" style="color:var(--su-ink-40)">{{ number_format($r->price_amount, 2) }} $</td>
                            <td><span class="su-chip">{{ $r->statusLabel() }}</span></td>
                            <td class="tabular-nums" style="color:var(--su-ink-40)">
                                @if($r->scheduled_at)
                                    <x-app-datetime :at="$r->scheduled_at" pattern="Y-m-d H:i" />
                                @else
                                    —
                                @endif
                            </td>
                            <td style="text-align:end">
                                <a href="{{ route('instructor.consultations.show', $r) }}" class="su-btn" style="height:32px">
                                    {{ __('instructor.cons_details') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="su-empty">
                                    <i class="fas fa-comments" aria-hidden="true"></i>
                                    <p>{{ __('instructor.cons_empty') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($requests, 'links') && $requests->hasPages())
            <div class="su-pager" style="padding:12px">{{ $requests->links() }}</div>
        @endif
    </section>
</div>
@endsection
