<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payments') || Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        // Online gateways (fawaterak / kashier) were written as ENUM values and truncated → 500 on invoice create after payment.
        DB::statement("ALTER TABLE `payments` MODIFY COLUMN `payment_gateway` VARCHAR(40) NULL");
    }

    public function down(): void
    {
        if (! Schema::hasTable('payments') || Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("UPDATE `payments` SET `payment_gateway` = 'other' WHERE `payment_gateway` IS NOT NULL AND `payment_gateway` NOT IN ('manual', 'moyasar', 'stripe', 'paypal', 'other')");
        DB::statement("ALTER TABLE `payments` MODIFY COLUMN `payment_gateway` ENUM('manual', 'moyasar', 'stripe', 'paypal', 'other') NULL");
    }
};
