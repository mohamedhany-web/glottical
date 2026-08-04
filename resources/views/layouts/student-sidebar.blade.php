@php
    $user = auth()->user();
    $isStudent = $user && ($user->role === 'student' || strtolower((string) $user->role) === 'student');
    $coursesCount = $user ? $user->activeCourses()->count() : 0;
    $enrollments = $user ? $user->courseEnrollments()->whereIn('status', ['active', 'completed'])->get() : collect();
    $totalProgress = $enrollments->isEmpty() ? 0 : round($enrollments->avg('progress') ?? 0, 0);
    $publicCoursesUrl = url('/courses');
    $closeSidebar = 'if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)';

    $tbUpcoming = 0;
    if ($user && \Illuminate\Support\Facades\Schema::hasTable('tutoring_group_bookings')) {
        $tbUpcoming = \App\Models\TutoringGroupBooking::where('user_id', $user->id)
            ->where('status', 'confirmed')->where('starts_at', '>=', now())->count();
    }
    $studentLiveCount = 0;
    try {
        $studentLiveCount = \App\Models\LiveSession::where('status', 'live')->count();
    } catch (\Throwable $e) {
    }
@endphp

<div class="flex flex-col h-full">
    <div class="ins-sidebar-brand flex items-center gap-3 px-4 py-5 flex-shrink-0 relative">
        <button @click="if (window.innerWidth < 1024) sidebarOpen = false" type="button"
                class="lg:hidden absolute top-3 left-3 w-8 h-8 rounded-lg bg-white/15 text-white hover:bg-white/25 flex items-center justify-center transition-colors z-10"
                aria-label="إغلاق">
            <i class="fas fa-times text-xs"></i>
        </button>
        <div class="w-11 h-11 rounded-xl bg-[#F5B800] text-[#072A66] flex items-center justify-center flex-shrink-0 shadow-lg shadow-black/20">
            <i class="fas fa-language text-lg"></i>
        </div>
        <div class="flex-1 min-w-0 relative z-10">
            <h2 class="text-base font-extrabold text-white leading-tight truncate">{{ config('app.name') }}</h2>
            <p class="text-[11px] text-white/70 font-medium mt-0.5">{{ __('student.learning_center') }}</p>
        </div>
    </div>

    <div class="px-3 py-3 flex-shrink-0">
        <div class="rounded-2xl border border-[#E8EEF8] dark:border-gray-700 bg-[#F4F7FC] dark:bg-gray-800/80 p-3">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-[11px] font-bold text-[#5B6577] dark:text-gray-400">{{ __('student.total_progress') }}</span>
                <span class="text-sm font-black text-[#0B3D91] dark:text-blue-300 tabular-nums">{{ $totalProgress }}%</span>
            </div>
            <div class="h-1.5 rounded-full bg-[#E8EEF8] dark:bg-gray-700 overflow-hidden">
                <div class="h-full rounded-full bg-[#F5B800]" style="width: {{ min(100, (int) $totalProgress) }}%"></div>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-2">
                <a href="{{ $publicCoursesUrl }}" class="rounded-xl bg-white dark:bg-gray-900 border border-[#E8EEF8] dark:border-gray-700 px-2.5 py-2 text-center hover:border-[#0B3D91]/30 transition-colors">
                    <p class="text-lg font-black text-[#0B3D91] dark:text-blue-300 tabular-nums leading-none">{{ $coursesCount }}</p>
                    <p class="text-[10px] font-bold text-[#8A94A6] mt-1">{{ __('student.courses') }}</p>
                </a>
                <a href="{{ Route::has('student.tutoring-bookings.index') ? route('student.tutoring-bookings.index') : route('my-courses.index') }}"
                   class="rounded-xl bg-white dark:bg-gray-900 border border-[#E8EEF8] dark:border-gray-700 px-2.5 py-2 text-center hover:border-[#F5B800]/50 transition-colors">
                    <p class="text-lg font-black text-[#8A6A00] tabular-nums leading-none">{{ $tbUpcoming }}</p>
                    <p class="text-[10px] font-bold text-[#8A94A6] mt-1">حصص قادمة</p>
                </a>
            </div>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto sidebar-scroll px-0 py-1 space-y-0.5 min-h-0">
        @if($isStudent || ($user && $user->hasAnyPermission('student.view.courses', 'student.view.my-courses', 'student.view.orders', 'student.view.invoices', 'student.view.wallet', 'student.view.certificates', 'student.view.achievements', 'student.view.exams', 'student.view.calendar', 'student.view.notifications', 'student.view.profile', 'student.view.settings')))

            <div class="ins-nav-group">
                <span><i class="fas fa-home text-[9px] opacity-50"></i> الرئيسية</span>
            </div>
            <a href="{{ route('dashboard') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-th-large"></i></span>
                <span class="flex-1 truncate">{{ __('student.dashboard') }}</span>
            </a>

            <div class="ins-nav-group mt-2">
                <span><i class="fas fa-school text-[9px] opacity-50"></i> 🏫 {{ app()->getLocale() === 'ar' ? 'مدرستي' : 'My School' }}</span>
            </div>

            @if(Route::has('student.school.index'))
            <a href="{{ route('student.school.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('student.school.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-school"></i></span>
                <span class="flex-1 truncate">{{ app()->getLocale() === 'ar' ? 'مدرستي' : 'My School' }}</span>
            </a>
            @endif

            @if(Route::has('student.tutoring-bookings.index'))
            <a href="{{ route('student.tutoring-bookings.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('student.tutoring-bookings.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-users"></i></span>
                <span class="flex-1 truncate">{{ app()->getLocale() === 'ar' ? 'حصص المدرسة' : 'School sessions' }}</span>
                @if($tbUpcoming > 0)<span class="ins-nav-badge">{{ $tbUpcoming }}</span>@endif
            </a>
            @endif

            @if(Route::has('student.tutoring-subscriptions.index'))
            <a href="{{ route('student.tutoring-subscriptions.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('student.tutoring-subscriptions.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-box-open"></i></span>
                <span class="flex-1 truncate">{{ app()->getLocale() === 'ar' ? 'باقات المدرسة' : 'School packages' }}</span>
            </a>
            @endif

            <div class="ins-nav-group mt-2">
                <span><i class="fas fa-chalkboard-teacher text-[9px] opacity-50"></i> 👨‍🏫 {{ app()->getLocale() === 'ar' ? 'حصصي الخاصة' : 'My Private Lessons' }}</span>
            </div>

            @if(Route::has('student.private-lectures.index'))
            <a href="{{ route('student.private-lectures.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('student.private-lectures.*') || request()->routeIs('student.private-messages.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-chalkboard-teacher"></i></span>
                <span class="flex-1 truncate">{{ app()->getLocale() === 'ar' ? 'الحصص الخاصة' : 'Private Lessons' }}</span>
            </a>
            @endif

            @if(Route::has('student.private-messages.index'))
            <a href="{{ route('student.private-messages.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('student.private-messages.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-comments"></i></span>
                <span class="flex-1 truncate">{{ app()->getLocale() === 'ar' ? 'رسائل المعلم' : 'Teacher messages' }}</span>
            </a>
            @endif

            @if(Route::has('student.one-to-one-sessions.index'))
            <a href="{{ route('student.one-to-one-sessions.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('student.one-to-one-sessions.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-user-graduate"></i></span>
                <span class="flex-1 truncate">{{ __('student.one_to_one_sessions_nav') }}</span>
            </a>
            @endif

            @if(Route::has('student.live-sessions.index'))
            <a href="{{ route('student.live-sessions.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('student.live-sessions.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-broadcast-tower"></i></span>
                <span class="flex-1 truncate">البث المباشر</span>
                @if($studentLiveCount > 0)
                    <span class="ins-nav-badge">{{ $studentLiveCount }}</span>
                @endif
            </a>
            @endif

            @if(Route::has('student.live-recordings.index'))
            <a href="{{ route('student.live-recordings.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('student.live-recordings.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-play-circle"></i></span>
                <span class="flex-1 truncate">تسجيلات البث</span>
            </a>
            @endif

            @if(Route::has('consultations.index') && $isStudent)
            <a href="{{ route('consultations.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('consultations.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-comments"></i></span>
                <span class="flex-1 truncate">استشارات المدربين</span>
            </a>
            @endif

            <div class="ins-nav-group mt-2">
                <span><i class="fas fa-book-open text-[9px] opacity-50"></i> كورساتي</span>
            </div>

            @if($isStudent || $user->hasPermission('student.view.courses'))
            @php $catalogActive = request()->routeIs('public.courses', 'public.course.*') || request()->routeIs('academic-years*') || request()->routeIs('subjects.*') || request()->routeIs('courses.show'); @endphp
            <a href="{{ route('public.courses') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ $catalogActive ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-compass"></i></span>
                <span class="flex-1 truncate">{{ __('student.browse_courses') }}</span>
            </a>
            @endif

            @if($isStudent || $user->hasPermission('student.view.my-courses'))
            <a href="{{ route('my-courses.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('my-courses.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-bookmark"></i></span>
                <span class="flex-1 truncate">{{ __('student.my_courses') }}</span>
                @if($coursesCount > 0)<span class="ins-nav-badge">{{ $coursesCount }}</span>@endif
            </a>
            <a href="{{ route('student.my-course-subscriptions') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('student.my-course-subscriptions') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-calendar-check"></i></span>
                <span class="flex-1 truncate">{{ __('student.course_subscriptions_nav') }}</span>
            </a>
            @endif

            <div class="ins-nav-group mt-2">
                <span><i class="fas fa-chart-line text-[9px] opacity-50"></i> متابعة وإنجاز</span>
            </div>

            @if($isStudent || $user->hasPermission('student.view.exams'))
            <a href="{{ route('student.exams.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('student.exams.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-clipboard-check"></i></span>
                <span class="flex-1 truncate">{{ __('student.exams') }}</span>
            </a>
            @endif

            @if($isStudent)
            <a href="{{ route('student.assignments.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('student.assignments.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-tasks"></i></span>
                <span class="flex-1 truncate">واجباتي</span>
            </a>
            @endif

            @if($isStudent || $user->hasPermission('student.view.certificates'))
            <a href="{{ route('student.certificates.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('student.certificates.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-award"></i></span>
                <span class="flex-1 truncate">{{ __('student.certificates') }}</span>
            </a>
            @endif

            @if($isStudent || $user->hasPermission('student.view.calendar'))
            <a href="{{ route('calendar') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('calendar') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-calendar-alt"></i></span>
                <span class="flex-1 truncate">{{ __('student.calendar') }}</span>
            </a>
            @endif

            @if($isStudent || $user->hasPermission('student.view.orders'))
            <a href="{{ route('orders.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-receipt"></i></span>
                <span class="flex-1 truncate">{{ __('student.orders') }}</span>
            </a>
            @endif

            @if($isStudent || $user->hasPermission('student.view.wallet'))
            <a href="{{ route('student.wallet.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('student.wallet.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-wallet"></i></span>
                <span class="flex-1 truncate">{{ __('student.wallet') }}</span>
            </a>
            @endif

            @if($isStudent && Route::has('referrals.index'))
            <a href="{{ route('referrals.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('referrals.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-user-friends"></i></span>
                <span class="flex-1 truncate">برنامج الإحالات</span>
            </a>
            @endif

            @if($isStudent || $user->hasPermission('student.view.notifications'))
            <a href="{{ route('notifications') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('notifications') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-bell"></i></span>
                <span class="flex-1 truncate">{{ __('student.notifications') }}</span>
            </a>
            @endif

            <div class="ins-nav-group mt-2">
                <span><i class="fas fa-user text-[9px] opacity-50"></i> الحساب</span>
            </div>

            @if($isStudent || $user->hasPermission('student.view.profile'))
            <a href="{{ route('profile') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('profile') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-user"></i></span>
                <span class="flex-1 truncate">{{ __('student.profile') }}</span>
            </a>
            @endif

            @if($isStudent || $user->hasPermission('student.view.settings'))
            <a href="{{ route('settings') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('settings') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-cog"></i></span>
                <span class="flex-1 truncate">{{ __('student.settings') }}</span>
            </a>
            @endif
        @endif

        @if($user && ($user->isAdmin() || $user->isInstructor()))
            <div class="ins-nav-group mt-2">
                <span><i class="fas fa-exchange-alt text-[9px] opacity-50"></i> لوحة أخرى</span>
            </div>
            @if($user->isAdmin())
                <a href="{{ route('admin.dashboard') }}" @click="{{ $closeSidebar }}"
                   class="ins-nav {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="ins-icon"><i class="fas fa-shield-alt"></i></span>
                    <span class="flex-1 truncate">{{ __('student.admin_panel') }}</span>
                </a>
            @endif
            @if($user->isInstructor())
                <a href="{{ route('dashboard') }}" @click="{{ $closeSidebar }}" class="ins-nav">
                    <span class="ins-icon"><i class="fas fa-chalkboard-teacher"></i></span>
                    <span class="flex-1 truncate">لوحة المعلم</span>
                </a>
            @endif
        @endif
    </nav>

    <div class="px-3 py-3 flex-shrink-0 border-t border-[#E8EEF8] dark:border-gray-700/80">
        <div class="ins-user-card flex items-center gap-3">
            <div class="u-avatar flex-shrink-0 w-10 h-10 rounded-xl">
                @if($user?->profile_image)
                    <img src="{{ $user->profile_image_url }}" alt="" class="w-full h-full object-cover rounded-xl">
                @else
                    {{ mb_substr($user?->name ?? 'U', 0, 1) }}
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate leading-tight">{{ $user?->name }}</p>
                <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate mt-0.5">
                    @if($user?->isAdmin()) {{ __('student.admin_role') }}
                    @elseif($user?->isInstructor()) {{ __('student.instructor_role') }}
                    @else {{ __('student.student_role') }}
                    @endif
                </p>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                @csrf
                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 text-red-500 dark:text-red-400 flex items-center justify-center transition-colors" title="تسجيل الخروج">
                    <i class="fas fa-sign-out-alt text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</div>
