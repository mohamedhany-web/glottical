<?php $__env->startSection('title', 'الاشتراكات'); ?>
<?php $__env->startSection('header', 'الاشتراكات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statusColors = [
        'active' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'expired' => 'bg-rose-100 text-rose-700 border-rose-200',
        'cancelled' => 'bg-amber-100 text-amber-700 border-amber-200',
    ];
    $statCards = [
        [
            'label' => 'إيراد الاشتراكات النشطة',
            'value' => number_format($stats['active_revenue'] ?? 0, 2) . ' ج.م',
            'icon' => 'fas fa-coins',
            'bg' => 'bg-blue-100',
            'text' => 'text-blue-600',
            'description' => 'قيمة الخطط المفعلة حالياً',
        ],
        [
            'label' => 'تجديد تلقائي',
            'value' => number_format($stats['auto_renew'] ?? 0),
            'icon' => 'fas fa-sync',
            'bg' => 'bg-emerald-100',
            'text' => 'text-emerald-600',
            'description' => 'اشتراكات محددة للتجديد التلقائي',
        ],
        [
            'label' => 'اشتراكات هذا الشهر',
            'value' => number_format($monthlyNew ?? 0),
            'icon' => 'fas fa-calendar-plus',
            'bg' => 'bg-violet-100',
            'text' => 'text-violet-600',
            'description' => 'تم تفعيلها منذ بداية الشهر',
        ],
        [
            'label' => 'إيراد الشهر الحالي',
            'value' => number_format($monthlyRevenue ?? 0, 2) . ' ج.م',
            'icon' => 'fas fa-chart-line',
            'bg' => 'bg-amber-100',
            'text' => 'text-amber-600',
            'description' => 'إجمالي قيمة الاشتراكات الجديدة هذا الشهر',
        ],
    ];
?>

