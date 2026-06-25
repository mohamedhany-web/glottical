<?php $__env->startSection('title', __('student.dashboard_title')); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .stat-card {
        background: white;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 16px;
        padding: 1.25rem;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative; overflow: hidden;
    }
    .stat-card::after {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(135deg, transparent 60%, rgba(40, 53, 147, 0.03) 100%);
        pointer-events: none; border-radius: 16px;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 40px -12px rgba(15, 23, 42, 0.08);
        border-color: rgba(40, 53, 147, 0.18);
    }
    .stat-icon {
        width: 46px; height: 46px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; color: white;
        transition: transform 0.25s ease;
    }
    .stat-card:hover .stat-icon { transform: scale(1.08) rotate(2deg); }

    .section-card {
        background: white;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 16px;
        transition: all 0.2s ease;
    }
    .section-card:hover {
        box-shadow: 0 8px 25px -8px rgba(15, 23, 42, 0.06);
        border-color: rgba(6, 182, 212, 0.12);
    }

    .progress-bar {
        height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;
    }
    .progress-bar .fill {
        height: 100%; border-radius: 3px;
        background: linear-gradient(90deg, #283593, #FB5607);
        position: relative;
    }
    .progress-bar .fill::after {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.35), transparent);
        animation: shimmer 2.5s infinite;
    }
    @keyframes shimmer { 0%{transform:translateX(-100%)} 100%{transform:translateX(100%)} }

    .course-row {
        padding: 0.875rem 1rem; border-radius: 12px;
        border: 1px solid transparent;
        transition: all 0.2s ease;
    }
    .course-row:hover {
        background: #f8fafc;
        border-color: #e2e8f0;
    }

    .mini-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.75rem;
        transition: all 0.2s ease;
    }
    .mini-card:hover {
        background: white;
        border-color: #cbd5e1;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }

    .badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 10px; border-radius: 6px;
        font-size: 11px; font-weight: 600;
    }

    /* وضع داكن — نفس منطق لوحة الإدارة / التخطيط العام (أنماط مخصصة كانت فاتحة فقط) */
    .dark .course-row {
        border-color: rgba(51, 65, 85, 0.5);
    }
    .dark .course-row:hover {
        background: rgba(51, 65, 85, 0.45);
        border-color: #475569;
    }
    .dark .mini-card {
        background: rgba(30, 41, 59, 0.85) !important;
        border-color: #475569 !important;
        color: #e2e8f0;
    }
    .dark .mini-card:hover {
        background: rgba(51, 65, 85, 0.55) !important;
        border-color: #64748b !important;
    }
    .dark .progress-bar {
        background: #334155;
    }
        .dark .progress-bar .fill {
        background: linear-gradient(90deg, #818cf8, #fb7185);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $progress = min((int) $stats['total_progress'], 100);
    $circumference = 2 * 3.14159 * 40;
    $strokeDashoffset = $circumference - ($progress / 100) * $circumference;
?>

<div class="space-y-6">
    
    <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200/80 dark:border-slate-700 overflow-hidden">
        <div class="bg-gradient-to-l from-[#FFE5F7]/70 via-white to-white dark:from-slate-800/80 dark:via-slate-800/90 dark:to-slate-900/90 p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                <div class="flex-1 min-w-0">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-[#FFE5F7] dark:bg-brand-900/30 text-[#283593] dark:text-brand-300 text-xs font-bold mb-3 border border-[#f5c7e8] dark:border-brand-800/50">
                        <i class="fas fa-chart-line text-[10px]"></i>
                        <?php echo e(__('student.your_dashboard')); ?>

                    </span>
                    <h1 class="font-heading text-2xl sm:text-3xl font-black text-slate-800 dark:text-slate-100 mb-1 leading-tight">
                        <?php echo e(__('student.welcome_name', ['name' => auth()->user()->name])); ?>

                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 text-sm max-w-lg"><?php echo e(__('student.dashboard_subtitle')); ?></p>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-4 flex-shrink-0">
                    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'student.view.courses')): ?>
                    <a href="<?php echo e(route('public.courses')); ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#283593] hover:bg-[#1f2a7a] text-white text-sm font-bold shadow-md shadow-[#283593]/25 transition-colors order-2 sm:order-1">
                        <i class="fas fa-th-large text-xs"></i>
                        <?php echo e(__('student.courses')); ?>

                    </a>
                    <?php endif; ?>
                    <div class="relative flex items-center justify-center order-1 sm:order-2">
                        <svg class="w-[88px] h-[88px]" viewBox="0 0 96 96" style="transform:rotate(-90deg)">
                            <defs>
                                <linearGradient id="pg" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#06b6d4"/>
                                    <stop offset="100%" stop-color="#0891b2"/>
                                </linearGradient>
                            </defs>
                            <circle cx="48" cy="48" r="40" fill="none" class="stroke-slate-200 dark:stroke-slate-600" stroke-width="5"/>
                            <circle cx="48" cy="48" r="40" fill="none" stroke="url(#pg)" stroke-width="5" stroke-linecap="round"
                                stroke-dasharray="<?php echo e($circumference); ?>" stroke-dashoffset="<?php echo e($strokeDashoffset); ?>"
                                style="transition: stroke-dashoffset 0.8s ease"/>
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center font-heading text-lg font-black text-[#283593] dark:text-brand-300"><?php echo e($progress); ?>%</span>
                    </div>
                    <div class="text-right hidden sm:block order-3">
                        <p class="text-sm font-bold text-slate-700 dark:text-slate-200"><?php echo e(__('student.total_progress')); ?></p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5"><?php echo e(__('student.from_course_completion')); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="<?php echo e(route('my-courses.index')); ?>" class="stat-card group">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-xs font-medium text-slate-500 mb-1"><?php echo e(__('student.my_active_courses')); ?></p>
                    <p class="text-3xl font-heading font-black text-slate-800"><?php echo e($stats['active_courses']); ?></p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-[#283593] to-[#1F2A7A] shadow-lg shadow-[#283593]/25">
                    <i class="fas fa-book-open"></i>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 font-medium"><?php echo e(__('student.active_courses_now')); ?></p>
        </a>

        <a href="<?php echo e(route('student.certificates.index')); ?>" class="stat-card group">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-xs font-medium text-slate-500 mb-1"><?php echo e(__('student.completed')); ?></p>
                    <p class="text-3xl font-heading font-black text-slate-800"><?php echo e($stats['completed_courses']); ?></p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-emerald-500 to-emerald-600 shadow-lg shadow-emerald-500/20">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 font-medium"><?php echo e(__('student.completed_courses')); ?></p>
        </a>

        <div class="stat-card group">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-xs font-medium text-slate-500 mb-1"><?php echo e(__('student.total_progress')); ?></p>
                    <p class="text-3xl font-heading font-black text-slate-800"><?php echo e($stats['total_progress']); ?>%</p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-[#FB5607] to-[#e84d00] shadow-lg shadow-[#FB5607]/25">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div class="progress-bar">
                <div class="fill" style="width: <?php echo e($stats['total_progress']); ?>%"></div>
            </div>
        </div>

        <a href="<?php echo e(route('orders.index')); ?>" class="stat-card group">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-xs font-medium text-slate-500 mb-1"><?php echo e(__('student.pending_orders')); ?></p>
                    <p class="text-3xl font-heading font-black text-slate-800"><?php echo e($stats['pending_orders']); ?></p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-amber-500 to-amber-600 shadow-lg shadow-amber-500/20">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 font-medium"><?php echo e(__('student.orders_in_processing')); ?></p>
        </a>
    </div>

    <?php if(isset($activeSubscription) && $activeSubscription): ?>
    
    <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200/80 dark:border-slate-700 overflow-hidden">
        <div class="p-4 sm:p-5 flex flex-wrap items-center justify-between gap-4 bg-gradient-to-l from-[#FFE5F7]/80 to-white dark:from-slate-800/90 dark:to-slate-900/90 border-b border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-[#FFE5F7] dark:bg-indigo-900/40 text-[#283593] dark:text-indigo-300 flex items-center justify-center">
                    <i class="fas fa-layer-group"></i>
                </span>
                <div>
                    <h2 class="font-bold text-slate-800 dark:text-slate-100"><?php echo e($activeSubscription->plan_name); ?></h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">مدة الباقة: <?php echo e(\App\Models\Subscription::getDurationLabel($activeSubscription->billing_cycle)); ?> · ينتهي في <?php echo e($activeSubscription->end_date?->format('Y-m-d')); ?></p>
                </div>
            </div>
            <a href="<?php echo e(route('student.my-subscription')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#283593] text-white text-sm font-semibold hover:bg-[#1f2a7a] transition-colors">
                <i class="fas fa-info-circle"></i>
                تفاصيل اشتراكي
            </a>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 min-w-0">
            <div class="section-card">
                <div class="flex items-center justify-between p-5 pb-0">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#283593] to-[#FB5607] flex items-center justify-center text-white shadow-md shadow-[#283593]/25">
                            <i class="fas fa-book-open text-sm"></i>
                        </span>
                        <h2 class="font-heading text-lg font-bold text-slate-800"><?php echo e(__('student.my_active_courses')); ?></h2>
                    </div>
                    <div class="flex items-center gap-3 flex-wrap justify-end">
                        <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'student.view.courses')): ?>
                        <a href="<?php echo e(route('public.courses')); ?>" class="text-[#283593] dark:text-indigo-400 hover:text-[#1f2a7a] dark:hover:text-indigo-300 text-sm font-semibold flex items-center gap-1 transition-colors">
                            <i class="fas fa-th-large text-[10px]"></i>
                            <?php echo e(__('student.courses')); ?>

                        </a>
                        <?php endif; ?>
                        <a href="<?php echo e(route('my-courses.index')); ?>" class="text-[#FB5607] hover:text-[#e84d00] text-sm font-semibold flex items-center gap-1 transition-colors">
                            <?php echo e(__('student.view_all')); ?> <i class="fas fa-arrow-left text-[10px]"></i>
                        </a>
                    </div>
                </div>
                <div class="p-5 space-y-1">
                    <?php $__empty_1 = true; $__currentLoopData = $activeCourses->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $prog = (float) ($course->pivot->progress ?? optional($course->enrollment ?? null)->progress ?? 0); ?>
                        <a href="<?php echo e(route('my-courses.show', $course->id)); ?>" class="course-row flex items-center gap-4 block">
                            <div class="w-11 h-11 rounded-xl bg-[#FFE5F7] border border-[#f5c7e8] flex items-center justify-center text-[#283593] flex-shrink-0">
                                <i class="fas fa-book text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-slate-800 text-sm truncate mb-1"><?php echo e($course->title); ?></h3>
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 progress-bar">
                                        <div class="fill" style="width: <?php echo e($prog); ?>%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-slate-600 min-w-[36px] text-left"><?php echo e($prog); ?>%</span>
                                    <span class="text-[10px] font-bold text-amber-600 flex items-center gap-0.5 flex-shrink-0">
                                        <i class="fas fa-star text-amber-400"></i>
                                        <?php echo e(number_format((float)($course->student_points ?? 0), 0)); ?>

                                    </span>
                                </div>
                            </div>
                            <i class="fas fa-chevron-left text-slate-300 text-[10px] flex-shrink-0 hidden sm:block"></i>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700/80 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-book-open text-2xl text-slate-300 dark:text-slate-500"></i>
                            </div>
                            <p class="font-heading font-bold text-slate-700 dark:text-slate-200 mb-1"><?php echo e(__('student.no_active_courses')); ?></p>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-5"><?php echo e(__('student.start_journey_now')); ?></p>
                            <a href="<?php echo e(route('public.courses')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#283593] to-[#1F2A7A] text-white rounded-xl text-sm font-bold shadow-lg shadow-[#283593]/25 hover:shadow-xl transition-all">
                                <i class="fas fa-search text-xs"></i>
                                <?php echo e(__('student.explore_courses')); ?>

                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="space-y-6 min-w-0">
            
            <div class="section-card">
                <div class="flex items-center justify-between p-4 pb-0">
                    <div class="flex items-center gap-2.5">
                        <span class="w-9 h-9 rounded-lg bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center text-white shadow-md shadow-amber-500/20">
                            <i class="fas fa-tasks text-xs"></i>
                        </span>
                        <h3 class="font-heading font-bold text-slate-800 text-sm"><?php echo e(__('student.assignments')); ?></h3>
                    </div>
                    <?php if($upcomingAssignments->count() > 0): ?>
                        <span class="badge bg-amber-50 text-amber-700 border border-amber-200"><?php echo e($upcomingAssignments->count()); ?></span>
                    <?php endif; ?>
                </div>
                <div class="p-4 space-y-2">
                    <?php $__empty_1 = true; $__currentLoopData = $upcomingAssignments->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $lecture = $assignment->lecture ?? null;
                            $course = $assignment->course ?? optional($lecture)->course;
                            $dueDate = optional($assignment->due_date);
                            $isOverdue = $dueDate && $dueDate->isPast();
                        ?>
                        <div class="mini-card">
                            <div class="font-bold text-slate-800 text-xs mb-1 truncate"><?php echo e($assignment->title); ?></div>
                            <?php if($course): ?>
                                <div class="text-[11px] text-slate-500 mb-2 truncate"><?php echo e($course->title); ?></div>
                            <?php endif; ?>
                            <?php if($dueDate): ?>
                                <span class="badge <?php echo e($isOverdue ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-[#FFE5F7] text-[#283593] border border-[#f5c7e8]'); ?>">
                                    <i class="fas fa-calendar text-[9px]"></i>
                                    <?php echo e($dueDate->translatedFormat('d M')); ?>

                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center py-6 text-slate-400">
                            <i class="fas fa-clipboard-check text-2xl mb-2 opacity-40"></i>
                            <p class="text-xs font-medium"><?php echo e(__('student.no_assignments')); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="section-card">
                <div class="flex items-center justify-between p-4 pb-0">
                    <div class="flex items-center gap-2.5">
                        <span class="w-9 h-9 rounded-lg bg-gradient-to-br from-[#283593] to-[#FB5607] flex items-center justify-center text-white shadow-md shadow-[#283593]/25">
                            <i class="fas fa-clipboard-check text-xs"></i>
                        </span>
                        <h3 class="font-heading font-bold text-slate-800 text-sm"><?php echo e(__('student.exams')); ?></h3>
                    </div>
                    <?php if($upcomingExams->count() > 0): ?>
                        <span class="badge bg-[#FFE5F7] text-[#283593] border border-[#f5c7e8]"><?php echo e($upcomingExams->count()); ?></span>
                    <?php endif; ?>
                </div>
                <div class="p-4 space-y-2">
                    <?php $__empty_1 = true; $__currentLoopData = $upcomingExams->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $course = $exam->course;
                            $startAt = $exam->start_time ?? ($exam->start_date ? $exam->start_date->copy()->startOfDay() : null);
                            $isAvailableNow = $startAt ? $startAt->isPast() : true;
                        ?>
                        <a href="<?php echo e(route('student.exams.show', $exam)); ?>" class="mini-card block hover:border-indigo-200">
                            <div class="font-bold text-slate-800 text-xs mb-1 truncate"><?php echo e($exam->title); ?></div>
                            <?php if($course): ?>
                                <div class="text-[11px] text-slate-500 mb-2 truncate"><?php echo e($course->title); ?></div>
                            <?php endif; ?>
                            <span class="badge <?php echo e($isAvailableNow ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200'); ?>">
                                <i class="fas <?php echo e($isAvailableNow ? 'fa-play-circle' : 'fa-clock'); ?> text-[9px]"></i>
                                <?php echo e($isAvailableNow ? __('student.available') : __('student.coming_soon')); ?>

                            </span>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center py-6 text-slate-400">
                            <i class="fas fa-file-alt text-2xl mb-2 opacity-40"></i>
                            <p class="text-xs font-medium"><?php echo e(__('student.no_exams')); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="section-card min-w-0">
            <div class="flex items-center gap-3 p-5 pb-0">
                <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white shadow-md shadow-emerald-500/20">
                    <i class="fas fa-chart-pie text-sm"></i>
                </span>
                <h2 class="font-heading text-base font-bold text-slate-800"><?php echo e(__('student.exam_results')); ?></h2>
            </div>
            <div class="p-5 space-y-2">
                <?php $__empty_1 = true; $__currentLoopData = $recentExamAttempts->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attempt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $exam = $attempt->exam; $course = optional($exam)->course; ?>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50/80 border border-slate-100 hover:border-emerald-200 transition-colors">
                        <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-award text-emerald-500"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-slate-800 text-xs truncate"><?php echo e($exam->title ?? __('student.exam_deleted')); ?></div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="badge bg-emerald-50 text-emerald-700 border border-emerald-200"><?php echo e($attempt->result_status); ?></span>
                                <?php if(!is_null($attempt->percentage)): ?>
                                    <span class="text-xs font-bold text-slate-600"><?php echo e(number_format($attempt->percentage, 1)); ?>%</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if($exam): ?>
                            <a href="<?php echo e(route('student.exams.result', [$exam, $attempt])); ?>" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-[11px] font-bold hover:bg-emerald-700 transition-colors shadow-sm flex-shrink-0">
                                <?php echo e(__('common.view')); ?>

                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-10 text-slate-400">
                        <i class="fas fa-poll text-3xl mb-2 opacity-30"></i>
                        <p class="text-xs font-medium"><?php echo e(__('student.no_results_yet')); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="section-card min-w-0">
            <div class="flex items-center gap-3 p-5 pb-0">
                <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-amber-500 flex items-center justify-center text-white shadow-md shadow-amber-400/20">
                    <i class="fas fa-certificate text-sm"></i>
                </span>
                <h2 class="font-heading text-base font-bold text-slate-800"><?php echo e(__('student.issued_certificates')); ?></h2>
            </div>
            <div class="p-5 space-y-2">
                <?php $__empty_1 = true; $__currentLoopData = $recentCertificates->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $certificate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50/80 border border-slate-100 hover:border-amber-200 transition-colors">
                        <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-ribbon text-amber-500"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-slate-800 text-xs truncate">
                                <?php echo e($certificate->title ?? $certificate->course_name ?? __('student.certificate_untitled')); ?>

                            </div>
                            <?php if($certificate->certificate_number): ?>
                                <span class="badge bg-[#FFE5F7] text-[#283593] border border-[#f5c7e8] mt-1">
                                    <?php echo e($certificate->certificate_number); ?>

                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-10 text-slate-400">
                        <i class="fas fa-certificate text-3xl mb-2 opacity-30"></i>
                        <p class="text-xs font-medium"><?php echo e(__('student.no_certificates_yet')); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\dashboard\student.blade.php ENDPATH**/ ?>