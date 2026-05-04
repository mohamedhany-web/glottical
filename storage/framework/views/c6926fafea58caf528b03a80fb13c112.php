
<?php $__env->startSection('title', $liveRecording->title ?? 'مشاهدة التسجيل'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
        <a href="<?php echo e(route('student.live-recordings.index')); ?>" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 transition-colors"><i class="fas fa-arrow-right"></i></a>
        <h1 class="text-xl font-bold text-slate-800 dark:text-white truncate"><?php echo e($liveRecording->title ?? 'تسجيل الجلسة'); ?></h1>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="aspect-video bg-black flex items-center justify-center">
            <video controls class="w-full h-full" preload="metadata" crossorigin="anonymous">
                <source src="<?php echo e($url); ?>" type="video/mp4">
                <p class="text-white p-4">المتصفح لا يدعم تشغيل الفيديو. <a href="<?php echo e($url); ?>" target="_blank" class="underline">افتح الرابط في نافذة جديدة</a></p>
            </video>
        </div>
        <div class="p-4 border-t border-slate-200 dark:border-slate-700 flex flex-wrap items-center gap-4 text-sm text-slate-600 dark:text-slate-400">
            <span><i class="fas fa-video ml-1"></i> <?php echo e($liveRecording->session?->title); ?></span>
            <span><i class="fas fa-clock ml-1"></i> <?php echo e($liveRecording->duration_for_humans); ?></span>
            <span><i class="fas fa-hdd ml-1"></i> <?php echo e($liveRecording->file_size_for_humans); ?></span>
            <a href="<?php echo e($url); ?>" target="_blank" rel="noopener" class="text-emerald-600 dark:text-emerald-400 hover:underline font-medium">
                <i class="fas fa-external-link-alt ml-1"></i> فتح الرابط في تاب جديد
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\student\live-recordings\show.blade.php ENDPATH**/ ?>