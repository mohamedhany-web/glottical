

<?php $__env->startSection('title', 'إدارة الطلاب والحسابات - Glottical'); ?>
<?php $__env->startSection('page_title', 'إدارة الطلاب والحسابات'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $stats = $stats ?? [];
    $users = $users ?? collect();
    $recentUsers = $recentUsers ?? collect();
    $recentlyActiveUsers = $recentlyActiveUsers ?? collect();
    $usersByMonth = $usersByMonth ?? collect();
    $trend = $stats['trend'] ?? null;

    $kpis = [
        ['label' => 'إجمالي الطلاب', 'value' => $stats['total'] ?? 0, 'icon' => 'fa-user-graduate', 'tone' => 'accent', 'note' => 'كل حسابات الطلاب'],
        ['label' => 'نشطون', 'value' => $stats['active'] ?? 0, 'icon' => 'fa-user-check', 'tone' => 'accent', 'note' => 'حسابات مفعّلة'],
        ['label' => 'غير نشطين', 'value' => $stats['inactive'] ?? 0, 'icon' => 'fa-user-slash', 'tone' => 'muted', 'note' => 'حسابات موقوفة'],
        ['label' => 'جدد هذا الشهر', 'value' => $stats['new_this_month'] ?? 0, 'icon' => 'fa-user-plus', 'tone' => 'metal', 'note' => 'تسجيلات الشهر الحالي'],
    ];
    $toneClass = [
        'accent' => 'bg-accent-soft text-accent',
        'metal' => 'bg-metal/15 text-metal',
        'muted' => 'bg-canvas-muted text-muted',
    ];
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $monthNames = [
        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
        5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
        9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
    ];
    $maxMonthCount = max(1, (int) ($usersByMonth->max('count') ?: 1));
?>

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">الحسابات · متابعة طلاب المنصة ونشاطهم</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">إدارة الطلاب والحسابات</h2>
            <?php if(is_array($trend) && isset($trend['difference'])): ?>
                <?php
                    $diff = (int) round($trend['difference']);
                    $percent = (float) ($trend['percent'] ?? 0);
                    $positive = $diff >= 0;
                ?>
                <p class="mt-1 text-sm text-muted">
                    تسجيلات هذا الشهر مقارنة بالسابق:
                    <span class="font-semibold <?php echo e($positive ? 'text-accent' : 'text-danger'); ?>">
                        <?php echo e($positive ? '+' : ''); ?><?php echo e(number_format($diff)); ?>

                        (<?php echo e($percent >= 0 ? '+' : ''); ?><?php echo e(number_format($percent, 1)); ?>%)
                    </span>
                </p>
            <?php endif; ?>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="<?php echo e(route('admin.users.create', ['from' => 'students', 'role' => 'student'])); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                <i class="fas fa-user-plus text-xs"></i>
                إضافة طالب
            </a>
        </div>
    </section>

    <?php if(request('created') == '1' || session('success') || request('updated') == '1'): ?>
        <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft" role="status">
            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent"><i class="fas fa-check text-sm"></i></span>
            <p><?php echo e(session('success', request('created') == '1' ? 'تم إنشاء الحساب بنجاح.' : 'تم التعديل بنجاح.')); ?></p>
        </div>
    <?php endif; ?>
    <?php if(session('warning') || isset($warning)): ?>
        <div class="flex items-center gap-3 rounded-2xl border border-metal/30 bg-canvas px-4 py-3 text-sm font-medium text-ink shadow-soft" role="status">
            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-metal/15 text-metal"><i class="fas fa-exclamation-triangle text-sm"></i></span>
            <p><?php echo e(session('warning', $warning ?? '')); ?></p>
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
            <h3 class="text-base font-semibold text-ink">اختصارات سريعة</h3>
            <p class="mt-0.5 text-xs text-muted">خدمات Glottical الحالية للطالب: كورسات ومجموعات</p>
        </div>
        <div class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-3 sm:p-5">
            <?php if(Route::has('admin.tutoring-groups.index')): ?>
                <a href="<?php echo e(route('admin.tutoring-groups.index', 'individual')); ?>" class="btn-press inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                    <i class="fas fa-user text-xs"></i>
                    مجموعات فردية
                </a>
                <a href="<?php echo e(route('admin.tutoring-groups.index', 'collective')); ?>" class="btn-press inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                    <i class="fas fa-users text-xs"></i>
                    مجموعات جماعية
                </a>
            <?php endif; ?>
            <?php if(Route::has('admin.advanced-courses.index')): ?>
                <a href="<?php echo e(route('admin.advanced-courses.index')); ?>" class="btn-press inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                    <i class="fas fa-graduation-cap text-xs"></i>
                    الكورسات
                </a>
            <?php endif; ?>
        </div>
    </article>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">البحث والفلترة</h3>
            <p class="mt-0.5 text-xs text-muted">ابحث بالاسم أو البريد أو الهاتف، أو صفِّ حسب الحالة</p>
        </div>
        <form method="GET" action="<?php echo e(route('admin.students-accounts.index')); ?>" class="grid grid-cols-1 gap-4 p-4 sm:p-5 md:grid-cols-3 md:items-end">
            <div>
                <label class="<?php echo e($labelClass); ?>" for="search">البحث</label>
                <input id="search" type="search" name="search" value="<?php echo e(request('search')); ?>"
                       placeholder="الاسم، البريد، أو الهاتف..." class="<?php echo e($fieldClass); ?>">
            </div>
            <div>
                <label class="<?php echo e($labelClass); ?>" for="status">الحالة</label>
                <select id="status" name="status" class="<?php echo e($fieldClass); ?>">
                    <option value="">كل الحالات</option>
                    <option value="1" <?php if(request('status') === '1'): echo 'selected'; endif; ?>>نشط</option>
                    <option value="0" <?php if(request('status') === '0'): echo 'selected'; endif; ?>>غير نشط</option>
                </select>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white">
                    <i class="fas fa-filter text-xs"></i>
                    تصفية
                </button>
                <?php if(request()->anyFilled(['search', 'status'])): ?>
                    <a href="<?php echo e(route('admin.students-accounts.index')); ?>" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl border border-line px-5 text-sm font-medium text-ink hover:bg-canvas">
                        <i class="fas fa-times text-xs"></i>
                        مسح
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </article>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
            <div class="min-w-0">
                <h3 class="text-base font-semibold text-ink">قائمة الطلاب</h3>
                <p class="mt-0.5 text-xs text-muted"><?php echo e(number_format($users->total())); ?> طالب</p>
            </div>
            <a href="<?php echo e(route('admin.users.create', ['from' => 'students', 'role' => 'student'])); ?>" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:text-accent">
                <i class="fas fa-plus text-xs"></i>
                إضافة
            </a>
        </div>

        <?php if($users->count() > 0): ?>
            <div class="admin-table-wrap">
                <table class="w-full min-w-[900px] text-right text-sm">
                    <thead class="bg-canvas text-[11px] uppercase tracking-wide text-muted">
                        <tr>
                            <th class="px-5 py-3 font-medium">الطالب</th>
                            <th class="px-3 py-3 font-medium">الحالة</th>
                            <th class="px-3 py-3 font-medium">تاريخ التسجيل</th>
                            <th class="px-5 py-3 font-medium">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="transition hover:bg-canvas">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex size-11 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-sm font-semibold text-accent">
                                            <?php echo e(mb_substr($user->name, 0, 1, 'UTF-8')); ?>

                                        </span>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-ink"><?php echo e($user->name); ?></p>
                                            <p class="mt-0.5 truncate text-xs text-muted">
                                                <i class="fas fa-envelope ml-1 text-[10px]"></i><?php echo e($user->email ?: '—'); ?>

                                            </p>
                                            <?php if($user->phone): ?>
                                                <p class="mt-0.5 truncate text-xs text-muted">
                                                    <i class="fas fa-phone ml-1 text-[10px]"></i><?php echo e($user->phone); ?>

                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <?php if($user->is_active): ?>
                                        <span class="rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent">نشط</span>
                                    <?php else: ?>
                                        <span class="rounded-lg bg-canvas-muted px-2.5 py-1 text-xs font-medium text-muted">غير نشط</span>
                                    <?php endif; ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-3">
                                    <p class="font-medium tabular-nums text-ink"><?php echo e($user->created_at?->format('Y-m-d')); ?></p>
                                    <p class="mt-0.5 text-xs tabular-nums text-muted"><?php echo e($user->created_at?->format('H:i')); ?></p>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-1.5">
                                        <a href="<?php echo e(route('admin.users.show', $user->id)); ?>"
                                           class="btn-press inline-flex size-8 items-center justify-center rounded-lg bg-canvas-muted text-muted transition hover:bg-ink hover:text-white"
                                           title="عرض">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>"
                                           class="btn-press inline-flex size-8 items-center justify-center rounded-lg bg-accent-soft text-accent transition hover:bg-accent hover:text-white"
                                           title="تعديل">
                                            <i class="fas fa-pen text-xs"></i>
                                        </a>
                                        <?php if($user->id !== auth()->id()): ?>
                                            <button type="button"
                                                    onclick="deleteStudent(this)"
                                                    data-delete-url="<?php echo e(route('admin.users.delete', $user->id)); ?>"
                                                    class="btn-press inline-flex size-8 items-center justify-center rounded-lg bg-danger/10 text-danger transition hover:bg-danger hover:text-white"
                                                    title="حذف">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <?php if($users->hasPages()): ?>
                <div class="border-t border-line px-4 py-4 sm:px-5"><?php echo e($users->withQueryString()->links()); ?></div>
            <?php endif; ?>
        <?php else: ?>
            <div class="px-4 py-16 text-center sm:px-5">
                <div class="mx-auto mb-3 inline-flex size-12 items-center justify-center rounded-2xl bg-accent-soft text-accent">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <p class="text-sm font-medium text-ink">لا توجد نتائج</p>
                <p class="mt-1 text-xs text-muted">
                    <?php if(request()->anyFilled(['search', 'status'])): ?>
                        لا توجد نتائج مطابقة للفلتر الحالي.
                    <?php else: ?>
                        <a href="<?php echo e(route('admin.users.create', ['from' => 'students', 'role' => 'student'])); ?>" class="text-accent hover:underline">أضف أول طالب</a>.
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </article>

    <div class="grid gap-5 lg:grid-cols-2">
        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">آخر المسجّلين</h3>
                <p class="mt-0.5 text-xs text-muted">أحدث حسابات الطلاب</p>
            </div>
            <div class="divide-y divide-line">
                <?php $__empty_1 = true; $__currentLoopData = $recentUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recentUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a href="<?php echo e(route('admin.users.show', $recentUser->id)); ?>" class="flex items-center gap-3 px-4 py-3 transition hover:bg-canvas sm:px-5">
                        <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-sm font-semibold text-accent">
                            <?php echo e(mb_substr($recentUser->name, 0, 1, 'UTF-8')); ?>

                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-ink"><?php echo e($recentUser->name); ?></p>
                            <p class="mt-0.5 text-xs text-muted"><?php echo e($recentUser->created_at?->diffForHumans()); ?></p>
                        </div>
                        <?php if($recentUser->is_active): ?>
                            <span class="rounded-lg bg-accent-soft px-2 py-1 text-[11px] font-medium text-accent">نشط</span>
                        <?php else: ?>
                            <span class="rounded-lg bg-canvas-muted px-2 py-1 text-[11px] font-medium text-muted">موقوف</span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="px-4 py-10 text-center text-sm text-muted sm:px-5">لا يوجد طلاب بعد</div>
                <?php endif; ?>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-base font-semibold text-ink">نشطون مؤخراً</h3>
                <p class="mt-0.5 text-xs text-muted">آخر 7 أيام · <?php echo e(number_format($stats['active_recently'] ?? 0)); ?> طالب</p>
            </div>
            <div class="divide-y divide-line">
                <?php $__empty_1 = true; $__currentLoopData = $recentlyActiveUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activeUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a href="<?php echo e(route('admin.users.show', $activeUser->id)); ?>" class="flex items-center gap-3 px-4 py-3 transition hover:bg-canvas sm:px-5">
                        <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-xl bg-metal/15 text-sm font-semibold text-metal">
                            <?php echo e(mb_substr($activeUser->name, 0, 1, 'UTF-8')); ?>

                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-ink"><?php echo e($activeUser->name); ?></p>
                            <p class="mt-0.5 text-xs text-muted">آخر نشاط: <?php echo e($activeUser->updated_at?->diffForHumans()); ?></p>
                        </div>
                        <span class="size-2 rounded-full bg-accent"></span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="px-4 py-10 text-center text-sm text-muted sm:px-5">لا يوجد نشاط حديث</div>
                <?php endif; ?>
            </div>
        </article>
    </div>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">تسجيلات آخر 6 أشهر</h3>
            <p class="mt-0.5 text-xs text-muted">عدد الطلاب الجدد شهرياً</p>
        </div>
        <div class="space-y-3 p-4 sm:p-5">
            <?php $__empty_1 = true; $__currentLoopData = $usersByMonth->reverse(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monthData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $bar = ((int) $monthData->count / $maxMonthCount) * 100;
                    $label = ($monthNames[(int) $monthData->month] ?? $monthData->month).' '.$monthData->year;
                ?>
                <div>
                    <div class="mb-1.5 flex items-center justify-between gap-3">
                        <span class="text-sm font-medium text-ink"><?php echo e($label); ?></span>
                        <span class="text-sm font-semibold tabular-nums text-accent"><?php echo e(number_format($monthData->count)); ?></span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-canvas">
                        <div class="h-full rounded-full bg-accent" style="width: <?php echo e(max(4, $bar)); ?>%"></div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="py-6 text-center text-sm text-muted">لا توجد بيانات شهرية بعد</p>
            <?php endif; ?>
        </div>
    </article>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    function deleteStudent(btn) {
        var deleteUrl = btn && btn.getAttribute ? btn.getAttribute('data-delete-url') : null;
        if (!deleteUrl) {
            alert('خطأ: رابط الحذف غير متوفر.');
            return;
        }
        if (!confirm('هل أنت متأكد من حذف هذا الطالب؟ هذا الإجراء لا يمكن التراجع عنه.')) {
            return;
        }
        var csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            alert('خطأ: لم يتم العثور على CSRF token');
            return;
        }
        fetch(deleteUrl, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(async function (response) {
            var contentType = response.headers.get('content-type') || '';
            var data = {};
            try {
                var text = await response.text();
                if (text && contentType.indexOf('application/json') !== -1) {
                    data = JSON.parse(text);
                }
            } catch (e) {}
            return { ok: response.ok, status: response.status, data: data };
        })
        .then(function (result) {
            if (result.ok && result.status === 200) {
                alert((result.data && result.data.message) ? result.data.message : 'تم حذف الطالب بنجاح');
                window.location.reload();
                return;
            }
            var errorMsg = (result.data && (result.data.message || result.data.error)) || 'حدث خطأ أثناء الحذف.';
            alert('خطأ: ' + errorMsg);
        })
        .catch(function () {
            alert('حدث خطأ أثناء حذف الطالب.');
        });
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\students-accounts\index.blade.php ENDPATH**/ ?>