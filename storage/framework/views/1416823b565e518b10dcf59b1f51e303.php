

<?php $__env->startSection('title', 'مراجعة ملف: ' . $user->name); ?>
<?php $__env->startSection('header', 'ملف تعريفي — ' . $user->name); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="<?php echo e(route('admin.portfolio-marketing-profiles.index', ['status' => 'pending_review'])); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-bold">
            <i class="fas fa-arrow-right"></i>
            رجوع للقائمة
        </a>
        <a href="<?php echo e(route('admin.users.show', $user)); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 text-slate-800 hover:bg-slate-200 text-sm font-bold">
            <i class="fas fa-user"></i>
            صفحة الحساب
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-2xl bg-white border-2 border-gray-200 p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-4">المحتوى المرسل (مسودة الطالب الحالية)</h2>
                <div class="space-y-4 text-sm">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase mb-1">Headline</p>
                        <p class="text-gray-900 font-semibold"><?php echo e($user->portfolio_headline ?: '—'); ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase mb-1">نبذة</p>
                        <p class="text-gray-700 whitespace-pre-line"><?php echo e($user->portfolio_about ?: '—'); ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase mb-1">مهارات</p>
                        <p class="text-gray-700 whitespace-pre-line"><?php echo e($user->portfolio_skills ?: '—'); ?></p>
                    </div>
                    <?php if($user->portfolio_intro_video_url): ?>
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase mb-1">فيديو تعريفي</p>
                            <a href="<?php echo e($user->portfolio_intro_video_url); ?>" target="_blank" rel="noopener" class="text-sky-600 font-bold hover:underline break-all"><?php echo e($user->portfolio_intro_video_url); ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if($user->portfolio_profile_status === \App\Models\User::PORTFOLIO_PROFILE_REJECTED && $user->portfolio_profile_rejected_reason): ?>
                <div class="rounded-2xl bg-red-50 border-2 border-red-200 p-5">
                    <p class="text-sm font-bold text-red-900 mb-2">سبب الرفض السابق</p>
                    <p class="text-sm text-red-800 whitespace-pre-line"><?php echo e($user->portfolio_profile_rejected_reason); ?></p>
                </div>
            <?php endif; ?>

            <?php if($user->portfolio_profile_status === \App\Models\User::PORTFOLIO_PROFILE_PENDING): ?>
                <div class="rounded-2xl bg-amber-50 border-2 border-amber-200 p-6 space-y-4">
                    <p class="font-bold text-amber-900">قرار المراجعة</p>
                    <form action="<?php echo e(route('admin.portfolio-marketing-profiles.approve', $user)); ?>" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-black">
                            <i class="fas fa-check"></i>
                            اعتماد وعرض للزوار
                        </button>
                    </form>
                    <form action="<?php echo e(route('admin.portfolio-marketing-profiles.reject', $user)); ?>" method="POST" class="mt-4 space-y-3">
                        <?php echo csrf_field(); ?>
                        <label class="block text-sm font-bold text-gray-800">ملاحظة للطالب (اختياري)</label>
                        <textarea name="portfolio_profile_rejected_reason" rows="3" class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 text-sm" placeholder="سبب الرفض أو ما يحتاج تعديلاً"><?php echo e(old('portfolio_profile_rejected_reason')); ?></textarea>
                        <?php $__errorArgs = ['portfolio_profile_rejected_reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-sm text-red-600 font-semibold"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-black">
                            <i class="fas fa-times"></i>
                            رفض
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if($user->portfolio_profile_reviewed_at): ?>
                <p class="text-xs text-gray-500">
                    آخر قرار: <?php echo e($user->portfolio_profile_reviewed_at->format('Y-m-d H:i')); ?>

                    <?php if($user->portfolioProfileReviewedBy): ?>
                        — <?php echo e($user->portfolioProfileReviewedBy->name); ?>

                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl bg-white border-2 border-gray-200 p-5 shadow-sm text-center">
                <p class="text-xs font-bold text-gray-500 uppercase mb-3">صورة الملف</p>
                <?php if($user->profile_image && $user->profile_image_url): ?>
                    <img src="<?php echo e($user->profile_image_url); ?>" alt="" class="w-40 h-40 rounded-2xl object-cover border-2 border-gray-200 mx-auto">
                <?php else: ?>
                    <div class="w-40 h-40 rounded-2xl bg-gray-100 border-2 border-dashed border-gray-300 mx-auto flex items-center justify-center text-gray-400">
                        <i class="fas fa-user text-4xl"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4 text-sm text-slate-700">
                <p class="font-bold mb-2">آخر نسخة معتمدة للزوار</p>
                <p class="text-xs text-slate-600">تُحدَّث عند الاعتماد فقط. أثناء «قيد المراجعة» يبقى الموقع العام على هذه النسخة حتى توافق على الجديد.</p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\portfolio-marketing\show.blade.php ENDPATH**/ ?>