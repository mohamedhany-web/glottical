

<?php $__env->startSection('title', __('student.ai_usages.page_title') . ' — ' . config('app.name', 'Muallimx')); ?>
<?php $__env->startSection('header', __('student.ai_usages.page_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-gray-200 dark:border-slate-700 shadow-sm">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-slate-100 mb-1"><?php echo e(__('student.ai_usages.page_title')); ?></h1>
        <p class="text-sm text-gray-500 dark:text-slate-300 leading-relaxed"><?php echo e(__('student.ai_usages.page_lead')); ?></p>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/30 px-4 py-3 text-sm text-emerald-800 dark:text-emerald-200">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if($games->isEmpty()): ?>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-dashed border-gray-300 dark:border-slate-600 p-10 text-center shadow-sm">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-sky-100 dark:bg-sky-900/40 text-sky-600 dark:text-sky-400">
                <i class="fas fa-wand-magic-sparkles text-xl"></i>
            </div>
            <p class="text-sm text-gray-600 dark:text-slate-300 max-w-lg mx-auto"><?php echo e(__('student.ai_usages.empty')); ?></p>
            <?php if(Route::has('student.features.show')): ?>
                <a href="<?php echo e(route('student.features.show', ['feature' => 'ai_tools'])); ?>" class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold shadow-sm transition-colors">
                    <i class="fas fa-arrow-left text-xs rtl:rotate-180"></i>
                    <?php echo e(__('student.ai_usages.go_build')); ?>

                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="space-y-3">
            <?php $__currentLoopData = $games; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $game): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white dark:bg-slate-800 rounded-xl p-4 sm:p-5 border border-gray-200 dark:border-slate-700 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-gray-900 dark:text-slate-100 truncate">
                            <?php echo e($game->title ?: __('student.ai_usages.untitled_game')); ?>

                        </p>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-1 font-mono truncate" dir="ltr"><?php echo e($game->storage_path); ?></p>
                        <p class="text-xs text-gray-400 dark:text-slate-500 mt-1"><?php echo e($game->updated_at->translatedFormat('Y-m-d H:i')); ?></p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <a href="<?php echo e($game->publicRelativeUrl()); ?>" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold">
                            <i class="fas fa-up-right-from-square text-xs"></i>
                            <?php echo e(__('student.ai_usages.open_game')); ?>

                        </a>
                        <form action="<?php echo e(route('student.ai-usages.saved-games.destroy', ['game' => $game->id])); ?>" method="post" onsubmit="return confirm(<?php echo json_encode(__('student.ai_usages.confirm_delete'), 15, 512) ?>);">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-red-200 dark:border-red-900/50 text-red-700 dark:text-red-300 text-sm font-semibold hover:bg-red-50 dark:hover:bg-red-950/30">
                                <i class="fas fa-trash text-xs"></i>
                                <?php echo e(__('student.ai_usages.remove_from_list')); ?>

                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="mt-4">
            <?php echo e($games->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\student\ai-usages\index.blade.php ENDPATH**/ ?>