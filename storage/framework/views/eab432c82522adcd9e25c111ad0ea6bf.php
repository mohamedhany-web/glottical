
<?php $__env->startSection('title', 'تعديل سيرفر: ' . $liveServer->name); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('admin.live-servers.index')); ?>" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 transition-colors"><i class="fas fa-arrow-right"></i></a>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white"><i class="fas fa-edit text-amber-500 ml-2"></i>تعديل سيرفر البث</h1>
        </div>
        <?php if(!empty($liveServer->config['ssh_username'] ?? null)): ?>
        <a href="<?php echo e(route('admin.live-servers.ssh-browse', $liveServer)); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-medium transition-colors">
            <i class="fas fa-folder-open"></i> تصفح الملفات عبر SSH
        </a>
        <?php endif; ?>
    </div>

    <form method="POST" action="<?php echo e(route('admin.live-servers.update', $liveServer)); ?>" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 space-y-5">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <div class="grid md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">اسم السيرفر</label>
                <input type="text" name="name" value="<?php echo e(old('name', $liveServer->name)); ?>" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">النطاق</label>
                <input type="text" name="domain" value="<?php echo e(old('domain', $liveServer->domain)); ?>" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">النوع</label>
                <select name="provider" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <option value="jitsi" <?php echo e($liveServer->provider === 'jitsi' ? 'selected' : ''); ?>>Jitsi Meet</option>
                    <option value="custom" <?php echo e($liveServer->provider === 'custom' ? 'selected' : ''); ?>>مخصص</option>
                </select>
            </div>
            <?php if($liveServer->provider === 'jitsi'): ?>
            <div class="md:col-span-2 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-900/50 p-4 text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                <p class="font-semibold text-slate-800 dark:text-slate-100 mb-1"><i class="fas fa-info-circle text-blue-500 ml-1"></i> شعار «Jitsi» داخل الاجتماع</p>
                <p>إن بقيت كلمة أو شعار Jitsi ظاهرة رغم إعدادات المنصة، فالسبب أن <strong>خادم Jitsi</strong> (وليس Laravel) يحدد ذلك من ملف الواجهة على السيرفر. على هذا السيرفر عدّل <code class="text-xs bg-slate-200 dark:bg-slate-700 px-1.5 py-0.5 rounded">interface_config.js</code> أو نسخة Docker <code class="text-xs bg-slate-200 dark:bg-slate-700 px-1.5 py-0.5 rounded">custom-interface_config.js</code> واضبط متغيرات العلامة إلى <code class="text-xs bg-slate-200 dark:bg-slate-700 px-1 rounded">false</code> ثم أعد تحميل الواجهة.</p>
            </div>
            <?php endif; ?>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">الحالة</label>
                <select name="status" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <option value="active" <?php echo e($liveServer->status === 'active' ? 'selected' : ''); ?>>نشط</option>
                    <option value="inactive" <?php echo e($liveServer->status === 'inactive' ? 'selected' : ''); ?>>معطل</option>
                    <option value="maintenance" <?php echo e($liveServer->status === 'maintenance' ? 'selected' : ''); ?>>صيانة</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">IP</label>
                <input type="text" name="ip_address" value="<?php echo e(old('ip_address', $liveServer->ip_address)); ?>" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">الحد الأقصى</label>
                <input type="number" name="max_participants" value="<?php echo e(old('max_participants', $liveServer->max_participants)); ?>" min="2" max="10000" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">رابط لوحة التحكم بالسيرفر (اختياري)</label>
                <input type="url" name="control_panel_url" value="<?php echo e(old('control_panel_url', $liveServer->control_panel_url)); ?>" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white" placeholder="https://panel.example.com">
                <p class="text-xs text-slate-500 mt-1">لرؤية الملفات والتحكم الكامل افتح هذا الرابط من صفحة «لوحة التحكم بالسيرفرات».</p>
            </div>
            <div class="md:col-span-2 border-t border-slate-200 dark:border-slate-600 pt-5 mt-2">
                <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 mb-3"><i class="fas fa-terminal text-emerald-500 ml-1"></i> الاتصال عبر SSH (لتصفح الملفات من المنصة)</h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">عنوان SSH (Host)</label>
                        <input type="text" name="ssh_host" value="<?php echo e(old('ssh_host', $liveServer->config['ssh_host'] ?? $liveServer->ip_address ?? $liveServer->domain)); ?>" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white" placeholder="IP أو النطاق">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">المنفذ (Port)</label>
                        <input type="number" name="ssh_port" value="<?php echo e(old('ssh_port', $liveServer->config['ssh_port'] ?? 22)); ?>" min="1" max="65535" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">اسم المستخدم</label>
                        <input type="text" name="ssh_username" value="<?php echo e(old('ssh_username', $liveServer->config['ssh_username'] ?? '')); ?>" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white" placeholder="root أو ubuntu">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">كلمة المرور</label>
                        <input type="password" name="ssh_password" value="" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white" placeholder="اتركه فارغاً للإبقاء على الحالية" autocomplete="new-password">
                    </div>
                </div>
                <p class="text-xs text-slate-500 mt-2">بعد الحفظ يمكنك فتح «تصفح الملفات عبر SSH» من صفحة السيرفر أو لوحة التحكم بالسيرفرات.</p>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">ملاحظات</label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white"><?php echo e(old('notes', $liveServer->notes)); ?></textarea>
            </div>
        </div>
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition-colors"><i class="fas fa-save ml-1"></i> حفظ</button>
            <a href="<?php echo e(route('admin.live-servers.index')); ?>" class="px-6 py-2.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-medium hover:bg-slate-300 transition-colors">إلغاء</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\live-servers\edit.blade.php ENDPATH**/ ?>