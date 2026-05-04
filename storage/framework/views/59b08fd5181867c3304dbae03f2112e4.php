<?php $__env->startSection('title', 'إصدار شهادة جديدة'); ?>
<?php $__env->startSection('header', 'إصدار شهادة جديدة'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">إصدار شهادة جديدة</h1>
        
        <form action="<?php echo e(route('admin.certificates.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?php echo csrf_field(); ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الطالب *</label>
                    <select id="certificate-user" name="user_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">اختر الطالب</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?> - <?php echo e($user->phone); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الكورس *</label>
                    <select id="certificate-course" name="course_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">اختر الكورس</option>
                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($course->id); ?>"><?php echo e($course->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <p class="text-xs text-gray-500 mt-2">سيتم عرض كورسات الطالب بعد اختياره.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العنوان *</label>
                    <input type="text" name="title" required value="<?php echo e(old('title')); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الإصدار</label>
                    <input type="date" name="issued_at" value="<?php echo e(old('issued_at', date('Y-m-d'))); ?>" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة *</label>
                    <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="pending" <?php echo e(old('status') == 'pending' ? 'selected' : ''); ?>>معلقة</option>
                        <option value="issued" <?php echo e(old('status') == 'issued' ? 'selected' : ''); ?>>مُصدرة</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                <textarea name="description" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500"><?php echo e(old('description')); ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ملف الشهادة (PDF فقط) *</label>
                <input type="file" name="certificate_file" required accept=".pdf,application/pdf"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-white">
                <?php $__errorArgs = ['certificate_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-sm text-red-600 mt-2"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <p class="text-xs text-gray-500 mt-2">يُسلَّم للطالب كملف PDF. الحد الأقصى 40 ميجابايت.</p>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                    إصدار الشهادة
                </button>
                <a href="<?php echo e(route('admin.certificates.index')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    (function () {
        const userSelect = document.getElementById('certificate-user');
        const courseSelect = document.getElementById('certificate-course');

        if (!userSelect || !courseSelect) return;

        const allCourseOptions = Array.from(courseSelect.querySelectorAll('option')).map(o => ({
            value: o.value,
            text: o.textContent
        }));

        function setCourseOptions(options) {
            courseSelect.innerHTML = '';
            for (const opt of options) {
                const el = document.createElement('option');
                el.value = opt.value;
                el.textContent = opt.text;
                courseSelect.appendChild(el);
            }
        }

        async function loadUserCourses(userId) {
            // reset to placeholder
            setCourseOptions([{ value: '', text: 'اختر الكورس' }]);
            if (!userId) return;

            try {
                const res = await fetch(`<?php echo e(url('/admin/certificates/user')); ?>/${encodeURIComponent(userId)}/courses`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) throw new Error('Failed to load');

                const data = await res.json();
                const courses = Array.isArray(data.courses) ? data.courses : [];

                if (courses.length > 0) {
                    setCourseOptions([{ value: '', text: 'اختر الكورس' }].concat(
                        courses.map(c => ({ value: String(c.id), text: c.title }))
                    ));
                } else {
                    // fallback to all courses if the student has none
                    setCourseOptions(allCourseOptions);
                }
            } catch (e) {
                // fallback to all courses on error
                setCourseOptions(allCourseOptions);
            }
        }

        userSelect.addEventListener('change', () => {
            loadUserCourses(userSelect.value);
        });
    })();
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\certificates\create.blade.php ENDPATH**/ ?>