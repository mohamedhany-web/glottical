<?php $__env->startSection('title', 'تعديل الكورس'); ?>
<?php $__env->startSection('header', __('admin.courses_management')); ?>

<?php $__env->startSection('content'); ?>
<div class="px-4 py-8" x-data="courseForm({ selectedSkills: <?php echo json_encode($selectedSkills ?? [], 15, 512) ?> })">
    <div class="w-full max-w-full space-y-8">
        <div class="section-card">
            <div class="section-card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <nav class="text-sm text-slate-500 dark:text-slate-400 mb-2">
                        <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-sky-600 dark:hover:text-sky-400"><?php echo e(__('admin.dashboard')); ?></a>
                        <span class="mx-2">/</span>
                        <a href="<?php echo e(route('admin.advanced-courses.index')); ?>" class="hover:text-sky-600 dark:hover:text-sky-400"><?php echo e(__('admin.courses_management')); ?></a>
                        <span class="mx-2">/</span>
                        <span class="text-slate-700 dark:text-slate-300 truncate"><?php echo e(Str::limit($advancedCourse->title, 30)); ?></span>
                    </nav>
                    <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-slate-100">تعديل الكورس التدريبي</h1>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">تحديث معلومات الكورس والمحتوى والمهارات المستهدفة.</p>
                </div>
                <a href="<?php echo e(route('admin.advanced-courses.index')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-600 px-4 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                    <i class="fas fa-arrow-right"></i>
                    العودة للكورسات
                </a>
            </div>
        </div>

        <form action="<?php echo e(route('admin.advanced-courses.update', $advancedCourse)); ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="xl:col-span-2 space-y-6">
                    <div class="section-card">
                        <div class="section-card-header">
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">المعلومات الأساسية</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">تحديث تفاصيل الكورس التدريبي.</p>
                        </div>
                        <div class="p-6 sm:p-8 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2 space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">عنوان الكورس *</label>
                                    <input type="text" name="title" value="<?php echo e(old('title', $advancedCourse->title)); ?>" required
                                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 shadow-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition"
                                           placeholder="مثال: إدارة الصف الفعّال">
                                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-500 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">المدرّس المسؤول</label>
                                    <select name="instructor_id"
                                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                                        <option value="">بدون مدرّس محدد</option>
                                        <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($instructor->id); ?>" <?php echo e(old('instructor_id', $advancedCourse->instructor_id) == $instructor->id ? 'selected' : ''); ?>>
                                                <?php echo e($instructor->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['instructor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-500 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">مسار الكورس (التصفية في صفحة الكورسات العامة)</label>
                                    <select name="course_category_id"
                                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                                        <option value="">— بدون مسار —</option>
                                        <?php $__currentLoopData = $courseCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($cc->id); ?>" <?php echo e((string) old('course_category_id', $advancedCourse->course_category_id) === (string) $cc->id ? 'selected' : ''); ?>><?php echo e($cc->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                        إدارة القائمة من
                                        <a href="<?php echo e(route('admin.course-categories.index')); ?>" class="text-sky-600 hover:underline font-semibold">مسارات الكورسات</a>.
                                    </p>
                                    <?php $__errorArgs = ['course_category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-500 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">مدة الكورس (ساعات)</label>
                                    <input type="number" name="duration_hours" value="<?php echo e(old('duration_hours', $advancedCourse->duration_hours ?? 0)); ?>" min="0"
                                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">مدة إضافية (دقائق)</label>
                                    <input type="number" name="duration_minutes" value="<?php echo e(old('duration_minutes', $advancedCourse->duration_minutes ?? 0)); ?>" min="0" max="59"
                                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">سعر الكورس الأساسي (قبل الخصم — جنيه)</label>
                                    <input type="number" name="price" value="<?php echo e(old('price', $advancedCourse->price ?? 0)); ?>" min="0" step="0.01"
                                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                                </div>
                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">سعر بعد الخصم (اختياري)</label>
                                    <input type="number" name="price_after_discount" value="<?php echo e(old('price_after_discount', $advancedCourse->price_after_discount)); ?>" min="0" step="0.01"
                                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition"
                                           placeholder="فارغ = بدون عرض ترويجي على البطاقة">
                                    <p class="text-xs text-slate-500 dark:text-slate-400">يجب أن يكون أقل من السعر الأساسي. الدفع والكوبونات على سعر «بعد الخصم».</p>
                                    <?php $__errorArgs = ['price_after_discount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-500 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">وصف الكورس</label>
                                <textarea name="description" rows="4"
                                          class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition"
                                          placeholder="اشرح محتوى الكورس وقيمته للمتدربين."><?php echo e(old('description', $advancedCourse->description)); ?></textarea>
                            </div>

                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                    <i class="fas fa-video text-sky-600 ml-1"></i>
                                    رابط الفيديو التقديمي (يظهر في صفحة الكورس)
                                </label>
                                <input type="url" name="video_url" value="<?php echo e(old('video_url', $advancedCourse->video_url)); ?>"
                                       class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition"
                                       placeholder="رابط تضمين Bunny (iframe.mediadelivery.net)، YouTube، Vimeo، أو .mp4">
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">يُعرض في الصندوق الرئيسي بجانب وصف الكورس.</p>
                                <?php $__errorArgs = ['video_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-500 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">أهداف الكورس</label>
                                <textarea name="objectives" rows="3"
                                          class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition"
                                          placeholder="الأهداف التعليمية للكورس"><?php echo e(old('objectives', $advancedCourse->objectives)); ?></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">تاريخ البداية</label>
                                    <input type="date" name="starts_at" value="<?php echo e(old('starts_at', $advancedCourse->starts_at ? $advancedCourse->starts_at->format('Y-m-d') : '')); ?>"
                                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">تاريخ النهاية</label>
                                    <input type="date" name="ends_at" value="<?php echo e(old('ends_at', $advancedCourse->ends_at ? $advancedCourse->ends_at->format('Y-m-d') : '')); ?>"
                                           class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-card">
                        <div class="section-card-header">
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">المهارات والمخرجات</h2>
                        </div>
                        <div class="p-6 sm:p-8 space-y-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">المهارات المستهدفة (اختياري)</label>
                                <?php
                                    $allSkills = \App\Models\AdvancedCourse::whereNotNull('skills')
                                        ->pluck('skills')
                                        ->flatMap(function($value) {
                                            if (is_array($value)) return $value;
                                            $decoded = is_string($value) ? json_decode($value, true) : null;
                                            return is_array($decoded) ? $decoded : [];
                                        })
                                        ->unique()->values();
                                ?>
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <?php $__currentLoopData = $allSkills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <button type="button" class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-600 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-500 hover:border-sky-400 transition"
                                                @click="addSkill('<?php echo e($skill); ?>')">
                                            <?php echo e($skill); ?>

                                        </button>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input id="customSkill" type="text" class="flex-1 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-sm text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition" placeholder="اكتب مهارة جديدة">
                                    <button type="button" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 text-sm font-semibold transition"
                                            @click="addSkill(document.getElementById('customSkill').value); document.getElementById('customSkill').value='';">
                                        <i class="fas fa-plus"></i>
                                        إضافة
                                    </button>
                                </div>
                                <div class="flex flex-wrap gap-2 mt-3" x-show="selectedSkills.length">
                                    <template x-for="(skill, index) in selectedSkills" :key="skill">
                                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-100 dark:bg-sky-900/40 text-sky-700 dark:text-sky-300 text-xs font-semibold">
                                            <span x-text="skill"></span>
                                            <button type="button" class="text-sky-600 dark:text-sky-400 hover:text-sky-800" @click="removeSkill(index)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <input type="hidden" name="skills[]" :value="skill">
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">المتطلبات المسبقة</label>
                                    <textarea name="prerequisites" rows="3"
                                              class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition"
                                              placeholder="ما الذي يجب أن يعرفه المتدرب قبل بدء الكورس؟"><?php echo e(old('prerequisites', $advancedCourse->prerequisites)); ?></textarea>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">ما الذي سيتعلمه المتدرب؟</label>
                                    <textarea name="what_you_learn" rows="3"
                                              class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition"
                                              placeholder="المخرجات التعليمية والمهارات المكتسبة"><?php echo e(old('what_you_learn', $advancedCourse->what_you_learn)); ?></textarea>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">متطلبات إضافية</label>
                                <textarea name="requirements" rows="3"
                                          class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition"
                                          placeholder="أدوات أو موارد يحتاجها المتدرب خلال الدراسة."><?php echo e(old('requirements', $advancedCourse->requirements)); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="section-card">
                        <div class="section-card-header">
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">إعدادات العرض</h2>
                        </div>
                        <div class="p-6 sm:p-8 space-y-4 text-sm text-slate-700 dark:text-slate-300">
                            <label class="flex items-center justify-between">
                                <span class="font-medium">تفعيل الكورس</span>
                                <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $advancedCourse->is_active) ? 'checked' : ''); ?> class="w-5 h-5 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                            </label>
                            <label class="flex items-center justify-between">
                                <span class="font-medium">وضع الكورس ضمن المميزة</span>
                                <input type="checkbox" name="is_featured" value="1" <?php echo e(old('is_featured', $advancedCourse->is_featured) ? 'checked' : ''); ?> class="w-5 h-5 text-amber-500 border-slate-300 rounded focus:ring-amber-500">
                            </label>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">لغة المحتوى</label>
                                <select name="language"
                                        class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-3 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                                    <option value="ar" <?php echo e(old('language', $advancedCourse->language ?? 'ar') == 'ar' ? 'selected' : ''); ?>>العربية</option>
                                    <option value="en" <?php echo e(old('language', $advancedCourse->language) == 'en' ? 'selected' : ''); ?>>الإنجليزية</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">رفع صورة للكورس</label>
                                <?php if($advancedCourse->thumbnail): ?>
                                    <div class="mb-3">
                                        <img src="<?php echo e(asset('storage/' . $advancedCourse->thumbnail)); ?>" alt="صورة الكورس الحالية"
                                             class="w-full h-32 object-cover rounded-xl border border-slate-200 dark:border-slate-600">
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">الصورة الحالية</p>
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="thumbnail" accept="image/*"
                                       class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition">
                                <p class="text-xs text-slate-500 dark:text-slate-400">PNG أو JPG بحد أقصى 40 ميجابايت.</p>
                            </div>
                        </div>
                    </div>

                    <div class="section-card">
                        <div class="section-card-header">
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">ملخص</h2>
                        </div>
                        <div class="p-6 sm:p-8 space-y-3 text-sm text-slate-600 dark:text-slate-400">
                            <div class="flex items-center justify-between">
                                <span>عدد المهارات المحددة</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="selectedSkills.length"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>الحالة</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200"><?php echo e(old('is_active', $advancedCourse->is_active) ? 'نشط' : 'مسودة'); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="section-card">
                        <div class="p-6 sm:p-8 space-y-3">
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white px-6 py-3 text-sm font-semibold shadow-lg transition">
                                <i class="fas fa-save"></i>
                                حفظ التعديلات
                            </button>
                            <a href="<?php echo e(route('admin.advanced-courses.show', $advancedCourse)); ?>" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 text-sm font-semibold transition">
                                <i class="fas fa-eye"></i>
                                عرض الكورس
                            </a>
                            <a href="<?php echo e(route('admin.advanced-courses.index')); ?>" class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 dark:border-slate-600 px-6 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                                إلغاء
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function courseForm({ selectedSkills }) {
    return {
        selectedSkills: selectedSkills || [],
        addSkill(value) {
            const skill = (value || '').trim();
            if (!skill) return;
            if (!this.selectedSkills.includes(skill)) this.selectedSkills.push(skill);
        },
        removeSkill(index) {
            this.selectedSkills.splice(index, 1);
        }
    };
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\advanced-courses\edit.blade.php ENDPATH**/ ?>