

<?php $__env->startSection('title', 'طالب: '.$student->name); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">عرض إشراف — <?php echo e($supervisor->name); ?></p>
            <h1 class="text-2xl font-bold text-gray-900"><?php echo e($student->name); ?></h1>
            <p class="text-sm text-gray-600"><?php echo e($student->email); ?> <?php if($student->phone): ?><span class="text-gray-400">·</span> <?php echo e($student->phone); ?><?php endif; ?></p>
            <p class="text-xs text-gray-500 mt-1">آخر ظهور: <?php echo e($student->last_login_at ? $student->last_login_at->format('Y-m-d H:i') : 'غير متوفر'); ?></p>
        </div>
        <a href="<?php echo e(route('admin.academic-supervision.supervisors.show', $supervisor)); ?>" class="text-sm font-semibold text-teal-700 hover:underline">← طلاب هذا المشرف</a>
    </div>

    <?php if(session('error')): ?>
        <div class="rounded-xl border border-red-200 bg-red-50 text-red-800 text-sm px-4 py-3"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-bold text-gray-900 mb-3">الاشتراك والميزات</h3>
            <?php if($subscription): ?>
                <p class="text-sm text-gray-800"><span class="text-gray-500">الباقة:</span> <?php echo e($subscription->plan_name ?: '—'); ?></p>
                <p class="text-sm text-gray-800 mt-1"><span class="text-gray-500">الحالة:</span> <?php echo e($subscription->status); ?></p>
                <p class="text-sm text-gray-800 mt-1"><span class="text-gray-500">حتى:</span> <?php echo e($subscription->end_date?->format('Y-m-d') ?? '—'); ?></p>
            <?php else: ?>
                <p class="text-sm text-amber-700">لا يوجد اشتراك نشط حالياً.</p>
            <?php endif; ?>
            <p class="text-sm mt-3">
                <span class="text-gray-500">Muallimx Classroom:</span>
                <?php if($hasClassroom): ?>
                    <span class="font-semibold text-emerald-700">مفعّل</span>
                <?php else: ?>
                    <span class="font-semibold text-gray-600">غير مفعّل في الباقة</span>
                <?php endif; ?>
            </p>
            <?php if($hasClassroom): ?>
                <p class="text-xs text-gray-500 mt-2">ميتينج هذا الشهر: <?php echo e($usedMeetingsThisMonth); ?> / <?php echo e((int) $limits['classroom_meetings_per_month']); ?> — الحد الأقصى للحضور: <?php echo e((int) $limits['classroom_max_participants']); ?></p>
            <?php endif; ?>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-bold text-gray-900 mb-3">الكورسات</h3>
            <p class="text-2xl font-black text-teal-900 tabular-nums"><?php echo e($student->course_enrollments_count); ?></p>
            <p class="text-xs text-gray-500">عدد التسجيلات في الكورسات</p>
        </div>
    </div>

    <?php if($liveMeeting): ?>
        <div class="rounded-2xl border-2 border-emerald-300/60 bg-gradient-to-br from-emerald-50 to-white p-5 shadow-sm flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold text-emerald-800 uppercase tracking-wide mb-1">ميتينج نشط الآن</p>
                <p class="font-bold text-gray-900"><?php echo e($liveMeeting->title ?: $liveMeeting->code); ?></p>
                <p class="text-sm text-gray-600 mt-1">الحضور الحالي: <?php echo e($liveMeeting->participants_count); ?> — الرمز: <?php echo e($liveMeeting->code); ?></p>
            </div>
            <a href="<?php echo e(route('admin.academic-supervision.supervisors.meetings.observe', [$supervisor, $liveMeeting])); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-500 shadow-md">
                <i class="fas fa-video"></i> دخول مراقبة الغرفة
            </a>
        </div>
    <?php endif; ?>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">تسجيلات الكورسات</h3>
        </div>
        <div class="overflow-x-auto max-h-80 overflow-y-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 font-semibold sticky top-0">
                    <tr>
                        <th class="text-right px-4 py-2">الكورس</th>
                        <th class="text-right px-4 py-2">الحالة</th>
                        <th class="text-right px-4 py-2">التقدم</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $en): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-2"><?php echo e($en->course?->title ?? '—'); ?></td>
                            <td class="px-4 py-2"><?php echo e($en->status ?? '—'); ?></td>
                            <td class="px-4 py-2"><?php echo e($en->progress !== null ? number_format((float) $en->progress, 1).'%' : '—'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500">لا توجد تسجيلات.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">اجتماعات Classroom</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 font-semibold">
                    <tr>
                        <th class="text-right px-4 py-2">العنوان</th>
                        <th class="text-right px-4 py-2">الحالة</th>
                        <th class="text-right px-4 py-2">الحضور</th>
                        <th class="text-right px-4 py-2 w-36"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $meetings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-2 font-medium"><?php echo e($m->title ?: $m->code); ?></td>
                            <td class="px-4 py-2">
                                <?php if($m->isLive()): ?>
                                    <span class="text-emerald-700 font-bold text-xs">لايف</span>
                                <?php elseif($m->ended_at): ?>
                                    <span class="text-gray-500 text-xs">انتهى</span>
                                <?php else: ?>
                                    <span class="text-amber-700 text-xs">مجدول</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2"><?php echo e($m->participants_count); ?></td>
                            <td class="px-4 py-2">
                                <?php if($m->isLive()): ?>
                                    <a href="<?php echo e(route('admin.academic-supervision.supervisors.meetings.observe', [$supervisor, $m])); ?>" class="text-cyan-600 font-semibold text-xs hover:underline">مراقبة</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">لا توجد اجتماعات.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\academic-supervision\student-show.blade.php ENDPATH**/ ?>