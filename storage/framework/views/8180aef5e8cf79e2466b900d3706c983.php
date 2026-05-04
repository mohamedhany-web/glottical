<?php $__env->startSection('title', 'خدمات الموقع'); ?>
<?php $__env->startSection('header', 'خدمات الموقع (الصفحة العامة)'); ?>
<?php $__env->startSection('content'); ?>
<div class="w-full space-y-6">
    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">خدمات الموقع</h1>
                <p class="text-slate-500 mt-1">تظهر في الصفحة العامة <code class="text-xs bg-slate-100 px-1 rounded">/services</code> وفي شريط التنقل. أضف اسم الخدمة، ومقدمة قصيرة، وتفاصيل كاملة.</p>
            </div>
            <a href="<?php echo e(route('admin.site-services.create')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white rounded-xl font-semibold shadow-lg shadow-sky-500/30 transition-all">
                <i class="fas fa-plus"></i>
                <span>خدمة جديدة</span>
            </a>
        </div>
        <div class="p-5 sm:p-8">
            <?php if(session('success')): ?>
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800"><?php echo e(session('success')); ?></div>
            <?php endif; ?>

            <form method="GET" action="<?php echo e(route('admin.site-services.index')); ?>" class="flex flex-wrap gap-3 mb-6">
                <input type="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="بحث بالاسم أو الرابط..."
                       class="flex-1 min-w-[200px] px-4 py-2 border border-slate-200 rounded-xl text-sm">
                <select name="status" class="px-4 py-2 border border-slate-200 rounded-xl text-sm">
                    <option value="">كل الحالات</option>
                    <option value="active" <?php if(request('status')==='active'): echo 'selected'; endif; ?>>نشط</option>
                    <option value="inactive" <?php if(request('status')==='inactive'): echo 'selected'; endif; ?>>معطل</option>
                </select>
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-sm font-semibold">تصفية</button>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-xs font-semibold uppercase text-slate-500">
                            <th class="px-4 py-3">الترتيب</th>
                            <th class="px-4 py-3 w-20">صورة</th>
                            <th class="px-4 py-3">الاسم</th>
                            <th class="px-4 py-3">الرابط</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php $__empty_1 = true; $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-600"><?php echo e($service->sort_order); ?></td>
                            <td class="px-4 py-3">
                                <?php if($service->publicImageUrl()): ?>
                                    <img src="<?php echo e($service->publicImageUrl()); ?>" alt="" class="w-14 h-10 object-cover rounded-lg border border-slate-200">
                                <?php else: ?>
                                    <span class="text-slate-300 text-xs">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 font-medium text-slate-800"><?php echo e($service->name); ?></td>
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('public.services.show', $service)); ?>" target="_blank" rel="noopener" class="text-sky-600 hover:underline text-xs font-mono">/services/<?php echo e($service->slug); ?></a>
                            </td>
                            <td class="px-4 py-3">
                                <?php if($service->is_active): ?>
                                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-700">نشط</span>
                                <?php else: ?>
                                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold bg-slate-100 text-slate-600">معطل</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <a href="<?php echo e(route('admin.site-services.edit', $service)); ?>" class="text-sky-600 hover:text-sky-700 font-medium ml-2">تعديل</a>
                                <form action="<?php echo e(route('admin.site-services.destroy', $service)); ?>" method="POST" class="inline" onsubmit="return confirm('حذف هذه الخدمة؟');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-rose-600 hover:text-rose-700 font-medium">حذف</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-500">
                                <i class="fas fa-concierge-bell text-4xl text-slate-300 mb-3 block"></i>
                                <p>لا توجد خدمات. <a href="<?php echo e(route('admin.site-services.create')); ?>" class="text-sky-600 hover:underline">أضف خدمة</a></p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4"><?php echo e($services->links()); ?></div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\site-services\index.blade.php ENDPATH**/ ?>