<?php $__env->startSection('title', 'إضافة اشتراك جديد'); ?>
<?php $__env->startSection('header', 'إضافة اشتراك جديد'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $typeOptions = \App\Models\Subscription::typeLabels();
    $tp = $teacherPlans ?? [];
    $starter = $tp['teacher_starter'] ?? null;
    $pro = $tp['teacher_pro'] ?? null;
    $fmtPrice = fn ($v) => number_format((float) $v, 0);
    $featureKeysOrder = [
        'library_access', 'ai_tools', 'classroom_access', 'support', 'teacher_profile',
        'visible_to_academies', 'can_apply_opportunities', 'full_ai_suite', 'teacher_evaluation',
        'recommended_to_academies', 'priority_opportunities', 'direct_support',
    ];
    $sRow = is_array($starter) ? $starter : [];
    $pRow = is_array($pro) ? $pro : [];
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
        ];
    }
    $checkedForCreate = array_keys(array_filter((array) old('features', [])));
?>
<div class="space-y-6" x-data="teacherSubscriptionForm()">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">إضافة اشتراك جديد</h1>
        
        <form action="<?php echo e(route('admin.subscriptions.store')); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">نمط اشتراك المعلم (اختياري)</label>
                    <select name="teacher_plan_key" x-model="selectedPlan" @change="applyPlan" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">بدون — إدخال يدوي</option>
                        <?php if($starter): ?>
                            <option value="teacher_starter">
                                <?php echo e($starter['label'] ?? 'الباقة الأساسية'); ?>

                                — <?php echo e($fmtPrice($starter['price'] ?? 0)); ?> ج.م / شهريًا
                            </option>
                        <?php endif; ?>
                        <?php if($pro): ?>
                            <option value="teacher_pro">
                                <?php echo e($pro['label'] ?? 'الباقة الشاملة'); ?>

                                — <?php echo e($fmtPrice($pro['price'] ?? 0)); ?> ج.م / شهريًا
                            </option>
                        <?php endif; ?>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">
                        اختيار باقة يملأ الحقول من الإعدادات؛ <strong>بدون باقة</strong> تُفعَّل المزايا الافتراضية من إعدادات الباقتين (اتحاد المزايا المفعّلة)، وتظهر أوصاف كل ميزة كما يضبطها المدير في «مزايا المعلمين».
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المستخدم *</label>
                    <div class="space-y-2">
                        <input type="text" id="mx-user-search" placeholder="ابحث بالاسم أو الهاتف..." class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <select id="mx-user-select" name="user_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            <option value="">اختر المستخدم</option>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?> - <?php echo e($user->phone); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع الاشتراك *</label>
                    <select name="subscription_type" x-model="form.subscription_type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="monthly">شهري</option>
                        <option value="quarterly">ربع سنوي</option>
                        <option value="yearly">سنوي</option>
                        <option value="lifetime">مدى الحياة</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">اسم الخطة *</label>
                    <input type="text" name="plan_name" x-model="form.plan_name" required value="<?php echo e(old('plan_name')); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">السعر *</label>
                    <div class="relative">
                        <input type="number" name="price" x-model.number="form.price" step="0.01" min="0" required value="<?php echo e(old('price')); ?>" 
                               class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <span class="absolute inset-y-0 left-4 flex items-center text-sm font-semibold text-gray-500">ج.م</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ البداية *</label>
                    <input type="date" name="start_date" required value="<?php echo e(old('start_date', date('Y-m-d'))); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الانتهاء *</label>
                    <input type="date" name="end_date" required value="<?php echo e(old('end_date', date('Y-m-d', strtotime('+1 month')))); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">دورة الفوترة *</label>
                    <select name="billing_cycle" x-model="form.billing_cycle" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="monthly">شهري</option>
                        <option value="quarterly">ربع سنوي</option>
                        <option value="yearly">سنوي</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="auto_renew" value="1" <?php echo e(old('auto_renew', false) ? 'checked' : ''); ?> 
                       class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                <label class="mr-2 text-sm font-medium text-gray-700">تجديد تلقائي</label>
            </div>

            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 space-y-3">
                <h2 class="text-sm font-semibold text-gray-800 mb-1">مزايا الخطة للمعلم</h2>
                <p class="text-xs text-gray-500 mb-2">
                    أوصاف المزايا تُعرض من إعدادات المدير في «مزايا باقات المعلمين» عند توفرها؛ وإلا يُستخدم الاسم الافتراضي من النظام.
                </p>
                <?php echo $__env->make('admin.subscriptions._subscription-feature-checkboxes', [
                    'featureKeysOrder' => $featureKeysOrder,
                    'featureDisplayLines' => $featureDisplayLines,
                    'checkedKeys' => $checkedForCreate,
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <p class="text-xs text-gray-400 mt-2">
                    تذكير: كل القيم المالية يتم التعامل معها بالجنيه المصري (ج.م).
                </p>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                    إنشاء الاشتراك
                </button>
                <a href="<?php echo e(route('admin.subscriptions.index')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
<script>
    function teacherSubscriptionForm() {
        var PLAN_FEATURES = <?php echo json_encode($planFeatures, 15, 512) ?>;
        var PLAN_META = <?php echo json_encode($planApplyMeta, 15, 512) ?>;
        var MANUAL_DEFAULT_FEATURES = <?php echo json_encode($manualDefaultFeatures, 15, 512) ?>;

        function syncSubscriptionFeatureCheckboxes(featureList) {
            var set = {};
            (featureList || []).forEach(function (f) { set[f] = true; });
            document.querySelectorAll('input[type=checkbox][data-sub-feature]').forEach(function (cb) {
                var fk = cb.getAttribute('data-sub-feature');
                cb.checked = !!set[fk];
            });
        }

        return {
            selectedPlan: '',
            form: {
                subscription_type: 'monthly',
                plan_name: <?php echo json_encode(old('plan_name', ''), 512) ?>,
                price: <?php echo json_encode(old('price', ''), 512) ?>,
                billing_cycle: 'monthly',
            },
            init() {
                var self = this;
                this.$nextTick(function () {
                    if (!self.selectedPlan && MANUAL_DEFAULT_FEATURES.length) {
                        syncSubscriptionFeatureCheckboxes(MANUAL_DEFAULT_FEATURES);
                    }
                });
            },
            applyPlan(event) {
                var key = event.target ? event.target.value : '';
                if (!key) {
                    this.$nextTick(function () {
                        syncSubscriptionFeatureCheckboxes(MANUAL_DEFAULT_FEATURES);
                    });
                    return;
                }
                if (!PLAN_FEATURES[key] || !PLAN_META[key]) return;

                var m = PLAN_META[key];
                this.form.subscription_type = m.subscription_type || 'monthly';
                this.form.plan_name = m.plan_name || '';
                this.form.price = parseFloat(m.price) || 0;
                this.form.billing_cycle = m.billing_cycle || 'monthly';

                this.$nextTick(function () {
                    syncSubscriptionFeatureCheckboxes(PLAN_FEATURES[key]);
                });
            },
        };
    }

    document.addEventListener('DOMContentLoaded', function () {
        var searchInput = document.getElementById('mx-user-search');
        var selectEl = document.getElementById('mx-user-select');
        if (!searchInput || !selectEl) return;

        searchInput.addEventListener('input', function () {
            var term = (this.value || '').toLowerCase();
            Array.prototype.forEach.call(selectEl.options, function (opt, idx) {
                if (idx === 0) {
                    opt.hidden = false;
                    return;
                }
                var text = (opt.text || '').toLowerCase();
                opt.hidden = term && text.indexOf(term) === -1;
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\subscriptions\create.blade.php ENDPATH**/ ?>