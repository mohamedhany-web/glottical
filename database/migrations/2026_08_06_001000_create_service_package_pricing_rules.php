<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_package_pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('scope', 40);
            $table->decimal('price_per_session', 10, 2);
            $table->unsignedInteger('min_sessions')->default(1);
            $table->unsignedInteger('max_sessions')->default(24);
            $table->unsignedInteger('session_step')->default(1);
            $table->unsignedSmallInteger('session_minutes')->default(60);
            $table->unsignedInteger('duration_days')->default(60);
            $table->json('discount_tiers')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['is_active', 'sort_order'], 'sppr_active_sort_idx');
        });

        if (Schema::hasTable('orders') && ! Schema::hasColumn('orders', 'custom_package_data')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->json('custom_package_data')->nullable()->after('service_package_id');
            });
        }

        DB::table('service_packages')->update(['currency' => 'USD']);

        DB::table('service_package_pricing_rules')->insert([
            [
                'name' => 'حصص فردية',
                'scope' => 'tutoring_individual',
                'price_per_session' => 25,
                'min_sessions' => 2,
                'max_sessions' => 24,
                'session_step' => 1,
                'session_minutes' => 60,
                'duration_days' => 90,
                'discount_tiers' => json_encode([
                    ['min_sessions' => 8, 'discount_percent' => 5],
                    ['min_sessions' => 12, 'discount_percent' => 10],
                    ['min_sessions' => 20, 'discount_percent' => 15],
                ]),
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'حصص جماعية / مدرسة',
                'scope' => 'tutoring_collective',
                'price_per_session' => 15,
                'min_sessions' => 4,
                'max_sessions' => 24,
                'session_step' => 1,
                'session_minutes' => 60,
                'duration_days' => 90,
                'discount_tiers' => json_encode([
                    ['min_sessions' => 8, 'discount_percent' => 5],
                    ['min_sessions' => 16, 'discount_percent' => 10],
                ]),
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'حصص خاصة 1:1',
                'scope' => 'private_lessons',
                'price_per_session' => 30,
                'min_sessions' => 2,
                'max_sessions' => 20,
                'session_step' => 1,
                'session_minutes' => 60,
                'duration_days' => 90,
                'discount_tiers' => json_encode([
                    ['min_sessions' => 8, 'discount_percent' => 5],
                    ['min_sessions' => 12, 'discount_percent' => 10],
                ]),
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'custom_package_data')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('custom_package_data');
            });
        }

        Schema::dropIfExists('service_package_pricing_rules');
    }
};
