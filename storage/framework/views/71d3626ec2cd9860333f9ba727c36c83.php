
<?php $__env->startSection('title', 'إنشاء جلسة بث مباشر'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="<?php echo e(route('admin.live-sessions.index')); ?>" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 transition-colors"><i class="fas fa-arrow-right"></i></a>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white"><i class="fas fa-plus-circle text-red-500 ml-2"></i>إنشاء جلسة بث مباشر</h1>
    </div>

    <form method="POST" action="<?php echo e(route('admin.live-sessions.store')); ?>" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6 space-y-5">
        <?php echo csrf_field(); ?>
        <div class="grid md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">عنوان الجلسة <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="<?php echo e(old('title')); ?>" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white" placeholder="مثال: حصة تفاعلية — الذكاء الاصطناعي">
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
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">المعلم = المشترك عندنا (طالب يشترون منا الخدمة).</p>
                <select name="instructor_id" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <option value="">اختر المعلم</option>
                    <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($inst->id); ?>" <?php echo e(old('instructor_id') == $inst->id ? 'selected' : ''); ?>><?php echo e($inst->name); ?><?php echo e($inst->role === 'student' ? ' (مشترك)' : ''); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['instructor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">الكورس (اختياري)</label>
                <select name="course_id" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <option value="">جلسة عامة</option>
                    <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($course->id); ?>" <?php echo e(old('course_id') == $course->id ? 'selected' : ''); ?>><?php echo e(Str::limit($course->title, 50)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">موعد البث <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="scheduled_at" value="<?php echo e(old('scheduled_at')); ?>" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                <?php $__errorArgs = ['scheduled_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">سيرفر البث</label>
                <select name="server_id" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <option value="">الافتراضي</option>
                    <?php $__currentLoopData = $servers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $server): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($server->id); ?>"><?php echo e($server->name); ?> (<?php echo e($server->domain); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">الحد الأقصى للمشاركين</label>
                <input type="number" name="max_participants" value="<?php echo e(old('max_participants', 100)); ?>" min="2" max="1000" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">كلمة مرور (اختياري)</label>
                <input type="text" name="password" value="<?php echo e(old('password')); ?>" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white" placeholder="اتركها فارغة إذا لا تريد">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">وصف الجلسة</label>
                <textarea name="description" rows="3" class="w-full rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white" placeholder="وصف مختصر عن محتوى الجلسة..."><?php echo e(old('description')); ?></textarea>
            </div>
        </div>

        <div class="border-t border-slate-200 dark:border-slate-700 pt-5">
            <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-3">إعدادات الغرفة</h3>
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-3">
                <label class="flex items-center gap-2 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg cursor-pointer">
                    <input type="checkbox" name="is_recorded" value="1" <?php echo e(old('is_recorded') ? 'checked' : ''); ?> class="rounded text-red-500 focus:ring-red-500">
                    <span class="text-sm text-slate-700 dark:text-slate-300">تسجيل الجلسة</span>
                </label>
                <label class="flex items-center gap-2 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg cursor-pointer">
                    <input type="checkbox" name="allow_chat" value="1" <?php echo e(old('allow_chat', true) ? 'checked' : ''); ?> class="rounded text-blue-500 focus:ring-blue-500">
                    <span class="text-sm text-slate-700 dark:text-slate-300">السماح بالشات</span>
                </label>
                <label class="flex items-center gap-2 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg cursor-pointer">
                    <input type="checkbox" name="allow_screen_share" value="1" <?php echo e(old('allow_screen_share', true) ? 'checked' : ''); ?> class="rounded text-emerald-500 focus:ring-emerald-500">
                    <span class="text-sm text-slate-700 dark:text-slate-300">مشاركة الشاشة</span>
                </label>
                <label class="flex items-center gap-2 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg cursor-pointer">
                    <input type="checkbox" name="require_enrollment" value="1" <?php echo e(old('require_enrollment', true) ? 'checked' : ''); ?> class="rounded text-amber-500 focus:ring-amber-500">
                    <span class="text-sm text-slate-700 dark:text-slate-300">يتطلب تسجيل في الكورس</span>
                </label>
                <label class="flex items-center gap-2 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg cursor-pointer">
                    <input type="checkbox" name="mute_on_join" value="1" <?php echo e(old('mute_on_join', true) ? 'checked' : ''); ?> class="rounded text-violet-500 focus:ring-violet-500">
                    <span class="text-sm text-slate-700 dark:text-slate-300">كتم الصوت عند الدخول</span>
                </label>
                <label class="flex items-center gap-2 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg cursor-pointer">
                    <input type="checkbox" name="video_off_on_join" value="1" <?php echo e(old('video_off_on_join', true) ? 'checked' : ''); ?> class="rounded text-pink-500 focus:ring-pink-500">
                    <span class="text-sm text-slate-700 dark:text-slate-300">إيقاف الفيديو عند الدخول</span>
                </label>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl font-semibold shadow-lg shadow-red-500/25 transition-all">
                <i class="fas fa-broadcast-tower ml-1"></i> إنشاء الجلسة
            </button>
            <a href="<?php echo e(route('admin.live-sessions.index')); ?>" class="px-6 py-2.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-medium hover:bg-slate-300 transition-colors">إلغاء</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\live-sessions\create.blade.php ENDPATH**/ ?>