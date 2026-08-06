<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('service_packages')) {
            Schema::table('service_packages', function (Blueprint $table) {
                if (! Schema::hasColumn('service_packages', 'plan_type')) {
                    $table->string('plan_type', 32)->nullable()->after('scope')->index();
                }
                if (! Schema::hasColumn('service_packages', 'term_months')) {
                    $table->unsignedTinyInteger('term_months')->nullable()->after('plan_type')->index();
                }
                if (! Schema::hasColumn('service_packages', 'weekly_group_sessions')) {
                    $table->unsignedTinyInteger('weekly_group_sessions')->default(0)->after('term_months');
                }
                if (! Schema::hasColumn('service_packages', 'weekly_private_sessions')) {
                    $table->unsignedTinyInteger('weekly_private_sessions')->default(0)->after('weekly_group_sessions');
                }
                if (! Schema::hasColumn('service_packages', 'includes_community')) {
                    $table->boolean('includes_community')->default(false)->after('weekly_private_sessions');
                }
                if (! Schema::hasColumn('service_packages', 'includes_libraries')) {
                    $table->boolean('includes_libraries')->default(false)->after('includes_community');
                }
                if (! Schema::hasColumn('service_packages', 'tagline')) {
                    $table->string('tagline', 255)->nullable()->after('description');
                }
                if (! Schema::hasColumn('service_packages', 'features')) {
                    $table->json('features')->nullable()->after('tagline');
                }
                if (! Schema::hasColumn('service_packages', 'gifts')) {
                    $table->json('gifts')->nullable()->after('features');
                }
            });
        }

        if (Schema::hasTable('student_service_entitlements')) {
            Schema::table('student_service_entitlements', function (Blueprint $table) {
                if (! Schema::hasColumn('student_service_entitlements', 'plan_type')) {
                    $table->string('plan_type', 32)->nullable()->after('scope')->index();
                }
                if (! Schema::hasColumn('student_service_entitlements', 'term_months')) {
                    $table->unsignedTinyInteger('term_months')->nullable()->after('plan_type');
                }
                if (! Schema::hasColumn('student_service_entitlements', 'weekly_group_sessions')) {
                    $table->unsignedTinyInteger('weekly_group_sessions')->default(0)->after('term_months');
                }
                if (! Schema::hasColumn('student_service_entitlements', 'weekly_private_sessions')) {
                    $table->unsignedTinyInteger('weekly_private_sessions')->default(0)->after('weekly_group_sessions');
                }
                if (! Schema::hasColumn('student_service_entitlements', 'includes_community')) {
                    $table->boolean('includes_community')->default(false)->after('weekly_private_sessions');
                }
                if (! Schema::hasColumn('student_service_entitlements', 'includes_libraries')) {
                    $table->boolean('includes_libraries')->default(false)->after('includes_community');
                }
            });
        }

        $this->seedCommercialPlans();
    }

    protected function seedCommercialPlans(): void
    {
        if (! Schema::hasTable('service_packages')) {
            return;
        }

        $gifts = [
            'مكتبة المناهج والألعاب التعليمية التفاعلية',
            'مكتبة فيديوهات تعليمية للأطفال',
        ];

        $plans = [
            'school' => [
                'label' => 'School Plan',
                'badge' => 'مدرسة',
                'tagline' => 'أفضل للعائلات التي تبحث عن تجربة مدرسة إسلامية متكاملة.',
                'scope' => 'tutoring_collective',
                'weekly_group' => 2,
                'weekly_private' => 0,
                'community' => true,
                'libraries' => true,
                'featured' => false,
                'sort' => 10,
                'features' => [
                    'حصتان مباشرًا أسبوعيًا',
                    'اختيار الفصل التعليمي المناسب حسب المواعيد',
                    'الدراسة ضمن مجموعة صغيرة (فصول الأسرة)',
                    'فريق من المعلمين المتخصصين تعيّنهم الإدارة',
                    'بناء مجتمع بين الطلاب',
                    'التفاعل مع أصدقاء يتعلمون القرآن والعربية والدراسات الإسلامية',
                ],
                'terms' => [
                    1 => ['price' => 50, 'monthly' => 50],
                    3 => ['price' => 130, 'monthly' => 50],
                    6 => ['price' => 240, 'monthly' => 50],
                ],
            ],
            'private' => [
                'label' => 'Private Plan',
                'badge' => 'فردي',
                'tagline' => 'حصص فردية مع معلم خاص وخطة تعليمية مخصصة.',
                'scope' => 'private_lessons',
                'weekly_group' => 0,
                'weekly_private' => 2,
                'community' => false,
                'libraries' => true,
                'featured' => false,
                'sort' => 20,
                'features' => [
                    'حصتان فرديتان أسبوعيًا',
                    'اختيار المعلم المناسب',
                    'اختيار الأيام والمواعيد المناسبة',
                    'خطة تعليمية فردية',
                ],
                'terms' => [
                    1 => ['price' => 80, 'monthly' => 80],
                    3 => ['price' => 200, 'monthly' => 80],
                    6 => ['price' => 320, 'monthly' => 80],
                ],
            ],
            'premier' => [
                'label' => 'Premier Plan',
                'badge' => 'الأفضل قيمة',
                'tagline' => 'أفضل قيمة وأفضل نتائج — يجمع بين مزايا المدرسة والتعليم الفردي.',
                'scope' => 'global',
                'weekly_group' => 2,
                'weekly_private' => 2,
                'community' => true,
                'libraries' => true,
                'featured' => true,
                'sort' => 30,
                'features' => [
                    'حصتان أسبوعيًا داخل الفصل التعليمي',
                    'حصتان أسبوعيًا مع معلم خاص',
                    'إجمالي 4 حصص أسبوعيًا',
                    'اختيار الفصل المناسب',
                    'اختيار المعلم المناسب',
                    'مجتمع طلابي مع متابعة فردية',
                ],
                'terms' => [
                    1 => ['price' => 110, 'monthly' => 110],
                    3 => ['price' => 300, 'monthly' => 110],
                    6 => ['price' => 500, 'monthly' => 110],
                ],
            ],
        ];

        $now = now();

        foreach ($plans as $planType => $plan) {
            foreach ($plan['terms'] as $months => $term) {
                $slug = $planType.'-'.$months.'m';
                $units = ((int) $plan['weekly_group'] + (int) $plan['weekly_private']) * 4 * (int) $months;
                $original = (float) $term['monthly'] * (int) $months;
                $payload = [
                    'name' => $plan['label'].' — '.$months.($months === 1 ? ' شهر' : ' أشهر'),
                    'description' => $plan['tagline'],
                    'tagline' => $plan['tagline'],
                    'badge' => $plan['badge'],
                    'scope' => $plan['scope'],
                    'plan_type' => $planType,
                    'term_months' => $months,
                    'weekly_group_sessions' => $plan['weekly_group'],
                    'weekly_private_sessions' => $plan['weekly_private'],
                    'includes_community' => $plan['community'],
                    'includes_libraries' => $plan['libraries'],
                    'features' => json_encode($plan['features'], JSON_UNESCAPED_UNICODE),
                    'gifts' => json_encode($gifts, JSON_UNESCAPED_UNICODE),
                    'tutoring_group_id' => null,
                    'units_count' => $units,
                    'session_minutes' => 60,
                    'duration_days' => (int) $months * 30,
                    'price' => $term['price'],
                    'original_price' => $original > $term['price'] ? $original : null,
                    'currency' => 'USD',
                    'is_active' => true,
                    'is_featured' => $plan['featured'] && (int) $months === 6,
                    'sort_order' => $plan['sort'] + (int) $months,
                    'updated_at' => $now,
                ];

                $existing = DB::table('service_packages')->where('slug', $slug)->first();
                if ($existing) {
                    DB::table('service_packages')->where('id', $existing->id)->update($payload);
                } else {
                    $payload['slug'] = $slug;
                    $payload['created_at'] = $now;
                    DB::table('service_packages')->insert($payload);
                }
            }
        }

        // Hide legacy non-commercial packs from the public commercial catalog.
        DB::table('service_packages')
            ->whereNull('plan_type')
            ->whereNull('tutoring_group_id')
            ->update(['is_active' => false, 'updated_at' => $now]);
    }

    public function down(): void
    {
        if (Schema::hasTable('student_service_entitlements')) {
            Schema::table('student_service_entitlements', function (Blueprint $table) {
                foreach (['includes_libraries', 'includes_community', 'weekly_private_sessions', 'weekly_group_sessions', 'term_months', 'plan_type'] as $col) {
                    if (Schema::hasColumn('student_service_entitlements', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('service_packages')) {
            Schema::table('service_packages', function (Blueprint $table) {
                foreach (['gifts', 'features', 'tagline', 'includes_libraries', 'includes_community', 'weekly_private_sessions', 'weekly_group_sessions', 'term_months', 'plan_type'] as $col) {
                    if (Schema::hasColumn('service_packages', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
