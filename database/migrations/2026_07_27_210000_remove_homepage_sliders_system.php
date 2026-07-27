<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('homepage_sliders');

        if (Schema::hasTable('permissions')) {
            $permissionId = DB::table('permissions')->where('name', 'manage.homepage-sliders')->value('id');
            if ($permissionId) {
                if (Schema::hasTable('role_permissions')) {
                    DB::table('role_permissions')->where('permission_id', $permissionId)->delete();
                }
                if (Schema::hasTable('user_permissions')) {
                    DB::table('user_permissions')->where('permission_id', $permissionId)->delete();
                }
                DB::table('permissions')->where('id', $permissionId)->delete();
            }
        }
    }

    public function down(): void
    {
        // Feature removed intentionally; recreate via historical migrations if needed.
    }
};
