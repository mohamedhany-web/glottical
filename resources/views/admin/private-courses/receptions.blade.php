@extends('layouts.admin')

@section('title', 'استقبال الطلاب')
@section('page_title', 'استقبال الطلاب — كورسات بريفيت')

@section('content')
<div class="space-y-4">
    <p class="text-sm text-muted">متابعة استقبال الطلاب الجدد بعد التسكين أو الشراء.</p>
    <div class="rounded-2xl border border-line bg-surface shadow-soft overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-[#F4F7FC] text-muted text-xs">
                <tr>
                    <th class="px-4 py-3 text-start">الطالب</th>
                    <th class="px-4 py-3 text-start">المعلم</th>
                    <th class="px-4 py-3 text-start">الحالة</th>
                    <th class="px-4 py-3 text-start">المصدر</th>
                    <th class="px-4 py-3 text-start">تحديث</th>
                </tr>
            </thead>
            <tbody>
                @forelse($receptions as $reception)
                    <tr class="border-t border-line align-top">
                        <td class="px-4 py-3 font-medium">{{ $reception->student->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $reception->instructor->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $reception->status }}</td>
                        <td class="px-4 py-3 text-muted">{{ $reception->source }}</td>
                        <td class="px-4 py-3">
                            <form method="post" action="{{ route('admin.private-courses.receptions.update', $reception) }}" class="flex flex-wrap gap-2 items-center">
                                @csrf
                                @method('PUT')
                                <select name="status" class="rounded-lg border border-line px-2 py-1 text-xs">
                                    <option value="pending" @selected($reception->status==='pending')>pending</option>
                                    <option value="welcomed" @selected($reception->status==='welcomed')>welcomed</option>
                                    <option value="completed" @selected($reception->status==='completed')>completed</option>
                                </select>
                                <button class="rounded-lg bg-accent px-3 py-1 text-xs font-bold text-white">حفظ</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-muted">لا سجلات استقبال.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3">{{ $receptions->links() }}</div>
    </div>
</div>
@endsection
