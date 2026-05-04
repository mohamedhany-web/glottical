

<?php $__env->startSection('title', 'عمولات كوبونات التسويق'); ?>
<?php $__env->startSection('header', ''); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white font-heading">
                <i class="fas fa-coins text-amber-500 ml-2"></i>عمولات كوبونات التسويق
            </h1>
            <p class="text-sm text-slate-500 mt-1">مستحقات مرتبطة بطلبات تم اعتمادها — سجّل مصروفاً تسويقياً ثم وافق عليه من المصروفات لإتمام التسوية</p>
        </div>
        <a href="<?php echo e(route('admin.coupons.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700/50">
            <i class="fas fa-ticket-alt"></i> الكوبونات
        </a>
    </div>

    <?php if(session('success')): ?>
    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-4 text-emerald-700 dark:text-emerald-400 text-sm">
        <i class="fas fa-check-circle ml-1"></i> <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-red-700 dark:text-red-400 text-sm">
        <i class="fas fa-exclamation-circle ml-1"></i> <?php echo e(session('error')); ?>

    </div>
    <?php endif; ?>

    <?php if(isset($stats)): ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
            <div class="text-sm text-slate-500 dark:text-slate-400">بانتظار مصروف</div>
            <div class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1"><?php echo e($stats['pending'] ?? 0); ?></div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
            <div class="text-sm text-slate-500 dark:text-slate-400">مصروف معلق</div>
            <div class="text-2xl font-bold text-cyan-600 dark:text-cyan-400 mt-1"><?php echo e($stats['expense_pending'] ?? 0); ?></div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
            <div class="text-sm text-slate-500 dark:text-slate-400">مسوّاة</div>
            <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1"><?php echo e($stats['settled'] ?? 0); ?></div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
            <div class="text-sm text-slate-500 dark:text-slate-400">مبالغ غير مسوّاة (ج.م)</div>
            <div class="text-2xl font-bold text-slate-800 dark:text-white mt-1 font-mono"><?php echo e(number_format($stats['amount_pending'] ?? 0, 2)); ?></div>
        </div>
    </div>
    <?php endif; ?>

    <form method="GET" action="<?php echo e(route('admin.coupon-commissions.index')); ?>" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 flex flex-wrap items-end gap-3">
        <div class="w-full sm:w-44">
            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">الحالة</label>
            <select name="status" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                <option value="">الكل</option>
                <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>بانتظار مصروف</option>
                <option value="expense_pending" <?php echo e(request('status') === 'expense_pending' ? 'selected' : ''); ?>>مصروف معلق</option>
                <option value="settled" <?php echo e(request('status') === 'settled' ? 'selected' : ''); ?>>مسوّاة</option>
                <option value="cancelled" <?php echo e(request('status') === 'cancelled' ? 'selected' : ''); ?>>ملغاة</option>
            </select>
        </div>
        <div class="flex-1 min-w-[140px]">
            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">معرّف المستفيد</label>
            <input type="number" name="beneficiary_id" min="1" value="<?php echo e(request('beneficiary_id')); ?>" placeholder="User ID" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
        </div>
        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-800 dark:bg-slate-600 hover:bg-slate-700 text-white text-sm font-medium">
            <i class="fas fa-filter"></i> تطبيق
        </button>
        <?php if(request()->hasAny(['status', 'beneficiary_id'])): ?>
        <a href="<?php echo e(route('admin.coupon-commissions.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 text-sm">إعادة تعيين</a>
        <?php endif; ?>
    </form>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">#</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">الكوبون</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">المستفيد</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">الطلب</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">القاعدة / النسبة</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">العمولة</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">الحالة</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    <?php $__empty_1 = true; $__currentLoopData = $accruals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                        <td class="px-4 py-3 font-mono text-slate-500"><?php echo e($row->id); ?></td>
                        <td class="px-4 py-3">
                            <span class="font-mono font-semibold text-slate-800 dark:text-white"><?php echo e($row->coupon->code ?? '—'); ?></span>
                        </td>
                        <td class="px-4 py-3 text-slate-800 dark:text-white"><?php echo e($row->beneficiary->name ?? '—'); ?> <span class="text-xs text-slate-500 font-mono">#<?php echo e($row->beneficiary_user_id); ?></span></td>
                        <td class="px-4 py-3">
                            <?php if($row->order_id): ?>
                            <a href="<?php echo e(route('admin.orders.show', $row->order_id)); ?>" class="text-violet-600 dark:text-violet-400 hover:underline font-mono">#<?php echo e($row->order_id); ?></a>
                            <?php else: ?>
                            —
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 font-mono text-slate-700 dark:text-slate-200"><?php echo e(number_format($row->base_amount_egp, 2)); ?> × <?php echo e($row->commission_percent); ?>%</td>
                        <td class="px-4 py-3 font-mono font-semibold text-slate-800 dark:text-white"><?php echo e(number_format($row->commission_amount_egp, 2)); ?> ج.م</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                                <?php if($row->status === 'settled'): ?> bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300
                                <?php elseif($row->status === 'pending'): ?> bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300
                                <?php elseif($row->status === 'expense_pending'): ?> bg-cyan-100 dark:bg-cyan-900/40 text-cyan-800 dark:text-cyan-300
                                <?php else: ?> bg-slate-100 dark:bg-slate-600 text-slate-700 dark:text-slate-200 <?php endif; ?>">
                                <?php echo e(\App\Models\CouponCommissionAccrual::statusLabel($row->status)); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-2">
                                <?php if($row->status === \App\Models\CouponCommissionAccrual::STATUS_PENDING): ?>
                                <form method="POST" action="<?php echo e(route('admin.coupon-commissions.store-expense', $row)); ?>" onsubmit="return confirm('إنشاء مصروف تسويق معلق بهذا المبلغ؟');">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-medium">إنشاء مصروف</button>
                                </form>
                                <?php endif; ?>
                                <?php if($row->expense_id): ?>
                                <a href="<?php echo e(route('admin.expenses.show', $row->expense_id)); ?>" class="text-xs px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700">المصروف</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-slate-500 dark:text-slate-400">لا توجد مستحقات مسجّلة بعد. تُنشأ تلقائياً عند اعتماد طلب استخدم كوبوناً له مستفيد ونسبة عمولة.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($accruals->hasPages()): ?>
        <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
            <?php echo e($accruals->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\coupon-commissions\index.blade.php ENDPATH**/ ?>