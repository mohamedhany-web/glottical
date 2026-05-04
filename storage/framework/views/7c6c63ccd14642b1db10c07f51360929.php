

<?php $__env->startSection('title', 'مكتبة المناهج التفاعلية'); ?>
<?php $__env->startSection('header', 'مكتبة المناهج التفاعلية'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-l from-indigo-50 via-white to-white p-6 border-b border-slate-100">
            <div class="flex flex-wrap items-center gap-3">
                <span class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                    <i class="fas fa-book-open text-lg"></i>
                </span>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-800">مكتبة المناهج التفاعلية (مناهج X)</h1>
                    <p class="text-sm text-slate-600 mt-0.5">
                        مناهج جاهزة للاستخدام في التحضير والتدريس مع الطلاب.
                        <?php if(empty($hasFullAccess) || !$hasFullAccess): ?>
                            <br><span class="text-xs text-amber-600 font-semibold">يمكنك فتح ملف واحد مجاناً (أي عنصر) للتجربة؛ بعدها تحتاج الاشتراك لفتح باقي المناهج.</span>
                        <?php else: ?>
                            <br><span class="text-xs text-emerald-600 font-semibold">لديك وصول كامل لجميع مناهج X ضمن اشتراكك.</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>

        <form method="GET" class="p-4 border-b border-slate-100 flex flex-wrap gap-3 items-center">
            <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="بحث في المناهج..."
                   class="px-3 py-2 rounded-xl border border-slate-200 text-sm w-64 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            <select name="category_id" class="px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">كل التصنيفات</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category_id') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <select name="language" class="px-3 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">كل اللغات</option>
                <option value="ar" <?php echo e(request('language') === 'ar' ? 'selected' : ''); ?>>العربية</option>
                <option value="en" <?php echo e(request('language') === 'en' ? 'selected' : ''); ?>>English</option>
                <option value="fr" <?php echo e(request('language') === 'fr' ? 'selected' : ''); ?>>Français</option>
            </select>
            <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700"><i class="fas fa-search ml-1"></i> بحث</button>
        </form>

        <div class="p-6">
            <?php if($items->isEmpty()): ?>
                <div class="text-center py-16">
                    <div class="w-20 h-20 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-book-open text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-2">لا توجد عناصر حالياً</h3>
                    <p class="text-slate-600 max-w-md mx-auto">سيتم إثراء المكتبة قريباً بمناهج تفاعلية جاهزة. عد لاحقاً أو راجع التصنيفات الأخرى.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            // قبل الاشتراك: يسمح بفتح عنصر واحد فقط؛ بعد استخدام المعاينة المجانية يُقفل الباقي
                            $isLocked = (empty($hasFullAccess) || !$hasFullAccess) && !empty($usedFreePreview);
                        ?>
                        <a href="<?php echo e($isLocked ? route('student.features.show', ['feature' => 'library_access']) : route('curriculum-library.show', $item)); ?>"
                           class="block rounded-xl border border-slate-200 bg-white p-5 hover:border-indigo-200 hover:shadow-lg transition-all text-right group <?php echo e($isLocked ? 'opacity-90' : ''); ?>">
                            <div class="flex items-start gap-3">
                                <span class="w-10 h-10 rounded-lg <?php echo e($item->is_free_preview ? 'bg-emerald-50 text-emerald-600' : 'bg-indigo-100 text-indigo-600'); ?> flex items-center justify-center flex-shrink-0 group-hover:bg-indigo-200 transition-colors">
                                    <i class="fas <?php echo e($isLocked ? 'fa-lock' : 'fa-file-alt'); ?>"></i>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-slate-900 group-hover:text-indigo-600 truncate"><?php echo e($item->title); ?></h3>
                                    <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                        <?php if($item->category): ?>
                                            <span class="inline-block text-xs font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded"><?php echo e($item->category->name); ?></span>
                                        <?php endif; ?>
                                        <?php if(isset($item->language) && $item->language): ?>
                                            <?php
                                                $langLabels = ['ar' => 'عربي', 'en' => 'EN', 'fr' => 'FR'];
                                            ?>
                                            <span class="inline-block text-xs font-medium text-slate-600 bg-slate-100 px-2 py-0.5 rounded"><?php echo e($langLabels[$item->language] ?? $item->language); ?></span>
                                        <?php endif; ?>
                                        <?php if($item->is_free_preview): ?>
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                معاينة مجانية
                                            </span>
                                        <?php elseif($isLocked): ?>
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">
                                                <i class="fas fa-lock"></i> اشترك لفتح المناهج
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if($item->subject || $item->grade_level): ?>
                                        <p class="text-xs text-slate-500 mt-1"><?php echo e($item->subject); ?><?php echo e($item->grade_level ? ' · ' . $item->grade_level : ''); ?></p>
                                    <?php endif; ?>
                                    <?php if($item->description): ?>
                                        <p class="text-sm text-slate-600 mt-2 line-clamp-2"><?php echo e(Str::limit($item->description, 100)); ?></p>
                                    <?php endif; ?>
                                </div>
                                <i class="fas fa-chevron-left text-slate-300 group-hover:text-indigo-500 flex-shrink-0"></i>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php if($items->hasPages()): ?>
                    <div class="mt-6"><?php echo e($items->withQueryString()->links()); ?></div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\student\curriculum-library\index.blade.php ENDPATH**/ ?>