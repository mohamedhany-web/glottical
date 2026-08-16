@extends('layouts.app')

@section('title', 'تفاصيل الاستشارة')

@section('content')
<div class="w-full max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <a href="{{ route('instructor.consultations.index') }}" class="text-sm text-sky-600 hover:underline">← القائمة</a>

    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-6 space-y-4">
        <div class="flex justify-between items-start gap-2">
            <h1 class="text-xl font-black text-slate-900 dark:text-white">استشارة — {{ $consultation->student->name ?? 'طالب' }}</h1>
            <span class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700 text-xs font-semibold">{{ $consultation->statusLabel() }}</span>
        </div>
        <dl class="text-sm space-y-2">
            <div class="flex justify-between border-b border-slate-100 dark:border-slate-700 pb-2"><dt class="text-slate-500">المبلغ</dt><dd class="font-bold">{{ number_format($consultation->price_amount, 2) }} ج.م</dd></div>
            <div class="flex justify-between border-b border-slate-100 dark:border-slate-700 pb-2"><dt class="text-slate-500">المدة</dt><dd>{{ (int) $consultation->duration_minutes }} دقيقة</dd></div>
            @if($consultation->student_message)
            <div><dt class="text-slate-500 mb-1">طلب الطالب</dt><dd class="text-slate-800 dark:text-slate-200 whitespace-pre-line">{{ $consultation->student_message }}</dd></div>
            @endif
        </dl>

        @if($consultation->status === \App\Models\ConsultationRequest::STATUS_SCHEDULED && $consultation->classroomMeeting)
            @php $m = $consultation->classroomMeeting; $joinUrl = url('classroom/join/'.$m->code); @endphp
            <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 p-4 space-y-3">
                <p class="font-bold text-emerald-900 dark:text-emerald-100">الموعد: <x-app-datetime :at="$consultation->scheduled_at" /></p>
                <p class="text-xs break-all text-emerald-800 dark:text-emerald-200">رابط الضيوف: {{ $joinUrl }}</p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('instructor.classroom.show', $m) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 text-white text-sm font-bold">إعدادات الغرفة</a>
                    @if(!$m->ended_at)
                    <a href="{{ route('instructor.classroom.room', $m) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-bold">دخول الغرفة</a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
