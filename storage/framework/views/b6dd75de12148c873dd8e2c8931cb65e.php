<?php $__env->startSection('title', __('instructor.personal_branding') . ' - ' . config('app.name', 'Muallimx')); ?>
<?php $__env->startSection('header', __('instructor.personal_branding')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 px-4 py-3 flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-600"></i>
            <span class="font-semibold text-emerald-800"><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="rounded-xl bg-rose-50 dark:bg-rose-900/30 border border-rose-200 px-4 py-3 flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-rose-600"></i>
            <span class="font-semibold text-rose-800"><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <div class="rounded-2xl p-5 sm:p-6 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100 mb-1"><?php echo e(__('instructor.profile_branding_title')); ?></h1>
        <p class="text-sm text-slate-500 dark:text-slate-400"><?php echo e(__('instructor.personal_branding_desc')); ?></p>
        <div class="mt-3">
            <span class="rounded-full px-3 py-1 text-xs font-semibold
                <?php if($profile->status == 'approved'): ?> bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400
                <?php elseif($profile->status == 'pending_review'): ?> bg-amber-100 text-amber-700
                <?php elseif($profile->status == 'rejected'): ?> bg-rose-100 dark:bg-rose-900/40 text-rose-700 dark:text-rose-400
                <?php else: ?> bg-slate-100 dark:bg-slate-700/50 text-slate-600 dark:text-slate-400
                <?php endif; ?>">
                <?php echo e(__('instructor.status_label')); ?>: <?php echo e(\App\Models\InstructorProfile::statusLabel($profile->status)); ?>

            </span>
            <?php if($profile->rejection_reason): ?>
                <p class="text-sm text-rose-600 mt-2"><?php echo e(__('instructor.rejection_reason_label')); ?>: <?php echo e($profile->rejection_reason); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <form method="POST" action="<?php echo e(route('instructor.personal-branding.update')); ?>" enctype="multipart/form-data" class="rounded-2xl p-5 sm:p-6 bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm space-y-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <?php if($profile->status === \App\Models\InstructorProfile::STATUS_APPROVED): ?>
            <div class="rounded-xl bg-sky-50 dark:bg-sky-900/20 border border-sky-200 dark:border-sky-800 px-4 py-3 text-sm text-sky-900 dark:text-sky-100">
                ملفك معتمد ومتاح للطلاب هنا:
                <a href="<?php echo e(route('public.instructors.show', $profile->user_id)); ?>" target="_blank" class="font-bold underline">عرض الملف العام</a>
            </div>
        <?php endif; ?>

        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2"><?php echo e(__('instructor.profile_photo')); ?></label>
            <?php if($profile->photo_path): ?>
                <div class="w-24 h-24 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden bg-slate-100 dark:bg-slate-700/50 relative mb-2">
                    <img src="<?php echo e($profile->photo_url); ?>" alt="<?php echo e(__('instructor.profile_photo_alt')); ?>" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                    <div class="hidden absolute inset-0 w-full h-full bg-slate-200 flex items-center justify-center text-slate-500 dark:text-slate-400"><i class="fas fa-user text-3xl"></i></div>
                </div>
            <?php endif; ?>
            <input type="file" name="photo" accept="image/*" class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:rounded-xl file:border-0 file:bg-sky-50 dark:bg-sky-900/30 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-sky-700 hover:file:bg-sky-100">
            <?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2"><?php echo e(__('instructor.intro_title')); ?></label>
            <input type="text" name="headline" value="<?php echo e(old('headline', $profile->headline)); ?>" placeholder="<?php echo e(__('instructor.headline_placeholder')); ?>" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-sm">
            <?php $__errorArgs = ['headline'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2"><?php echo e(__('instructor.bio')); ?></label>
            <textarea name="bio" rows="5" placeholder="<?php echo e(__('instructor.bio_placeholder')); ?>" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-sm"><?php echo e(old('bio', $profile->bio)); ?></textarea>
            <?php $__errorArgs = ['bio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2"><?php echo e(__('instructor.experience')); ?></label>
            <textarea name="experience" rows="10" placeholder="<?php echo e(__('instructor.experience_placeholder')); ?>" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-sm"><?php echo e(old('experience', $profile->experience)); ?></textarea>
            <?php $__errorArgs = ['experience'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2"><?php echo e(__('instructor.skills')); ?></label>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-2"><?php echo e(__('instructor.skills_hint')); ?></p>
            <textarea name="skills" rows="5" placeholder="<?php echo e(__('instructor.skills_placeholder')); ?>" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-sm"><?php echo e(old('skills', $profile->skills)); ?></textarea>
            <?php $__errorArgs = ['skills'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <?php
                $skillsPreview = $profile->skills_list;
                if (old('skills') !== null) {
                    $split = preg_split('/[\r\n,،]+/u', old('skills'), -1, PREG_SPLIT_NO_EMPTY);
                    $skillsPreview = array_values(array_filter(array_map('trim', $split)));
                }
            ?>
            <?php if(count($skillsPreview) > 0): ?>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2"><?php echo e(__('instructor.skills_preview')); ?> (<?php echo e(count($skillsPreview)); ?> <?php echo e(__('instructor.skill_count')); ?>):</p>
            <div class="flex flex-wrap gap-2 mt-1">
                <?php $__currentLoopData = $skillsPreview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="inline-flex items-center rounded-lg bg-sky-50 dark:bg-sky-900/30 text-sky-700 px-2.5 py-1 text-xs font-medium"><?php echo e($skill); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4 sm:p-5 bg-slate-50/60 dark:bg-slate-800/40">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-1">الاستشارات (اختياري)</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">يمكنك تحديد سعر ومدة الاستشارة للطلاب. إن تركتها فارغة سيُستخدم الافتراضي من إعدادات المنصة.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">السعر (ج.م)</label>
                    <input type="number" step="0.01" min="0" max="999999.99" name="consultation_price_egp" value="<?php echo e(old('consultation_price_egp', $profile->consultation_price_egp)); ?>" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-sm" dir="ltr">
                    <?php $__errorArgs = ['consultation_price_egp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">المدة (دقيقة)</label>
                    <input type="number" min="15" max="480" name="consultation_duration_minutes" value="<?php echo e(old('consultation_duration_minutes', $profile->consultation_duration_minutes)); ?>" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-sm" dir="ltr">
                    <?php $__errorArgs = ['consultation_duration_minutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-rose-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="rounded-xl bg-sky-600 text-white px-5 py-2.5 text-sm font-semibold hover:bg-sky-700"><?php echo e(__('instructor.save_changes')); ?></button>
        </div>
    </form>

    <?php if(in_array($profile->status, ['draft', 'rejected'])): ?>
    <form method="POST" action="<?php echo e(route('instructor.personal-branding.submit')); ?>" class="inline mt-4">
        <?php echo csrf_field(); ?>
        <button type="submit" class="rounded-xl bg-amber-500 dark:bg-amber-600 text-white px-5 py-2.5 text-sm font-semibold hover:bg-amber-600"><?php echo e(__('instructor.submit_for_review')); ?></button>
    </form>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\instructor\personal-branding\edit.blade.php ENDPATH**/ ?>