

<?php $__env->startSection('title', 'إضافة عميل محتمل'); ?>
<?php $__env->startSection('header', 'إضافة عميل محتمل'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full space-y-6">
    <?php echo $__env->make('partials.crm-employee-nav', ['role' => 'marketing'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm p-5 sm:p-6">
        <nav class="text-sm text-slate-500 dark:text-slate-400 mb-2">
            <a href="<?php echo e(route('employee.crm.dashboard')); ?>" class="hover:text-teal-600 transition-colors">Glottical CRM</a>
            <span class="mx-2">/</span>
            <a href="<?php echo e(route('employee.crm.leads.index')); ?>" class="hover:text-teal-600 transition-colors">العملاء المحتملون</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 dark:text-slate-300 font-semibold">إضافة عميل جديد</span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-600 dark:bg-teal-900/50 dark:text-teal-300 flex items-center justify-center shrink-0">
                    <i class="fas fa-user-plus text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100">إضافة عميل محتمل</h1>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-0.5">سجّل العميل فور أول تواصل — يصبح في حالة «عميل جديد» حتى تعيّنه الإدارة لمندوب مبيعات.</p>
                </div>
            </div>
            <a href="<?php echo e(route('employee.crm.leads.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-700/50 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl font-semibold transition-colors shrink-0">
                <i class="fas fa-arrow-right"></i>
                العودة للقائمة
            </a>
        </div>
    </div>

    <div class="rounded-xl border border-teal-200 bg-teal-50 px-4 py-3 text-sm text-teal-900 dark:bg-teal-950/40 dark:border-teal-800 dark:text-teal-100">
        <p class="font-bold">تذكير للتسويق</p>
        <p class="mt-1 text-teal-800 dark:text-teal-200">أنت مالك التسويق لهذا العميل — عمولتك تبقى لك حتى لو تغيّر موظف المبيعات لاحقاً. ما لم يُسجَّل في CRM، لم يحدث.</p>
    </div>

    
    <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <form method="POST" action="<?php echo e(route('employee.crm.leads.store')); ?>">
            <?php echo csrf_field(); ?>
            <div class="p-6 sm:p-8 space-y-8">
                <div class="space-y-6">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 border-b border-slate-200 dark:border-slate-700 pb-2">بيانات العميل</h2>

                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">الاسم <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" required
                               class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 text-slate-800 dark:text-slate-100"
                               placeholder="اسم العميل الكامل">
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">البريد الإلكتروني</label>
                            <input type="email" name="email" id="email" value="<?php echo e(old('email')); ?>"
                                   class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 text-slate-800 dark:text-slate-100"
                                   placeholder="example@email.com">
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">الهاتف</label>
                            <input type="text" name="phone" id="phone" value="<?php echo e(old('phone')); ?>"
                                   class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 text-slate-800 dark:text-slate-100"
                                   placeholder="01xxxxxxxxx">
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div>
                        <label for="company" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">الشركة / المؤسسة</label>
                        <input type="text" name="company" id="company" value="<?php echo e(old('company')); ?>"
                               class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 text-slate-800 dark:text-slate-100"
                               placeholder="اختياري">
                        <?php $__errorArgs = ['company'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="space-y-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 border-b border-slate-200 dark:border-slate-700 pb-2">الاهتمام والمصدر</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="source" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">من أين عرفناه؟ <span class="text-red-500">*</span></label>
                            <select name="source" id="source" required
                                    class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 text-slate-800 dark:text-slate-100">
                                <?php $__currentLoopData = \App\Models\SalesLead::sourceLabels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($val); ?>" <?php echo e(old('source', \App\Models\SalesLead::SOURCE_OTHER) === $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['source'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="interested_advanced_course_id" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">الكورس الذي يهتم به</label>
                            <select name="interested_advanced_course_id" id="interested_advanced_course_id"
                                    class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 text-slate-800 dark:text-slate-100">
                                <option value="">— اختياري —</option>
                                <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($c->id); ?>" <?php echo e((string) old('interested_advanced_course_id') === (string) $c->id ? 'selected' : ''); ?>><?php echo e($c->title); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['interested_advanced_course_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">ملاحظات</label>
                        <textarea name="notes" id="notes" rows="4"
                                  class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 text-slate-800 dark:text-slate-100"
                                  placeholder="أي تفاصيل من أول تواصل: الاهتمام، الموعد، طريقة التواصل..."><?php echo e(old('notes')); ?></textarea>
                        <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="bg-sky-50 dark:bg-sky-900/30 border border-sky-200 dark:border-sky-800 rounded-xl p-4 text-sm text-sky-800 dark:text-sky-200 max-w-lg">
                        <span class="font-semibold">نصيحة:</span> بعد الحفظ تابع العميل من قائمة «العملاء المحتملون» حتى تُعيّنه الإدارة لمندوب مبيعات.
                    </div>
                    <div class="flex flex-wrap gap-3 shrink-0">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-bold transition-colors">
                            <i class="fas fa-save"></i>
                            حفظ العميل المحتمل
                        </button>
                        <a href="<?php echo e(route('employee.crm.leads.index')); ?>" class="inline-flex items-center gap-2 px-6 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl font-semibold transition-colors">
                            إلغاء
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.employee', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/employee/crm/leads/create.blade.php ENDPATH**/ ?>