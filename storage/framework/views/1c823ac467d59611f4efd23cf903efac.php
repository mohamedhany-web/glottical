<?php $__env->startSection('title', 'جلسات البث المباشر'); ?>

<?php $__env->startSection('content'); ?>
<section class="st-join-hero" aria-label="جلسات البث المباشر">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">Live · LiveKit</p>
        <h1 class="st-join-hero__title">جلسات البث المباشر</h1>
        <p class="st-join-hero__meta">بث المنصة الجماعي والكورسات — الحصص الخاصة 1:1 من صفحة الحصص الخاصة وليس من هنا</p>
    </div>
    <div class="st-join-hero__actions">
        <?php if(Route::has('student.learn.index')): ?>
        <a href="<?php echo e(route('student.learn.index')); ?>" class="st-pill st-pill--outline st-pill--lg">
            <i class="fas fa-book-open"></i> تعلّمي
        </a>
        <?php endif; ?>
        <?php if(Route::has('student.live-recordings.index')): ?>
        <a href="<?php echo e(route('student.live-recordings.index')); ?>" class="st-pill st-pill--solid st-pill--lg">
            <i class="fas fa-play-circle"></i> التسجيلات
        </a>
        <?php endif; ?>
    </div>
</section>

<?php if(session('success')): ?>
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-900 px-4 py-3 text-sm font-medium mb-4"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="rounded-xl border border-red-200 bg-red-50 text-red-900 px-4 py-3 text-sm font-medium mb-4"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<div class="flex flex-wrap gap-2 mb-5">
    <a href="<?php echo e(route('student.live-sessions.index')); ?>" class="st-pill <?php echo e(!request('status') ? 'st-pill--solid' : 'st-pill--outline'); ?>">الكل</a>
    <a href="<?php echo e(route('student.live-sessions.index', ['status' => 'live'])); ?>" class="st-pill <?php echo e(request('status') === 'live' ? 'st-pill--solid' : 'st-pill--outline'); ?>">مباشر</a>
    <a href="<?php echo e(route('student.live-sessions.index', ['status' => 'scheduled'])); ?>" class="st-pill <?php echo e(request('status') === 'scheduled' ? 'st-pill--solid' : 'st-pill--outline'); ?>">مجدولة</a>
</div>

<?php if($liveSessions->count() > 0 && (!request('status') || request('status') === 'live')): ?>
<div class="space-y-3 mb-6">
    <h2 class="text-sm font-bold text-[var(--st-muted)] uppercase tracking-wide flex items-center gap-2 m-0">
        <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
        مباشر الآن
    </h2>
    <?php $__currentLoopData = $liveSessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $live): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="st-class-card" style="min-height:auto;padding:18px 20px;flex-direction:row;align-items:center;gap:16px;flex-wrap:wrap;">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold">
                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span>
                    مباشر
                </span>
                <?php if($live->course): ?>
                    <span class="text-xs text-[var(--st-muted)] truncate"><?php echo e($live->course->title); ?></span>
                <?php endif; ?>
            </div>
            <h3 class="font-black text-[var(--st-ink-strong)] text-lg m-0"><?php echo e($live->title); ?></h3>
            <p class="text-sm text-[var(--st-ink)] mt-1 mb-0">
                <i class="fas fa-chalkboard-teacher text-[var(--st-blue)] ml-1"></i><?php echo e($live->instructor?->name ?? '—'); ?>

                <?php if($live->started_at): ?>
                    <span class="text-[var(--st-muted)] mx-2">•</span>
                    <span class="text-[var(--st-muted)]">بدأ <?php echo e($live->started_at->diffForHumans()); ?></span>
                <?php endif; ?>
            </p>
        </div>
        <form method="POST" action="<?php echo e(route('student.live-sessions.join', $live)); ?>" class="shrink-0 m-0">
            <?php echo csrf_field(); ?>
            <button type="submit" class="st-pill st-pill--solid st-pill--lg">
                <i class="fas fa-video"></i> انضم الآن
            </button>
        </form>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>

<?php if(request('status') !== 'live'): ?>
<div>
    <h2 class="text-sm font-bold text-[var(--st-muted)] uppercase tracking-wide mb-3">
        <?php echo e(request('status') === 'scheduled' ? 'الجلسات المجدولة' : 'الجلسات القادمة'); ?>

    </h2>

    <?php
        $listSessions = $sessions->filter(fn ($s) => $s->status !== 'live');
    ?>

    <?php if($listSessions->isEmpty()): ?>
        <div class="st-class-card items-center justify-center text-center p-10" style="min-height:180px;">
            <i class="fas fa-calendar-alt text-4xl text-[var(--st-muted)] mb-4"></i>
            <p class="font-bold text-[var(--st-ink-strong)] m-0">لا توجد جلسات مجدولة <?php echo e(request('status') === 'scheduled' ? 'حالياً' : 'في هذه الصفحة'); ?></p>
            <p class="text-sm text-[var(--st-muted)] mt-1">ستُعرض الجلسات عند جدولتها من قبل المدرب</p>
        </div>
    <?php else: ?>
    <div class="space-y-3">
        <?php $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($session->status !== 'live'): ?>
        <a href="<?php echo e(route('student.live-sessions.show', $session)); ?>" class="st-class-card no-underline text-inherit" style="min-height:auto;padding:18px 20px;display:block;">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-sky-100 text-sky-800 text-xs font-semibold">مجدولة</span>
                        <?php if($session->course): ?>
                            <span class="text-xs text-[var(--st-muted)] truncate"><?php echo e($session->course->title); ?></span>
                        <?php endif; ?>
                    </div>
                    <h3 class="font-black text-[var(--st-ink-strong)] text-lg m-0"><?php echo e($session->title); ?></h3>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-xs text-[var(--st-ink)]">
                        <span><i class="fas fa-chalkboard-teacher text-[var(--st-blue)] ml-1"></i><?php echo e($session->instructor?->name ?? '—'); ?></span>
                        <span><i class="fas fa-calendar ml-1 text-[var(--st-muted)]"></i><?php echo e($session->scheduled_at?->format('Y/m/d')); ?></span>
                        <span><i class="fas fa-clock ml-1 text-[var(--st-muted)]"></i><?php echo e($session->scheduled_at?->format('H:i')); ?></span>
                    </div>
                </div>
                <span class="st-pill st-pill--outline shrink-0">التفاصيل <i class="fas fa-chevron-left text-xs opacity-70"></i></span>
            </div>
        </a>
        <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>
</div>
<?php elseif($liveSessions->isEmpty()): ?>
    <div class="st-class-card items-center justify-center text-center p-10" style="min-height:180px;">
        <i class="fas fa-broadcast-tower text-4xl text-[var(--st-muted)] mb-4"></i>
        <p class="font-bold text-[var(--st-ink-strong)] m-0">لا توجد جلسات مباشرة حالياً</p>
        <p class="text-sm text-[var(--st-muted)] mt-1">عند بدء المدرب للبث ستظهر الجلسة أعلاه</p>
    </div>
<?php endif; ?>

<?php if($sessions->hasPages()): ?>
    <div class="pt-4"><?php echo e($sessions->links()); ?></div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student-timeline', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/glottical/resources/views/student/live-sessions/index.blade.php ENDPATH**/ ?>