<div class="flex flex-col h-full">
    {{-- Brand --}}
    <div class="ins-sidebar-brand flex items-center gap-3 px-4 py-4 flex-shrink-0 relative">
        <button @click="if (window.innerWidth < 1024) sidebarOpen = false"
                class="lg:hidden absolute top-3 left-3 w-8 h-8 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 flex items-center justify-center transition-colors z-10">
            <i class="fas fa-times text-xs"></i>
        </button>
        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-lg shadow-sky-500/20">
            <i class="fas fa-chalkboard-teacher text-lg"></i>
        </div>
        <div class="flex-1 min-w-0 relative z-10">
            <h2 class="text-base font-bold text-gray-900 dark:text-gray-100 leading-tight">{{ config('app.name') }}</h2>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-0.5">{{ __('instructor.instructor_panel') }}</p>
        </div>
    </div>

    {{-- Stats cards --}}
    @php
        $user = auth()->user();
        $directCourseIds = \App\Models\AdvancedCourse::where('instructor_id', $user->id)->pluck('id');
        $assignedFromPaths = $user->teachingLearningPaths()->get()->flatMap(fn($ay) => json_decode($ay->pivot->assigned_courses ?? '[]', true) ?: []);
        $teachingCourseIds = $directCourseIds->merge($assignedFromPaths)->unique()->filter()->values();
        $myCoursesCount = $teachingCourseIds->count();
        $totalStudents = $teachingCourseIds->isEmpty() ? 0 : \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $teachingCourseIds)->where('status', 'active')->distinct('user_id')->count('user_id');
    @endphp
    <div class="px-3 py-3 flex-shrink-0">
        <div class="grid grid-cols-2 gap-2.5">
            <a href="{{ route('instructor.courses.index') }}" class="ins-stat-card bg-white dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700/80 block group">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-book text-sm"></i>
                    </span>
                    <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">{{ __('instructor.courses') }}</span>
                </div>
                <div class="text-xl font-black text-gray-900 dark:text-gray-100 leading-none tabular-nums">{{ $myCoursesCount }}</div>
            </a>
            <a href="{{ route('instructor.courses.index') }}" class="ins-stat-card bg-white dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700/80 block group">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-user-graduate text-sm"></i>
                    </span>
                    <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">{{ __('instructor.students') }}</span>
                </div>
                <div class="text-xl font-black text-gray-900 dark:text-gray-100 leading-none tabular-nums">{{ $totalStudents }}</div>
            </a>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto sidebar-scroll px-0 py-2 space-y-0.5 min-h-0">
        @php
            $user = auth()->user();
            $isInstructor = $user->isInstructor() || $user->isTeacher() || strtolower($user->role) === 'teacher' || strtolower($user->role) === 'instructor';
        @endphp

        {{-- ─── نظرة عامة ─── --}}
        <div class="ins-nav-group">
            <span class="inline-flex items-center gap-1.5">
                <i class="fas fa-home text-[9px] opacity-50"></i>
                {{ __('instructor.overview') }}
            </span>
        </div>
        @if($isInstructor || $user->hasAnyPermission('instructor.view.courses', 'instructor.manage.lectures', 'instructor.manage.assignments', 'instructor.manage.exams', 'instructor.manage.attendance', 'instructor.view.tasks'))

            <a href="{{ route('dashboard') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="ins-icon bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400">
                    <i class="fas fa-th-large text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.dashboard') }}</span>
            </a>

            @if($isInstructor || $user->hasPermission('instructor.view.courses'))
            <a href="{{ route('instructor.courses.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.courses.*') ? 'active' : '' }}">
                <span class="ins-icon bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400">
                    <i class="fas fa-book-open text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.my_courses') }}</span>
                @if($myCoursesCount > 0)
                    <span class="ins-nav-badge bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300">{{ $myCoursesCount }}</span>
                @endif
            </a>
            @endif

            @if(Route::has('curriculum-library.index'))
            <a href="{{ route('curriculum-library.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('curriculum-library.*') ? 'active' : '' }}">
                <span class="ins-icon bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300">
                    <i class="fas fa-folder-open text-sm"></i>
                </span>
                <span class="flex-1 truncate">مكتبة المناهج</span>
            </a>
            @endif

            {{-- ─── أدوات التدريس ─── --}}
            <div class="ins-nav-group mt-3">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-tools text-[9px] opacity-50"></i>
                    أدوات التدريس
                </span>
            </div>

            @if($isInstructor || $user->hasPermission('instructor.manage.lectures'))
            <a href="{{ route('instructor.lectures.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.lectures.*') ? 'active' : '' }}">
                <span class="ins-icon bg-violet-100 dark:bg-violet-900/40 text-violet-600 dark:text-violet-400">
                    <i class="fas fa-chalkboard text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.lectures') }}</span>
            </a>
            @endif

            @if($isInstructor || $user->hasPermission('instructor.manage.assignments'))
            <a href="{{ route('instructor.assignments.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.assignments.*') ? 'active' : '' }}">
                <span class="ins-icon bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400">
                    <i class="fas fa-tasks text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.assignments') }}</span>
            </a>
            @endif

            @if($isInstructor || $user->hasPermission('instructor.manage.exams'))
            <a href="{{ route('instructor.exams.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.exams.*') ? 'active' : '' }}">
                <span class="ins-icon bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400">
                    <i class="fas fa-clipboard-check text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.exams') }}</span>
            </a>
            @endif

            @if($isInstructor)
            <a href="{{ route('instructor.question-banks.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.question-banks.*') || request()->routeIs('instructor.questions.*') ? 'active' : '' }}">
                <span class="ins-icon bg-teal-100 dark:bg-teal-900/40 text-teal-600 dark:text-teal-400">
                    <i class="fas fa-database text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.question_banks') }}</span>
            </a>
            @endif

            @if($isInstructor || $user->hasPermission('instructor.manage.attendance'))
            <a href="{{ route('instructor.attendance.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.attendance.*') ? 'active' : '' }}">
                <span class="ins-icon bg-cyan-100 dark:bg-cyan-900/40 text-cyan-600 dark:text-cyan-400">
                    <i class="fas fa-clipboard-list text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.attendance') }}</span>
            </a>
            @endif

            @if(Route::has('instructor.live-sessions.index'))
            <a href="{{ route('instructor.live-sessions.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.live-sessions.*') ? 'active' : '' }}">
                <span class="ins-icon bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400">
                    <i class="fas fa-broadcast-tower text-sm"></i>
                </span>
                <span class="flex-1 truncate">البث المباشر</span>
                @php $liveCount = \App\Models\LiveSession::where('instructor_id', auth()->id())->where('status', 'live')->count(); @endphp
                @if($liveCount > 0)
                    <span class="ins-nav-badge bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400">
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse inline-block ml-1"></span>{{ $liveCount }}
                    </span>
                @endif
            </a>
            @endif

            @if(Route::has('instructor.consultations.index'))
            <a href="{{ route('instructor.consultations.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.consultations.*') ? 'active' : '' }}">
                <span class="ins-icon bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400">
                    <i class="fas fa-comments-dollar text-sm"></i>
                </span>
                <span class="flex-1 truncate">استشارات الطلاب</span>
            </a>
            @endif
            @if(Route::has('instructor.one-to-one-sessions.index'))
            <a href="{{ route('instructor.one-to-one-sessions.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.one-to-one-sessions.*') ? 'active' : '' }}">
                <span class="ins-icon bg-violet-100 dark:bg-violet-900/40 text-violet-600 dark:text-violet-400">
                    <i class="fas fa-user-graduate text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('student.one_to_one_sessions_instructor_nav') }}</span>
            </a>
            @endif
            @if(Route::has('instructor.one-to-one-availability.index'))
            <a href="{{ route('instructor.one-to-one-availability.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.one-to-one-availability.*') ? 'active' : '' }}">
                <span class="ins-icon bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400">
                    <i class="fas fa-calendar-week text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('student.one_to_one_availability_title') }}</span>
            </a>
            @endif
            @if(Route::has('instructor.calendar'))
            <a href="{{ route('instructor.calendar') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.calendar') || request()->routeIs('instructor.calendar.events') ? 'active' : '' }}">
                <span class="ins-icon bg-teal-100 dark:bg-teal-900/40 text-teal-600 dark:text-teal-400">
                    <i class="fas fa-calendar-check text-sm"></i>
                </span>
                <span class="flex-1 truncate">تقويم الاستشارات</span>
            </a>
            @endif

            {{-- ─── الإدارة ─── --}}
            <div class="ins-nav-group mt-3">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-briefcase text-[9px] opacity-50"></i>
                    الإدارة
                </span>
            </div>

            @if($isInstructor || $user->hasPermission('instructor.view.tasks'))
            <a href="{{ route('instructor.tasks.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.tasks.*') ? 'active' : '' }}">
                <span class="ins-icon bg-orange-100 dark:bg-orange-900/40 text-orange-600 dark:text-orange-400">
                    <i class="fas fa-check-square text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.tasks_from_management') }}</span>
            </a>
            @endif

            @if(($isInstructor || $user->hasPermission('instructor.view.tasks')) && Route::has('instructor.management-requests.index'))
            <a href="{{ route('instructor.management-requests.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.management-requests.*') ? 'active' : '' }}">
                <span class="ins-icon bg-sky-100 dark:bg-sky-900/40 text-sky-600 dark:text-sky-400">
                    <i class="fas fa-paper-plane text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.submit_requests_to_management') }}</span>
            </a>
            @endif

            <a href="{{ route('instructor.agreements.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.agreements.*') ? 'active' : '' }}">
                <span class="ins-icon bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400">
                    <i class="fas fa-handshake text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.agreements_system') }}</span>
            </a>

            {{-- ─── المالية ─── --}}
            <div class="ins-nav-group mt-3">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-coins text-[9px] opacity-50"></i>
                    المالية
                </span>
            </div>

            <a href="{{ route('instructor.transfer-account.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.transfer-account.*') ? 'active' : '' }}">
                <span class="ins-icon bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400">
                    <i class="fas fa-university text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.transfer_account') }}</span>
            </a>

            <a href="{{ route('instructor.withdrawals.index') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.withdrawals.*') ? 'active' : '' }}">
                <span class="ins-icon bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400">
                    <i class="fas fa-money-bill-wave text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.withdrawal_requests') }}</span>
            </a>

            <a href="{{ route('instructor.personal-branding.edit') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('instructor.personal-branding.*') ? 'active' : '' }}">
                <span class="ins-icon bg-fuchsia-100 dark:bg-fuchsia-900/40 text-fuchsia-600 dark:text-fuchsia-400">
                    <i class="fas fa-user-tie text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.personal_branding') }}</span>
            </a>
        @endif

        {{-- ─── الحساب ─── --}}
        <div class="ins-nav-group mt-3">
            <span class="inline-flex items-center gap-1.5">
                <i class="fas fa-user-cog text-[9px] opacity-50"></i>
                الحساب
            </span>
        </div>

        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                <span class="ins-icon bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400">
                    <i class="fas fa-shield-alt text-sm"></i>
                </span>
                <span class="flex-1 truncate">{{ __('instructor.admin_panel') }}</span>
            </a>
        @endif

        <a href="{{ route('instructor.profile') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
           class="ins-nav {{ request()->routeIs('instructor.profile*') ? 'active' : '' }}">
            <span class="ins-icon bg-gray-100 dark:bg-gray-700/60 text-gray-600 dark:text-gray-400">
                <i class="fas fa-user text-sm"></i>
            </span>
            <span class="flex-1 truncate">{{ __('instructor.profile') }}</span>
        </a>

        @if(auth()->check() && auth()->user()->hasPermission('student.view.settings'))
        <a href="{{ route('settings') }}" @click="if(window.innerWidth<1024) sidebarOpen=false"
           class="ins-nav {{ request()->routeIs('settings') ? 'active' : '' }}">
            <span class="ins-icon bg-gray-100 dark:bg-gray-700/60 text-gray-600 dark:text-gray-400">
                <i class="fas fa-cog text-sm"></i>
            </span>
            <span class="flex-1 truncate">{{ __('instructor.settings') }}</span>
        </a>
        @endif
    </nav>

    {{-- User card --}}
    <div class="px-3 py-3 flex-shrink-0 border-t border-gray-200/80 dark:border-gray-700/80">
        <div class="ins-user-card flex items-center gap-3">
            <div class="u-avatar w-10 h-10 flex-shrink-0 rounded-xl">
                @if(auth()->user()->profile_image)
                    <img src="{{ auth()->user()->profile_image_url }}" alt="" class="w-full h-full object-cover rounded-xl">
                @else
                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate leading-tight">{{ auth()->user()->name }}</p>
                <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate mt-0.5">{{ __('instructor.instructor_role') }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                @csrf
                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 text-red-500 dark:text-red-400 flex items-center justify-center transition-colors" title="{{ __('instructor.logout') }}">
                    <i class="fas fa-sign-out-alt text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</div>
