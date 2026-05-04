

<?php $__env->startSection('title', 'تصنيفات مكتبة المناهج'); ?>
<?php $__env->startSection('header', 'تصنيفات مكتبة المناهج'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900">تصنيفات المكتبة</h1>
                <p class="text-sm text-slate-600 mt-1">تنظيم عناصر المنهج تحت تصنيفات (رياضيات، علوم، لغة عربية، إلخ).</p>
            </div>
            <a href="<?php echo e(route('admin.curriculum-library.categories.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">
                <i class="fas fa-plus"></i> إضافة تصنيف
            </a>
        </div>
        <div class="divide-y divide-slate-100">
            <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="px-6 py-4 flex items-center justify-between hover:bg-slate-50/50">
                    <div>
                        <h3 class="font-bold text-slate-900"><?php echo e($cat->name); ?></h3>
                        <?php if($cat->description): ?>
                            <p class="text-sm text-slate-500 mt-0.5"><?php echo e(Str::limit($cat->description, 80)); ?></p>
                        <?php endif; ?>
                        <p class="text-xs text-slate-400 mt-1"><?php echo e($cat->items_count); ?> عنصر · ترتيب <?php echo e($cat->order); ?></p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium <?php echo e($cat->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'); ?>"><?php echo e($cat->is_active ? 'نشط' : 'معطل'); ?></span>
                        <?php if(!empty($cat->is_restricted)): ?>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800" title="يظهر فقط للمستخدمين المحددين">
                                <i class="fas fa-user-lock text-[10px]"></i> خاص (<?php echo e($cat->restricted_users_count); ?>)
                            </span>
                        <?php endif; ?>
                        <a href="<?php echo e(route('admin.curriculum-library.categories.edit', $cat)); ?>" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">تعديل</a>
                        <form action="<?php echo e(route('admin.curriculum-library.categories.destroy', $cat)); ?>" method="POST" class="inline" onsubmit="return confirm('حذف التصنيف؟ العناصر لن تُحذف لكن ستُفقد التصنيف.');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="text-rose-600 hover:text-rose-800 text-sm font-semibold">حذف</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-6 py-12 text-center text-slate-500">لا توجد تصنيفات. <a href="<?php echo e(route('admin.curriculum-library.categories.create')); ?>" class="text-indigo-600 font-semibold">أضف تصنيفاً</a></div>
            <?php endif; ?>
        </div>
    </div>
    <a href="<?php echo e(route('admin.curriculum-library.index')); ?>" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 text-sm font-semibold"><i class="fas fa-arrow-right"></i> العودة لعناصر المنهج</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\curriculum-library\categories.blade.php ENDPATH**/ ?>