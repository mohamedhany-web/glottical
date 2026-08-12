<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('library_videos', function (Blueprint $table) {
            if (! Schema::hasColumn('library_videos', 'audience')) {
                $table->string('audience', 32)->default('general')->after('created_by');
            }
            if (! Schema::hasColumn('library_videos', 'instructor_id')) {
                $table->foreignId('instructor_id')->nullable()->after('audience')->constrained('users')->nullOnDelete();
            }
            $table->index(['audience', 'instructor_id', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::table('library_videos', function (Blueprint $table) {
            if (Schema::hasColumn('library_videos', 'instructor_id')) {
                $table->dropConstrainedForeignId('instructor_id');
            }
            if (Schema::hasColumn('library_videos', 'audience')) {
                $table->dropColumn('audience');
            }
        });
    }
};
