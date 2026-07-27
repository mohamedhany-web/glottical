
<?php
    $lead = $salesLead ?? null;
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
?>

<div class="space-y-8">
    <div class="space-y-5">
        <h3 class="border-b border-line pb-2 text-base font-semibold text-ink">بيانات العميل</h3>

        <div>
            <label class="<?php echo e($labelClass); ?>" for="name">الاسم <span class="text-rose-500">*</span></label>
            <input type="text" name="name" id="name" value="<?php echo e(old('name', $lead?->name)); ?>" required class="<?php echo e($fieldClass); ?>" placeholder="اسم العميل الكامل">
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="<?php echo e($labelClass); ?>" for="email">البريد الإلكتروني</label>
                <input type="email" name="email" id="email" value="<?php echo e(old('email', $lead?->email)); ?>" class="<?php echo e($fieldClass); ?>" placeholder="example@email.com">
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="<?php echo e($labelClass); ?>" for="phone">الهاتف</label>
                <input type="text" name="phone" id="phone" value="<?php echo e(old('phone', $lead?->phone)); ?>" class="<?php echo e($fieldClass); ?>" placeholder="01xxxxxxxxx">
                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div>
            <label class="<?php echo e($labelClass); ?>" for="company">الشركة / المؤسسة</label>
            <input type="text" name="company" id="company" value="<?php echo e(old('company', $lead?->company)); ?>" class="<?php echo e($fieldClass); ?>" placeholder="اختياري">
            <?php $__errorArgs = ['company'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>

    <div class="space-y-5 border-t border-line pt-6">
        <h3 class="border-b border-line pb-2 text-base font-semibold text-ink">المصدر والاهتمام</h3>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="<?php echo e($labelClass); ?>" for="source">المصدر <span class="text-rose-500">*</span></label>
                <select name="source" id="source" required class="<?php echo e($fieldClass); ?>">
                    <?php $__currentLoopData = $sourceLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>" <?php if(old('source', $lead?->source ?? \App\Models\SalesLead::SOURCE_OTHER) === $val): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['source'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="<?php echo e($labelClass); ?>" for="interested_advanced_course_id">كورس الاهتمام</label>
                <select name="interested_advanced_course_id" id="interested_advanced_course_id" class="<?php echo e($fieldClass); ?>">
                    <option value="">— اختياري —</option>
                    <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>" <?php if((string) old('interested_advanced_course_id', $lead?->interested_advanced_course_id) === (string) $c->id): echo 'selected'; endif; ?>><?php echo e($c->title); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['interested_advanced_course_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div>
            <label class="<?php echo e($labelClass); ?>" for="notes">ملاحظات</label>
            <textarea name="notes" id="notes" rows="4" class="<?php echo e($areaClass); ?>" placeholder="تفاصيل أول تواصل، الاهتمام، المواعيد..."><?php echo e(old('notes', $lead?->notes)); ?></textarea>
            <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>

    <div class="space-y-5 border-t border-line pt-6">
        <h3 class="border-b border-line pb-2 text-base font-semibold text-ink">التعيين (اختياري)</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <?php if(! $lead): ?>
            <div>
                <label class="<?php echo e($labelClass); ?>" for="marketing_owner_id">مالك التسويق</label>
                <select name="marketing_owner_id" id="marketing_owner_id" class="<?php echo e($fieldClass); ?>">
                    <option value="">أنا (الحساب الحالي)</option>
                    <?php $__currentLoopData = $marketingUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($u->id); ?>" <?php if((string) old('marketing_owner_id') === (string) $u->id): echo 'selected'; endif; ?>><?php echo e($u->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['marketing_owner_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <?php endif; ?>
            <div>
                <label class="<?php echo e($labelClass); ?>" for="assigned_to">موظف المبيعات</label>
                <select name="assigned_to" id="assigned_to" class="<?php echo e($fieldClass); ?>">
                    <option value="">— بدون تعيين —</option>
                    <?php $__currentLoopData = $salesUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($u->id); ?>" <?php if((string) old('assigned_to', $lead?->assigned_to) === (string) $u->id): echo 'selected'; endif; ?>><?php echo e($u->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['assigned_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="<?php echo e($labelClass); ?>" for="crm_group_id">مجموعة الفريق</label>
                <select name="crm_group_id" id="crm_group_id" class="<?php echo e($fieldClass); ?>">
                    <option value="">— اختياري —</option>
                    <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($g->id); ?>" <?php if((string) old('crm_group_id', $lead?->crm_group_id) === (string) $g->id): echo 'selected'; endif; ?>><?php echo e($g->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['crm_group_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>
        <?php if($lead): ?>
            <p class="text-xs text-muted">مالك التسويق ثابت ولا يُغيَّر بعد الإنشاء (<?php echo e($lead->marketingOwner?->name ?? '—'); ?>).</p>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\crm\leads\_form.blade.php ENDPATH**/ ?>