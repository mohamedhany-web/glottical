

<?php $__env->startSection('title', 'لوحة المشرف الأكاديمي'); ?>
<?php $__env->startSection('header', 'لوحة المشرف الأكاديمي'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .as-card {
        border-radius: 18px;
        border: 2px solid rgba(13, 148, 136, 0.18);
        background: linear-gradient(145deg, rgba(255,255,255,0.98) 0%, rgba(240, 253, 250, 0.92) 100%);
        box-shadow: 0 4px 18px rgba(13, 148, 136, 0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .as-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 28px rgba(13, 148, 136, 0.12);
    }
    .as-hero {
        border-radius: 20px;
        border: 2px solid rgba(13, 148, 136, 0.22);
        background: linear-gradient(125deg, rgba(255,255,255,0.99) 0%, rgba(204, 251, 241, 0.55) 45%, rgba(240, 253, 250, 0.95) 100%);
        box-shadow: 0 6px 24px rgba(15, 118, 110, 0.1);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="as-hero p-6 sm:p-8 relative overflow-hidden">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-teal-700 mb-1">الإشراف الأكاديمي</p>
                <h2 class="text-2xl sm:text-3xl font-black text-gray-900 mb-2">مرحباً، <?php echo e($user->name); ?></h2>
                <p class="text-gray-600 text-sm sm:text-base max-w-xl">
                    متابعة الطلاب المعيّنين لك: الظهور على المنصة، التسجيل في الكورسات، وجلسات Classroom النشطة.
                </p>
                <?php if($user->employeeJob): ?>
                    <p class="text-sm text-gray-500 mt-3 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5"><i class="fas fa-briefcase text-teal-600"></i><?php echo e($user->employeeJob->name); ?></span>
                        <?php if($user->employee_code): ?>
                            <span class="text-gray-400">·</span>
                            <span><?php echo e($user->employee_code); ?></span>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 shrink-0">
                <?php if($user->employeeCan('academic_supervision_desk')): ?>
                    <a href="<?php echo e(route('employee.academic-supervision.index')); ?>"
                       class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-teal-600 text-white font-bold text-sm shadow-lg hover:bg-teal-500 transition-colors">
                        <i class="fas fa-user-graduate"></i>
                        فتح الإشراف الأكاديمي
                    </a>
                <?php endif; ?>
                <?php if($user->employeeCan('tasks')): ?>
                    <a href="<?php echo e(route('employee.tasks.index')); ?>"
                       class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl border-2 border-teal-200 bg-white text-teal-800 font-bold text-sm hover:bg-teal-50 transition-colors">
                        <i class="fas fa-tasks"></i>
                        مهامي
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="as-card p-5">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-gray-600">طلاب تحت إشرافك</p>
                    <p class="text-3xl font-black text-teal-900 tabular-nums mt-1"><?php echo e(number_format($stats['supervised_students'])); ?></p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-teal-100 flex items-center justify-center text-teal-700">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="as-card p-5 border-emerald-200/60">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-gray-600">جلسات لايف الآن</p>
                    <p class="text-3xl font-black text-emerald-800 tabular-nums mt-1"><?php echo e(number_format($stats['live_meetings'])); ?></p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-700">
                    <i class="fas fa-video text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="as-card p-5 border-sky-200/60">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-gray-600">مسجّلون في كورس</p>
                    <p class="text-3xl font-black text-sky-900 tabular-nums mt-1"><?php echo e(number_format($stats['students_with_courses'])); ?></p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-sky-100 flex items-center justify-center text-sky-700">
                    <i class="fas fa-book-open text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="as-card p-5 border-amber-200/60">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-gray-600">بلا ظهور +١٤ يوم</p>
                    <p class="text-3xl font-black text-amber-900 tabular-nums mt-1"><?php echo e(number_format($stats['inactive_students'])); ?></p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-700">
                    <i class="fas fa-user-clock text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <?php if($liveMeetings->isNotEmpty()): ?>
        <div class="rounded-2xl border-2 border-emerald-300/50 bg-gradient-to-br from-emerald-50/90 to-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <h3 class="text-lg font-black text-gray-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    جلسات نشطة الآن
                </h3>
            </div>
            <ul class="space-y-2">
                <?php $__currentLoopData = $liveMeetings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-emerald-200/60 bg-white/80 px-4 py-3">
                        <div>
                            <p class="font-bold text-gray-900"><?php echo e($m->user?->name ?? 'طالب'); ?> — <?php echo e($m->title ?: $m->code); ?></p>
                            <p class="text-xs text-gray-500 mt-0.5">الحضور: <?php echo e($m->participants_count); ?> · <?php echo e($m->code); ?></p>
                        </div>
                        <?php if($user->employeeCan('academic_supervision_desk')): ?>
                            <a href="<?php echo e(route('employee.academic-supervision.meeting.observe', $m)); ?>"
                               class="inline-flex items-center gap-2 text-sm font-bold text-emerald-700 hover:text-emerald-900">
                                دخول المراقبة <i class="fas fa-arrow-left text-xs"></i>
                            </a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-base font-black text-gray-900">طلابك</h3>
                    <p class="text-xs text-gray-500">لمحة سريعة — آخر ظهور وحالة الجلسة</p>
                </div>
                <?php if($user->employeeCan('academic_supervision_desk')): ?>
                    <a href="<?php echo e(route('employee.academic-supervision.index')); ?>" class="text-sm font-bold text-teal-700 hover:underline">عرض الكل</a>
                <?php endif; ?>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 font-semibold">
                        <tr>
                            <th class="text-right px-4 py-3">الطالب</th>
                            <th class="text-right px-4 py-3">آخر ظهور</th>
                            <th class="text-right px-4 py-3">الجلسة</th>
                            <th class="text-right px-4 py-3 w-24"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $studentsPreview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php $live = $liveByStudentId->get($st->id); ?>
                            <tr class="hover:bg-teal-50/30">
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-gray-900"><?php echo e($st->name); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo e($st->email); ?></p>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                    <?php echo e($st->last_login_at ? $st->last_login_at->diffForHumans() : '—'); ?>

                                </td>
                                <td class="px-4 py-3">
                                    <?php if($live): ?>
                                        <span class="text-xs font-bold text-emerald-700">لايف (<?php echo e($live->participants_count); ?>)</span>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if($user->employeeCan('academic_supervision_desk')): ?>
                                        <a href="<?php echo e(route('employee.academic-supervision.show', $st)); ?>" class="text-teal-700 font-bold text-xs hover:underline">تفاصيل</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="px-4 py-12 text-center text-gray-500">
                                    لم يُعيَّن لك طلاب بعد. تواصل مع الإدارة لربط الطلاب بحسابك.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-4">
            <?php if($user->employeeCan('tasks')): ?>
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-black text-gray-900">مهامك</h3>
                        <a href="<?php echo e(route('employee.tasks.index')); ?>" class="text-xs font-bold text-teal-700 hover:underline">الكل</a>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-center mb-4">
                        <div class="rounded-lg bg-gray-50 py-2">
                            <p class="text-lg font-black text-gray-900"><?php echo e($stats['total_tasks']); ?></p>
                            <p class="text-[10px] text-gray-500 font-semibold">إجمالي</p>
                        </div>
                        <div class="rounded-lg bg-amber-50 py-2">
                            <p class="text-lg font-black text-amber-800"><?php echo e($stats['pending_tasks']); ?></p>
                            <p class="text-[10px] text-amber-700 font-semibold">معلّقة</p>
                        </div>
                        <div class="rounded-lg bg-red-50 py-2">
                            <p class="text-lg font-black text-red-800"><?php echo e($stats['overdue_tasks']); ?></p>
                            <p class="text-[10px] text-red-700 font-semibold">متأخرة</p>
                        </div>
                    </div>
                    <ul class="space-y-2 max-h-64 overflow-y-auto">
                        <?php $__empty_1 = true; $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <li>
                                <a href="<?php echo e(route('employee.tasks.show', $task)); ?>" class="block rounded-lg border border-gray-100 px-3 py-2 hover:border-teal-200 hover:bg-teal-50/40 transition-colors">
                                    <p class="text-xs font-bold text-gray-900 line-clamp-1"><?php echo e($task->title); ?></p>
                                    <p class="text-[10px] text-gray-500 mt-0.5"><?php echo e($task->status); ?></p>
                                </a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <li class="text-xs text-gray-500 text-center py-4">لا مهام حالياً</li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="rounded-2xl border border-teal-200 bg-teal-50/50 p-5">
                <h3 class="text-sm font-black text-teal-900 mb-2">اختصارات</h3>
                <ul class="space-y-2 text-sm">
                    <?php if($user->employeeCan('calendar')): ?>
                        <li><a href="<?php echo e(route('employee.calendar')); ?>" class="text-teal-800 font-semibold hover:underline">التقويم</a></li>
                    <?php endif; ?>
                    <?php if($user->employeeCan('leaves')): ?>
                        <li><a href="<?php echo e(route('employee.leaves.index')); ?>" class="text-teal-800 font-semibold hover:underline">إجازاتي</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo e(route('employee.profile')); ?>" class="text-teal-800 font-semibold hover:underline">الملف الشخصي</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\employee\dashboard-academic-supervisor.blade.php ENDPATH**/ ?>