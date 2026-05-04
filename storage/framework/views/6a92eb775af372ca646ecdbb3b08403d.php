

<?php $__env->startSection('title', __('instructor.create_assignment') . ' - ' . config('app.name', 'Muallimx')); ?>
<?php $__env->startSection('header', __('instructor.create_assignment')); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .form-card { background: #fff; border: 1px solid rgb(226 232 240); border-radius: 1rem; transition: box-shadow 0.2s; }
    .form-card:focus-within { box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- الهيدر -->
    <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm p-5 sm:p-6 mb-6">
        <nav class="text-sm text-slate-500 dark:text-slate-400 mb-2">
            <a href="<?php echo e(route('instructor.assignments.index')); ?>" class="hover:text-sky-600 transition-colors"><?php echo e(__('instructor.assignments')); ?></a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 dark:text-slate-300 font-semibold"><?php echo e(__('instructor.create_assignment')); ?></span>
        </nav>
        <div class="flex flex-wrap items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                <i class="fas fa-tasks text-lg"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100"><?php echo e(__('instructor.create_assignment')); ?></h1>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-0.5"><?php echo e(__('instructor.add_assignment_for_course')); ?></p>
            </div>
        </div>
    </div>

    <!-- بطاقة النموذج -->
    <div class="form-card shadow-sm p-6 sm:p-8">
        <?php echo $__env->make('instructor.assignments.create-form', ['courses' => $courses], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const courseSelect = document.getElementById('advanced_course_id');
    if (!courseSelect) return;

    const newCourseSelect = courseSelect.cloneNode(true);
    courseSelect.parentNode.replaceChild(newCourseSelect, courseSelect);

    newCourseSelect.addEventListener('change', function() {
        const courseId = this.value;
        const lessonSelect = document.getElementById('lesson_id');
        if (!lessonSelect) return;

        while (lessonSelect.children.length > 1) lessonSelect.removeChild(lessonSelect.lastChild);
        if (!courseId) return;

        const loadingOption = document.createElement('option');
        loadingOption.value = '';
        loadingOption.textContent = <?php echo json_encode(__('instructor.loading_text'), 15, 512) ?>;
        loadingOption.disabled = true;
        lessonSelect.appendChild(loadingOption);
        lessonSelect.disabled = true;

        fetch('/instructor/api/courses/' + courseId + '/lessons-list', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
            .then(function(r) { return r.ok ? r.json() : Promise.reject(); })
            .then(function(data) {
                loadingOption.remove();
                var lessons = Array.isArray(data) ? data : (data.lessons || []);
                if (lessons.length > 0) {
                    lessons.forEach(function(lesson) {
                        var opt = document.createElement('option');
                        opt.value = lesson.id;
                        opt.textContent = lesson.title || 'درس ' + (lesson.order || '');
                        lessonSelect.appendChild(opt);
                    });
                } else {
                    var noOpt = document.createElement('option');
                    noOpt.value = '';
                    noOpt.textContent = <?php echo json_encode(__('instructor.no_lessons_in_course'), 15, 512) ?>;
                    noOpt.disabled = true;
                    lessonSelect.appendChild(noOpt);
                }
                lessonSelect.disabled = false;
            })
            .catch(function() {
                loadingOption.remove();
                var errOpt = document.createElement('option');
                errOpt.value = '';
                errOpt.textContent = <?php echo json_encode(__('instructor.error_occurred'), 15, 512) ?>;
                errOpt.disabled = true;
                lessonSelect.appendChild(errOpt);
                lessonSelect.disabled = false;
            });
    });

    updateGroupOptions(newCourseSelect.value);
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\instructor\assignments\create.blade.php ENDPATH**/ ?>