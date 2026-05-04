<?php $__env->startSection('title', 'تفاصيل الشهادة'); ?>
<?php $__env->startSection('header', 'تفاصيل الشهادة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">شهادة <?php echo e($certificate->certificate_number); ?></h1>
                <p class="text-gray-600 mt-1 text-sm">التسليم عبر ملف PDF مرفوع من لوحة الإدارة.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <?php if(!empty($certificate->pdf_path)): ?>
                    <a href="<?php echo e(route('admin.certificates.file', $certificate)); ?>" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-external-link-alt"></i>
                        فتح PDF
                    </a>
                    <a href="<?php echo e(route('admin.certificates.download', $certificate)); ?>"
                       class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-download"></i>
                        تحميل PDF
                    </a>
                <?php endif; ?>
                <a href="<?php echo e(route('admin.certificates.edit', $certificate)); ?>" class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-edit"></i>
                    تعديل
                </a>
                <a href="<?php echo e(route('admin.certificates.index')); ?>" class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    القائمة
                </a>
                <form action="<?php echo e(route('admin.certificates.destroy', $certificate)); ?>" method="POST" class="inline"
                      onsubmit="return confirm('حذف الشهادة وملف PDF نهائياً؟');">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-trash-alt"></i>
                        حذف
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-gray-50 rounded-xl p-4">
                <h3 class="text-lg font-bold text-gray-900 mb-3">الطالب</h3>
                <div class="space-y-2 text-sm">
                    <div><span class="text-gray-600">الاسم:</span> <span class="font-medium text-gray-900 mr-2"><?php echo e($certificate->user->name ?? '—'); ?></span></div>
                    <div><span class="text-gray-600">البريد:</span> <span class="font-medium text-gray-900 mr-2"><?php echo e($certificate->user->email ?? '—'); ?></span></div>
                    <div><span class="text-gray-600">الهاتف:</span> <span class="font-medium text-gray-900 mr-2"><?php echo e($certificate->user->phone ?? '—'); ?></span></div>
                </div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <h3 class="text-lg font-bold text-gray-900 mb-3">بيانات الشهادة</h3>
                <div class="space-y-2 text-sm">
                    <div><span class="text-gray-600">العنوان:</span> <span class="font-medium text-gray-900 mr-2"><?php echo e($certificate->title ?? $certificate->course_name ?? '—'); ?></span></div>
                    <?php if($certificate->course): ?>
                        <div><span class="text-gray-600">الكورس:</span> <span class="font-medium text-gray-900 mr-2"><?php echo e($certificate->course->title); ?></span></div>
                    <?php elseif($certificate->course_name): ?>
                        <div><span class="text-gray-600">الكورس:</span> <span class="font-medium text-gray-900 mr-2"><?php echo e($certificate->course_name); ?></span></div>
                    <?php endif; ?>
                    <?php
                        $status = $certificate->status ?? ($certificate->is_verified ? 'issued' : 'pending');
                    ?>
                    <div><span class="text-gray-600">الحالة:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mr-2
                            <?php if($status == 'issued'): ?> bg-green-100 text-green-800
                            <?php elseif($status == 'pending'): ?> bg-yellow-100 text-yellow-800
                            <?php else: ?> bg-red-100 text-red-800
                            <?php endif; ?>">
                            <?php echo e($status == 'issued' ? 'مُصدرة' : ($status == 'pending' ? 'معلقة' : 'ملغاة')); ?>

                        </span>
                    </div>
                    <div><span class="text-gray-600">تاريخ الإصدار:</span> <span class="font-medium text-gray-900 mr-2"><?php echo e(($certificate->issued_at ? $certificate->issued_at->format('Y-m-d') : ($certificate->issue_date ? $certificate->issue_date->format('Y-m-d') : '—'))); ?></span></div>
                    <div><span class="text-gray-600">رمز التحقق:</span> <span class="font-mono text-gray-900 mr-2"><?php echo e($certificate->verification_code ?? '—'); ?></span></div>
                </div>
            </div>
        </div>

        <?php if($certificate->description): ?>
            <div class="border-t border-gray-200 pt-4 mb-6">
                <h3 class="text-sm font-bold text-gray-900 mb-2">الوصف</h3>
                <p class="text-gray-600 text-sm"><?php echo e($certificate->description); ?></p>
            </div>
        <?php endif; ?>

        <?php if(!empty($certificate->pdf_path)): ?>
            <div class="border border-gray-200 rounded-xl overflow-hidden bg-slate-100" style="min-height: 70vh;">
                <iframe title="معاينة الشهادة PDF"
                        src="<?php echo e(route('admin.certificates.file', $certificate)); ?>"
                        class="w-full border-0"
                        style="min-height: 70vh; height: 75vh;"></iframe>
            </div>
        <?php else: ?>
            <div class="rounded-xl border-2 border-dashed border-amber-300 bg-amber-50 p-8 text-center">
                <i class="fas fa-file-pdf text-amber-500 text-4xl mb-3"></i>
                <p class="text-amber-900 font-semibold">لا يوجد ملف PDF مرفوع.</p>
                <p class="text-sm text-amber-800 mt-2">استخدم «تعديل» لرفع ملف PDF للشهادة.</p>
                <a href="<?php echo e(route('admin.certificates.edit', $certificate)); ?>" class="inline-flex items-center gap-2 mt-4 text-sky-600 font-semibold hover:underline">
                    <i class="fas fa-upload"></i> رفع PDF
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\certificates\show.blade.php ENDPATH**/ ?>