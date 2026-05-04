<?php $__env->startSection('title', 'الشهادات'); ?>
<?php $__env->startSection('header', 'الشهادات'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">الشهادات</h1>
                <p class="text-gray-600 mt-1">إدارة شهادات الطلاب</p>
            </div>
            <div class="flex gap-3">
                <a href="<?php echo e(route('admin.certificates.create')); ?>"
                   class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30 inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span>إصدار شهادة جديدة</span>
                </a>
            </div>
        </div>
    </div>

    <?php if(isset($stats)): ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-blue-700 font-medium mb-1">إجمالي الشهادات</div>
                        <div class="text-3xl font-black text-blue-900"><?php echo e($stats['total'] ?? 0); ?></div>
                    </div>
                    <div class="w-16 h-16 bg-blue-200 rounded-xl flex items-center justify-center">
                        <i class="fas fa-certificate text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-green-700 font-medium mb-1">المُصدرة</div>
                        <div class="text-3xl font-black text-green-900"><?php echo e($stats['issued'] ?? 0); ?></div>
                    </div>
                    <div class="w-16 h-16 bg-green-200 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-6 border border-yellow-200">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-yellow-700 font-medium mb-1">المعلقة</div>
                        <div class="text-3xl font-black text-yellow-900"><?php echo e($stats['pending'] ?? 0); ?></div>
                    </div>
                    <div class="w-16 h-16 bg-yellow-200 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(isset($certificates) && $certificates->count() > 0): ?>
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">رقم الشهادة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الطالب</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">العنوان</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الكورس</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">تاريخ الإصدار</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">ملف PDF</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php $__currentLoopData = $certificates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $certificate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 font-mono"><?php echo e($certificate->certificate_number); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo e($certificate->user->name ?? 'غير معروف'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo e($certificate->title ?? $certificate->course_name ?? '-'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo e($certificate->course->title ?? ($certificate->course_name ?? '-')); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                        $status = $certificate->status ?? ($certificate->is_verified ? 'issued' : 'pending');
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        <?php if($status == 'issued'): ?> bg-green-100 text-green-800
                                        <?php elseif($status == 'pending'): ?> bg-yellow-100 text-yellow-800
                                        <?php elseif($status == 'revoked'): ?> bg-red-100 text-red-800
                                        <?php else: ?> bg-gray-100 text-gray-800
                                        <?php endif; ?>">
                                        <?php if($status == 'issued'): ?> مُصدرة
                                        <?php elseif($status == 'pending'): ?> معلقة
                                        <?php elseif($status == 'revoked'): ?> ملغاة
                                        <?php else: ?> <?php echo e($status); ?>

                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo e($certificate->issued_at ? $certificate->issued_at->format('Y-m-d') : ($certificate->issue_date ? $certificate->issue_date->format('Y-m-d') : '-')); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php if(!empty($certificate->pdf_path)): ?>
                                        <span class="inline-flex items-center gap-1 text-emerald-700 font-medium">
                                            <i class="fas fa-check-circle"></i>
                                            موجود
                                        </span>
                                    <?php else: ?>
                                        <span class="text-amber-600 text-sm">غير مرفوع</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                                        <a href="<?php echo e(route('admin.certificates.show', $certificate)); ?>"
                                           class="inline-flex items-center gap-1 text-sky-600 hover:text-sky-900 transition-colors">
                                            <i class="fas fa-eye"></i>
                                            عرض
                                        </a>
                                        <?php if(!empty($certificate->pdf_path)): ?>
                                            <a href="<?php echo e(route('admin.certificates.download', $certificate)); ?>"
                                               class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-800 transition-colors">
                                                <i class="fas fa-download"></i>
                                                تحميل
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?php echo e(route('admin.certificates.edit', $certificate)); ?>"
                                           class="inline-flex items-center gap-1 text-slate-600 hover:text-slate-900 transition-colors">
                                            <i class="fas fa-edit"></i>
                                            تعديل
                                        </a>
                                        <form action="<?php echo e(route('admin.certificates.destroy', $certificate)); ?>" method="POST" class="inline"
                                              onsubmit="return confirm('حذف هذه الشهادة وملفها نهائياً؟');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="inline-flex items-center gap-1 text-rose-600 hover:text-rose-800 transition-colors">
                                                <i class="fas fa-trash-alt"></i>
                                                حذف
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                <?php echo e($certificates->links()); ?>

            </div>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-xl shadow-lg p-12 text-center border border-gray-200">
            <div class="w-24 h-24 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-certificate text-gray-400 text-5xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد شهادات</h3>
            <p class="text-gray-600 mb-6">لم يتم إصدار أي شهادات حتى الآن</p>
            <a href="<?php echo e(route('admin.certificates.create')); ?>"
               class="inline-flex items-center gap-2 bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg shadow-sky-500/30">
                <i class="fas fa-plus"></i>
                <span>إصدار شهادة جديدة</span>
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\certificates\index.blade.php ENDPATH**/ ?>