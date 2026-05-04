<?php $__env->startSection('title', __('student.my_certificates_title')); ?>
<?php $__env->startSection('header', __('student.my_certificates_title')); ?>

<?php $isRtl = app()->getLocale() === 'ar'; ?>

<?php $__env->startPush('styles'); ?>
<style>
    .cert-card {
        transition: all 0.25s ease;
        background: #fff;
        border: 1px solid #e5e7eb;
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .cert-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(14, 165, 233, 0.12);
        border-color: #bae6fd;
    }
    .stats-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .stats-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .empty-state {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
    }
    .dark .cert-card {
        background: #1e293b !important;
        border-color: #334155 !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .dark .cert-card:hover {
        border-color: rgba(14, 165, 233, 0.35) !important;
        box-shadow: 0 8px 20px rgba(0,0,0,0.25);
    }
    .dark .stats-card {
        background: #1e293b !important;
        border-color: #334155 !important;
    }
    .dark .empty-state {
        background: rgba(15, 23, 42, 0.6) !important;
        border-color: #475569 !important;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 w-full min-w-0">
    <div class="bg-white dark:bg-slate-800/95 rounded-xl p-5 border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-slate-100 mb-1"><?php echo e(__('student.my_certificates_title')); ?></h1>
                <p class="text-sm text-gray-500 dark:text-slate-400"><?php echo e(__('student.certificates_subtitle')); ?></p>
            </div>
            <a href="<?php echo e(route('my-courses.index')); ?>" class="inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-5 py-3 rounded-xl text-sm font-bold transition-colors shadow-md shrink-0 w-full sm:w-auto">
                <i class="fas fa-book-open"></i>
                <?php echo e(__('student.view_my_courses')); ?>

            </a>
        </div>
    </div>

    <?php if(isset($stats)): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
            <div class="stats-card p-4">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide"><?php echo e(__('student.total_certificates')); ?></p>
                        <p class="text-2xl sm:text-3xl font-bold text-sky-600 leading-none mt-1"><?php echo e($stats['total'] ?? 0); ?></p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-sky-100 dark:bg-sky-900/40 flex items-center justify-center text-sky-600 flex-shrink-0">
                        <i class="fas fa-certificate"></i>
                    </div>
                </div>
            </div>
            <div class="stats-card p-4">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide"><?php echo e(__('student.issued_label')); ?></p>
                        <p class="text-2xl sm:text-3xl font-bold text-emerald-600 leading-none mt-1"><?php echo e($stats['issued'] ?? 0); ?></p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600 flex-shrink-0">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(isset($certificates) && $certificates->count() > 0): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <?php $__currentLoopData = $certificates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $certificate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('student.certificates.show', $certificate)); ?>" class="cert-card block">
                    <div class="h-28 bg-sky-100 dark:bg-sky-900/30 flex items-center justify-center border-b border-gray-100 dark:border-slate-600">
                        <i class="fas fa-certificate text-4xl text-sky-600 dark:text-sky-400"></i>
                    </div>
                    <div class="p-4">
                        <h3 class="text-base font-bold text-gray-900 dark:text-slate-100 mb-2 line-clamp-2 leading-snug">
                            <?php echo e($certificate->title ?? $certificate->course_name ?? __('student.completion_certificate')); ?>

                        </h3>
                        <?php if($certificate->course): ?>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mb-3 line-clamp-2"><?php echo e($certificate->course->title); ?></p>
                        <?php endif; ?>
                        <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500 dark:text-slate-400 mb-3">
                            <span>
                                <i class="fas fa-calendar text-sky-500 <?php echo e($isRtl ? 'ml-1' : 'mr-1'); ?>"></i>
                                <?php echo e(($certificate->issued_at ? $certificate->issued_at->format('Y-m-d') : ($certificate->issue_date ? $certificate->issue_date->format('Y-m-d') : '—'))); ?>

                            </span>
                            <?php if($certificate->certificate_number): ?>
                                <span class="font-mono bg-gray-100 dark:bg-slate-700 px-2 py-0.5 rounded-md border border-gray-200 dark:border-slate-600">#<?php echo e(substr($certificate->certificate_number, -6)); ?></span>
                            <?php endif; ?>
                        </div>
                        <span class="mt-1 inline-flex items-center justify-center gap-2 w-full py-2.5 rounded-lg bg-sky-500 hover:bg-sky-600 text-white text-sm font-semibold transition-colors">
                            <?php echo e(__('student.view_certificate')); ?>

                            <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?> text-xs"></i>
                        </span>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($certificates->hasPages()): ?>
            <div class="flex justify-center pt-2"><?php echo e($certificates->links()); ?></div>
        <?php endif; ?>
    <?php else: ?>
        <div class="empty-state rounded-xl p-10 sm:p-12 text-center">
            <div class="w-16 h-16 bg-sky-100 dark:bg-sky-900/40 rounded-2xl flex items-center justify-center mx-auto mb-4 text-sky-600">
                <i class="fas fa-certificate text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-slate-100 mb-2"><?php echo e(__('student.no_certificates')); ?></h3>
            <p class="text-sm text-gray-500 dark:text-slate-400 mb-6 max-w-sm mx-auto"><?php echo e(__('student.no_certificates_desc')); ?></p>
            <a href="<?php echo e(route('my-courses.index')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white text-sm font-semibold rounded-lg transition-colors">
                <i class="fas fa-book-open"></i>
                <?php echo e(__('student.view_my_courses')); ?>

            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\student\certificates\index.blade.php ENDPATH**/ ?>