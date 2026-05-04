<?php $__env->startSection('title', 'اشتراكي - الباقة والمدة'); ?>
<?php $__env->startSection('header', 'اشتراكي'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <?php
        $sub = $subscription;
        $durationLabel = \App\Models\Subscription::getDurationLabel($sub->billing_cycle);
        $planRank = ['teacher_starter' => 1, 'teacher_pro' => 2];
        $currentKey = (string) ($sub->teacher_plan_key ?? '');
        $currentRank = $planRank[$currentKey] ?? 0;
        $upgradeOptions = collect(['teacher_starter', 'teacher_pro'])
            ->filter(fn ($k) => ($planRank[$k] ?? 0) > $currentRank)
            ->values();
    ?>

    
    <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-lg overflow-hidden">
        <div class="bg-gradient-to-l from-sky-50 via-white to-white dark:from-slate-800/90 dark:via-slate-800/95 dark:to-slate-900/95 p-6 border-b border-slate-100 dark:border-slate-700">
            <div class="flex flex-wrap items-center gap-3 mb-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-slate-100"><?php echo e($sub->plan_name); ?></h1>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    نشط
                </span>
            </div>
            <p class="text-sm text-slate-600 dark:text-slate-400">مدة الباقة: <strong><?php echo e($durationLabel); ?></strong> · ينتهي عند انتهاء المدة</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50/50 dark:bg-slate-700/40">
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">تاريخ التفعيل</p>
                    <p class="mt-1 text-base font-bold text-slate-900 dark:text-slate-100"><?php echo e($sub->start_date?->format('Y-m-d') ?? '—'); ?></p>
                </div>
                <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50/50 dark:bg-slate-700/40">
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">مدة الباقة</p>
                    <p class="mt-1 text-base font-bold text-slate-900 dark:text-slate-100"><?php echo e($durationLabel); ?></p>
                </div>
                <div class="p-4 rounded-xl border border-rose-100 dark:border-rose-900/50 bg-rose-50/50 dark:bg-rose-950/30">
                    <p class="text-xs font-semibold text-rose-600 dark:text-rose-400 uppercase tracking-wide">ينتهي في</p>
                    <p class="mt-1 text-base font-bold text-slate-900 dark:text-slate-100"><?php echo e($sub->end_date?->format('Y-m-d') ?? '—'); ?></p>
                </div>
            </div>
        </div>
    </div>

    
    <?php $displayFeatures = \App\Models\Subscription::normalizeFeatureKeys($sub->features ?? []); ?>
    <?php if(count($displayFeatures) > 0): ?>
        <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm p-6">
            <h2 class="text-lg font-black text-slate-900 dark:text-slate-100 mb-4">المزايا المتاحة في باقتك</h2>
            <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <?php $__currentLoopData = $displayFeatures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $featureKey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                        <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check text-[10px]"></i>
                        </span>
                        <?php echo e(__("student.subscription_feature.{$featureKey}")); ?>

                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <?php if($upgradeOptions->count() > 0): ?>
        <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm p-6">
            <h2 class="text-lg font-black text-slate-900 dark:text-slate-100 mb-2">ترقية الباقة</h2>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                يمكنك الترقية إلى باقة أعلى. سيتم إرسال طلب ترقية للمراجعة (مثل الاشتراك) وبعد الموافقة يتم تفعيل الباقة الأعلى.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <?php $__currentLoopData = $upgradeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $planKey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('public.subscription.checkout', $planKey) . '?upgrade=1&from=' . $sub->id); ?>"
                       class="group p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-sky-300 dark:hover:border-sky-700 hover:bg-sky-50/60 dark:hover:bg-slate-700/40 transition">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="text-sm font-black text-slate-900 dark:text-slate-100">
                                    <?php echo e($planKey === 'teacher_pro' ? 'الباقة الشاملة' : 'الباقة الأساسية'); ?>

                                </div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">ترقية إلى مستوى أعلى</div>
                            </div>
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-sky-600 text-white group-hover:bg-sky-700 transition">
                                <i class="fas fa-arrow-up text-xs"></i>
                            </span>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>

    <p class="text-sm text-slate-500 dark:text-slate-400">
        عند انتهاء المدة سيُغلق الاشتراك تلقائياً ولن تظهر لك روابط القسم المدفوع حتى تجدد.
        <a href="<?php echo e(route('public.pricing')); ?>" class="text-sky-600 dark:text-sky-400 font-semibold hover:underline">عرض الباقات والتجديد</a>
    </p>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\student\my-subscription.blade.php ENDPATH**/ ?>