<?php $__env->startSection('title', 'إضافة خدمة'); ?>
<?php $__env->startSection('header', 'إضافة خدمة للموقع'); ?>
<?php $__env->startSection('content'); ?>
<div class="w-full">
    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
            <h1 class="text-xl font-bold text-slate-900">خدمة جديدة</h1>
            <p class="text-slate-500 mt-1">يُنشأ الرابط تلقائياً من الاسم إن تركت حقل الرابط فارغاً (أحرف إنجليزية وشرطة).</p>
        </div>
        <form action="<?php echo e(route('admin.site-services.store')); ?>" method="POST" enctype="multipart/form-data" class="p-5 sm:p-8 space-y-6">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">اسم الخدمة <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="<?php echo e(old('name')); ?>" required maxlength="255"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">الرابط في المتصفح (اختياري)</label>
                <input type="text" name="slug" value="<?php echo e(old('slug')); ?>" dir="ltr" placeholder="مثال: teacher-training"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 font-mono text-sm">
                <p class="mt-1 text-xs text-slate-500">فقط a-z و 0-9 و شرطة. يترك فارغاً للإنشاء التلقائي.</p>
                <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">صورة الخدمة (اختياري)</label>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif"
                       class="block w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                <p class="mt-1.5 text-xs text-slate-500">صورة للبطاقة وصفحة الخدمة — تُرفع على Cloudflare R2 عند ضبط <code class="bg-slate-100 px-1 rounded">SITE_SERVICES_DISK=r2</code> في ملف البيئة (مع AWS_*).</p>
                <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">مقدمة قصيرة (بطاقة القائمة)</label>
                <textarea name="summary" rows="3" maxlength="2000"
                          class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500"><?php echo e(old('summary')); ?></textarea>
                <?php $__errorArgs = ['summary'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">تفاصيل الخدمة <span class="text-rose-500">*</span></label>
                <textarea name="body" rows="12" required
                          class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500"><?php echo e(old('body')); ?></textarea>
                <?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">ترتيب العرض</label>
                    <input type="number" name="sort_order" value="<?php echo e(old('sort_order', 0)); ?>" min="0"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30">
                    <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="flex items-end pb-1">
                    <input type="hidden" name="is_active" value="0">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" <?php if((string) old('is_active', '1') !== '0'): echo 'checked'; endif; ?>
                               class="rounded border-slate-300 text-sky-500 focus:ring-sky-500">
                        <span class="text-sm font-semibold text-slate-700">نشط ويظهر في الموقع</span>
                    </label>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold">
                    <i class="fas fa-save"></i> حفظ
                </button>
                <a href="<?php echo e(route('admin.site-services.index')); ?>" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold">إلغاء</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\site-services\create.blade.php ENDPATH**/ ?>