
<?php $__env->startSection('title', 'إضافة تسجيل جلسة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="<?php echo e(route('admin.live-recordings.index')); ?>" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 transition-colors"><i class="fas fa-arrow-right"></i></a>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white"><i class="fas fa-plus-circle text-emerald-500 ml-2"></i>إضافة تسجيل جلسة (R2 / يدوي)</h1>
    </div>

    <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800 p-4 text-sm text-amber-800 dark:text-amber-200">
        <p class="font-semibold mb-1">تسجيل تلقائي من Jibri → R2:</p>
        <p class="mb-2">بعد رفع الفيديو إلى R2 من سكربت Jibri، استدعِ الويب هوك:</p>
        <code class="block bg-white dark:bg-slate-800 p-2 rounded text-xs break-all">curl -X POST <?php echo e(url('/api/live-recordings/register')); ?> \<br>
  -H "X-Webhook-Token: YOUR_TOKEN" \<br>
  -H "Content-Type: application/json" \<br>
  -d '{"session_id": 1, "file_path": "live-recordings/session-1/rec.mp4", "duration_seconds": 3600, "file_size": 104857600}'</code>
        <p class="mt-2 text-xs">اضبط <code>LIVE_RECORDINGS_WEBHOOK_TOKEN</code> في .env ثم استخدمه في الهيدر.</p>
    </div>

    <form method="POST" action="<?php echo e(route('admin.live-recordings.store')); ?>" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 space-y-5">
        <?php echo csrf_field(); ?>
        <div class="grid md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">الجلسة <span class="text-red-500">*</span></label>
                <select name="session_id" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <option value="">اختر الجلسة</option>
                    <?php $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s->id); ?>" <?php echo e(old('session_id') == $s->id ? 'selected' : ''); ?>><?php echo e($s->title); ?> (<?php echo e($s->scheduled_at?->format('Y-m-d H:i')); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['session_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">عنوان التسجيل</label>
                <input type="text" name="title" value="<?php echo e(old('title')); ?>" placeholder="اختياري — يُستخدم عنوان الجلسة إن تُرك فارغاً" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">مكان التخزين <span class="text-red-500">*</span></label>
                <select name="storage_disk" id="storage_disk" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <option value="r2" <?php echo e(old('storage_disk', 'r2') === 'r2' ? 'selected' : ''); ?>>Cloudflare R2 (بعد رفع Jibri)</option>
                    <option value="local" <?php echo e(old('storage_disk') === 'local' ? 'selected' : ''); ?>>محلي (storage)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">مسار الملف في R2 أو storage <span class="text-red-500">*</span></label>
                <input type="text" name="file_path" value="<?php echo e(old('file_path')); ?>" required placeholder="مثال: live-recordings/session-1/2025-03-16-rec.mp4" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                <p class="text-xs text-slate-500 mt-1">المفتاح (key) كما ظهر بعد الرفع إلى R2، أو المسار النسبي داخل storage للمحلي.</p>
                <?php $__errorArgs = ['file_path'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">رابط خارجي (بديل)</label>
                <input type="url" name="external_url" value="<?php echo e(old('external_url')); ?>" placeholder="https://..." class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                <p class="text-xs text-slate-500 mt-1">إن وُجد يُستخدم للمشاهدة بدل file_path.</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">المدة (ثانية)</label>
                <input type="number" name="duration_seconds" value="<?php echo e(old('duration_seconds', 0)); ?>" min="0" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">حجم الملف (بايت)</label>
                <input type="number" name="file_size" value="<?php echo e(old('file_size', 0)); ?>" min="0" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
            </div>
            <div class="md:col-span-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_published" value="1" <?php echo e(old('is_published') ? 'checked' : ''); ?> class="rounded text-emerald-500">
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">نشر التسجيل للطلاب فوراً</span>
                </label>
            </div>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold transition-colors"><i class="fas fa-save ml-1"></i> إضافة التسجيل</button>
            <a href="<?php echo e(route('admin.live-recordings.index')); ?>" class="px-6 py-2.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-medium hover:bg-slate-300 transition-colors">إلغاء</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\live-recordings\create.blade.php ENDPATH**/ ?>