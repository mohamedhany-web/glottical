<?php $__env->startSection('title', 'طلب استشارة — '.$instructor->name); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 pb-10">
    <div class="flex flex-wrap items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
        <a href="<?php echo e(route('dashboard')); ?>" class="hover:text-sky-600 dark:hover:text-sky-400 font-medium"><?php echo e(__('auth.dashboard')); ?></a>
        <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
        <a href="<?php echo e(route('consultations.index')); ?>" class="hover:text-sky-600 dark:hover:text-sky-400 font-medium">طلبات الاستشارة</a>
        <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
        <span class="text-gray-900 dark:text-gray-200 font-semibold truncate">طلب جديد</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-10 items-start">
        
        <div class="lg:col-span-1 order-2 lg:order-1">
            <div class="sticky top-24 rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-lg p-6 space-y-5">
                <h2 class="text-lg font-black text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-receipt text-sky-500"></i>
                    ملخص الطلب
                </h2>
                <div class="flex items-start gap-3 pb-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-500 to-cyan-600 flex items-center justify-center text-white shadow-md flex-shrink-0">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-gray-900 dark:text-white truncate"><?php echo e($instructor->name); ?></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">استشارة فردية</p>
                        <a href="<?php echo e(route('public.instructors.show', $instructor)); ?>" class="text-xs text-sky-600 dark:text-sky-400 font-semibold hover:underline mt-2 inline-block">الملف العام للمدرب</a>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">قيمة الاستشارة</span>
                        <span class="font-bold text-sky-600 dark:text-sky-400"><?php echo e(number_format($priceEgp, 2)); ?> <?php echo e(__('public.currency_egp')); ?></span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
                        <span class="font-bold text-gray-900 dark:text-white">مدة الجلسة</span>
                        <span class="font-bold text-gray-900 dark:text-white"><?php echo e((int) $durationMinutes); ?> دقيقة</span>
                    </div>
                </div>
                <ol class="space-y-2 text-xs text-gray-600 dark:text-gray-400">
                    <li class="flex gap-2"><span class="font-bold text-sky-600">١</span> التحويل على <strong class="text-gray-800 dark:text-gray-200">حسابات المنصة</strong> وإرفاق الإيصال</li>
                    <li class="flex gap-2"><span class="font-bold text-sky-600">٢</span> مراجعة الإدارة وتأكيد استلام المبلغ</li>
                    <li class="flex gap-2"><span class="font-bold text-sky-600">٣</span> جدولة الموعد وإرسال رابط الغرفة</li>
                </ol>
            </div>
        </div>

        <div class="lg:col-span-2 order-1 lg:order-2">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-lg overflow-hidden">
                <div class="px-5 sm:px-8 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-900/40">
                    <h1 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white">طلب استشارة ودفع</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">نفس آلية دفع الكورسات: تحويل على حسابات المنصة المعروضة ثم رفع صورة الإيصال.</p>
                </div>

                <div class="p-5 sm:p-8 space-y-6">
                    <?php if(session('error')): ?>
                        <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 text-sm"><?php echo e(session('error')); ?></div>
                    <?php endif; ?>
                    <?php if($errors->any()): ?>
                        <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                            <ul class="list-disc list-inside text-sm text-red-800 dark:text-red-200 space-y-1">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($err); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if($settings->payment_instructions): ?>
                    <div class="rounded-xl bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/40 p-4">
                        <h3 class="font-bold text-amber-900 dark:text-amber-200 text-sm mb-2">تعليمات من المنصة</h3>
                        <div class="text-sm text-amber-950 dark:text-amber-100 whitespace-pre-line leading-relaxed"><?php echo e($settings->payment_instructions); ?></div>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('consultations.store', $instructor)); ?>" enctype="multipart/form-data" class="space-y-5">
                        <?php echo csrf_field(); ?>

                        <div>
                            <label class="block text-sm font-bold text-gray-800 dark:text-gray-200 mb-2">موضوع أو استفسارك (اختياري)</label>
                            <textarea name="student_message" rows="4" class="w-full rounded-xl border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-900 px-4 py-3 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 outline-none transition-shadow" placeholder="صف بإيجاز ما تحتاجه من الاستشارة..."><?php echo e(old('student_message')); ?></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-black text-gray-800 dark:text-gray-200 mb-2 flex items-center gap-2">
                                <i class="fas fa-credit-card text-sky-500"></i>
                                طريقة الدفع
                            </label>
                            <select name="payment_method" id="payment_method" required
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all bg-white dark:bg-gray-900 font-medium text-gray-900 dark:text-gray-100">
                                <option value="">اختر طريقة الدفع</option>
                                <option value="bank_transfer" <?php if(old('payment_method')==='bank_transfer'): echo 'selected'; endif; ?>>تحويل بنكي / محفظة إلكترونية</option>
                                <option value="cash" <?php if(old('payment_method')==='cash'): echo 'selected'; endif; ?>>نقدي</option>
                                <option value="other" <?php if(old('payment_method')==='other'): echo 'selected'; endif; ?>>أخرى</option>
                            </select>
                        </div>

                        <?php if(isset($availableWallets) && $availableWallets->count() > 0): ?>
                        <div id="wallet_selection" class="hidden">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-university text-sky-600 ml-2"></i>
                                حساب المنصة للتحويل
                            </label>
                            <select name="wallet_id" id="wallet_id"
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all bg-white dark:bg-gray-900">
                                <option value="">اختر الحساب</option>
                                <?php $__currentLoopData = $availableWallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wallet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($wallet->id); ?>"
                                            <?php if((string)old('wallet_id')===(string)$wallet->id): echo 'selected'; endif; ?>
                                            data-type="<?php echo e($wallet->type); ?>"
                                            data-name="<?php echo e($wallet->name); ?>"
                                            data-account-number="<?php echo e($wallet->account_number); ?>"
                                            data-bank-name="<?php echo e($wallet->bank_name); ?>"
                                            data-account-holder="<?php echo e($wallet->account_holder); ?>"
                                            data-notes="<?php echo e($wallet->notes); ?>">
                                        <?php echo e($wallet->name ?? \App\Models\Wallet::typeLabel($wallet->type)); ?>

                                        <?php if($wallet->account_number): ?> — <?php echo e($wallet->account_number); ?> <?php endif; ?>
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>

                            <div id="wallet_details" class="hidden mt-4 p-4 bg-sky-50 dark:bg-sky-900/20 rounded-xl border-2 border-sky-200 dark:border-sky-800">
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                    <i class="fas fa-info-circle text-sky-600"></i>
                                    تفاصيل التحويل
                                </h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between gap-2"><span class="text-gray-600 dark:text-gray-400">النوع</span><span class="font-semibold text-gray-900 dark:text-gray-100" id="wallet_type_text"></span></div>
                                    <div id="wallet_name_detail" class="hidden flex justify-between gap-2"><span class="text-gray-600 dark:text-gray-400">الاسم</span><span class="font-semibold text-gray-900 dark:text-gray-100" id="wallet_name_text"></span></div>
                                    <div id="wallet_account_detail" class="hidden flex justify-between gap-2"><span class="text-gray-600 dark:text-gray-400">رقم الحساب</span><span class="font-mono font-semibold text-gray-900 dark:text-gray-100" id="wallet_account_text"></span></div>
                                    <div id="wallet_bank_detail" class="hidden flex justify-between gap-2"><span class="text-gray-600 dark:text-gray-400">البنك</span><span class="font-semibold text-gray-900 dark:text-gray-100" id="wallet_bank_text"></span></div>
                                    <div id="wallet_holder_detail" class="hidden flex justify-between gap-2"><span class="text-gray-600 dark:text-gray-400">صاحب الحساب</span><span class="font-semibold text-gray-900 dark:text-gray-100" id="wallet_holder_text"></span></div>
                                    <div id="wallet_notes_detail" class="hidden mt-2 pt-2 border-t border-sky-200 dark:border-sky-700"><span class="text-gray-600 dark:text-gray-400 text-xs block mb-1">ملاحظات</span><span class="text-sm text-gray-800 dark:text-gray-200" id="wallet_notes_text"></span></div>
                                </div>
                                <div class="mt-3 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg text-xs text-amber-900 dark:text-amber-100">
                                    <i class="fas fa-exclamation-triangle ml-1"></i>
                                    حوّل المبلغ <strong><?php echo e(number_format($priceEgp, 2)); ?> <?php echo e(__('public.currency_egp')); ?></strong> ثم ارفع صورة الإيصال أدناه.
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div>
                            <label class="block text-sm font-black text-gray-800 dark:text-gray-200 mb-2 flex items-center gap-2">
                                <i class="fas fa-image text-emerald-500"></i>
                                صورة الإيصال <span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="payment_proof" accept="image/*" required
                                   class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-sm">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">jpeg أو png أو jpg — بحد أقصى 40 ميجابايت (مثل طلبات الكورسات).</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">مرجع التحويل (اختياري)</label>
                            <input type="text" name="payment_reference" value="<?php echo e(old('payment_reference')); ?>"
                                   class="w-full rounded-xl border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-900 px-4 py-3 text-sm text-gray-900 dark:text-gray-100"
                                   placeholder="رقم العملية أو أي مرجع يساعد الإدارة">
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 pt-2">
                            <button type="submit" class="inline-flex flex-1 items-center justify-center gap-2 px-6 py-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-sm shadow-lg shadow-emerald-600/25 transition-all">
                                <i class="fas fa-paper-plane"></i>
                                إرسال الطلب والإيصال
                            </button>
                            <a href="<?php echo e(route('consultations.index')); ?>" class="inline-flex items-center justify-center gap-2 px-6 py-4 rounded-xl border-2 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-bold text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethod = document.getElementById('payment_method');
    const walletSelection = document.getElementById('wallet_selection');
    const walletId = document.getElementById('wallet_id');
    const walletDetails = document.getElementById('wallet_details');

    if (paymentMethod && walletSelection) {
        paymentMethod.addEventListener('change', function() {
            if (this.value === 'bank_transfer' || this.value === 'other') {
                walletSelection.classList.remove('hidden');
            } else {
                walletSelection.classList.add('hidden');
                if (walletDetails) walletDetails.classList.add('hidden');
                if (walletId) walletId.value = '';
            }
        });
        if (paymentMethod.value === 'bank_transfer' || paymentMethod.value === 'other') {
            walletSelection.classList.remove('hidden');
        }
    }

    if (walletId && walletDetails) {
        walletId.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            if (!this.value || !opt) {
                walletDetails.classList.add('hidden');
                return;
            }
            const type = opt.getAttribute('data-type');
            const typeLabels = { vodafone_cash: 'فودافون كاش', instapay: 'إنستا باي', bank_transfer: 'تحويل بنكي', cash: 'كاش', other: 'أخرى' };
            document.getElementById('wallet_type_text').textContent = typeLabels[type] || type || '—';

            function toggleLine(idPrefix, val) {
                const row = document.getElementById(idPrefix + '_detail');
                const text = document.getElementById(idPrefix + '_text');
                if (val) { row.classList.remove('hidden'); text.textContent = val; }
                else { row.classList.add('hidden'); }
            }
            toggleLine('wallet_name', opt.getAttribute('data-name'));
            toggleLine('wallet_account', opt.getAttribute('data-account-number'));
            toggleLine('wallet_bank', opt.getAttribute('data-bank-name'));
            toggleLine('wallet_holder', opt.getAttribute('data-account-holder'));
            const notes = opt.getAttribute('data-notes');
            const notesRow = document.getElementById('wallet_notes_detail');
            if (notes) { notesRow.classList.remove('hidden'); document.getElementById('wallet_notes_text').textContent = notes; }
            else { notesRow.classList.add('hidden'); }

            walletDetails.classList.remove('hidden');
        });
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\student\consultations\create.blade.php ENDPATH**/ ?>