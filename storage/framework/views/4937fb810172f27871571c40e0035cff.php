
<?php
    $guideTitle = $title ?? 'كيف تسير العملية؟';
    $guideBody = $body ?? '';
    $guideSteps = $steps ?? [];
?>
<?php if($guideBody !== '' || count($guideSteps) > 0): ?>
<aside class="rounded-2xl border border-accent/25 bg-gradient-to-l from-accent-soft/70 via-surface to-surface px-4 py-3.5 shadow-soft" role="note" aria-label="<?php echo e($guideTitle); ?>">
    <div class="flex gap-3">
        <span class="mt-0.5 inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-accent text-white shadow-soft" aria-hidden="true">
            <i class="fas fa-route text-sm"></i>
        </span>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold text-ink"><?php echo e($guideTitle); ?></p>
            <?php if($guideBody !== ''): ?>
                <p class="mt-1 text-sm leading-relaxed text-ink-soft"><?php echo e($guideBody); ?></p>
            <?php endif; ?>
            <?php if(count($guideSteps) > 0): ?>
                <ol class="mt-2.5 space-y-1.5">
                    <?php $__currentLoopData = $guideSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex gap-2 text-sm leading-snug text-ink-soft">
                            <span class="mt-0.5 inline-flex size-5 shrink-0 items-center justify-center rounded-full bg-accent-soft text-[11px] font-bold text-accent"><?php echo e($i + 1); ?></span>
                            <span><?php echo e($step); ?></span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ol>
            <?php endif; ?>
        </div>
    </div>
</aside>
<?php endif; ?>
<?php /**PATH /Users/cityphone/Documents/glottical/resources/views/admin/partials/workflow-guide.blade.php ENDPATH**/ ?>