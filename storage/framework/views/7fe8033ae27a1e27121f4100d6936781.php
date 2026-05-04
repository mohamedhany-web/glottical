

<?php $__env->startSection('title', __('instructor.create_exam_new') . ' - ' . config('app.name', 'Muallimx')); ?>
<?php $__env->startSection('header', __('instructor.create_exam_new')); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- هيدر الصفحة (عرض الصفحة كاملاً) -->
    <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm p-5 sm:p-6 mb-6">
        <nav class="text-sm text-slate-500 dark:text-slate-400 mb-2">
            <a href="<?php echo e(route('instructor.exams.index')); ?>" class="hover:text-sky-600 transition-colors"><?php echo e(__('instructor.my_exams')); ?></a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 dark:text-slate-300 font-semibold"><?php echo e(__('instructor.create_exam_new')); ?></span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-clipboard-list text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100"><?php echo e(__('instructor.create_exam_new')); ?></h1>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-0.5"><?php echo e(__('instructor.add_exam_to_course')); ?></p>
                </div>
            </div>
            <a href="<?php echo e(route('instructor.exams.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-700/50 hover:bg-slate-200 text-slate-700 dark:text-slate-300 rounded-xl font-semibold transition-colors">
                <i class="fas fa-arrow-right"></i>
                <?php echo e(__('instructor.back')); ?>

            </a>
        </div>
    </div>

    <!-- بطاقة النموذج -->
    <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <form action="<?php echo e(route('instructor.exams.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="p-6 sm:p-8 space-y-8">
                <!-- معلومات الاختبار -->
                <div class="space-y-6">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 border-b border-slate-200 dark:border-slate-700 pb-2"><?php echo e(__('instructor.exam_info')); ?></h2>
                    <div>
                        <label for="title" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1"><?php echo e(__('instructor.title')); ?> <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="<?php echo e(old('title')); ?>" required
                               class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100"
                               placeholder="<?php echo e(__('instructor.exam_title_placeholder')); ?>">
                        <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="advanced_course_id" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1"><?php echo e(__('instructor.online_course')); ?> <span class="text-red-500">*</span></label>
                            <select name="advanced_course_id" id="advanced_course_id" onchange="loadLessons()"
                                    class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100">
                                <option value=""><?php echo e(__('instructor.choose_course')); ?></option>
                                <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($course->id); ?>" <?php echo e(old('advanced_course_id') == $course->id ? 'selected' : ''); ?>><?php echo e($course->title); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['advanced_course_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div id="lesson-wrap">
                            <label for="course_lesson_id" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1"><?php echo e(__('instructor.lesson_optional')); ?></label>
                            <select name="course_lesson_id" id="course_lesson_id"
                                    class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100">
                                <option value=""><?php echo e(__('instructor.general_exam')); ?></option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1"><?php echo e(__('instructor.description')); ?></label>
                        <textarea name="description" id="description" rows="3" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100" placeholder="<?php echo e(__('instructor.description_placeholder')); ?>"><?php echo e(old('description')); ?></textarea>
                    </div>
                    <div>
                        <label for="instructions" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1"><?php echo e(__('instructor.exam_instructions')); ?></label>
                        <textarea name="instructions" id="instructions" rows="4" class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100" placeholder="<?php echo e(__('instructor.instructions_placeholder')); ?>"><?php echo e(old('instructions')); ?></textarea>
                    </div>
                </div>

                <!-- التوقيت والدرجات -->
                <div class="space-y-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 border-b border-slate-200 dark:border-slate-700 pb-2"><?php echo e(__('instructor.time_and_marks')); ?></h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <label for="duration_minutes" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1"><?php echo e(__('instructor.duration_minutes')); ?> (<?php echo e(__('instructor.minute_unit')); ?>) <span class="text-red-500">*</span></label>
                            <input type="number" name="duration_minutes" id="duration_minutes" value="<?php echo e(old('duration_minutes', 60)); ?>" min="5" max="480" required
                                   class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100">
                        </div>
                        <div>
                            <label for="total_marks" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1"><?php echo e(__('instructor.total_marks')); ?> <span class="text-red-500">*</span></label>
                            <input type="number" name="total_marks" id="total_marks" value="<?php echo e(old('total_marks', 100)); ?>" min="1" required
                                   class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100">
                        </div>
                        <div>
                            <label for="passing_marks" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1"><?php echo e(__('instructor.passing_marks')); ?> <span class="text-red-500">*</span></label>
                            <input type="number" name="passing_marks" id="passing_marks" value="<?php echo e(old('passing_marks', 60)); ?>" min="0" step="0.5" required
                                   class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100">
                        </div>
                        <div>
                            <label for="attempts_allowed" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1"><?php echo e(__('instructor.attempts_allowed')); ?> <span class="text-red-500">*</span></label>
                            <input type="number" name="attempts_allowed" id="attempts_allowed" value="<?php echo e(old('attempts_allowed', 1)); ?>" min="1" max="10" required
                                   class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="start_time" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1"><?php echo e(__('instructor.start_time')); ?></label>
                            <input type="datetime-local" name="start_time" id="start_time" value="<?php echo e(old('start_time')); ?>"
                                   class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100">
                        </div>
                        <div>
                            <label for="end_time" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1"><?php echo e(__('instructor.end_time')); ?></label>
                            <input type="datetime-local" name="end_time" id="end_time" value="<?php echo e(old('end_time')); ?>"
                                   class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100">
                        </div>
                    </div>
                </div>

                <!-- إعدادات العرض والحالة -->
                <div class="space-y-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 border-b border-slate-200 dark:border-slate-700 pb-2"><?php echo e(__('instructor.display_settings')); ?></h2>
                    <div class="flex flex-wrap gap-6">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="randomize_questions" value="1" <?php echo e(old('randomize_questions') ? 'checked' : ''); ?> class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300"><?php echo e(__('instructor.randomize_questions')); ?></span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="randomize_options" value="1" <?php echo e(old('randomize_options') ? 'checked' : ''); ?> class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300"><?php echo e(__('instructor.randomize_options')); ?></span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="show_results_immediately" value="1" <?php echo e(old('show_results_immediately', true) ? 'checked' : ''); ?> class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300"><?php echo e(__('instructor.show_results_immediately')); ?></span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="show_correct_answers" value="1" <?php echo e(old('show_correct_answers') ? 'checked' : ''); ?> class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300"><?php echo e(__('instructor.show_correct_answers')); ?></span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="show_explanations" value="1" <?php echo e(old('show_explanations') ? 'checked' : ''); ?> class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300"><?php echo e(__('instructor.show_explanations')); ?></span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="allow_review" value="1" <?php echo e(old('allow_review', true) ? 'checked' : ''); ?> class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300"><?php echo e(__('instructor.allow_review')); ?></span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', true) ? 'checked' : ''); ?> class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300"><?php echo e(__('instructor.exam_active')); ?></span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="show_in_sidebar" value="1" <?php echo e(old('show_in_sidebar', true) ? 'checked' : ''); ?> class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300"><?php echo e(__('instructor.show_in_sidebar')); ?></span>
                        </label>
                    </div>
                    <div class="max-w-xs">
                        <label for="sidebar_position" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1"><?php echo e(__('instructor.sidebar_position')); ?></label>
                        <input type="number" name="sidebar_position" id="sidebar_position" value="<?php echo e(old('sidebar_position', 1)); ?>" min="1" max="10"
                               class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100">
                    </div>
                </div>

                <!-- نصائح وأزرار -->
                <div class="pt-6 border-t border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="bg-sky-50 dark:bg-sky-900/30 border border-sky-200 rounded-xl p-4 text-sm text-sky-800 max-w-md">
                        <span class="font-semibold"><?php echo e(__('instructor.tips')); ?>:</span> <?php echo e(__('instructor.tip_after_create_exam')); ?>

                    </div>
                    <div class="flex gap-3 shrink-0">
                        <button type="submit" class="px-6 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-semibold transition-colors">
                            <i class="fas fa-save ml-2"></i>
                            <?php echo e(__('instructor.create_exam_btn')); ?>

                        </button>
                        <a href="<?php echo e(route('instructor.exams.index')); ?>" class="px-6 py-2.5 bg-slate-100 dark:bg-slate-700/50 hover:bg-slate-200 text-slate-700 dark:text-slate-300 rounded-xl font-semibold transition-colors">
                            <?php echo e(__('common.cancel')); ?>

                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function loadLessons() {
    const courseId = document.getElementById('advanced_course_id').value;
    const lessonSelect = document.getElementById('course_lesson_id');
    
    lessonSelect.innerHTML = '<option value="">اختبار عام للكورس</option>';
    
    if (courseId) {
        fetch(`/instructor/api/courses/${courseId}/lessons-list`)
            .then(response => response.json())
            .then(lessons => {
                (Array.isArray(lessons) ? lessons : []).forEach(lesson => {
                    const option = document.createElement('option');
                    option.value = lesson.id;
                    option.textContent = lesson.title || ('درس ' + (lesson.order || ''));
                    lessonSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error loading lessons:', error));
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const courseId = document.getElementById('advanced_course_id').value;
    if (courseId) loadLessons();
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\instructor\exams\create.blade.php ENDPATH**/ ?>