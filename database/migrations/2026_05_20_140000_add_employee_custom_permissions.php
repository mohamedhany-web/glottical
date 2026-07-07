<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (! Schema::hasColumn('users', 'employee_permissions_custom')) {
                    $table->boolean('employee_permissions_custom')->default(false)->after('employee_notes');
                }
                if (! Schema::hasColumn('users', 'employee_permissions')) {
                    $table->json('employee_permissions')->nullable()->after('employee_permissions_custom');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'employee_permissions')) {
                    $table->dropColumn('employee_permissions');
                }
                if (Schema::hasColumn('users', 'employee_permissions_custom')) {
                    $table->dropColumn('employee_permissions_custom');
                }
            });
        }
    }
};
