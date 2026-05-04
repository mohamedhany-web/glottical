

<?php $__env->startSection('title', 'طلب استشارة #'.$consultation->id); ?>
<?php $__env->startSection('header', 'طلب استشارة #'.$consultation->id); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-5xl">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm font-medium"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm font-medium"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 space-y-3">
        <div class="flex flex-wrap justify-between gap-2">
            <h2 class="text-xl font-bold text-slate-900">تفاصيل الطلب</h2>
            <span class="px-3 py-1 rounded-full bg-slate-100 text-sm font-semibold"><?php echo e($consultation->statusLabel()); ?></span>
        </div>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div><dt class="text-slate-500">الطالب</dt><dd class="font-semibold"><?php echo e($consultation->student->name ?? '—'); ?> — <?php echo e($consultation->student->email ?? ''); ?></dd></div>
            <div><dt class="text-slate-500">المدرب</dt><dd class="font-semibold"><?php echo e($consultation->instructor->name ?? '—'); ?></dd></div>
            <div><dt class="text-slate-500">المبلغ</dt><dd class="font-semibold"><?php echo e(number_format($consultation->price_amount, 2)); ?> ج.م</dd></div>
            <div><dt class="text-slate-500">المدة</dt><dd class="font-semibold"><?php echo e((int) $consultation->duration_minutes); ?> دقيقة</dd></div>
            <?php if($consultation->payment_reference): ?>
            <div class="sm:col-span-2"><dt class="text-slate-500">مرجع التحويل</dt><dd class="font-mono text-xs"><?php echo e($consultation->payment_reference); ?></dd></div>
            <?php endif; ?>
            <?php if($consultation->payment_method): ?>
            <div><dt class="text-slate-500">طريقة الدفع</dt><dd class="font-semibold">
                <?php if($consultation->payment_method === 'bank_transfer'): ?> تحويل بنكي / محفظة
                <?php elseif($consultation->payment_method === 'cash'): ?> نقدي
                <?php else: ?> أخرى <?php endif; ?>
            </dd></div>
            <?php endif; ?>
            <?php if($consultation->platformWallet): ?>
            <div class="sm:col-span-2"><dt class="text-slate-500">حساب المنصة</dt><dd class="font-semibold"><?php echo e($consultation->platformWallet->name ?? \App\Models\Wallet::typeLabel($consultation->platformWallet->type)); ?> <?php if($consultation->platformWallet->account_number): ?><span class="font-mono text-sm"> — <?php echo e($consultation->platformWallet->account_number); ?></span><?php endif; ?></dd></div>
            <?php endif; ?>
            <?php if($consultation->payment_proof): ?>
            <div class="sm:col-span-2">
                <dt class="text-slate-500 mb-2">إيصال الدفع</dt>
                <dd>
                    <a href="<?php echo e(asset('storage/'.$consultation->payment_proof)); ?>" target="_blank" rel="noopener" class="inline-block">
                        <img src="<?php echo e(asset('storage/'.$consultation->payment_proof)); ?>" alt="إيصال" class="max-h-64 rounded-lg border border-slate-200 shadow-sm">
                    </a>
                </dd>
            </div>
            <?php endif; ?>
            <?php if($consultation->student_message): ?>
            <div class="sm:col-span-2"><dt class="text-slate-500">رسالة الطالب</dt><dd class="text-slate-800 whitespace-pre-line"><?php echo e($consultation->student_message); ?></dd></div>
            <?php endif; ?>
            <?php if($consultation->walletTransaction): ?>
            <div class="sm:col-span-2 rounded-xl bg-violet-50 border border-violet-200 p-4">
                <dt class="text-violet-800 font-bold text-sm mb-2">دفع من المحفظة</dt>
                <dd class="text-sm text-slate-700 space-y-1">
                    <p>معاملة المحفظة #<?php echo e($consultation->walletTransaction->id); ?> — المبلغ <?php echo e(number_format($consultation->walletTransaction->amount, 2)); ?> ج.م</p>
                    <p class="text-xs text-slate-500"><?php echo e($consultation->walletTransaction->created_at?->format('Y-m-d H:i')); ?></p>
                    <?php if($consultation->walletTransaction->notes): ?>
                    <p class="text-xs font-mono bg-white/80 rounded px-2 py-1 border border-violet-100"><?php echo e($consultation->walletTransaction->notes); ?></p>
                    <?php endif; ?>
                </dd>
            </div>
            <?php endif; ?>
        </dl>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <h3 class="font-bold text-slate-900 mb-3">ملاحظات إدارية</h3>
        <form method="POST" action="<?php echo e(route('admin.consultations.notes', $consultation)); ?>">
            <?php echo csrf_field(); ?>
            <textarea name="admin_notes" rows="3" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm mb-2"><?php echo e(old('admin_notes', $consultation->admin_notes)); ?></textarea>
            <button type="submit" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-semibold">حفظ الملاحظات</button>
        </form>
    </div>

    <?php if(in_array($consultation->status, [\App\Models\ConsultationRequest::STATUS_PENDING, \App\Models\ConsultationRequest::STATUS_PAYMENT_REPORTED], true)): ?>
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-6 space-y-3">
        <?php if($consultation->status === \App\Models\ConsultationRequest::STATUS_PAYMENT_REPORTED && $consultation->payment_proof): ?>
            <p class="text-sm text-emerald-900">تحقق من إيصال الدفع وحساب المنصة ثم أكّد استلام المبلغ.</p>
        <?php endif; ?>
        <form method="POST" action="<?php echo e(route('admin.consultations.confirm-payment', $consultation)); ?>" class="inline">
            <?php echo csrf_field(); ?>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-bold">تأكيد استلام الدفع</button>
        </form>
    </div>
    <?php endif; ?>

    <?php if($consultation->status === \App\Models\ConsultationRequest::STATUS_AWAITING_VERIFICATION): ?>
    <div class="rounded-2xl border border-violet-200 bg-violet-50/80 p-6 space-y-3">
        <p class="text-sm text-violet-900 font-semibold">طلب قديم: تم خصم المبلغ من <strong>محفظة رصيد الطالب</strong>. راجع تفاصيل الطلب ثم اقبل الطلب للمتابعة إلى الجدولة.</p>
        <form method="POST" action="<?php echo e(route('admin.consultations.confirm-payment', $consultation)); ?>" class="inline">
            <?php echo csrf_field(); ?>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-violet-700 hover:bg-violet-800 text-white text-sm font-bold">قبول الطلب والدفع (محفظة)</button>
        </form>
    </div>
    <?php endif; ?>

    <?php if($consultation->status === \App\Models\ConsultationRequest::STATUS_PAID): ?>
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <h3 class="font-bold text-slate-900 mb-3">جدولة الاستشارة وإنشاء غرفة Classroom</h3>
        <form method="POST" action="<?php echo e(route('admin.consultations.schedule', $consultation)); ?>" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">موعد البدء</label>
                <input type="datetime-local" name="scheduled_at" value="<?php echo e(old('scheduled_at')); ?>" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">مدة الجلسة (دقيقة)</label>
                <input type="number" name="duration_minutes" value="<?php echo e(old('duration_minutes', $consultation->duration_minutes)); ?>" min="15" max="480" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-sky-600 text-white text-sm font-bold">جدولة وإنشاء رابط الغرفة</button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <?php if($consultation->status === \App\Models\ConsultationRequest::STATUS_SCHEDULED && $consultation->classroomMeeting): ?>
    <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-6">
        <h3 class="font-bold text-emerald-900 mb-2">الغرفة جاهزة</h3>
        <p class="text-sm text-emerald-800 mb-2">الموعد: <?php echo e($consultation->scheduled_at?->format('Y-m-d H:i')); ?></p>
        <p class="text-xs font-mono break-all"><?php echo e(url('classroom/join/'.$consultation->classroomMeeting->code)); ?></p>
    </div>
    <?php endif; ?>

    <div class="flex flex-wrap gap-3">
        <?php if(!in_array($consultation->status, [\App\Models\ConsultationRequest::STATUS_CANCELLED, \App\Models\ConsultationRequest::STATUS_COMPLETED], true)): ?>
        <form method="POST" action="<?php echo e(route('admin.consultations.cancel', $consultation)); ?>" onsubmit="return confirm('إلغاء الطلب؟');">
            <?php echo csrf_field(); ?>
            <button type="submit" class="px-4 py-2 rounded-lg border border-rose-300 text-rose-700 text-sm font-semibold">إلغاء الطلب</button>
        </form>
        <?php endif; ?>
        <?php if($consultation->status === \App\Models\ConsultationRequest::STATUS_SCHEDULED): ?>
        <form method="POST" action="<?php echo e(route('admin.consultations.complete', $consultation)); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="px-4 py-2 rounded-lg bg-slate-700 text-white text-sm font-semibold">تسجيل كمكتملة</button>
        </form>
        <?php endif; ?>
        <a href="<?php echo e(route('admin.consultations.index')); ?>" class="px-4 py-2 rounded-lg text-sky-600 text-sm font-semibold hover:underline">رجوع للقائمة</a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\consultations\show.blade.php ENDPATH**/ ?>