<?php $__env->startSection('title', 'إدارة الطلبات'); ?>
<?php $__env->startSection('page_title', 'إدارة الطلبات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $kpis = [
        ['label' => 'إجمالي الطلبات', 'value' => $stats['total'], 'icon' => 'fa-shopping-cart', 'tone' => 'accent', 'note' => 'كل الطلبات المسجلة'],
        ['label' => 'قيد المراجعة', 'value' => $stats['pending'], 'icon' => 'fa-hourglass-half', 'tone' => 'metal', 'note' => 'بانتظار الموافقة أو الرفض'],
        ['label' => 'مكتملة', 'value' => $stats['approved'], 'icon' => 'fa-check-circle', 'tone' => 'accent', 'note' => 'تمت الموافقة عليها'],
        ['label' => 'مرفوضة', 'value' => $stats['rejected'], 'icon' => 'fa-times-circle', 'tone' => 'muted', 'note' => 'تم رفضها بعد المراجعة'],
    ];
    $toneClass = [
        'accent' => 'bg-accent-soft text-accent',
        'metal' => 'bg-metal/15 text-metal',
        'muted' => 'bg-canvas-muted text-muted',
    ];
    $statusBadges = [
        'pending' => ['label' => 'في الانتظار', 'classes' => 'bg-metal/15 text-metal'],
        'approved' => ['label' => 'مقبولة', 'classes' => 'bg-accent-soft text-accent'],
        'rejected' => ['label' => 'مرفوضة', 'classes' => 'bg-canvas-muted text-muted'],
    ];
    $paymentMethodLabels = [
        'bank_transfer' => 'تحويل بنكي',
        'wallet' => 'محفظة إلكترونية',
        'online' => 'دفع إلكتروني',
        'cash' => 'نقدي',
        'other' => 'أخرى',
    ];
    $avgAmount = $stats['total'] > 0 ? (float) \App\Models\Order::avg('amount') : 0;
    $monthCount = \App\Models\Order::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
    $acceptRate = $stats['total'] > 0 ? round(($stats['approved'] / $stats['total']) * 100, 1) : 0;
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المبيعات · الطلبات</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">إدارة الطلبات</h2>
            <p class="mt-1 text-sm text-muted">متابعة تسجيلات شراء الكورسات والموافقة عليها</p>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.sales.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-chart-pie text-xs"></i>
                تحليلات المبيعات
            </a>
            <a href="<?php echo e(route('admin.crm.leads.index')); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                <i class="fas fa-users text-xs"></i>
                CRM
            </a>
        </div>
    </section>

    <?php if(session('success')): ?>
        <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft" role="status">
            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent"><i class="fas fa-check text-sm"></i></span>
            <p><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?>

    <section class="admin-kpi-grid grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <?php $__currentLoopData = $kpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <div class="inline-flex size-9 items-center justify-center rounded-xl <?php echo e($toneClass[$kpi['tone']]); ?>">
                    <i class="fas <?php echo e($kpi['icon']); ?> text-sm"></i>
                </div>
                <p class="mt-3 text-xs text-muted"><?php echo e($kpi['label']); ?></p>
                <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-ink"><?php echo e(number_format($kpi['value'])); ?></p>
                <p class="mt-1 text-[11px] text-muted"><?php echo e($kpi['note']); ?></p>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">البحث والفلترة</h3>
            <p class="mt-0.5 text-xs text-muted">حسب الحالة، طريقة الدفع، المندوب، أو بيانات العميل/الكورس</p>
        </div>
        <form method="GET" id="filterForm" action="<?php echo e(route('admin.orders.index')); ?>" class="grid grid-cols-1 gap-4 p-4 sm:p-5 md:grid-cols-2 xl:grid-cols-5 xl:items-end">
            <div class="xl:col-span-2">
                <label class="<?php echo e($labelClass); ?>" for="search">البحث</label>
                <input id="search" type="search" name="search" value="<?php echo e(old('search', request('search'))); ?>" maxlength="255" placeholder="اسم، بريد، هاتف، أو كورس…" class="<?php echo e($fieldClass); ?>">
            </div>
            <div>
                <label class="<?php echo e($labelClass); ?>" for="status">الحالة</label>
                <select id="status" name="status" class="<?php echo e($fieldClass); ?>">
                    <option value="">جميع الحالات</option>
                    <option value="pending" <?php if(request('status') === 'pending'): echo 'selected'; endif; ?>>في الانتظار</option>
                    <option value="approved" <?php if(request('status') === 'approved'): echo 'selected'; endif; ?>>مقبولة</option>
                    <option value="rejected" <?php if(request('status') === 'rejected'): echo 'selected'; endif; ?>>مرفوضة</option>
                </select>
            </div>
            <div>
                <label class="<?php echo e($labelClass); ?>" for="payment_method">طريقة الدفع</label>
                <select id="payment_method" name="payment_method" class="<?php echo e($fieldClass); ?>">
                    <option value="">جميع الطرق</option>
                    <option value="bank_transfer" <?php if(request('payment_method') === 'bank_transfer'): echo 'selected'; endif; ?>>تحويل بنكي</option>
                    <option value="online" <?php if(request('payment_method') === 'online'): echo 'selected'; endif; ?>>دفع إلكتروني</option>
                    <option value="cash" <?php if(request('payment_method') === 'cash'): echo 'selected'; endif; ?>>نقدي</option>
                    <option value="other" <?php if(request('payment_method') === 'other'): echo 'selected'; endif; ?>>أخرى</option>
                </select>
            </div>
            <div>
                <label class="<?php echo e($labelClass); ?>" for="sales_owner_id">مندوب المبيعات</label>
                <select id="sales_owner_id" name="sales_owner_id" class="<?php echo e($fieldClass); ?>">
                    <option value="">الكل</option>
                    <option value="unassigned" <?php if(request('sales_owner_id') === 'unassigned'): echo 'selected'; endif; ?>>بدون مندوب</option>
                    <?php $__currentLoopData = $salesEmployees ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $se): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($se->id); ?>" <?php if((string) request('sales_owner_id') === (string) $se->id): echo 'selected'; endif; ?>><?php echo e($se->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex flex-wrap gap-2 xl:col-span-5">
                <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                    <i class="fas fa-filter text-xs"></i>
                    تطبيق
                </button>
                <?php if(request()->anyFilled(['search', 'status', 'payment_method', 'sales_owner_id'])): ?>
                    <a href="<?php echo e(route('admin.orders.index')); ?>" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line px-5 text-sm font-medium text-ink hover:bg-canvas">
                        <i class="fas fa-times text-xs"></i>
                        مسح
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </article>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
            <div>
                <h3 class="text-base font-semibold text-ink">قائمة الطلبات</h3>
                <p class="mt-0.5 text-xs text-muted">من الأحدث إلى الأقدم</p>
            </div>
            <span class="inline-flex rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent"><?php echo e(number_format($orders->total())); ?> طلب</span>
        </div>

        <div class="admin-table-wrap overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-line bg-canvas/60 text-xs text-muted">
                    <tr>
                        <th class="px-4 py-3 text-start font-medium">#</th>
                        <th class="px-4 py-3 text-start font-medium">العميل</th>
                        <th class="px-4 py-3 text-start font-medium">الكورس / المسار</th>
                        <th class="px-4 py-3 text-start font-medium">المبلغ</th>
                        <th class="px-4 py-3 text-start font-medium">الدفع</th>
                        <th class="px-4 py-3 text-start font-medium">المندوب</th>
                        <th class="px-4 py-3 text-start font-medium">الحالة</th>
                        <th class="px-4 py-3 text-start font-medium">التاريخ</th>
                        <th class="px-4 py-3 text-start font-medium">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $badge = $statusBadges[$order->status] ?? null; ?>
                        <tr class="hover:bg-canvas/40">
                            <td class="px-4 py-3 tabular-nums text-muted"><?php echo e($order->id); ?></td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-ink"><?php echo e($order->user->name ?? '—'); ?></p>
                                <p class="mt-0.5 text-[11px] text-muted"><?php echo e($order->user->email ?? $order->user->phone ?? '—'); ?></p>
                            </td>
                            <td class="px-4 py-3">
                                <?php if($order->academic_year_id && ! $order->advanced_course_id): ?>
                                    <p class="font-medium text-ink"><?php echo e($order->learningPath->name ?? 'طلب قديم'); ?></p>
                                    <p class="text-[11px] text-muted">طلب قديم</p>
                                <?php elseif($order->course): ?>
                                    <p class="font-medium text-ink"><?php echo e($order->course->title ?? 'كورس'); ?></p>
                                    <?php if($order->course->academicYear || $order->course->academicSubject): ?>
                                        <p class="text-[11px] text-muted">
                                            <?php echo e(optional($order->course->academicYear)->name); ?><?php echo e($order->course->academicSubject ? ' · '.$order->course->academicSubject->name : ''); ?>

                                        </p>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 font-semibold tabular-nums text-ink"><?php echo e(number_format($order->amount, 2)); ?> <span class="text-xs font-normal text-muted">ج.م</span></td>
                            <td class="px-4 py-3 text-ink-soft"><?php echo e($paymentMethodLabels[$order->payment_method] ?? $order->payment_method ?? '—'); ?></td>
                            <td class="px-4 py-3 text-ink-soft">
                                <?php if($order->salesOwner): ?>
                                    <?php echo e($order->salesOwner->name); ?>

                                <?php elseif($order->status === 'pending'): ?>
                                    <span class="text-metal">بدون مندوب</span>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php if($badge): ?>
                                    <span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-medium <?php echo e($badge['classes']); ?>"><?php echo e($badge['label']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-xs tabular-nums text-muted"><?php echo e($order->created_at->format('Y-m-d H:i')); ?></td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1.5">
                                    <a href="<?php echo e(route('admin.orders.show', $order)); ?>" class="btn-press inline-flex h-8 items-center rounded-lg border border-line px-3 text-xs font-medium text-ink hover:border-accent/30 hover:text-accent" title="التفاصيل">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if($order->status === 'pending'): ?>
                                        <button type="button" class="approve-btn btn-press inline-flex h-8 items-center rounded-lg border border-line px-3 text-xs font-medium text-accent hover:border-accent/40" title="موافقة" data-order-id="<?php echo e($order->id); ?>" data-url="<?php echo e(route('admin.orders.approve', $order)); ?>">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="reject-btn btn-press inline-flex h-8 items-center rounded-lg border border-line px-3 text-xs font-medium text-rose-600 hover:border-rose-300 hover:bg-rose-50" title="رفض" data-order-id="<?php echo e($order->id); ?>" data-url="<?php echo e(route('admin.orders.reject', $order)); ?>">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="px-4 py-16 text-center">
                                <div class="mx-auto mb-3 inline-flex size-12 items-center justify-center rounded-2xl bg-accent-soft text-accent">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <p class="text-sm font-medium text-ink">لا توجد طلبات</p>
                                <p class="mt-1 text-xs text-muted">لا توجد نتائج مطابقة للفلاتر الحالية.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($orders->hasPages()): ?>
            <div class="border-t border-line px-4 py-4 sm:px-5"><?php echo e($orders->appends(request()->query())->links()); ?></div>
        <?php endif; ?>
    </article>

    <section class="admin-kpi-grid grid gap-3 sm:grid-cols-3">
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-accent-soft text-accent">
                <i class="fas fa-percentage text-sm"></i>
            </div>
            <p class="mt-3 text-xs text-muted">معدل القبول</p>
            <p class="mt-1 text-xl font-semibold tabular-nums text-ink"><?php echo e($acceptRate); ?>%</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-metal/15 text-metal">
                <i class="fas fa-calendar text-sm"></i>
            </div>
            <p class="mt-3 text-xs text-muted">طلبات هذا الشهر</p>
            <p class="mt-1 text-xl font-semibold tabular-nums text-ink"><?php echo e(number_format($monthCount)); ?></p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-canvas-muted text-muted">
                <i class="fas fa-coins text-sm"></i>
            </div>
            <p class="mt-3 text-xs text-muted">متوسط قيمة الطلب</p>
            <p class="mt-1 text-xl font-semibold tabular-nums text-ink"><?php echo e(number_format($avgAmount, 2)); ?> <span class="text-sm font-normal text-muted">ج.م</span></p>
        </article>
    </section>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function() {
    var csrfToken = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    if (!csrfToken) return;

    function sendRequest(url, btn) {
        var formData = new FormData();
        formData.append('_token', csrfToken);
        var originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(function(response) {
            var contentType = response.headers.get('content-type');
            if (contentType && contentType.indexOf('application/json') !== -1) {
                return response.json();
            }
            if (response.ok) {
                window.location.reload();
                return;
            }
            return response.text().then(function(text) { throw new Error(text || 'حدث خطأ أثناء المعالجة'); });
        })
        .then(function(data) {
            if (data && data.success) {
                if (data.message) alert(data.message);
                window.location.reload();
            } else if (data) {
                alert((data.error || data.message) || 'حدث خطأ أثناء المعالجة');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        })
        .catch(function(err) {
            alert(err.message || 'حدث خطأ أثناء المعالجة');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    }

    document.querySelectorAll('.approve-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (!confirm('هل أنت متأكد من الموافقة على هذا الطلب؟\nسيتم تفعيل الكورس تلقائياً.')) return;
            sendRequest(btn.getAttribute('data-url'), btn);
        });
    });

    document.querySelectorAll('.reject-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (!confirm('هل أنت متأكد من رفض هذا الطلب؟')) return;
            sendRequest(btn.getAttribute('data-url'), btn);
        });
    });

    var filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function() {
            var searchInput = this.querySelector('input[name="search"]');
            if (searchInput) searchInput.value = searchInput.value.replace(/<[^>]*>/g, '').trim();
        });
    }
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\orders\index.blade.php ENDPATH**/ ?>