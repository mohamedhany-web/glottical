<div class="flex flex-col h-full">
    
    <div class="ins-sidebar-brand flex items-center gap-3 px-4 py-4 flex-shrink-0 relative">
        <button @click="if (window.innerWidth < 1024) sidebarOpen = false"
                class="lg:hidden absolute top-3 left-3 w-8 h-8 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 flex items-center justify-center transition-colors z-10">
            <i class="fas fa-times text-xs"></i>
        </button>
        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-lg shadow-sky-500/20">
            <i class="fas fa-chalkboard-teacher text-lg"></i>
        </div>
        <div class="flex-1 min-w-0 relative z-10">
            <h2 class="text-base font-bold text-gray-900 dark:text-gray-100 leading-tight">Muallimx</h2>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium mt-0.5"><?php echo e(__('instructor.instructor_panel')); ?></p>
        </div>
    </div>

    
    <?php
        $user = auth()->user();
        $directCourseIds = \App\Models\AdvancedCourse::where('instructor_id', $user->id)->pluck('id');
        $assignedFromPaths = $user->teachingLearningPaths()->get()->flatMap(fn($ay) => json_decode($ay->pivot->assigned_courses ?? '[]', true) ?: []);
        $teachingCourseIds = $directCourseIds->merge($assignedFromPaths)->unique()->filter()->values();
        $myCoursesCount = $teachingCourseIds->count();
        $totalStudents = $teachingCourseIds->isEmpty() ? 0 : \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $teachingCourseIds)->where('status', 'active')->distinct('user_id')->count('user_id');
    ?>
    <div class="px-3 py-3 flex-shrink-0">
        <div class="grid grid-cols-2 gap-2.5">
            <a href="<?php echo e(route('instructor.courses.index')); ?>" class="ins-stat-card bg-white dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700/80 block group">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-book text-sm"></i>
                    </span>
                    <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider"><?php echo e(__('instructor.courses')); ?></span>
                </div>
                <div class="text-xl font-black text-gray-900 dark:text-gray-100 leading-none tabular-nums"><?php echo e($myCoursesCount); ?></div>
            </a>
            <a href="<?php echo e(route('instructor.courses.index')); ?>" class="ins-stat-card bg-white dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700/80 block group">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-user-graduate text-sm"></i>
                    </span>
                    <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider"><?php echo e(__('instructor.students')); ?></span>
                </div>
                <div class="text-xl font-black text-gray-900 dark:text-gray-100 leading-none tabular-nums"><?php echo e($totalStudents); ?></div>
            </a>
        </div>
    </div>

    
    <nav class="flex-1 overflow-y-auto sidebar-scroll px-0 py-2 space-y-0.5 min-h-0">
        <?php
            $user = auth()->user();
            $isInstructor = $user->isInstructor() || $user->isTeacher() || strtolower($user->role) === 'teacher' || strtolower($user->role) === 'instructor';
        ?>

        
        <div class="ins-nav-group">
            <span class="inline-flex items-center gap-1.5">
                <i class="fas fa-home text-[9px] opacity-50"></i>
                <?php echo e(__('instructor.overview')); ?>

            </span>
        </div>
        <?php if($isInstructor || $user->hasAnyPermission('instructor.view.courses', 'instructor.manage.lectures', 'instructor.manage.assignments', 'instructor.manage.exams', 'instructor.manage.attendance', 'instructor.view.tasks')): ?>

            <a href="<?php echo e(route('dashboard')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                <span class="ins-icon bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400">
                    <i class="fas fa-th-large text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.dashboard')); ?></span>
            </a>

            <?php if($isInstructor || $user->hasPermission('instructor.view.courses')): ?>
            <a href="<?php echo e(route('instructor.courses.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.courses.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400">
                    <i class="fas fa-book-open text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.my_courses')); ?></span>
                <?php if($myCoursesCount > 0): ?>
                    <span class="ins-nav-badge bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300"><?php echo e($myCoursesCount); ?></span>
                <?php endif; ?>
            </a>
            <?php endif; ?>

            
            <div class="ins-nav-group mt-3">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-tools text-[9px] opacity-50"></i>
                    أدوات التدريس
                </span>
            </div>

            <?php if($isInstructor || $user->hasPermission('instructor.manage.lectures')): ?>
            <a href="<?php echo e(route('instructor.lectures.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.lectures.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-violet-100 dark:bg-violet-900/40 text-violet-600 dark:text-violet-400">
                    <i class="fas fa-chalkboard text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.lectures')); ?></span>
            </a>
            <?php endif; ?>

            <?php if($isInstructor || $user->hasPermission('instructor.manage.assignments')): ?>
            <a href="<?php echo e(route('instructor.assignments.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.assignments.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400">
                    <i class="fas fa-tasks text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.assignments')); ?></span>
            </a>
            <?php endif; ?>

            <?php if($isInstructor || $user->hasPermission('instructor.manage.exams')): ?>
            <a href="<?php echo e(route('instructor.exams.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.exams.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400">
                    <i class="fas fa-clipboard-check text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.exams')); ?></span>
            </a>
            <?php endif; ?>

            <?php if($isInstructor): ?>
            <a href="<?php echo e(route('instructor.question-banks.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.question-banks.*') || request()->routeIs('instructor.questions.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-teal-100 dark:bg-teal-900/40 text-teal-600 dark:text-teal-400">
                    <i class="fas fa-database text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.question_banks')); ?></span>
            </a>
            <?php endif; ?>

            <?php if($isInstructor || $user->hasPermission('instructor.manage.attendance')): ?>
            <a href="<?php echo e(route('instructor.attendance.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.attendance.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-cyan-100 dark:bg-cyan-900/40 text-cyan-600 dark:text-cyan-400">
                    <i class="fas fa-clipboard-list text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.attendance')); ?></span>
            </a>
            <?php endif; ?>

            <?php if(Route::has('instructor.live-sessions.index')): ?>
            <a href="<?php echo e(route('instructor.live-sessions.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.live-sessions.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400">
                    <i class="fas fa-broadcast-tower text-sm"></i>
                </span>
                <span class="flex-1 truncate">البث المباشر</span>
                <?php $liveCount = \App\Models\LiveSession::where('instructor_id', auth()->id())->where('status', 'live')->count(); ?>
                <?php if($liveCount > 0): ?>
                    <span class="ins-nav-badge bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400">
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse inline-block ml-1"></span><?php echo e($liveCount); ?>

                    </span>
                <?php endif; ?>
            </a>
            <?php endif; ?>

            <?php if(Route::has('instructor.consultations.index')): ?>
            <a href="<?php echo e(route('instructor.consultations.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.consultations.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400">
                    <i class="fas fa-comments-dollar text-sm"></i>
                </span>
                <span class="flex-1 truncate">استشارات الطلاب</span>
            </a>
            <?php endif; ?>
            <?php if(Route::has('instructor.calendar')): ?>
            <a href="<?php echo e(route('instructor.calendar')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.calendar') || request()->routeIs('instructor.calendar.events') ? 'active' : ''); ?>">
                <span class="ins-icon bg-teal-100 dark:bg-teal-900/40 text-teal-600 dark:text-teal-400">
                    <i class="fas fa-calendar-check text-sm"></i>
                </span>
                <span class="flex-1 truncate">تقويم الاستشارات</span>
            </a>
            <?php endif; ?>

            
            <div class="ins-nav-group mt-3">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-briefcase text-[9px] opacity-50"></i>
                    الإدارة
                </span>
            </div>

            <?php if($isInstructor || $user->hasPermission('instructor.view.tasks')): ?>
            <a href="<?php echo e(route('instructor.tasks.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.tasks.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-orange-100 dark:bg-orange-900/40 text-orange-600 dark:text-orange-400">
                    <i class="fas fa-check-square text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.tasks_from_management')); ?></span>
            </a>
            <?php endif; ?>

            <?php if(($isInstructor || $user->hasPermission('instructor.view.tasks')) && Route::has('instructor.management-requests.index')): ?>
            <a href="<?php echo e(route('instructor.management-requests.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.management-requests.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-sky-100 dark:bg-sky-900/40 text-sky-600 dark:text-sky-400">
                    <i class="fas fa-paper-plane text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.submit_requests_to_management')); ?></span>
            </a>
            <?php endif; ?>

            <a href="<?php echo e(route('instructor.agreements.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.agreements.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400">
                    <i class="fas fa-handshake text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.agreements_system')); ?></span>
            </a>

            
            <div class="ins-nav-group mt-3">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-coins text-[9px] opacity-50"></i>
                    المالية
                </span>
            </div>

            <a href="<?php echo e(route('instructor.transfer-account.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.transfer-account.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400">
                    <i class="fas fa-university text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.transfer_account')); ?></span>
            </a>

            <a href="<?php echo e(route('instructor.withdrawals.index')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.withdrawals.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400">
                    <i class="fas fa-money-bill-wave text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.withdrawal_requests')); ?></span>
            </a>

            <a href="<?php echo e(route('instructor.personal-branding.edit')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('instructor.personal-branding.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-fuchsia-100 dark:bg-fuchsia-900/40 text-fuchsia-600 dark:text-fuchsia-400">
                    <i class="fas fa-user-tie text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.personal_branding')); ?></span>
            </a>
        <?php endif; ?>

        
        <div class="ins-nav-group mt-3">
            <span class="inline-flex items-center gap-1.5">
                <i class="fas fa-user-cog text-[9px] opacity-50"></i>
                الحساب
            </span>
        </div>

        <?php if(auth()->user()->isAdmin()): ?>
            <a href="<?php echo e(route('admin.dashboard')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
               class="ins-nav <?php echo e(request()->routeIs('admin.*') ? 'active' : ''); ?>">
                <span class="ins-icon bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400">
                    <i class="fas fa-shield-alt text-sm"></i>
                </span>
                <span class="flex-1 truncate"><?php echo e(__('instructor.admin_panel')); ?></span>
            </a>
        <?php endif; ?>

        <a href="<?php echo e(route('instructor.profile')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
           class="ins-nav <?php echo e(request()->routeIs('instructor.profile*') ? 'active' : ''); ?>">
            <span class="ins-icon bg-gray-100 dark:bg-gray-700/60 text-gray-600 dark:text-gray-400">
                <i class="fas fa-user text-sm"></i>
            </span>
            <span class="flex-1 truncate"><?php echo e(__('instructor.profile')); ?></span>
        </a>

        <?php if(auth()->check() && auth()->user()->hasPermission('student.view.settings')): ?>
        <a href="<?php echo e(route('settings')); ?>" @click="if(window.innerWidth<1024) sidebarOpen=false"
           class="ins-nav <?php echo e(request()->routeIs('settings') ? 'active' : ''); ?>">
            <span class="ins-icon bg-gray-100 dark:bg-gray-700/60 text-gray-600 dark:text-gray-400">
                <i class="fas fa-cog text-sm"></i>
            </span>
            <span class="flex-1 truncate"><?php echo e(__('instructor.settings')); ?></span>
        </a>
        <?php endif; ?>
    </nav>

    
    <div class="px-3 py-3 flex-shrink-0 border-t border-gray-200/80 dark:border-gray-700/80">
        <div class="ins-user-card flex items-center gap-3">
            <div class="u-avatar w-10 h-10 flex-shrink-0 rounded-xl">
                <?php if(auth()->user()->profile_image): ?>
                    <img src="<?php echo e(auth()->user()->profile_image_url); ?>" alt="" class="w-full h-full object-cover rounded-xl">
                <?php else: ?>
                    <?php echo e(mb_substr(auth()->user()->name, 0, 1)); ?>

                <?php endif; ?>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate leading-tight"><?php echo e(auth()->user()->name); ?></p>
                <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate mt-0.5"><?php echo e(__('instructor.instructor_role')); ?></p>
            </div>
            <form method="POST" action="<?php echo e(route('logout')); ?>" class="flex-shrink-0">
                <?php echo csrf_field(); ?>
                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 text-red-500 dark:text-red-400 flex items-center justify-center transition-colors" title="<?php echo e(__('instructor.logout')); ?>">
                    <i class="fas fa-sign-out-alt text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\layouts\instructor-sidebar.blade.php ENDPATH**/ ?>