<?php $__env->startSection('title', 'تفاصيل الاجتماع'); ?>
<?php $__env->startSection('header', 'تفاصيل الاجتماع'); ?>

<?php
    $rp = ($useInstructorRoutes ?? false) ? 'instructor.' : 'student.';
?>
<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm font-medium"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 text-sm font-medium"><?php echo e(session('error')); ?></div>
    <?php endif; ?>
    <?php if(session('info')): ?>
        <div class="rounded-xl bg-sky-50 border border-sky-200 text-sky-800 px-4 py-3 text-sm font-medium"><?php echo e(session('info')); ?></div>
    <?php endif; ?>

    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-6 space-y-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white"><?php echo e($meeting->title); ?></h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">الكود: <span class="font-mono font-bold"><?php echo e($meeting->code); ?></span></p>
            </div>
            <div class="flex items-center gap-2">
                <?php if(!$meeting->consultation_request_id && !($useInstructorRoutes ?? false)): ?>
                <a href="<?php echo e(route('student.classroom.edit', $meeting)); ?>" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold">تعديل</a>
                <?php endif; ?>
                <?php if(!$meeting->started_at && !$meeting->ended_at): ?>
                    <form action="<?php echo e(route($rp.'classroom.start-meeting', $meeting)); ?>" method="POST"><?php echo csrf_field(); ?><button class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold">بدء الآن</button></form>
                <?php elseif($meeting->isLive()): ?>
                    <a href="<?php echo e(route($rp.'classroom.room', $meeting)); ?>" class="px-4 py-2 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-semibold">دخول الغرفة</a>
                    <form method="POST" action="<?php echo e(route($rp.'classroom.end', $meeting)); ?>" onsubmit="return confirm('إنهاء الاجتماع؟');"><?php echo csrf_field(); ?><button class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-800 text-white text-sm font-semibold">إنهاء</button></form>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="rounded-xl border border-slate-200 dark:border-slate-600 p-3">
                <p class="text-xs text-slate-500 dark:text-slate-400">الحالة</p>
                <p class="text-sm font-semibold text-slate-800 dark:text-white">
                    <?php echo e($meeting->isLive() ? 'مباشر' : (!$meeting->started_at ? 'مجدول' : 'منتهي')); ?>

                </p>
            </div>
            <div class="rounded-xl border border-slate-200 dark:border-slate-600 p-3">
                <p class="text-xs text-slate-500 dark:text-slate-400">الموعد المحدد</p>
                <p class="text-sm font-semibold text-slate-800 dark:text-white"><?php echo e(optional($meeting->scheduled_for)->format('Y-m-d H:i') ?? 'غير محدد'); ?></p>
            </div>
            <div class="rounded-xl border border-slate-200 dark:border-slate-600 p-3">
                <p class="text-xs text-slate-500 dark:text-slate-400">مدة الاجتماع</p>
                <p class="text-sm font-semibold text-slate-800 dark:text-white"><?php echo e((int) ($meeting->planned_duration_minutes ?? $limits['classroom_max_duration_minutes'])); ?> دقيقة</p>
            </div>
            <div class="rounded-xl border border-slate-200 dark:border-slate-600 p-3">
                <p class="text-xs text-slate-500 dark:text-slate-400">الحد الأقصى للمشاركين</p>
                <p class="text-sm font-semibold text-slate-800 dark:text-white"><?php echo e((int) ($meeting->max_participants ?? 25)); ?></p>
            </div>
            <div class="rounded-xl border border-slate-200 dark:border-slate-600 p-3">
                <p class="text-xs text-slate-500 dark:text-slate-400">أعلى ذروة مشاركين</p>
                <p class="text-sm font-semibold text-slate-800 dark:text-white"><?php echo e((int) ($meeting->participants_peak ?? 0)); ?></p>
            </div>
            <div class="rounded-xl border border-slate-200 dark:border-slate-600 p-3">
                <p class="text-xs text-slate-500 dark:text-slate-400">إجمالي المشاركين المسجلين</p>
                <p class="text-sm font-semibold text-slate-800 dark:text-white"><?php echo e((int) ($meeting->participants_count ?? 0)); ?></p>
            </div>
        </div>

        <div class="rounded-xl border border-dashed border-slate-300 dark:border-slate-600 p-3 flex flex-wrap items-center justify-between gap-3">
            <div class="text-xs text-slate-600 dark:text-slate-300">رابط الانضمام للطلاب والضيوف:</div>
            <div class="flex items-center gap-2">
                <input type="text" readonly value="<?php echo e($joinUrl); ?>" class="w-[340px] max-w-[60vw] px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs">
                <button type="button" onclick="navigator.clipboard.writeText('<?php echo e($joinUrl); ?>')" class="px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 text-xs font-semibold">نسخ</button>
            </div>
        </div>

        <?php if($meeting->ended_at): ?>
            <?php
                $hasVideo = (bool) $meeting->recording_path;
                $hasAudio = (bool) $meeting->recording_audio_path;
                $hasAnyMedia = $hasVideo || $hasAudio;
            ?>
            <div class="rounded-xl border border-sky-200 dark:border-sky-800 bg-sky-50/70 dark:bg-sky-900/10 p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <p class="text-sm font-bold text-slate-800 dark:text-slate-100">التسجيل والتقرير الصوتي</p>
                    <?php if($hasAnyMedia): ?>
                        <?php if($hasVideo): ?>
                            <p class="text-xs text-slate-600 dark:text-slate-300 mt-1">
                                تم حفظ تسجيل المحاضرة.
                                <?php if($meeting->recording_uploaded_at): ?>
                                    وقت الرفع: <?php echo e($meeting->recording_uploaded_at->format('Y-m-d H:i')); ?>

                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                        <?php if($hasAudio): ?>
                            <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-1">تم حفظ التقرير الصوتي (الفويس) لهذا الاجتماع.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-xs text-slate-600 dark:text-slate-300 mt-1">لا يوجد تسجيل أو تقرير صوتي مرفوع لهذا الاجتماع.</p>
                    <?php endif; ?>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <?php if($meeting->recording_download_url): ?>
                        <a href="<?php echo e($meeting->recording_download_url); ?>" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold">
                            <i class="fas fa-download"></i>
                            تحميل تسجيل المحاضرة
                        </a>
                    <?php endif; ?>
                    <?php if($meeting->recording_audio_download_url): ?>
                        <a href="<?php echo e($meeting->recording_audio_download_url); ?>" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
                            <i class="fas fa-music"></i>
                            تحميل التقرير الصوتي
                        </a>
                        <audio controls preload="none" class="max-w-full md:max-w-sm h-9 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800">
                            <source src="<?php echo e($meeting->recording_audio_download_url); ?>" type="<?php echo e($meeting->recording_audio_mime_type ?: 'audio/webm'); ?>">
                        </audio>
                    <?php elseif($meeting->recording_audio_path): ?>
                        <span class="text-xs text-amber-700 dark:text-amber-300">التقرير الصوتي موجود لكن رابط التحميل غير متاح حالياً.</span>
                    <?php endif; ?>
                    <?php if(!$meeting->recording_download_url && !$meeting->recording_audio_download_url && $hasAnyMedia): ?>
                        <span class="text-xs text-amber-700 dark:text-amber-300">الملف موجود ولكن رابط التحميل غير متاح حالياً.</span>
                    <?php endif; ?>
                </div>
            </div>

            <?php
                $canGenerateReport = (bool) ($meeting->recording_audio_download_url || $meeting->recording_download_url);
            ?>
            <div class="rounded-xl border border-violet-200 dark:border-violet-900 bg-violet-50/60 dark:bg-violet-950/20 p-4 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div>
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100">تقرير المحاضرة النصي (بالذكاء الاصطناعي)</p>
                        <p class="text-xs text-slate-600 dark:text-slate-300 mt-1">
                            يُرسل التسجيل أو التقرير الصوتي إلى خدمة المعالجة، ثم يُحدَّث هذا القسم تلقائياً عند اكتمال إنشاء التقرير.
                        </p>
                    </div>
                    <?php if($canGenerateReport && !($activeAiReport ?? null)): ?>
                        <form method="POST" action="<?php echo e(route($rp.'classroom.ai-report', $meeting)); ?>" class="shrink-0" onsubmit="return confirm('سيتم إرسال رابط التسجيل/التقرير الصوتي لإنشاء تقرير نصي عن المحاضرة. هل تريد المتابعة؟');">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold">
                                <i class="fas fa-robot"></i>
                                إنشاء التقرير النصي
                            </button>
                        </form>
                    <?php elseif(! $canGenerateReport): ?>
                        <p class="text-xs text-amber-800 dark:text-amber-200 shrink-0 max-w-xs">ارفع التسجيل أو التقرير الصوتي أولاً حتى يظهر زر إنشاء التقرير.</p>
                    <?php endif; ?>
                </div>
                <?php if($activeAiReport ?? null): ?>
                    <div class="rounded-lg border border-violet-200/80 dark:border-violet-800 bg-white/80 dark:bg-slate-900/40 px-3 py-2 text-xs text-violet-900 dark:text-violet-100">
                        <span class="font-semibold">حالة الطلب:</span>
                        <?php if($activeAiReport->status === 'pending'): ?> في انتظار المعالجة
                        <?php else: ?> جاري المعالجة…
                        <?php endif; ?>
                        <span class="text-slate-500 dark:text-slate-400">(يمكنك تحديث الصفحة لاحقاً)</span>
                    </div>
                <?php endif; ?>
                <?php if(($latestCompletedAiReport ?? null) && $latestCompletedAiReport->summary): ?>
                    <div>
                        <p class="text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">آخر تقرير مكتمل:</p>
                        <div class="rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 py-3 text-sm text-slate-800 dark:text-slate-100 whitespace-pre-wrap leading-relaxed"><?php echo e($latestCompletedAiReport->summary); ?></div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="flex items-center justify-between">
            <?php if($meeting->consultation_request_id && ($useInstructorRoutes ?? false)): ?>
                <a href="<?php echo e(route('instructor.consultations.show', $meeting->consultation_request_id)); ?>" class="text-sm text-sky-600 hover:underline">العودة لتفاصيل الاستشارة</a>
            <?php elseif($useInstructorRoutes ?? false): ?>
                <a href="<?php echo e(route('instructor.consultations.index')); ?>" class="text-sm text-sky-600 hover:underline">العودة لطلبات الاستشارة</a>
            <?php else: ?>
                <a href="<?php echo e(route('student.classroom.index')); ?>" class="text-sm text-sky-600 hover:underline">العودة لقائمة الاجتماعات</a>
            <?php endif; ?>
            <?php if(!($useInstructorRoutes ?? false) && !$meeting->consultation_request_id): ?>
            <form action="<?php echo e(route('student.classroom.destroy', $meeting)); ?>" method="POST" onsubmit="return confirm('حذف الاجتماع نهائياً؟');">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button class="text-sm text-rose-600 hover:underline">حذف الاجتماع</button>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\student\classroom\show.blade.php ENDPATH**/ ?>