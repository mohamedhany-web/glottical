<?php $__env->startSection('title', 'التحقق من الشهادة - ' . config('app.name')); ?>
<?php $__env->startSection('meta_description', 'تحقق من صحة شهادات ' . config('app.name') . ' عبر رمز التحقق أو الرقم التسلسلي.'); ?>
<?php $__env->startSection('meta_keywords', 'تحقق شهادة, ' . config('app.name') . ', شهادات'); ?>
<?php $__env->startSection('canonical_url', url()->current()); ?>

<?php $__env->startPush('styles'); ?>
    <?php echo $__env->make('components.certificate-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<section class="hero-gradient min-h-[32vh] flex items-center relative overflow-hidden pt-28 pb-10" style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.85) 25%, rgba(14, 165, 233, 0.7) 50%, rgba(14, 165, 233, 0.75) 75%, rgba(2, 132, 199, 0.8) 100%);">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <h1 class="text-3xl md:text-4xl font-black text-white mb-2" style="text-shadow: 0 4px 16px rgba(0,0,0,0.8), 0 0 12px rgba(14, 165, 233, 0.4);">
            التحقق من الشهادة
        </h1>
        <p class="text-white/95 text-sm md:text-base max-w-xl mx-auto">
            أدخل رمز التحقق أو السيريال للتحقق من صحة الشهادة الصادرة عن <?php echo e(config('app.name')); ?>

        </p>
    </div>
</section>

<div class="py-10 md:py-14 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl p-6 md:p-8 mb-8 border border-slate-100 dark:border-slate-700">
            <form method="GET" action="<?php echo e(route('public.certificates.verify')); ?>" class="flex flex-col sm:flex-row gap-4">
                <input type="text"
                       name="code"
                       value="<?php echo e(request('code')); ?>"
                       placeholder="أدخل رمز التحقق أو السيريال"
                       class="flex-1 px-6 py-4 border-2 border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-lg outline-none transition-colors">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-sky-600 to-cyan-600 hover:from-sky-700 hover:to-cyan-700 text-white px-8 py-4 rounded-xl font-semibold transition-all duration-300 shadow-lg shadow-sky-500/25 hover:shadow-xl whitespace-nowrap">
                    <i class="fas fa-search ml-2"></i>
                    التحقق
                </button>
            </form>
        </div>

        <?php if(isset($certificate)): ?>
            <?php if($certificate && $isValid): ?>
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl overflow-hidden border-2 border-emerald-500">
                    <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 p-6 text-white">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-3xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-black">شهادة صحيحة ومعتمدة</h2>
                                <p class="text-emerald-100">تم التحقق من صحة هذه الشهادة</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">معلومات المعلم</h3>
                                <div class="space-y-2 text-sm">
                                    <div><span class="text-gray-600 dark:text-slate-400">الاسم:</span> <span class="font-semibold text-gray-900 dark:text-white"><?php echo e($certificate->user->name ?? 'غير معروف'); ?></span></div>
                                    <div><span class="text-gray-600 dark:text-slate-400">البريد:</span> <span class="font-semibold text-gray-900 dark:text-white"><?php echo e($certificate->user->email ?? '-'); ?></span></div>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">معلومات الشهادة</h3>
                                <div class="space-y-2 text-sm">
                                    <div><span class="text-gray-600 dark:text-slate-400">رقم الشهادة:</span> <span class="font-semibold text-gray-900 dark:text-white font-mono"><?php echo e($certificate->certificate_number); ?></span></div>
                                    <?php if($certificate->serial_number): ?>
                                    <div><span class="text-gray-600 dark:text-slate-400">السيريال:</span> <span class="font-semibold text-gray-900 dark:text-white font-mono"><?php echo e($certificate->serial_number); ?></span></div>
                                    <?php endif; ?>
                                    <div><span class="text-gray-600 dark:text-slate-400">تاريخ الإصدار:</span> <span class="font-semibold text-gray-900 dark:text-white"><?php echo e($certificate->issued_at ? $certificate->issued_at->format('Y-m-d') : '-'); ?></span></div>
                                    <div><span class="text-gray-600 dark:text-slate-400">الحالة:</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-200">
                                            مُصدرة ومعتمدة
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 dark:border-slate-600 pt-8">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">معاينة الشهادة</h3>
                            <div class="certificate-container">
                                <?php echo $__env->make('components.certificate-templates', [
                                    'certificate' => $certificate,
                                    'template' => $certificate->template ?? 'classic'
                                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl p-8 border-2 border-red-500">
                    <div class="text-center">
                        <div class="w-20 h-20 bg-red-100 dark:bg-red-950/50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-times-circle text-red-600 dark:text-red-400 text-4xl"></i>
                        </div>
                        <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-2">شهادة غير صحيحة</h2>
                        <p class="text-red-600 dark:text-red-400 font-semibold"><?php echo e($error ?? 'تم اكتشاف تلاعب في الشهادة أو الشهادة غير موجودة'); ?></p>
                    </div>
                </div>
            <?php endif; ?>
        <?php elseif(isset($error)): ?>
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl p-8 border-2 border-amber-500">
                <div class="text-center">
                    <div class="w-20 h-20 bg-amber-100 dark:bg-amber-950/50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-amber-600 dark:text-amber-400 text-4xl"></i>
                    </div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-2">تنبيه</h2>
                    <p class="text-amber-700 dark:text-amber-300 font-semibold"><?php echo e($error); ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\public\certificates\verify.blade.php ENDPATH**/ ?>