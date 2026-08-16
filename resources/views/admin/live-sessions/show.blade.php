@extends('layouts.admin')
@section('title', $liveSession->title)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.live-sessions.index') }}" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 transition-colors"><i class="fas fa-arrow-right"></i></a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $liveSession->title }}</h1>
                <p class="text-sm text-slate-400 font-mono mt-0.5">{{ $liveSession->room_name }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($liveSession->status === 'live')
                <form method="POST" action="{{ route('admin.live-sessions.force-end', $liveSession) }}" onsubmit="return confirm('هل تريد إنهاء البث؟')">
                    @csrf
                    <button class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-stop ml-1"></i> إنهاء البث
                    </button>
                </form>
            @elseif($liveSession->status === 'scheduled')
                <a href="{{ route('admin.live-sessions.edit', $liveSession) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-edit ml-1"></i> تعديل
                </a>
                <form method="POST" action="{{ route('admin.live-sessions.cancel', $liveSession) }}" onsubmit="return confirm('إلغاء الجلسة؟')">
                    @csrf
                    <button class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-300 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-ban ml-1"></i> إلغاء الجلسة
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Status Banner --}}
    @if($liveSession->status === 'live')
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 flex items-center gap-3">
        <span class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></span>
        <span class="font-semibold text-red-700 dark:text-red-400">البث مباشر الآن</span>
        <span class="text-sm text-red-600 dark:text-red-400">— بدأ {{ $liveSession->started_at?->diffForHumans() }}</span>
    </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Session Info --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
                <h2 class="font-bold text-slate-800 dark:text-white mb-4"><i class="fas fa-info-circle text-blue-500 ml-2"></i>تفاصيل الجلسة</h2>
                <div class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-slate-500">المعلم (المشترك):</span>
                        @if($liveSession->instructor)
                            <a href="{{ route('admin.users.show', $liveSession->instructor->id) }}" class="font-semibold text-slate-800 dark:text-white mr-2 hover:text-blue-600 hover:underline">{{ $liveSession->instructor->name }}</a>
                            @if($liveSession->instructor->role === 'student')
                                <span class="text-xs text-emerald-600 dark:text-emerald-400">(مشترك — طالب لدينا)</span>
                            @endif
                        @else
                            <span class="font-semibold text-slate-800 dark:text-white mr-2">—</span>
                        @endif
                    </div>
                    <div><span class="text-slate-500">الكورس:</span> <span class="font-semibold text-slate-800 dark:text-white mr-2">{{ $liveSession->course?->title ?? 'جلسة عامة' }}</span></div>
                    <div><span class="text-slate-500">الموعد:</span> <span class="font-semibold text-slate-800 dark:text-white mr-2">@if($liveSession->scheduled_at)<x-app-datetime :at="$liveSession->scheduled_at" pattern="Y/m/d H:i" />@else — @endif</span></div>
                    <div><span class="text-slate-500">المدة:</span> <span class="font-semibold text-slate-800 dark:text-white mr-2">{{ $liveSession->duration_for_humans }}</span></div>
                    <div><span class="text-slate-500">الحد الأقصى:</span> <span class="font-semibold text-slate-800 dark:text-white mr-2">{{ $liveSession->max_participants }} مشارك</span></div>
                    <div><span class="text-slate-500">السيرفر:</span> <span class="font-semibold text-slate-800 dark:text-white mr-2">{{ $liveSession->server?->name ?? 'افتراضي' }}</span></div>
                    <div><span class="text-slate-500">تسجيل:</span> <span class="font-semibold mr-2">{{ $liveSession->is_recorded ? '✅ نعم' : '❌ لا' }}</span></div>
                    <div><span class="text-slate-500">شات:</span> <span class="font-semibold mr-2">{{ $liveSession->allow_chat ? '✅ مفعل' : '❌ معطل' }}</span></div>
                </div>
                @if($liveSession->description)
                <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <p class="text-sm text-slate-600 dark:text-slate-400">{{ $liveSession->description }}</p>
                </div>
                @endif
            </div>

            {{-- Attendance --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
                <h2 class="font-bold text-slate-800 dark:text-white mb-4">
                    <i class="fas fa-users text-emerald-500 ml-2"></i>سجل الحضور
                    <span class="text-sm font-normal text-slate-500 mr-2">({{ $attendees->count() }})</span>
                </h2>
                @if($attendees->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-700/50">
                            <tr>
                                <th class="px-3 py-2 text-right text-slate-600 dark:text-slate-300">المستخدم</th>
                                <th class="px-3 py-2 text-center text-slate-600 dark:text-slate-300">الدور</th>
                                <th class="px-3 py-2 text-right text-slate-600 dark:text-slate-300">وقت الدخول</th>
                                <th class="px-3 py-2 text-right text-slate-600 dark:text-slate-300">وقت الخروج</th>
                                <th class="px-3 py-2 text-center text-slate-600 dark:text-slate-300">المدة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach($attendees as $att)
                            <tr>
                                <td class="px-3 py-2 font-medium text-slate-800 dark:text-white">{{ $att->user?->name ?? 'محذوف' }}</td>
                                <td class="px-3 py-2 text-center">
                                    @if($att->role_in_session === 'instructor')
                                        <span class="px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 text-xs">مدرب</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-600 text-slate-600 dark:text-slate-300 text-xs">{{ __('admin.student_role_label') }}</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-xs text-slate-500">{{ $att->joined_at?->format('H:i:s') }}</td>
                                <td class="px-3 py-2 text-xs text-slate-500">{{ $att->left_at?->format('H:i:s') ?? '—' }}</td>
                                <td class="px-3 py-2 text-center text-xs text-slate-500">{{ $att->duration_for_humans }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-slate-500 text-center py-6"><i class="fas fa-user-slash text-2xl text-slate-300 mb-2 block"></i>لا يوجد حضور بعد</p>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- التحكم في المعلم (المشترك عندنا) --}}
            @if($liveSession->instructor)
            <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800 p-5">
                <h3 class="font-bold text-slate-800 dark:text-white mb-2 text-sm"><i class="fas fa-user-cog text-amber-500 ml-1"></i> التحكم في المعلم</h3>
                <p class="text-xs text-slate-600 dark:text-slate-400 mb-3">المعلم = المشترك (طالب لدينا يشترون منا الخدمة). يمكنك مراجعة بياناته واشتراكه من لوحة الإدارة.</p>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('admin.users.show', $liveSession->instructor->id) }}" class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-white dark:bg-slate-800 border border-amber-200 dark:border-amber-700 text-amber-700 dark:text-amber-400 rounded-lg text-sm font-semibold hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors">
                        <i class="fas fa-user"></i> عرض بيانات المعلم
                    </a>
                    @if($liveSession->instructor->role === 'student')
                    <a href="{{ route('admin.subscriptions.index') }}?user_id={{ $liveSession->instructor->id }}" class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-100 transition-colors">
                        <i class="fas fa-crown"></i> اشتراكاته
                    </a>
                    @endif
                </div>
            </div>
            @endif

            {{-- Quick Info --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
                <h3 class="font-bold text-slate-800 dark:text-white mb-3 text-sm">الحالة</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500">الحالة</span>
                        @if($liveSession->status === 'live')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-100 dark:bg-red-900/40 text-red-600 text-xs font-bold"><span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span> مباشر</span>
                        @elseif($liveSession->status === 'scheduled')
                            <span class="px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 text-xs font-medium">مجدولة</span>
                        @elseif($liveSession->status === 'ended')
                            <span class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-600 text-slate-600 dark:text-slate-300 text-xs font-medium">منتهية</span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-600 text-xs font-medium">ملغاة</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500">عدد الحاضرين</span>
                        <span class="font-bold text-slate-800 dark:text-white">{{ $attendees->count() }}</span>
                    </div>
                    @if($liveSession->started_at)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500">بدأت</span>
                        <span class="text-xs text-slate-600 dark:text-slate-300">{{ $liveSession->started_at->format('H:i') }}</span>
                    </div>
                    @endif
                    @if($liveSession->ended_at)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500">انتهت</span>
                        <span class="text-xs text-slate-600 dark:text-slate-300">{{ $liveSession->ended_at->format('H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Recordings --}}
            @if($liveSession->recordings->count() > 0)
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5">
                <h3 class="font-bold text-slate-800 dark:text-white mb-3 text-sm"><i class="fas fa-play-circle text-emerald-500 ml-1"></i> التسجيلات</h3>
                <div class="space-y-2">
                    @foreach($liveSession->recordings as $rec)
                    <div class="flex items-center justify-between p-2 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $rec->title ?? 'تسجيل' }}</p>
                            <p class="text-[11px] text-slate-500">{{ $rec->duration_for_humans }} • {{ $rec->file_size_for_humans }}</p>
                        </div>
                        @if($rec->getUrl())
                        <a href="{{ $rec->getUrl() }}" target="_blank" class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 text-blue-500"><i class="fas fa-external-link-alt text-xs"></i></a>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Delete --}}
            @if($liveSession->status !== 'live')
            <form method="POST" action="{{ route('admin.live-sessions.destroy', $liveSession) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذه الجلسة؟ سيتم حذف جميع بيانات الحضور والتسجيلات.')">
                @csrf @method('DELETE')
                <button class="w-full px-4 py-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors">
                    <i class="fas fa-trash-alt ml-1"></i> حذف الجلسة
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
