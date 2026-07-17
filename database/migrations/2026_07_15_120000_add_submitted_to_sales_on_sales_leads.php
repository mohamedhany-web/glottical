<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_leads', function (Blueprint $table) {
            if (! Schema::hasColumn('sales_leads', 'submitted_to_sales_at')) {
                $table->timestamp('submitted_to_sales_at')->nullable()->after('assigned_at');
            }
            if (! Schema::hasColumn('sales_leads', 'submitted_to_sales_by')) {
                $table->foreignId('submitted_to_sales_by')->nullable()->after('submitted_to_sales_at')
                    ->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales_leads', function (Blueprint $table) {
            if (Schema::hasColumn('sales_leads', 'submitted_to_sales_by')) {
                $table->dropConstrainedForeignId('submitted_to_sales_by');
            }
            if (Schema::hasColumn('sales_leads', 'submitted_to_sales_at')) {
                $table->dropColumn('submitted_to_sales_at');
            }
        });
    }
};
