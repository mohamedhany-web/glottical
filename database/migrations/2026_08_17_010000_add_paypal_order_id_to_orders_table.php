<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'paypal_order_id')) {
                $table->string('paypal_order_id', 64)->nullable()->after('fawaterak_invoice_id');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('orders') || ! Schema::hasColumn('orders', 'paypal_order_id')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('paypal_order_id');
        });
    }
};
