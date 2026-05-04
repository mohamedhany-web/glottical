

<?php $__env->startSection('title', 'تفاصيل الاستشارة'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 pb-10">
    <div class="flex flex-wrap items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
        <a href="<?php echo e(route('dashboard')); ?>" class="hover:text-sky-600 dark:hover:text-sky-400 font-medium"><?php echo e(__('auth.dashboard')); ?></a>
        <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
        <a href="<?php echo e(route('consultations.index')); ?>" class="hover:text-sky-600 dark:hover:text-sky-400 font-medium">طلبات الاستشارة</a>
        <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
        <span class="text-gray-900 dark:text-gray-200 font-semibold">تفاصيل</span>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-lg overflow-hidden">
        <div class="px-5 sm:px-8 py-5 border-b border-gray-100 dark:border-gray-700 flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white">استشارة مع <?php echo e($consultation->instructor->name ?? 'المدرب'); ?></h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">رقم الطلب: <span class="font-mono font-semibold text-gray-700 dark:text-gray-300">#<?php echo e($consultation->id); ?></span></p>
            </div>
            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200"><?php echo e($consultation->statusLabel()); ?></span>
        </div>

        <div class="p-5 sm:p-8 space-y-6">
            <?php if(session('success')): ?>
                <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm font-medium"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 text-sm"><?php echo e(session('error')); ?></div>
            <?php endif; ?>

            <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700">
                    <dt class="text-gray-500 dark:text-gray-400 text-xs mb-1">المبلغ</dt>
                    <dd class="font-bold text-gray-900 dark:text-white text-lg"><?php echo e(number_format($consultation->price_amount, 2)); ?> <?php echo e(__('public.currency_egp')); ?></dd>
                </div>
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700">
                    <dt class="text-gray-500 dark:text-gray-400 text-xs mb-1">المدة</dt>
                    <dd class="font-bold text-gray-900 dark:text-white text-lg"><?php echo e((int) $consultation->duration_minutes); ?> دقيقة</dd>
                </div>
                <?php if($consultation->payment_method): ?>
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700">
                    <dt class="text-gray-500 dark:text-gray-400 text-xs mb-1">طريقة الدفع</dt>
                    <dd class="font-bold text-gray-900 dark:text-white">
                        <?php if($consultation->payment_method === 'bank_transfer'): ?> تحويل بنكي / محفظة
                        <?php elseif($consultation->payment_method === 'cash'): ?> نقدي
                        <?php else: ?> أخرى <?php endif; ?>
                    </dd>
                </div>
                <?php endif; ?>
            </dl>

            <?php if($consultation->platformWallet): ?>
            <div class="rounded-xl border border-sky-200 dark:border-sky-800 bg-sky-50 dark:bg-sky-900/20 p-4 text-sm">
                <p class="font-bold text-sky-900 dark:text-sky-100 mb-1">حساب التحويل المختار</p>
                <p class="text-gray-800 dark:text-gray-200"><?php echo e($consultation->platformWallet->name ?? \App\Models\Wallet::typeLabel($consultation->platformWallet->type)); ?>

                    <?php if($consultation->platformWallet->account_number): ?> — <span class="font-mono"><?php echo e($consultation->platformWallet->account_number); ?></span><?php endif; ?>
                </p>
            </div>
            <?php endif; ?>

            <?php if($consultation->student_message): ?>
            <div class="rounded-xl border border-gray-200 dark:border-gray-600 p-4 sm:p-5">
                <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">رسالتك</h3>
                <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line leading-relaxed"><?php echo e($consultation->student_message); ?></p>
            </div>
            <?php endif; ?>

            <?php if($consultation->payment_proof): ?>
            <div class="rounded-xl border border-gray-200 dark:border-gray-600 p-4 sm:p-5">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2"><i class="fas fa-receipt text-emerald-500"></i> إيصال الدفع المُرسل</h3>
                <a href="<?php echo e(asset('storage/'.$consultation->payment_proof)); ?>" target="_blank" rel="noopener" class="block">
                    <img src="<?php echo e(asset('storage/'.$consultation->payment_proof)); ?>" alt="إيصال الدفع" class="max-h-96 w-auto max-w-full rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm mx-auto">
                </a>
                <p class="text-xs text-gray-500 mt-2 text-center">اضغط على الصورة لفتحها بحجم كامل</p>
            </div>
            <?php endif; ?>

            <?php if($consultation->status === \App\Models\ConsultationRequest::STATUS_AWAITING_VERIFICATION): ?>
            <div class="rounded-xl border border-violet-200 dark:border-violet-800 bg-violet-50 dark:bg-violet-900/20 p-4 sm:p-6">
                <div class="flex gap-3">
                    <div class="w-10 h-10 rounded-xl bg-violet-600 text-white flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-violet-900 dark:text-violet-100">بانتظار مراجعة الإدارة</h3>
                        <p class="text-sm text-violet-800 dark:text-violet-200/90 mt-2 leading-relaxed">تم خصم المبلغ سابقاً من <a href="<?php echo e(route('student.wallet.index')); ?>" class="font-bold underline hover:no-underline">محفظة رصيدك</a> (طلب قديم). فريق المنصة يتحقق من الطلب؛ بعد الموافقة ستصلك إشعاراً ويُحدد موعد الجلسة.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if($consultation->status === \App\Models\ConsultationRequest::STATUS_PAYMENT_REPORTED && $consultation->paidViaPlatformAccounts()): ?>
            <div class="rounded-xl border border-sky-200 dark:border-sky-800 bg-sky-50 dark:bg-sky-900/20 p-4 sm:p-6">
                <div class="flex gap-3">
                    <div class="w-10 h-10 rounded-xl bg-sky-600 text-white flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sky-900 dark:text-sky-100">تم استلام طلبك وإيصال الدفع</h3>
                        <p class="text-sm text-sky-800 dark:text-sky-200/90 mt-2 leading-relaxed">الإدارة تتحقق من استلام المبلغ على حسابات المنصة. بعد التأكيد ستصلك إشعار ويُحدد موعد الجلسة.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if($consultation->status === \App\Models\ConsultationRequest::STATUS_PENDING): ?>
                <?php if($settings->payment_instructions): ?>
                <div class="rounded-xl bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/40 p-4 text-sm whitespace-pre-line text-amber-950 dark:text-amber-100"><?php echo e($settings->payment_instructions); ?></div>
                <?php endif; ?>
                <form method="POST" action="<?php echo e(route('consultations.report-payment', $consultation)); ?>" class="space-y-3 rounded-xl border border-gray-200 dark:border-gray-600 p-4">
                    <?php echo csrf_field(); ?>
                    <p class="text-sm text-gray-600 dark:text-gray-400">طلب قديم بانتظار إبلاغ التحويل — يُفضّل إنشاء طلب جديد من صفحة المدرب بإيصال مرفق.</p>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">مرجع التحويل (اختياري)</label>
                    <input type="text" name="payment_reference" value="<?php echo e(old('payment_reference', $consultation->payment_reference)); ?>" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-900 px-4 py-2.5 text-sm">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold">أبلغت عن إتمام التحويل</button>
                </form>
            <?php endif; ?>

            <?php if($consultation->status === \App\Models\ConsultationRequest::STATUS_SCHEDULED && $consultation->classroomMeeting): ?>
                <?php $joinUrl = url('classroom/join/'.$consultation->classroomMeeting->code); ?>
                <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 p-4 sm:p-6 space-y-3">
                    <h3 class="font-bold text-emerald-900 dark:text-emerald-100 flex items-center gap-2"><i class="fas fa-video"></i> موعد الجلسة</h3>
                    <p class="text-sm text-emerald-800 dark:text-emerald-200"><?php echo e($consultation->scheduled_at?->format('Y-m-d H:i')); ?></p>
                    <div class="flex flex-wrap gap-2 items-center">
                        <input type="text" readonly value="<?php echo e($joinUrl); ?>" class="flex-1 min-w-[200px] text-xs px-3 py-2 rounded-lg border border-emerald-200 dark:border-emerald-800 bg-white dark:bg-gray-900">
                        <button type="button" onclick="navigator.clipboard.writeText('<?php echo e($joinUrl); ?>')" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-xs font-bold">نسخ الرابط</button>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">يظهر الموعد في <a href="<?php echo e(route('calendar')); ?>" class="text-sky-600 dark:text-sky-400 font-semibold underline">تقويمك</a>.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\student\consultations\show.blade.php ENDPATH**/ ?>