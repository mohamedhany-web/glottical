
<?php $__env->startSection('title', 'سلايدر الصفحة الرئيسية'); ?>
<?php $__env->startSection('header', 'سلايدر الهيرو — الصفحة الرئيسية'); ?>
<?php $__env->startSection('content'); ?>
<div class="w-full space-y-6">
    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">سلايدر الصفحة الرئيسية</h1>
                <p class="text-slate-500 mt-1 text-sm max-w-2xl">تحكم كامل في شرائح الهيرو (البانر المتحرك) أعلى الصفحة الرئيسية. اختر كورساً أو مساراً أو محتوى مخصصاً، رتّب بالسحب، وحدّد مواعيد العرض.</p>
            </div>
            <a href="<?php echo e(route('admin.homepage-sliders.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white rounded-xl font-semibold shadow-lg transition-all">
                <i class="fas fa-plus"></i>
                <span>سلايدر جديد</span>
            </a>
        </div>
        <div class="p-5 sm:p-8">
            <?php if(session('success')): ?>
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800"><?php echo e(session('success')); ?></div>
            <?php endif; ?>

            <?php if($rows->isEmpty()): ?>
                <div class="text-center py-16 px-4 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50">
                    <i class="fas fa-images text-5xl text-slate-300 mb-4"></i>
                    <p class="text-slate-600 font-semibold mb-2">لا توجد سلايدرات مُعدّة بعد</p>
                    <p class="text-slate-500 text-sm mb-6">بدون سلايدرات، تُستخدم تلقائياً أول الكورسات المميزة والمسارات (السلوك القديم).</p>
                    <a href="<?php echo e(route('admin.homepage-sliders.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 text-white font-semibold hover:bg-amber-600">إضافة أول سلايدر</a>
                </div>
            <?php else: ?>
                <form method="GET" class="flex flex-wrap gap-3 mb-6">
                    <select name="source_type" class="px-4 py-2 border border-slate-200 rounded-xl text-sm">
                        <option value="">كل الأنواع</option>
                        <option value="course" <?php if(request('source_type')==='course'): echo 'selected'; endif; ?>>كورس</option>
                        <option value="path" <?php if(request('source_type')==='path'): echo 'selected'; endif; ?>>مسار</option>
                        <option value="custom" <?php if(request('source_type')==='custom'): echo 'selected'; endif; ?>>مخصص</option>
                    </select>
                    <select name="status" class="px-4 py-2 border border-slate-200 rounded-xl text-sm">
                        <option value="">كل الحالات</option>
                        <option value="active" <?php if(request('status')==='active'): echo 'selected'; endif; ?>>نشط</option>
                        <option value="inactive" <?php if(request('status')==='inactive'): echo 'selected'; endif; ?>>معطل</option>
                    </select>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-sm font-semibold">تصفية</button>
                </form>

                <p class="text-xs text-slate-500 mb-3"><i class="fas fa-grip-vertical ml-1"></i> اسحب الصفوف لتغيير ترتيب العرض في الصفحة الرئيسية</p>

                <div id="slider-sortable" class="space-y-2">
                    <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="slider-row flex flex-wrap items-center gap-3 p-4 rounded-2xl border border-slate-200 bg-white hover:border-amber-200 transition cursor-grab active:cursor-grabbing" data-id="<?php echo e($row->id); ?>">
                        <span class="text-slate-400 drag-handle px-1"><i class="fas fa-grip-vertical"></i></span>
                        <div class="h-14 w-24 shrink-0 rounded-xl overflow-hidden bg-slate-100 border border-slate-200">
                            <?php $thumb = $row->publicImageUrl() ?: ($row->course && $row->course->thumbnail ? asset('storage/'.str_replace('\\','/',$row->course->thumbnail)) : null); ?>
                            <?php if($thumb): ?>
                                <img src="<?php echo e($thumb); ?>" alt="" class="h-full w-full object-cover">
                            <?php else: ?>
                                <span class="flex h-full w-full items-center justify-center text-slate-400"><i class="fas fa-image"></i></span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-[180px]">
                            <p class="font-bold text-slate-900"><?php echo e($row->resolvedTitle() ?: '—'); ?></p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                <span class="rounded-full px-2 py-0.5 bg-slate-100 text-slate-700 font-semibold"><?php echo e($row->sourceTypeLabel()); ?></span>
                                <?php if($row->course): ?> · <?php echo e(Str::limit($row->course->title, 40)); ?> <?php endif; ?>
                                <?php if($row->academicYear): ?> · <?php echo e(Str::limit($row->academicYear->name, 40)); ?> <?php endif; ?>
                            </p>
                        </div>
                        <div class="text-sm text-slate-500">#<?php echo e($row->sort_order); ?></div>
                        <div>
                            <?php if($row->is_active): ?>
                                <span class="rounded-full px-2 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-700">نشط</span>
                            <?php else: ?>
                                <span class="rounded-full px-2 py-0.5 text-xs font-semibold bg-slate-100 text-slate-600">معطل</span>
                            <?php endif; ?>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 ms-auto">
                            <form action="<?php echo e(route('admin.homepage-sliders.toggle-active', $row)); ?>" method="POST" class="inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50"><?php echo e($row->is_active ? 'إيقاف' : 'تفعيل'); ?></button>
                            </form>
                            <a href="<?php echo e(route('admin.homepage-sliders.edit', $row)); ?>" class="text-sky-600 hover:text-sky-700 font-semibold text-sm">تعديل</a>
                            <form action="<?php echo e(route('admin.homepage-sliders.destroy', $row)); ?>" method="POST" class="inline" onsubmit="return confirm('حذف هذا السلايدر؟');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-rose-600 hover:text-rose-700 font-semibold text-sm">حذف</button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if($rows->isNotEmpty()): ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var container = document.getElementById('slider-sortable');
    if (!container || typeof Sortable === 'undefined') return;

    new Sortable(container, {
        handle: '.drag-handle',
        animation: 150,
        onEnd: function () {
            var items = [];
            container.querySelectorAll('.slider-row').forEach(function (el, index) {
                items.push({ id: parseInt(el.getAttribute('data-id'), 10), order: index });
            });
            fetch('<?php echo e(route('admin.homepage-sliders.reorder')); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ items: items })
            }).catch(function () {
                alert('تعذّر حفظ الترتيب. أعد تحميل الصفحة.');
            });
        }
    });
});
</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/admin/homepage-sliders/index.blade.php ENDPATH**/ ?>