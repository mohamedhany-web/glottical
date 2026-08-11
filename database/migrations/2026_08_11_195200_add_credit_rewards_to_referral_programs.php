<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('referral_programs', function (Blueprint $table) {
            if (! Schema::hasColumn('referral_programs', 'reward_mode')) {
                $table->string('reward_mode', 20)->default('credits')->after('description');
            }
            if (! Schema::hasColumn('referral_programs', 'credit_scope')) {
                $table->string('credit_scope', 40)->default('private_lessons')->after('reward_mode');
            }
            if (! Schema::hasColumn('referral_programs', 'referred_credit_units')) {
                $table->unsignedSmallInteger('referred_credit_units')->default(1)->after('credit_scope');
            }
            if (! Schema::hasColumn('referral_programs', 'referrer_credit_units')) {
                $table->unsignedSmallInteger('referrer_credit_units')->default(1)->after('referred_credit_units');
            }
            if (! Schema::hasColumn('referral_programs', 'credit_duration_days')) {
                $table->unsignedSmallInteger('credit_duration_days')->nullable()->after('referrer_credit_units');
            }
            if (! Schema::hasColumn('referral_programs', 'grant_referred_on')) {
                $table->string('grant_referred_on', 32)->default('first_purchase')->after('credit_duration_days');
            }
            if (! Schema::hasColumn('referral_programs', 'grant_referrer_on')) {
                $table->string('grant_referrer_on', 32)->default('first_purchase')->after('grant_referred_on');
            }
            if (! Schema::hasColumn('referral_programs', 'share_message_ar')) {
                $table->text('share_message_ar')->nullable()->after('grant_referrer_on');
            }
            if (! Schema::hasColumn('referral_programs', 'share_message_en')) {
                $table->text('share_message_en')->nullable()->after('share_message_ar');
            }
        });

        Schema::table('referrals', function (Blueprint $table) {
            if (! Schema::hasColumn('referrals', 'referred_entitlement_id')) {
                $table->unsignedBigInteger('referred_entitlement_id')->nullable()->after('auto_coupon_id');
            }
            if (! Schema::hasColumn('referrals', 'referrer_entitlement_id')) {
                $table->unsignedBigInteger('referrer_entitlement_id')->nullable()->after('referred_entitlement_id');
            }
            if (! Schema::hasColumn('referrals', 'referred_units_granted')) {
                $table->unsignedSmallInteger('referred_units_granted')->default(0)->after('referrer_entitlement_id');
            }
            if (! Schema::hasColumn('referrals', 'referrer_units_granted')) {
                $table->unsignedSmallInteger('referrer_units_granted')->default(0)->after('referred_units_granted');
            }
        });
    }

    public function down(): void
    {
        Schema::table('referral_programs', function (Blueprint $table) {
            foreach ([
                'reward_mode', 'credit_scope', 'referred_credit_units', 'referrer_credit_units',
                'credit_duration_days', 'grant_referred_on', 'grant_referrer_on',
                'share_message_ar', 'share_message_en',
            ] as $col) {
                if (Schema::hasColumn('referral_programs', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('referrals', function (Blueprint $table) {
            foreach ([
                'referred_entitlement_id', 'referrer_entitlement_id',
                'referred_units_granted', 'referrer_units_granted',
            ] as $col) {
                if (Schema::hasColumn('referrals', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
