<?php $__env->startSection('title', $label . ' — ' . config('app.name', 'Muallimx')); ?>
<?php $__env->startSection('header', $label); ?>

<?php
    $cfg = $featureConfig ?? [];
    $icon = $cfg['icon'] ?? 'fa-wand-magic-sparkles';
    $qTypes = \App\Services\FullAiSuiteContextService::QUESTION_TYPES;
    $preview = session('full_ai_preview');
    $jsonPreview = $preview && is_array($preview) ? json_encode($preview['context'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '';
    $promptText = $preview && is_array($preview) ? (string) ($preview['prompt'] ?? '') : '';
    $gameHtmlUrl = $preview && is_array($preview) ? (string) ($preview['game_html_url'] ?? '') : '';
    $gameStoragePath = $preview && is_array($preview) ? (string) ($preview['game_storage_path'] ?? '') : '';
    $muallimxAiText = $preview && is_array($preview)
        ? (string) ($preview['muallimx_ai_text'] ?? $preview['gemini_text'] ?? '')
        : '';
    $muallimxAiError = $preview && is_array($preview)
        ? (string) ($preview['muallimx_ai_error'] ?? $preview['gemini_error'] ?? '')
        : '';
    $requiresCourseSelection = $requiresCourseSelection ?? false;
    $pageHint = $pageHint ?? __('student.full_ai_suite.layer2_hint');
?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-start gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-sky-100 dark:bg-sky-900/40 text-sky-600 dark:text-sky-400">
                <i class="fas <?php echo e($icon); ?> text-xl"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-slate-100 mb-1"><?php echo e($label); ?></h1>
                <p class="text-sm text-gray-500 dark:text-slate-300 leading-relaxed"><?php echo e($description); ?></p>
                <p class="mt-3 text-xs sm:text-sm text-gray-600 dark:text-slate-400 leading-relaxed rounded-lg bg-gray-50 dark:bg-slate-900/60 border border-gray-100 dark:border-slate-600 px-3 py-2">
                    <i class="fas fa-circle-info text-sky-500 ml-1.5"></i>
                    <?php echo e(__('student.full_ai_suite.layer1_subtitle')); ?>

                </p>
            </div>
        </div>
    </div>

    <?php if(in_array(($feature ?? ''), ['ai_tools', 'full_ai_suite'], true) && Route::has('student.ai-usages.index')): ?>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end">
            <a href="<?php echo e(route('student.ai-usages.index')); ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-sky-200 dark:border-sky-700 bg-sky-50 dark:bg-sky-950/40 text-sky-800 dark:text-sky-100 text-sm font-semibold shadow-sm hover:bg-sky-100 dark:hover:bg-sky-900/55 transition-colors">
                <i class="fas fa-folder-open text-sky-600 dark:text-sky-400"></i>
                <?php echo e(__('student.ai_usages.open_saved_page')); ?>

            </a>
        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="rounded-xl bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-800 dark:text-red-200 space-y-1">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <p class="flex items-start gap-2"><i class="fas fa-circle-exclamation mt-0.5 shrink-0"></i><span><?php echo e($err); ?></span></p>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <?php if($requiresCourseSelection && $courses->isEmpty()): ?>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-dashed border-gray-300 dark:border-slate-600 p-8 text-center shadow-sm">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-slate-400">
                <i class="fas fa-book-open text-xl"></i>
            </div>
            <p class="text-sm text-gray-600 dark:text-slate-300 max-w-xl mx-auto"><?php echo e(__('student.full_ai_suite.course_empty')); ?></p>
            <a href="<?php echo e(route('public.courses')); ?>" class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold shadow-sm transition-colors">
                <i class="fas fa-magnifying-glass text-xs"></i>
                <?php echo e(__('student.full_ai_suite.course_empty_link')); ?>

            </a>
        </div>
    <?php else: ?>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 sm:p-6 border border-gray-200 dark:border-slate-700 shadow-sm space-y-5">
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-slate-100"><?php echo e(__('student.full_ai_suite.form_card_title')); ?></h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1"><?php echo e($pageHint); ?></p>
            </div>

            <form action="<?php echo e(route('student.features.full-ai-suite.preview')); ?>" method="POST" class="space-y-5">
                <?php echo csrf_field(); ?>
                <div class="space-y-5" x-data="{ questionType: <?php echo json_encode(old('question_type') ?: array_key_first($qTypes), 15, 512) ?>, len: <?php echo e(strlen(old('question', ''))); ?> }">
                    <?php if($requiresCourseSelection): ?>
                        <div>
                            <label for="advanced_course_id" class="block text-sm font-semibold text-gray-800 dark:text-slate-200 mb-2">
                                <?php echo e(__('student.full_ai_suite.course_label')); ?>

                            </label>
                            <select name="advanced_course_id" id="advanced_course_id" required
                                class="w-full rounded-xl border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-slate-100 px-4 py-3 text-sm shadow-sm focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500">
                                <option value=""><?php echo e(__('student.full_ai_suite.course_placeholder')); ?></option>
                                <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($c->id); ?>" <?php if(old('advanced_course_id') == $c->id): echo 'selected'; endif; ?>>
                                        <?php echo e($c->title); ?><?php if($c->category): ?> — <?php echo e($c->category); ?> <?php endif; ?>
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div>
                        <label for="question_type" class="block text-sm font-semibold text-gray-800 dark:text-slate-200 mb-2">
                            <?php echo e(__('student.full_ai_suite.question_type_label')); ?>

                        </label>
                        <select name="question_type" id="question_type" required x-model="questionType"
                            class="w-full rounded-xl border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-slate-100 px-4 py-3 text-sm shadow-sm focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500">
                            <?php $__currentLoopData = $qTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $transKey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php if(old('question_type') === $key): echo 'selected'; endif; ?>><?php echo e(__($transKey)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div x-show="questionType === 'educational_games'" x-cloak
                        class="rounded-xl border border-amber-200 dark:border-amber-800/60 bg-amber-50/90 dark:bg-amber-950/25 px-4 py-3 text-sm text-amber-950 dark:text-amber-100 space-y-2">
                        <p class="font-bold flex items-center gap-2">
                            <i class="fas fa-gamepad text-amber-600 dark:text-amber-400"></i>
                            <?php echo e(__('student.full_ai_suite.educational_games_intro_title')); ?>

                        </p>
                        <p class="leading-relaxed text-amber-900/95 dark:text-amber-100/90">
                            <?php echo e(__('student.full_ai_suite.educational_games_intro_body')); ?>

                        </p>
                    </div>

                    <div>
                        <label for="question" class="block text-sm font-semibold text-gray-800 dark:text-slate-200 mb-2">
                            <?php echo e(__('student.full_ai_suite.question_label')); ?>

                        </label>
                        <textarea name="question" id="question" rows="6" required minlength="10" maxlength="4000"
                            placeholder="<?php echo e(__('student.full_ai_suite.question_placeholder')); ?>"
                            x-on:input="len = $el.value.length"
                            class="w-full rounded-xl border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-slate-100 px-4 py-3 text-sm leading-relaxed shadow-sm focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500 resize-y"><?php echo e(old('question')); ?></textarea>
                        <div class="flex justify-between text-xs text-gray-400 dark:text-slate-500 mt-1.5">
                            <span>10 — 4000</span>
                            <span><span x-text="len">0</span> / 4000</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-3 pt-1">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold shadow-sm transition-colors w-full sm:w-auto">
                        <i class="fas fa-wand-magic-sparkles text-sm"></i>
                        <?php echo e(__('student.full_ai_suite.preview_button')); ?>

                    </button>
                    <p class="text-xs text-gray-500 dark:text-slate-400 flex-1"><?php echo e(__('student.full_ai_suite.preview_hint_short')); ?></p>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <?php if($preview && is_array($preview)): ?>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-gray-200 dark:border-slate-700 shadow-sm space-y-3">
            <h2 class="text-lg font-bold text-gray-900 dark:text-slate-100"><?php echo e(__('student.full_ai_suite.result_title')); ?></h2>
            <p class="text-sm text-gray-500 dark:text-slate-400"><?php echo e(__('student.full_ai_suite.result_lead')); ?></p>

            <div class="rounded-xl border border-gray-100 dark:border-slate-600 bg-gray-50 dark:bg-slate-900/50 p-4">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-slate-200 mb-2"><?php echo e(__('student.full_ai_suite.custom_description_title')); ?></h3>
                <p class="text-sm text-gray-700 dark:text-slate-300 leading-relaxed whitespace-pre-wrap"><?php echo e(data_get($preview, 'context.option_description', '—')); ?></p>
            </div>

            <?php if($muallimxAiText !== ''): ?>
                <div class="rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50/90 dark:bg-emerald-950/25 p-4">
                    <h3 class="text-sm font-semibold text-emerald-900 dark:text-emerald-200 mb-2 flex items-center gap-2">
                        <i class="fas fa-robot text-emerald-600 dark:text-emerald-400"></i>
                        <?php echo e(__('student.full_ai_suite.muallimx_ai_reply_title')); ?>

                    </h3>
                    <div class="text-sm text-gray-900 dark:text-slate-100 whitespace-pre-wrap leading-relaxed"><?php echo e($muallimxAiText); ?></div>
                </div>
            <?php elseif($muallimxAiError !== ''): ?>
                <div class="rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950/25 p-4 text-sm text-red-800 dark:text-red-200">
                    <p class="font-semibold mb-1 flex items-center gap-2">
                        <i class="fas fa-triangle-exclamation"></i>
                        <?php echo e(__('student.full_ai_suite.muallimx_ai_reply_title')); ?>

                    </p>
                    <p><?php echo e($muallimxAiError); ?></p>
                </div>
            <?php endif; ?>

            <?php if($gameHtmlUrl !== ''): ?>
                <div class="rounded-xl border border-gray-200 dark:border-slate-600 overflow-hidden">
                    <div class="flex flex-wrap items-center justify-between gap-2 px-4 py-3 bg-gray-50 dark:bg-slate-900/60 border-b border-gray-200 dark:border-slate-600">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-slate-100"><?php echo e(__('student.full_ai_suite.educational_game_preview_title')); ?></h3>
                        <a href="<?php echo e($gameHtmlUrl); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold">
                            <i class="fas fa-up-right-from-square text-[10px]"></i>
                            <?php echo e(__('student.full_ai_suite.open_game_file')); ?>

                        </a>
                    </div>
                    <iframe src="<?php echo e($gameHtmlUrl); ?>" title="<?php echo e(__('student.full_ai_suite.educational_game_preview_title')); ?>" class="w-full min-h-[480px] bg-white" loading="lazy"></iframe>
                    <?php if($gameStoragePath !== '' && Route::has('student.ai-usages.saved-games.store')): ?>
                        <div class="px-4 py-4 bg-slate-50 dark:bg-slate-900/70 border-t border-gray-200 dark:border-slate-600 space-y-3">
                            <p class="text-xs text-gray-600 dark:text-slate-400"><?php echo e(__('student.ai_usages.save_hint')); ?></p>
                            <form action="<?php echo e(route('student.ai-usages.saved-games.store')); ?>" method="POST" class="flex flex-col sm:flex-row sm:flex-wrap sm:items-end gap-3">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="storage_path" value="<?php echo e($gameStoragePath); ?>">
                                <div class="flex-1 min-w-[200px]">
                                    <label for="saved_game_title" class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1"><?php echo e(__('student.ai_usages.title_optional')); ?></label>
                                    <input type="text" name="title" id="saved_game_title" maxlength="200"
                                        class="w-full rounded-xl border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-gray-900 dark:text-slate-100 px-3 py-2 text-sm"
                                        placeholder="<?php echo e(__('student.ai_usages.title_optional')); ?>">
                                </div>
                                <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold shadow-sm">
                                    <i class="fas fa-bookmark text-sm"></i>
                                    <?php echo e(__('student.ai_usages.save_to_list')); ?>

                                </button>
                            </form>
                            <?php if(Route::has('student.ai-usages.index')): ?>
                                <a href="<?php echo e(route('student.ai-usages.index')); ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-sky-600 dark:text-sky-400 hover:underline">
                                    <i class="fas fa-list text-xs"></i>
                                    <?php echo e(__('student.ai_usages.nav')); ?>

                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    <?php endif; ?>

    <div class="flex justify-center sm:justify-start">
        <a href="<?php echo e(route('student.my-subscription')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-700 dark:text-slate-200 text-sm font-semibold hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors shadow-sm">
            <i class="fas fa-receipt text-sky-600 dark:text-sky-400"></i>
            <?php echo e(__('student.full_ai_suite.back_subscription')); ?>

        </a>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>[x-cloak]{display:none!important}</style>
<style>details summary::-webkit-details-marker{display:none}</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\student\features\full-ai-suite.blade.php ENDPATH**/ ?>