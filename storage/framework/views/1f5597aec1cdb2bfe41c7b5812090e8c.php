<?php $__env->startSection('title', 'تفاصيل الكوبون: ' . $coupon->code); ?>
<?php $__env->startSection('header', ''); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex items-start gap-3">
            <a href="<?php echo e(route('admin.coupons.index')); ?>" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 transition-colors"><i class="fas fa-arrow-right"></i></a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white font-heading">
                    <i class="fas fa-ticket-alt text-violet-500 ml-2"></i><?php echo e($coupon->code); ?>

                </h1>
                <p class="text-slate-600 dark:text-slate-300 mt-1"><?php echo e($coupon->title ?? $coupon->name); ?></p>
                <div class="flex flex-wrap gap-2 mt-3">
                    <?php
                        $isActive = $coupon->is_active && (!$coupon->expires_at || $coupon->expires_at >= now());
                    ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($isActive ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-600 text-slate-600 dark:text-slate-300'); ?>">
                        <?php echo e($isActive ? 'نشط' : 'منتهي أو غير نشط'); ?>

                    </span>
                    <?php if($coupon->is_public ?? true): ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-300">عام</span>
                    <?php else: ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-300">خاص</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.coupon-commissions.index', array_filter(['beneficiary_id' => $coupon->beneficiary_user_id]))); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 text-sm font-semibold hover:bg-amber-50 dark:hover:bg-amber-900/20">
                <i class="fas fa-coins"></i> عمولات التسويق
            </a>
            <a href="<?php echo e(route('admin.coupons.edit', $coupon)); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition-colors">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <form method="POST" action="<?php echo e(route('admin.coupons.destroy', $coupon)); ?>" class="inline" onsubmit="return confirm('حذف هذا الكوبون؟');">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-sm font-medium hover:bg-red-50 dark:hover:bg-red-900/20">حذف</button>
            </form>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-5">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
            <h2 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2"><i class="fas fa-percent text-violet-500"></i> تفاصيل الخصم</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-slate-500">نوع الخصم</dt><dd class="font-medium text-slate-800 dark:text-white"><?php echo e($coupon->discount_type === 'percentage' ? 'نسبة مئوية' : 'مبلغ ثابت'); ?></dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">القيمة</dt><dd class="font-bold text-slate-800 dark:text-white"><?php echo e($coupon->discount_type === 'percentage' ? $coupon->discount_value.'%' : number_format($coupon->discount_value, 2).' ج.م'); ?></dd></div>
                <?php if($coupon->minimum_amount): ?>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">الحد الأدنى للطلب</dt><dd><span class="font-mono"><?php echo e(number_format($coupon->minimum_amount, 2)); ?> ج.م</span></dd></div>
                <?php endif; ?>
                <?php if($coupon->maximum_discount): ?>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">الحد الأقصى للخصم</dt><dd><span class="font-mono"><?php echo e(number_format($coupon->maximum_discount, 2)); ?> ج.م</span></dd></div>
                <?php endif; ?>
            </dl>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
            <h2 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2"><i class="fas fa-chart-line text-cyan-500"></i> الاستخدام</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-slate-500">عدد الاستخدامات</dt><dd class="font-mono font-semibold"><?php echo e($coupon->used_count ?? 0); ?> <?php if($coupon->usage_limit): ?> / <?php echo e($coupon->usage_limit); ?> <?php else: ?> <span class="text-slate-400">/ ∞</span> <?php endif; ?></dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">الحد لكل مستخدم</dt><dd><?php echo e($coupon->usage_limit_per_user ?? 1); ?></dd></div>
                <?php if($coupon->starts_at): ?>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">من</dt><dd><?php echo e($coupon->starts_at->format('Y-m-d')); ?></dd></div>
                <?php endif; ?>
                <?php if($coupon->expires_at): ?>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">إلى</dt><dd><?php echo e($coupon->expires_at->format('Y-m-d')); ?></dd></div>
                <?php endif; ?>
            </dl>
        </div>
    </div>

    <?php
        $scopeLabel = match ($coupon->applicable_to ?? 'all') {
            'courses' => 'كورسات محددة',
            'specific' => 'كورسات محددة + مستخدمون (إن وُجد)',
            'subscriptions' => 'الاشتراكات فقط',
            default => 'جميع الكورسات',
        };
    ?>
    <div class="grid md:grid-cols-2 gap-5">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
            <h2 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2"><i class="fas fa-bullseye text-violet-500"></i> النطاق والمستخدمون</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-slate-500">نطاق الكورسات</dt><dd class="font-medium text-slate-800 dark:text-white"><?php echo e($scopeLabel); ?></dd></div>
                <?php if(isset($scopedCourses) && $scopedCourses->count() > 0): ?>
                <div>
                    <dt class="text-slate-500 mb-2">الكورسات المحددة</dt>
                    <dd class="text-slate-700 dark:text-slate-200 space-y-1">
                        <?php $__currentLoopData = $scopedCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="text-xs font-mono bg-slate-100 dark:bg-slate-700/50 rounded px-2 py-1"><?php echo e($sc->title); ?> <span class="text-slate-500">#<?php echo e($sc->id); ?></span></div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </dd>
                </div>
                <?php endif; ?>
                <?php if(is_array($coupon->applicable_user_ids) && count($coupon->applicable_user_ids) > 0): ?>
                <div class="flex justify-between gap-4 flex-wrap"><dt class="text-slate-500">مستخدمون مسموح لهم</dt><dd class="font-mono text-xs"><?php echo e(implode(', ', $coupon->applicable_user_ids)); ?></dd></div>
                <?php else: ?>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">تقييد المستخدمين</dt><dd>لا يوجد (أي مستخدم يحقق الشروط)</dd></div>
                <?php endif; ?>
            </dl>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-amber-200 dark:border-amber-900/40 p-6 shadow-sm">
            <h2 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2"><i class="fas fa-coins text-amber-500"></i> عمولة التسويق</h2>
            <?php if($coupon->beneficiary_user_id && $coupon->commission_percent): ?>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-slate-500">المستفيد</dt><dd class="font-medium text-slate-800 dark:text-white"><?php echo e($coupon->beneficiary->name ?? '—'); ?> <span class="font-mono text-xs text-slate-500">#<?php echo e($coupon->beneficiary_user_id); ?></span></dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">النسبة</dt><dd class="font-bold"><?php echo e($coupon->commission_percent); ?>%</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">القاعدة</dt><dd><?php echo e(($coupon->commission_on ?? 'final_paid') === 'original_price' ? 'السعر الأصلي' : 'المبلغ النهائي بعد الخصم'); ?></dd></div>
            </dl>
            <?php else: ?>
            <p class="text-sm text-slate-500 dark:text-slate-400">لا توجد عمولة مسجّلة لهذا الكوبون.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php if($coupon->description): ?>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
        <h2 class="font-bold text-slate-800 dark:text-white mb-2">الوصف</h2>
        <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed"><?php echo e($coupon->description); ?></p>
    </div>
    <?php endif; ?>

    <?php if($coupon->usages && $coupon->usages->count() > 0): ?>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
            <h2 class="font-bold text-slate-800 dark:text-white">سجل الاستخدامات</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">المستخدم</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">الطلب</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">مبلغ الخصم</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    <?php $__currentLoopData = $coupon->usages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                        <td class="px-4 py-3 text-slate-800 dark:text-white"><?php echo e($usage->user->name ?? '—'); ?></td>
                        <td class="px-4 py-3 font-mono">
                            <?php if($usage->order_id): ?>
                            <a href="<?php echo e(route('admin.orders.show', $usage->order_id)); ?>" class="text-violet-600 dark:text-violet-400 hover:underline">#<?php echo e($usage->order_id); ?></a>
                            <?php else: ?>
                            —
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 font-mono"><?php echo e(number_format($usage->discount_amount ?? 0, 2)); ?> ج.م</td>
                        <td class="px-4 py-3 text-slate-500"><?php echo e($usage->created_at ? $usage->created_at->format('Y-m-d H:i') : '—'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\coupons\show.blade.php ENDPATH**/ ?>