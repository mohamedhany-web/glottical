<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('crm_groups')) {
            Schema::create('crm_groups', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId('team_leader_id')->nullable()->constrained('users')->nullOnDelete();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('crm_group_members')) {
            Schema::create('crm_group_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('crm_group_id')->constrained('crm_groups')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('role', 32); // marketing | sales
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['crm_group_id', 'user_id', 'role'], 'crm_grp_member_unique');
                $table->index(['user_id', 'role'], 'crm_grp_user_role_idx');
            });
        }

        if (Schema::hasTable('sales_leads')) {
            Schema::table('sales_leads', function (Blueprint $table) {
                if (! Schema::hasColumn('sales_leads', 'marketing_owner_id')) {
                    $table->foreignId('marketing_owner_id')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
                }
                if (! Schema::hasColumn('sales_leads', 'crm_group_id')) {
                    $table->foreignId('crm_group_id')->nullable()->after('assigned_to')->constrained('crm_groups')->nullOnDelete();
                }
                if (! Schema::hasColumn('sales_leads', 'assigned_at')) {
                    $table->timestamp('assigned_at')->nullable()->after('crm_group_id');
                }
                if (! Schema::hasColumn('sales_leads', 'order_id')) {
                    $table->foreignId('order_id')->nullable()->after('converted_order_id')->constrained('orders')->nullOnDelete();
                }
                if (! Schema::hasColumn('sales_leads', 'enrollment_id')) {
                    $table->unsignedBigInteger('enrollment_id')->nullable()->after('order_id');
                }
            });

            // Immutable marketing owner for existing rows
            if (Schema::hasColumn('sales_leads', 'marketing_owner_id')) {
                DB::table('sales_leads')
                    ->whereNull('marketing_owner_id')
                    ->update(['marketing_owner_id' => DB::raw('created_by')]);
            }

            // Map legacy statuses
            $map = [
                'new' => 'new_lead',
                'qualified' => 'interested',
                'converted' => 'closed_won',
                'lost' => 'closed_lost',
            ];
            foreach ($map as $from => $to) {
                DB::table('sales_leads')->where('status', $from)->update(['status' => $to]);
            }
        }

        if (Schema::hasTable('orders') && ! Schema::hasColumn('orders', 'sales_lead_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('sales_lead_id')->nullable()->after('sales_owner_id')->constrained('sales_leads')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('crm_commissions')) {
            Schema::create('crm_commissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sales_lead_id')->constrained('sales_leads')->cascadeOnDelete();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('type', 32); // marketing | sales | team_leader
                $table->decimal('base_amount_egp', 12, 2);
                $table->decimal('commission_percent', 5, 2);
                $table->decimal('commission_amount_egp', 12, 2);
                $table->string('status', 32)->default('pending'); // pending | approved | paid
                $table->timestamp('approved_at')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['order_id', 'user_id', 'type'], 'crm_comm_order_user_type_uq');
            });
        }

        if (! Schema::hasTable('crm_audit_logs')) {
            Schema::create('crm_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('sales_lead_id')->nullable()->constrained('sales_leads')->nullOnDelete();
                $table->string('action', 64);
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index(['sales_lead_id', 'created_at'], 'crm_audit_lead_idx');
                $table->index(['action', 'created_at'], 'crm_audit_action_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_audit_logs');
        Schema::dropIfExists('crm_commissions');

        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'sales_lead_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropConstrainedForeignId('sales_lead_id');
            });
        }

        if (Schema::hasTable('sales_leads')) {
            Schema::table('sales_leads', function (Blueprint $table) {
                foreach (['enrollment_id', 'order_id', 'assigned_at', 'crm_group_id', 'marketing_owner_id'] as $col) {
                    if (Schema::hasColumn('sales_leads', $col)) {
                        if (in_array($col, ['crm_group_id', 'marketing_owner_id', 'order_id'], true)) {
                            $table->dropConstrainedForeignId($col);
                        } else {
                            $table->dropColumn($col);
                        }
                    }
                }
            });
        }

        Schema::dropIfExists('crm_group_members');
        Schema::dropIfExists('crm_groups');
    }
};
