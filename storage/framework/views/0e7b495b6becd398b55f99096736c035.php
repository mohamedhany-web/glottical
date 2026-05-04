

<?php $__env->startSection('title', 'طلب إجازة'); ?>
<?php $__env->startSection('header', 'تفاصيل طلب الإجازة'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6">
    <?php if(session('error')): ?>
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-900 px-4 py-3 text-sm"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">معلومات الطلب</h2>
            <span class="px-3 py-1 text-sm font-semibold rounded-full
                <?php if($leave->status === 'pending'): ?> bg-amber-100 text-amber-800
                <?php elseif($leave->status === 'approved'): ?> bg-emerald-100 text-emerald-800
                <?php elseif($leave->status === 'rejected'): ?> bg-rose-100 text-rose-800
                <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>">
                <?php echo e($leave->status_label); ?>

            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
            <div>
                <p class="text-gray-500 font-medium mb-1">الموظف</p>
                <p class="font-bold text-gray-900"><?php echo e($leave->employee->name); ?></p>
                <?php if($leave->employee->employee_code): ?>
                    <p class="text-gray-500 mt-1">الرمز: <?php echo e($leave->employee->employee_code); ?></p>
                <?php endif; ?>
                <?php if($leave->employee->employeeJob): ?>
                    <p class="text-gray-500">الوظيفة: <?php echo e($leave->employee->employeeJob->name); ?></p>
                <?php endif; ?>
            </div>
            <div>
                <p class="text-gray-500 font-medium mb-1">نوع الإجازة</p>
                <p class="font-bold text-gray-900"><?php echo e($leave->type_label); ?></p>
            </div>
            <div>
                <p class="text-gray-500 font-medium mb-1">من</p>
                <p class="font-semibold text-gray-900"><?php echo e($leave->start_date->format('Y-m-d')); ?></p>
            </div>
            <div>
                <p class="text-gray-500 font-medium mb-1">إلى</p>
                <p class="font-semibold text-gray-900"><?php echo e($leave->end_date->format('Y-m-d')); ?></p>
            </div>
            <div>
                <p class="text-gray-500 font-medium mb-1">عدد الأيام</p>
                <p class="font-semibold text-gray-900"><?php echo e($leave->days); ?> يوم</p>
            </div>
            <div>
                <p class="text-gray-500 font-medium mb-1">تاريخ التقديم</p>
                <p class="font-semibold text-gray-900"><?php echo e($leave->created_at->format('Y-m-d H:i')); ?></p>
            </div>
            <div class="md:col-span-2">
                <p class="text-gray-500 font-medium mb-1">السبب</p>
                <div class="bg-gray-50 p-4 rounded-lg whitespace-pre-wrap text-gray-900"><?php echo e($leave->reason); ?></div>
            </div>
        </div>
    </div>

    <?php if($leave->status !== 'pending' && $leave->reviewer): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">المراجعة</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">بواسطة</p>
                    <p class="font-semibold"><?php echo e($leave->reviewer->name); ?></p>
                </div>
                <div>
                    <p class="text-gray-500">التاريخ</p>
                    <p class="font-semibold"><?php echo e($leave->reviewed_at?->format('Y-m-d H:i')); ?></p>
                </div>
                <?php if($leave->admin_notes): ?>
                    <div class="md:col-span-2">
                        <p class="text-gray-500 mb-1">ملاحظات</p>
                        <div class="bg-gray-50 p-4 rounded-lg whitespace-pre-wrap"><?php echo e($leave->admin_notes); ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if($leave->status === 'pending'): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">قرار الموارد البشرية</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <form action="<?php echo e(route('employee.hr.leaves.approve', $leave)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">ملاحظات (اختياري)</label>
                        <textarea name="admin_notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500"></textarea>
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-3 rounded-lg font-bold text-sm">
                            <i class="fas fa-check ml-2"></i>موافقة
                        </button>
                    </div>
                </form>
                <form action="<?php echo e(route('employee.hr.leaves.reject', $leave)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">سبب الرفض <span class="text-rose-600">*</span></label>
                        <textarea name="admin_notes" rows="3" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-rose-500"></textarea>
                        <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white px-4 py-3 rounded-lg font-bold text-sm">
                            <i class="fas fa-times ml-2"></i>رفض
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <div class="flex justify-end">
        <a href="<?php echo e(route('employee.hr.leaves.index')); ?>" class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-semibold">
            <i class="fas fa-arrow-right ml-2"></i>القائمة
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\employee\hr\leaves\show.blade.php ENDPATH**/ ?>