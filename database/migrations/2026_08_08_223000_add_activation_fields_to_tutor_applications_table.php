<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tutor_applications')) {
            return;
        }

        Schema::table('tutor_applications', function (Blueprint $table) {
            if (! Schema::hasColumn('tutor_applications', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('reviewed_by')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('tutor_applications', 'activated_at')) {
                $table->timestamp('activated_at')->nullable()->after('user_id');
            }
            if (! Schema::hasColumn('tutor_applications', 'activated_by')) {
                $table->foreignId('activated_by')->nullable()->after('activated_at')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('tutor_applications')) {
            return;
        }

        Schema::table('tutor_applications', function (Blueprint $table) {
            if (Schema::hasColumn('tutor_applications', 'activated_by')) {
                $table->dropConstrainedForeignId('activated_by');
            }
            if (Schema::hasColumn('tutor_applications', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
            if (Schema::hasColumn('tutor_applications', 'activated_at')) {
                $table->dropColumn('activated_at');
            }
        });
    }
};
