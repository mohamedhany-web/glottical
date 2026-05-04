
<?php $__env->startSection('title', 'إعدادات نظام البث المباشر'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white"><i class="fas fa-sliders-h text-violet-500 ml-2"></i>إعدادات نظام البث المباشر</h1>
        <p class="text-sm text-slate-500 mt-1">تكوين إعدادات Jitsi والبث العامة</p>
    </div>

    <?php $currentJitsi = isset($settings['jitsi']) ? $settings['jitsi']->firstWhere('key', 'jitsi_domain') : null; ?>
    <?php if($currentJitsi && $currentJitsi->value && strpos($currentJitsi->value, 'meet.jit.si') !== false): ?>
    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4 flex items-start gap-3">
        <i class="fas fa-exclamation-triangle text-amber-500 text-xl mt-0.5"></i>
        <div>
            <p class="font-bold text-amber-800 dark:text-amber-200">نطاق meet.jit.si للاختبار فقط</p>
            <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">المكالمات المضمّنة (embed) عبر meet.jit.si تُقطع تلقائياً بعد 5 دقائق، ولا يمكن إخفاء شعار Jitsi من المتصفح على هذا النطاق. للإنتاج: غيّر <strong>نطاق Jitsi Meet</strong> أدناه إلى سيرفر Jitsi خاص بك (مع تعديل <code class="text-xs bg-amber-100 dark:bg-amber-950 px-1 rounded">interface_config.js</code> على الخادم إن رغبت بإخفاء العلامة) أو استخدم <strong>Jitsi as a Service</strong> من 8x8.</p>
        </div>
    </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-4 text-emerald-700 dark:text-emerald-400 text-sm">
        <i class="fas fa-check-circle ml-1"></i> <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('admin.live-settings.update')); ?>" class="space-y-6">
        <?php echo csrf_field(); ?>

        <?php $index = 0; ?>
        <?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
            <h2 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                <?php if($group === 'general'): ?>
                    <i class="fas fa-cog text-slate-400"></i> إعدادات عامة
                <?php elseif($group === 'jitsi'): ?>
                    <i class="fas fa-video text-blue-400"></i> إعدادات Jitsi
                <?php elseif($group === 'access'): ?>
                    <i class="fas fa-lock text-amber-400"></i> صلاحيات الدخول
                <?php elseif($group === 'room'): ?>
                    <i class="fas fa-door-open text-emerald-400"></i> إعدادات الغرفة
                <?php else: ?>
                    <i class="fas fa-cog text-slate-400"></i> <?php echo e($group); ?>

                <?php endif; ?>
            </h2>
            <?php if($group === 'jitsi'): ?>
            <div class="mb-4 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-900/50 p-4 text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                <p class="font-semibold text-slate-800 dark:text-slate-100 mb-2"><i class="fas fa-info-circle text-blue-500 ml-1"></i> ظهور كلمة أو شعار «Jitsi» داخل الغرفة</p>
                <p class="mb-2">المنصة ترسل بالفعل طلب إخفاء العلامة عبر واجهة الـ iframe، لكن خوادم Jitsi <strong>لا تطبّق ذلك من المتصفح</strong> لمعاملات العلامة التجارية (قائمة بيضاء في الواجهة). على <strong>meet.jit.si</strong> يبقى الشعار ظاهراً وفق شروط الخدمة.</p>
                <p>لإخفائه على سيرفرك الخاص عدّل ملف الواجهة على الخادم نفسه، مثلاً <code class="text-xs bg-slate-200 dark:bg-slate-700 px-1.5 py-0.5 rounded">/usr/share/jitsi-meet/interface_config.js</code> أو في Docker الملف <code class="text-xs bg-slate-200 dark:bg-slate-700 px-1.5 py-0.5 rounded">CONFIG/web/custom-interface_config.js</code> واضبط <code class="text-xs bg-slate-200 dark:bg-slate-700 px-1 rounded">SHOW_JITSI_WATERMARK</code> و<code class="text-xs bg-slate-200 dark:bg-slate-700 px-1 rounded">SHOW_BRAND_WATERMARK</code> و<code class="text-xs bg-slate-200 dark:bg-slate-700 px-1 rounded">SHOW_WATERMARK_FOR_GUESTS</code> إلى <code class="text-xs bg-slate-200 dark:bg-slate-700 px-1 rounded">false</code> ثم أعد نشر الواجهة أو أعد تحميل الصفحة بعد مسح ذاكرة التخزين المؤقت للمتصفح.</p>
            </div>
            <?php endif; ?>
            <div class="space-y-4">
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center justify-between gap-4">
                    <input type="hidden" name="settings[<?php echo e($index); ?>][key]" value="<?php echo e($setting->key); ?>">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300 flex-1"><?php echo e($setting->label ?? $setting->key); ?></label>
                    <?php if($setting->type === 'boolean'): ?>
                        <select name="settings[<?php echo e($index); ?>][value]" class="w-28 rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                            <option value="1" <?php echo e($setting->value ? 'selected' : ''); ?>>نعم</option>
                            <option value="0" <?php echo e(!$setting->value ? 'selected' : ''); ?>>لا</option>
                        </select>
                    <?php elseif($setting->type === 'integer'): ?>
                        <input type="number" name="settings[<?php echo e($index); ?>][value]" value="<?php echo e($setting->value); ?>" class="w-32 rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm">
                    <?php else: ?>
                        <input type="text" name="settings[<?php echo e($index); ?>][value]" value="<?php echo e($setting->value); ?>" class="w-64 rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm" placeholder="—">
                    <?php endif; ?>
                </div>
                <?php $index++; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <button type="submit" class="px-6 py-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-semibold shadow-lg shadow-violet-500/25 transition-all">
            <i class="fas fa-save ml-1"></i> حفظ الإعدادات
        </button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\admin\live-settings\index.blade.php ENDPATH**/ ?>