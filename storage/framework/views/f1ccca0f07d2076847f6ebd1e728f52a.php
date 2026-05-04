
<?php $__env->startSection('title', 'تعديل رأي'); ?>
<?php $__env->startSection('header', 'تعديل رأي'); ?>
<?php $__env->startSection('content'); ?>
<?php
    $t = $siteTestimonial;
    $oldType = old('content_type', $t->content_type);
?>
<div class="w-full" x-data="{ type: '<?php echo e($oldType); ?>' }">
    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
            <h1 class="text-xl font-bold text-slate-900">تعديل رأي</h1>
        </div>
        <form action="<?php echo e(route('admin.site-testimonials.update', $t)); ?>" method="POST" enctype="multipart/form-data" class="p-5 sm:p-8 space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div>
                <span class="block text-sm font-semibold text-slate-700 mb-2">نوع العرض <span class="text-rose-500">*</span></span>
                <div class="flex flex-wrap gap-4">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="content_type" value="text" x-model="type" class="text-sky-600 focus:ring-sky-500">
                        <span>نص</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="content_type" value="image" x-model="type" class="text-sky-600 focus:ring-sky-500">
                        <span>صورة</span>
                    </label>
                </div>
                <?php $__errorArgs = ['content_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <template x-if="type === 'text'">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">نص الرأي <span class="text-rose-500">*</span></label>
                    <textarea name="body" rows="6" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/30"><?php echo e(old('body', $t->body)); ?></textarea>
                    <?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </template>

            <template x-if="type === 'image'">
                <div class="space-y-4">
                    <?php if($t->publicImageUrl()): ?>
                        <div class="rounded-xl border border-slate-200 overflow-hidden max-w-md">
                            <img src="<?php echo e($t->publicImageUrl()); ?>" alt="" class="w-full h-auto max-h-56 object-contain bg-slate-50">
                        </div>
                    <?php endif; ?>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">استبدال الصورة (اختياري)</label>
                        <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif"
                               class="block w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-violet-50 file:text-violet-700">
                        <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <?php if($t->image_path): ?>
                            <input type="hidden" name="remove_image" value="0">
                            <label class="mt-3 inline-flex items-center gap-2 cursor-pointer text-sm text-rose-700">
                                <input type="checkbox" name="remove_image" value="1" class="rounded border-slate-300 text-rose-600">
                                <span>حذف الصورة الحالية (يلزمك رفع صورة جديدة)</span>
                            </label>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">وصف تحت الصورة (اختياري)</label>
                        <textarea name="body" rows="2" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl"><?php echo e(old('body', $t->body)); ?></textarea>
                    </div>
                </div>
            </template>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">اسم صاحب الرأي</label>
                    <input type="text" name="author_name" value="<?php echo e(old('author_name', $t->author_name)); ?>" maxlength="190" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">المسمى (اختياري)</label>
                    <input type="text" name="role_label" value="<?php echo e(old('role_label', $t->role_label)); ?>" maxlength="190" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl">
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">ترتيب العرض</label>
                    <input type="number" name="sort_order" value="<?php echo e(old('sort_order', $t->sort_order)); ?>" min="0" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl">
                </div>
                <div class="flex flex-col gap-3 justify-end pb-1">
                    <input type="hidden" name="is_active" value="0">
                    <input type="hidden" name="is_featured" value="0">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $t->is_active ? '1' : '0') === '1'): echo 'checked'; endif; ?> class="rounded border-slate-300 text-sky-600">
                        <span class="text-sm font-semibold text-slate-700">نشط</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" <?php if(old('is_featured', $t->is_featured ? '1' : '0') === '1'): echo 'checked'; endif; ?> class="rounded border-slate-300 text-amber-600">
                        <span class="text-sm font-semibold text-slate-700">بطاقة مميزة</span>
                    </label>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold">
                    <i class="fas fa-save"></i> حفظ
                </button>
                <a href="<?php echo e(route('admin.site-testimonials.index')); ?>" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold">رجوع</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\site-testimonials\edit.blade.php ENDPATH**/ ?>