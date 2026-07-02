@extends('layouts.app')

@section('title', __('instructor.my_requests_to_management') . ' - ' . config('app.name'))
@section('header', __('instructor.submit_requests_to_management'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if(session('success'))
        <div class="mb-6 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 text-emerald-800 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex justify-end">
        <a href="{{ route('instructor.management-requests.create') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg transition-all">
            <i class="fas fa-plus"></i>
            {{ __('instructor.new_request') }}
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800/95 rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200">
            <h2 class="text-2xl font-black text-gray-900 flex items-center gap-3">
                <i class="fas fa-inbox text-indigo-600"></i>
                {{ __('instructor.my_requests_to_management') }}
            </h2>
            <p class="text-gray-500 mt-1">{{ __('instructor.my_requests_description') }}</p>
        </div>

        <form method="GET" class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex flex-wrap gap-4">
            <select name="status" class="rounded-xl border border-gray-300 px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">{{ __('instructor.all_statuses_filter') }}</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('instructor.pending_review') }}</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ __('instructor.approved') }}</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ __('instructor.rejected') }}</option>
            </select>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-indigo-700">{{ __('common.search') }}</button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-900">{{ __('instructor.request_subject') }}</th>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-900">{{ __('common.status') }}</th>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-900">{{ __('common.date') }}</th>
                        <th class="px-6 py-4 text-center text-sm font-bold text-gray-900">{{ __('instructor.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-800/95 divide-y divide-gray-200">
                    @forelse($requests as $req)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/40 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-gray-900">{{ $req->subject }}</p>
                            <p class="text-sm text-gray-500 mt-0.5">{{ Str::limit($req->message, 60) }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($req->status == 'pending')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">{{ __('instructor.pending_review') }}</span>
                            @elseif($req->status == 'approved')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800">{{ __('instructor.approved') }}</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-100 dark:bg-rose-900/40 text-rose-800">{{ __('instructor.rejected') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $req->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('instructor.management-requests.show', $req) }}"
                               class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 font-semibold text-sm">
                                <i class="fas fa-eye"></i>
                                {{ __('common.view') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                            <p class="font-medium">{{ __('instructor.no_requests_yet') }}</p>
                            <a href="{{ route('instructor.management-requests.create') }}" class="mt-2 inline-block text-indigo-600 font-semibold">{{ __('instructor.new_request') }}</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $requests->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
