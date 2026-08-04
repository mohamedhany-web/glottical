@extends('layouts.admin')

@section('title', 'رسائل كورسات بريفيت')
@section('page_title', 'رسائل كورسات بريفيت')

@section('content')
<div class="space-y-4">
    <p class="text-sm text-muted">محادثات الطلاب مع المعلمين — مرئية للإدارة.</p>
    <div class="rounded-2xl border border-line bg-surface shadow-soft overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-[#F4F7FC] text-muted text-xs">
                <tr>
                    <th class="px-4 py-3 text-start">الطالب</th>
                    <th class="px-4 py-3 text-start">المعلم</th>
                    <th class="px-4 py-3 text-start">الحالة</th>
                    <th class="px-4 py-3 text-start">آخر رسالة</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($threads as $thread)
                    <tr class="border-t border-line">
                        <td class="px-4 py-3 font-medium text-ink">{{ $thread->student->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $thread->instructor->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $thread->status }}</td>
                        <td class="px-4 py-3 text-muted">{{ $thread->last_message_at?->format('Y-m-d H:i') ?? '—' }}</td>
                        <td class="px-4 py-3 text-end">
                            <a href="{{ route('admin.private-courses.threads.show', $thread) }}" class="text-accent font-bold text-xs">عرض</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-muted">لا محادثات بعد.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3">{{ $threads->links() }}</div>
    </div>
</div>
@endsection
