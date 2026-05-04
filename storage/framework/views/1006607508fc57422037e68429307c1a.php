

<?php $__env->startSection('title', $employee->name); ?>
<?php $__env->startSection('header', 'ملف موظف — الموارد البشرية'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="flex flex-wrap gap-3">
        <a href="<?php echo e(route('employee.hr.employees.index')); ?>" class="text-sm font-semibold text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-right ml-1"></i> دليل الموظفين
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex flex-wrap justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-gray-900"><?php echo e($employee->name); ?></h2>
                <p class="text-sm text-gray-600 mt-1"><?php echo e($employee->employeeJob?->name ?? 'بدون وظيفة محددة'); ?></p>
            </div>
            <?php if($employee->is_active): ?>
                <span class="h-fit text-xs font-bold px-3 py-1 rounded-full bg-emerald-100 text-emerald-800">نشط</span>
            <?php else: ?>
                <span class="h-fit text-xs font-bold px-3 py-1 rounded-full bg-gray-200 text-gray-700">موقوف</span>
            <?php endif; ?>
        </div>

        <dl class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500 font-medium">البريد</dt><dd class="font-medium text-gray-900"><?php echo e($employee->email); ?></dd></div>
            <div><dt class="text-gray-500 font-medium">الهاتف</dt><dd class="font-medium text-gray-900"><?php echo e($employee->phone ?: '—'); ?></dd></div>
            <div><dt class="text-gray-500 font-medium">رمز الموظف</dt><dd class="font-medium text-gray-900"><?php echo e($employee->employee_code ?: '—'); ?></dd></div>
            <div><dt class="text-gray-500 font-medium">تاريخ التوظيف</dt><dd class="font-medium text-gray-900"><?php echo e($employee->hire_date ? $employee->hire_date->format('Y-m-d') : '—'); ?></dd></div>
            <div><dt class="text-gray-500 font-medium">إنهاء الخدمة</dt><dd class="font-medium text-gray-900"><?php echo e($employee->termination_date ? $employee->termination_date->format('Y-m-d') : '—'); ?></dd></div>
            <div><dt class="text-gray-500 font-medium">الراتب (مرجعي)</dt><dd class="font-medium text-gray-900 tabular-nums"><?php echo e($employee->salary !== null ? number_format((float) $employee->salary, 2) : '—'); ?></dd></div>
        </dl>

        <?php if($employee->employee_notes): ?>
            <div class="mt-6">
                <p class="text-xs font-bold text-gray-500 mb-1">ملاحظات إدارية (من الملف)</p>
                <div class="text-sm bg-amber-50 border border-amber-100 rounded-lg p-3 whitespace-pre-wrap text-gray-800"><?php echo e($employee->employee_notes); ?></div>
            </div>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="rounded-xl border border-gray-200 bg-white p-4 text-center">
            <p class="text-xs text-gray-500">إجازات معلّقة</p>
            <p class="text-2xl font-black text-amber-800 tabular-nums"><?php echo e($leaveStats['pending']); ?></p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 text-center">
            <p class="text-xs text-gray-500">أيام معتمدة (السنة)</p>
            <p class="text-2xl font-black text-emerald-800 tabular-nums"><?php echo e($leaveStats['approved_year']); ?></p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 text-center">
            <p class="text-xs text-gray-500">مهام قيد التنفيذ</p>
            <p class="text-2xl font-black text-indigo-800 tabular-nums"><?php echo e($pendingTasks); ?></p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 text-center">
            <p class="text-xs text-gray-500">اتفاقيات عمل</p>
            <p class="text-2xl font-black text-violet-800 tabular-nums"><?php echo e($agreementsCount); ?></p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 font-bold text-gray-900">آخر طلبات الإجازة</div>
        <ul class="divide-y divide-gray-100 max-h-64 overflow-y-auto text-sm">
            <?php $__empty_1 = true; $__currentLoopData = $recentLeaves; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <li class="px-5 py-3 flex justify-between gap-2">
                    <span><?php echo e($lv->type_label); ?> — <?php echo e($lv->start_date?->format('Y-m-d')); ?></span>
                    <span class="text-xs font-bold shrink-0 <?php if($lv->status === 'approved'): ?> text-emerald-700 <?php elseif($lv->status === 'pending'): ?> text-amber-700 <?php else: ?> text-gray-600 <?php endif; ?>"><?php echo e($lv->status_label); ?></span>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <li class="px-5 py-8 text-center text-gray-500">لا توجد طلبات.</li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">سجل الموارد البشرية</h3>
        <p class="text-xs text-gray-500 mb-4">تسجيلات داخلية (إنذارات، تقدير، ملاحظات) — تظهر هنا فقط لمن لديهم صلاحية HR.</p>

        <form method="POST" action="<?php echo e(route('employee.hr.employees.hr-events.store', $employee)); ?>" class="space-y-3 mb-8 pb-8 border-b border-gray-100">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">النوع</label>
                    <select name="event_type" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <?php $__currentLoopData = \App\Models\HrEmployeeEvent::typeLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($val); ?>"><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">تاريخ الحدث</label>
                    <input type="date" name="event_date" value="<?php echo e(old('event_date', now()->format('Y-m-d'))); ?>" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">عنوان مختصر (اختياري)</label>
                <input type="text" name="title" value="<?php echo e(old('title')); ?>" maxlength="255" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">التفاصيل <span class="text-rose-600">*</span></label>
                <textarea name="body" rows="4" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"><?php echo e(old('body')); ?></textarea>
            </div>
            <?php $__errorArgs = ['event_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <button type="submit" class="px-4 py-2 rounded-lg bg-gray-900 hover:bg-gray-800 text-white text-sm font-bold">إضافة للسجل</button>
        </form>

        <ul class="space-y-4">
            <?php $__empty_1 = true; $__currentLoopData = $hrEvents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <li class="rounded-lg border border-gray-100 bg-gray-50/80 p-4 text-sm">
                    <div class="flex flex-wrap justify-between gap-2 mb-2">
                        <span class="font-bold text-gray-900"><?php echo e($ev->type_label); ?></span>
                        <span class="text-xs text-gray-500"><?php echo e($ev->event_date?->format('Y-m-d')); ?> — <?php echo e($ev->author?->name); ?></span>
                    </div>
                    <?php if($ev->title): ?><p class="font-semibold text-gray-800 mb-1"><?php echo e($ev->title); ?></p><?php endif; ?>
                    <p class="text-gray-700 whitespace-pre-wrap"><?php echo e($ev->body); ?></p>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <li class="text-center text-gray-500 py-6">لا توجد تسجيلات بعد.</li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\employee\hr\employees\show.blade.php ENDPATH**/ ?>