<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('service_packages')) {
            Schema::create('service_packages', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('badge', 64)->nullable();
                $table->string('scope', 40)->default('global'); // global|tutoring_individual|tutoring_collective|private_lessons
                $table->foreignId('tutoring_group_id')->nullable()->constrained('tutoring_groups')->nullOnDelete();
                $table->unsignedInteger('units_count')->default(1);
                $table->unsignedInteger('duration_days')->nullable();
                $table->decimal('price', 12, 2)->default(0);
                $table->decimal('original_price', 12, 2)->nullable();
                $table->string('currency', 8)->default('EGP');
                $table->boolean('is_active')->default(true);
                $table->boolean('is_featured')->default(false);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
                $table->index(['scope', 'is_active', 'sort_order']);
            });
        }

        if (! Schema::hasTable('student_service_entitlements')) {
            Schema::create('student_service_entitlements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('service_package_id')->nullable()->constrained('service_packages')->nullOnDelete();
                $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->string('scope', 40)->default('global');
                $table->foreignId('tutoring_group_id')->nullable()->constrained('tutoring_groups')->nullOnDelete();
                $table->unsignedInteger('units_total')->default(0);
                $table->unsignedInteger('units_used')->default(0);
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->string('status', 20)->default('active'); // active|expired|cancelled
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'status', 'scope'], 'sse_user_status_scope_idx');
                $table->index(['user_id', 'tutoring_group_id', 'status'], 'sse_user_group_status_idx');
            });
        }

        if (Schema::hasTable('orders') && ! Schema::hasColumn('orders', 'service_package_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->unsignedBigInteger('service_package_id')->nullable()->after('tutoring_group_cohort_id');
                $table->foreign('service_package_id', 'orders_service_package_fk')
                    ->references('id')->on('service_packages')->nullOnDelete();
            });
        }

        if (Schema::hasTable('tutoring_group_bookings') && ! Schema::hasColumn('tutoring_group_bookings', 'student_service_entitlement_id')) {
            Schema::table('tutoring_group_bookings', function (Blueprint $table) {
                $table->unsignedBigInteger('student_service_entitlement_id')->nullable()->after('student_tutoring_subscription_id');
                $table->foreign('student_service_entitlement_id', 'tgb_sse_fk')
                    ->references('id')->on('student_service_entitlements')->nullOnDelete();
            });
        }

        if (Schema::hasTable('student_tutoring_subscriptions') && ! Schema::hasColumn('student_tutoring_subscriptions', 'student_service_entitlement_id')) {
            Schema::table('student_tutoring_subscriptions', function (Blueprint $table) {
                $table->unsignedBigInteger('student_service_entitlement_id')->nullable()->after('order_id');
                $table->foreign('student_service_entitlement_id', 'sts_sse_fk')
                    ->references('id')->on('student_service_entitlements')->nullOnDelete();
            });
        }

        if (Schema::hasTable('one_to_one_sessions') && ! Schema::hasColumn('one_to_one_sessions', 'student_service_entitlement_id')) {
            Schema::table('one_to_one_sessions', function (Blueprint $table) {
                $table->unsignedBigInteger('student_service_entitlement_id')->nullable()->after('student_course_enrollment_id');
                $table->foreign('student_service_entitlement_id', 'oto_sse_fk')
                    ->references('id')->on('student_service_entitlements')->nullOnDelete();
            });
        }

        // Migrate existing tutoring packages → service_packages
        if (Schema::hasTable('tutoring_group_packages') && Schema::hasTable('service_packages')) {
            $packages = DB::table('tutoring_group_packages')->orderBy('id')->get();
            foreach ($packages as $pkg) {
                $slug = 'tgp-'.$pkg->id.'-'.Str::slug((string) $pkg->name);
                $base = $slug;
                $n = 2;
                while (DB::table('service_packages')->where('slug', $slug)->exists()) {
                    $slug = $base.'-'.$n;
                    $n++;
                }

                $durationDays = max(1, (int) ($pkg->duration_months ?? 1)) * 30;

                DB::table('service_packages')->insert([
                    'name' => $pkg->name,
                    'slug' => $slug,
                    'description' => 'باقة مجموعة فردية (مرحّلة)',
                    'badge' => $pkg->is_featured ? 'مميزة' : null,
                    'scope' => 'tutoring_individual',
                    'tutoring_group_id' => $pkg->tutoring_group_id,
                    'units_count' => max(1, (int) ($pkg->sessions_count ?? 1)),
                    'duration_days' => $durationDays,
                    'price' => $pkg->price ?? 0,
                    'original_price' => $pkg->original_price,
                    'currency' => $pkg->currency ?: 'EGP',
                    'is_active' => (bool) ($pkg->is_active ?? true),
                    'is_featured' => (bool) ($pkg->is_featured ?? false),
                    'sort_order' => (int) ($pkg->sort_order ?? 0),
                    'created_at' => $pkg->created_at ?? now(),
                    'updated_at' => $pkg->updated_at ?? now(),
                ]);
            }
        }

        // Migrate existing subscriptions → entitlements
        if (Schema::hasTable('student_tutoring_subscriptions') && Schema::hasTable('student_service_entitlements')) {
            $subs = DB::table('student_tutoring_subscriptions')->orderBy('id')->get();
            foreach ($subs as $sub) {
                $servicePackageId = null;
                if ($sub->tutoring_group_package_id) {
                    $servicePackageId = DB::table('service_packages')
                        ->where('scope', 'tutoring_individual')
                        ->where('tutoring_group_id', $sub->tutoring_group_id)
                        ->where('units_count', (int) $sub->sessions_total)
                        ->orderBy('id')
                        ->value('id');
                }

                $entitlementId = DB::table('student_service_entitlements')->insertGetId([
                    'user_id' => $sub->user_id,
                    'service_package_id' => $servicePackageId,
                    'order_id' => $sub->order_id,
                    'scope' => 'tutoring_individual',
                    'tutoring_group_id' => $sub->tutoring_group_id,
                    'units_total' => (int) $sub->sessions_total,
                    'units_used' => (int) $sub->sessions_used,
                    'starts_at' => $sub->starts_at,
                    'expires_at' => $sub->expires_at,
                    'status' => $sub->status ?: 'active',
                    'notes' => 'migrated_from_subscription:'.$sub->id,
                    'created_at' => $sub->created_at ?? now(),
                    'updated_at' => $sub->updated_at ?? now(),
                ]);

                DB::table('student_tutoring_subscriptions')
                    ->where('id', $sub->id)
                    ->update(['student_service_entitlement_id' => $entitlementId]);

                if (Schema::hasColumn('tutoring_group_bookings', 'student_service_entitlement_id')) {
                    DB::table('tutoring_group_bookings')
                        ->where('student_tutoring_subscription_id', $sub->id)
                        ->update(['student_service_entitlement_id' => $entitlementId]);
                }
            }
        }

        // Default academy packages if none global
        if (Schema::hasTable('service_packages')) {
            $defaults = [
                ['name' => 'باقة تجريبية', 'slug' => 'trial-4', 'badge' => 'تجريبية', 'units' => 4, 'days' => 30, 'price' => 199, 'sort' => 1],
                ['name' => 'باقة أساسية', 'slug' => 'basic-8', 'badge' => 'الأكثر اختياراً', 'units' => 8, 'days' => 60, 'price' => 349, 'sort' => 2, 'featured' => true],
                ['name' => 'باقة مكثفة', 'slug' => 'intensive-12', 'badge' => 'الأقوى', 'units' => 12, 'days' => 90, 'price' => 499, 'sort' => 3],
            ];
            foreach ($defaults as $row) {
                if (DB::table('service_packages')->where('slug', $row['slug'])->exists()) {
                    continue;
                }
                DB::table('service_packages')->insert([
                    'name' => $row['name'],
                    'slug' => $row['slug'],
                    'description' => $row['units'].' حصص قابلة للاستخدام ضمن خدمات الأكاديمية حسب نطاق الباقة.',
                    'badge' => $row['badge'],
                    'scope' => 'global',
                    'tutoring_group_id' => null,
                    'units_count' => $row['units'],
                    'duration_days' => $row['days'],
                    'price' => $row['price'],
                    'original_price' => null,
                    'currency' => 'EGP',
                    'is_active' => true,
                    'is_featured' => (bool) ($row['featured'] ?? false),
                    'sort_order' => $row['sort'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('one_to_one_sessions') && Schema::hasColumn('one_to_one_sessions', 'student_service_entitlement_id')) {
            Schema::table('one_to_one_sessions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('student_service_entitlement_id');
            });
        }
        if (Schema::hasTable('student_tutoring_subscriptions') && Schema::hasColumn('student_tutoring_subscriptions', 'student_service_entitlement_id')) {
            Schema::table('student_tutoring_subscriptions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('student_service_entitlement_id');
            });
        }
        if (Schema::hasTable('tutoring_group_bookings') && Schema::hasColumn('tutoring_group_bookings', 'student_service_entitlement_id')) {
            Schema::table('tutoring_group_bookings', function (Blueprint $table) {
                $table->dropConstrainedForeignId('student_service_entitlement_id');
            });
        }
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'service_package_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropConstrainedForeignId('service_package_id');
            });
        }

        Schema::dropIfExists('student_service_entitlements');
        Schema::dropIfExists('service_packages');
    }
};
