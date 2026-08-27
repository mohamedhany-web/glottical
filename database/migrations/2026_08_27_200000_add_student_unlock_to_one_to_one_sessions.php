<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('one_to_one_sessions')) {
            return;
        }

        Schema::table('one_to_one_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('one_to_one_sessions', 'student_unlocked_at')) {
                $table->timestamp('student_unlocked_at')->nullable()->after('notes');
            }
            if (! Schema::hasColumn('one_to_one_sessions', 'student_unlocked_by_user_id')) {
                $table->foreignId('student_unlocked_by_user_id')
                    ->nullable()
                    ->after('student_unlocked_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('one_to_one_sessions')) {
            return;
        }

        Schema::table('one_to_one_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('one_to_one_sessions', 'student_unlocked_by_user_id')) {
                $table->dropConstrainedForeignId('student_unlocked_by_user_id');
            }
            if (Schema::hasColumn('one_to_one_sessions', 'student_unlocked_at')) {
                $table->dropColumn('student_unlocked_at');
            }
        });
    }
};
