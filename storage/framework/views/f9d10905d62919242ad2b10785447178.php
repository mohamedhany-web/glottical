

<?php $__env->startSection('title', 'الدعم الفني'); ?>
<?php $__env->startSection('header', 'الدعم الفني'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm font-medium"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-6">
        <h1 class="text-2xl font-black text-slate-900 dark:text-white">خدمة الدعم الفني 24/7</h1>
        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">أنشئ تذكرة دعم وسيقوم الفريق بمتابعتها حتى الحل.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <form action="<?php echo e(route('student.support.store')); ?>" method="POST" class="lg:col-span-1 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5 space-y-4">
            <?php echo csrf_field(); ?>
            <h2 class="font-bold text-slate-900 dark:text-white">إنشاء تذكرة جديدة</h2>
            <?php if($inquiryCategories->isEmpty()): ?>
                <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 px-4 py-3 text-sm">
                    لا توجد تصنيفات استفسار متاحة حالياً. يرجى التواصل مع الإدارة أو المحاولة لاحقاً.
                </div>
            <?php else: ?>
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">تصنيف الاستفسار</label>
                <select name="support_inquiry_category_id" required class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                    <option value="" disabled <?php echo e(old('support_inquiry_category_id') ? '' : 'selected'); ?>>— اختر التصنيف —</option>
                    <?php $__currentLoopData = $inquiryCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->id); ?>" <?php if((string) old('support_inquiry_category_id') === (string) $cat->id): echo 'selected'; endif; ?>><?php echo e($cat->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['support_inquiry_category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">عنوان المشكلة</label>
                <input type="text" name="subject" value="<?php echo e(old('subject')); ?>" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">الأولوية</label>
                <select name="priority" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                    <option value="normal">عادية</option>
                    <option value="low">منخفضة</option>
                    <option value="high">عالية</option>
                    <option value="urgent">عاجلة</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">تفاصيل المشكلة</label>
                <textarea name="message" rows="6" class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white"><?php echo e(old('message')); ?></textarea>
                <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold">إرسال التذكرة</button>
            <?php endif; ?>
        </form>

        <div class="lg:col-span-2 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-700">
                <h2 class="font-bold text-slate-900 dark:text-white">تذاكري</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr class="text-xs text-slate-600 dark:text-slate-300 uppercase">
                            <th class="px-4 py-3 text-right">التصنيف</th>
                            <th class="px-4 py-3 text-right">العنوان</th>
                            <th class="px-4 py-3 text-right">الحالة</th>
                            <th class="px-4 py-3 text-right">الأولوية</th>
                            <th class="px-4 py-3 text-right">آخر تحديث</th>
                            <th class="px-4 py-3 text-right">تفاصيل</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                        <?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/20">
                                <td class="px-4 py-3 text-xs text-slate-600 dark:text-slate-300"><?php echo e($ticket->inquiryCategory->name ?? '—'); ?></td>
                                <td class="px-4 py-3 text-sm font-semibold text-slate-900 dark:text-white"><?php echo e($ticket->subject); ?></td>
                                <td class="px-4 py-3 text-xs text-slate-700 dark:text-slate-300"><?php echo e($ticket->status); ?></td>
                                <td class="px-4 py-3 text-xs text-slate-700 dark:text-slate-300"><?php echo e($ticket->priority); ?></td>
                                <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400"><?php echo e(optional($ticket->last_reply_at ?? $ticket->updated_at)->format('Y-m-d H:i')); ?></td>
                                <td class="px-4 py-3 text-sm"><a href="<?php echo e(route('student.support.show', $ticket)); ?>" class="text-sky-600 hover:underline">فتح</a></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">لا توجد تذاكر بعد.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t border-slate-200 dark:border-slate-700"><?php echo e($tickets->links()); ?></div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\student\support\index.blade.php ENDPATH**/ ?>