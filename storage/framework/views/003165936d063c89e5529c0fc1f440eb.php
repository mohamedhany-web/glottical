<?php $__env->startSection('title', 'المحافظ الذكية'); ?>
<?php $__env->startSection('page_title', 'المحافظ الذكية'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $recentWallet = ($recentWallets ?? collect())->first();
    $netMonth = ($currentMonthDeposits ?? 0) - ($currentMonthWithdrawals ?? 0);
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $kpis = [
        ['label' => 'إجمالي المحافظ', 'value' => $stats['total'] ?? 0, 'icon' => 'fa-wallet', 'tone' => 'accent', 'note' => 'يشمل كل المحافظ المربوطة بالطلاب'],
        ['label' => 'الرصيد المتاح', 'value' => number_format($stats['total_balance'] ?? 0, 2), 'icon' => 'fa-coins', 'tone' => 'accent', 'note' => 'إجمالي الأرصدة الحالية بكل المحافظ', 'suffix' => ' ج.م'],
        ['label' => 'الرصيد المعلّق', 'value' => number_format($stats['pending_balance'] ?? 0, 2), 'icon' => 'fa-hourglass-half', 'tone' => 'metal', 'note' => 'المبالغ المعلّقة أو قيد المراجعة', 'suffix' => ' ج.م'],
        ['label' => 'صافي تدفقات الشهر', 'value' => number_format($netMonth, 2), 'icon' => 'fa-wave-square', 'tone' => 'muted', 'note' => 'الإيداعات ناقص السحوبات خلال ' . \Carbon\Carbon::now()->translatedFormat('F'), 'suffix' => ' ج.م'],
    ];
    $toneClass = [
        'accent' => 'bg-accent-soft text-accent',
        'metal' => 'bg-metal/15 text-metal',
        'muted' => 'bg-canvas-muted text-muted',
    ];
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المالية · المحافظ</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">المحافظ الذكية</h2>
            <p class="mt-1 text-sm text-muted">إدارة محافظ الدفع المربوطة بالطلاب مع متابعة الأرصدة، المعاملات، وأنواع القنوات المالية المختلفة.</p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <?php if($recentWallet): ?>
                <a href="<?php echo e(route('admin.wallets.reports', $recentWallet)); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                    <i class="fas fa-chart-pie text-xs"></i>
                    تقارير سريعة
                </a>
            <?php endif; ?>
            <a href="<?php echo e(route('admin.wallets.create')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                <i class="fas fa-plus text-xs"></i>
                إضافة محفظة جديدة
            </a>
        </div>
    </section>

    <?php if(session('success')): ?>
        <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft" role="status">
            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent"><i class="fas fa-check text-sm"></i></span>
            <p><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?>

    <div class="flex flex-wrap items-center gap-2">
        <span class="inline-flex items-center gap-2 rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent">
            <span class="size-1.5 rounded-full bg-accent"></span>
            نشطة: <?php echo e(htmlspecialchars($stats['active'] ?? 0)); ?>

        </span>
        <span class="inline-flex items-center gap-2 rounded-lg bg-canvas-muted px-2.5 py-1 text-xs font-medium text-muted">
            <span class="size-1.5 rounded-full bg-muted"></span>
            غير نشطة: <?php echo e(htmlspecialchars($stats['inactive'] ?? 0)); ?>

        </span>
        <span class="inline-flex items-center gap-2 rounded-lg bg-metal/15 px-2.5 py-1 text-xs font-medium text-metal">
            <i class="fas fa-chart-line text-[10px]"></i>
            المعاملات المسجلة: <?php echo e(number_format($totalTransactions ?? 0)); ?>

        </span>
    </div>

    <section class="admin-kpi-grid grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <?php $__currentLoopData = $kpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <div class="inline-flex size-9 items-center justify-center rounded-xl <?php echo e($toneClass[$kpi['tone']]); ?>">
                    <i class="fas <?php echo e($kpi['icon']); ?> text-sm"></i>
                </div>
                <p class="mt-3 text-xs text-muted"><?php echo e($kpi['label']); ?></p>
                <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-ink"><?php echo e($kpi['value']); ?><?php echo e($kpi['suffix'] ?? ''); ?></p>
                <p class="mt-1 text-[11px] text-muted"><?php echo e($kpi['note']); ?></p>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
            <div>
                <h3 class="text-base font-semibold text-ink">تحويل بين المحافظ</h3>
                <p class="mt-0.5 text-xs text-muted">يمكنك تحويل رصيد من محفظة إلى أخرى من محافظك الشخصية فقط.</p>
            </div>
            <span class="inline-flex items-center gap-2 rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent">
                <i class="fas fa-exchange-alt text-[10px]"></i>
                <?php echo e(($transferWallets ?? collect())->count()); ?> محافظ متاحة
            </span>
        </div>

        <?php if(($transferWallets ?? collect())->count() >= 2): ?>
            <form method="POST" action="<?php echo e(route('admin.wallets.transfer')); ?>" class="grid grid-cols-1 gap-4 p-4 sm:p-5 md:grid-cols-2 xl:grid-cols-5 xl:items-end">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="<?php echo e($labelClass); ?>" for="from_wallet_id">من محفظة</label>
                    <select id="from_wallet_id" name="from_wallet_id" class="<?php echo e($fieldClass); ?>" required>
                        <option value="">اختر محفظة المصدر</option>
                        <?php $__currentLoopData = ($transferWallets ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $walletOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($walletOption->id); ?>" <?php echo e((string) old('from_wallet_id') === (string) $walletOption->id ? 'selected' : ''); ?>>
                                <?php echo e($walletOption->name); ?> (<?php echo e(number_format($walletOption->balance, 2)); ?> <?php echo e($walletOption->currency ?? 'EGP'); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['from_wallet_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>" for="to_wallet_id">إلى محفظة</label>
                    <select id="to_wallet_id" name="to_wallet_id" class="<?php echo e($fieldClass); ?>" required>
                        <option value="">اختر محفظة الوجهة</option>
                        <?php $__currentLoopData = ($transferWallets ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $walletOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($walletOption->id); ?>" <?php echo e((string) old('to_wallet_id') === (string) $walletOption->id ? 'selected' : ''); ?>>
                                <?php echo e($walletOption->name); ?> (<?php echo e(number_format($walletOption->balance, 2)); ?> <?php echo e($walletOption->currency ?? 'EGP'); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['to_wallet_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>" for="transfer_amount">المبلغ</label>
                    <input id="transfer_amount" type="number" step="0.01" min="0.01" name="amount" value="<?php echo e(old('amount')); ?>" class="<?php echo e($fieldClass); ?>" placeholder="0.00" required>
                    <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="<?php echo e($labelClass); ?>" for="transfer_notes">ملاحظات (اختياري)</label>
                    <input id="transfer_notes" type="text" name="notes" value="<?php echo e(old('notes')); ?>" class="<?php echo e($fieldClass); ?>" placeholder="سبب التحويل أو مرجع العملية">
                    <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-rose-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <button type="submit" class="btn-press inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                        <i class="fas fa-paper-plane text-xs"></i>
                        تنفيذ التحويل
                    </button>
                </div>
            </form>
        <?php else: ?>
            <div class="mx-4 mb-4 mt-0 rounded-xl border border-line bg-metal/15 px-4 py-3 text-sm text-metal sm:mx-5 sm:mb-5">
                تحتاج إلى محفظتين نشطتين على الأقل لتنفيذ التحويل.
            </div>
        <?php endif; ?>
    </article>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2 xl:col-span-2">
            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                    <div>
                        <h3 class="text-base font-semibold text-ink">نشاط الشهر الحالي</h3>
                        <p class="mt-0.5 text-xs text-muted"><?php echo e(\Carbon\Carbon::now()->translatedFormat('F Y')); ?></p>
                    </div>
                    <span class="inline-flex items-center gap-2 rounded-lg bg-metal/15 px-2.5 py-1 text-xs font-medium text-metal">
                        <i class="fas fa-calendar-alt text-[10px]"></i>
                        <?php echo e(\Carbon\Carbon::now()->translatedFormat('F Y')); ?>

                    </span>
                </div>
                <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 sm:p-5">
                    <div class="rounded-xl border border-line bg-accent-soft/50 p-4">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-accent">الإيداعات</p>
                            <i class="fas fa-arrow-down text-accent"></i>
                        </div>
                        <p class="mt-3 text-2xl font-semibold tabular-nums text-ink">
                            <?php echo e(number_format($currentMonthDeposits ?? 0, 2)); ?> <span class="text-sm font-normal text-muted">ج.م</span>
                        </p>
                    </div>
                    <div class="rounded-xl border border-line bg-canvas-muted/50 p-4">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-muted">السحوبات</p>
                            <i class="fas fa-arrow-up text-muted"></i>
                        </div>
                        <p class="mt-3 text-2xl font-semibold tabular-nums text-ink">
                            <?php echo e(number_format($currentMonthWithdrawals ?? 0, 2)); ?> <span class="text-sm font-normal text-muted">ج.م</span>
                        </p>
                    </div>
                </div>
            </article>

            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">توزيع حسب النوع</h3>
                    <p class="mt-0.5 text-xs text-muted">عدد المحافظ والأرصدة لكل نوع</p>
                </div>
                <div class="space-y-4 p-4 sm:p-5">
                    <?php $__empty_1 = true; $__currentLoopData = $typeDistribution; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex size-10 items-center justify-center rounded-xl bg-accent-soft text-accent">
                                    <i class="fas fa-signal text-sm"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-ink"><?php echo e($type['label']); ?></p>
                                    <p class="text-xs text-muted"><?php echo e(number_format($type['wallets_count'])); ?> محفظة</p>
                                </div>
                            </div>
                            <p class="text-sm font-semibold tabular-nums text-accent">
                                <?php echo e(number_format($type['total_balance'], 2)); ?> <span class="text-xs font-normal text-muted">ج.م</span>
                            </p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-sm text-muted">لا توجد بيانات كافية حالياً.</p>
                    <?php endif; ?>
                </div>
            </article>
        </div>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">أحدث المحافظ المضافة</h3>
                <p class="mt-0.5 text-xs text-muted">آخر المحافظ التي تم إنشاؤها</p>
            </div>
            <div class="space-y-3 p-4 sm:p-5">
                <?php $__empty_1 = true; $__currentLoopData = $recentWallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="rounded-xl border border-line bg-canvas/40 p-4">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-ink"><?php echo e($recent->name ?? 'محفظة بدون اسم'); ?></p>
                            <span class="text-xs text-muted"><?php echo e(optional($recent->created_at)->diffForHumans()); ?></span>
                        </div>
                        <p class="mt-1 text-xs text-accent"><?php echo e($recent->type_name); ?></p>
                        <p class="mt-1 text-xs text-muted">
                            <?php echo e($recent->user?->name ?? 'غير مرتبط'); ?> · <?php echo e($recent->user?->phone ?? 'بدون رقم'); ?>

                        </p>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-sm font-semibold tabular-nums text-ink">
                                <?php echo e(number_format($recent->balance, 2)); ?> <span class="text-xs font-normal text-muted">ج.م</span>
                            </span>
                            <a href="<?php echo e(route('admin.wallets.show', $recent)); ?>" class="text-xs font-medium text-accent hover:underline">
                                تفاصيل <i class="fas fa-arrow-left text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-sm text-muted">لا توجد محافظ حديثة.</p>
                <?php endif; ?>
            </div>
        </article>
    </div>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
            <div>
                <h3 class="text-base font-semibold text-ink">جميع المحافظ</h3>
                <p class="mt-0.5 text-xs text-muted">قائمة المحافظ المفعلة وغير المفعلة مع تفاصيل الاتصال والرصيد</p>
            </div>
            <span class="inline-flex rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent"><?php echo e(number_format($wallets->total())); ?> محفظة</span>
        </div>

        <?php if($wallets->count()): ?>
            <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 sm:p-5 xl:grid-cols-3">
                <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wallet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex flex-col gap-4 rounded-2xl border border-line bg-canvas/30 p-5 transition hover:border-accent/20 hover:shadow-soft">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="text-base font-semibold text-ink"><?php echo e($wallet->name ?? 'محفظة بدون اسم'); ?></h4>
                                    <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-medium <?php echo e($wallet->is_active ? 'bg-accent-soft text-accent' : 'bg-canvas-muted text-muted'); ?>">
                                        <span class="size-1.5 rounded-full <?php echo e($wallet->is_active ? 'bg-accent' : 'bg-muted'); ?>"></span>
                                        <?php echo e($wallet->is_active ? 'نشطة' : 'غير نشطة'); ?>

                                    </span>
                                </div>
                                <p class="mt-1 text-xs text-accent"><?php echo e($wallet->type_name); ?></p>
                                <p class="mt-1 text-xs text-muted">
                                    <?php echo e($wallet->user?->name ?? $wallet->account_holder ?? 'غير محدد'); ?> · <?php echo e($wallet->user?->phone ?? 'بدون رقم'); ?>

                                </p>
                            </div>
                            <span class="inline-flex size-11 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent">
                                <i class="fas fa-wallet"></i>
                            </span>
                        </div>

                        <dl class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <dt class="text-xs text-muted">الرصيد الحالي</dt>
                                <dd class="mt-0.5 font-semibold tabular-nums text-ink"><?php echo e(number_format($wallet->balance, 2)); ?> <span class="text-xs font-normal text-muted">ج.م</span></dd>
                            </div>
                            <div>
                                <dt class="text-xs text-muted">الرصيد المعلّق</dt>
                                <dd class="mt-0.5 font-semibold tabular-nums text-ink"><?php echo e(number_format($wallet->pending_balance ?? 0, 2)); ?> <span class="text-xs font-normal text-muted">ج.م</span></dd>
                            </div>
                            <div>
                                <dt class="text-xs text-muted">رقم الحساب</dt>
                                <dd class="mt-0.5 font-medium text-ink"><?php echo e($wallet->account_number ?? 'غير متوفر'); ?></dd>
                            </div>
                            <div>
                                <dt class="text-xs text-muted">البنك / القناة</dt>
                                <dd class="mt-0.5 font-medium text-ink"><?php echo e($wallet->bank_name ?? 'غير محدد'); ?></dd>
                            </div>
                        </dl>

                        <div class="flex items-center justify-between text-xs text-muted">
                            <span>أضيفت <?php echo e(optional($wallet->created_at)->diffForHumans()); ?></span>
                            <span>آخر تحديث <?php echo e(optional($wallet->updated_at)->diffForHumans()); ?></span>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <a href="<?php echo e(route('admin.wallets.show', $wallet)); ?>" class="btn-press inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-accent-soft px-4 py-2 text-sm font-medium text-accent transition hover:bg-accent/10">
                                <i class="fas fa-eye text-xs"></i>
                                عرض التفاصيل
                            </a>
                            <a href="<?php echo e(route('admin.wallets.transactions', $wallet)); ?>" class="btn-press inline-flex size-9 items-center justify-center rounded-xl border border-line text-ink transition hover:border-accent/30 hover:text-accent" title="سجل المعاملات">
                                <i class="fas fa-receipt text-xs"></i>
                            </a>
                            <a href="<?php echo e(route('admin.wallets.reports', $wallet)); ?>" class="btn-press inline-flex size-9 items-center justify-center rounded-xl border border-line text-ink transition hover:border-accent/30 hover:text-accent" title="التقارير">
                                <i class="fas fa-chart-bar text-xs"></i>
                            </a>
                            <a href="<?php echo e(route('admin.wallets.edit', $wallet)); ?>" class="btn-press inline-flex size-9 items-center justify-center rounded-xl border border-line text-ink transition hover:border-accent/30 hover:text-accent" title="تعديل">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <form action="<?php echo e(route('admin.wallets.destroy', $wallet)); ?>" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من إزالة هذه المحفظة؟ سيتم حذف المحفظة نهائياً.');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn-press inline-flex size-9 items-center justify-center rounded-xl border border-line text-rose-600 transition hover:border-rose-300 hover:bg-rose-50" title="إزالة المحفظة">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <?php if($wallets->hasPages()): ?>
                <div class="border-t border-line px-4 py-4 sm:px-5"><?php echo e($wallets->withQueryString()->links()); ?></div>
            <?php endif; ?>
        <?php else: ?>
            <div class="px-4 py-16 text-center sm:px-5">
                <div class="mx-auto mb-3 inline-flex size-12 items-center justify-center rounded-2xl bg-accent-soft text-accent">
                    <i class="fas fa-inbox"></i>
                </div>
                <p class="text-sm font-medium text-ink">لا توجد محافظ حتى الآن</p>
                <p class="mt-1 text-xs text-muted">يمكنك إنشاء محفظة جديدة من خلال الزر العلوي.</p>
            </div>
        <?php endif; ?>
    </article>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\wallets\index.blade.php ENDPATH**/ ?>