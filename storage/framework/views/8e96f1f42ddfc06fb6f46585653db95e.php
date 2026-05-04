<?php $__env->startSection('title', __('instructor.lectures') . ' - ' . config('app.name', 'Muallimx')); ?>
<?php $__env->startSection('header', __('instructor.lectures')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="rounded-2xl p-6 text-white shadow-lg border border-white/10 bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-white/15 border border-white/20 flex items-center justify-center shrink-0">
                        <i class="fas fa-chalkboard-teacher text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-xl sm:text-2xl font-black leading-tight truncate"><?php echo e(__('instructor.lectures')); ?></h1>
                        <p class="text-sm text-white/90 mt-0.5"><?php echo e(__('instructor.manage_lectures_curriculum')); ?></p>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('instructor.courses.index')); ?>"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/15 hover:bg-white/20 border border-white/20 text-white font-semibold transition-colors">
                    <i class="fas fa-book"></i>
                    <span><?php echo e(__('instructor.courses')); ?></span>
                </a>
                <a href="<?php echo e(route('instructor.lectures.create')); ?>"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-indigo-700 hover:bg-white/90 border border-white/20 font-extrabold transition-colors">
                    <i class="fas fa-plus"></i>
                    <span><?php echo e(__('instructor.add_new_lecture')); ?></span>
                </a>
            </div>
        </div>
    </div>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="rounded-2xl p-5 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1"><?php echo e(__('instructor.total_courses')); ?></p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-slate-100"><?php echo e($stats['total_courses'] ?? 0); ?></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 dark:bg-sky-900/30 flex items-center justify-center">
                <i class="fas fa-book text-sky-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1"><?php echo e(__('instructor.active_courses')); ?></p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-slate-100"><?php echo e($stats['active_courses'] ?? 0); ?></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1"><?php echo e(__('instructor.total_lectures')); ?></p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-slate-100"><?php echo e($stats['total_lectures'] ?? 0); ?></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center">
                <i class="fas fa-chalkboard-teacher text-violet-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1"><?php echo e(__('instructor.scheduled_lectures')); ?></p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-slate-100"><?php echo e($stats['scheduled_lectures'] ?? 0); ?></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center">
                <i class="fas fa-calendar-alt text-amber-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1"><?php echo e(__('instructor.total_students')); ?></p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-slate-100"><?php echo e($stats['total_students'] ?? 0); ?></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center">
                <i class="fas fa-users text-violet-600 text-lg"></i>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div class="bg-white dark:bg-slate-800/95 rounded-2xl p-5 sm:p-6 border border-slate-200 dark:border-slate-700 shadow-sm">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2"><?php echo e(__('common.search')); ?></label>
                <input type="text" name="search" id="search" value="<?php echo e(request('search')); ?>" 
                       placeholder="<?php echo e(__('instructor.search_in_courses')); ?>"
                       class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
            </div>
            <div>
                <label for="status" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2"><?php echo e(__('common.status')); ?></label>
                <select name="status" id="status" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                    <option value=""><?php echo e(__('instructor.all_statuses')); ?></option>
                    <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>><?php echo e(__('instructor.active_status')); ?></option>
                    <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>><?php echo e(__('instructor.not_active')); ?></option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-sky-500 dark:bg-sky-600 hover:bg-sky-600 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-search"></i>
                    <span class="mr-2"><?php echo e(__('common.search')); ?></span>
                </button>
                <?php if(request()->anyFilled(['search', 'status'])): ?>
                    <a href="<?php echo e(route('instructor.lectures.index')); ?>" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700/50 hover:bg-slate-200 text-slate-700 dark:text-slate-300 rounded-xl font-semibold transition-colors inline-flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- قائمة الكورسات -->
    <?php if($courses->count() > 0): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 max-w-6xl">
            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-xl overflow-hidden bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow flex flex-col">
                <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between gap-2 flex-wrap">
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 truncate flex-1 min-w-0"><?php echo e($course->title); ?></h3>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold shrink-0 <?php echo e($course->is_active ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-400'); ?>">
                            <?php if($course->is_active): ?><i class="fas fa-check-circle"></i> <?php echo e(__('instructor.active_status')); ?>

                            <?php else: ?><i class="fas fa-times-circle"></i> <?php echo e(__('instructor.not_active')); ?>

                            <?php endif; ?>
                        </span>
                    </div>
                    <?php if($course->academicYear): ?>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1"><?php echo e($course->academicYear->name); ?></p>
                    <?php endif; ?>
                </div>

                <div class="p-4 flex-1">
                    <div class="grid grid-cols-2 gap-x-3 gap-y-1.5 text-xs">
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-book-open text-violet-500 w-4 text-center"></i>
                            <span class="text-slate-500 dark:text-slate-400"><?php echo e(__('instructor.sections_label')); ?>:</span>
                            <span class="text-slate-800 dark:text-slate-100 font-medium"><?php echo e($course->sections_count ?? 0); ?></span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-chalkboard-teacher text-amber-500 w-4 text-center"></i>
                            <span class="text-slate-500 dark:text-slate-400"><?php echo e(__('instructor.lectures_label')); ?>:</span>
                            <span class="text-slate-800 dark:text-slate-100 font-medium"><?php echo e($course->lectures_count ?? 0); ?></span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-users text-emerald-500 w-4 text-center"></i>
                            <span class="text-slate-500 dark:text-slate-400"><?php echo e(__('instructor.students_label')); ?>:</span>
                            <span class="text-slate-800 dark:text-slate-100 font-medium"><?php echo e($course->enrollments_count ?? 0); ?></span>
                        </div>
                    </div>
                </div>

                <div class="p-4 pt-0 flex flex-col gap-2">
                    <a href="<?php echo e(route('instructor.courses.curriculum', $course)); ?>" 
                       class="w-full inline-flex items-center justify-center gap-2 bg-sky-500 dark:bg-sky-600 hover:bg-sky-600 text-white px-3 py-2 rounded-lg text-sm font-semibold transition-colors">
                        <i class="fas fa-sitemap"></i>
<span><?php echo e(__('instructor.build_curriculum')); ?></span>
                    </a>
                    <a href="<?php echo e(route('instructor.courses.show', $course)); ?>"
                       class="w-full inline-flex items-center justify-center gap-2 bg-slate-100 dark:bg-slate-700/50 hover:bg-slate-200 text-slate-700 dark:text-slate-300 px-3 py-2 rounded-lg text-sm font-semibold transition-colors">
                        <i class="fas fa-eye"></i>
                        <span><?php echo e(__('instructor.view_details')); ?></span>
                    </a>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="mt-6 flex justify-center">
            <div class="bg-white dark:bg-slate-800/95 rounded-xl p-3 border border-slate-200 dark:border-slate-700 shadow-sm">
                <?php echo e($courses->links()); ?>

            </div>
        </div>
    <?php else: ?>
        <div class="rounded-2xl p-12 sm:p-16 text-center bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="w-24 h-24 rounded-2xl bg-sky-50 dark:bg-sky-900/30 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-chalkboard-teacher text-4xl text-sky-500"></i>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100 mb-2"><?php echo e(__('instructor.no_courses')); ?></h3>
            <p class="text-slate-500 dark:text-slate-400 max-w-md mx-auto"><?php echo e(__('instructor.no_courses_lectures_desc')); ?></p>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\instructor\lectures\index.blade.php ENDPATH**/ ?>