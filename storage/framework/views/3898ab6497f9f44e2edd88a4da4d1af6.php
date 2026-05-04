

<?php $__env->startSection('title', 'تأكيد تفعيل المصادقة الثنائية'); ?>
<?php $__env->startSection('header', 'تأكيد تفعيل المصادقة الثنائية'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-2xl mx-auto space-y-6 pb-10">
    <section class="rounded-2xl border border-slate-200 dark:border-slate-600 bg-gradient-to-br from-slate-50 via-white to-violet-50/40 dark:from-slate-800 dark:via-slate-800 dark:to-slate-900 p-6 sm:p-8 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-start gap-5">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-violet-500/25 shrink-0">
                <i class="fas fa-envelope-open-text text-2xl"></i>
            </div>
            <div class="flex-1 min-w-0 space-y-2">
                <h2 class="text-xl font-black text-slate-900 dark:text-slate-100">التحقق عبر البريد</h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-7">
                    لتفعيل <strong>إلزام المصادقة الثنائية لحسابات الأدمن</strong>، أدخل الرمز المكوّن من 6 أرقام الذي أُرسل إلى بريدك.
                </p>
                <?php if($userEmail): ?>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400" dir="ltr"><?php echo e($userEmail); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php if(session('success')): ?>
        <div class="flex items-center gap-3 px-5 py-3 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm font-semibold">
            <i class="fas fa-check-circle"></i>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-2xl text-rose-800 dark:text-rose-200 text-sm">
            <ul class="list-disc list-inside space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($err); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 shadow-lg overflow-hidden p-6 sm:p-8 space-y-6">
        <form method="post" action="<?php echo e(route('admin.system-settings.two-factor.confirm.submit')); ?>" class="space-y-5">
            <?php echo csrf_field(); ?>
            <div>
                <label for="code" class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-2">رمز التحقق</label>
                <input type="text" name="code" id="code" value="<?php echo e(old('code')); ?>" required maxlength="10" autocomplete="one-time-code" inputmode="numeric"
                       class="w-full max-w-xs px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-center text-2xl tracking-[0.4em] font-black text-slate-900 dark:text-slate-100 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 outline-none"
                       placeholder="000000" dir="ltr">
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">الرمز صالح لمدة 15 دقيقة.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-sm font-black shadow-lg shadow-violet-500/25 hover:from-violet-700 hover:to-indigo-700 transition-colors">
                    <i class="fas fa-check"></i>
                    تأكيد التفعيل
                </button>
                <a href="<?php echo e(route('admin.system-settings.edit')); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-sm font-bold hover:bg-slate-50 dark:hover:bg-slate-700">
                    إلغاء والعودة
                </a>
            </div>
        </form>

        <div class="pt-4 border-t border-slate-100 dark:border-slate-600">
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">لم يصلك الرمز؟</p>
            <form method="post" action="<?php echo e(route('admin.system-settings.two-factor.resend')); ?>" class="inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="text-sm font-bold text-violet-600 dark:text-violet-400 hover:text-violet-800 dark:hover:text-violet-300">
                    <i class="fas fa-redo ml-1"></i>
                    إعادة إرسال الرمز
                </button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\system-settings\two-factor-confirm.blade.php ENDPATH**/ ?>