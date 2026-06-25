<?php
    $thumbUrl = $course->thumbnail_url;
    $instName = $course->instructor->name ?? '';
    $rating = $course->rating !== null ? number_format((float) $course->rating, 1) : null;
    $dur = (int) ($course->duration_hours ?? 0);
    $durLabel = $dur > 0 ? $dur.' '.__($a.'.course_duration') : ($course->lessons_count ?? 0).' '.__('landing.lesson_single');
    $lvlKey = match ($course->level ?? 'beginner') {
        'intermediate' => $a.'.level_intermediate',
        'advanced' => $a.'.level_advanced',
        default => $a.'.level_beginner',
    };
    $lvlLabel = __($lvlKey);
?>
<div class="stream-card-wrap netflix-item shrink-0">
    <article class="stream-card group relative rounded-xl overflow-hidden bg-[#1a2d4d] border border-white/10 shadow-lg outline-none transition-all duration-300 hover:scale-[1.06] hover:z-20 hover:shadow-[0_0_0_2px_rgba(245,184,0,0.65),0_24px_48px_-12px_rgba(0,163,196,0.35)] flex flex-col">
        <a href="<?php echo e(route('public.course.show', $course->id)); ?>" class="flex flex-col h-full min-h-0 text-start">
            
            <div class="relative aspect-video w-full shrink-0 bg-[#152a4a] overflow-hidden">
                <?php if($thumbUrl): ?>
                    <div class="media-thumb-skeleton absolute inset-0" aria-hidden="true"></div>
                    <img src="<?php echo e($thumbUrl); ?>"
                         alt="<?php echo e($course->title); ?>"
                         width="400"
                         height="225"
                         class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105 group-hover:brightness-110 media-thumb-img"
                         sizes="(max-width: 640px) 85vw, 400px"
                         loading="lazy"
                         decoding="async"
                         onload="this.classList.add('is-loaded');this.previousElementSibling?.remove();"
                         onerror="this.style.display='none';this.previousElementSibling?.remove();this.nextElementSibling?.classList.remove('hidden');">
                    <div class="hidden absolute inset-0 flex items-center justify-center text-white/25 bg-[#152a4a]"><i class="fas fa-play-circle text-5xl"></i></div>
                <?php else: ?>
                    <div class="absolute inset-0 flex items-center justify-center text-white/25"><i class="fas fa-play-circle text-5xl"></i></div>
                <?php endif; ?>
                <div class="absolute inset-0 bg-gradient-to-t from-[#0d1528]/80 via-transparent to-transparent pointer-events-none"></div>
                <div class="absolute inset-0 z-10 flex items-center justify-center opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-300 bg-[#0d1528]/50 backdrop-blur-[2px] pointer-events-none group-hover:pointer-events-auto">
                    <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-acad-yellow text-acad-blue font-black text-sm shadow-lg scale-95 group-hover:scale-100 transition pointer-events-none"><i class="fas fa-play text-xs"></i><?php echo e(__($a.'.stream_play')); ?></span>
                </div>
            </div>
            
            <div class="relative z-20 p-3 pt-3 border-t border-white/10 bg-[#121f38]">
                <div class="flex flex-wrap items-center gap-1.5 mb-1.5">
                    <span class="text-[10px] font-black px-2 py-0.5 rounded-md bg-white/15 text-white backdrop-blur-sm border border-white/10"><?php echo e($durLabel); ?></span>
                    <span class="text-[10px] font-black px-2 py-0.5 rounded-md bg-[#00A3C4]/25 text-[#7ee8ff] border border-[#00A3C4]/30"><?php echo e($lvlLabel); ?></span>
                </div>
                <h3 class="font-black text-white text-sm sm:text-base leading-snug line-clamp-2"><?php echo e(\Illuminate\Support\Str::limit($course->title, 72)); ?></h3>
                <?php if($instName !== ''): ?>
                    <p class="text-[11px] text-white/65 font-semibold mt-0.5 truncate"><?php echo e($instName); ?></p>
                <?php endif; ?>
                <p class="text-amber-400 text-xs font-bold mt-1">
                    <?php if($rating !== null): ?><i class="fas fa-star"></i> <?php echo e($rating); ?><?php else: ?><span class="text-white/40"><?php echo e(__('public.no_rating_yet')); ?></span><?php endif; ?>
                </p>
            </div>
        </a>
    </article>
</div>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\partials\landing-stream-card.blade.php ENDPATH**/ ?>