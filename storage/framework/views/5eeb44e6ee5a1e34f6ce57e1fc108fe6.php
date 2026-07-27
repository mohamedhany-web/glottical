<?php
    $thumbPath = $course->thumbnail ? str_replace('\\', '/', $course->thumbnail) : null;
    $thumbUrl = $thumbPath ? storage_asset($thumbPath) : null;
    $instName = $course->instructor->name ?? __('public.instructor_fallback');
    $rating = $course->rating !== null ? number_format((float) $course->rating, 1) : null;
    $dur = (int) ($course->duration_hours ?? 0);
    $durLabel = $dur > 0 ? $dur.' '.__($a.'.course_duration') : ($course->lessons_count ?? 0).' '.__('landing.lesson_single');
?>
<div class="netflix-item">
    <article class="card-course h-full rounded-2xl border border-slate-100 bg-white overflow-hidden shadow-sm hover:shadow-xl transition flex flex-col">
        <a href="<?php echo e(route('public.course.show', $course->id)); ?>" class="block flex flex-col flex-1">
            <div class="aspect-[16/10] bg-acad-gray overflow-hidden shrink-0 relative">
                <?php if($thumbUrl): ?>
                    <div class="media-thumb-skeleton absolute inset-0" aria-hidden="true"></div>
                    <img src="<?php echo e($thumbUrl); ?>" alt="" class="w-full h-full object-cover media-thumb-img" width="320" height="200" loading="lazy" decoding="async" sizes="(max-width:640px) 85vw, 320px" onload="this.classList.add('is-loaded');this.previousElementSibling?.remove();" onerror="this.style.display='none';this.previousElementSibling?.remove();">
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center text-acad-blue/30"><i class="fas fa-image text-3xl"></i></div>
                <?php endif; ?>
            </div>
            <div class="p-4 flex-1 flex flex-col">
                <div class="flex flex-wrap items-center gap-1.5">
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-acad-blueSoft text-acad-blue"><?php echo e($durLabel); ?></span>
                    <?php if($course->isMonthlyBilling()): ?>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700"><?php echo e(__('public.course_badge_monthly')); ?></span>
                    <?php endif; ?>
                    <?php if($course->isOneToOne()): ?>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-violet-100 text-violet-700"><?php echo e(__('public.course_badge_one_to_one')); ?></span>
                    <?php endif; ?>
                </div>
                <h4 class="mt-2 font-black text-acad-ink leading-snug line-clamp-2 text-sm flex-1"><?php echo e(\Illuminate\Support\Str::limit($course->title, 56)); ?></h4>
                <p class="mt-1 text-xs text-slate-500 truncate"><?php echo e($instName); ?></p>
                <div class="mt-2 flex items-center justify-between text-xs">
                    <span class="text-amber-500 font-bold"><?php if($rating): ?><i class="fas fa-star"></i> <?php echo e($rating); ?><?php else: ?> — <?php endif; ?></span>
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
            </div>
        </a>
    </article>
</div>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\partials\landing-course-card-compact.blade.php ENDPATH**/ ?>