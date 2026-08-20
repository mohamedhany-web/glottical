<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('subscriptions')) {
            return;
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'teacher_plan_key')) {
                $table->string('teacher_plan_key')
                    ->nullable()
                    ->after('subscription_type');
            }

            if (!Schema::hasColumn('subscriptions', 'features')) {
                $table->json('features')
                    ->nullable()
                    ->after('billing_cycle');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'features')) {
                $table->dropColumn('features');
            }

            if (Schema::hasColumn('subscriptions', 'teacher_plan_key')) {
                $table->dropColumn('teacher_plan_key');
            }
        });
    }
};

