@extends('layouts.app')

@section('title', 'مراجعة طلبات المعلمين')
@section('header', 'مراجعة طلبات التوظيف')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-800 font-semibold">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('admin.tutor-applications.hub') }}" class="text-sm font-semibold text-sky-700 hover:underline">← لوحة التوظيف</a>
            <p class="mt-1 text-sm text-slate-500">عرض ومراجعة كل طلبات التقديم مع البيانات والمستندات.</p>
        </div>
        <a href="{{ $applyUrl }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700">
            <i class="fas fa-link"></i> لينك التقديم
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        @foreach([
            ['label' => 'مسودات', 'value' => $stats['draft'] ?? 0, 'status' => 'draft'],
            ['label' => 'قيد المراجعة', 'value' => $stats['pending'], 'status' => 'pending'],
            ['label' => 'بانتظار التفعيل', 'value' => $stats['approved'], 'status' => 'approved'],
            ['label' => 'مفعّل', 'value' => $stats['activated'], 'status' => 'activated'],
            ['label' => 'مرفوض', 'value' => $stats['rejected'], 'status' => 'rejected'],
        ] as $card)
            <a href="{{ route('admin.tutor-applications.index', ['status' => $card['status']]) }}"
               class="rounded-2xl border bg-white p-4 {{ request('status') === $card['status'] ? 'border-sky-400 ring-1 ring-sky-200' : 'border-slate-200' }}">
                <p class="text-xs font-semibold text-slate-500">{{ $card['label'] }}</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">{{ $card['value'] }}</p>
            </a>
        @endforeach
    </div>

    <form method="GET" class="rounded-2xl border border-slate-200 bg-white p-4 grid gap-3 md:grid-cols-3 md:items-end">
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">بحث</label>
            <input type="text" name="search" value="{{ request('search') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="اسم / بريد / جوال">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">الحالة</label>
            <select name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <option value="">الكل</option>
                <option value="draft" @selected(request('status') === 'draft')>مسودة</option>
                <option value="pending" @selected(request('status') === 'pending')>قيد المراجعة</option>
                <option value="approved" @selected(request('status') === 'approved')>بانتظار التفعيل</option>
                <option value="activated" @selected(request('status') === 'activated')>مفعّل</option>
                <option value="rejected" @selected(request('status') === 'rejected')>مرفوض</option>
            </select>
        </div>
        <button class="inline-flex h-10 items-center justify-center rounded-xl bg-sky-600 px-4 text-sm font-semibold text-white">تصفية</button>
    </form>

    <div class="overflow-x-auto overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-start font-semibold">المتقدم</th>
                    <th class="px-4 py-3 text-start font-semibold">التواصل</th>
                    <th class="px-4 py-3 text-start font-semibold">البيانات</th>
                    <th class="px-4 py-3 text-start font-semibold">الحالة</th>
                    <th class="px-4 py-3 text-start font-semibold">التاريخ</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($applications as $app)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($app->photoUrl())
                                    <img src="{{ $app->photoUrl() }}" alt="" class="h-10 w-10 rounded-xl object-cover border border-slate-200">
                                @else
                                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-500 font-bold">{{ mb_substr($app->full_name, 0, 1) }}</span>
                                @endif
                                <div>
                                    <div class="font-semibold text-slate-800">{{ $app->full_name }}</div>
                                    <div class="text-xs text-slate-500 line-clamp-1">{{ $app->headline }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            <div dir="ltr">{{ $app->email }}</div>
                            <div dir="ltr">{{ $app->phone }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500">
                            <div>{{ $app->education ?: '—' }}</div>
                            <div>{{ $app->years_experience !== null ? $app->years_experience.' سنة خبرة' : '—' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold
                                @if($app->status === 'activated') bg-emerald-100 text-emerald-700
                                @elseif($app->status === 'approved') bg-sky-100 text-sky-700
                                @elseif($app->status === 'rejected') bg-rose-100 text-rose-700
                                @else bg-amber-100 text-amber-700 @endif">
                                {{ $app->statusLabel() }}
                            </span>
                            @if($app->user)
                                <div class="mt-1 text-[11px] text-slate-500">حساب: {{ $app->user->name }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-500">{{ $app->created_at?->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 text-end whitespace-nowrap">
                            <a href="{{ route('admin.tutor-applications.show', $app) }}" class="font-semibold text-sky-700 hover:underline">
                                {{ $app->canActivateAccount() ? 'مراجعة / تفعيل' : 'عرض البيانات' }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-slate-500">لا توجد طلبات.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $applications->links() }}
</div>
@endsection
