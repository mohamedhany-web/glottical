<?php $__env->startSection('title', 'تفاصيل الدفعة - ' . config('app.name')); ?>
<?php $__env->startSection('page_title', 'تفاصيل الدفعة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $statusBadges = [
        'completed' => ['label' => 'مكتملة', 'classes' => 'bg-accent-soft text-accent'],
        'pending' => ['label' => 'معلقة', 'classes' => 'bg-metal/15 text-metal'],
        'processing' => ['label' => 'قيد المعالجة', 'classes' => 'bg-accent-soft text-accent'],
        'failed' => ['label' => 'فاشلة', 'classes' => 'bg-canvas-muted text-muted'],
        'cancelled' => ['label' => 'ملغاة', 'classes' => 'bg-canvas-muted text-muted'],
        'refunded' => ['label' => 'مستردة', 'classes' => 'bg-canvas-muted text-muted'],
    ];
    $paymentMethodLabels = [
        'cash' => ['label' => 'نقدي', 'icon' => 'fas fa-money-bill'],
        'card' => ['label' => 'بطاقة', 'icon' => 'fas fa-credit-card'],
        'bank_transfer' => ['label' => 'تحويل بنكي', 'icon' => 'fas fa-university'],
        'online' => ['label' => 'دفع إلكتروني', 'icon' => 'fas fa-globe'],
        'wallet' => ['label' => 'محفظة', 'icon' => 'fas fa-wallet'],
        'other' => ['label' => 'أخرى', 'icon' => 'fas fa-ellipsis-h'],
    ];
    $statusMeta = $statusBadges[$payment->status] ?? null;
    $invoiceStatusBadges = [
        'paid' => ['label' => 'مدفوعة', 'classes' => 'bg-accent-soft text-accent'],
        'pending' => ['label' => 'معلقة', 'classes' => 'bg-metal/15 text-metal'],
    ];
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المحاسبة · المدفوعات · <?php echo e($payment->payment_number); ?></p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">تفاصيل الدفعة #<?php echo e($payment->payment_number); ?></h2>
            <p class="mt-1 text-sm text-muted">
                <i class="fas fa-calendar-alt text-xs"></i>
                أُنشئت في <?php echo e($payment->created_at->format('Y-m-d H:i')); ?>

            </p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.payments.edit', $payment)); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-edit text-xs"></i>
                تعديل الدفعة
            </a>
            <a href="<?php echo e(route('admin.payments.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                العودة للقائمة
            </a>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <div class="space-y-5 xl:col-span-2">
            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                    <div>
                        <h3 class="text-base font-semibold text-ink">معلومات الدفعة</h3>
                        <p class="mt-0.5 text-xs text-muted">المبلغ وطريقة الدفع والتواريخ</p>
                    </div>
                    <?php if($statusMeta): ?>
                        <span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-medium <?php echo e($statusMeta['classes']); ?>"><?php echo e($statusMeta['label']); ?></span>
                    <?php endif; ?>
                </div>
                <dl class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 sm:p-5">
                    <div>
                        <dt class="text-xs font-medium text-muted">المبلغ</dt>
                        <dd class="mt-1 text-2xl font-semibold tabular-nums text-ink"><?php echo e(number_format($payment->amount, 2)); ?> <span class="text-sm font-normal text-muted">ج.م</span></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-muted">العملة</dt>
                        <dd class="mt-1 text-sm font-semibold text-ink"><?php echo e($payment->currency ?? 'EGP'); ?></dd>
                    </div>
                    <?php if(($payment->gateway_fee_amount ?? 0) > 0 || $payment->net_after_gateway_fee !== null): ?>
                    <div>
                        <dt class="text-xs font-medium text-muted">عمولة البوابة (تقدير)</dt>
                        <dd class="mt-1 text-sm font-semibold tabular-nums text-ink"><?php echo e(number_format((float) ($payment->gateway_fee_amount ?? 0), 2)); ?> <span class="text-xs font-normal text-muted">ج.م</span></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-muted">صافي بعد العمولة</dt>
                        <dd class="mt-1 text-sm font-semibold tabular-nums text-ink">
                            <?php
                                $netShow = $payment->net_after_gateway_fee !== null
                                    ? (float) $payment->net_after_gateway_fee
                                    : round((float) $payment->amount - (float) ($payment->gateway_fee_amount ?? 0), 2);
                            ?>
                            <?php echo e(number_format($netShow, 2)); ?> <span class="text-xs font-normal text-muted">ج.م</span>
                        </dd>
                    </div>
                    <?php endif; ?>
                    <div>
                        <dt class="text-xs font-medium text-muted">طريقة الدفع</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center gap-2 rounded-lg bg-canvas-muted px-2.5 py-1 text-xs font-medium text-ink">
                                <i class="<?php echo e($paymentMethodLabels[$payment->payment_method]['icon'] ?? 'fas fa-ellipsis-h'); ?> text-[10px] text-muted"></i>
                                <?php echo e($paymentMethodLabels[$payment->payment_method]['label'] ?? $payment->payment_method); ?>

                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-muted">تاريخ الإنشاء</dt>
                        <dd class="mt-1 text-sm font-medium tabular-nums text-ink"><?php echo e($payment->created_at->format('Y-m-d H:i')); ?></dd>
                    </div>
                    <?php if($payment->paid_at): ?>
                    <div>
                        <dt class="text-xs font-medium text-muted">تاريخ الدفع</dt>
                        <dd class="mt-1 text-sm font-medium tabular-nums text-ink"><?php echo e($payment->paid_at->format('Y-m-d H:i')); ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if($payment->processedBy): ?>
                    <div>
                        <dt class="text-xs font-medium text-muted">معالج بواسطة</dt>
                        <dd class="mt-1 text-sm font-semibold text-ink"><?php echo e($payment->processedBy->name); ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if($payment->payment_gateway): ?>
                    <div>
                        <dt class="text-xs font-medium text-muted">بوابة الدفع</dt>
                        <dd class="mt-1 text-sm font-semibold text-ink"><?php echo e($payment->payment_gateway); ?></dd>
                    </div>
                    <?php endif; ?>
                </dl>
            </article>

            <?php if($payment->user): ?>
            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">بيانات العميل</h3>
                    <p class="mt-0.5 text-xs text-muted">معلومات العميل المرتبط بالدفعة</p>
                </div>
                <dl class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 sm:p-5">
                    <div>
                        <dt class="text-xs font-medium text-muted">الاسم</dt>
                        <dd class="mt-1 text-sm font-semibold text-ink"><?php echo e($payment->user->name ?? 'غير معروف'); ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-muted">رقم الهاتف</dt>
                        <dd class="mt-1 text-sm font-semibold text-ink"><?php echo e($payment->user->phone ?? '—'); ?></dd>
                    </div>
                    <?php if($payment->user->email): ?>
                    <div>
                        <dt class="text-xs font-medium text-muted">البريد الإلكتروني</dt>
                        <dd class="mt-1 break-all text-sm font-medium text-ink"><?php echo e($payment->user->email); ?></dd>
                    </div>
                    <?php endif; ?>
                    <div>
                        <dt class="text-xs font-medium text-muted">الدور</dt>
                        <dd class="mt-1 text-sm font-semibold text-ink">
                            <?php if($payment->user->role == 'student'): ?> <?php echo e(__('admin.student_role_label')); ?>

                            <?php elseif($payment->user->role == 'instructor'): ?> مدرب
                            <?php elseif($payment->user->role == 'admin'): ?> إداري
                            <?php else: ?> <?php echo e($payment->user->role); ?>

                            <?php endif; ?>
                        </dd>
                    </div>
                </dl>
            </article>
            <?php endif; ?>

            <?php if($payment->notes): ?>
            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">ملاحظات</h3>
                </div>
                <div class="p-4 sm:p-5">
                    <p class="text-sm leading-relaxed text-ink-soft"><?php echo e($payment->notes); ?></p>
                </div>
            </article>
            <?php endif; ?>
        </div>

        <div class="space-y-5">
            <?php if($payment->invoice): ?>
            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">الفاتورة المرتبطة</h3>
                    <p class="mt-0.5 text-xs text-muted">تفاصيل الفاتورة المرتبطة بهذه الدفعة</p>
                </div>
                <dl class="space-y-4 p-4 sm:p-5">
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-xs font-medium text-muted">رقم الفاتورة</dt>
                        <dd>
                            <a href="<?php echo e(route('admin.invoices.show', $payment->invoice)); ?>" class="text-sm font-semibold text-accent transition hover:text-accent/80">
                                <?php echo e($payment->invoice->invoice_number); ?>

                                <i class="fas fa-external-link-alt text-xs mr-1"></i>
                            </a>
                        </dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-xs font-medium text-muted">المبلغ الإجمالي</dt>
                        <dd class="text-sm font-semibold tabular-nums text-ink"><?php echo e(number_format($payment->invoice->total_amount, 2)); ?> <span class="text-xs font-normal text-muted">ج.م</span></dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-xs font-medium text-muted">الحالة</dt>
                        <dd>
                            <?php
                                $invBadge = $invoiceStatusBadges[$payment->invoice->status] ?? ['label' => 'متأخرة', 'classes' => 'bg-canvas-muted text-muted'];
                            ?>
                            <span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-medium <?php echo e($invBadge['classes']); ?>"><?php echo e($invBadge['label']); ?></span>
                        </dd>
                    </div>
                </dl>
            </article>
            <?php endif; ?>

            <?php if($payment->transactions && $payment->transactions->count() > 0): ?>
            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">المعاملات المرتبطة</h3>
                    <p class="mt-0.5 text-xs text-muted"><?php echo e($payment->transactions->count()); ?> معاملة</p>
                </div>
                <div class="divide-y divide-line">
                    <?php $__currentLoopData = $payment->transactions->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center justify-between gap-4 px-4 py-3 sm:px-5">
                        <a href="<?php echo e(route('admin.transactions.show', $transaction)); ?>" class="text-sm font-semibold text-accent transition hover:text-accent/80">
                            <?php echo e($transaction->transaction_number ?? 'N/A'); ?>

                        </a>
                        <span class="text-sm tabular-nums text-muted"><?php echo e(number_format($transaction->amount, 2)); ?> ج.م</span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($payment->transactions->count() > 3): ?>
                    <p class="px-4 py-3 text-xs text-muted sm:px-5">و <?php echo e($payment->transactions->count() - 3); ?> معاملة أخرى</p>
                    <?php endif; ?>
                </div>
            </article>
            <?php endif; ?>

            <?php if($payment->reference_number || $payment->transaction_id): ?>
            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">مراجع إضافية</h3>
                </div>
                <dl class="space-y-4 p-4 sm:p-5">
                    <?php if($payment->reference_number): ?>
                    <div>
                        <dt class="text-xs font-medium text-muted">رقم المرجع</dt>
                        <dd class="mt-1 font-mono text-sm text-ink"><?php echo e($payment->reference_number); ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if($payment->transaction_id): ?>
                    <div>
                        <dt class="text-xs font-medium text-muted">رقم المعاملة</dt>
                        <dd class="mt-1 font-mono text-sm text-ink"><?php echo e($payment->transaction_id); ?></dd>
                    </div>
                    <?php endif; ?>
                </dl>
            </article>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\payments\show.blade.php ENDPATH**/ ?>