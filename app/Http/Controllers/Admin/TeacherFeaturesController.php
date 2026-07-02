<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TeacherFeaturesController extends Controller
{
    protected string $cacheKey = 'teacher_features_settings';

    public function index()
    {
        $settings = $this->getSettings();

        return view('admin.teacher-features.index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'plans' => 'required|array',
            'plans.teacher_starter.price' => 'required|numeric|min:0',
            'plans.teacher_pro.price' => 'required|numeric|min:0',
            'plans.*.label' => 'nullable|string|max:255',
            'plans.*.billing_cycle' => 'nullable|string|in:monthly',
            'plans.*.features' => 'nullable|array',
            'plans.*.features.*' => 'nullable|string|max:100',
            'plans.*.feature_descriptions' => 'nullable|array',
            'plans.*.feature_descriptions.*' => 'nullable|string|max:500',
            'plans.*.limits.classroom_meetings_per_month' => 'nullable|integer|min:0|max:10000',
            'plans.*.limits.classroom_max_participants' => 'nullable|integer|min:1|max:1000',
            'plans.*.limits.classroom_default_duration_minutes' => 'nullable|integer|min:15|max:1440',
            'plans.*.limits.classroom_max_duration_minutes' => 'nullable|integer|min:30|max:1440',
            'plans.*.limits.personal_marketing_profile_sections' => 'nullable|integer|min:1|max:20',
            'plans.*.limits.personal_marketing_priority_score' => 'nullable|integer|min:0|max:100',
            'plans.*.limits.personal_marketing_monthly_featured_days' => 'nullable|integer|min:0|max:31',
            'plans.*.card_subtitle' => 'nullable|string|max:500',
            'plans.*.card_badge' => 'nullable|string|max:200',
            'plans.*.card_price_hint' => 'nullable|string|max:500',
            'plans.*.card_cta' => 'nullable|string|max:120',
            'plans.*.card_footer_note' => 'nullable|string|max:500',
        ]);

        $plans = $validated['plans'];

        // تأكيد أن كل خطة تحتوي على الحقول المطلوبة
        foreach (['teacher_starter', 'teacher_pro'] as $key) {
            $plans[$key]['label'] = $plans[$key]['label'] ?? $this->defaultSettings()[$key]['label'] ?? '';
            $plans[$key]['billing_cycle'] = 'monthly';
            $plans[$key]['features'] = isset($plans[$key]['features']) && is_array($plans[$key]['features'])
                ? array_values(array_filter(
                    $plans[$key]['features'],
                    static fn ($f) => $f !== null && $f !== '' && $f !== 'zoom_access'
                ))
                : [];
            if ($key === 'teacher_starter') {
                $plans[$key]['features'] = array_values(array_filter(
                    $plans[$key]['features'],
                    static fn ($f) => $f !== 'classroom_access'
                ));
            }
            $defaults = $this->defaultSettings()[$key]['limits'] ?? [];
            $limits = $plans[$key]['limits'] ?? [];
            $plans[$key]['limits'] = [
                'classroom_meetings_per_month' => (int) ($limits['classroom_meetings_per_month'] ?? ($defaults['classroom_meetings_per_month'] ?? 0)),
                'classroom_max_participants' => (int) ($limits['classroom_max_participants'] ?? ($defaults['classroom_max_participants'] ?? 25)),
                'classroom_default_duration_minutes' => (int) ($limits['classroom_default_duration_minutes'] ?? ($defaults['classroom_default_duration_minutes'] ?? 60)),
                'classroom_max_duration_minutes' => (int) ($limits['classroom_max_duration_minutes'] ?? ($defaults['classroom_max_duration_minutes'] ?? 120)),
                'personal_marketing_profile_sections' => (int) ($limits['personal_marketing_profile_sections'] ?? ($defaults['personal_marketing_profile_sections'] ?? 5)),
                'personal_marketing_priority_score' => (int) ($limits['personal_marketing_priority_score'] ?? ($defaults['personal_marketing_priority_score'] ?? 0)),
                'personal_marketing_monthly_featured_days' => (int) ($limits['personal_marketing_monthly_featured_days'] ?? ($defaults['personal_marketing_monthly_featured_days'] ?? 0)),
            ];
            if ($plans[$key]['limits']['classroom_default_duration_minutes'] > $plans[$key]['limits']['classroom_max_duration_minutes']) {
                $plans[$key]['limits']['classroom_default_duration_minutes'] = $plans[$key]['limits']['classroom_max_duration_minutes'];
            }
            if ($key === 'teacher_starter') {
                // باقة البداية لا تحتوي على Glottical Classroom نهائياً.
                $plans[$key]['limits']['classroom_meetings_per_month'] = 0;
                $plans[$key]['limits']['classroom_max_participants'] = 1;
                $plans[$key]['limits']['classroom_default_duration_minutes'] = 60;
                $plans[$key]['limits']['classroom_max_duration_minutes'] = 60;
            }

            $defaultDescriptions = $this->defaultSettings()[$key]['feature_descriptions'] ?? [];
            $rawDescriptions = $plans[$key]['feature_descriptions'] ?? [];
            $normalizedDescriptions = [];
            if (is_array($rawDescriptions)) {
                foreach ($rawDescriptions as $featureKey => $descriptionValue) {
                    if (!is_string($featureKey)) {
                        continue;
                    }
                    $normalizedDescriptions[$featureKey] = is_string($descriptionValue)
                        ? trim($descriptionValue)
                        : '';
                }
            }
            foreach ($defaultDescriptions as $featureKey => $defaultDescription) {
                if (!array_key_exists($featureKey, $normalizedDescriptions) || $normalizedDescriptions[$featureKey] === '') {
                    $normalizedDescriptions[$featureKey] = (string) $defaultDescription;
                }
            }
            $plans[$key]['feature_descriptions'] = $normalizedDescriptions;

            $defCard = $this->defaultSettings()[$key];
            foreach (['card_subtitle', 'card_badge', 'card_price_hint', 'card_cta', 'card_footer_note'] as $cf) {
                $raw = $plans[$key][$cf] ?? ($defCard[$cf] ?? '');
                $plans[$key][$cf] = is_string($raw) ? trim($raw) : '';
            }
        }

        $now = now();
        $exists = DB::table('settings')->where('key', 'teacher_features')->exists();
        if ($exists) {
            DB::table('settings')->where('key', 'teacher_features')->update([
                'value' => json_encode($plans),
                'updated_at' => $now,
            ]);
        } else {
            DB::table('settings')->insert([
                'key' => 'teacher_features',
                'value' => json_encode($plans),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        Cache::forget($this->cacheKey);
        Cache::forget('teacher_features_settings_limits');

        return back()->with('success', 'تم تحديث مزايا باقات المعلمين بنجاح.');
    }

    public function getSettings(): array
    {
        return Cache::remember($this->cacheKey, 300, function () {
            if (!DB::getSchemaBuilder()->hasTable('settings')) {
                return $this->defaultSettings();
            }

            $row = DB::table('settings')->where('key', 'teacher_features')->first();

            if (!$row) {
                return $this->defaultSettings();
            }

            $decoded = json_decode($row->value, true);

            if (!is_array($decoded)) {
                return $this->defaultSettings();
            }

            $defaults = $this->defaultSettings();
            $merged = [];
            foreach (['teacher_starter', 'teacher_pro'] as $planKey) {
                $merged[$planKey] = array_merge(
                    $defaults[$planKey] ?? [],
                    is_array($decoded[$planKey] ?? null) ? $decoded[$planKey] : []
                );
                if (isset($merged[$planKey]['features']) && is_array($merged[$planKey]['features'])) {
                    $merged[$planKey]['features'] = array_values(array_filter(
                        $merged[$planKey]['features'],
                        static fn ($f) => $f !== 'zoom_access'
                    ));
                }
                if ($planKey === 'teacher_starter') {
                    $merged[$planKey]['features'] = array_values(array_filter(
                        $merged[$planKey]['features'],
                        static fn ($f) => $f !== 'classroom_access'
                    ));
                    $merged[$planKey]['limits']['classroom_meetings_per_month'] = 0;
                }
                $defaultFeatureDescriptions = $defaults[$planKey]['feature_descriptions'] ?? [];
                $decodedFeatureDescriptions = is_array($merged[$planKey]['feature_descriptions'] ?? null)
                    ? $merged[$planKey]['feature_descriptions']
                    : [];
                $merged[$planKey]['feature_descriptions'] = array_merge($defaultFeatureDescriptions, $decodedFeatureDescriptions);
            }

            return $merged;
        });
    }

    protected function defaultSettings(): array
    {
        return [
            'teacher_starter' => [
                'label' => 'الباقة الأساسية',
                'price' => 200,
                'billing_cycle' => 'monthly',
                'card_subtitle' => 'كل الخدمات التعليمية بدون الميتينج وخدماته',
                'card_badge' => '',
                'card_price_hint' => 'اشتراك شهري مناسب للبدء.',
                'card_cta' => 'ابدأ الآن',
                'card_footer_note' => '',
                'features' => [
                    'library_access',
                    'ai_tools',
                    'support',
                    'teacher_profile',
                    'visible_to_academies',
                    'can_apply_opportunities',
                    'full_ai_suite',
                    'teacher_evaluation',
                    'recommended_to_academies',
                    'priority_opportunities',
                    'direct_support',
                ],
                'feature_descriptions' => [
                    'library_access' => 'وصول كامل لمكتبة مناهج وأنشطة جاهزة للتطبيق مباشرة.',
                    'ai_tools' => 'أدوات ذكاء اصطناعي تساعدك على تجهيز المحتوى بسرعة.',
                    'support' => 'دعم فني لمساعدتك في أي مشكلة تشغيلية داخل المنصة.',
                    'teacher_profile' => 'بروفايل مهني يعرض خبراتك ومجالاتك التعليمية.',
                    'visible_to_academies' => 'ظهور ملفك للأكاديميات الباحثة عن معلمين.',
                    'can_apply_opportunities' => 'إمكانية التقديم على فرص التدريس المتاحة.',
                    'full_ai_suite' => 'مجموعة AI موسعة لتخطيط الدروس والمتابعة.',
                    'teacher_evaluation' => 'تقييم احترافي يساعدك على تحسين أدائك.',
                    'recommended_to_academies' => 'ترشيح ملفك للأكاديميات المناسبة لمهاراتك.',
                    'priority_opportunities' => 'أولوية أعلى في الوصول لبعض فرص التدريس.',
                    'direct_support' => 'دعم مباشر وسريع للحالات المهمة.',
                ],
                'limits' => [
                    'classroom_meetings_per_month' => 0,
                    'classroom_max_participants' => 1,
                    'classroom_default_duration_minutes' => 60,
                    'classroom_max_duration_minutes' => 60,
                    'personal_marketing_profile_sections' => 5,
                    'personal_marketing_priority_score' => 40,
                    'personal_marketing_monthly_featured_days' => 0,
                ],
            ],
            'teacher_pro' => [
                'label' => 'الباقة الشاملة',
                'price' => 600,
                'billing_cycle' => 'monthly',
                'card_subtitle' => 'كل الخدمات + الميتينج وجميع خدماته',
                'card_badge' => 'الأكثر شمولاً',
                'card_price_hint' => 'اشتراك شهري يشمل جميع الأدوات واللايف ميتينج.',
                'card_cta' => 'ابدأ العمل الآن',
                'card_footer_note' => 'تشمل كامل المزايا بدون استثناء.',
                'features' => [
                    'library_access',
                    'ai_tools',
                    'classroom_access',
                    'support',
                    'teacher_profile',
                    'visible_to_academies',
                    'can_apply_opportunities',
                    'full_ai_suite',
                    'teacher_evaluation',
                    'recommended_to_academies',
                    'priority_opportunities',
                    'direct_support',
                ],
                'feature_descriptions' => [
                    'library_access' => 'وصول كامل لمكتبة مناهج وأنشطة جاهزة للتطبيق مباشرة.',
                    'ai_tools' => 'أدوات ذكاء اصطناعي تساعدك على تجهيز المحتوى بسرعة.',
                    'classroom_access' => 'استخدام Glottical Classroom لعقد لايف ميتينج وإدارة الجلسات.',
                    'support' => 'دعم فني لمساعدتك في أي مشكلة تشغيلية داخل المنصة.',
                    'teacher_profile' => 'بروفايل مهني يعرض خبراتك ومجالاتك التعليمية.',
                    'visible_to_academies' => 'ظهور ملفك للأكاديميات الباحثة عن معلمين.',
                    'can_apply_opportunities' => 'إمكانية التقديم على فرص التدريس المتاحة.',
                    'full_ai_suite' => 'مجموعة AI موسعة لتخطيط الدروس والمتابعة.',
                    'teacher_evaluation' => 'تقييم احترافي يساعدك على تحسين أدائك.',
                    'recommended_to_academies' => 'ترشيح ملفك للأكاديميات المناسبة لمهاراتك.',
                    'priority_opportunities' => 'أولوية أعلى في الوصول لبعض فرص التدريس.',
                    'direct_support' => 'دعم مباشر وسريع للحالات المهمة.',
                ],
                'limits' => [
                    'classroom_meetings_per_month' => 9999,
                    'classroom_max_participants' => 150,
                    'classroom_default_duration_minutes' => 90,
                    'classroom_max_duration_minutes' => 480,
                    'personal_marketing_profile_sections' => 12,
                    'personal_marketing_priority_score' => 90,
                    'personal_marketing_monthly_featured_days' => 12,
                ],
            ],
        ];
    }
}

