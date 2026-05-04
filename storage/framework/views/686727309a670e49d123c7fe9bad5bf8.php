<?php $__env->startSection('title', __('instructor.question_banks')); ?>
<?php $__env->startSection('header', __('instructor.question_banks')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="rounded-2xl p-6 text-white shadow-lg border border-white/10 bg-gradient-to-l from-indigo-600 via-blue-600 to-cyan-500">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-white/15 border border-white/20 flex items-center justify-center shrink-0">
                        <i class="fas fa-database text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-xl sm:text-2xl font-black leading-tight truncate"><?php echo e(__('instructor.question_banks')); ?></h1>
                        <p class="text-sm text-white/90 mt-0.5"><?php echo e(__('instructor.question_banks_manage_desc')); ?></p>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a href="<?php echo e(route('instructor.courses.index')); ?>"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/15 hover:bg-white/20 border border-white/20 text-white font-semibold transition-colors">
                    <i class="fas fa-book"></i>
                    <span><?php echo e(__('instructor.courses')); ?></span>
                </a>
                <a href="<?php echo e(route('instructor.question-banks.create')); ?>"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-indigo-700 hover:bg-white/90 border border-white/20 font-extrabold transition-colors">
                    <i class="fas fa-plus"></i>
                    <span><?php echo e(__('instructor.create_question_bank')); ?></span>
                </a>
            </div>
        </div>
    </div>

    <?php if($questionBanks->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php $__currentLoopData = $questionBanks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm hover:border-sky-300 hover:shadow-md transition-all p-5">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex-1"><?php echo e($bank->title); ?></h3>
                        <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold <?php echo e($bank->is_active ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400' : 'bg-amber-100 text-amber-700'); ?>">
                            <?php echo e($bank->is_active ? __('instructor.active_status') : __('instructor.inactive_status')); ?>

                        </span>
                    </div>
                    <?php if($bank->description): ?>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mb-4 line-clamp-2"><?php echo e($bank->description); ?></p>
                    <?php endif; ?>
                    <div class="flex items-center gap-4 text-sm text-slate-500 dark:text-slate-400 mb-4">
                        <span class="font-semibold text-slate-700 dark:text-slate-300"><?php echo e($bank->questions_count); ?></span> <?php echo e(__('instructor.question_single')); ?>

                        <?php if($bank->difficulty): ?>
                            <span>• <?php echo e($bank->difficulty === 'easy' ? __('instructor.difficulty_easy') : ($bank->difficulty === 'medium' ? __('instructor.intermediate') : __('instructor.difficulty_hard'))); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="<?php echo e(route('instructor.question-banks.show', $bank)); ?>" class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 bg-sky-500 dark:bg-sky-600 hover:bg-sky-600 text-white rounded-lg text-sm font-semibold transition-colors">
                            <i class="fas fa-eye"></i> <?php echo e(__('common.view')); ?>

                        </a>
                        <a href="<?php echo e(route('instructor.question-banks.edit', $bank)); ?>" class="p-2 rounded-lg bg-slate-100 dark:bg-slate-700/50 hover:bg-slate-200 text-slate-600 dark:text-slate-400 transition-colors" title="<?php echo e(__('common.edit')); ?>">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="flex justify-center">
            <div class="rounded-xl p-3 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm"><?php echo e($questionBanks->links()); ?></div>
        </div>
    <?php else: ?>
        <div class="rounded-xl border border-dashed border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 py-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-sky-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-database text-2xl text-sky-500"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2"><?php echo e(__('instructor.no_question_banks')); ?></h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 max-w-sm mx-auto"><?php echo e(__('instructor.no_question_banks_description')); ?></p>
            <a href="<?php echo e(route('instructor.question-banks.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-500 dark:bg-sky-600 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i> <?php echo e(__('instructor.create_question_bank')); ?>

            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\instructor\question-banks\index.blade.php ENDPATH**/ ?>