<?php
    $thumbPath = $course->thumbnail ? str_replace('\\', '/', $course->thumbnail) : null;
    $thumbUrl = $thumbPath ? storage_asset($thumbPath) : null;
    $instName = $course->instructor->name ?? __('public.instructor_fallback');
    $rating = $course->rating !== null ? number_format((float) $course->rating, 1) : null;
    $ratingNum = $course->rating !== null ? (float) $course->rating : 0;
    $dur = (int) ($course->duration_hours ?? 0);
    $durLabel = $dur > 0 ? $dur.' '.__($a.'.course_duration') : ($course->lessons_count ?? 0).' '.__('landing.lesson_single');
    $catSlug = optional($course->courseCategory)->name ? \Illuminate\Support\Str::slug($course->courseCategory->name) : '';
    $priceNum = $course->is_free ? 0.0 : (float) $course->effectiveCheckoutPrice();
    $showDiscount = ! $course->is_free && ! $course->isMonthlyBilling() && $course->hasPromotionalPrice();
?>
<article class="card-course reveal rounded-2xl border border-slate-100 bg-white overflow-hidden relative group"
    data-course-card
    data-id="<?php echo e($course->id); ?>"
    data-level="<?php echo e($course->level ?? 'beginner'); ?>"
    data-price="<?php echo e($priceNum); ?>"
    data-free="<?php echo e($course->is_free ? '1' : '0'); ?>"
    data-duration="<?php echo e($dur); ?>"
    data-rating="<?php echo e($ratingNum); ?>"
    data-category="<?php echo e($catSlug); ?>"
    data-lang="<?php echo e(strtolower((string) ($course->language ?? ''))); ?>">
    <button type="button" class="wishlist-btn absolute top-3 <?php echo e($isRtl ? 'left-3' : 'right-3'); ?> z-10 w-10 h-10 rounded-full bg-white/95 shadow-md border border-slate-100 flex items-center justify-center text-slate-400 hover:text-rose-500 transition"
        data-wishlist-toggle="<?php echo e($course->id); ?>"
        aria-label="<?php echo e(__($a.'.wishlist_add')); ?>">
        <i class="fas fa-heart"></i>
    </button>
    <div class="relative aspect-[16/10] bg-acad-gray overflow-hidden">
        <?php if($thumbUrl): ?>
            <img src="<?php echo e($thumbUrl); ?>" alt="<?php echo e($course->title); ?>" class="w-full h-full object-cover transition duration-500 group-hover:scale-105" loading="lazy" decoding="async">
        <?php else: ?>
            <div class="w-full h-full flex items-center justify-center text-acad-blue/30"><i class="fas fa-image text-4xl"></i></div>
        <?php endif; ?>
        <div class="absolute inset-0 bg-gradient-to-t from-acad-blue/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-4 gap-2">
            <button type="button" class="quick-view-btn px-4 py-2 rounded-xl bg-white text-acad-blue font-extrabold text-sm shadow-lg hover:scale-105 transition" data-quick-view="<?php echo e($course->id); ?>"><?php echo e(__($a.'.quick_view')); ?></button>
        </div>
    </div>
    <div class="p-5">
        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-block text-[11px] font-bold px-2 py-0.5 rounded-md bg-acad-blueSoft text-acad-blue"><?php echo e($durLabel); ?></span>
            <?php if($course->is_free): ?>
                <span class="text-[11px] font-black px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700"><?php echo e(__('landing.free')); ?></span>
            <?php elseif($showDiscount): ?>
                <?php $pctOff = (float) $course->price > 0 ? (int) round((1 - (float) $course->price_after_discount / (float) $course->price) * 100) : 0; ?>
                <span class="text-[11px] font-black px-2 py-0.5 rounded-md bg-rose-100 text-rose-700">-<?php echo e(max(0, min(99, $pctOff))); ?>%</span>
            <?php endif; ?>
            <?php if($course->isMonthlyBilling()): ?>
                <span class="text-[11px] font-black px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700"><?php echo e(__('public.course_badge_monthly')); ?></span>
            <?php endif; ?>
            <?php if($course->isOneToOne()): ?>
                <span class="text-[11px] font-black px-2 py-0.5 rounded-md bg-violet-100 text-violet-700"><?php echo e(__('public.course_badge_one_to_one')); ?></span>
            <?php endif; ?>
        </div>
        <h3 class="mt-3 font-black text-lg text-acad-ink leading-snug line-clamp-2">
            <a href="<?php echo e(route('public.course.show', $course->id)); ?>" class="hover:text-acad-cyan transition"><?php echo e(\Illuminate\Support\Str::limit($course->title, 70)); ?></a>
        </h3>
        <p class="mt-1 text-sm text-slate-500"><?php echo e($instName); ?></p>
        <div class="mt-3 flex items-center justify-between gap-2">
            <span class="text-amber-500 font-bold text-sm">
                <?php if($rating !== null): ?><i class="fas fa-star"></i> <?php echo e($rating); ?><?php else: ?><span class="text-slate-400 text-xs"><?php echo e(__('public.no_rating_yet')); ?></span><?php endif; ?>
            </span>
            <?php if (isset($component)) { $__componentOriginal9ce3c0e6a304a546d78b53c70e0ef542 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ce3c0e6a304a546d78b53c70e0ef542 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.advanced-course-card-price','data' => ['course' => $course,'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('advanced-course-card-price'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['course' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($course),'size' => 'sm']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ce3c0e6a304a546d78b53c70e0ef542)): ?>
<?php $attributes = $__attributesOriginal9ce3c0e6a304a546d78b53c70e0ef542; ?>
<?php unset($__attributesOriginal9ce3c0e6a304a546d78b53c70e0ef542); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ce3c0e6a304a546d78b53c70e0ef542)): ?>
<?php $component = $__componentOriginal9ce3c0e6a304a546d78b53c70e0ef542; ?>
<?php unset($__componentOriginal9ce3c0e6a304a546d78b53c70e0ef542); ?>
<?php endif; ?>
        </div>
        <a href="<?php echo e(route('public.course.show', $course->id)); ?>" class="mt-4 w-full inline-flex justify-center items-center gap-2 py-2.5 rounded-xl bg-acad-yellow text-acad-blue font-extrabold text-sm hover:brightness-105 transition shadow-sm"><?php echo e(__($a.'.course_enroll')); ?></a>
    </div>
</article>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\partials\landing-course-card-product.blade.php ENDPATH**/ ?>