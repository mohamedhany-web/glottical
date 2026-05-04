<?php $__env->startSection('title', __('instructor.my_courses') . ' - ' . config('app.name', 'Muallimx')); ?>
<?php $__env->startSection('header', __('instructor.my_courses')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="rounded-2xl p-6 text-white shadow-lg border border-white/10 bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-white/15 border border-white/20 flex items-center justify-center shrink-0">
                        <i class="fas fa-book text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-xl sm:text-2xl font-black leading-tight truncate"><?php echo e(__('instructor.my_courses')); ?></h1>
                        <p class="text-sm text-white/90 mt-0.5"><?php echo e(__('instructor.courses_assigned_to_you')); ?></p>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('instructor.lectures.index')); ?>"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/15 hover:bg-white/20 border border-white/20 text-white font-semibold transition-colors">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span><?php echo e(__('instructor.lectures')); ?></span>
                </a>
                <?php if(\Illuminate\Support\Facades\Route::has('instructor.calendar.index')): ?>
                    <a href="<?php echo e(route('instructor.calendar.index')); ?>"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/15 hover:bg-white/20 border border-white/20 text-white font-semibold transition-colors">
                        <i class="fas fa-calendar-alt"></i>
                        <span><?php echo e(__('instructor.calendar') ?? 'التقويم'); ?></span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl p-5 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1"><?php echo e(__('instructor.total_courses')); ?></p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-slate-100"><?php echo e($stats['total'] ?? 0); ?></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 dark:bg-sky-900/30 flex items-center justify-center">
                <i class="fas fa-book text-sky-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1"><?php echo e(__('instructor.active')); ?></p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-slate-100"><?php echo e($stats['active'] ?? 0); ?></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1"><?php echo e(__('instructor.inactive')); ?></p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-slate-100"><?php echo e($stats['inactive'] ?? 0); ?></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center">
                <i class="fas fa-ban text-amber-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1"><?php echo e(__('instructor.total_students')); ?></p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-slate-100"><?php echo e($stats['total_students'] ?? 0); ?></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center">
                <i class="fas fa-user-graduate text-violet-600 text-lg"></i>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div class="bg-white dark:bg-slate-800/95 rounded-2xl p-5 sm:p-6 border border-slate-200 dark:border-slate-700 shadow-sm">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2"><?php echo e(__('common.search')); ?></label>
                <input type="text" name="search" id="search" value="<?php echo e(request('search')); ?>" 
                       placeholder="<?php echo e(__('instructor.search_in_course_titles')); ?>"
                       class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
            </div>
            <div>
                <label for="status" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2"><?php echo e(__('common.status')); ?></label>
                <select name="status" id="status" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-100 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-colors">
                    <option value=""><?php echo e(__('instructor.all_statuses')); ?></option>
                    <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>><?php echo e(__('instructor.active_status')); ?></option>
                    <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>><?php echo e(__('instructor.inactive_status')); ?></option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-sky-500 dark:bg-sky-600 hover:bg-sky-600 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-search"></i>
                    <span><?php echo e(__('common.search')); ?></span>
                </button>
                <?php if(request()->anyFilled(['search', 'status'])): ?>
                    <a href="<?php echo e(route('instructor.courses.index')); ?>" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700/50 hover:bg-slate-200 text-slate-700 dark:text-slate-300 rounded-xl font-semibold transition-colors inline-flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- قائمة الكورسات -->
    <?php if($courses->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-2xl overflow-hidden bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-shadow">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 truncate flex-1"><?php echo e($course->title); ?></h3>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold shrink-0 <?php echo e($course->is_active ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400' : 'bg-rose-100 dark:bg-rose-900/40 text-rose-700 dark:text-rose-400'); ?>">
                            <i class="fas <?php echo e($course->is_active ? 'fa-check-circle' : 'fa-ban'); ?>"></i>
                            <?php echo e($course->is_active ? __('instructor.active_status') : __('instructor.inactive_status')); ?>

                        </span>
                    </div>
                </div>

                <div class="px-5 py-4">
                    <?php if($course->description): ?>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mb-4 line-clamp-2"><?php echo e(Str::limit($course->description, 100)); ?></p>
                    <?php endif; ?>

                    <div class="space-y-2 mb-4">
                        <?php if($course->academicYear): ?>
                        <div class="flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-graduation-cap text-sky-600 text-xs"></i>
                            </div>
                            <span class="text-slate-500 dark:text-slate-400"><?php echo e(__('instructor.year')); ?>:</span>
                            <span class="text-slate-800 dark:text-slate-100 font-medium"><?php echo e($course->academicYear->name); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($course->academicSubject): ?>
                        <div class="flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-book text-violet-600 text-xs"></i>
                            </div>
                            <span class="text-slate-500 dark:text-slate-400"><?php echo e(__('instructor.subject')); ?>:</span>
                            <span class="text-slate-800 dark:text-slate-100 font-medium"><?php echo e($course->academicSubject->name); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($course->programming_language): ?>
                        <div class="flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-cyan-50 dark:bg-cyan-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-code text-cyan-600 text-xs"></i>
                            </div>
                            <span class="text-slate-500 dark:text-slate-400"><?php echo e(__('instructor.language_label')); ?>:</span>
                            <span class="text-slate-800 dark:text-slate-100 font-medium"><?php echo e($course->programming_language); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($course->level): ?>
                        <div class="flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-signal text-emerald-600 text-xs"></i>
                            </div>
                            <span class="text-slate-500 dark:text-slate-400"><?php echo e(__('instructor.level_label')); ?>:</span>
                            <span class="text-slate-800 dark:text-slate-100 font-medium">
                                <?php if($course->level == 'beginner'): ?> <?php echo e(__('instructor.beginner')); ?>

                                <?php elseif($course->level == 'intermediate'): ?> <?php echo e(__('instructor.intermediate')); ?>

                                <?php else: ?> <?php echo e(__('instructor.advanced')); ?>

                                <?php endif; ?>
                            </span>
                        </div>
                        <?php endif; ?>
                        <?php if(!$course->is_free && $course->effectivePurchasePrice() > 0): ?>
                        <div class="flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-money-bill-wave text-amber-600 text-xs"></i>
                            </div>
                            <span class="text-slate-500 dark:text-slate-400"><?php echo e(__('instructor.price')); ?>:</span>
                            <span class="text-slate-800 dark:text-slate-100 font-semibold flex flex-col items-start tabular-nums">
                                <?php if($course->hasPromotionalPrice()): ?>
                                    <span class="text-xs text-slate-400 line-through"><?php echo e(number_format($course->listPriceAmount(), 2)); ?> ج.م</span>
                                <?php endif; ?>
                                <span><?php echo e(number_format($course->effectivePurchasePrice(), 2)); ?> ج.م</span>
                            </span>
                        </div>
                        <?php else: ?>
                        <div class="flex items-center gap-2 text-sm">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-gift text-emerald-600 text-xs"></i>
                            </div>
                            <span class="text-emerald-600 font-semibold"><?php echo e(__('instructor.free')); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/60 border-t border-slate-200 dark:border-slate-700">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div>
                            <div class="text-lg font-bold text-slate-800 dark:text-slate-100"><?php echo e($course->lectures_count ?? 0); ?></div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 font-medium"><?php echo e(__('instructor.lecture_single')); ?></div>
                        </div>
                        <div>
                            <div class="text-lg font-bold text-slate-800 dark:text-slate-100"><?php echo e($course->enrollments_count ?? 0); ?></div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 font-medium"><?php echo e(__('instructor.student_single')); ?></div>
                        </div>
                    </div>
                </div>

                <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-700">
                    <a href="<?php echo e(route('instructor.courses.show', $course)); ?>" 
                       class="w-full inline-flex items-center justify-center gap-2 bg-sky-500 dark:bg-sky-600 hover:bg-sky-600 text-white px-4 py-2.5 rounded-xl font-semibold transition-colors">
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
                <i class="fas fa-book-open text-4xl text-sky-500"></i>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100 mb-2"><?php echo e(__('instructor.no_courses')); ?></h3>
            <p class="text-slate-500 dark:text-slate-400 max-w-md mx-auto"><?php echo e(__('instructor.courses_description_empty')); ?></p>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\instructor\courses\index.blade.php ENDPATH**/ ?>