
<?php $__env->startSection('title', 'إضافة سيرفر بث'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="<?php echo e(route('admin.live-servers.index')); ?>" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 transition-colors"><i class="fas fa-arrow-right"></i></a>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white"><i class="fas fa-plus-circle text-cyan-500 ml-2"></i>إضافة سيرفر بث</h1>
    </div>

    <form method="POST" action="<?php echo e(route('admin.live-servers.store')); ?>" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 space-y-5">
        <?php echo csrf_field(); ?>
        <div class="grid md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">اسم السيرفر <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="<?php echo e(old('name')); ?>" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white" placeholder="مثال: Jitsi Server 1">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">النطاق (Domain) <span class="text-red-500">*</span></label>
                <input type="text" name="domain" value="<?php echo e(old('domain')); ?>" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white" placeholder="live.Muallimx.com">
                <?php $__errorArgs = ['domain'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">نوع المنصة <span class="text-red-500">*</span></label>
                <select name="provider" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <option value="jitsi" <?php echo e(old('provider') === 'jitsi' ? 'selected' : ''); ?>>Jitsi Meet</option>
                    <option value="custom" <?php echo e(old('provider') === 'custom' ? 'selected' : ''); ?>>مخصص</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">عنوان IP (اختياري)</label>
                <input type="text" name="ip_address" value="<?php echo e(old('ip_address')); ?>" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white" placeholder="xxx.xxx.xxx.xxx">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">الحد الأقصى للمشاركين <span class="text-red-500">*</span></label>
                <input type="number" name="max_participants" value="<?php echo e(old('max_participants', 100)); ?>" min="2" max="10000" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">رابط لوحة التحكم بالسيرفر (اختياري)</label>
                <input type="url" name="control_panel_url" value="<?php echo e(old('control_panel_url')); ?>" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white" placeholder="https://panel.example.com أو cPanel / Plesk / Webmin">
                <p class="text-xs text-slate-500 mt-1">رابط لوحة التحكم (cPanel، Plesk، Webmin، أو أي واجهة لإدارة الملفات والسيرفر) لفتحها من صفحة «لوحة التحكم بالسيرفرات».</p>
            </div>
            <div class="md:col-span-2 border-t border-slate-200 dark:border-slate-600 pt-5 mt-2">
                <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 mb-3"><i class="fas fa-terminal text-emerald-500 ml-1"></i> الاتصال عبر SSH (لتصفح الملفات من المنصة)</h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">عنوان SSH (Host)</label>
                        <input type="text" name="ssh_host" value="<?php echo e(old('ssh_host')); ?>" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white" placeholder="IP أو النطاق">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">المنفذ (Port)</label>
                        <input type="number" name="ssh_port" value="<?php echo e(old('ssh_port', 22)); ?>" min="1" max="65535" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">اسم المستخدم</label>
                        <input type="text" name="ssh_username" value="<?php echo e(old('ssh_username')); ?>" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white" placeholder="root أو ubuntu">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1">كلمة المرور</label>
                        <input type="password" name="ssh_password" value="" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white" autocomplete="new-password">
                    </div>
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">ملاحظات</label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white" placeholder="ملاحظات إضافية حول السيرفر..."><?php echo e(old('notes')); ?></textarea>
            </div>
        </div>
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-cyan-500 hover:bg-cyan-600 text-white rounded-xl font-semibold transition-colors"><i class="fas fa-server ml-1"></i> إضافة السيرفر</button>
            <a href="<?php echo e(route('admin.live-servers.index')); ?>" class="px-6 py-2.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-medium hover:bg-slate-300 transition-colors">إلغاء</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\live-servers\create.blade.php ENDPATH**/ ?>