
<?php $__env->startSection('title', 'جلسات البث المباشر'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">
                <i class="fas fa-broadcast-tower text-red-500 ml-2"></i>جلسات البث المباشر
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">إدارة جلسات البث والتحكم في المعلمين (المعلم = المشترك عندنا — طالب يشترون منا الخدمة)</p>
        </div>
        <a href="<?php echo e(route('admin.live-sessions.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl font-semibold shadow-lg shadow-red-500/25 transition-all">
            <i class="fas fa-plus"></i> إنشاء جلسة جديدة
        </a>
    </div>

    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                    <i class="fas fa-list text-slate-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800 dark:text-white"><?php echo e($stats['total']); ?></p>
                    <p class="text-xs text-slate-500">إجمالي الجلسات</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-red-200 dark:border-red-900/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-red-50 dark:bg-red-900/30 flex items-center justify-center">
                    <i class="fas fa-circle text-red-500 animate-pulse text-xs"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-red-600"><?php echo e($stats['live']); ?></p>
                    <p class="text-xs text-slate-500">مباشر الآن</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-blue-200 dark:border-blue-900/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                    <i class="fas fa-clock text-blue-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-blue-600"><?php echo e($stats['scheduled']); ?></p>
                    <p class="text-xs text-slate-500">مجدولة</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-emerald-200 dark:border-emerald-900/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-emerald-600"><?php echo e($stats['ended']); ?></p>
                    <p class="text-xs text-slate-500">منتهية</p>
                </div>
            </div>
        </div>
    </div>

    
    <form method="GET" class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="text-xs text-slate-500 mb-1 block">بحث</label>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="اسم الجلسة أو الغرفة..." class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
        </div>
        <div>
            <label class="text-xs text-slate-500 mb-1 block">الحالة</label>
            <select name="status" class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                <option value="">الكل</option>
                <option value="scheduled" <?php echo e(request('status') == 'scheduled' ? 'selected' : ''); ?>>مجدولة</option>
                <option value="live" <?php echo e(request('status') == 'live' ? 'selected' : ''); ?>>مباشر</option>
                <option value="ended" <?php echo e(request('status') == 'ended' ? 'selected' : ''); ?>>منتهية</option>
                <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>ملغاة</option>
            </select>
        </div>
        <div>
            <label class="text-xs text-slate-500 mb-1 block">الكورس</label>
            <select name="course_id" class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                <option value="">الكل</option>
                <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($course->id); ?>" <?php echo e(request('course_id') == $course->id ? 'selected' : ''); ?>><?php echo e(Str::limit($course->title, 30)); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="text-xs text-slate-500 mb-1 block">المعلم (المشترك)</label>
            <select name="instructor_id" class="rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                <option value="">الكل</option>
                <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($inst->id); ?>" <?php echo e(request('instructor_id') == $inst->id ? 'selected' : ''); ?>><?php echo e($inst->name); ?><?php echo e($inst->role === 'student' ? ' (مشترك)' : ''); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-slate-800 dark:bg-slate-600 text-white rounded-lg text-sm font-medium hover:bg-slate-700 transition-colors">
            <i class="fas fa-search ml-1"></i> بحث
        </button>
        <?php if(request()->hasAny(['search', 'status', 'course_id', 'instructor_id'])): ?>
            <a href="<?php echo e(route('admin.live-sessions.index')); ?>" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-sm hover:bg-slate-300 transition-colors">مسح</a>
        <?php endif; ?>
    </form>

    
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-4 py-3 text-right text-slate-600 dark:text-slate-300 font-semibold">#</th>
                        <th class="px-4 py-3 text-right text-slate-600 dark:text-slate-300 font-semibold">الجلسة</th>
                        <th class="px-4 py-3 text-right text-slate-600 dark:text-slate-300 font-semibold">المعلم (المشترك)</th>
                        <th class="px-4 py-3 text-right text-slate-600 dark:text-slate-300 font-semibold">الكورس</th>
                        <th class="px-4 py-3 text-center text-slate-600 dark:text-slate-300 font-semibold">الحالة</th>
                        <th class="px-4 py-3 text-center text-slate-600 dark:text-slate-300 font-semibold">الحضور</th>
                        <th class="px-4 py-3 text-right text-slate-600 dark:text-slate-300 font-semibold">الموعد</th>
                        <th class="px-4 py-3 text-center text-slate-600 dark:text-slate-300 font-semibold">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    <?php $__empty_1 = true; $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-4 py-3 text-slate-500"><?php echo e($session->id); ?></td>
                        <td class="px-4 py-3">
                            <a href="<?php echo e(route('admin.live-sessions.show', $session)); ?>" class="font-semibold text-slate-800 dark:text-white hover:text-blue-600 transition-colors"><?php echo e(Str::limit($session->title, 40)); ?></a>
                            <p class="text-[11px] text-slate-400 mt-0.5 font-mono"><?php echo e($session->room_name); ?></p>
                        </td>
                        <td class="px-4 py-3">
                            <?php if($session->instructor): ?>
                                <a href="<?php echo e(route('admin.users.show', $session->instructor->id)); ?>" class="font-medium text-slate-800 dark:text-white hover:text-blue-600 hover:underline"><?php echo e($session->instructor->name); ?></a>
                                <?php if($session->instructor->role === 'student'): ?>
                                    <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-medium">(مشترك)</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-slate-500">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300"><?php echo e(Str::limit($session->course?->title ?? 'عامة', 25)); ?></td>
                        <td class="px-4 py-3 text-center">
                            <?php if($session->status === 'live'): ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 text-xs font-bold">
                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span> مباشر
                                </span>
                            <?php elseif($session->status === 'scheduled'): ?>
                                <span class="px-2.5 py-1 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-xs font-medium">مجدولة</span>
                            <?php elseif($session->status === 'ended'): ?>
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-600 text-slate-600 dark:text-slate-300 text-xs font-medium">منتهية</span>
                            <?php else: ?>
                                <span class="px-2.5 py-1 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-600 text-xs font-medium">ملغاة</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center gap-1 text-slate-600 dark:text-slate-300">
                                <i class="fas fa-users text-xs text-slate-400"></i> <?php echo e($session->attendance_count); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-500 text-xs"><?php echo e($session->scheduled_at?->format('Y/m/d H:i') ?? '—'); ?></td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="<?php echo e(route('admin.live-sessions.show', $session)); ?>" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 hover:text-blue-600 transition-colors" title="عرض">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <?php if($session->status === 'scheduled'): ?>
                                    <a href="<?php echo e(route('admin.live-sessions.edit', $session)); ?>" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 hover:text-amber-600 transition-colors" title="تعديل">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <form method="POST" action="<?php echo e(route('admin.live-sessions.cancel', $session)); ?>" class="inline" onsubmit="return confirm('إلغاء هذه الجلسة؟')">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="p-1.5 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900/30 text-slate-500 hover:text-amber-600 transition-colors" title="إلغاء الجلسة">
                                            <i class="fas fa-ban text-xs"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <?php if($session->status === 'live'): ?>
                                    <form method="POST" action="<?php echo e(route('admin.live-sessions.force-end', $session)); ?>" class="inline" onsubmit="return confirm('هل تريد إنهاء البث؟')">
                                        <?php echo csrf_field(); ?>
                                        <button class="p-1.5 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 text-slate-500 hover:text-red-600 transition-colors" title="إنهاء البث">
                                            <i class="fas fa-stop text-xs"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center">
                            <i class="fas fa-broadcast-tower text-4xl text-slate-300 dark:text-slate-600 mb-3"></i>
                            <p class="text-slate-500">لا توجد جلسات بث بعد</p>
                            <a href="<?php echo e(route('admin.live-sessions.create')); ?>" class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-red-500 text-white rounded-lg text-sm font-medium hover:bg-red-600 transition-colors">
                                <i class="fas fa-plus"></i> إنشاء أول جلسة
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($sessions->hasPages()): ?>
        <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
            <?php echo e($sessions->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\live-sessions\index.blade.php ENDPATH**/ ?>