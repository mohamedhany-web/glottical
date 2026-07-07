

<?php $__env->startSection('title', 'تعديل مجموعة الفريق'); ?>
<?php $__env->startSection('header', 'تعديل مجموعة الفريق'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full space-y-6">
    <?php echo $__env->make('partials.crm-admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm p-5 sm:p-6">
        <nav class="text-sm text-slate-500 dark:text-slate-400 mb-2">
            <a href="<?php echo e(route('admin.crm.dashboard')); ?>" class="hover:text-indigo-600 transition-colors">Glottical CRM</a>
            <span class="mx-2">/</span>
            <a href="<?php echo e(route('admin.crm.groups.index')); ?>" class="hover:text-indigo-600 transition-colors">مجموعات الفريق</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 dark:text-slate-300 font-semibold"><?php echo e($group->name); ?></span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-300 flex items-center justify-center shrink-0">
                    <i class="fas fa-users text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100"><?php echo e($group->name); ?></h1>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-0.5">
                        قائد الفريق: <strong><?php echo e($group->teamLeader?->name ?? '—'); ?></strong>
                        — <?php echo e($group->members->where('is_active', true)->count()); ?> عضو
                        — <?php echo e($group->leads_count); ?> عميل محتمل
                    </p>
                </div>
            </div>
            <a href="<?php echo e(route('admin.crm.groups.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-700/50 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl font-semibold transition-colors shrink-0">
                <i class="fas fa-arrow-right"></i>
                العودة للقائمة
            </a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm dark:bg-emerald-950/40 dark:border-emerald-800 dark:text-emerald-100">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        <div class="xl:col-span-1">
            <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <form method="POST" action="<?php echo e(route('admin.crm.groups.update', $group)); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="p-6 sm:p-8 space-y-6">
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 border-b border-slate-200 dark:border-slate-700 pb-2">بيانات المجموعة</h2>

                        <div>
                            <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">اسم المجموعة <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="<?php echo e(old('name', $group->name)); ?>" required
                                   class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-800 dark:text-slate-100">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="team_leader_id" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">قائد الفريق</label>
                            <select name="team_leader_id" id="team_leader_id"
                                    class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-800 dark:text-slate-100">
                                <option value="">— اختياري —</option>
                                <?php $__currentLoopData = $leaders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($l->id); ?>" <?php if((string) old('team_leader_id', $group->team_leader_id) === (string) $l->id): echo 'selected'; endif; ?>><?php echo e($l->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['team_leader_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-medium text-slate-700 dark:text-slate-300">
                            <input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $group->is_active)): echo 'checked'; endif; ?> class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            المجموعة نشطة
                        </label>

                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-colors">
                            <i class="fas fa-save"></i>
                            حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>

        
        <div class="xl:col-span-2 space-y-6">
            <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8 space-y-6">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 border-b border-slate-200 dark:border-slate-700 pb-2">إضافة عضو للفريق</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">أضف موظفي التسويق والمبيعات. عند تعيين عميل محتمل يمكن ربطه بهذه المجموعة لاحتساب عمولة قائد الفريق.</p>
                    </div>

                    <form method="POST" action="<?php echo e(route('admin.crm.groups.members.store', $group)); ?>" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <?php echo csrf_field(); ?>
                        <div class="sm:col-span-1">
                            <label for="user_id" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">الموظف</label>
                            <select name="user_id" id="user_id" required
                                    class="w-full px-3 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100">
                                <option value="">اختر موظفاً</option>
                                <optgroup label="تسويق">
                                    <?php $__currentLoopData = $marketingUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </optgroup>
                                <optgroup label="مبيعات">
                                    <?php $__currentLoopData = $salesUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </optgroup>
                            </select>
                        </div>
                        <div class="sm:col-span-1">
                            <label for="role" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">الدور في الفريق</label>
                            <select name="role" id="role"
                                    class="w-full px-3 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800 dark:text-slate-100">
                                <option value="marketing">تسويق</option>
                                <option value="sales">مبيعات</option>
                            </select>
                        </div>
                        <div class="sm:col-span-1 flex items-end">
                            <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold transition-colors">
                                <i class="fas fa-user-plus ml-1"></i> إضافة
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="rounded-2xl bg-white dark:bg-slate-800/95 border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">أعضاء المجموعة</h2>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-700">
                    <?php $__empty_1 = true; $__currentLoopData = $group->members->where('is_active', true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 font-bold shrink-0">
                                    <?php echo e(mb_substr($m->user?->name ?? '?', 0, 1)); ?>

                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-800 dark:text-slate-100 truncate"><?php echo e($m->user?->name ?? '—'); ?></p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        <?php echo e($m->role === 'marketing' ? 'تسويق' : ($m->role === 'sales' ? 'مبيعات' : $m->role)); ?>

                                    </p>
                                </div>
                            </div>
                            <form method="POST" action="<?php echo e(route('admin.crm.groups.members.destroy', [$group, $m])); ?>" onsubmit="return confirm('إزالة هذا العضو من المجموعة؟')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-rose-600 hover:text-rose-700 text-xs font-bold px-3 py-1.5 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors">
                                    إزالة
                                </button>
                            </form>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 text-sm">
                            لا يوجد أعضاء بعد — أضف موظفي التسويق والمبيعات من النموذج أعلاه.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/admin/crm/groups/edit.blade.php ENDPATH**/ ?>