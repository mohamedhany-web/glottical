

<?php $__env->startSection('title', 'بوابة الدفع - التقارير المحاسبية - Muallimx'); ?>
<?php $__env->startSection('header', 'مدفوعات بوابة الدفع'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full space-y-6">
    <nav class="text-sm text-slate-500 mb-2">
        <a href="<?php echo e(route('admin.accounting.reports')); ?>" class="text-sky-600 hover:text-sky-700">التقارير المحاسبية</a>
        <span class="mx-1">/</span>
        <span class="text-slate-700">بوابة الدفع</span>
    </nav>

    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4">
                <h2 class="text-xl font-bold text-slate-900">ملخص مدفوعات البوابة (أونلاين ومكتملة)</h2>
                <a href="<?php echo e(route('admin.accounting.reports.export', array_merge(request()->query(), ['type' => 'payment_gateway']))); ?>" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-700">
                    <i class="fas fa-file-excel"></i>
                    تصدير Excel
                </a>
            </div>
            <form method="GET" action="<?php echo e(route('admin.accounting.reports.payment-gateway')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">الفترة</label>
                    <select name="period" class="w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-2.5 text-sm" onchange="this.form.submit()">
                        <option value="day" <?php echo e(($period ?? '') == 'day' ? 'selected' : ''); ?>>اليوم</option>
                        <option value="week" <?php echo e(($period ?? '') == 'week' ? 'selected' : ''); ?>>هذا الأسبوع</option>
                        <option value="month" <?php echo e(($period ?? '') == 'month' ? 'selected' : ''); ?>>هذا الشهر</option>
                        <option value="year" <?php echo e(($period ?? '') == 'year' ? 'selected' : ''); ?>>هذه السنة</option>
                        <option value="all" <?php echo e(($period ?? '') == 'all' ? 'selected' : ''); ?>>الكل</option>
                        <option value="custom" <?php echo e(($period ?? '') == 'custom' ? 'selected' : ''); ?>>مخصص</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">من تاريخ</label>
                    <input type="date" name="start_date" value="<?php echo e($startDate ? $startDate->format('Y-m-d') : ''); ?>" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-2">إلى تاريخ</label>
                    <input type="date" name="end_date" value="<?php echo e($endDate ? $endDate->format('Y-m-d') : ''); ?>" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm" />
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-sky-700">
                        <i class="fas fa-filter"></i>
                        تطبيق
                    </button>
                </div>
            </form>
            <p class="text-sm text-slate-500 mt-3">من <?php echo e($startDate->format('Y-m-d')); ?> إلى <?php echo e($endDate->format('Y-m-d')); ?></p>
        </div>
        <div class="p-5 sm:p-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/80 p-5">
                <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wide">عدد العمليات</p>
                <p class="text-2xl font-black text-emerald-900 mt-2"><?php echo e(number_format($gatewaySummary['count'])); ?></p>
            </div>
            <div class="rounded-2xl border border-sky-200 bg-sky-50/80 p-5">
                <p class="text-xs font-semibold text-sky-700 uppercase tracking-wide">إجمالي المحصّل (عميل)</p>
                <p class="text-2xl font-black text-sky-900 mt-2"><?php echo e(number_format($gatewaySummary['gross'], 2)); ?> ج.م</p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50/80 p-5">
                <p class="text-xs font-semibold text-amber-800 uppercase tracking-wide">عمولات البوابة (تقدير)</p>
                <p class="text-2xl font-black text-amber-900 mt-2"><?php echo e(number_format($gatewaySummary['fees'], 2)); ?> ج.م</p>
            </div>
            <div class="rounded-2xl border border-violet-200 bg-violet-50/80 p-5">
                <p class="text-xs font-semibold text-violet-800 uppercase tracking-wide">صافي بعد العمولة</p>
                <p class="text-2xl font-black text-violet-900 mt-2"><?php echo e(number_format($gatewaySummary['net'], 2)); ?> ج.م</p>
            </div>
        </div>
    </section>

    <?php if($byGateway->isNotEmpty()): ?>
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">حسب بوابة الدفع</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase text-slate-500">
                        <th class="px-4 py-3">البوابة</th>
                        <th class="px-4 py-3">العدد</th>
                        <th class="px-4 py-3">المحصّل</th>
                        <th class="px-4 py-3">العمولات</th>
                        <th class="px-4 py-3">الصافي</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php $__currentLoopData = $byGateway; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-semibold"><?php echo e($g->payment_gateway); ?></td>
                        <td class="px-4 py-3"><?php echo e(number_format($g->cnt)); ?></td>
                        <td class="px-4 py-3 text-emerald-700 font-semibold"><?php echo e(number_format($g->gross, 2)); ?></td>
                        <td class="px-4 py-3 text-amber-700 font-semibold"><?php echo e(number_format($g->fees, 2)); ?></td>
                        <td class="px-4 py-3 text-violet-800 font-semibold"><?php echo e(number_format($g->net, 2)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php endif; ?>

    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">تفاصيل المدفوعات</h3>
            <p class="text-xs text-slate-500 mt-1">فواتيرك تظهر كـ <code class="bg-slate-100 px-1 rounded">other</code>، كاشير كـ <code class="bg-slate-100 px-1 rounded">kashier</code></p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase text-slate-500">
                        <th class="px-4 py-3">رقم الدفعة</th>
                        <th class="px-4 py-3">العميل</th>
                        <th class="px-4 py-3">البوابة</th>
                        <th class="px-4 py-3">المحصّل</th>
                        <th class="px-4 py-3">العمولة</th>
                        <th class="px-4 py-3">الصافي</th>
                        <th class="px-4 py-3">الفاتورة</th>
                        <th class="px-4 py-3">تاريخ الدفع</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $fee = (float) ($payment->gateway_fee_amount ?? 0);
                        $net = $payment->net_after_gateway_fee !== null
                            ? (float) $payment->net_after_gateway_fee
                            : round((float) $payment->amount - $fee, 2);
                    ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <a href="<?php echo e(route('admin.payments.show', $payment)); ?>" class="font-semibold text-sky-600 hover:text-sky-700"><?php echo e($payment->payment_number); ?></a>
                        </td>
                        <td class="px-4 py-3"><?php echo e($payment->user->name ?? '—'); ?></td>
                        <td class="px-4 py-3 font-mono text-xs"><?php echo e($payment->payment_gateway); ?></td>
                        <td class="px-4 py-3 font-semibold text-emerald-700"><?php echo e(number_format($payment->amount, 2)); ?></td>
                        <td class="px-4 py-3 text-amber-700"><?php echo e(number_format($fee, 2)); ?></td>
                        <td class="px-4 py-3 text-violet-800"><?php echo e(number_format($net, 2)); ?></td>
                        <td class="px-4 py-3">
                            <?php if($payment->invoice): ?>
                                <a href="<?php echo e(route('admin.invoices.show', $payment->invoice)); ?>" class="text-sky-600 hover:text-sky-700"><?php echo e($payment->invoice->invoice_number); ?></a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3"><?php echo e($payment->paid_at ? $payment->paid_at->format('Y-m-d H:i') : '—'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8" class="px-4 py-8 text-center text-slate-500">لا توجد مدفوعات بوابة في هذه الفترة.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($items->hasPages()): ?>
        <div class="px-5 py-4 border-t border-slate-200">
            <?php echo e($items->links()); ?>

        </div>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\accounting\reports-payment-gateway.blade.php ENDPATH**/ ?>