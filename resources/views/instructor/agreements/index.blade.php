@extends('layouts.app')

@section('title', __('instructor.agreements_system') . ' - ' . config('app.name'))
@section('header', __('instructor.agreements_system'))

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl p-5 sm:p-6 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm">
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-slate-100 mb-1">{{ __('instructor.agreements_system') }}</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">تابع عقودك ونسب الاستحقاق وحالة المدفوعات من مكان واحد.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <div class="rounded-2xl p-5 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 inline-flex items-center justify-center">
                    <i class="fas fa-sack-dollar"></i>
                </span>
                <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 px-2 py-1 rounded-lg">مدفوع</span>
            </div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ __('instructor.total_earned') }}</p>
            <p class="mt-2 text-2xl font-black text-slate-900 dark:text-slate-100">{{ number_format($stats['total_earned'], 2) }} {{ __('public.currency_egp') }}</p>
        </div>

        <div class="rounded-2xl p-5 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="w-11 h-11 rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 inline-flex items-center justify-center">
                    <i class="fas fa-clock"></i>
                </span>
                <span class="text-xs font-semibold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 px-2 py-1 rounded-lg">معلّق</span>
            </div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ __('instructor.pending') }}</p>
            <p class="mt-2 text-2xl font-black text-slate-900 dark:text-slate-100">{{ number_format($stats['pending_amount'], 2) }} {{ __('public.currency_egp') }}</p>
        </div>

        <div class="rounded-2xl p-5 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="w-11 h-11 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 inline-flex items-center justify-center">
                    <i class="fas fa-receipt"></i>
                </span>
                <span class="text-xs font-semibold text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 px-2 py-1 rounded-lg">سجلات</span>
            </div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ __('instructor.total_payments') }}</p>
            <p class="mt-2 text-2xl font-black text-slate-900 dark:text-slate-100">{{ number_format($stats['total_payments']) }}</p>
        </div>
    </div>

    @if($activeAgreement)
    <div class="rounded-2xl p-5 sm:p-6 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="space-y-3">
                <div class="flex items-center flex-wrap gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-600 text-white">{{ __('instructor.active_status') }}</span>
                    <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-slate-100">{{ $activeAgreement->title }}</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400">{{ __('instructor.agreement_number') }}</p>
                        <p class="font-bold text-slate-900 dark:text-slate-100">{{ $activeAgreement->agreement_number }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500 dark:text-slate-400">{{ __('instructor.type') }}</p>
                        <p class="font-bold text-slate-900 dark:text-slate-100">
                            @if($activeAgreement->type == 'course_price')
                                {{ __('instructor.course_price') }}
                            @elseif($activeAgreement->type == 'hourly_rate')
                                {{ __('instructor.hourly_rate') }}
                            @else
                                {{ __('instructor.monthly_salary') }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-slate-500 dark:text-slate-400">{{ __('instructor.rate') }}</p>
                        <p class="font-bold text-slate-900 dark:text-slate-100">{{ number_format($activeAgreement->rate, 2) }} {{ __('public.currency_egp') }}</p>
                    </div>
                </div>
            </div>
            <a href="{{ route('instructor.agreements.show', $activeAgreement) }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition-colors shadow-sm">
                <i class="fas fa-eye"></i>
                {{ __('instructor.view_details') }}
            </a>
        </div>
    </div>
    @endif

    <div class="bg-white dark:bg-slate-800/95 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700">
            <h2 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                <i class="fas fa-handshake text-emerald-600 dark:text-emerald-400"></i>
                {{ __('instructor.all_agreements') }}
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-800/70">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-bold tracking-wider text-slate-700 dark:text-slate-300 uppercase">{{ __('instructor.agreement_number') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-bold tracking-wider text-slate-700 dark:text-slate-300 uppercase">{{ __('instructor.title') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-bold tracking-wider text-slate-700 dark:text-slate-300 uppercase">{{ __('instructor.type') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-bold tracking-wider text-slate-700 dark:text-slate-300 uppercase">{{ __('instructor.rate') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-bold tracking-wider text-slate-700 dark:text-slate-300 uppercase">{{ __('common.status') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-bold tracking-wider text-slate-700 dark:text-slate-300 uppercase">{{ __('instructor.start_date') }}</th>
                        <th class="px-6 py-4 text-center text-xs font-bold tracking-wider text-slate-700 dark:text-slate-300 uppercase">{{ __('instructor.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($agreements as $agreement)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-slate-900 dark:text-slate-100">{{ $agreement->agreement_number }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $agreement->title }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                @if($agreement->type == 'course_price') bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300
                                @elseif($agreement->type == 'hourly_rate') bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-300
                                @else bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300
                                @endif">
                                @if($agreement->type == 'course_price')
                                    {{ __('instructor.course_price') }}
                                @elseif($agreement->type == 'hourly_rate')
                                    {{ __('instructor.hourly_rate') }}
                                @else
                                    {{ __('instructor.monthly_salary') }}
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-slate-900 dark:text-slate-100">{{ number_format($agreement->rate, 2) }} {{ __('public.currency_egp') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                @if($agreement->status == 'active') bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400
                                @elseif($agreement->status == 'draft') bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300
                                @elseif($agreement->status == 'suspended') bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300
                                @elseif($agreement->status == 'terminated') bg-rose-100 dark:bg-rose-900/40 text-rose-700 dark:text-rose-400
                                @else bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300
                                @endif">
                                @if($agreement->status == 'active') {{ __('instructor.active_status') }}
                                @elseif($agreement->status == 'draft') {{ __('instructor.draft') }}
                                @elseif($agreement->status == 'suspended') {{ __('instructor.suspended') }}
                                @elseif($agreement->status == 'terminated') {{ __('instructor.terminated') }}
                                @else {{ __('instructor.agreement_completed') }}
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-slate-600 dark:text-slate-400">{{ $agreement->start_date->format('Y-m-d') }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('instructor.agreements.show', $agreement) }}"
                               class="inline-flex items-center justify-center w-10 h-10 bg-emerald-100 dark:bg-emerald-900/40 hover:bg-emerald-200 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 rounded-xl transition-colors"
                               title="{{ __('common.view') }}">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700/60 rounded-full flex items-center justify-center">
                                    <i class="fas fa-handshake text-slate-400 dark:text-slate-500 text-2xl"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-slate-100">{{ __('instructor.no_agreements') }}</p>
                                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">{{ __('instructor.no_agreements_description') }}</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
