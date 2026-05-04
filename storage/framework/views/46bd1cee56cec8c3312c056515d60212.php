<?php $__env->startSection('title', 'واجباتي'); ?>
<?php $__env->startSection('header', 'واجباتي'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">واجباتي</h1>
            <p class="text-sm text-gray-500">الواجبات المنشورة في كورساتك المسجّل بها</p>
        </div>
        <a href="<?php echo e(route('my-courses.index')); ?>" class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors">
            <i class="fas fa-book-open"></i>
            كورساتي
        </a>
    </div>

    <?php if($assignments->isEmpty()): ?>
        <div class="bg-white rounded-xl border border-gray-200 p-10 text-center text-gray-600">
            <i class="fas fa-tasks text-4xl text-gray-300 mb-4"></i>
            <p class="font-medium">لا توجد واجبات متاحة حالياً.</p>
        </div>
    <?php else: ?>
        <div class="space-y-3">
            <?php $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $sub = $assignment->my_submission ?? null;
                    $courseTitle = $assignment->course->title ?? 'كورس';
                ?>
                <a href="<?php echo e(route('student.assignments.show', $assignment)); ?>" class="block bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:border-sky-300 hover:shadow transition-all">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <h2 class="font-bold text-gray-900 text-lg"><?php echo e($assignment->title); ?></h2>
                            <p class="text-sm text-gray-500 mt-1"><?php echo e($courseTitle); ?></p>
                            <?php if($assignment->due_date): ?>
                                <p class="text-xs text-gray-600 mt-2">
                                    <i class="fas fa-clock ml-1"></i>
                                    التسليم: <?php echo e($assignment->due_date->timezone(config('app.timezone'))->format('Y-m-d H:i')); ?>

                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="shrink-0">
                            <?php if(!$sub): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">لم يُسلَّم</span>
                            <?php elseif($sub->status === 'submitted'): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-sky-100 text-sky-800">قيد التصحيح</span>
                            <?php elseif($sub->status === 'graded'): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">مُقيَّم<?php echo e($sub->score !== null ? ' — '.$sub->score.'/'.$assignment->max_score : ''); ?></span>
                            <?php elseif($sub->status === 'returned'): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-violet-100 text-violet-800">مُعاد للتعديل</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\student\assignments\index.blade.php ENDPATH**/ ?>