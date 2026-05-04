
<?php $__env->startSection('title', 'تعديل جلسة: ' . $liveSession->title); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="<?php echo e(route('admin.live-sessions.show', $liveSession)); ?>" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 transition-colors"><i class="fas fa-arrow-right"></i></a>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white"><i class="fas fa-edit text-amber-500 ml-2"></i>تعديل جلسة البث</h1>
    </div>

    <form method="POST" action="<?php echo e(route('admin.live-sessions.update', $liveSession)); ?>" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 space-y-5">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <div class="grid md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">عنوان الجلسة <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="<?php echo e(old('title', $liveSession->title)); ?>" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">المعلم (المشترك) <span class="text-red-500">*</span></label>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">المعلم = المشترك عندنا (طالب يشترون منا الخدمة). للإدارة الكاملة يمكنك تغيير المعلم أو مراجعة بياناته من صفحة المستخدم.</p>
                <select name="instructor_id" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($inst->id); ?>" <?php echo e(old('instructor_id', $liveSession->instructor_id) == $inst->id ? 'selected' : ''); ?>><?php echo e($inst->name); ?><?php echo e($inst->role === 'student' ? ' (مشترك)' : ''); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">الكورس</label>
                <select name="course_id" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <option value="">جلسة عامة</option>
                    <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($course->id); ?>" <?php echo e(old('course_id', $liveSession->course_id) == $course->id ? 'selected' : ''); ?>><?php echo e(Str::limit($course->title, 50)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">موعد البث <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="scheduled_at" value="<?php echo e(old('scheduled_at', $liveSession->scheduled_at?->format('Y-m-d\TH:i'))); ?>" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">سيرفر البث</label>
                <select name="server_id" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <option value="">الافتراضي</option>
                    <?php $__currentLoopData = $servers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $server): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($server->id); ?>" <?php echo e(old('server_id', $liveSession->server_id) == $server->id ? 'selected' : ''); ?>><?php echo e($server->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">الحد الأقصى</label>
                <input type="number" name="max_participants" value="<?php echo e(old('max_participants', $liveSession->max_participants)); ?>" min="2" max="1000" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">كلمة مرور</label>
                <input type="text" name="password" value="<?php echo e(old('password', $liveSession->password)); ?>" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">وصف الجلسة</label>
                <textarea name="description" rows="3" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white"><?php echo e(old('description', $liveSession->description)); ?></textarea>
            </div>
        </div>

        <div class="border-t border-slate-200 dark:border-slate-700 pt-5">
            <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-3">إعدادات الغرفة</h3>
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-3">
                <?php $checks = [
                    ['name' => 'is_recorded', 'label' => 'تسجيل الجلسة', 'color' => 'red'],
                    ['name' => 'allow_chat', 'label' => 'السماح بالشات', 'color' => 'blue'],
                    ['name' => 'allow_screen_share', 'label' => 'مشاركة الشاشة', 'color' => 'emerald'],
                    ['name' => 'require_enrollment', 'label' => 'يتطلب تسجيل في الكورس', 'color' => 'amber'],
                    ['name' => 'mute_on_join', 'label' => 'كتم الصوت عند الدخول', 'color' => 'violet'],
                    ['name' => 'video_off_on_join', 'label' => 'إيقاف الفيديو عند الدخول', 'color' => 'pink'],
                ]; ?>
                <?php $__currentLoopData = $checks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="flex items-center gap-2 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg cursor-pointer">
                    <input type="checkbox" name="<?php echo e($chk['name']); ?>" value="1" <?php echo e(old($chk['name'], $liveSession->{$chk['name']}) ? 'checked' : ''); ?> class="rounded text-<?php echo e($chk['color']); ?>-500">
                    <span class="text-sm text-slate-700 dark:text-slate-300"><?php echo e($chk['label']); ?></span>
                </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition-colors"><i class="fas fa-save ml-1"></i> حفظ التعديلات</button>
            <a href="<?php echo e(route('admin.live-sessions.show', $liveSession)); ?>" class="px-6 py-2.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-medium hover:bg-slate-300 transition-colors">إلغاء</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\live-sessions\edit.blade.php ENDPATH**/ ?>