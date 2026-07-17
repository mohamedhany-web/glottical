<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('live_settings')) {
            return;
        }

        $now = now();

        // إنهاء الجلسة إذا غادر المدرب ولم يعد — بدل انتظار 3 ساعات والبث فارغ
        $exists = DB::table('live_settings')->where('key', 'idle_end_minutes')->exists();
        if (! $exists) {
            DB::table('live_settings')->insert([
                'key' => 'idle_end_minutes',
                'value' => '20',
                'type' => 'integer',
                'group' => 'general',
                'label' => 'إنهاء تلقائي بعد مغادرة المدرب (دقيقة)',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // خفض الحد الأقصى الافتراضي من 180 إلى 90 إن كان ما زال على القيمة القديمة
        DB::table('live_settings')
            ->where('key', 'auto_end_minutes')
            ->where('value', '180')
            ->update([
                'value' => '90',
                'label' => 'إنهاء تلقائي بعد أقصى مدة للبث (دقيقة)',
                'updated_at' => $now,
            ]);
    }

    public function down(): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('live_settings')) {
            return;
        }

        DB::table('live_settings')->where('key', 'idle_end_minutes')->delete();

        DB::table('live_settings')
            ->where('key', 'auto_end_minutes')
            ->where('value', '90')
            ->update([
                'value' => '180',
                'updated_at' => now(),
            ]);
    }
};
