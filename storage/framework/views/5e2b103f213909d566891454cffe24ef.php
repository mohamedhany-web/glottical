<?php $__env->startSection('title', 'إرسال بريد — '.$audienceLabel); ?>
<?php $__env->startSection('header', 'إشعارات البريد (Gmail) — إرسال جديد'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="<?php echo e(route('admin.email-broadcasts.index', $audience)); ?>" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            <i class="fas fa-arrow-right ml-1"></i> رجوع
        </a>
        <span class="text-xs font-bold px-3 py-1 rounded-full bg-slate-100 text-slate-700">الفئة: <?php echo e($audienceLabel); ?></span>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="<?php echo e(route('admin.email-broadcasts.store', $audience)); ?>" class="space-y-4" x-data="{ mode: <?php echo e(json_encode(old('mode', 'audience'))); ?> }">
            <?php echo csrf_field(); ?>
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                <p class="font-bold text-slate-900 mb-2">نوع الإرسال</p>
                <label class="inline-flex items-center gap-2 ml-4">
                    <input type="radio" name="mode" value="audience" x-model="mode">
                    <span>إرسال للجمهور (<?php echo e($audienceLabel); ?>)</span>
                </label>
                <label class="inline-flex items-center gap-2">
                    <input type="radio" name="mode" value="single_email" x-model="mode">
                    <span>إرسال لإيميل محدد</span>
                </label>
                <?php $__errorArgs = ['mode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div x-show="mode === 'single_email'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">الإيميل *</label>
                    <input type="email" name="single_email" value="<?php echo e(old('single_email')); ?>" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm" placeholder="example@gmail.com">
                    <?php $__errorArgs = ['single_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">اسم (اختياري)</label>
                    <input type="text" name="single_name" value="<?php echo e(old('single_name')); ?>" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm">
                    <?php $__errorArgs = ['single_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">عنوان الرسالة (Subject) *</label>
                <input type="text" name="subject" value="<?php echo e(old('subject')); ?>" required maxlength="255"
                       class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm">
                <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">نص الرسالة *</label>
                <textarea name="body" rows="10" required maxlength="20000"
                          class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm leading-6"><?php echo e(old('body')); ?></textarea>
                <p class="text-xs text-slate-500 mt-1">سيتم إرسالها كبريد من Gmail حسب إعدادات `.env`.</p>
                <?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 text-sm text-amber-900" x-show="mode === 'audience'" x-cloak>
                سيتم إرسال هذه الرسالة إلى <strong><?php echo e($audienceLabel); ?></strong> (كل المستخدمين النشطين الذين لديهم بريد).
            </div>
            <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 text-sm text-amber-900" x-show="mode === 'single_email'" x-cloak>
                سيتم إرسال هذه الرسالة إلى <strong>إيميل واحد</strong> فقط (المحدد أعلاه).
            </div>

            <button type="submit" class="px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold">
                <i class="fas fa-paper-plane ml-2"></i> بدء الإرسال
            </button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\email-broadcasts\create.blade.php ENDPATH**/ ?>