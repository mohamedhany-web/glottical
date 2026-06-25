<?php
    $thumbUrl = $course->thumbnail_url;
    $isOneToOne = $course->isOneToOne();
?>
<a href="<?php echo e(route('public.course.show', $course->id)); ?>"
   class="group flex flex-col sm:flex-row gap-4 p-4 rounded-2xl border border-white/10 bg-white/[0.03] hover:border-acad-yellow/40 hover:bg-white/[0.06] transition-all duration-300">
    <div class="w-full sm:w-24 h-36 sm:h-24 shrink-0 rounded-xl overflow-hidden bg-acad-navyMid relative">
        <?php if($thumbUrl): ?>
            <img src="<?php echo e($thumbUrl); ?>" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
        <?php else: ?>
            <div class="absolute inset-0 flex items-center justify-center text-white/30"><i class="fas fa-play-circle text-3xl"></i></div>
        <?php endif; ?>
        <?php if($isOneToOne): ?>
            <span class="absolute top-2 <?php echo e(($isRtl ?? true) ? 'left' : 'right'); ?>-2 text-[10px] font-black px-2 py-0.5 rounded-md bg-violet-400 text-acad-blue"><?php echo e(__('public.course_badge_one_to_one')); ?></span>
        <?php endif; ?>
    </div>
    <div class="flex-1 min-w-0 flex flex-col justify-center text-start">
        <h4 class="font-black text-white group-hover:text-acad-yellow transition-colors line-clamp-2 leading-snug text-sm sm:text-base mb-2"><?php echo e($course->title); ?></h4>
        <div class="flex flex-wrap items-center gap-3 text-xs text-white/50">
            <?php
                $pay = $course->effectiveCheckoutPrice();
                $isMonthly = $course->isMonthlyBilling();
            ?>
            <span class="font-bold text-acad-yellow tabular-nums">
                <?php echo e(number_format($pay, 0)); ?> <?php echo e(__('public.currency_egp')); ?><?php if($isMonthly): ?>/<?php echo e(__('public.per_month')); ?><?php endif; ?>
            </span>
            <?php if(($course->lessons_count ?? 0) > 0): ?>
                <span class="flex items-center gap-1"><i class="fas fa-play-circle"></i> <?php echo e($course->lessons_count); ?> <?php echo e(__('public.lesson_single')); ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="hidden sm:flex shrink-0 self-center">
        <span class="w-9 h-9 rounded-xl bg-white/5 group-hover:bg-acad-yellow/20 flex items-center justify-center transition-colors">
            <i class="fas fa-arrow-<?php echo e(($isRtl ?? true) ? 'left' : 'right'); ?> text-[10px] text-white/40 group-hover:text-acad-yellow"></i>
        </span>
    </div>
</a>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\partials\instructor-course-card.blade.php ENDPATH**/ ?>