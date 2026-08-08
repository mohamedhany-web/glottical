@extends('layouts.app')

@section('title', 'المعلمون المفعّلون')
@section('header', 'المعلمون المفعّلون من التوظيف')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route('admin.tutor-applications.hub') }}" class="text-sm font-semibold text-sky-700 hover:underline">← لوحة التوظيف</a>
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو البريد" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <button class="rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white">بحث</button>
        </form>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-start font-semibold">المعلم</th>
                    <th class="px-4 py-3 text-start font-semibold">الحساب</th>
                    <th class="px-4 py-3 text-start font-semibold">التفعيل</th>
                    <th class="px-4 py-3 text-start font-semibold">الحالة</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($applications as $app)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-800">{{ $app->full_name }}</div>
                            <div class="text-xs text-slate-500">{{ $app->headline }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @if($app->user)
                                <div>{{ $app->user->name }}</div>
                                <div class="text-xs text-slate-500" dir="ltr">{{ $app->user->email }}</div>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-500">
                            <div>{{ $app->activated_at?->format('Y-m-d H:i') ?: '—' }}</div>
                            <div class="text-xs">بواسطة: {{ $app->activatedByUser->name ?? '—' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @if($app->user)
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $app->user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $app->user->is_active ? 'نشط' : 'معطّل' }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-end space-x-2 space-x-reverse">
                            <a href="{{ route('admin.tutor-applications.show', $app) }}" class="font-semibold text-sky-700 hover:underline">الطلب</a>
                            @if($app->user)
                                <a href="{{ route('public.instructors.show', $app->user) }}" target="_blank" class="font-semibold text-slate-600 hover:underline">الملف العام</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-slate-500">لا يوجد معلمون مفعّلون من التوظيف بعد.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $applications->links() }}
</div>
@endsection
