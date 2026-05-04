
<?php $__env->startSection('title', 'لوحة التحكم بالسيرفرات'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white"><i class="fas fa-tachometer-alt text-violet-500 ml-2"></i>لوحة التحكم بالسيرفرات (VPS)</h1>
            <p class="text-sm text-slate-500 mt-1">متابعة السيرفرات وفتح لوحة التحكم لكل سيرفر — التحديث كل 30 ثانية</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('admin.live-servers.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium hover:bg-slate-300 transition-colors">
                <i class="fas fa-server"></i> قائمة السيرفرات
            </a>
            <button type="button" onclick="location.reload()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-600 text-white font-medium transition-colors">
                <i class="fas fa-sync-alt"></i> تحديث الآن
            </button>
        </div>
    </div>

    <?php if($defaultJitsiDomain ?? null): ?>
    <div class="bg-slate-100 dark:bg-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-600 px-4 py-2 text-sm text-slate-600 dark:text-slate-300">
        <i class="fas fa-link text-cyan-500 ml-1"></i>
        <strong>النطاق الافتراضي:</strong> <code class="bg-slate-200 dark:bg-slate-600 px-1.5 py-0.5 rounded"><?php echo e($defaultJitsiDomain); ?></code>
    </div>
    <?php endif; ?>

    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4 text-sm text-amber-800 dark:text-amber-200">
        <i class="fas fa-info-circle ml-1"></i>
        لرؤية <strong>جميع ملفات السيرفر</strong> والتحكم الكامل (ملفات، أوامر، إعدادات) استخدم زر <strong>«فتح لوحة التحكم»</strong> بعد إضافة رابط لوحة التحكم في <a href="<?php echo e(route('admin.live-servers.index')); ?>" class="underline font-semibold">تعديل السيرفر</a> (cPanel، Plesk، Webmin، أو أي واجهة تستخدمها على VPS).
    </div>

    <?php if(session('success')): ?>
    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-4 text-emerald-700 dark:text-emerald-400 text-sm"><i class="fas fa-check-circle ml-1"></i> <?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-red-700 dark:text-red-400 text-sm"><i class="fas fa-exclamation-circle ml-1"></i> <?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5" id="servers-grid">
        <?php $__empty_1 = true; $__currentLoopData = $servers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $server): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $isDefault = ($defaultJitsiDomain ?? '') && trim($defaultJitsiDomain) === trim($server->domain);
            $loadCount = $server->active_sessions_count;
            $loadPct = $server->max_participants > 0 ? min(100, (int) round(($loadCount / $server->max_participants) * 100)) : 0;
            $hasPanel = $server->control_panel_url !== '';
        ?>
        <div class="bg-white dark:bg-slate-800 rounded-xl border <?php echo e($isDefault ? 'border-cyan-500 ring-1 ring-cyan-500/30' : 'border-slate-200 dark:border-slate-700'); ?> shadow-lg overflow-hidden">
            <div class="p-5 border-b border-slate-100 dark:border-slate-700">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-white text-lg"><?php echo e($server->name); ?></h3>
                        <p class="text-sm text-slate-500 font-mono mt-0.5"><?php echo e($server->domain); ?></p>
                        <?php if($server->ip_address): ?>
                        <p class="text-xs text-slate-400 mt-0.5">IP: <?php echo e($server->ip_address); ?></p>
                        <?php endif; ?>
                        <?php if($isDefault): ?>
                        <span class="inline-block mt-2 px-2 py-0.5 rounded bg-cyan-100 dark:bg-cyan-900/40 text-cyan-700 dark:text-cyan-400 text-xs font-medium">نطاق افتراضي</span>
                        <?php endif; ?>
                    </div>
                    <?php if($server->status === 'active'): ?>
                    <span class="px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 text-xs font-semibold">نشط</span>
                    <?php elseif($server->status === 'maintenance'): ?>
                    <span class="px-2.5 py-1 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-600 text-xs font-semibold">صيانة</span>
                    <?php else: ?>
                    <span class="px-2.5 py-1 rounded-full bg-slate-200 dark:bg-slate-600 text-slate-500 text-xs font-semibold">معطل</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="p-5 space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">الجلسات النشطة</span>
                    <span class="font-bold text-slate-700 dark:text-white"><?php echo e($loadCount); ?> / <?php echo e($server->max_participants); ?></span>
                </div>
                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all <?php echo e($loadPct > 80 ? 'bg-red-500' : ($loadPct > 50 ? 'bg-amber-500' : 'bg-emerald-500')); ?>" style="width: <?php echo e($loadPct); ?>%"></div>
                </div>

                <?php $hasSsh = !empty($server->config['ssh_username'] ?? null); ?>
                <?php if($hasSsh): ?>
                <a href="<?php echo e(route('admin.live-servers.ssh-browse', $server)); ?>" class="flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-semibold transition-colors shadow-lg shadow-emerald-500/25">
                    <i class="fas fa-folder-open"></i> تصفح الملفات عبر SSH
                </a>
                <?php endif; ?>
                <?php if($hasPanel): ?>
                <a href="<?php echo e($server->control_panel_url); ?>" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-violet-500 hover:bg-violet-600 text-white font-semibold transition-colors shadow-lg shadow-violet-500/25">
                    <i class="fas fa-external-link-alt"></i> فتح لوحة التحكم بالسيرفر
                </a>
                <p class="text-xs text-slate-500 text-center">ملفات، إعدادات، وتحكم كامل على VPS</p>
                <?php elseif(!$hasSsh): ?>
                <p class="text-xs text-slate-500 text-center py-2">أضف بيانات SSH أو رابط لوحة التحكم من <a href="<?php echo e(route('admin.live-servers.edit', $server)); ?>" class="text-cyan-500 underline">تعديل السيرفر</a></p>
                <?php endif; ?>

                <div class="flex flex-wrap gap-2 pt-2 border-t border-slate-100 dark:border-slate-700">
                    <form method="POST" action="<?php echo e(route('admin.live-servers.test-connection', $server)); ?>" class="inline"><?php echo csrf_field(); ?><button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-cyan-50 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400 rounded-lg text-xs font-medium hover:bg-cyan-100"><i class="fas fa-plug"></i> اختبار</button></form>
                    <?php if($server->status === 'active' && !$isDefault): ?>
                    <form method="POST" action="<?php echo e(route('admin.live-servers.set-default', $server)); ?>" class="inline"><?php echo csrf_field(); ?><button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg text-xs font-medium hover:bg-slate-200">افتراضي</button></form>
                    <?php endif; ?>
                    <a href="<?php echo e(route('admin.live-servers.edit', $server)); ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg text-xs font-medium hover:bg-slate-200"><i class="fas fa-cog"></i> تعديل</a>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="md:col-span-3 text-center py-16 rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-700">
            <i class="fas fa-server text-5xl text-slate-300 dark:text-slate-600 mb-4"></i>
            <p class="text-slate-500 font-medium">لا توجد سيرفرات مضافة</p>
            <a href="<?php echo e(route('admin.live-servers.create')); ?>" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 bg-cyan-500 hover:bg-cyan-600 text-white rounded-xl font-semibold transition-colors"><i class="fas fa-plus"></i> إضافة سيرفر</a>
        </div>
        <?php endif; ?>
    </div>

    <p class="text-xs text-slate-400 text-center">آخر تحديث: <span id="last-update"><?php echo e(now()->format('H:i:s')); ?></span> — الصفحة تُحدَّث تلقائياً كل 30 ثانية</p>
</div>

<?php if($servers->isNotEmpty()): ?>
<script>
(function() {
    var interval = 30 * 1000;
    setTimeout(function reload() {
        window.location.reload();
    }, interval);
})();
</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\live-servers\control.blade.php ENDPATH**/ ?>