<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * يضمن وجود جدول subscriptions قبل أي هجرة تعتمد عليه.
 * بعض السيرفرات تفتقده فتنهار صفحات البث (canHostLiveSession).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('subscriptions')) {
            return;
        }

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('subscription_type');
            $table->string('teacher_plan_key')->nullable();
            $table->string('plan_name');
            $table->decimal('price', 10, 2)->default(0);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status', 32)->default('active');
            $table->boolean('auto_renew')->default(false);
            $table->string('billing_cycle', 20)->default('monthly');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->json('features')->nullable();
            $table->json('feature_limits')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('end_date');

            if (Schema::hasTable('users')) {
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            }
            if (Schema::hasTable('invoices')) {
                $table->foreign('invoice_id')->references('id')->on('invoices')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        // لا نحذف — قد يكون الجدول مستخدماً في المحاسبة.
    }
};
