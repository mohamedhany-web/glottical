

<?php $__env->startSection('title', 'الإشراف الأكاديمي'); ?>
<?php $__env->startSection('header', 'الإشراف الأكاديمي'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <p class="text-sm text-gray-600">متابعة الطلاب المعيّنين لك: آخر ظهور، الاشتراك، الميتينج النشط، والكورسات.</p>

    <?php if(session('error')): ?>
        <div class="rounded-xl border border-red-200 bg-red-50 text-red-800 text-sm px-4 py-3"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-900">طلابي (<?php echo e($students->count()); ?>)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 font-semibold">
                    <tr>
                        <th class="text-right px-4 py-3">الطالب</th>
                        <th class="text-right px-4 py-3">آخر ظهور</th>
                        <th class="text-right px-4 py-3">ميتينج الآن</th>
                        <th class="text-right px-4 py-3 w-28"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $live = $liveMeetings->get($row->id);
                        ?>
                        <tr class="hover:bg-gray-50/80">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-900"><?php echo e($row->name); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($row->email); ?></p>
                            </td>
                            <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
                                <?php echo e($row->last_login_at ? $row->last_login_at->diffForHumans() : '—'); ?>

                            </td>
                            <td class="px-4 py-3">
                                <?php if($live): ?>
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-2 py-1">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        لايف — <?php echo e($live->participants_count); ?> في الغرفة
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400">لا يوجد</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-left">
                                <a href="<?php echo e(route('employee.academic-supervision.show', $row)); ?>" class="text-teal-700 font-semibold hover:underline">التفاصيل</a>
                                <?php if($live): ?>
                                    <a href="<?php echo e(route('employee.academic-supervision.meeting.observe', $live)); ?>" class="block text-xs text-cyan-600 font-semibold mt-1 hover:underline">دخول المراقبة</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-gray-500">لم يُعيَّن لك طلاب بعد. تواصل مع الإدارة لربط الطلاب بحسابك.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\employee\academic-supervision\index.blade.php ENDPATH**/ ?>