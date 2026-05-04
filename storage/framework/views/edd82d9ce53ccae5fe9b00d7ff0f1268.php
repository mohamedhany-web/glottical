

<?php $__env->startSection('title', __('instructor.transfer_account') . ' - ' . config('app.name', 'Muallimx')); ?>
<?php $__env->startSection('header', __('instructor.transfer_account')); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    <section class="rounded-2xl bg-white dark:bg-slate-800/95/95 backdrop-blur border-2 border-slate-200 dark:border-slate-700/50 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-indigo-50 to-white">
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-slate-50 flex items-center gap-2">
                <i class="fas fa-university text-indigo-600"></i>
                <?php echo e(__('instructor.transfer_account')); ?>

            </h2>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-2"><?php echo e(__('instructor.transfer_account_desc')); ?></p>
        </div>

        <?php if(session('success')): ?>
        <div class="mx-5 mt-5 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">
            <i class="fas fa-check-circle mr-2"></i><?php echo e(session('success')); ?>

        </div>
        <?php endif; ?>

        <form action="<?php echo e(route('instructor.transfer-account.store')); ?>" method="POST" class="p-5 sm:p-8 space-y-5">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="bank_name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1"><?php echo e(__('instructor.bank_name')); ?></label>
                    <input type="text" name="bank_name" id="bank_name" value="<?php echo e(old('bank_name', $detail->bank_name)); ?>"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="<?php echo e(__('instructor.placeholder_bank_example')); ?>">
                    <?php $__errorArgs = ['bank_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label for="account_holder_name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1"><?php echo e(__('instructor.account_holder_name')); ?></label>
                    <input type="text" name="account_holder_name" id="account_holder_name" value="<?php echo e(old('account_holder_name', $detail->account_holder_name)); ?>"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="<?php echo e(__('instructor.placeholder_name_on_card')); ?>">
                    <?php $__errorArgs = ['account_holder_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label for="account_number" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1"><?php echo e(__('instructor.account_number')); ?></label>
                    <input type="text" name="account_number" id="account_number" value="<?php echo e(old('account_number', $detail->account_number)); ?>" dir="ltr"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="<?php echo e(__('instructor.placeholder_account_number')); ?>">
                    <?php $__errorArgs = ['account_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label for="iban" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1"><?php echo e(__('instructor.iban')); ?></label>
                    <input type="text" name="iban" id="iban" value="<?php echo e(old('iban', $detail->iban)); ?>" dir="ltr"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="EG...">
                    <?php $__errorArgs = ['iban'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label for="branch_name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1"><?php echo e(__('instructor.branch_name')); ?></label>
                    <input type="text" name="branch_name" id="branch_name" value="<?php echo e(old('branch_name', $detail->branch_name)); ?>"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="<?php echo e(__('instructor.placeholder_branch_optional')); ?>">
                    <?php $__errorArgs = ['branch_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label for="swift_code" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1"><?php echo e(__('instructor.swift_code')); ?></label>
                    <input type="text" name="swift_code" id="swift_code" value="<?php echo e(old('swift_code', $detail->swift_code)); ?>" dir="ltr"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="<?php echo e(__('instructor.placeholder_optional')); ?>">
                    <?php $__errorArgs = ['swift_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
            <div>
                <label for="notes" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1"><?php echo e(__('instructor.notes')); ?></label>
                <textarea name="notes" id="notes" rows="2" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="<?php echo e(__('instructor.placeholder_extra_transfer')); ?>"><?php echo e(old('notes', $detail->notes)); ?></textarea>
                <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="pt-4">
                <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-colors">
                    <i class="fas fa-save mr-2"></i><?php echo e(__('instructor.save_transfer_data')); ?>

                </button>
            </div>
        </form>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\instructor\transfer-account\index.blade.php ENDPATH**/ ?>