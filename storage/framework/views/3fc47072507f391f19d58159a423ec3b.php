<?php $__env->startSection('title', __('instructor.exams')); ?>
<?php $__env->startSection('header', __('instructor.exams')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="rounded-2xl p-6 text-white shadow-lg border border-white/10 bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-white/15 border border-white/20 flex items-center justify-center shrink-0">
                        <i class="fas fa-clipboard-check text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-xl sm:text-2xl font-black leading-tight truncate"><?php echo e(__('instructor.exams')); ?></h1>
                        <p class="text-sm text-white/90 mt-0.5"><?php echo e(__('instructor.manage_exams_attempts')); ?></p>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a href="<?php echo e(route('instructor.courses.index')); ?>"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/15 hover:bg-white/20 border border-white/20 text-white font-semibold transition-colors">
                    <i class="fas fa-book"></i>
                    <span><?php echo e(__('instructor.courses')); ?></span>
                </a>
                <a href="<?php echo e(route('instructor.exams.create')); ?>"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-indigo-700 hover:bg-white/90 border border-white/20 font-extrabold transition-colors">
                    <i class="fas fa-plus"></i>
                    <span><?php echo e(__('instructor.create_exam')); ?></span>
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-xl p-4 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase"><?php echo e(__('instructor.total')); ?></p>
                    <p class="text-2xl font-bold text-slate-800 dark:text-slate-100"><?php echo e($stats['total'] ?? 0); ?></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center text-sky-600"><i class="fas fa-clipboard-check"></i></div>
            </div>
        </div>
        <div class="rounded-xl p-4 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase"><?php echo e(__('instructor.active')); ?></p>
                    <p class="text-2xl font-bold text-slate-800 dark:text-slate-100"><?php echo e($stats['active'] ?? 0); ?></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="rounded-xl p-4 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase"><?php echo e(__('instructor.attempts')); ?></p>
                    <p class="text-2xl font-bold text-slate-800 dark:text-slate-100"><?php echo e($stats['total_attempts'] ?? 0); ?></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center text-violet-600"><i class="fas fa-redo"></i></div>
            </div>
        </div>
        <div class="rounded-xl p-4 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase"><?php echo e(__('instructor.completed_attempts')); ?></p>
                    <p class="text-2xl font-bold text-slate-800 dark:text-slate-100"><?php echo e($stats['completed_attempts'] ?? 0); ?></p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600"><i class="fas fa-check-double"></i></div>
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm p-5">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="course_id" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1"><?php echo e(__('instructor.online_course')); ?></label>
                <select name="course_id" id="course_id" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100">
                    <option value=""><?php echo e(__('instructor.all_online_courses')); ?></option>
                    <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($course->id); ?>" <?php echo e(request('course_id') == $course->id ? 'selected' : ''); ?>><?php echo e($course->title); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label for="is_active" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1"><?php echo e(__('common.status')); ?></label>
                <select name="is_active" id="is_active" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100">
                    <option value=""><?php echo e(__('instructor.all')); ?></option>
                    <option value="1" <?php echo e(request('is_active') == '1' ? 'selected' : ''); ?>><?php echo e(__('instructor.active')); ?></option>
                    <option value="0" <?php echo e(request('is_active') == '0' ? 'selected' : ''); ?>><?php echo e(__('instructor.inactive')); ?></option>
                </select>
            </div>
            <div>
                <label for="search" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1"><?php echo e(__('common.search')); ?></label>
                <input type="text" name="search" id="search" value="<?php echo e(request('search')); ?>" placeholder="<?php echo e(__('instructor.search_placeholder')); ?>" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2.5 bg-sky-500 dark:bg-sky-600 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-search ml-1"></i> <?php echo e(__('common.search')); ?>

                </button>
                <?php if(request()->anyFilled(['course_id', 'is_active', 'search'])): ?>
                    <a href="<?php echo e(route('instructor.exams.index')); ?>" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700/50 hover:bg-slate-200 text-slate-700 dark:text-slate-300 rounded-xl font-semibold transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if($exams->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <?php $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm hover:border-sky-300 hover:shadow-md transition-all overflow-hidden flex flex-col">
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 line-clamp-2 flex-1 min-w-0"><?php echo e($exam->title); ?></h3>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold shrink-0 <?php echo e($exam->is_active ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400' : 'bg-amber-100 text-amber-700'); ?>">
                                <?php echo e($exam->is_active ? __('instructor.active_status') : __('instructor.inactive_status')); ?>

                            </span>
                        </div>
                        <?php if($exam->description): ?>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mb-3 line-clamp-2"><?php echo e($exam->description); ?></p>
                        <?php endif; ?>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-slate-500 dark:text-slate-400 mt-auto">
                            <span class="truncate max-w-full" title="<?php echo e($exam->advancedCourse->title ?? '—'); ?>"><i class="fas fa-book text-sky-500 ml-1"></i> <?php echo e($exam->advancedCourse->title ?? '—'); ?></span>
                            <span><?php echo e($exam->duration_minutes); ?> د</span>
                            <span><?php echo e($exam->total_marks); ?> <?php echo e(__('instructor.marks')); ?></span>
                            <span><?php echo e($exam->questions_count); ?> <?php echo e(__('instructor.question_single')); ?></span>
                            <span><?php echo e($exam->attempts_count); ?> <?php echo e(__('instructor.attempt_single')); ?></span>
                        </div>
                    </div>
                    <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-700/80 bg-slate-50 dark:bg-slate-800/50 flex flex-wrap items-center gap-2">
                        <a href="<?php echo e(route('instructor.exams.questions.manage', $exam)); ?>" class="inline-flex items-center gap-1.5 px-3 py-2 bg-violet-600 dark:bg-violet-700 hover:bg-violet-600 text-white rounded-lg font-semibold text-sm transition-colors">
                            <i class="fas fa-list"></i> <?php echo e(__('instructor.questions')); ?>

                        </a>
                        <a href="<?php echo e(route('instructor.exams.show', $exam)); ?>" class="inline-flex items-center gap-1.5 px-3 py-2 bg-sky-500 dark:bg-sky-600 hover:bg-sky-600 text-white rounded-lg font-semibold text-sm transition-colors">
                            <i class="fas fa-eye"></i> <?php echo e(__('common.view')); ?>

                        </a>
                        <form action="<?php echo e(route('instructor.exams.destroy', $exam)); ?>" method="POST" class="inline" onsubmit="return confirm('<?php echo e(__('instructor.confirm_delete_exam')); ?>');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-50 dark:bg-red-900/30 hover:bg-red-100 text-red-600 rounded-lg font-semibold text-sm transition-colors border border-red-100 hover:border-red-200" title="<?php echo e(__('instructor.delete_exam_title')); ?>">
                                <i class="fas fa-trash-alt"></i> <?php echo e(__('common.delete')); ?>

                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="flex justify-center">
            <div class="rounded-xl p-3 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm"><?php echo e($exams->links()); ?></div>
        </div>
    <?php else: ?>
        <div class="rounded-xl border border-dashed border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 py-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-sky-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-clipboard-check text-2xl text-sky-500"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2"><?php echo e(__('instructor.no_exams')); ?></h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4"><?php echo e(__('instructor.no_exams_description')); ?></p>
            <a href="<?php echo e(route('instructor.exams.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-500 dark:bg-sky-600 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i> <?php echo e(__('instructor.create_exam')); ?>

            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\instructor\exams\index.blade.php ENDPATH**/ ?>