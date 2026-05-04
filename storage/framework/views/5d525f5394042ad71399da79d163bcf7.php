

<?php $__env->startSection('title', 'تفاصيل الاستشارة'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <a href="<?php echo e(route('instructor.consultations.index')); ?>" class="text-sm text-sky-600 hover:underline">← القائمة</a>

    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-6 space-y-4">
        <div class="flex justify-between items-start gap-2">
            <h1 class="text-xl font-black text-slate-900 dark:text-white">استشارة — <?php echo e($consultation->student->name ?? 'طالب'); ?></h1>
            <span class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700 text-xs font-semibold"><?php echo e($consultation->statusLabel()); ?></span>
        </div>
        <dl class="text-sm space-y-2">
            <div class="flex justify-between border-b border-slate-100 dark:border-slate-700 pb-2"><dt class="text-slate-500">المبلغ</dt><dd class="font-bold"><?php echo e(number_format($consultation->price_amount, 2)); ?> ج.م</dd></div>
            <div class="flex justify-between border-b border-slate-100 dark:border-slate-700 pb-2"><dt class="text-slate-500">المدة</dt><dd><?php echo e((int) $consultation->duration_minutes); ?> دقيقة</dd></div>
            <?php if($consultation->student_message): ?>
            <div><dt class="text-slate-500 mb-1">طلب الطالب</dt><dd class="text-slate-800 dark:text-slate-200 whitespace-pre-line"><?php echo e($consultation->student_message); ?></dd></div>
            <?php endif; ?>
        </dl>

        <?php if($consultation->status === \App\Models\ConsultationRequest::STATUS_SCHEDULED && $consultation->classroomMeeting): ?>
            <?php $m = $consultation->classroomMeeting; $joinUrl = url('classroom/join/'.$m->code); ?>
            <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 p-4 space-y-3">
                <p class="font-bold text-emerald-900 dark:text-emerald-100">الموعد: <?php echo e($consultation->scheduled_at?->format('Y-m-d H:i')); ?></p>
                <p class="text-xs break-all text-emerald-800 dark:text-emerald-200">رابط الضيوف: <?php echo e($joinUrl); ?></p>
                <div class="flex flex-wrap gap-2">
                    <a href="<?php echo e(route('instructor.classroom.show', $m)); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 text-white text-sm font-bold">إعدادات الغرفة</a>
                    <?php if(!$m->ended_at): ?>
                    <a href="<?php echo e(route('instructor.classroom.room', $m)); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-bold">دخول الغرفة</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\instructor\consultations\show.blade.php ENDPATH**/ ?>