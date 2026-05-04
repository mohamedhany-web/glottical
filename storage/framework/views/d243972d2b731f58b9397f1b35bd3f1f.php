

<?php $__env->startSection('title', 'استهلاك المشترك — ' . ($user->name ?? 'المشترك')); ?>
<?php $__env->startSection('header', 'استهلاك المشترك (رقابة كاملة)'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <nav class="text-sm text-slate-500">
        <a href="<?php echo e(route('admin.subscriptions.index')); ?>" class="text-sky-600 hover:text-sky-700">الاشتراكات</a>
        <span class="mx-1">/</span>
        <a href="<?php echo e(route('admin.subscriptions.show', $subscription)); ?>" class="text-sky-600 hover:text-sky-700">تفاصيل الاشتراك</a>
        <span class="mx-1">/</span>
        <span class="text-slate-700">استهلاك المشترك</span>
    </nav>

    
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-chart-pie text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900">استهلاك المشترك (المعلم)</h1>
                    <p class="text-sm text-slate-600 mt-0.5"><?php echo e($user->name); ?> — خطة <?php echo e($subscription->plan_name); ?></p>
                    <p class="text-xs text-slate-500 mt-1"><?php echo e($user->phone ?? $user->email); ?> · <?php echo e($subscription->status === 'active' ? 'اشتراك نشط' : ($subscription->status === 'expired' ? 'منتهي' : 'ملغي')); ?></p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('admin.subscriptions.show', $subscription)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 font-semibold text-sm hover:bg-slate-50">
                    <i class="fas fa-arrow-right"></i>
                    تفاصيل الاشتراك
                </a>
                <a href="<?php echo e(route('admin.users.show', $user->id)); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-sky-600 text-white font-semibold text-sm hover:bg-sky-700">
                    <i class="fas fa-user"></i>
                    بيانات المستخدم
                </a>
            </div>
        </div>
    </div>

    
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-layer-group text-amber-500"></i>
                الخطة والمزايا المفعّلة
            </h2>
        </div>
        <div class="p-6">
            <div class="flex flex-wrap gap-2 mb-4">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 text-sm font-medium">
                    <?php echo e($subscription->plan_name); ?> · <?php echo e(\App\Models\Subscription::billingCycleLabel($subscription->billing_cycle)); ?>

                </span>
                <span class="text-slate-500 text-sm">من <?php echo e($subscription->start_date?->format('Y-m-d')); ?> إلى <?php echo e($subscription->end_date?->format('Y-m-d')); ?></span>
            </div>
            <?php if(count($subscriptionFeatures) > 0): ?>
            <p class="text-xs text-slate-500 mb-2">مزايا الباقة المستفاد منها:</p>
            <ul class="flex flex-wrap gap-2">
                <?php $__currentLoopData = $subscriptionFeatures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $cfg = $featuresConfig[$key] ?? null; ?>
                    <li class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-sm font-medium">
                        <?php if($cfg && isset($cfg['icon'])): ?><i class="fas <?php echo e($cfg['icon']); ?> text-xs"></i><?php endif; ?>
                        <?php echo e($key); ?>

                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <?php else: ?>
            <p class="text-sm text-slate-500">لا توجد مزايا محددة لهذه الخطة أو لم تُضف بعد.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-book-open text-indigo-500"></i>
                    التسجيلات في الكورسات
                </h2>
                <span class="text-sm font-semibold text-slate-600"><?php echo e($enrollments->count()); ?></span>
            </div>
            <div class="p-4 max-h-80 overflow-y-auto space-y-2">
                <?php $__empty_1 = true; $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50/50 px-4 py-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate"><?php echo e($e->course->title ?? '—'); ?></p>
                        <p class="text-xs text-slate-500"><?php echo e($e->course->academicSubject->name ?? '—'); ?> · <?php echo e(optional($e->created_at)->format('Y-m-d')); ?></p>
                    </div>
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold
                        <?php if($e->status === 'active'): ?> bg-emerald-100 text-emerald-700
                        <?php elseif($e->status === 'completed'): ?> bg-sky-100 text-sky-700
                        <?php else: ?> bg-slate-100 text-slate-600 <?php endif; ?>">
                        <?php echo e($e->status === 'active' ? 'نشط' : ($e->status === 'completed' ? 'مكتمل' : $e->status)); ?>

                    </span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-slate-500 py-6 text-center">لا توجد تسجيلات في كورسات حتى الآن.</p>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-clipboard-check text-violet-500"></i>
                    محاولات الامتحانات
                </h2>
                <span class="text-sm font-semibold text-slate-600"><?php echo e($examAttempts->count()); ?></span>
            </div>
            <div class="p-4 max-h-80 overflow-y-auto space-y-2">
                <?php $__empty_1 = true; $__currentLoopData = $examAttempts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50/50 px-4 py-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate"><?php echo e($a->exam->title ?? '—'); ?></p>
                        <p class="text-xs text-slate-500"><?php echo e(optional($a->created_at)->format('Y-m-d H:i')); ?></p>
                    </div>
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold
                        <?php echo e($a->score >= 80 ? 'bg-emerald-100 text-emerald-700' : ($a->score >= 60 ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700')); ?>">
                        <?php echo e($a->score ?? '—'); ?>%
                    </span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-slate-500 py-6 text-center">لا توجد محاولات امتحانات.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-shopping-bag text-emerald-500"></i>
                طلبات الشراء (كورسات)
            </h2>
            <span class="text-sm font-semibold text-slate-600"><?php echo e($orders->count()); ?> طلب</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase">الكورس</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase">المبلغ</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-6 py-3 text-sm font-medium text-slate-800"><?php echo e($o->course->title ?? '—'); ?></td>
                        <td class="px-6 py-3 text-sm text-slate-600"><?php echo e(number_format($o->amount ?? 0, 2)); ?> ج.م</td>
                        <td class="px-6 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                                <?php if($o->status === 'approved'): ?> bg-emerald-100 text-emerald-700
                                <?php elseif($o->status === 'pending'): ?> bg-amber-100 text-amber-700
                                <?php else: ?> bg-rose-100 text-rose-700 <?php endif; ?>">
                                <?php echo e($o->status === 'approved' ? 'معتمد' : ($o->status === 'pending' ? 'معلق' : 'مرفوض')); ?>

                            </span>
                        </td>
                        <td class="px-6 py-3 text-xs text-slate-500"><?php echo e(optional($o->created_at)->format('Y-m-d')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500">لا توجد طلبات.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-history text-slate-500"></i>
                آخر النشاطات في النظام
            </h2>
        </div>
        <div class="p-4 max-h-96 overflow-y-auto space-y-2">
            <?php $__empty_1 = true; $__currentLoopData = $activityLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex items-start gap-3 rounded-xl border border-slate-100 px-4 py-3 hover:bg-slate-50/50">
                <span class="inline-flex w-8 h-8 rounded-lg bg-slate-100 items-center justify-center text-slate-500 flex-shrink-0">
                    <i class="fas fa-circle-notch text-xs"></i>
                </span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-slate-800"><?php echo e($log->action); ?></p>
                    <p class="text-xs text-slate-500"><?php echo e(optional($log->created_at)->diffForHumans()); ?> · <?php echo e(optional($log->created_at)->format('Y-m-d H:i')); ?></p>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-sm text-slate-500 py-8 text-center">لا يوجد سجل نشاط.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\subscriptions\consumption.blade.php ENDPATH**/ ?>