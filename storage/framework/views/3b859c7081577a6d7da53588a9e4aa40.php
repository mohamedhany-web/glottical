<?php $__env->startSection('title', 'الكوبونات والخصومات'); ?>
<?php $__env->startSection('header', ''); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white font-heading">
                <i class="fas fa-ticket-alt text-violet-500 ml-2"></i>الكوبونات والخصومات
            </h1>
            <p class="text-sm text-slate-500 mt-1">إدارة أكواد الخصم والاستخدامات</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="<?php echo e(route('admin.marketing.student-wallet-credit.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-semibold text-sm transition-all">
                <i class="fas fa-wallet"></i> رصيد محفظة طالب
            </a>
            <a href="<?php echo e(route('admin.coupons.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-semibold shadow-lg shadow-violet-500/25 transition-all">
                <i class="fas fa-plus"></i> إضافة كوبون جديد
            </a>
        </div>
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
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
            <div class="text-sm text-slate-500 dark:text-slate-400">إجمالي الكوبونات</div>
            <div class="text-2xl font-bold text-slate-800 dark:text-white mt-1"><?php echo e($stats['total'] ?? 0); ?></div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
            <div class="text-sm text-slate-500 dark:text-slate-400">نشطة</div>
            <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1"><?php echo e($stats['active'] ?? 0); ?></div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm">
            <div class="text-sm text-slate-500 dark:text-slate-400">منتهية/غير نشطة</div>
            <div class="text-2xl font-bold text-rose-600 dark:text-rose-400 mt-1"><?php echo e($stats['expired'] ?? 0); ?></div>
        </div>
    </div>
    <?php endif; ?>

    
    <form method="GET" action="<?php echo e(route('admin.coupons.index')); ?>" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">بحث</label>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="كود أو عنوان..." class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
        </div>
        <div class="w-full sm:w-40">
            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">الحالة</label>
            <select name="status" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                <option value="">الكل</option>
                <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>نشط</option>
                <option value="expired" <?php echo e(request('status') === 'expired' ? 'selected' : ''); ?>>منتهي</option>
            </select>
        </div>
        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-800 dark:bg-slate-600 hover:bg-slate-700 text-white text-sm font-medium">
            <i class="fas fa-filter"></i> تطبيق
        </button>
        <?php if(request()->hasAny(['search', 'status'])): ?>
        <a href="<?php echo e(route('admin.coupons.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50">إعادة تعيين</a>
        <?php endif; ?>
    </form>

    
    <?php if(isset($coupons) && $coupons->count() > 0): ?>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">الكود</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">العنوان</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">نوع الخصم</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">القيمة</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">الاستخدامات</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">الحالة</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    <?php $__currentLoopData = $coupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coupon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-4 py-3 text-slate-900 dark:text-white font-mono font-medium"><?php echo e($coupon->code); ?></td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300"><?php echo e($coupon->title ?? $coupon->name ?? '—'); ?></td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            <?php echo e($coupon->discount_type == 'percentage' ? 'نسبة مئوية' : 'مبلغ ثابت'); ?>

                        </td>
                        <td class="px-4 py-3 text-slate-900 dark:text-white font-medium">
                            <?php echo e($coupon->discount_type == 'percentage' ? $coupon->discount_value . '%' : number_format($coupon->discount_value, 2) . ' ج.م'); ?>

                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-400">
                            <span title="استخدامات فعلية"><?php echo e($coupon->used_count ?? ($coupon->usages_count ?? 0)); ?></span>
                            <?php if($coupon->usage_limit): ?>
                                / <?php echo e($coupon->usage_limit); ?>

                            <?php else: ?>
                                <span class="text-slate-400">/ ∞</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <?php
                                $isActive = $coupon->is_active && (!$coupon->expires_at || $coupon->expires_at >= now());
                            ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                <?php echo e($isActive ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-600 text-slate-600 dark:text-slate-300'); ?>">
                                <?php echo e($isActive ? 'نشط' : 'منتهي'); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="<?php echo e(route('admin.coupons.show', $coupon)); ?>" class="text-violet-600 dark:text-violet-400 hover:underline text-xs font-medium">عرض</a>
                                <a href="<?php echo e(route('admin.coupons.edit', $coupon)); ?>" class="text-slate-600 dark:text-slate-400 hover:underline text-xs font-medium">تعديل</a>
                                <form method="POST" action="<?php echo e(route('admin.coupons.destroy', $coupon)); ?>" class="inline" onsubmit="return confirm('حذف هذا الكوبون؟');">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-rose-600 dark:text-rose-400 hover:underline text-xs font-medium">حذف</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-700/20">
            <?php echo e($coupons->links()); ?>

        </div>
    </div>
    <?php else: ?>
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-12 text-center">
        <i class="fas fa-ticket-alt text-slate-300 dark:text-slate-600 text-5xl mb-4"></i>
        <p class="text-slate-600 dark:text-slate-400 font-medium">لا توجد كوبونات</p>
        <p class="text-sm text-slate-500 mt-1">أضف أول كوبون أو غيّر معايير البحث</p>
        <a href="<?php echo e(route('admin.coupons.create')); ?>" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-semibold text-sm">
            <i class="fas fa-plus"></i> إضافة كوبون
        </a>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\coupons\index.blade.php ENDPATH**/ ?>