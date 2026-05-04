

<?php $__env->startSection('title', 'طلب سحب جديد - ' . config('app.name', 'Muallimx')); ?>
<?php $__env->startSection('header', 'طلب سحب جديد'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-8">
        <h2 class="text-lg font-black text-slate-800 dark:text-slate-100 mb-4 flex items-center gap-2">
            <i class="fas fa-chart-pie text-amber-600"></i>
            إجمالي الماليات
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-emerald-100 text-xs font-semibold mb-1">إجمالي المكتسب</p>
                        <p class="text-2xl font-black"><?php echo e(number_format($stats['total_earned'], 2)); ?> ج.م</p>
                    </div>
                    <div class="w-12 h-12 bg-white dark:bg-slate-800/95/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-5 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-xs font-semibold mb-1">إجمالي المسحوب</p>
                        <p class="text-2xl font-black"><?php echo e(number_format($stats['total_withdrawn'], 2)); ?> ج.م</p>
                    </div>
                    <div class="w-12 h-12 bg-white dark:bg-slate-800/95/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-arrow-down text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-5 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-amber-100 text-xs font-semibold mb-1">طلبات سحب قيد الانتظار</p>
                        <p class="text-2xl font-black"><?php echo e(number_format($stats['pending_withdrawals'], 2)); ?> ج.م</p>
                    </div>
                    <div class="w-12 h-12 bg-white dark:bg-slate-800/95/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl p-5 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-xs font-semibold mb-1">المتاح للسحب</p>
                        <p class="text-2xl font-black"><?php echo e(number_format($stats['available_amount'], 2)); ?> ج.م</p>
                    </div>
                    <div class="w-12 h-12 bg-white dark:bg-slate-800/95/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-wallet text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800/95 rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-amber-50 to-white">
            <h2 class="text-xl font-black text-gray-900 flex items-center gap-3">
                <i class="fas fa-plus-circle text-amber-600"></i>
                تقديم طلب سحب جديد
            </h2>
            <p class="text-sm text-gray-600 mt-1">المبلغ المتاح للسحب: <span class="font-bold text-amber-700"><?php echo e(number_format($stats['available_amount'], 2)); ?> ج.م</span></p>
        </div>

        <?php if(session('error')): ?>
            <div class="mx-6 mt-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 rounded-xl text-red-700 text-sm">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <?php if($stats['available_amount'] <= 0): ?>
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-wallet text-amber-600 text-2xl"></i>
                </div>
                <p class="font-bold text-gray-900">لا يوجد مبلغ متاح للسحب حالياً.</p>
                <p class="text-sm text-gray-600 mt-1">بعد استلام المبالغ الموافق عليها من الإدارة ستظهر هنا.</p>
                <a href="<?php echo e(route('instructor.withdrawals.index')); ?>" class="inline-flex items-center gap-2 mt-6 px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold">
                    <i class="fas fa-arrow-right"></i>
                    العودة لطلبات السحب
                </a>
            </div>
        <?php else: ?>
            <form action="<?php echo e(route('instructor.withdrawals.store')); ?>" method="POST" class="p-6 md:p-8 space-y-6">
                <?php echo csrf_field(); ?>

                <div>
                    <label for="amount" class="block text-sm font-bold text-gray-700 mb-2">المبلغ المطلوب (ج.م) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" id="amount" value="<?php echo e(old('amount')); ?>" min="0.01" step="0.01" max="<?php echo e($stats['available_amount']); ?>" required
                           class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                           placeholder="0.00">
                    <p class="mt-1 text-xs text-gray-500">الحد الأقصى: <?php echo e(number_format($stats['available_amount'], 2)); ?> ج.م</p>
                    <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label for="payment_method" class="block text-sm font-bold text-gray-700 mb-2">طريقة الاستلام <span class="text-red-500">*</span></label>
                    <select name="payment_method" id="payment_method" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <option value="">اختر طريقة الاستلام</option>
                        <option value="bank_transfer" <?php echo e(old('payment_method') == 'bank_transfer' ? 'selected' : ''); ?>>تحويل بنكي</option>
                        <option value="wallet" <?php echo e(old('payment_method') == 'wallet' ? 'selected' : ''); ?>>محفظة</option>
                        <option value="cash" <?php echo e(old('payment_method') == 'cash' ? 'selected' : ''); ?>>نقداً</option>
                        <option value="other" <?php echo e(old('payment_method') == 'other' ? 'selected' : ''); ?>>أخرى</option>
                    </select>
                    <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div id="bank_fields" class="space-y-4 <?php echo e(old('payment_method') != 'bank_transfer' ? 'hidden' : ''); ?>">
                    <div>
                        <label for="bank_name" class="block text-sm font-bold text-gray-700 mb-2">اسم البنك</label>
                        <input type="text" name="bank_name" id="bank_name" value="<?php echo e(old('bank_name')); ?>"
                               class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                               placeholder="مثال: البنك الأهلي">
                        <?php $__errorArgs = ['bank_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="account_holder_name" class="block text-sm font-bold text-gray-700 mb-2">اسم صاحب الحساب</label>
                        <input type="text" name="account_holder_name" id="account_holder_name" value="<?php echo e(old('account_holder_name')); ?>"
                               class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <?php $__errorArgs = ['account_holder_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="account_number" class="block text-sm font-bold text-gray-700 mb-2">رقم الحساب</label>
                        <input type="text" name="account_number" id="account_number" value="<?php echo e(old('account_number')); ?>"
                               class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <?php $__errorArgs = ['account_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="iban" class="block text-sm font-bold text-gray-700 mb-2">الآيبان (اختياري)</label>
                        <input type="text" name="iban" id="iban" value="<?php echo e(old('iban')); ?>"
                               class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                               placeholder="EG...">
                        <?php $__errorArgs = ['iban'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-bold text-gray-700 mb-2">ملاحظات (اختياري)</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                              placeholder="أي تفاصيل إضافية"><?php echo e(old('notes')); ?></textarea>
                    <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex flex-wrap gap-4 justify-end pt-4 border-t border-gray-200">
                    <a href="<?php echo e(route('instructor.withdrawals.index')); ?>"
                       class="px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-bold hover:bg-gray-50 dark:hover:bg-slate-700/40 transition-colors">
                        إلغاء
                    </a>
                    <button type="submit" class="px-6 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold shadow-lg transition-all">
                        <i class="fas fa-paper-plane ml-2"></i>
                        تقديم الطلب
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('payment_method').addEventListener('change', function() {
    var bankFields = document.getElementById('bank_fields');
    bankFields.classList.toggle('hidden', this.value !== 'bank_transfer');
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\instructor\withdrawals\create.blade.php ENDPATH**/ ?>