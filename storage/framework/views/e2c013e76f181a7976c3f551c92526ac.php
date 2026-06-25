<?php $__env->startSection('title', 'تعديل الاشتراك'); ?>
<?php $__env->startSection('header', 'تعديل الاشتراك'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $typeOptions = \App\Models\Subscription::typeLabels();
    $cycleOptions = \App\Models\Subscription::billingCycleLabels();
    $subscriptionFeatureKeys = \App\Models\Subscription::normalizeFeatureKeys($subscription->features ?? []);
    $tp = $teacherPlans ?? [];
    $fmtPrice = fn ($v) => number_format((float) $v, 0);
    $starter = $tp['teacher_starter'] ?? null;
    $pro = $tp['teacher_pro'] ?? null;
    $sRow = is_array($starter) ? $starter : [];
    $pRow = is_array($pro) ? $pro : [];
    $planFeatures = [
        'teacher_starter' => is_array($sRow['features'] ?? null) ? $sRow['features'] : [
            'library_access', 'ai_tools', 'support', 'teacher_profile', 'visible_to_academies', 'can_apply_opportunities', 'full_ai_suite', 'teacher_evaluation', 'recommended_to_academies', 'priority_opportunities', 'direct_support',
        ],
        'teacher_pro' => is_array($pRow['features'] ?? null) ? $pRow['features'] : [
            'library_access', 'ai_tools', 'classroom_access', 'support', 'teacher_profile', 'visible_to_academies', 'can_apply_opportunities', 'full_ai_suite', 'teacher_evaluation', 'recommended_to_academies', 'priority_opportunities', 'direct_support',
        ],
    ];
    $planApplyMeta = [];
    foreach (['teacher_starter', 'teacher_pro'] as $planKey) {
        if (! isset($tp[$planKey]) || ! is_array($tp[$planKey])) {
            continue;
        }
        $row = $tp[$planKey];
        $billingCycle = is_string($row['billing_cycle'] ?? null) ? $row['billing_cycle'] : 'monthly';
        $typeKeys = array_keys($typeOptions);
        $subscriptionType = in_array($billingCycle, $typeKeys, true) ? $billingCycle : 'monthly';
        $planApplyMeta[$planKey] = [
            'subscription_type' => $subscriptionType,
            'plan_name' => (string) ($row['label'] ?? ''),
            'price' => (float) ($row['price'] ?? 0),
            'billing_cycle' => $billingCycle,
            'limits' => \App\Services\SubscriptionLimitService::limitsArrayForPlanKey($tp, $planKey),
        ];
    }

    $featureKeysOrder = [
        'library_access', 'ai_tools', 'classroom_access', 'support', 'teacher_profile',
        'visible_to_academies', 'can_apply_opportunities', 'full_ai_suite', 'teacher_evaluation',
        'recommended_to_academies', 'priority_opportunities', 'direct_support',
    ];
    $starterF = is_array($sRow['features'] ?? null) ? $sRow['features'] : [];
    $proF = is_array($pRow['features'] ?? null) ? $pRow['features'] : [];
    $manualDefaultFeatures = array_values(array_unique(array_merge(
        array_map('strval', $starterF),
        array_map('strval', $proF)
    )));
    $sDesc = is_array($sRow['feature_descriptions'] ?? null) ? $sRow['feature_descriptions'] : [];
    $pDesc = is_array($pRow['feature_descriptions'] ?? null) ? $pRow['feature_descriptions'] : [];
    $featureDisplayLines = [];
    foreach ($featureKeysOrder as $fk) {
        $hint = trim((string) ($pDesc[$fk] ?? ''));
        if ($hint === '') {
            $hint = trim((string) ($sDesc[$fk] ?? ''));
        }
        $featureDisplayLines[$fk] = $hint !== '' ? $hint : __('student.subscription_feature.'.$fk);
    }
    $checkedForEdit = array_keys(array_filter((array) old('features', [])));
    if ($checkedForEdit === []) {
        $checkedForEdit = $subscriptionFeatureKeys;
    }
    $editPlanKeyForLimits = ($subscription->teacher_plan_key && isset($tp[$subscription->teacher_plan_key]))
        ? (string) $subscription->teacher_plan_key
        : 'teacher_starter';
    $initialLimitsForForm = array_merge(
        \App\Services\SubscriptionLimitService::limitsArrayForPlanKey($tp, $editPlanKeyForLimits),
        is_array($subscription->feature_limits) ? $subscription->feature_limits : [],
        is_array(old('limits')) ? old('limits') : []
    );
?>
<div class="container mx-auto px-4 py-8 space-y-6">
    <?php if($errors->any()): ?>
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl">
            <strong class="font-semibold">حدثت أخطاء:</strong>
            <ul class="list-disc pr-6 mt-2 text-sm">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-black text-gray-900">تعديل الاشتراك</h1>
                    <p class="text-sm text-gray-500 mt-1">قم بتحديث تفاصيل الخطة والفترة الزمنية للمستخدم.</p>
                </div>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-sky-100 text-sky-600">
                    <i class="fas fa-id-card"></i>
                    <?php echo e($subscription->plan_name); ?>

                </span>
            </div>

            <form action="<?php echo e(route('admin.subscriptions.update', $subscription)); ?>" method="POST" class="space-y-8" x-data="editTeacherSubscriptionForm('<?php echo e($subscription->teacher_plan_key ?? ''); ?>')">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700">نمط اشتراك المعلم (اختياري)</label>
                        <select name="teacher_plan_key" x-model="selectedPlan" @change="applyPlan" class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            <option value="">بدون — إدخال يدوي</option>
                            <?php if($starter): ?>
                                <option value="teacher_starter">
                                    <?php echo e($starter['label'] ?? 'الباقة الأساسية'); ?>

                                    — <?php echo e($fmtPrice($starter['price'] ?? 0)); ?> ج.م
                                    <?php if(!empty($starter['billing_cycle'])): ?>
                                        (<?php echo e($cycleOptions[$starter['billing_cycle']] ?? $starter['billing_cycle']); ?>)
                                    <?php endif; ?>
                                </option>
                            <?php endif; ?>
                            <?php if($pro): ?>
                                <option value="teacher_pro">
                                    <?php echo e($pro['label'] ?? 'الباقة الشاملة'); ?>

                                    — <?php echo e($fmtPrice($pro['price'] ?? 0)); ?> ج.م
                                    <?php if(!empty($pro['billing_cycle'])): ?>
                                        (<?php echo e($cycleOptions[$pro['billing_cycle']] ?? $pro['billing_cycle']); ?>)
                                    <?php endif; ?>
                                </option>
                            <?php endif; ?>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            اختيار باقة يملأ الحقول من <strong>إعدادات باقات المعلمين</strong> الحالية (الأسماء والأسعار ودورة الفوترة والمزايا).
                        </p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">المستخدم *</label>
                        <select name="user_id" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($user->id); ?>" <?php echo e($subscription->user_id == $user->id ? 'selected' : ''); ?>>
                                    <?php echo e($user->name); ?> — <?php echo e($user->phone); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">نوع الاشتراك *</label>
                        <select name="subscription_type" x-model="form.subscription_type" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            <?php $__currentLoopData = $typeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e($subscription->subscription_type === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">اسم الخطة *</label>
                        <input type="text" name="plan_name" x-model="form.plan_name" value="<?php echo e(old('plan_name', $subscription->plan_name)); ?>" required
                               class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">السعر *</label>
                        <div class="relative">
                            <input type="number" name="price" x-model.number="form.price" step="0.01" min="0" value="<?php echo e(old('price', $subscription->price)); ?>" required
                                   class="w-full pl-12 pr-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            <span class="absolute inset-y-0 left-4 flex items-center text-sm font-semibold text-gray-500">ج.م</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">تاريخ البداية *</label>
                        <input type="date" name="start_date" value="<?php echo e(old('start_date', optional($subscription->start_date)->format('Y-m-d'))); ?>" required
                               class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">تاريخ الانتهاء *</label>
                        <input type="date" name="end_date" value="<?php echo e(old('end_date', optional($subscription->end_date)->format('Y-m-d'))); ?>" required
                               class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">دورة الفوترة *</label>
                        <select name="billing_cycle" x-model="form.billing_cycle" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            <?php $__currentLoopData = $cycleOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e($subscription->billing_cycle === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">الحالة *</label>
                        <select name="status" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            <option value="active" <?php echo e($subscription->status === 'active' ? 'selected' : ''); ?>>نشط</option>
                            <option value="expired" <?php echo e($subscription->status === 'expired' ? 'selected' : ''); ?>>منتهي</option>
                            <option value="cancelled" <?php echo e($subscription->status === 'cancelled' ? 'selected' : ''); ?>>ملغي</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between bg-gray-50 border border-gray-100 rounded-2xl px-4 py-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">تفعيل التجديد التلقائي</p>
                        <p class="text-xs text-gray-500">في حالة التفعيل سيتم تجديد الاشتراك تلقائياً بعد انتهاء المدة.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="auto_renew" value="1" class="sr-only peer" <?php echo e(old('auto_renew', $subscription->auto_renew) ? 'checked' : ''); ?>>
                        <div class="w-12 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-sky-500 rounded-full peer peer-checked:after:translate-x-6 peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sky-500"></div>
                    </label>
                </div>

                <div class="bg-gray-50 border border-gray-100 rounded-2xl px-4 py-4 space-y-3">
                    <h2 class="text-sm font-semibold text-gray-900">مزايا الخطة للمعلم</h2>
                    <p class="text-xs text-gray-500">
                        النصوص التوضيحية تُقرأ من <strong>إعدادات باقات المعلمين</strong> عند توفرها؛ وعند اختيار «إدخال يدوي» تُستعاد مزايا هذا الاشتراك الحالية (أو القيم بعد التحقق من النموذج).
                    </p>
                    <?php echo $__env->make('admin.subscriptions._subscription-feature-checkboxes', [
                        'featureKeysOrder' => $featureKeysOrder,
                        'featureDisplayLines' => $featureDisplayLines,
                        'checkedKeys' => $checkedForEdit,
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php echo $__env->make('admin.subscriptions._subscription-limit-fields', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <p class="text-xs text-gray-400 mt-2">
                        جميع المبالغ المالية في النظام تستخدم العملة الأساسية: الجنيه المصري (ج.م).
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white font-semibold shadow-lg shadow-sky-500/30 transition-all">
                        <i class="fas fa-save"></i>
                        تحديث الاشتراك
                    </button>
                    <a href="<?php echo e(route('admin.subscriptions.show', $subscription)); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold transition-all">
                        إلغاء والرجوع
                    </a>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-lg font-black text-gray-900 mb-4">ملخص الاشتراك الحالي</h2>
                <div class="space-y-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">المستخدم</p>
                        <p class="mt-1 text-base font-semibold text-gray-900"><?php echo e($subscription->user->name ?? 'غير معروف'); ?></p>
                        <p class="text-xs text-gray-500"><?php echo e($subscription->user->phone ?? 'بدون رقم'); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">الفترة الحالية</p>
                        <p class="mt-1 text-base font-semibold text-gray-900"><?php echo e($subscription->start_date?->format('Y-m-d') ?? 'غير محدد'); ?> → <?php echo e($subscription->end_date?->format('Y-m-d') ?? 'غير محدد'); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">تاريخ التحديث الأخير</p>
                        <p class="mt-1 text-base font-semibold text-gray-900"><?php echo e(optional($subscription->updated_at)->diffForHumans()); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-6">
                <h2 class="text-lg font-black text-gray-900 mb-4">نصائح سريعة</h2>
                <ul class="space-y-3 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-info-circle mt-1 text-sky-500"></i>
                        تأكد من أن تاريخ الانتهاء بعد تاريخ البداية لتجنب رفض النموذج.
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-sync mt-1 text-sky-500"></i>
                        قم بتفعيل التجديد التلقائي للحسابات المستمرة لتوفير الوقت.
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-receipt mt-1 text-sky-500"></i>
                        اربط الاشتراك بالفاتورة المناسبة لضمان تتبع مالي دقيق.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<script>
    function editTeacherSubscriptionForm(initialPlanKey) {
        var PLAN_FEATURES = <?php echo json_encode($planFeatures, 15, 512) ?>;
        var PLAN_META = <?php echo json_encode($planApplyMeta, 15, 512) ?>;
        var MANUAL_DEFAULT_FEATURES = <?php echo json_encode($manualDefaultFeatures, 15, 512) ?>;
        var EDIT_SAVED_FEATURES = <?php echo json_encode(\App\Models\Subscription::normalizeFeatureKeys($checkedForEdit), 15, 512) ?>;
        var EDIT_LIMITS_SNAPSHOT = <?php echo json_encode($initialLimitsForForm, 15, 512) ?>;

        function syncSubscriptionFeatureCheckboxes(featureList) {
            var set = {};
            (featureList || []).forEach(function (f) { set[f] = true; });
            document.querySelectorAll('input[type=checkbox][data-sub-feature]').forEach(function (cb) {
                var fk = cb.getAttribute('data-sub-feature');
                cb.checked = !!set[fk];
            });
        }

        return {
            selectedPlan: initialPlanKey || '',
            form: {
                subscription_type: '<?php echo e($subscription->subscription_type); ?>',
                plan_name: <?php echo json_encode($subscription->plan_name, 15, 512) ?>,
                price: <?php echo json_encode((float) $subscription->price, 15, 512) ?>,
                billing_cycle: '<?php echo e($subscription->billing_cycle); ?>',
            },
            limits: <?php echo json_encode($initialLimitsForForm, 15, 512) ?>,
            applyPlan(event) {
                var key = event.target ? event.target.value : '';
                if (!key) {
                    this.$nextTick(function () {
                        syncSubscriptionFeatureCheckboxes(EDIT_SAVED_FEATURES.length ? EDIT_SAVED_FEATURES : MANUAL_DEFAULT_FEATURES);
                    });
                    if (EDIT_LIMITS_SNAPSHOT) {
                        Object.assign(this.limits, EDIT_LIMITS_SNAPSHOT);
                    }
                    return;
                }
                if (!PLAN_FEATURES[key] || !PLAN_META[key]) return;

                var m = PLAN_META[key];
                this.form.subscription_type = m.subscription_type || 'monthly';
                this.form.plan_name = m.plan_name || '';
                this.form.price = parseFloat(m.price) || 0;
                this.form.billing_cycle = m.billing_cycle || 'monthly';
                if (m.limits) {
                    Object.assign(this.limits, m.limits);
                }

                this.$nextTick(function () {
                    syncSubscriptionFeatureCheckboxes(PLAN_FEATURES[key]);
                });
            },
        };
    }
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\subscriptions\edit.blade.php ENDPATH**/ ?>