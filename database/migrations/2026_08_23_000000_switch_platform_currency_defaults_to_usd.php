<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Switch stored platform currency defaults from EGP to USD.
 * Does NOT convert numeric amounts (no FX).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('wallets') && Schema::hasColumn('wallets', 'currency')) {
            DB::table('wallets')->where('currency', 'EGP')->update(['currency' => 'USD']);
        }

        if (Schema::hasTable('settings') && Schema::hasColumn('settings', 'key') && Schema::hasColumn('settings', 'value')) {
            DB::table('settings')
                ->whereIn('key', ['kashier_currency', 'fawaterak_currency', 'paypal_currency'])
                ->where('value', 'EGP')
                ->update(['value' => 'USD']);
        }

        foreach (['service_packages', 'packages', 'tutoring_groups', 'orders', 'payments', 'invoices'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'currency')) {
                DB::table($table)->where('currency', 'EGP')->update(['currency' => 'USD']);
            }
        }

        // Ensure course USD prices exist for checkout (copy from legacy fields; no FX)
        if (Schema::hasTable('advanced_courses') && Schema::hasColumn('advanced_courses', 'price_usd')) {
            DB::table('advanced_courses')
                ->where(function ($q) {
                    $q->whereNull('price_usd')->orWhere('price_usd', '');
                })
                ->orderBy('id')
                ->chunkById(200, function ($rows) {
                    foreach ($rows as $row) {
                        $usd = $row->price ?? null;
                        if (($usd === null || $usd === '') && Schema::hasColumn('advanced_courses', 'price_egp')) {
                            $usd = $row->price_egp ?? null;
                        }
                        if ($usd === null || $usd === '') {
                            continue;
                        }
                        $sale = $row->price_after_discount ?? null;
                        if (($sale === null || $sale === '') && isset($row->price_egp_after_discount)) {
                            $sale = $row->price_egp_after_discount;
                        }
                        DB::table('advanced_courses')->where('id', $row->id)->update([
                            'price_usd' => $usd,
                            'price_usd_after_discount' => $sale,
                        ]);
                    }
                });
        }
    }

    public function down(): void
    {
        // Irreversible without FX; leave data as USD.
    }
};
