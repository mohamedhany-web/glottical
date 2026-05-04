<?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="rounded-xl border border-slate-200 bg-slate-50/50 dark:bg-slate-800/40 dark:border-slate-600 overflow-hidden" style="margin-right: <?php echo e(($depth ?? 0) * 0.75); ?>rem">
        <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-600 bg-white/80 dark:bg-slate-800/80">
            <h3 class="text-base font-black text-slate-800 dark:text-slate-100"><?php echo e($section->title); ?></h3>
            <?php if($section->description): ?>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1"><?php echo e($section->description); ?></p>
            <?php endif; ?>
        </div>
        <div class="p-4 space-y-2">
            <?php $__currentLoopData = $section->materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex flex-wrap items-center gap-3 p-3 rounded-xl bg-white dark:bg-slate-800/90 border border-slate-100 dark:border-slate-600">
                    <?php if($material->file_kind === 'pptx'): ?>
                        <span class="w-10 h-10 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center"><i class="fas fa-file-powerpoint"></i></span>
                    <?php elseif($material->file_kind === 'pdf'): ?>
                        <span class="w-10 h-10 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center"><i class="fas fa-file-pdf"></i></span>
                    <?php elseif($material->file_kind === 'html'): ?>
                        <span class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center"><i class="fas fa-code"></i></span>
                    <?php else: ?>
                        <span class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center"><i class="fas fa-file"></i></span>
                    <?php endif; ?>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-800 dark:text-slate-100 truncate"><?php echo e($material->displayTitle()); ?></p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                            <?php if($material->effectiveAllowViewInPlatform()): ?> عرض داخل المنصة <?php else: ?> — <?php endif; ?>
                            <?php if($material->effectiveAllowDownload()): ?> · تحميل <?php endif; ?>
                        </p>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <?php if($material->file_kind === 'html' && $material->effectiveAllowViewInPlatform()): ?>
                            <a href="<?php echo e(route('curriculum-library.material.html', [$item, $material])); ?>" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        <?php elseif($material->file_kind === 'pptx' && $material->effectiveAllowViewInPlatform()): ?>
                            <a href="<?php echo e(route('curriculum-library.material.presentation', [$item, $material])); ?>" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-semibold hover:bg-amber-700">
                                <i class="fas fa-play"></i> عرض تفاعلي
                            </a>
                        <?php elseif($material->file_kind === 'pdf' && $material->effectiveAllowViewInPlatform()): ?>
                            <a href="<?php echo e(route('curriculum-library.material.pdf', [$item, $material])); ?>" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        <?php endif; ?>
                        <?php if($material->effectiveAllowDownload()): ?>
                            <a href="<?php echo e(route('curriculum-library.material.download', [$item, $material])); ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">
                                <i class="fas fa-download"></i> تحميل
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if($section->treeChildren->isNotEmpty()): ?>
                <div class="mt-3 space-y-3 border-t border-dashed border-slate-200 dark:border-slate-600 pt-3">
                    <?php echo $__env->make('student.curriculum-library._section-node', ['sections' => $section->treeChildren, 'item' => $item, 'depth' => ($depth ?? 0) + 1], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\student\curriculum-library\_section-node.blade.php ENDPATH**/ ?>