<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm font-medium">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 text-sm font-medium">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-layer-group text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">لوحة الاشتراكات</h2>
                    <p class="text-sm text-slate-600 mt-1">راقب أداء الاشتراكات، الإيرادات المتجددة، وحالات التجديد التلقائي.</p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.subscriptions.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-500 rounded-xl shadow hover:from-blue-700 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                <i class="fas fa-plus"></i>
                إضافة اشتراك جديد
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-6">
            <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-slate-600 mb-1"><?php echo e($card['label']); ?></p>
                            <p class="text-2xl font-black text-slate-900"><?php echo e($card['value']); ?></p>
                        </div>
                        <div class="w-12 h-12 rounded-lg <?php echo e($card['bg']); ?> flex items-center justify-center <?php echo e($card['text']); ?> shadow-sm flex-shrink-0">
                            <i class="<?php echo e($card['icon']); ?> text-lg"></i>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600"><?php echo e($card['description']); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="px-6 pb-4 flex flex-wrap items-center gap-3 text-xs font-semibold">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                نشطة: <?php echo e(number_format($stats['active'] ?? 0)); ?>

            </span>
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-rose-100 text-rose-700 border border-rose-200">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                منتهية: <?php echo e(number_format($stats['expired'] ?? 0)); ?>

            </span>
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-100 text-amber-700 border border-amber-200">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                ملغاة: <?php echo e(number_format($stats['cancelled'] ?? 0)); ?>

            </span>
        </div>
    </section>

    
    <?php if(isset($pendingRequests) && $pendingRequests->count() > 0): ?>
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-amber-50">
                <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-clock text-amber-600"></i>
                    طلبات الاشتراك المعلقة (<?php echo e($pendingRequests->count()); ?>)
                </h3>
                <p class="text-xs text-slate-600 mt-1">مراجعة الطلبات وتفعيل الاشتراك للطالب ليظهر له القسم المدفوع في لوحته.</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    <?php $__currentLoopData = $pendingRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-xl border border-slate-200 bg-slate-50/50 hover:border-amber-200 hover:bg-white transition-all p-5">
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <div>
                                    <h4 class="font-bold text-slate-900"><?php echo e(htmlspecialchars($req->plan_name)); ?></h4>
                                    <p class="text-sm text-blue-600 mt-1"><?php echo e(htmlspecialchars($req->user->name ?? 'غير معروف')); ?></p>
                                    <p class="text-xs text-slate-500"><?php echo e($req->user->email ?? ''); ?> · <?php echo e($req->user->phone ?? ''); ?></p>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    معلق
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-sm text-slate-600 mb-2">
                                <span>السعر</span>
                                <span class="font-bold text-slate-900"><?php echo e(number_format($req->price, 0)); ?> ج.م</span>
                            </div>
                            <div class="flex items-center justify-between text-sm text-slate-600 mb-2">
                                <span>دورة الفوترة</span>
                                <span class="font-semibold text-slate-800"><?php echo e(htmlspecialchars(\App\Models\Subscription::billingCycleLabel($req->billing_cycle))); ?></span>
                            </div>
                            <?php if($req->payment_method): ?>
                                <div class="flex items-center justify-between text-sm text-slate-600 mb-2">
                                    <span>طريقة الدفع</span>
                                    <span class="font-semibold text-slate-800"><?php echo e($req->payment_method === 'wallet' ? 'محفظة إلكترونية' : 'تحويل بنكي'); ?><?php if($req->wallet): ?> — <?php echo e($req->wallet->name ?? \App\Models\Wallet::typeLabel($req->wallet->type)); ?><?php endif; ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if($req->payment_proof): ?>
                                <div class="mb-3">
                                    <a href="<?php echo e(asset('storage/' . $req->payment_proof)); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-xs font-semibold text-sky-600 hover:text-sky-800">
                                        <i class="fas fa-file-invoice"></i>
                                        عرض إيصال الدفع
                                    </a>
                                </div>
                            <?php endif; ?>
                            <p class="text-xs text-slate-500 mb-4">طلب <?php echo e(optional($req->created_at)->diffForHumans()); ?></p>
                            <div class="flex flex-wrap gap-2">
                                <form action="<?php echo e(route('admin.subscription-requests.approve', $req)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 transition-colors">
                                        <i class="fas fa-check"></i>
                                        تفعيل الاشتراك
                                    </button>
                                </form>
                                <form action="<?php echo e(route('admin.subscription-requests.reject', $req)); ?>" method="POST" class="inline" onsubmit="return confirm('هل تريد رفض هذا الطلب؟');">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 transition-colors">
                                        <i class="fas fa-times"></i>
                                        رفض
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">توزيع الاشتراكات حسب النوع</h3>
                <p class="text-xs text-slate-600 mt-1"><?php echo e($planDistribution->sum('subscriptions_count')); ?> إجمالي</p>
            </div>
            <div class="p-6 space-y-4">
                <?php $__empty_1 = true; $__currentLoopData = $planDistribution; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $distribution): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-blue-100 text-blue-600">
                                <i class="fas fa-tags text-sm"></i>
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-slate-900"><?php echo e(htmlspecialchars($distribution['label'])); ?></p>
                                <p class="text-xs text-slate-500"><?php echo e(number_format($distribution['subscriptions_count'])); ?> اشتراك</p>
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-blue-600"><?php echo e(number_format($distribution['total_price'], 2)); ?> ج.م</p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-sm text-slate-500">لا توجد بيانات كافية حالياً.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-hourglass-half text-rose-500 text-sm"></i>
                    اشتراكات يقترب انتهاءها
                </h3>
                <p class="text-xs text-slate-600 mt-1">خلال 30 يوم</p>
            </div>
            <div class="p-6 space-y-3">
                <?php $__empty_1 = true; $__currentLoopData = $expiringSoon; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $upcoming): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="rounded-lg border border-slate-200 bg-slate-50/50 p-4">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-slate-900"><?php echo e($upcoming->plan_name); ?></p>
                            <span class="text-xs text-slate-500"><?php echo e(optional($upcoming->end_date)->diffForHumans()); ?></span>
                        </div>
                        <p class="text-xs text-blue-600 mt-1"><?php echo e($upcoming->user->name ?? 'غير معروف'); ?></p>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-sm font-semibold text-slate-900"><?php echo e(number_format($upcoming->price, 2)); ?> ج.م</span>
                            <a href="<?php echo e(route('admin.subscriptions.show', $upcoming)); ?>" class="text-xs font-semibold text-blue-600 hover:text-blue-800">تفاصيل <i class="fas fa-arrow-left text-[10px]"></i></a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-sm text-slate-500">لا توجد اشتراكات على وشك الانتهاء.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h3 class="text-base font-black text-slate-900">أحدث الاشتراكات</h3>
            </div>
            <div class="p-6 space-y-3">
                <?php $__empty_1 = true; $__currentLoopData = $recentSubscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="rounded-lg border border-slate-200 bg-slate-50/50 p-4">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-slate-900"><?php echo e(htmlspecialchars($recent->plan_name)); ?></p>
                            <span class="text-xs text-slate-500"><?php echo e(optional($recent->created_at)->diffForHumans()); ?></span>
                        </div>
                        <p class="text-xs text-blue-600 mt-1"><?php echo e(htmlspecialchars($recent->user->name ?? 'غير مرتبط')); ?></p>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-sm font-semibold text-slate-900"><?php echo e(number_format($recent->price, 2)); ?> ج.م</span>
                            <a href="<?php echo e(route('admin.subscriptions.show', $recent)); ?>" class="text-xs font-semibold text-blue-600 hover:text-blue-800">عرض <i class="fas fa-arrow-left text-[10px]"></i></a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-sm text-slate-500">لا توجد اشتراكات حديثة.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>

    
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-slate-900">قائمة الاشتراكات</h3>
                <p class="text-xs text-slate-600 mt-1">كل الاشتراكات الحالية مع تفاصيل المستخدم والحالة.</p>
            </div>
            <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1.5 rounded-lg border border-blue-200"><?php echo e($subscriptions->total()); ?> اشتراك</span>
        </div>
        <div class="overflow-x-auto">
            <div class="p-4 space-y-3">
                <?php $__empty_1 = true; $__currentLoopData = $subscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subscription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="rounded-lg border border-slate-200 bg-slate-50/50 hover:border-blue-200 hover:bg-white transition-all duration-200 overflow-hidden">
                        <div class="px-4 py-3">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center rounded-lg
                                    <?php if($subscription->status === 'active'): ?> bg-emerald-100 text-emerald-600
                                    <?php elseif($subscription->status === 'expired'): ?> bg-rose-100 text-rose-600
                                    <?php else: ?> bg-amber-100 text-amber-600
                                    <?php endif; ?>">
                                    <i class="fas fa-<?php echo e($subscription->status === 'active' ? 'check-circle' : ($subscription->status === 'expired' ? 'clock' : 'ban')); ?> text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0 space-y-1">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <div>
                                            <p class="text-sm font-bold text-slate-900"><?php echo e(htmlspecialchars($subscription->plan_name)); ?></p>
                                            <p class="text-xs text-slate-600"><?php echo e(htmlspecialchars($subscription->user->name ?? '—')); ?> · <?php echo e(htmlspecialchars($subscription->user->phone ?? '—')); ?></p>
                                        </div>
                                        <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-semibold border <?php echo e($statusColors[$subscription->status] ?? 'bg-slate-100 text-slate-700 border-slate-200'); ?>">
                                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                            <?php echo e($subscription->status === 'active' ? 'نشط' : ($subscription->status === 'expired' ? 'منتهي' : 'ملغي')); ?>

                                        </span>
                                    </div>
                                    <div class="flex flex-wrap gap-x-4 gap-y-0.5 text-xs text-slate-600">
                                        <span><?php echo e(htmlspecialchars(\App\Models\Subscription::typeLabel($subscription->subscription_type))); ?></span>
                                        <span><strong><?php echo e(number_format($subscription->price, 2)); ?></strong> ج.م</span>
                                        <span title="مدة الباقة"><?php echo e(htmlspecialchars(\App\Models\Subscription::getDurationLabel($subscription->billing_cycle))); ?></span>
                                        <span>تفعيل: <?php echo e($subscription->start_date?->format('Y-m-d')); ?></span>
                                        <span>ينتهي: <?php echo e($subscription->end_date?->format('Y-m-d') ?? '—'); ?></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <a href="<?php echo e(route('admin.subscriptions.show', $subscription)); ?>" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-300 bg-white text-blue-600 hover:bg-blue-50 hover:border-blue-400 transition-colors text-sm" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('admin.subscriptions.edit', $subscription)); ?>" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-600 hover:bg-slate-50 transition-colors text-sm" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?php echo e(route('admin.subscriptions.destroy', $subscription)); ?>" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الاشتراك؟');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 bg-white text-rose-600 hover:bg-rose-50 transition-colors text-sm" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="rounded-xl border border-slate-200 bg-white p-12 text-center">
                        <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                            <i class="fas fa-layer-group text-3xl"></i>
                        </div>
                        <p class="text-lg font-bold text-slate-900 mb-1">لا توجد اشتراكات</p>
                        <p class="text-sm text-slate-600 mb-4">لم يتم إنشاء أي اشتراكات بعد.</p>
                        <a href="<?php echo e(route('admin.subscriptions.create')); ?>" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 px-4 py-2.5 text-sm font-semibold text-white shadow hover:from-blue-700 hover:to-blue-600 transition-all duration-200">
                            <i class="fas fa-plus"></i>
                            إضافة اشتراك جديد
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if($subscriptions->hasPages()): ?>
                <div class="border-t border-slate-200 px-6 py-4">
                    <?php echo e($subscriptions->withQueryString()->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\subscriptions\index.blade.php ENDPATH**/ ?>