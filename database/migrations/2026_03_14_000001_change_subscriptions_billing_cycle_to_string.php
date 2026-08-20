<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('subscriptions')) {
            return;
        }

        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE subscriptions MODIFY billing_cycle VARCHAR(20) NOT NULL DEFAULT \'monthly\'');
            // تحويل القيم الرقمية القديمة (1, 3, 12) إلى النص المقابل
            DB::statement("UPDATE subscriptions SET billing_cycle = CASE billing_cycle WHEN '1' THEN 'monthly' WHEN '3' THEN 'quarterly' WHEN '12' THEN 'yearly' ELSE billing_cycle END WHERE billing_cycle IN ('1', '3', '12')");
        } else {
            DB::statement('ALTER TABLE subscriptions ALTER COLUMN billing_cycle TYPE VARCHAR(20) USING billing_cycle::text');
            DB::statement("ALTER TABLE subscriptions ALTER COLUMN billing_cycle SET DEFAULT 'monthly'");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE subscriptions MODIFY billing_cycle INT NOT NULL DEFAULT 1');
        }
    }
};
