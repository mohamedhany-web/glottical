

<?php $__env->startSection('title', 'تفاصيل الطلب #' . $order->id . ' - ' . config('app.name')); ?>
<?php $__env->startSection('page_title', 'تفاصيل الطلب #' . $order->id); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $statusTone = match ($order->status) {
        'pending' => ['badge' => 'bg-metal/15 text-metal', 'icon' => 'fa-clock', 'hint' => 'جاري المراجعة'],
        'approved' => ['badge' => 'bg-accent-soft text-accent', 'icon' => 'fa-check-circle', 'hint' => 'تمت الموافقة'],
        default => ['badge' => 'bg-canvas-muted text-muted', 'icon' => 'fa-times-circle', 'hint' => 'تم الرفض'],
    };
?>

<div class="space-y-5">

    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المبيعات · الطلبات · #<?php echo e($order->id); ?></p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">تفاصيل الطلب #<?php echo e($order->id); ?></h2>
            <p class="mt-1 text-sm text-muted">
                <i class="fas fa-calendar-alt text-xs"></i>
                <?php echo e($order->created_at->format('d/m/Y - H:i')); ?>

            </p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.orders.index')); ?>"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                العودة للطلبات
            </a>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        
        <div class="space-y-5 xl:col-span-2">
            
            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">معلومات العميل</h3>
                    <p class="mt-0.5 text-xs text-muted">بيانات المتعلم المرتبط بالطلب</p>
                </div>
                <dl class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 sm:p-5">
                    <div>
                        <dt class="text-xs font-medium text-muted">الاسم</dt>
                        <dd class="mt-1 text-sm font-semibold text-ink"><?php echo e(htmlspecialchars($order->user->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8')); ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-muted">رقم الهاتف</dt>
                        <dd class="mt-1 text-sm font-semibold text-ink"><?php echo e(htmlspecialchars($order->user->phone ?? 'غير محدد', ENT_QUOTES, 'UTF-8')); ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-muted">البريد الإلكتروني</dt>
                        <dd class="mt-1 break-all text-sm font-medium text-ink"><?php echo e(htmlspecialchars($order->user->email ?? 'غير محدد', ENT_QUOTES, 'UTF-8')); ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-muted">تاريخ التسجيل</dt>
                        <dd class="mt-1 text-sm font-medium text-ink"><?php echo e($order->user->created_at ? $order->user->created_at->format('d/m/Y') : 'غير محدد'); ?></dd>
                    </div>
                </dl>
            </article>

            
            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">المبيعات والمتابعة</h3>
                    <p class="mt-0.5 text-xs text-muted">تعيين مندوب مبيعات وملاحظات الفريق (تظهر لموظف السيلز أيضاً).</p>
                </div>
                <div class="space-y-5 p-4 sm:p-5">
                    <?php if(session('success')): ?>
                        <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft" role="status">
                            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent"><i class="fas fa-check text-sm"></i></span>
                            <p><?php echo e(session('success')); ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if(session('error')): ?>
                        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert"><?php echo e(session('error')); ?></div>
                    <?php endif; ?>
                    <form action="<?php echo e(route('admin.orders.sales-assign', $order)); ?>" method="POST" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <div class="flex-1">
                            <label class="<?php echo e($labelClass); ?>">مندوب المبيعات</label>
                            <select name="sales_owner_id" class="<?php echo e($fieldClass); ?>">
                                <option value="">— بدون مندوب —</option>
                                <?php $__currentLoopData = $salesEmployees ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $se): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($se->id); ?>" <?php echo e((int) old('sales_owner_id', $order->sales_owner_id) === (int) $se->id ? 'selected' : ''); ?>><?php echo e($se->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <button type="submit" class="btn-press inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                            حفظ
                        </button>
                    </form>
                    <?php if($order->sales_contacted_at): ?>
                        <p class="text-xs text-muted">آخر نشاط مبيعات: <?php echo e($order->sales_contacted_at->format('d/m/Y H:i')); ?></p>
                    <?php endif; ?>
                    <div>
                        <h4 class="mb-3 text-sm font-semibold text-ink">سجل الملاحظات</h4>
                        <div class="mb-4 max-h-56 space-y-3 overflow-y-auto">
                            <?php $__empty_1 = true; $__currentLoopData = $order->salesNotes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="rounded-xl border border-line bg-canvas px-4 py-3 text-sm">
                                    <p class="whitespace-pre-wrap text-ink"><?php echo e($note->body); ?></p>
                                    <p class="mt-2 text-xs text-muted"><?php echo e($note->user?->name); ?> — <?php echo e($note->created_at->format('Y-m-d H:i')); ?></p>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <p class="text-xs text-muted">لا توجد ملاحظات بعد.</p>
                            <?php endif; ?>
                        </div>
                        <form action="<?php echo e(route('admin.orders.sales-notes.store', $order)); ?>" method="POST" class="space-y-3">
                            <?php echo csrf_field(); ?>
                            <textarea name="body" rows="2" required maxlength="5000" class="<?php echo e($areaClass); ?>" placeholder="ملاحظة للفريق…"></textarea>
                            <button type="submit" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink transition hover:border-accent/30 hover:text-accent">
                                إضافة ملاحظة
                            </button>
                        </form>
                    </div>
                </div>
            </article>

            
            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">
                        <?php echo e($order->academic_year_id && ! $order->advanced_course_id ? 'طلب قديم' : 'معلومات الكورس'); ?>

                    </h3>
                </div>
                <div class="p-4 sm:p-5">
                    <div class="flex flex-col gap-4 sm:flex-row">
                        <div class="flex h-24 w-full flex-shrink-0 items-center justify-center rounded-xl bg-accent-soft sm:w-24">
                            <?php if($order->course && $order->course->thumbnail): ?>
                                <img src="<?php echo e(storage_asset($order->course->thumbnail)); ?>" alt="<?php echo e(htmlspecialchars($order->course->title ?? 'كورس', ENT_QUOTES, 'UTF-8')); ?>"
                                     class="h-full w-full rounded-xl object-cover" onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\'%3E%3Cpath fill=\'%230B3D91\' d=\'M8 5v14l11-7z\'/%3E%3C/svg%3E';">
                            <?php else: ?>
                                <i class="fas fa-play-circle text-2xl text-accent"></i>
                            <?php endif; ?>
                        </div>

                        <div class="flex-1">
                            <?php if($order->academic_year_id && ! $order->advanced_course_id): ?>
                                <h4 class="mb-2 text-base font-semibold text-ink"><?php echo e(htmlspecialchars($order->learningPath->name ?? 'طلب قديم', ENT_QUOTES, 'UTF-8')); ?></h4>
                                <div class="mb-3 flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-canvas px-2.5 py-1 text-xs font-medium text-ink-soft">
                                        <i class="fas fa-clock text-xs"></i>
                                        طلب قديم
                                    </span>
                                    <?php if($order->amount): ?>
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent">
                                        <i class="fas fa-money-bill-wave text-xs"></i>
                                        <?php echo e(number_format($order->amount, 2)); ?> ج.م
                                    </span>
                                    <?php endif; ?>
                                </div>
                            <?php elseif($order->course): ?>
                                <h4 class="mb-2 text-base font-semibold text-ink"><?php echo e(htmlspecialchars($order->course->title ?? 'كورس غير محدد', ENT_QUOTES, 'UTF-8')); ?></h4>
                                <?php if($order->course->academicYear || $order->course->academicSubject): ?>
                                <div class="mb-3 flex flex-wrap items-center gap-2">
                                    <?php if($order->course->academicYear): ?>
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent">
                                        <i class="fas fa-graduation-cap text-xs"></i>
                                        <?php echo e(htmlspecialchars($order->course->academicYear->name, ENT_QUOTES, 'UTF-8')); ?>

                                    </span>
                                    <?php endif; ?>
                                    <?php if($order->course->academicSubject): ?>
                                    <span class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-canvas px-2.5 py-1 text-xs font-medium text-ink-soft">
                                        <i class="fas fa-layer-group text-xs"></i>
                                        <?php echo e(htmlspecialchars($order->course->academicSubject->name, ENT_QUOTES, 'UTF-8')); ?>

                                    </span>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                <?php if($order->course->description): ?>
                                    <p class="text-sm text-muted">
                                        <?php echo e(htmlspecialchars(Str::limit($order->course->description, 150), ENT_QUOTES, 'UTF-8')); ?>

                                    </p>
                                <?php endif; ?>
                            <?php else: ?>
                                <h4 class="mb-2 text-base font-semibold text-ink">غير محدد</h4>
                                <p class="text-sm text-muted">لا توجد معلومات متاحة</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </article>

            
            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">تفاصيل الدفع</h3>
                </div>
                <div class="p-4 sm:p-5">
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-medium text-muted">المبلغ</dt>
                            <dd class="mt-1 text-xl font-semibold tabular-nums text-accent">
                                <?php echo e(number_format($order->amount, 2)); ?> <span class="text-sm font-medium text-muted">ج.م</span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-medium text-muted">طريقة الدفع</dt>
                            <dd class="mt-1">
                                <?php if($order->payment_method == 'bank_transfer'): ?>
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent">
                                        <i class="fas fa-university text-xs"></i>
                                        تحويل بنكي
                                    </span>
                                <?php elseif($order->payment_method == 'cash'): ?>
                                    <span class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-canvas px-2.5 py-1 text-xs font-medium text-ink-soft">
                                        <i class="fas fa-money-bill text-xs"></i>
                                        نقدي
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-canvas px-2.5 py-1 text-xs font-medium text-muted">
                                        <i class="fas fa-question-circle text-xs"></i>
                                        أخرى
                                    </span>
                                <?php endif; ?>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-medium text-muted">تاريخ الطلب</dt>
                            <dd class="mt-1 text-sm font-medium text-ink">
                                <?php echo e($order->created_at->format('d/m/Y')); ?>

                                <span class="font-normal text-muted">- <?php echo e($order->created_at->format('H:i')); ?></span>
                            </dd>
                        </div>

                        <?php if($order->approved_at): ?>
                        <div>
                            <dt class="text-xs font-medium text-muted">تاريخ المراجعة</dt>
                            <dd class="mt-1 text-sm font-medium text-ink">
                                <?php echo e($order->approved_at->format('d/m/Y')); ?>

                                <span class="font-normal text-muted">- <?php echo e($order->approved_at->format('H:i')); ?></span>
                            </dd>
                        </div>
                        <?php endif; ?>
                    </dl>

                    <?php if($order->wallet): ?>
                    <div class="mt-4 rounded-xl border border-line bg-canvas px-4 py-3">
                        <p class="mb-1.5 text-xs font-medium text-muted">حساب الاستلام على المنصة</p>
                        <div class="text-sm font-semibold text-ink">
                            <?php echo e($order->wallet->name ?? \App\Models\Wallet::typeLabel($order->wallet->type)); ?>

                            <?php if($order->wallet->account_number): ?>
                                <span class="font-mono font-medium text-ink-soft"> — <?php echo e($order->wallet->account_number); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php elseif(in_array($order->payment_method, ['bank_transfer', 'wallet'], true)): ?>
                    <div class="mt-4 rounded-xl border border-line bg-canvas px-4 py-3">
                        <p class="flex items-center gap-2 text-sm text-ink-soft">
                            <i class="fas fa-exclamation-triangle text-metal"></i>
                            لم يُحدَّد حساب استلام على المنصة لهذا الطلب؛ ولن يُسجَّل رصيد على المحفظة عند الموافقة حتى يتم التحديد.
                        </p>
                    </div>
                    <?php endif; ?>

                    <?php if($order->status === \App\Models\Order::STATUS_PENDING && in_array($order->payment_method, ['bank_transfer', 'wallet'], true) && isset($platformWallets) && $platformWallets->count() > 0): ?>
                    <div class="mt-4 rounded-xl border border-line bg-canvas p-4">
                        <h4 class="mb-1 text-sm font-semibold text-ink">حساب التحويل على المنصة (للإيداع عند الموافقة)</h4>
                        <p class="mb-4 text-xs text-muted">اختر المحفظة التي استلمتم عليها التحويل. عند الموافقة يُضاف المبلغ تلقائياً لرصيدها مع قيد في معاملات المحفظة.</p>
                        <form action="<?php echo e(route('admin.orders.receiving-wallet', $order)); ?>" method="post" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <div class="flex-1">
                                <label for="receiving_wallet_id" class="<?php echo e($labelClass); ?>">الحساب</label>
                                <select name="wallet_id" id="receiving_wallet_id" required class="<?php echo e($fieldClass); ?>">
                                    <option value="">— اختر —</option>
                                    <?php $__currentLoopData = $platformWallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($w->id); ?>" <?php if((string) old('wallet_id', $order->wallet_id) === (string) $w->id): echo 'selected'; endif; ?>>
                                            <?php echo e($w->name ?? \App\Models\Wallet::typeLabel($w->type)); ?><?php if($w->account_number): ?> — <?php echo e($w->account_number); ?><?php endif; ?>
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <button type="submit" class="btn-press inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                                <i class="fas fa-save text-xs"></i>
                                حفظ
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>

                    <?php if($order->notes): ?>
                        <div class="mt-4 border-t border-line pt-4">
                            <p class="text-xs font-medium text-muted">ملاحظات العميل</p>
                            <div class="mt-2 whitespace-pre-wrap rounded-xl bg-canvas px-4 py-3 text-sm text-ink">
                                <?php echo e(htmlspecialchars($order->notes, ENT_QUOTES, 'UTF-8')); ?>

                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </article>

            
            <?php if($order->payment_proof): ?>
                <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                    <div class="border-b border-line px-4 py-4 sm:px-5">
                        <h3 class="text-base font-semibold text-ink">إيصال الدفع</h3>
                        <p class="mt-0.5 text-xs text-muted">اضغط على الصورة لعرضها بحجم أكبر</p>
                    </div>
                    <div class="p-4 sm:p-5">
                        <div class="text-center">
                            <div class="inline-block rounded-xl border border-line bg-canvas p-3">
                                <?php
                                    $fullPath = storage_path('app/public/' . $order->payment_proof);
                                    $imageExists = file_exists($fullPath);

                                    $imageUrl = null;
                                    if ($imageExists) {
                                        $imageUrl = storage_asset($order->payment_proof);
                                        if (!file_exists(public_path('storage/' . $order->payment_proof))) {
                                            try {
                                                $imageUrl = route('storage.file', ['path' => htmlspecialchars($order->payment_proof, ENT_QUOTES, 'UTF-8')]);
                                            } catch (\Exception $e) {
                                                $imageUrl = url('/storage/' . htmlspecialchars($order->payment_proof, ENT_QUOTES, 'UTF-8'));
                                            }
                                        }
                                    }
                                ?>
                                <?php if($imageExists): ?>
                                <img src="<?php echo e(htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8')); ?>"
                                     alt="إيصال الدفع"
                                     class="h-auto max-w-full cursor-pointer rounded-xl transition hover:opacity-90"
                                     onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';"
                                     onclick="openImageModal(this.src)">
                                <div class="hidden rounded-xl border border-line bg-canvas px-4 py-3">
                                    <p class="flex items-center gap-2 text-sm text-ink-soft">
                                        <i class="fas fa-exclamation-triangle text-metal"></i>
                                        <span>الصورة غير متوفرة حالياً</span>
                                    </p>
                                </div>
                                <?php else: ?>
                                <div class="rounded-xl border border-line bg-canvas px-4 py-3">
                                    <p class="flex items-center gap-2 text-sm text-ink-soft">
                                        <i class="fas fa-exclamation-triangle text-metal"></i>
                                        <span>الصورة غير موجودة في الخادم</span>
                                    </p>
                                </div>
                                <?php endif; ?>
                            </div>
                            <p class="mt-4 flex items-center justify-center gap-2 text-xs text-muted">
                                <i class="fas fa-info-circle"></i>
                                اضغط على الصورة لعرضها بحجم أكبر
                            </p>
                        </div>
                    </div>
                </article>
            <?php endif; ?>
        </div>

        
        <div class="space-y-5">
            <article class="sticky top-8 overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                    <div>
                        <h3 class="text-base font-semibold text-ink">حالة الطلب</h3>
                        <p class="mt-0.5 text-xs text-muted">الموافقة أو الرفض</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-medium <?php echo e($statusTone['badge']); ?>">
                        <i class="fas <?php echo e($statusTone['icon']); ?> text-xs"></i>
                        <?php echo e(htmlspecialchars($order->status_text ?? 'غير محدد', ENT_QUOTES, 'UTF-8')); ?>

                    </span>
                </div>

                <div class="p-4 sm:p-5">
                    <div class="mb-6 text-center">
                        <div class="mx-auto mb-4 flex size-14 items-center justify-center rounded-xl <?php echo e($statusTone['badge']); ?>">
                            <i class="fas <?php echo e($statusTone['icon']); ?> text-2xl"></i>
                        </div>

                        <div class="mb-1 text-lg font-semibold text-ink">
                            <?php echo e(htmlspecialchars($order->status_text ?? 'غير محدد', ENT_QUOTES, 'UTF-8')); ?>

                        </div>
                        <p class="text-sm text-muted"><?php echo e($statusTone['hint']); ?></p>
                    </div>

                    <?php if($order->status == 'pending'): ?>
                        <script>
                            // تعريف الدوال مباشرة قبل الأزرار لضمان توفرها
                            (function() {
                                let isProcessing = false;

                                window.approveOrder = async function(orderId) {
                                    console.log('approveOrder called with orderId:', orderId);

                                    if (isProcessing) {
                                        console.log('Already processing, returning...');
                                        return;
                                    }

                                    const confirmed = confirm('هل أنت متأكد من الموافقة على هذا الطلب؟\nسيتم تفعيل الكورس للعميل تلقائياً.');
                                    if (!confirmed) {
                                        console.log('User cancelled approval');
                                        return;
                                    }

                                    console.log('Starting approval process...');

                                    const approveBtn = document.getElementById('approveBtn');
                                    const rejectBtn = document.getElementById('rejectBtn');
                                    const originalApproveText = approveBtn ? approveBtn.innerHTML : '';

                                    isProcessing = true;
                                    if (approveBtn) {
                                        approveBtn.disabled = true;
                                        approveBtn.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري المعالجة...';
                                    }
                                    if (rejectBtn) rejectBtn.disabled = true;

                                    try {
                                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                                        if (!csrfToken) {
                                            alert('خطأ: لم يتم العثور على CSRF token');
                                            if (approveBtn) {
                                                approveBtn.disabled = false;
                                                approveBtn.innerHTML = originalApproveText;
                                            }
                                            if (rejectBtn) rejectBtn.disabled = false;
                                            isProcessing = false;
                                            return;
                                        }

                                        console.log('CSRF Token found, making request...');

                                        const formData = new FormData();
                                        formData.append('_token', csrfToken);

                                        const controller = new AbortController();
                                        const timeoutId = setTimeout(() => controller.abort(), 120000);

                                        console.log('Fetching:', `/admin/orders/${orderId}/approve`);

                                        const response = await fetch(`/admin/orders/${orderId}/approve`, {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': csrfToken,
                                                'Accept': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest'
                                            },
                                            body: formData,
                                            signal: controller.signal
                                        });

                                        clearTimeout(timeoutId);

                                        const contentType = response.headers.get('content-type');
                                        let data;

                                        if (contentType && contentType.includes('application/json')) {
                                            data = await response.json();
                                        } else {
                                            if (response.redirected || response.status === 200) {
                                                window.location.reload();
                                                return;
                                            }
                                            const text = await response.text();
                                            throw new Error(text || 'حدث خطأ أثناء المعالجة');
                                        }

                                        if (response.ok) {
                                            if (data.redirect) {
                                                window.location.href = data.redirect;
                                            } else {
                                                window.location.reload();
                                            }
                                        } else {
                                            console.error('Error Response Data:', data);
                                            const errorMsg = data.error || data.message || 'حدث خطأ أثناء المعالجة';
                                            const errorDetails = data.file && data.line ? `\n\nالملف: ${data.file}\nالسطر: ${data.line}` : '';
                                            alert(errorMsg + errorDetails);

                                            if (approveBtn) {
                                                approveBtn.disabled = false;
                                                approveBtn.innerHTML = originalApproveText;
                                            }
                                            if (rejectBtn) rejectBtn.disabled = false;
                                            isProcessing = false;
                                        }
                                    } catch (error) {
                                        console.error('Error in approveOrder:', error);
                                        let errorMessage = '';

                                        if (error.name === 'AbortError') {
                                            errorMessage = 'انتهت مهلة الانتظار. العملية تستغرق وقتاً طويلاً. يرجى المحاولة مرة أخرى أو مراجعة السجلات.';
                                        } else if (error.message) {
                                            errorMessage = error.message;
                                        } else {
                                            errorMessage = 'حدث خطأ غير معروف أثناء الموافقة على الطلب: ' + error.toString();
                                        }

                                        alert(errorMessage);

                                        if (approveBtn) {
                                            approveBtn.disabled = false;
                                            approveBtn.innerHTML = originalApproveText;
                                        }
                                        if (rejectBtn) rejectBtn.disabled = false;
                                        isProcessing = false;
                                    }
                                };

                                window.rejectOrder = async function(orderId) {
                                    if (isProcessing) return;

                                    const confirmed = confirm('هل أنت متأكد من رفض هذا الطلب؟');
                                    if (!confirmed) return;

                                    const approveBtn = document.getElementById('approveBtn');
                                    const rejectBtn = document.getElementById('rejectBtn');
                                    const originalRejectText = rejectBtn ? rejectBtn.innerHTML : '';

                                    isProcessing = true;
                                    if (approveBtn) approveBtn.disabled = true;
                                    if (rejectBtn) {
                                        rejectBtn.disabled = true;
                                        rejectBtn.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري المعالجة...';
                                    }

                                    try {
                                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                                        const formData = new FormData();
                                        formData.append('_token', csrfToken);

                                        const controller = new AbortController();
                                        const timeoutId = setTimeout(() => controller.abort(), 60000);

                                        const response = await fetch(`/admin/orders/${orderId}/reject`, {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': csrfToken,
                                                'Accept': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest'
                                            },
                                            body: formData,
                                            signal: controller.signal
                                        });

                                        clearTimeout(timeoutId);

                                        const contentType = response.headers.get('content-type');
                                        let data;

                                        if (contentType && contentType.includes('application/json')) {
                                            data = await response.json();
                                        } else {
                                            if (response.redirected || response.status === 200) {
                                                window.location.reload();
                                                return;
                                            }
                                            const text = await response.text();
                                            throw new Error(text || 'حدث خطأ أثناء المعالجة');
                                        }

                                        if (response.ok) {
                                            if (data.redirect) {
                                                window.location.href = data.redirect;
                                            } else {
                                                window.location.reload();
                                            }
                                        } else {
                                            console.error('Error Response Data:', data);
                                            const errorMsg = data.error || data.message || 'حدث خطأ أثناء المعالجة';
                                            const errorDetails = data.file && data.line ? `\n\nالملف: ${data.file}\nالسطر: ${data.line}` : '';
                                            alert(errorMsg + errorDetails);

                                            if (approveBtn) approveBtn.disabled = false;
                                            if (rejectBtn) {
                                                rejectBtn.disabled = false;
                                                rejectBtn.innerHTML = originalRejectText;
                                            }
                                            isProcessing = false;
                                        }
                                    } catch (error) {
                                        console.error('Error:', error);
                                        let errorMessage = '';

                                        if (error.name === 'AbortError') {
                                            errorMessage = 'انتهت مهلة الانتظار. يرجى المحاولة مرة أخرى.';
                                        } else if (error.message) {
                                            errorMessage = error.message;
                                        } else {
                                            errorMessage = 'حدث خطأ غير معروف أثناء رفض الطلب';
                                        }

                                        alert(errorMessage);

                                        if (approveBtn) approveBtn.disabled = false;
                                        if (rejectBtn) {
                                            rejectBtn.disabled = false;
                                            rejectBtn.innerHTML = originalRejectText;
                                        }
                                        isProcessing = false;
                                    }
                                };

                                console.log('Order approval functions ready:', typeof window.approveOrder, typeof window.rejectOrder);
                            })();
                        </script>
                        <div class="space-y-3">
                            <button type="button"
                                    id="approveBtn"
                                    onclick="window.approveOrder(<?php echo e($order->id); ?>); return false;"
                                    class="btn-press inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white disabled:cursor-not-allowed disabled:opacity-50">
                                <i class="fas fa-check text-xs"></i>
                                الموافقة على الطلب
                            </button>

                            <button type="button"
                                    id="rejectBtn"
                                    onclick="window.rejectOrder(<?php echo e($order->id); ?>); return false;"
                                    class="btn-press inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-rose-600 transition hover:border-rose-300 hover:bg-rose-50 disabled:cursor-not-allowed disabled:opacity-50">
                                <i class="fas fa-times text-xs"></i>
                                رفض الطلب
                            </button>
                        </div>
                    <?php elseif($order->status == 'approved'): ?>
                        <div class="rounded-xl border border-line bg-canvas px-4 py-3">
                            <p class="flex items-start gap-2 text-sm text-ink">
                                <i class="fas fa-check-circle mt-0.5 text-accent"></i>
                                <span>تمت الموافقة على الطلب وتم تفعيل الكورس للعميل.</span>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="rounded-xl border border-line bg-canvas px-4 py-3">
                            <p class="flex items-start gap-2 text-sm text-ink-soft">
                                <i class="fas fa-exclamation-circle mt-0.5 text-muted"></i>
                                <span>تم رفض هذا الطلب.</span>
                            </p>
                        </div>
                    <?php endif; ?>

                    <?php if($order->approver): ?>
                        <div class="mt-6 border-t border-line pt-5">
                            <p class="mb-3 text-xs font-medium text-muted">تمت المراجعة بواسطة:</p>
                            <div class="flex items-center gap-3">
                                <div class="flex size-11 items-center justify-center rounded-xl bg-accent-soft text-sm font-semibold text-accent">
                                    <?php echo e(mb_substr(htmlspecialchars($order->approver->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8'), 0, 1)); ?>

                                </div>
                                <div>
                                    <p class="font-semibold text-ink"><?php echo e(htmlspecialchars($order->approver->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8')); ?></p>
                                    <?php if($order->approved_at): ?>
                                        <p class="text-xs text-muted"><?php echo e($order->approved_at->format('d/m/Y - H:i')); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </article>
        </div>
    </div>
</div>


<div id="imageModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/90 backdrop-blur-sm" onclick="closeImageModal()">
    <div class="relative max-h-[90vh] max-w-5xl p-4">
        <button onclick="closeImageModal()" class="absolute -top-12 left-0 text-3xl font-bold text-white transition hover:text-white/70">
            <i class="fas fa-times-circle"></i>
        </button>
        <img id="modalImage" src="" alt="إيصال الدفع" class="max-h-[90vh] max-w-full rounded-xl object-contain shadow-2xl" onerror="closeImageModal();">
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    // الكود الرئيسي موجود في inline script قبل الأزرار
    // هذا القسم فقط للدوال المساعدة الأخرى

    function openImageModal(src) {
        // حماية من XSS - التحقق من URL
        if (!src || !src.startsWith('http') && !src.startsWith('/')) {
            return;
        }
        const img = document.getElementById('modalImage');
        if (img) {
            img.src = src;
            document.getElementById('imageModal').classList.remove('hidden');
            document.getElementById('imageModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
            const img = document.getElementById('modalImage');
            if (img) {
                img.src = '';
            }
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
        }
    });

</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\orders\show.blade.php ENDPATH**/ ?>