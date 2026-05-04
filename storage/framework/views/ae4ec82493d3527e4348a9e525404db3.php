

<?php $__env->startSection('title', __('instructor.dashboard_title')); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .dash-welcome {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,.05);
    }
    html.dark .dash-welcome {
        background: #1e293b;
        border-color: #334155;
        box-shadow: 0 1px 3px rgba(0,0,0,.15);
    }
    .dash-stat {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        transition: transform .2s, box-shadow .2s, border-color .2s;
    }
    .dash-stat:hover {
        transform: translateY(-4px); box-shadow: 0 12px 28px -10px rgba(0,0,0,.12);
        border-color: #cbd5e1;
    }
    html.dark .dash-stat {
        background: rgba(30,41,59,.6); border-color: rgba(51,65,85,.8);
    }
    html.dark .dash-stat:hover {
        border-color: rgba(71,85,105,.9); box-shadow: 0 12px 28px -10px rgba(0,0,0,.35);
    }
    .dash-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        overflow: hidden; transition: box-shadow .2s;
    }
    .dash-card:hover { box-shadow: 0 8px 24px -8px rgba(0,0,0,.08); }
    html.dark .dash-card {
        background: rgba(30,41,59,.6); border-color: rgba(51,65,85,.8);
    }
    html.dark .dash-card:hover { box-shadow: 0 8px 24px -8px rgba(0,0,0,.25); }
    .dash-card-header {
        padding: 14px 18px; border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
    }
    html.dark .dash-card-header { border-bottom-color: rgba(51,65,85,.6); }
    .dash-row {
        display: flex; align-items: center; gap: 12px; padding: 12px 18px;
        transition: background .15s; border-bottom: 1px solid #f8fafc;
    }
    .dash-row:last-child { border-bottom: none; }
    .dash-row:hover { background: #f8fafc; }
    html.dark .dash-row { border-bottom-color: rgba(51,65,85,.4); }
    html.dark .dash-row:hover { background: rgba(51,65,85,.3); }
    .dash-quick {
        display: flex; align-items: center; gap: 12px; padding: 14px 18px;
        background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
        transition: all .2s;
    }
    .dash-quick:hover {
        border-color: #cbd5e1; box-shadow: 0 6px 16px -6px rgba(0,0,0,.08);
        transform: translateY(-2px);
    }
    html.dark .dash-quick {
        background: rgba(30,41,59,.6); border-color: rgba(51,65,85,.8);
    }
    html.dark .dash-quick:hover { border-color: rgba(71,85,105,.9); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 pb-4">
    
    <section class="dash-welcome px-5 py-6 sm:px-6 sm:py-7">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-start sm:items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-sky-100 dark:bg-sky-900/40 text-sky-600 dark:text-sky-400 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-chalkboard-teacher text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100 leading-tight">
                        <?php echo e(__('instructor.welcome')); ?>، <?php echo e(auth()->user()->name); ?>

                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?php echo e(__('instructor.overview_activity_today')); ?></p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-2 flex items-center gap-2">
                        <i class="fas fa-calendar-day text-gray-400"></i>
                        <time datetime="<?php echo e(now()->toIso8601String()); ?>"><?php echo e(now()->translatedFormat('l، d F Y')); ?></time>
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="<?php echo e(route('instructor.lectures.create')); ?>"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-xl transition-colors">
                    <i class="fas fa-video text-sm"></i>
                    <?php echo e(__('instructor.add_lecture')); ?>

                </a>
                <a href="<?php echo e(route('instructor.assignments.create')); ?>"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-semibold rounded-xl border border-gray-200 dark:border-gray-600 transition-colors">
                    <i class="fas fa-tasks text-sm"></i>
                    <?php echo e(__('instructor.add_assignment')); ?>

                </a>
            </div>
        </div>
    </section>

    
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="<?php echo e(route('instructor.courses.index')); ?>" class="dash-stat p-5 block group">
            <div class="flex items-center justify-between mb-3">
                <span class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <i class="fas fa-book text-lg"></i>
                </span>
                <span class="text-gray-300 dark:text-gray-600 group-hover:text-blue-400 transition-colors"><i class="fas fa-arrow-left text-sm"></i></span>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100 tabular-nums leading-none"><?php echo e(number_format($stats['my_courses'])); ?></div>
            <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-1.5 uppercase tracking-wider"><?php echo e(__('instructor.my_courses')); ?></div>
        </a>

        <a href="<?php echo e(route('instructor.courses.index')); ?>" class="dash-stat p-5 block group">
            <div class="flex items-center justify-between mb-3">
                <span class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <i class="fas fa-user-graduate text-lg"></i>
                </span>
                <span class="text-gray-300 dark:text-gray-600 group-hover:text-emerald-400 transition-colors"><i class="fas fa-arrow-left text-sm"></i></span>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100 tabular-nums leading-none"><?php echo e(number_format($stats['total_students'])); ?></div>
            <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-1.5 uppercase tracking-wider"><?php echo e(__('instructor.total_students')); ?></div>
        </a>

        <a href="<?php echo e(route('instructor.lectures.index')); ?>" class="dash-stat p-5 block group">
            <div class="flex items-center justify-between mb-3">
                <span class="w-12 h-12 rounded-xl bg-violet-100 dark:bg-violet-900/40 text-violet-600 dark:text-violet-400 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <i class="fas fa-chalkboard text-lg"></i>
                </span>
                <span class="text-gray-300 dark:text-gray-600 group-hover:text-violet-400 transition-colors"><i class="fas fa-arrow-left text-sm"></i></span>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100 tabular-nums leading-none"><?php echo e(number_format($stats['total_lectures'])); ?></div>
            <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-1.5 uppercase tracking-wider">
                <?php echo e(__('instructor.lectures')); ?>

                <?php if($stats['upcoming_lectures'] > 0): ?>
                    <span class="text-violet-500 dark:text-violet-400 font-normal"> · <?php echo e($stats['upcoming_lectures']); ?> <?php echo e(__('instructor.upcoming')); ?></span>
                <?php endif; ?>
            </div>
        </a>

        <a href="<?php echo e(route('instructor.assignments.index')); ?>" class="dash-stat p-5 block group">
            <div class="flex items-center justify-between mb-3">
                <span class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <i class="fas fa-tasks text-lg"></i>
                </span>
                <span class="text-gray-300 dark:text-gray-600 group-hover:text-amber-400 transition-colors"><i class="fas fa-arrow-left text-sm"></i></span>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100 tabular-nums leading-none"><?php echo e(number_format($stats['total_assignments'])); ?></div>
            <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-1.5 uppercase tracking-wider">
                <?php echo e(__('instructor.assignments')); ?>

                <?php if($stats['pending_submissions'] > 0): ?>
                    <span class="inline-flex items-center gap-1 text-amber-600 dark:text-amber-400 font-normal">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-amber-600 animate-pulse"></span>
                        <?php echo e($stats['pending_submissions']); ?> <?php echo e(__('instructor.need_grading')); ?>

                    </span>
                <?php endif; ?>
            </div>
        </a>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="dash-card">
            <div class="dash-card-header">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <i class="fas fa-book text-sm"></i>
                    </span>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100"><?php echo e(__('instructor.my_recent_courses')); ?></h3>
                </div>
                <a href="<?php echo e(route('instructor.courses.index')); ?>" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline"><?php echo e(__('instructor.view_all')); ?> ←</a>
            </div>
            <div>
                <?php $__empty_1 = true; $__currentLoopData = $my_courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('instructor.courses.show', $course)); ?>" class="dash-row block">
                    <span class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-play text-xs"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate"><?php echo e($course->title); ?></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            <?php echo e($course->active_students_count ?? 0); ?> <?php echo e(__('instructor.student_single')); ?>

                            <?php if($course->academicSubject): ?> · <?php echo e($course->academicSubject->name); ?> <?php endif; ?>
                        </p>
                    </div>
                    <i class="fas fa-chevron-left text-gray-300 dark:text-gray-600 text-xs flex-shrink-0"></i>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-6 py-12 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-700/60 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-book text-2xl text-gray-400 dark:text-gray-500"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo e(__('instructor.no_courses_assigned')); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="dash-card">
            <div class="dash-card-header">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-violet-100 dark:bg-violet-900/40 text-violet-600 dark:text-violet-400 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-sm"></i>
                    </span>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100"><?php echo e(__('instructor.upcoming_lectures')); ?></h3>
                </div>
                <a href="<?php echo e(route('instructor.lectures.index')); ?>" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline"><?php echo e(__('instructor.view_all')); ?> ←</a>
            </div>
            <div>
                <?php $__empty_1 = true; $__currentLoopData = $upcoming_lectures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lecture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('instructor.lectures.show', $lecture)); ?>" class="dash-row block">
                    <span class="w-10 h-10 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-video text-xs"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate"><?php echo e($lecture->title); ?></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            <?php echo e($lecture->course->title ?? __('instructor.not_specified')); ?> · <?php echo e($lecture->scheduled_at->format('m/d H:i')); ?>

                        </p>
                    </div>
                    <span class="text-[10px] font-semibold text-violet-600 dark:text-violet-400 bg-violet-50 dark:bg-violet-900/20 px-2.5 py-1 rounded-lg flex-shrink-0">
                        <?php echo e($lecture->scheduled_at->diffForHumans()); ?>

                    </span>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-6 py-12 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-700/60 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar text-2xl text-gray-400 dark:text-gray-500"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3"><?php echo e(__('instructor.no_lectures')); ?></p>
                    <a href="<?php echo e(route('instructor.lectures.create')); ?>" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline"><?php echo e(__('instructor.add_lecture')); ?> ←</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="dash-card">
        <div class="dash-card-header">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <i class="fas fa-tasks text-sm"></i>
                </span>
                <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100"><?php echo e(__('instructor.assignments_need_grading')); ?></h3>
                <?php if($stats['pending_submissions'] > 0): ?>
                    <span class="inline-flex items-center justify-center min-w-[24px] h-6 px-2 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 text-xs font-bold"><?php echo e($stats['pending_submissions']); ?></span>
                <?php endif; ?>
            </div>
            <a href="<?php echo e(route('instructor.assignments.index')); ?>" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline"><?php echo e(__('instructor.view_all')); ?> ←</a>
        </div>
        <div>
            <?php $__empty_1 = true; $__currentLoopData = $pending_assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a href="<?php echo e(route('instructor.assignments.submissions', $submission->assignment)); ?>" class="dash-row block">
                <span class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-file-alt text-xs"></i>
                </span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate"><?php echo e($submission->assignment->title ?? __('instructor.assignment_default')); ?></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><?php echo e($submission->student->name ?? __('instructor.student_single')); ?> · <?php echo e($submission->created_at->diffForHumans()); ?></p>
                </div>
                <span class="text-[10px] font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 px-2.5 py-1 rounded-lg flex-shrink-0">
                    <?php echo e(__('instructor.grade_assignment')); ?>

                </span>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="px-6 py-12 text-center">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 dark:bg-emerald-900/20 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check text-emerald-500 dark:text-emerald-400 text-xl"></i>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400"><?php echo e(__('instructor.all_assignments_graded')); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div>
        <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3 px-1"><?php echo e(__('instructor.quick_actions')); ?></h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="<?php echo e(route('instructor.lectures.create')); ?>" class="dash-quick group">
                <span class="w-11 h-11 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fas fa-video"></i>
                </span>
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors"><?php echo e(__('instructor.add_lecture')); ?></span>
            </a>
            <a href="<?php echo e(route('instructor.assignments.create')); ?>" class="dash-quick group">
                <span class="w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fas fa-tasks"></i>
                </span>
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors"><?php echo e(__('instructor.add_assignment')); ?></span>
            </a>
            <a href="<?php echo e(route('instructor.exams.index')); ?>" class="dash-quick group">
                <span class="w-11 h-11 rounded-xl bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fas fa-clipboard-check"></i>
                </span>
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 group-hover:text-rose-600 dark:group-hover:text-rose-400 transition-colors"><?php echo e(__('instructor.manage_exams')); ?></span>
            </a>
            <a href="<?php echo e(route('instructor.attendance.index')); ?>" class="dash-quick group">
                <span class="w-11 h-11 rounded-xl bg-cyan-100 dark:bg-cyan-900/40 text-cyan-600 dark:text-cyan-400 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fas fa-clipboard-list"></i>
                </span>
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors"><?php echo e(__('instructor.attendance_absence')); ?></span>
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\dashboard\instructor.blade.php ENDPATH**/ ?>