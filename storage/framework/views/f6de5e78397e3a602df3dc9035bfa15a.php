

<?php $__env->startSection('title', 'Lead #' . $salesLead->id); ?>
<?php $__env->startSection('header', 'عميل محتمل #' . $salesLead->id); ?>

<?php $__env->startSection('content'); ?>
<?php
    $open = !$salesLead->isConverted() && !$salesLead->isLost();
?>
<div class="space-y-6 max-w-4xl">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="<?php echo e(route('employee.sales.leads.index')); ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-right"></i> القائمة
        </a>
        <div class="flex flex-wrap gap-2">
            <?php if($open): ?>
                <a href="<?php echo e(route('employee.sales.leads.edit', $salesLead)); ?>" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-800 hover:bg-gray-900 text-white text-sm font-bold">تعديل</a>
            <?php endif; ?>
            <?php if($open && (int) $salesLead->assigned_to !== (int) auth()->id()): ?>
                <form method="POST" action="<?php echo e(route('employee.sales.leads.assign-me', $salesLead)); ?>" class="inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="px-3 py-2 rounded-lg bg-teal-100 hover:bg-teal-200 text-teal-900 text-sm font-bold">تعيين لي</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 text-sm"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-900 px-4 py-3 text-sm"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="text-xl font-black text-gray-900"><?php echo e($salesLead->name); ?></h2>
                <?php if($salesLead->company): ?><p class="text-sm text-gray-600"><?php echo e($salesLead->company); ?></p><?php endif; ?>
            </div>
            <div>
                <?php if($salesLead->status === \App\Models\SalesLead::STATUS_CONVERTED): ?>
                    <span class="rounded-full bg-emerald-100 text-emerald-800 px-3 py-1 text-sm font-bold"><?php echo e($salesLead->status_label); ?></span>
                <?php elseif($salesLead->status === \App\Models\SalesLead::STATUS_LOST): ?>
                    <span class="rounded-full bg-rose-100 text-rose-800 px-3 py-1 text-sm font-bold"><?php echo e($salesLead->status_label); ?></span>
                <?php else: ?>
                    <span class="rounded-full bg-amber-100 text-amber-800 px-3 py-1 text-sm font-bold"><?php echo e($salesLead->status_label); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div><dt class="text-gray-500 font-semibold">البريد</dt><dd class="text-gray-900"><?php echo e($salesLead->email ?: '—'); ?></dd></div>
            <div><dt class="text-gray-500 font-semibold">الهاتف</dt><dd class="text-gray-900"><?php echo e($salesLead->phone ?: '—'); ?></dd></div>
            <div><dt class="text-gray-500 font-semibold">المصدر</dt><dd class="text-gray-900"><?php echo e($salesLead->source_label); ?></dd></div>
            <div><dt class="text-gray-500 font-semibold">كورس الاهتمام</dt><dd class="text-gray-900"><?php echo e($salesLead->interestedCourse?->title ?? '—'); ?></dd></div>
            <div><dt class="text-gray-500 font-semibold">أنشأه</dt><dd class="text-gray-900"><?php echo e($salesLead->creator?->name ?? '—'); ?></dd></div>
            <div><dt class="text-gray-500 font-semibold">المسؤول الحالي</dt><dd class="text-gray-900"><?php echo e($salesLead->assignedTo?->name ?? '—'); ?></dd></div>
        </dl>
        <?php if($salesLead->notes): ?>
            <div>
                <p class="text-xs font-semibold text-gray-500 mb-1">ملاحظات</p>
                <div class="rounded-lg bg-gray-50 border border-gray-100 p-3 text-sm text-gray-800 whitespace-pre-wrap"><?php echo e($salesLead->notes); ?></div>
            </div>
        <?php endif; ?>
        <?php if($salesLead->isConverted()): ?>
            <div class="rounded-lg border border-emerald-200 bg-emerald-50/50 p-4 text-sm space-y-2">
                <p class="font-bold text-emerald-900">بيانات التحويل</p>
                <?php if($salesLead->converted_at): ?><p class="text-emerald-800">تاريخ: <?php echo e($salesLead->converted_at->format('Y-m-d H:i')); ?></p><?php endif; ?>
                <?php if($salesLead->linkedUser): ?>
                    <p>مستخدم منصة: <span class="font-bold text-gray-900"><?php echo e($salesLead->linkedUser->name); ?></span>
                        <?php if($salesLead->linkedUser->email): ?><span class="text-gray-600"> — <?php echo e($salesLead->linkedUser->email); ?></span><?php endif; ?>
                    </p>
                <?php endif; ?>
                <?php if($salesLead->convertedOrder): ?>
                    <p>طلب مرتبط: <a href="<?php echo e(route('employee.sales.orders.show', $salesLead->convertedOrder)); ?>" class="font-bold text-teal-700 hover:underline">#<?php echo e($salesLead->convertedOrder->id); ?></a>
                        — <?php echo e($salesLead->convertedOrder->course?->title ?? '—'); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php if($salesLead->isLost() && $salesLead->lost_reason): ?>
            <div class="rounded-lg border border-rose-200 bg-rose-50/50 p-4 text-sm">
                <p class="font-bold text-rose-900 mb-1">سبب الخسارة</p>
                <p class="text-rose-800 whitespace-pre-wrap"><?php echo e($salesLead->lost_reason); ?></p>
            </div>
        <?php endif; ?>
    </div>

    <?php if($open): ?>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6" x-data="{ mode: <?php echo e(json_encode(old('mode', 'order'))); ?> }">
            <h3 class="text-base font-bold text-gray-900 mb-4">تحويل إلى عميل فعلي</h3>
            <form method="POST" action="<?php echo e(route('employee.sales.leads.convert', $salesLead)); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div class="space-y-2 text-sm">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="mode" value="order" x-model="mode" class="text-teal-600 focus:ring-teal-500">
                        <span>ربط بطلب موجود (رقم الطلب)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="mode" value="user" x-model="mode" class="text-teal-600 focus:ring-teal-500">
                        <span>ربط بمستخدم مسجّل (معرّف المستخدم)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="mode" value="manual" x-model="mode" class="text-teal-600 focus:ring-teal-500">
                        <span>تحويل يدوي (بدون ربط طلب/مستخدم)</span>
                    </label>
                </div>
                <div x-show="mode === 'order'" x-cloak class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-600">رقم الطلب</label>
                    <input type="number" name="order_id" value="<?php echo e(old('order_id')); ?>" min="1" class="w-full max-w-xs rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="مثال: 120">
                    <?php $__errorArgs = ['order_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div x-show="mode === 'user'" x-cloak class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-600">معرّف المستخدم (من لوحة الإدارة)</label>
                    <input type="number" name="user_id" value="<?php echo e(old('user_id')); ?>" min="1" class="w-full max-w-xs rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <?php $__errorArgs = ['user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">ملاحظة على التحويل (اختياري)</label>
                    <textarea name="conversion_note" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"><?php echo e(old('conversion_note')); ?></textarea>
                </div>
                <?php $__errorArgs = ['mode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">تسجيل التحويل</button>
            </form>
        </div>

        <div class="bg-white rounded-xl border border-rose-200 shadow-sm p-6">
            <h3 class="text-base font-bold text-rose-900 mb-3">تسجيل خسارة</h3>
            <form method="POST" action="<?php echo e(route('employee.sales.leads.lost', $salesLead)); ?>" class="space-y-3">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">السبب <span class="text-rose-600">*</span></label>
                    <textarea name="lost_reason" required rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"><?php echo e(old('lost_reason')); ?></textarea>
                    <?php $__errorArgs = ['lost_reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <button type="submit" class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold">حفظ كخاسر</button>
            </form>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\employee\sales\leads\show.blade.php ENDPATH**/ ?>