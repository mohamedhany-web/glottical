<?php
    $isRtl = app()->getLocale() === 'ar';
    $certTitle = $certificate->title ?? $certificate->course_name ?? __('student.completion_certificate');
?>

<?php $__env->startSection('title', $certTitle); ?>
<?php $__env->startSection('header', $certTitle); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 w-full min-w-0 max-w-5xl mx-auto">
    <div class="bg-white dark:bg-slate-800/95 rounded-xl p-5 border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0 flex-1">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-slate-100 mb-2 break-words"><?php echo e($certTitle); ?></h1>
                <p class="text-sm text-gray-500 dark:text-slate-400">
                    <?php echo e(__('student.certificate_number_label')); ?>:
                    <span class="font-mono font-semibold text-gray-800 dark:text-slate-200"><?php echo e($certificate->certificate_number ?? '—'); ?></span>
                </p>
            </div>
            <a href="<?php echo e(route('student.certificates.index')); ?>" class="inline-flex items-center justify-center gap-2 shrink-0 text-sm font-semibold text-sky-600 dark:text-sky-400 hover:text-sky-800 dark:hover:text-sky-300 border border-sky-200 dark:border-sky-800 bg-sky-50 dark:bg-sky-900/30 hover:bg-sky-100 dark:hover:bg-sky-900/50 px-4 py-2.5 rounded-xl transition-colors w-full sm:w-auto">
                <i class="fas fa-arrow-<?php echo e($isRtl ? 'right' : 'left'); ?>"></i>
                <?php echo e(__('student.my_certificates_title')); ?>

            </a>
        </div>
    </div>

    <?php if(!empty($certificate->pdf_path)): ?>
        <div class="flex flex-col sm:flex-row flex-wrap gap-2 sm:gap-3">
            <a href="<?php echo e(route('student.certificates.file', $certificate)); ?>" target="_blank" rel="noopener"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white px-4 py-3 text-sm font-semibold transition-colors w-full sm:w-auto">
                <i class="fas fa-external-link-alt"></i>
                <?php echo e(__('student.certificate_open_pdf')); ?>

            </a>
            <a href="<?php echo e(route('student.certificates.file', $certificate)); ?>" download
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-3 text-sm font-semibold transition-colors w-full sm:w-auto">
                <i class="fas fa-download"></i>
                <?php echo e(__('student.certificate_download')); ?>

            </a>
        </div>

        <div class="rounded-xl border border-gray-200 dark:border-slate-600 bg-slate-100 dark:bg-slate-900/50 overflow-hidden shadow-sm -mx-1 sm:mx-0">
            <iframe title="<?php echo e(__('student.certificate_iframe_title')); ?>"
                    src="<?php echo e(route('student.certificates.file', $certificate)); ?>"
                    class="w-full border-0 block min-h-[280px] h-[50vh] sm:h-[58vh] md:h-[65vh] lg:h-[70vh] max-h-[85vh]"></iframe>
        </div>
    <?php else: ?>
        <div class="rounded-xl border-2 border-dashed border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 p-6 sm:p-8 text-center">
            <i class="fas fa-file-pdf text-amber-500 text-4xl mb-3"></i>
            <p class="text-amber-900 dark:text-amber-200 font-semibold"><?php echo e(__('student.certificate_pdf_missing')); ?></p>
            <p class="text-sm text-amber-800/90 dark:text-amber-300/90 mt-2"><?php echo e(__('student.certificate_pdf_contact')); ?></p>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\student\certificates\show.blade.php ENDPATH**/ ?>