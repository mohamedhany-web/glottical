<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('crm_reports')) {
            Schema::create('crm_reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('crm_group_id')->nullable()->constrained('crm_groups')->nullOnDelete();
                $table->string('type', 32); // weekly | monthly | ad_hoc
                $table->date('period_start')->nullable();
                $table->date('period_end')->nullable();
                $table->string('title');
                $table->text('summary')->nullable();
                $table->string('file_path')->nullable();
                $table->string('file_name')->nullable();
                $table->string('status', 32)->default('submitted'); // draft | submitted | reviewed
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->text('admin_notes')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'type', 'status']);
                $table->index(['period_start', 'period_end']);
            });
        }

        if (! Schema::hasTable('crm_messages')) {
            Schema::create('crm_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('recipient_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('crm_group_id')->nullable()->constrained('crm_groups')->nullOnDelete();
                $table->foreignId('sales_lead_id')->nullable()->constrained('sales_leads')->nullOnDelete();
                $table->text('body');
                $table->string('attachment_path')->nullable();
                $table->string('attachment_name')->nullable();
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->index(['recipient_id', 'read_at']);
                $table->index(['crm_group_id', 'created_at']);
                $table->index(['sales_lead_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_messages');
        Schema::dropIfExists('crm_reports');
    }
};
