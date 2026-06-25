<?php $__env->startSection('title', $item ? 'تعديل عنصر المنهج' : 'إضافة عنصر منهج'); ?>
<?php $__env->startSection('header', $item ? 'تعديل عنصر المنهج' : 'إضافة عنصر منهج'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-none space-y-4">
    <?php if($item): ?>
        <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-indigo-900 font-semibold">الأقسام والمواد والتحكم بالعرض/التحميل تُدار من صفحة هيكل المنهج.</p>
            <a href="<?php echo e(route('admin.curriculum-library.items.structure', $item)); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700">
                <i class="fas fa-sitemap"></i> هيكل المنهج
            </a>
        </div>
    <?php endif; ?>
    <?php if($item && $item->files && $item->files->isNotEmpty()): ?>
        <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6">
            <h3 class="text-sm font-bold text-slate-800 mb-3">ملفات قديمة (قبل الهيكل الهرمي)</h3>
            <ul class="space-y-2">
                <?php $__currentLoopData = $item->files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="flex items-center justify-between py-2 px-3 rounded-lg bg-slate-50 border border-slate-100">
                        <span class="text-sm text-slate-700">
                            <?php echo e($f->label ?: ($f->file_type === 'presentation' ? 'عرض شرائح' : ($f->file_type === 'pdf' ? 'PDF' : ($f->file_type === 'assignment' ? 'وجبة' : 'HTML')))); ?>

                        </span>
                        <form action="<?php echo e(route('admin.curriculum-library.items.files.destroy', [$item, $f])); ?>" method="POST" class="inline" onsubmit="return confirm('حذف هذا الملف؟');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-semibold">حذف</button>
                        </form>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6">
        <form action="<?php echo e($item ? route('admin.curriculum-library.items.update', $item) : route('admin.curriculum-library.items.store')); ?>" method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>
            

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">العنوان</label>
                    <input type="text" name="title" value="<?php echo e(old('title', $item?->title)); ?>" required
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500">
                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">التصنيف</label>
                    <select name="category_id" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500">
                        <option value="">— بدون تصنيف —</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat->id); ?>" <?php echo e(old('category_id', $item?->category_id) == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">الرابط (slug) — اختياري</label>
                    <input type="text" name="slug" value="<?php echo e(old('slug', $item?->slug)); ?>" class="w-full px-3 py-2 rounded-lg border border-slate-200">
                    <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">المادة / التخصص</label>
                    <input type="text" name="subject" value="<?php echo e(old('subject', $item?->subject)); ?>" placeholder="مثال: رياضيات، لغة عربية"
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">المستوى / المرحلة داخل القسم</label>
                    <input type="text" name="grade_level" value="<?php echo e(old('grade_level', $item?->grade_level)); ?>" placeholder="مثال: المستوى المبتدئ، المستوى الأول، الثاني، الثالث…"
                           class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-slate-500 mt-1">نص حر تضبطه أنت لكل قسم (عربي، إسلاميات، قرآن…).</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">اللغة (مناهج أكس)</label>
                    <select name="language" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500">
                        <option value="ar" <?php echo e(old('language', $item?->language ?? 'ar') === 'ar' ? 'selected' : ''); ?>>العربية</option>
                        <option value="en" <?php echo e(old('language', $item?->language ?? 'ar') === 'en' ? 'selected' : ''); ?>>English</option>
                        <option value="fr" <?php echo e(old('language', $item?->language ?? 'ar') === 'fr' ? 'selected' : ''); ?>>Français</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">نوع المحتوى</label>
                    <select name="item_type" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500">
                        <option value="presentation" <?php echo e(old('item_type', $item?->item_type ?? 'presentation') === 'presentation' ? 'selected' : ''); ?>>بوربوينت تفاعلي</option>
                        <option value="assignment" <?php echo e(old('item_type', $item?->item_type ?? 'presentation') === 'assignment' ? 'selected' : ''); ?>>وجبة (تحميل/إرسال للطالب)</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" <?php echo e(old('is_active', $item?->is_active ?? true) ? 'checked' : ''); ?> class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_active" class="text-sm font-semibold text-slate-700">نشط (يظهر للمعلمين)</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_free_preview" id="is_free_preview" value="1" <?php echo e(old('is_free_preview', $item?->is_free_preview ?? false) ? 'checked' : ''); ?> class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_free_preview" class="text-sm font-semibold text-slate-700">معاينة مجانية (يُعرض كعينة للتجربة)</label>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">ترتيب العرض</label>
                    <input type="number" name="order" value="<?php echo e(old('order', $item?->order ?? 0)); ?>" min="0" class="w-24 px-3 py-2 rounded-lg border border-slate-200">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">وصف مختصر</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500" placeholder="ملخص يظهر في قائمة المكتبة"><?php echo e(old('description', $item?->description)); ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">المحتوى (تفصيلي — يدعم HTML)</label>
                <textarea name="content" rows="10" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 font-mono text-sm" placeholder="المحتوى التفاعلي أو التعليمي للدرس/الوحدة..."><?php echo e(old('content', $item?->content)); ?></textarea>
                <p class="text-xs text-slate-500 mt-1">يمكنك استخدام HTML لعناوين، قوائم، روابط، أو تضمين أهداف الدرس وأنشطة مقترحة.</p>
            </div>

            <div class="flex gap-2 pt-4">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700"><?php echo e($item ? 'حفظ التعديلات' : 'إضافة العنصر'); ?></button>
                <a href="<?php echo e(route('admin.curriculum-library.index')); ?>" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50">إلغاء</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\curriculum-library\items-form.blade.php ENDPATH**/ ?>