@extends('layouts.admin')

@section('title', 'لوحة الإدارة - Glottical')
@section('page_title', 'لوحة الإدارة')

@section('content')
<div class="space-y-8">

    {{-- Welcome --}}
    <div class="animate-fade-in">
        <h2 class="text-2xl font-heading font-bold text-navy-800">مرحباً، {{ auth()->user()->name }}</h2>
        <p class="text-slate-500 text-sm mt-1">نظرة عامة على أداء المنصة اليوم — تظهر الأرقام والأقسام حسب صلاحيات دورك</p>
    </div>

    @php
        $ds = $dashboardShow ?? [];
        $hasAnyDashboardWidget = collect($ds)->filter(fn ($v) => (bool) $v)->isNotEmpty();
    @endphp
    @if(isset($dashboardShow) && ! $hasAnyDashboardWidget)
        <div class="rounded-xl border border-amber-200 bg-amber-50/80 px-4 py-3 text-sm text-amber-900">
            <i class="fas fa-info-circle ml-1"></i>
            لا توجد بطاقات إحصائية لعرضها حالياً. اطلب من المسؤول إسناد الصلاحيات المناسبة لدورك (مثل الكورسات، الطلبات، الفواتير، …).
        </div>
    @endif

    {{-- Quick Stats Row 1 --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @if(!empty($ds['users_metric']))
        @php $usersMetric = $metrics['users'] ?? null; $usersTrend = $usersMetric['trend'] ?? null; @endphp
        <div class="stat-card animate-fade-in animate-fade-in-1" style="--before-bg: #6366f1;">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 mb-1">إجمالي المستخدمين</p>
                    <p class="text-3xl font-heading font-bold text-slate-800">{{ number_format($usersMetric['total'] ?? 0) }}</p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-brand to-blue-600 shadow-lg shadow-brand/20">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-sm">
                <span class="text-slate-400">هذا الشهر:</span>
                <span class="font-semibold text-slate-700">{{ number_format($usersMetric['new_this_month'] ?? 0) }}</span>
                @if($usersTrend)
                    @php $percent = $usersTrend['percent']; $positive = $percent >= 0; @endphp
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $positive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                        {{ $positive ? '+' : '' }}{{ number_format($percent, 1) }}%
                    </span>
                @endif
            </div>
        </div>
        @endif

        @if(!empty($ds['students_metric']))
        @php $studentsMetric = $metrics['students'] ?? null; $studentsTrend = $studentsMetric['trend'] ?? null; @endphp
        <div class="stat-card animate-fade-in animate-fade-in-2">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 mb-1">الطلاب</p>
                    <p class="text-3xl font-heading font-bold text-slate-800">{{ number_format($studentsMetric['total'] ?? 0) }}</p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-emerald-500 to-emerald-600 shadow-lg shadow-emerald-500/25">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-sm">
                <span class="text-slate-400">هذا الشهر:</span>
                <span class="font-semibold text-slate-700">{{ number_format($studentsMetric['new_this_month'] ?? 0) }}</span>
                @if($studentsTrend)
                    @php $percent = $studentsTrend['percent']; $positive = $percent >= 0; @endphp
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $positive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                        {{ $positive ? '+' : '' }}{{ number_format($percent, 1) }}%
                    </span>
                @endif
            </div>
        </div>
        @endif

        @if(!empty($ds['instructors_metric']))
        @php $instructorsMetric = $metrics['instructors'] ?? null; $instructorsTrend = $instructorsMetric['trend'] ?? null; @endphp
        <div class="stat-card animate-fade-in animate-fade-in-3">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 mb-1">المدربين</p>
                    <p class="text-3xl font-heading font-bold text-slate-800">{{ number_format($instructorsMetric['total'] ?? 0) }}</p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-violet-500 to-violet-600 shadow-lg shadow-violet-500/25">
                    <i class="fas fa-user-tie"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-sm">
                <span class="text-slate-400">هذا الشهر:</span>
                <span class="font-semibold text-slate-700">{{ number_format($instructorsMetric['new_this_month'] ?? 0) }}</span>
                @if($instructorsTrend)
                    @php $percent = $instructorsTrend['percent']; $positive = $percent >= 0; @endphp
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $positive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                        {{ $positive ? '+' : '' }}{{ number_format($percent, 1) }}%
                    </span>
                @endif
            </div>
        </div>
        @endif

        @if(!empty($ds['courses_metric']))
        @php $coursesMetric = $metrics['courses'] ?? null; $coursesTrend = $coursesMetric['trend'] ?? null; @endphp
        <div class="stat-card animate-fade-in animate-fade-in-4">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 mb-1">الكورسات</p>
                    <p class="text-3xl font-heading font-bold text-slate-800">{{ number_format($coursesMetric['total'] ?? 0) }}</p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-amber-500 to-orange-500 shadow-lg shadow-amber-500/25">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-sm">
                <span class="text-slate-400">هذا الشهر:</span>
                <span class="font-semibold text-slate-700">{{ number_format($coursesMetric['new_this_month'] ?? 0) }}</span>
                @if($coursesTrend)
                    @php $percent = $coursesTrend['percent']; $positive = $percent >= 0; @endphp
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $positive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                        {{ $positive ? '+' : '' }}{{ number_format($percent, 1) }}%
                    </span>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Financial Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @if(!empty($ds['revenue_total']))
        <div class="stat-card animate-fade-in">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 mb-1">إجمالي الإيرادات</p>
                    <p class="text-2xl font-heading font-bold text-slate-800">{{ number_format($stats['total_revenue'] ?? 0, 2) }} <span class="text-base font-normal text-slate-400">ج.م</span></p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
        @endif

        @if(!empty($ds['monthly_revenue']))
        @php $revenueMetric = $metrics['monthly_revenue'] ?? null; $revenueTrend = $revenueMetric['trend'] ?? null; @endphp
        <div class="stat-card animate-fade-in">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 mb-1">إيرادات الشهر</p>
                    <p class="text-2xl font-heading font-bold text-slate-800">{{ number_format($revenueMetric['current'] ?? 0, 2) }} <span class="text-base font-normal text-slate-400">ج.م</span></p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-sky-500 to-blue-600 shadow-lg shadow-sky-500/25">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            @if($revenueTrend)
                @php $diff = $revenueTrend['difference']; $percent = $revenueTrend['percent']; $positive = $diff >= 0; @endphp
                <div class="mt-3 flex items-center gap-2 text-sm">
                    <span class="font-semibold {{ $positive ? 'text-emerald-600' : 'text-rose-500' }}">{{ $positive ? '+' : '' }}{{ number_format($diff, 2) }} ج.م</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $positive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                        {{ $percent >= 0 ? '+' : '' }}{{ number_format($percent, 1) }}%
                    </span>
                </div>
            @endif
        </div>
        @endif

        @if(!empty($ds['pending_invoices_metric']))
        @php $pendingMetric = $metrics['pending_invoices'] ?? null; $pendingTrend = $pendingMetric['trend'] ?? null; @endphp
        <div class="stat-card animate-fade-in">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 mb-1">فواتير معلقة</p>
                    <p class="text-3xl font-heading font-bold text-slate-800">{{ number_format($pendingMetric['total'] ?? 0) }}</p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-amber-400 to-orange-500 shadow-lg shadow-amber-500/25">
                    <i class="fas fa-file-invoice"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-sm">
                <span class="text-slate-400">هذا الشهر:</span>
                <span class="font-semibold text-slate-700">{{ number_format($pendingMetric['new_this_month'] ?? 0) }}</span>
            </div>
        </div>
        @endif

        @if(!empty($ds['enrollments_metric']))
        @php $enrollmentsMetric = $metrics['enrollments'] ?? null; $enrollmentsTrend = $enrollmentsMetric['trend'] ?? null; @endphp
        <div class="stat-card animate-fade-in">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-500 mb-1">التسجيلات النشطة</p>
                    <p class="text-3xl font-heading font-bold text-slate-800">{{ number_format($enrollmentsMetric['total'] ?? 0) }}</p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-fuchsia-500 to-purple-600 shadow-lg shadow-fuchsia-500/25">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2 text-sm">
                <span class="text-slate-400">هذا الشهر:</span>
                <span class="font-semibold text-slate-700">{{ number_format($enrollmentsMetric['new_this_month'] ?? 0) }}</span>
                @if($enrollmentsTrend)
                    @php $percent = $enrollmentsTrend['percent']; $positive = $percent >= 0; @endphp
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $positive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                        {{ $positive ? '+' : '' }}{{ number_format($percent, 1) }}%
                    </span>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Activity & Exams --}}
    @if(!empty($ds['activity_feed']) || !empty($ds['exam_attempts']))
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @if(!empty($ds['activity_feed']))
        <div class="section-card animate-fade-in">
            <div class="section-card-header">
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-history text-indigo-500"></i> آخر النشاطات
                </h3>
            </div>
            <div>
                @if(isset($stats['recent_activities']) && $stats['recent_activities']->count() > 0)
                    @foreach($stats['recent_activities']->take(5) as $activity)
                        <div class="list-row">
                            <div class="w-9 h-9 bg-indigo-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-history text-indigo-500 text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-700 truncate">{{ $activity->user->name ?? 'مستخدم محذوف' }}</p>
                                <p class="text-xs text-slate-400">{{ $activity->action }} &middot; {{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                    <div class="px-6 py-3 border-t border-slate-100">
                        <a href="{{ route('admin.activity-log') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">
                            عرض جميع النشاطات <i class="fas fa-arrow-left text-[10px]"></i>
                        </a>
                    </div>
                @else
                    <div class="py-12 text-center">
                        <i class="fas fa-history text-3xl text-slate-200 mb-2"></i>
                        <p class="text-sm text-slate-400">لا توجد أنشطة بعد</p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        @if(!empty($ds['exam_attempts']))
        <div class="section-card animate-fade-in">
            <div class="section-card-header">
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-clipboard-check text-violet-500"></i> آخر محاولات الامتحانات
                </h3>
            </div>
            <div>
                @if(isset($stats['recent_exam_attempts']) && $stats['recent_exam_attempts']->count() > 0)
                    @foreach($stats['recent_exam_attempts']->take(5) as $attempt)
                        <div class="list-row">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-700">{{ $attempt->student->name ?? 'طالب محذوف' }}</p>
                                <p class="text-xs text-slate-400">{{ $attempt->exam->title ?? 'امتحان محذوف' }}</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold flex-shrink-0
                                {{ $attempt->score >= 80 ? 'bg-emerald-50 text-emerald-600' : ($attempt->score >= 60 ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600') }}">
                                {{ $attempt->score }}%
                            </span>
                        </div>
                    @endforeach
                @else
                    <div class="py-12 text-center">
                        <i class="fas fa-clipboard-check text-3xl text-slate-200 mb-2"></i>
                        <p class="text-sm text-slate-400">لا توجد محاولات امتحانات بعد</p>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Recent Users & Courses --}}
    @if((!empty($ds['recent_users']) && isset($recent_users)) || (!empty($ds['recent_courses']) && isset($recent_courses)))
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @if(!empty($ds['recent_users']) && isset($recent_users))
        <div class="section-card animate-fade-in">
            <div class="section-card-header">
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-users text-sky-500"></i> آخر المستخدمين
                </h3>
                <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">
                    عرض الكل <i class="fas fa-arrow-left text-[10px]"></i>
                </a>
            </div>
            <div>
                @foreach($recent_users as $user)
                <div class="list-row">
                    <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        {{ mb_substr($user->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 truncate">{{ $user->name }}</p>
                        <p class="text-xs text-slate-400">{{ $user->phone ?? $user->email }}</p>
                    </div>
                    <div class="text-left flex-shrink-0">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                            @if($user->role === 'student') bg-emerald-50 text-emerald-600
                            @elseif($user->role === 'instructor') bg-violet-50 text-violet-600
                            @elseif($user->role === 'super_admin') bg-rose-50 text-rose-600
                            @else bg-slate-100 text-slate-500 @endif">
                            @if($user->role === 'student') طالب
                            @elseif($user->role === 'instructor') مدرب
                            @elseif($user->role === 'super_admin') مدير عام
                            @else غير محدد @endif
                        </span>
                        <p class="text-[11px] text-slate-300 mt-0.5">{{ $user->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if(!empty($ds['recent_courses']) && isset($recent_courses))
        <div class="section-card animate-fade-in">
            <div class="section-card-header">
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-book text-amber-500"></i> آخر الكورسات
                </h3>
                <a href="{{ route('admin.advanced-courses.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">
                    عرض الكل <i class="fas fa-arrow-left text-[10px]"></i>
                </a>
            </div>
            <div>
                @forelse($recent_courses as $course)
                <div class="list-row">
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-book text-white text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 truncate">{{ $course->title }}</p>
                        <p class="text-xs text-slate-400">{{ $course->academicSubject->name ?? 'غير محدد' }}</p>
                    </div>
                    <div class="text-left flex-shrink-0">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                            @if($course->is_active) bg-emerald-50 text-emerald-600
                            @else bg-slate-100 text-slate-500 @endif">
                            @if($course->is_active) نشط @else غير نشط @endif
                        </span>
                        <p class="text-[11px] text-slate-300 mt-0.5">{{ $course->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center">
                    <i class="fas fa-book text-3xl text-slate-200 mb-2"></i>
                    <p class="text-sm text-slate-400">لا توجد كورسات بعد</p>
                </div>
                @endforelse
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- قسم المبيعات / قسم الموارد البشرية --}}
    @if(!empty($salesSection) || !empty($hrSection))
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @if(!empty($salesSection))
        <div class="section-card animate-fade-in">
            <div class="section-card-header">
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-shopping-cart text-emerald-500"></i> قسم المبيعات
                </h3>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">
                    عرض الكل <i class="fas fa-arrow-left text-[10px]"></i>
                </a>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
                        <p class="text-xs text-slate-500 font-medium">طلبات معلقة</p>
                        <p class="text-xl font-heading font-bold text-slate-800">{{ $salesSection['orders_pending'] }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
                        <p class="text-xs text-slate-500 font-medium">معتمدة هذا الشهر</p>
                        <p class="text-xl font-heading font-bold text-slate-800">{{ $salesSection['orders_approved_month'] }}</p>
                    </div>
                    <div class="col-span-2 rounded-xl bg-emerald-50 p-3 border border-emerald-100">
                        <p class="text-xs text-emerald-600 font-medium">إيرادات الشهر (طلبات معتمدة)</p>
                        <p class="text-xl font-heading font-bold text-emerald-700">{{ number_format($salesSection['revenue_month'] ?? 0, 2) }} ج.م</p>
                    </div>
                </div>
                <p class="text-xs font-semibold text-slate-500 mb-2">آخر الطلبات</p>
                @forelse($salesSection['recent_orders'] ?? [] as $order)
                <div class="list-row">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 truncate">{{ $order->user->name ?? '—' }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ $order->course->title ?? '—' }}</p>
                    </div>
                    <div class="text-left flex-shrink-0">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                            @if($order->status === 'approved') bg-emerald-50 text-emerald-600
                            @elseif($order->status === 'pending') bg-amber-50 text-amber-600
                            @else bg-rose-50 text-rose-600 @endif">
                            @if($order->status === 'approved') معتمد
                            @elseif($order->status === 'pending') معلق
                            @else مرفوض @endif
                        </span>
                        <p class="text-sm font-bold text-slate-700 mt-0.5">{{ number_format($order->amount ?? 0, 0) }} ج.م</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-slate-400 py-4 text-center">لا توجد طلبات حديثة</p>
                @endforelse
            </div>
        </div>
        @endif

        @if(!empty($hrSection))
        <div class="section-card animate-fade-in">
            <div class="section-card-header">
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-users-cog text-indigo-500"></i> قسم الموارد البشرية
                </h3>
                <a href="{{ route('admin.employees.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">
                    عرض الكل <i class="fas fa-arrow-left text-[10px]"></i>
                </a>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
                        <p class="text-xs text-slate-500 font-medium">إجمالي الموظفين</p>
                        <p class="text-xl font-heading font-bold text-slate-800">{{ $hrSection['employees_total'] }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
                        <p class="text-xs text-slate-500 font-medium">طلبات إجازة معلقة</p>
                        <p class="text-xl font-heading font-bold text-slate-800">{{ $hrSection['leaves_pending'] }}</p>
                    </div>
                    <div class="col-span-2 rounded-xl bg-indigo-50 p-3 border border-indigo-100">
                        <p class="text-xs text-indigo-600 font-medium">إجازات معتمدة هذا الشهر</p>
                        <p class="text-xl font-heading font-bold text-indigo-700">{{ $hrSection['leaves_approved_month'] }}</p>
                    </div>
                </div>
                <p class="text-xs font-semibold text-slate-500 mb-2">آخر طلبات الإجازة</p>
                @forelse($hrSection['recent_leaves'] ?? [] as $leave)
                <div class="list-row">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 truncate">{{ $leave->employee->name ?? '—' }}</p>
                        <p class="text-xs text-slate-400">{{ $leave->days ?? 0 }} يوم · {{ optional($leave->employee->employeeJob)->name ?? '—' }}</p>
                    </div>
                    <div class="text-left flex-shrink-0">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                            @if($leave->status === 'approved') bg-emerald-50 text-emerald-600
                            @elseif($leave->status === 'pending') bg-amber-50 text-amber-600
                            @else bg-rose-50 text-rose-600 @endif">
                            @if($leave->status === 'approved') معتمد
                            @elseif($leave->status === 'pending') معلق
                            @else مرفوض @endif
                        </span>
                        <p class="text-[11px] text-slate-300 mt-0.5">{{ $leave->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-slate-400 py-4 text-center">لا توجد طلبات إجازة حديثة</p>
                @endforelse
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <p class="text-xs font-semibold text-slate-500 mb-2">آخر الموظفين المضافة</p>
                    @forelse($hrSection['recent_employees'] ?? [] as $emp)
                    <div class="list-row">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-700 truncate">{{ $emp->name }}</p>
                            <p class="text-xs text-slate-400">{{ optional($emp->employeeJob)->name ?? 'موظف' }}</p>
                        </div>
                        <p class="text-[11px] text-slate-300">{{ $emp->hire_date ? $emp->hire_date->diffForHumans() : $emp->created_at->diffForHumans() }}</p>
                    </div>
                    @empty
                    <p class="text-sm text-slate-400 py-2 text-center">لا يوجد موظفون</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Invoices & Payments --}}
    @if((!empty($ds['invoices_panel']) && isset($pending_invoices) && $pending_invoices->count() > 0) || (!empty($ds['payments_panel']) && isset($recent_payments) && $recent_payments->count() > 0))
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @if(!empty($ds['invoices_panel']) && isset($pending_invoices) && $pending_invoices->count() > 0)
        <div class="section-card animate-fade-in">
            <div class="section-card-header">
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-file-invoice text-amber-500"></i> الفواتير المعلقة
                </h3>
                <a href="#" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">عرض الكل <i class="fas fa-arrow-left text-[10px]"></i></a>
            </div>
            <div>
                @foreach($pending_invoices as $invoice)
                <div class="list-row">
                    <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-file-invoice text-amber-500 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 truncate">{{ $invoice->invoice_number ?? 'غير محدد' }}</p>
                        <p class="text-xs text-slate-400">{{ $invoice->user->name ?? 'غير محدد' }}</p>
                    </div>
                    <div class="text-left flex-shrink-0">
                        <p class="text-sm font-bold text-slate-700">{{ number_format($invoice->total_amount ?? 0, 2) }} ج.م</p>
                        <p class="text-[11px] text-slate-300">{{ $invoice->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if(!empty($ds['payments_panel']) && isset($recent_payments) && $recent_payments->count() > 0)
        <div class="section-card animate-fade-in">
            <div class="section-card-header">
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-money-bill-wave text-emerald-500"></i> المدفوعات الأخيرة
                </h3>
                <a href="#" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">عرض الكل <i class="fas fa-arrow-left text-[10px]"></i></a>
            </div>
            <div>
                @foreach($recent_payments as $payment)
                <div class="list-row">
                    <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check-circle text-emerald-500 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 truncate">{{ $payment->payment_number ?? 'غير محدد' }}</p>
                        <p class="text-xs text-slate-400">{{ $payment->user->name ?? 'غير محدد' }}</p>
                    </div>
                    <div class="text-left flex-shrink-0">
                        <p class="text-sm font-bold text-emerald-600">{{ number_format($payment->amount ?? 0, 2) }} ج.م</p>
                        <p class="text-[11px] text-slate-300">{{ $payment->paid_at ? $payment->paid_at->diffForHumans() : $payment->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- قسم العناصر المدفوعة: الاشتراكات والباقات --}}
    @if(!empty($subscriptionPackages))
    <div class="section-card animate-fade-in">
        <div class="section-card-header">
            <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-crown text-amber-500"></i> العناصر المدفوعة — الاشتراكات
            </h3>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.subscriptions.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">
                    عرض كل الاشتراكات <i class="fas fa-arrow-left text-[10px]"></i>
                </a>
                <a href="{{ route('admin.subscriptions.create') }}" class="text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1.5 rounded-lg transition-colors">
                    إضافة اشتراك
                </a>
            </div>
        </div>
        <div class="p-5">
            @if($subscriptionPackages->count() > 0)
            <p class="text-xs text-slate-500 mb-4">الباقات المتاحة وجميع المشتركين في كل باقة. يمكنك الدخول على أي مشترك لإدارة اشتراكه ومراجعة بياناته بالكامل.</p>
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($subscriptionPackages as $package)
                <div class="rounded-2xl border border-slate-200 bg-slate-50/50 overflow-hidden">
                    <div class="px-4 py-3 bg-white border-b border-slate-200 flex items-center justify-between">
                        <h4 class="text-sm font-bold text-slate-800">{{ $package['plan_name'] }}</h4>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                            <i class="fas fa-users text-[10px]"></i>
                            {{ $package['count'] }} مشترك
                        </span>
                    </div>
                    <div class="p-3 max-h-64 overflow-y-auto space-y-1.5">
                        @forelse($package['subscriptions'] as $sub)
                        <div class="flex items-center justify-between gap-2 rounded-xl border border-slate-100 bg-white px-3 py-2 hover:border-indigo-200 transition-colors">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-700 truncate">{{ $sub->user->name ?? '—' }}</p>
                                <p class="text-[11px] text-slate-400">{{ $sub->user->phone ?? $sub->user->email ?? '—' }}</p>
                            </div>
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <a href="{{ route('admin.subscriptions.show', $sub) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-sky-100 text-sky-600 hover:bg-sky-200 transition-colors text-xs" title="تفاصيل الاشتراك والتحكم">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($sub->user)
                                <a href="{{ route('admin.users.show', $sub->user->id) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors text-xs" title="بيانات المستخدم">
                                    <i class="fas fa-user"></i>
                                </a>
                                @endif
                            </div>
                        </div>
                        @empty
                        <p class="text-xs text-slate-400 py-2 text-center">لا مشتركين في هذه الباقة</p>
                        @endforelse
                    </div>
                    @if($package['count'] > $package['subscriptions']->count())
                    <div class="px-4 py-2 border-t border-slate-100 bg-slate-50/80 text-center">
                        <a href="{{ route('admin.subscriptions.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                            عرض كل الاشتراكات ({{ $package['count'] }} في هذه الباقة) <i class="fas fa-arrow-left text-[10px]"></i>
                        </a>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <div class="py-10 text-center rounded-xl bg-slate-50 border border-slate-100">
                <i class="fas fa-layer-group text-3xl text-slate-300 mb-2"></i>
                <p class="text-sm text-slate-500">لا توجد اشتراكات أو باقات حالياً.</p>
                <a href="{{ route('admin.subscriptions.create') }}" class="inline-flex items-center gap-2 mt-3 text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                    <i class="fas fa-plus"></i> إضافة اشتراك جديد
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Quick Actions --}}
    <div class="section-card animate-fade-in">
        <div class="section-card-header">
            <div>
                <h3 class="text-base font-heading font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-bolt text-amber-500"></i> إجراءات سريعة
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">روابط مباشرة للمهام اليومية</p>
            </div>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                @foreach(($quickActions ?? []) as $action)
                    <a href="{{ $action['route'] }}" class="group flex flex-col items-center gap-3 p-5 rounded-xl bg-slate-50/80 hover:bg-white border border-transparent hover:border-slate-200 hover:shadow-lg transition-all duration-200">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $action['icon_background'] }} flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                            <i class="{{ $action['icon'] }} text-white text-lg"></i>
                        </div>
                        <p class="text-xs font-semibold text-slate-600 text-center leading-relaxed">{{ $action['title'] }}</p>
                        @php $actionCount = $action['count'] ?? 0; @endphp
                        <p class="text-2xl font-heading font-bold text-slate-800">{{ number_format($actionCount) }}</p>
                        @if(!empty($action['meta']))
                            <p class="text-[11px] text-slate-400">{{ $action['meta'] }}</p>
                        @endif
                    </a>
                @endforeach
                @if(empty($quickActions))
                    <div class="col-span-full text-center py-8 text-slate-400 text-sm">
                        لا توجد مهام عاجلة حالياً
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
