<?php $__env->startSection('title', 'جلسات البث المباشر'); ?>
<?php $__env->startSection('header', 'جلسات البث المباشر'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    
    <div class="bg-white dark:bg-slate-800/95 rounded-xl p-5 sm:p-6 border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-slate-100 mb-1 flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 shrink-0">
                        <i class="fas fa-broadcast-tower"></i>
                    </span>
                    <span>جلسات البث المباشر</span>
                </h1>
                <p class="text-sm text-gray-500 dark:text-slate-400">الجلسات المباشرة والمجدولة المتاحة لك وفق كورساتك</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a href="<?php echo e(route('my-courses.index')); ?>" class="inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-colors shadow-sm">
                    <i class="fas fa-book-open"></i>
                    كورساتي
                </a>
                <a href="<?php echo e(route('student.live-recordings.index')); ?>" class="inline-flex items-center justify-center gap-2 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 text-gray-700 dark:text-slate-200 px-4 py-2.5 rounded-xl text-sm font-semibold hover:border-sky-300 dark:hover:border-sky-600 transition-colors">
                    <i class="fas fa-play-circle text-sky-600"></i>
                    التسجيلات
                </a>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-900 dark:text-emerald-200 px-4 py-3 text-sm font-medium"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 text-red-900 dark:text-red-200 px-4 py-3 text-sm font-medium"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    
    <div class="flex flex-wrap gap-2">
        <a href="<?php echo e(route('student.live-sessions.index')); ?>" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors <?php echo e(!request('status') ? 'bg-sky-500 text-white shadow-sm' : 'bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-300 hover:border-sky-300'); ?>">الكل</a>
        <a href="<?php echo e(route('student.live-sessions.index', ['status' => 'live'])); ?>" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request('status') === 'live' ? 'bg-red-600 text-white shadow-sm' : 'bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-300 hover:border-red-300'); ?>">مباشر</a>
        <a href="<?php echo e(route('student.live-sessions.index', ['status' => 'scheduled'])); ?>" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors <?php echo e(request('status') === 'scheduled' ? 'bg-sky-500 text-white shadow-sm' : 'bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-300 hover:border-sky-300'); ?>">مجدولة</a>
    </div>

    
    <?php if($liveSessions->count() > 0 && (!request('status') || request('status') === 'live')): ?>
    <div class="space-y-3">
        <h2 class="text-sm font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wide flex items-center gap-2">
            <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
            مباشر الآن
        </h2>
        <?php $__currentLoopData = $liveSessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $live): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white dark:bg-slate-800/95 rounded-xl border border-red-200 dark:border-red-900/50 shadow-sm p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center gap-4 ring-1 ring-red-100/80 dark:ring-red-900/30">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 text-xs font-bold">
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span>
                        مباشر
                    </span>
                    <?php if($live->course): ?>
                        <span class="text-xs text-gray-500 dark:text-slate-400 truncate max-w-full"><?php echo e($live->course->title); ?></span>
                    <?php endif; ?>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-slate-100 text-lg"><?php echo e($live->title); ?></h3>
                <p class="text-sm text-gray-600 dark:text-slate-400 mt-1">
                    <i class="fas fa-chalkboard-teacher text-sky-600 ml-1"></i><?php echo e($live->instructor?->name ?? '—'); ?>

                    <?php if($live->started_at): ?>
                        <span class="text-gray-300 dark:text-slate-600 mx-2">•</span>
                        <span class="text-gray-500">بدأ <?php echo e($live->started_at->diffForHumans()); ?></span>
                    <?php endif; ?>
                </p>
            </div>
            <form method="POST" action="<?php echo e(route('student.live-sessions.join', $live)); ?>" class="shrink-0">
                <?php echo csrf_field(); ?>
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-bold shadow-md shadow-red-500/20 transition-colors">
                    <i class="fas fa-video"></i>
                    انضم الآن
                </button>
            </form>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>

    
    <?php if(request('status') !== 'live'): ?>
    <div>
        <h2 class="text-sm font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wide mb-3">
            <?php echo e(request('status') === 'scheduled' ? 'الجلسات المجدولة' : 'الجلسات القادمة'); ?>

        </h2>

        <?php
            $listSessions = $sessions->filter(fn ($s) => $s->status !== 'live');
        ?>

        <?php if($listSessions->isEmpty()): ?>
            <div class="bg-white dark:bg-slate-800/95 rounded-xl border border-gray-200 dark:border-slate-700 p-10 text-center shadow-sm">
                <i class="fas fa-calendar-alt text-4xl text-gray-300 dark:text-slate-600 mb-4"></i>
                <p class="font-medium text-gray-700 dark:text-slate-300">لا توجد جلسات مجدولة <?php echo e(request('status') === 'scheduled' ? 'حالياً' : 'في هذه الصفحة'); ?></p>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">ستُعرض الجلسات عند جدولتها من قبل المدرب</p>
                <a href="<?php echo e(route('my-courses.index')); ?>" class="inline-flex items-center gap-2 mt-4 text-sky-600 dark:text-sky-400 hover:text-sky-700 font-semibold text-sm">
                    <i class="fas fa-book-open"></i> تصفح كورساتي
                </a>
            </div>
        <?php else: ?>
        <div class="space-y-3">
            <?php $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($session->status !== 'live'): ?>
            <a href="<?php echo e(route('student.live-sessions.show', $session)); ?>" class="block bg-white dark:bg-slate-800/95 rounded-xl border border-gray-200 dark:border-slate-700 p-5 shadow-sm hover:border-sky-300 dark:hover:border-sky-600 hover:shadow transition-all">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-sky-100 dark:bg-sky-900/40 text-sky-800 dark:text-sky-200 text-xs font-semibold">مجدولة</span>
                            <?php if($session->course): ?>
                                <span class="text-xs text-gray-500 dark:text-slate-400 truncate"><?php echo e($session->course->title); ?></span>
                            <?php endif; ?>
                        </div>
                        <h3 class="font-bold text-gray-900 dark:text-slate-100 text-lg"><?php echo e($session->title); ?></h3>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-xs text-gray-600 dark:text-slate-400">
                            <span><i class="fas fa-chalkboard-teacher text-sky-600 ml-1"></i><?php echo e($session->instructor?->name ?? '—'); ?></span>
                            <span><i class="fas fa-calendar ml-1 text-gray-400"></i><?php echo e($session->scheduled_at?->format('Y/m/d')); ?></span>
                            <span><i class="fas fa-clock ml-1 text-gray-400"></i><?php echo e($session->scheduled_at?->format('H:i')); ?></span>
                        </div>
                        <?php if($session->description): ?>
                            <p class="text-sm text-gray-500 dark:text-slate-400 mt-2 line-clamp-2"><?php echo e(Str::limit($session->description, 120)); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="shrink-0 flex items-center gap-2">
                        <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-sky-50 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 text-sm font-semibold">
                            التفاصيل
                            <i class="fas fa-chevron-left text-xs opacity-70"></i>
                        </span>
                    </div>
                </div>
            </a>
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
    </div>
    <?php elseif($liveSessions->isEmpty()): ?>
        <div class="bg-white dark:bg-slate-800/95 rounded-xl border border-gray-200 dark:border-slate-700 p-10 text-center shadow-sm">
            <i class="fas fa-broadcast-tower text-4xl text-gray-300 dark:text-slate-600 mb-4"></i>
            <p class="font-medium text-gray-700 dark:text-slate-300">لا توجد جلسات مباشرة حالياً</p>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">عند بدء المدرب للبث ستظهر الجلسة أعلاه</p>
        </div>
    <?php endif; ?>

    <?php if($sessions->hasPages()): ?>
        <div class="pt-2"><?php echo e($sessions->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\student\live-sessions\index.blade.php ENDPATH**/ ?>