<div class="flex flex-col h-full">
    
    <div class="ins-sidebar-brand flex items-center gap-3 px-4 py-4 flex-shrink-0 relative">
        <button @click="if (window.innerWidth < 1024) sidebarOpen = false"
                class="lg:hidden absolute top-3 left-3 w-8 h-8 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 flex items-center justify-center transition-colors z-10">
            <i class="fas fa-times text-xs"></i>
        </button>
        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-[#283593] to-[#FB5607] text-white flex items-center justify-center flex-shrink-0 shadow-lg shadow-[#283593]/25">
            <i class="fas fa-user-graduate text-lg"></i>
        </div>
        <div class="flex-1 min-w-0 relative z-10">
            <h2 class="text-base font-bold text-gray-900 dark:text-gray-100 leading-tight">Muallimx</h2>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-0.5"><?php echo e(__('student.learning_center')); ?></p>
        </div>
    </div>

    
    <?php
        $coursesCount = auth()->user()->activeCourses()->count();
        $enrollments = auth()->user()->courseEnrollments()->whereIn('status', ['active', 'completed'])->get();
        $totalProgress = $enrollments->isEmpty() ? 0 : round($enrollments->avg('progress') ?? 0, 0);
        /** صفحة الكورسات العامة (الواجهة الخارجية) */
        $publicCoursesUrl = url('/courses');
    ?>
    <div class="px-3 py-3 flex-shrink-0">
        <div class="grid grid-cols-2 gap-2.5">
            <a href="<?php echo e($publicCoursesUrl); ?>"
               title="<?php echo e(__('student.browse_courses')); ?>"
               class="ins-stat-card bg-white dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700/80 block group cursor-pointer no-underline text-inherit focus:outline-none focus-visible:ring-2 focus-visible:ring-[#283593] focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 rounded-xl">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-8 h-8 rounded-lg bg-[#FFE5F7] dark:bg-violet-900/40 text-[#283593] dark:text-violet-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-book-open text-sm"></i>
                    </span>
                    <span class="text-[10px] font-bold text-[#283593] dark:text-violet-400 uppercase tracking-wider"><?php echo e(__('student.courses')); ?></span>
                </div>
                <div class="text-xl font-black text-gray-900 dark:text-gray-100 leading-none tabular-nums"><?php echo e($coursesCount); ?></div>
            </a>
            <a href="<?php echo e(route('my-courses.index')); ?>" class="ins-stat-card bg-white dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700/80 block group">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-chart-line text-sm"></i>
                    </span>
                    <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider"><?php echo e(__('student.progress')); ?></span>
                </div>
                <div class="text-xl font-black text-gray-900 dark:text-gray-100 leading-none tabular-nums"><?php echo e($totalProgress); ?>%</div>
            </a>
        </div>
    </div>

    
    <nav class="flex-1 overflow-y-auto sidebar-scroll px-0 py-2 space-y-0.5 min-h-0">
        <?php
            $user = auth()->user();
            $isStudent = $user->role === 'student' || strtolower($user->role) === 'student';
        ?>
        <?php if($isStudent || $user->hasAnyPermission('student.view.courses', 'student.view.my-courses', 'student.view.orders', 'student.view.invoices', 'student.view.wallet', 'student.view.certificates', 'student.view.achievements', 'student.view.exams', 'student.view.calendar', 'student.view.notifications', 'student.view.profile', 'student.view.settings')): ?>

            
            <div class="ins-nav-group">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-home text-[9px] opacity-50"></i>
                    الرئيسية
                </span>
            </div>

            <a href="<?php echo e(route('dashboard')); ?>" @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
               class="ins-nav <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                <span class="ins-icon bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400">
                    <i class="fas fa-th-large text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('student.dashboard')); ?></span>
            </a>

            <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'student.view.courses')): ?>
            <?php $catalogActive = request()->routeIs('public.courses', 'public.course.*') || request()->routeIs('academic-years*') || request()->routeIs('subjects.*') || request()->routeIs('courses.show'); ?>
            <a href="<?php echo e(route('public.courses')); ?>" @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
               class="ins-nav <?php echo e($catalogActive ? 'active' : ''); ?>">
                <span class="ins-icon bg-[#FFE5F7] dark:bg-indigo-900/40 text-[#283593] dark:text-indigo-400">
                    <i class="fas fa-search text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('student.browse_courses')); ?></span>
            </a>
            <?php endif; ?>

            <?php if($isStudent || $user->hasPermission('student.view.my-courses')): ?>
            <a href="<?php echo e(route('my-courses.index')); ?>" @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
               class="ins-nav <?php echo e(request()->routeIs('my-courses.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-[#eef2ff] dark:bg-violet-900/40 text-[#283593] dark:text-violet-400">
                    <i class="fas fa-book-open text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('student.my_courses')); ?></span>
                <?php if($coursesCount > 0): ?>
                    <span class="ins-nav-badge bg-[#eef2ff] dark:bg-violet-900/50 text-[#283593] dark:text-violet-300"><?php echo e($coursesCount); ?></span>
                <?php endif; ?>
            </a>
            <a href="<?php echo e(route('student.my-course-subscriptions')); ?>" @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
               class="ins-nav <?php echo e(request()->routeIs('student.my-course-subscriptions') ? 'active' : ''); ?>">
                <span class="ins-icon bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400">
                    <i class="fas fa-calendar-check text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('student.course_subscriptions_nav')); ?></span>
            </a>
            <?php if(Route::has('student.one-to-one-sessions.index')): ?>
            <a href="<?php echo e(route('student.one-to-one-sessions.index')); ?>" @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
               class="ins-nav <?php echo e(request()->routeIs('student.one-to-one-sessions.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-violet-100 dark:bg-violet-900/40 text-violet-600 dark:text-violet-400">
                    <i class="fas fa-user-graduate text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('student.one_to_one_sessions_nav')); ?></span>
            </a>
            <?php endif; ?>
            <?php endif; ?>

            <?php if(Route::has('student.live-sessions.index')): ?>
            <a href="<?php echo e(route('student.live-sessions.index')); ?>" @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
               class="ins-nav <?php echo e(request()->routeIs('student.live-sessions.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400">
                    <i class="fas fa-broadcast-tower text-sm"></i>
                </span>
                <span class="flex-1 truncate">البث المباشر</span>
                <?php $studentLiveCount = \App\Models\LiveSession::where('status', 'live')->count(); ?>
                <?php if($studentLiveCount > 0): ?>
                    <span class="ins-nav-badge bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400">
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse inline-block ml-1"></span><?php echo e($studentLiveCount); ?>

                    </span>
                <?php endif; ?>
            </a>
            <?php endif; ?>

            <?php if(Route::has('student.live-recordings.index')): ?>
            <a href="<?php echo e(route('student.live-recordings.index')); ?>" @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
               class="ins-nav <?php echo e(request()->routeIs('student.live-recordings.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400">
                    <i class="fas fa-play-circle text-sm"></i>
                </span>
                <span class="flex-1 truncate">تسجيلات البث</span>
            </a>
            <?php endif; ?>

            
            <div class="ins-nav-group mt-3">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-trophy text-[9px] opacity-50"></i>
                    التعلم والإنجازات
                </span>
            </div>

            <?php if($isStudent || $user->hasPermission('student.view.orders')): ?>
            <a href="<?php echo e(route('orders.index')); ?>" @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
               class="ins-nav <?php echo e(request()->routeIs('orders.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400">
                    <i class="fas fa-shopping-bag text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('student.orders')); ?></span>
            </a>
            <?php endif; ?>

            <?php if($isStudent || $user->hasPermission('student.view.exams')): ?>
            <a href="<?php echo e(route('student.exams.index')); ?>" @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
               class="ins-nav <?php echo e(request()->routeIs('student.exams.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400">
                    <i class="fas fa-clipboard-check text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('student.exams')); ?></span>
            </a>
            <?php endif; ?>

            <?php if($isStudent): ?>
            <a href="<?php echo e(route('student.assignments.index')); ?>" @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
               class="ins-nav <?php echo e(request()->routeIs('student.assignments.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400">
                    <i class="fas fa-tasks text-sm"></i>
                </span>
                <span class="flex-1 truncate">واجباتي</span>
            </a>
            <?php endif; ?>

            <?php if($isStudent || $user->hasPermission('student.view.certificates')): ?>
            <a href="<?php echo e(route('student.certificates.index')); ?>" @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
               class="ins-nav <?php echo e(request()->routeIs('student.certificates.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400">
                    <i class="fas fa-award text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('student.certificates')); ?></span>
            </a>
            <?php endif; ?>

            <?php if($isStudent || $user->hasPermission('student.view.wallet')): ?>
            <a href="<?php echo e(route('student.wallet.index')); ?>" @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
               class="ins-nav <?php echo e(request()->routeIs('student.wallet.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-teal-100 dark:bg-teal-900/40 text-teal-600 dark:text-teal-400">
                    <i class="fas fa-wallet text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('student.wallet')); ?></span>
            </a>
            <?php endif; ?>

            <?php if($isStudent && Route::has('referrals.index')): ?>
            <a href="<?php echo e(route('referrals.index')); ?>" @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
               class="ins-nav <?php echo e(request()->routeIs('referrals.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-cyan-100 dark:bg-cyan-900/40 text-cyan-600 dark:text-cyan-400">
                    <i class="fas fa-user-friends text-sm"></i>
                </span>
                <span class="flex-1 truncate">برنامج الإحالات</span>
            </a>
            <?php endif; ?>

            <?php if($isStudent || $user->hasPermission('student.view.calendar')): ?>
            <?php
                $upcomingEventsCount = 0;
                try {
                    $user = auth()->user();
                    $upcomingEventsCount += \App\Models\Lecture::whereHas('course', function($q) use ($user) {
                        $q->whereHas('enrollments', function($q2) use ($user) { $q2->where('user_id', $user->id)->where('status', 'active'); });
                    })->where('status', 'scheduled')->where('scheduled_at', '>=', now())->count();
                    $upcomingEventsCount += \App\Models\Exam::whereHas('course', function($q) use ($user) {
                        $q->whereHas('enrollments', function($q2) use ($user) { $q2->where('user_id', $user->id)->where('status', 'active'); });
                    })->where('is_active', true)->where('is_published', true)->where(function($q) { $q->where('start_time', '>=', now())->orWhere('start_date', '>=', now()); })->count();
                    $upcomingEventsCount += \App\Models\Assignment::whereHas('course', function($q) use ($user) {
                        $q->whereHas('enrollments', function($q2) use ($user) { $q2->where('user_id', $user->id)->where('status', 'active'); });
                    })->where('status', 'published')->where('due_date', '>=', now())->count();
                    $upcomingEventsCount += \App\Models\LectureAssignment::whereHas('lecture.course', function($q) use ($user) {
                        $q->whereHas('enrollments', function($q2) use ($user) { $q2->where('user_id', $user->id)->where('status', 'active'); });
                    })->where('status', 'published')->where('due_date', '>=', now())->count();
                } catch (\Exception $e) {}
            ?>
            <a href="<?php echo e(route('calendar')); ?>" @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
               class="ins-nav <?php echo e(request()->routeIs('calendar') ? 'active' : ''); ?>">
                <span class="ins-icon bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 relative">
                    <i class="fas fa-calendar-alt text-sm"></i>
                    <?php if($upcomingEventsCount > 0): ?>
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full border-2 border-white dark:border-gray-900 flex items-center justify-center text-[7px] font-bold text-white"><?php echo e($upcomingEventsCount > 9 ? '9+' : $upcomingEventsCount); ?></span>
                    <?php endif; ?>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('student.calendar')); ?></span>
                <?php if($upcomingEventsCount > 0): ?>
                    <span class="ins-nav-badge bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300"><?php echo e($upcomingEventsCount); ?></span>
                <?php endif; ?>
            </a>
            <?php endif; ?>

            <?php if(Route::has('consultations.index') && $isStudent): ?>
            <a href="<?php echo e(route('consultations.index')); ?>" @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
               class="ins-nav <?php echo e(request()->routeIs('consultations.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400">
                    <i class="fas fa-comments-dollar text-sm"></i>
                </span>
                <span class="flex-1 truncate">استشارات المدربين</span>
            </a>
            <?php endif; ?>

            <?php if($isStudent || $user->hasPermission('student.view.notifications')): ?>
            <a href="<?php echo e(route('notifications')); ?>" @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
               class="ins-nav <?php echo e(request()->routeIs('notifications') ? 'active' : ''); ?>">
                <span class="ins-icon bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 relative">
                    <i class="fas fa-bell text-sm"></i>
                    <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white dark:border-gray-900 animate-pulse"></span>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('student.notifications')); ?></span>
            </a>
            <?php endif; ?>

            <?php if($isStudent && Route::has('student.ai-usages.index')): ?>
            <a href="<?php echo e(route('student.ai-usages.index')); ?>" @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
               class="ins-nav <?php echo e(request()->routeIs('student.ai-usages.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-violet-100 dark:bg-violet-900/40 text-violet-600 dark:text-violet-400">
                    <i class="fas fa-flask text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('student.ai_usages.nav')); ?></span>
            </a>
            <?php endif; ?>

            <?php if(Route::has('curriculum-library.index')): ?>
            <a href="<?php echo e(route('curriculum-library.index')); ?>" @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
               class="ins-nav <?php echo e(request()->routeIs('curriculum-library.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400">
                    <i class="fas fa-book-open text-sm"></i>
                </span>
                <span class="flex-1 truncate">مكتبة المنهج</span>
            </a>
            <?php endif; ?>

            
            <?php
                $activeSub = $user->activeSubscription();
                $featureConfig = config('student_subscription_features', []);
            ?>
            <?php if($activeSub): ?>
            <div class="ins-nav-group mt-3">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-crown text-[9px] text-amber-500 opacity-80"></i>
                    <span>القسم المدفوع</span>
                    <span class="mr-auto text-[8px] font-semibold bg-gradient-to-l from-amber-500 to-orange-500 text-white px-1.5 py-0.5 rounded-full leading-none">PRO</span>
                </span>
            </div>

            <?php if(Route::has('student.my-subscription')): ?>
            <a href="<?php echo e(route('student.my-subscription')); ?>" @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
               class="ins-nav <?php echo e(request()->routeIs('student.my-subscription') ? 'active' : ''); ?>">
                <span class="ins-icon bg-[#FFE5F7] dark:bg-sky-900/40 text-[#283593] dark:text-sky-400">
                    <i class="fas fa-gem text-sm"></i>
                </span>
                <span class="flex-1 truncate">اشتراكي</span>
                <span class="text-[9px] font-medium text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded" title="ينتهي في <?php echo e($activeSub->end_date?->format('Y-m-d')); ?>"><?php echo e($activeSub->end_date?->format('m/d')); ?></span>
            </a>
            <?php endif; ?>

            <?php $__currentLoopData = $featureConfig; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $featureKey => $cfg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($featureKey === 'library_access'): ?>
                    <?php continue; ?>
                <?php endif; ?>
                <?php if(!$user->hasSubscriptionFeature($featureKey)): ?>
                    <?php continue; ?>
                <?php endif; ?>
                <?php
                    $routeName = $cfg['route'] ?? 'student.features.show';
                    $params = $cfg['route_params'] ?? [];
                    if ($routeName === 'student.features.show') {
                        $params = array_merge($params, ['feature' => $featureKey]);
                    }
                    $url = $routeName === 'student.features.show' ? route('student.features.show', $params) : route($routeName, $params);
                    if ($routeName === 'student.portfolio.index') $isActive = request()->routeIs('student.portfolio.*');
                    elseif ($routeName === 'curriculum-library.index') $isActive = request()->routeIs('curriculum-library.*');
                    elseif ($routeName === 'student.classroom.index') $isActive = request()->routeIs('student.classroom.*');
                    elseif ($routeName === 'student.support.index') $isActive = request()->routeIs('student.support.*');
                    elseif ($routeName === 'student.academies.visibility') $isActive = request()->routeIs('student.academies.*');
                    elseif ($routeName === 'student.opportunities.index') $isActive = request()->routeIs('student.opportunities.*');
                    else $isActive = request()->routeIs('student.features.show') && request()->route('feature') === $featureKey;
                ?>
                <?php if(($routeName === 'student.features.show' && Route::has('student.features.show')) || ($routeName !== 'student.features.show' && Route::has($routeName))): ?>
                <a href="<?php echo e($url); ?>" @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
                   class="ins-nav <?php echo e($isActive ? 'active' : ''); ?>">
                    <span class="ins-icon <?php echo e($cfg['icon_bg'] ?? 'bg-slate-100 dark:bg-slate-700/70'); ?> <?php echo e($cfg['icon_text'] ?? 'text-slate-600 dark:text-slate-300'); ?>">
                        <i class="fas <?php echo e($cfg['icon'] ?? 'fa-star'); ?> text-sm"></i>
                    </span>
                    <span class="flex-1 truncate"><?php echo e(__("student.subscription_feature.{$featureKey}")); ?></span>
                </a>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>

            
            <div class="ins-nav-group mt-3">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-user-cog text-[9px] opacity-50"></i>
                    الحساب
                </span>
            </div>

            <?php if($isStudent || $user->hasPermission('student.view.profile')): ?>
            <a href="<?php echo e(route('profile')); ?>" @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
               class="ins-nav <?php echo e(request()->routeIs('profile') ? 'active' : ''); ?>">
                <span class="ins-icon bg-gray-100 dark:bg-gray-700/60 text-gray-600 dark:text-gray-400">
                    <i class="fas fa-user text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('student.profile')); ?></span>
            </a>
            <?php endif; ?>

            <?php if($isStudent || $user->hasPermission('student.view.settings')): ?>
            <a href="<?php echo e(route('settings')); ?>" @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
               class="ins-nav <?php echo e(request()->routeIs('settings') ? 'active' : ''); ?>">
                <span class="ins-icon bg-gray-100 dark:bg-gray-700/60 text-gray-600 dark:text-gray-400">
                    <i class="fas fa-cog text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('student.settings')); ?></span>
            </a>
            <?php endif; ?>
        <?php endif; ?>

        <?php if(auth()->user()->isAdmin() || auth()->user()->isInstructor()): ?>
            <div class="ins-nav-group mt-3">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-exchange-alt text-[9px] opacity-50"></i>
                    لوحة أخرى
                </span>
            </div>
            <?php if(auth()->user()->isAdmin()): ?>
                <a href="<?php echo e(route('admin.dashboard')); ?>" @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
                   class="ins-nav <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                    <span class="ins-icon bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400">
                        <i class="fas fa-shield-alt text-sm"></i>
                    </span>
                    <span class="flex-1 truncate"><?php echo e(__('student.admin_panel')); ?></span>
                </a>
            <?php endif; ?>
            <?php if(auth()->user()->isInstructor()): ?>
                <a href="<?php echo e(route('dashboard')); ?>" @click="if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)"
                   class="ins-nav">
                <span class="ins-icon bg-[#FFE5F7] dark:bg-sky-900/40 text-[#283593] dark:text-sky-400">
                        <i class="fas fa-chalkboard-teacher text-sm"></i>
                    </span>
                    <span class="flex-1 truncate">لوحة المعلم</span>
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </nav>

    
    <div class="px-3 py-3 flex-shrink-0 border-t border-gray-200/80 dark:border-gray-700/80">
        <div class="ins-user-card flex items-center gap-3">
            <div class="u-avatar flex-shrink-0 w-10 h-10 rounded-xl">
                <?php if(auth()->user()->profile_image): ?>
                    <img src="<?php echo e(auth()->user()->profile_image_url); ?>" alt="" class="w-full h-full object-cover rounded-xl">
                <?php else: ?>
                    <?php echo e(mb_substr(auth()->user()->name, 0, 1)); ?>

                <?php endif; ?>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate leading-tight"><?php echo e(auth()->user()->name); ?></p>
                <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate mt-0.5">
                    <?php if(auth()->user()->isAdmin()): ?> <?php echo e(__('student.admin_role')); ?>

                    <?php elseif(auth()->user()->isInstructor()): ?> <?php echo e(__('student.instructor_role')); ?>

                    <?php else: ?> <?php echo e(__('student.student_role')); ?>

                    <?php endif; ?>
                </p>
            </div>
            <form method="POST" action="<?php echo e(route('logout')); ?>" class="flex-shrink-0">
                <?php echo csrf_field(); ?>
                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 text-red-500 dark:text-red-400 flex items-center justify-center transition-colors" title="تسجيل الخروج">
                    <i class="fas fa-sign-out-alt text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\layouts\student-sidebar.blade.php ENDPATH**/ ?>