<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tutoring_groups')) {
            Schema::table('tutoring_groups', function (Blueprint $table) {
                if (! Schema::hasColumn('tutoring_groups', 'hourly_rate')) {
                    $table->decimal('hourly_rate', 10, 2)->nullable()->after('price');
                }
                if (! Schema::hasColumn('tutoring_groups', 'sessions_per_month')) {
                    $table->unsignedTinyInteger('sessions_per_month')->default(8)->after('hourly_rate');
                }
                if (! Schema::hasColumn('tutoring_groups', 'whatsapp_group_url')) {
                    $table->string('whatsapp_group_url')->nullable()->after('sessions_per_month');
                }
                if (! Schema::hasColumn('tutoring_groups', 'learning_path')) {
                    $table->string('learning_path', 20)->nullable()->after('whatsapp_group_url'); // arabic|english
                }
            });
        }

        if (! Schema::hasTable('tutoring_group_cohorts')) {
            Schema::create('tutoring_group_cohorts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tutoring_group_id')->constrained('tutoring_groups')->cascadeOnDelete();
                $table->string('title');
                $table->string('slug');
                $table->dateTime('starts_at')->nullable();
                $table->json('study_days')->nullable(); // [1,3,6] ISO
                $table->time('study_time')->nullable();
                $table->string('timezone', 64)->default('Africa/Cairo');
                $table->unsignedSmallInteger('capacity')->default(8);
                $table->unsignedSmallInteger('enrolled_count')->default(0);
                $table->unsignedSmallInteger('min_enrollment')->default(3);
                $table->string('status', 20)->default('open'); // open|full|closed|postponed|completed
                $table->dateTime('postponed_to')->nullable();
                $table->string('whatsapp_group_url')->nullable();
                $table->dateTime('enrollment_closes_at')->nullable();
                $table->boolean('is_visible')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->unique(['tutoring_group_id', 'slug']);
                $table->index(['tutoring_group_id', 'status']);
                $table->index(['starts_at', 'status']);
            });
        }

        if (! Schema::hasTable('tutoring_group_packages')) {
            Schema::create('tutoring_group_packages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tutoring_group_id')->constrained('tutoring_groups')->cascadeOnDelete();
                $table->string('name');
                $table->unsignedTinyInteger('duration_months')->default(1);
                $table->unsignedSmallInteger('sessions_count')->default(8);
                $table->unsignedTinyInteger('sessions_per_month')->default(8);
                $table->decimal('hourly_rate', 10, 2)->default(0);
                $table->decimal('price', 10, 2)->default(0);
                $table->decimal('original_price', 10, 2)->nullable();
                $table->string('currency', 8)->default('USD');
                $table->boolean('is_active')->default(true);
                $table->boolean('is_featured')->default(false);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->index(['tutoring_group_id', 'is_active']);
            });
        }

        if (! Schema::hasTable('student_tutoring_subscriptions')) {
            Schema::create('student_tutoring_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('tutoring_group_id')->constrained('tutoring_groups')->cascadeOnDelete();
                $table->foreignId('tutoring_group_package_id')->nullable()->constrained('tutoring_group_packages')->nullOnDelete();
                $table->unsignedSmallInteger('sessions_total')->default(0);
                $table->unsignedSmallInteger('sessions_used')->default(0);
                $table->dateTime('starts_at')->nullable();
                $table->dateTime('expires_at')->nullable();
                $table->string('status', 20)->default('active'); // active|expired|cancelled
                $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->timestamps();

                $table->index(['user_id', 'status']);
                $table->index(['tutoring_group_id', 'status']);
            });
        }

        if (Schema::hasTable('tutoring_group_bookings')) {
            Schema::table('tutoring_group_bookings', function (Blueprint $table) {
                if (! Schema::hasColumn('tutoring_group_bookings', 'cohort_id')) {
                    $table->foreignId('cohort_id')->nullable()->after('tutoring_group_id')
                        ->constrained('tutoring_group_cohorts')->nullOnDelete();
                }
                if (! Schema::hasColumn('tutoring_group_bookings', 'tutoring_group_package_id')) {
                    $table->foreignId('tutoring_group_package_id')->nullable()->after('cohort_id')
                        ->constrained('tutoring_group_packages')->nullOnDelete();
                }
                if (! Schema::hasColumn('tutoring_group_bookings', 'student_tutoring_subscription_id')) {
                    $table->foreignId('student_tutoring_subscription_id')->nullable()->after('tutoring_group_package_id')
                        ->constrained('student_tutoring_subscriptions')->nullOnDelete();
                }
                if (! Schema::hasColumn('tutoring_group_bookings', 'classroom_meeting_id')) {
                    $table->foreignId('classroom_meeting_id')->nullable()->after('student_tutoring_subscription_id')
                        ->constrained('classroom_meetings')->nullOnDelete();
                }
                if (! Schema::hasColumn('tutoring_group_bookings', 'order_id')) {
                    $table->foreignId('order_id')->nullable()->after('classroom_meeting_id')
                        ->constrained('orders')->nullOnDelete();
                }
                if (! Schema::hasColumn('tutoring_group_bookings', 'payment_status')) {
                    $table->string('payment_status', 20)->default('none')->after('order_id');
                }
            });
        }

        if (Schema::hasTable('classroom_meetings') && ! Schema::hasColumn('classroom_meetings', 'tutoring_group_booking_id')) {
            Schema::table('classroom_meetings', function (Blueprint $table) {
                $table->foreignId('tutoring_group_booking_id')->nullable()->after('one_to_one_session_id')
                    ->constrained('tutoring_group_bookings')->nullOnDelete();
            });
        }

        if (Schema::hasTable('orders') && ! Schema::hasColumn('orders', 'tutoring_group_package_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('tutoring_group_id')->nullable()->after('advanced_course_id')
                    ->constrained('tutoring_groups')->nullOnDelete();
                $table->foreignId('tutoring_group_package_id')->nullable()->after('tutoring_group_id')
                    ->constrained('tutoring_group_packages')->nullOnDelete();
                $table->foreignId('tutoring_group_cohort_id')->nullable()->after('tutoring_group_package_id')
                    ->constrained('tutoring_group_cohorts')->nullOnDelete();
                $table->string('order_type', 40)->nullable()->after('tutoring_group_cohort_id'); // course|tutoring_package|tutoring_cohort
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                foreach (['tutoring_group_cohort_id', 'tutoring_group_package_id', 'tutoring_group_id', 'order_type'] as $col) {
                    if (Schema::hasColumn('orders', $col)) {
                        if (str_ends_with($col, '_id')) {
                            $table->dropConstrainedForeignId($col);
                        } else {
                            $table->dropColumn($col);
                        }
                    }
                }
            });
        }

        if (Schema::hasTable('classroom_meetings') && Schema::hasColumn('classroom_meetings', 'tutoring_group_booking_id')) {
            Schema::table('classroom_meetings', function (Blueprint $table) {
                $table->dropConstrainedForeignId('tutoring_group_booking_id');
            });
        }

        if (Schema::hasTable('tutoring_group_bookings')) {
            Schema::table('tutoring_group_bookings', function (Blueprint $table) {
                foreach (['payment_status', 'order_id', 'classroom_meeting_id', 'student_tutoring_subscription_id', 'tutoring_group_package_id', 'cohort_id'] as $col) {
                    if (Schema::hasColumn('tutoring_group_bookings', $col)) {
                        if (str_ends_with($col, '_id')) {
                            $table->dropConstrainedForeignId($col);
                        } else {
                            $table->dropColumn($col);
                        }
                    }
                }
            });
        }

        Schema::dropIfExists('student_tutoring_subscriptions');
        Schema::dropIfExists('tutoring_group_packages');
        Schema::dropIfExists('tutoring_group_cohorts');

        if (Schema::hasTable('tutoring_groups')) {
            Schema::table('tutoring_groups', function (Blueprint $table) {
                foreach (['learning_path', 'whatsapp_group_url', 'sessions_per_month', 'hourly_rate'] as $col) {
                    if (Schema::hasColumn('tutoring_groups', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
