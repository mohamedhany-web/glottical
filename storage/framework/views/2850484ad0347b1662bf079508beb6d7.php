<?php $__env->startSection('title', 'إعدادات النظام'); ?>
<?php $__env->startSection('header', 'إعدادات النظام'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-6xl mx-auto space-y-6 pb-10">
    
    <section class="rounded-2xl border border-slate-200 dark:border-slate-600 bg-gradient-to-br from-slate-50 via-white to-sky-50/50 dark:from-slate-800 dark:via-slate-800 dark:to-slate-900 p-6 sm:p-8 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-start gap-6">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-sky-500 to-blue-600 flex items-center justify-center text-white shadow-lg shadow-sky-500/25 shrink-0">
                <i class="fas fa-sliders-h text-2xl"></i>
            </div>
            <div class="flex-1 min-w-0 space-y-3">
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-slate-100 leading-tight">مركز إعدادات المنصة</h2>
                <p class="text-sm sm:text-[15px] text-slate-600 dark:text-slate-300 leading-7 max-w-3xl">
                    من هنا تضبط ما يظهر للزوار في الفوتر (تواصل وسوشيال)، وشعار لوحة التحكم، و<strong class="text-slate-800 dark:text-slate-200">المصادقة الثنائية لحسابات الأدمن</strong> فقط.
                    يمكن لاحقاً إضافة أقسام أخرى في هذه الصفحة دون تغيير عنوان واحد.
                </p>
                <ul class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 space-y-1.5 list-disc list-inside max-w-3xl">
                    <li><strong class="text-slate-700 dark:text-slate-300">الشعار:</strong> يُفضّل صورة مربعة أو شبه مربعة بخلفية شفافة أو فاتحة، بحد أقصى 2 ميغابايت.</li>
                    <li><strong class="text-slate-700 dark:text-slate-300">الفوتر:</strong> اترك أي حقل فارغاً واحفظ لاستعادة القيمة الافتراضية لهذا الحقل.</li>
                </ul>
            </div>
        </div>
    </section>

    <?php if(session('success')): ?>
        <div class="flex items-center gap-3 px-5 py-3 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm font-semibold">
            <i class="fas fa-check-circle"></i>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('info')): ?>
        <div class="flex items-center gap-3 px-5 py-3 rounded-2xl bg-sky-50 dark:bg-sky-900/20 border border-sky-100 dark:border-sky-800 text-sky-800 dark:text-sky-200 text-sm font-semibold">
            <i class="fas fa-info-circle"></i>
            <?php echo e(session('info')); ?>

        </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-2xl text-rose-800 dark:text-rose-200 text-sm">
            <p class="font-bold mb-2">يرجى تصحيح ما يلي:</p>
            <ul class="list-disc list-inside space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($err); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo e(route('admin.system-settings.update')); ?>" enctype="multipart/form-data" class="space-y-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        
        <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-600 bg-slate-50/80 dark:bg-slate-700/30 flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300 text-sm font-black">1</span>
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-slate-100">شعار لوحة التحكم وأيقونة الموقع</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">يظهر بدل الحرف «M» في الشريط الجانبي، وفي شعار النافبار العام (الصفحة الرئيسية والصفحات العامة)، وبجانب عنوان الصفحة في الشريط العلوي للوحة التحكم، وكأيقونة تبويب المتصفح (favicon).</p>
                    <p class="text-[11px] text-sky-700 dark:text-sky-300 mt-2 leading-relaxed rounded-lg bg-sky-50 dark:bg-sky-900/20 border border-sky-100 dark:border-sky-800 px-3 py-2">
                        <strong>محلياً:</strong> نفّذ <code class="text-[10px] bg-white dark:bg-slate-800 px-1 rounded">php artisan storage:link</code> وتأكد أن <code class="text-[10px]">APP_URL</code> يطابق عنوان المتصفح (مثلاً <code class="text-[10px]">http://127.0.0.1:8000</code>).<br>
                        <strong>Cloudflare R2:</strong> في <code class="text-[10px]">.env</code> اضبط <code class="text-[10px]">ADMIN_BRANDING_DISK=r2</code> مع <code class="text-[10px]">AWS_*</code> و<code class="text-[10px]">AWS_URL</code> (رابط الـ bucket العام)، ثم <code class="text-[10px]">php artisan config:clear</code>.
                    </p>
                </div>
            </div>
            <div class="p-6 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center gap-6">
                    <div class="shrink-0">
                        <?php if($adminPanelLogoUrl): ?>
                            <div class="w-24 h-24 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-600 p-2 bg-slate-50 dark:bg-slate-900 flex items-center justify-center">
                                <img src="<?php echo e($adminPanelLogoUrl); ?>" alt="" class="max-w-full max-h-full object-contain">
                            </div>
                        <?php else: ?>
                            <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-3xl font-black shadow-md">M</div>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 space-y-3 min-w-0">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">رفع شعار جديد</label>
                        <input type="file" name="admin_panel_logo" accept="image/jpeg,image/png,image/webp,image/gif"
                               class="block w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 dark:file:bg-slate-700 dark:file:text-slate-200">
                        <p class="text-xs text-slate-500">صيغ مسموحة: JPG, PNG, WebP, GIF — حتى 2 ميغابايت.</p>
                        <?php if($adminPanelLogoUrl): ?>
                        <label class="inline-flex items-center gap-2 cursor-pointer text-sm text-rose-700 dark:text-rose-300">
                            <input type="checkbox" name="remove_admin_panel_logo" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500" <?php if(old('remove_admin_panel_logo')): echo 'checked'; endif; ?>>
                            <span>حذف الشعار الحالي والعودة للحرف الافتراضي</span>
                        </label>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-600 bg-slate-50/80 dark:bg-slate-700/30 flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 text-sm font-black">★</span>
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-slate-100">رابط عرض الصور (Cloudflare R2)</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">يُستخدم للشعار، سلايدر الصفحة الرئيسية، صور الكورسات، وكل ملفات المنصة. بدونه قد لا تظهر الصور على الاستضافة.</p>
                </div>
            </div>
            <div class="p-6 space-y-3">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-200">R2 Public URL</label>
                <input type="url" name="r2_public_url" value="<?php echo e(old('r2_public_url', $r2PublicUrl)); ?>"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-sm text-slate-800 dark:text-slate-100 font-mono"
                       placeholder="https://pub-xxxxxxxx.r2.dev">
                <p class="text-xs text-slate-500 leading-relaxed">
                    من لوحة Cloudflare: R2 → Bucket → <strong>Public access</strong> → انسخ رابط <code class="text-[10px] bg-slate-100 dark:bg-slate-700 px-1 rounded">r2.dev</code>.
                    لا تستخدم <code class="text-[10px]">cloudflarestorage.com</code>. يمكن أيضاً ضبط <code class="text-[10px]">R2_PUBLIC_URL</code> في <code class="text-[10px]">.env</code> على السيرفر.
                </p>
                <?php $__errorArgs = ['r2_public_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-xs text-rose-600 font-semibold"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        
        <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-600 bg-slate-50/80 dark:bg-slate-700/30 flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 text-sm font-black">2</span>
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-slate-100">فوتر الموقع العام</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">يُعرض في الصفحة الرئيسية، صفحات الخدمات، الأسعار، ومعرض الأعمال.</p>
                </div>
            </div>
            <div class="p-6 space-y-8">
                <section class="space-y-4">
                    <h4 class="text-sm font-black text-slate-800 dark:text-slate-200 border-b border-slate-100 dark:border-slate-600 pb-2">الهوية والنص التعريفي</h4>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">السطر تحت اسم Muallimx (النافبار والفوتر)</label>
                        <input type="text" name="footer_brand_tagline" value="<?php echo e(old('footer_brand_tagline', $values['footer_brand_tagline'])); ?>"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-sm text-slate-800 dark:text-slate-100"
                               placeholder="<?php echo e($defaults['footer_brand_tagline']); ?>">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">فقرة تعريفية قصيرة</label>
                        <textarea name="footer_blurb" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-sm text-slate-800 dark:text-slate-100"
                                  placeholder="<?php echo e($defaults['footer_blurb']); ?>"><?php echo e(old('footer_blurb', $values['footer_blurb'])); ?></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">السطر بجانب حقوق النشر أسفل الفوتر</label>
                        <input type="text" name="footer_bottom_tagline" value="<?php echo e(old('footer_bottom_tagline', $values['footer_bottom_tagline'])); ?>"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-sm text-slate-800 dark:text-slate-100"
                               placeholder="<?php echo e($defaults['footer_bottom_tagline']); ?>">
                    </div>
                </section>

                <section class="space-y-4">
                    <h4 class="text-sm font-black text-slate-800 dark:text-slate-200 border-b border-slate-100 dark:border-slate-600 pb-2">التواصل</h4>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">البريد الإلكتروني</label>
                        <input type="email" name="footer_email" value="<?php echo e(old('footer_email', $values['footer_email'])); ?>"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-sm" dir="ltr"
                               placeholder="<?php echo e($defaults['footer_email']); ?>">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">رقم الهاتف (عرض + رابط اتصال عند الإمكان)</label>
                        <input type="text" name="footer_phone" value="<?php echo e(old('footer_phone', $values['footer_phone'])); ?>"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-sm" dir="ltr"
                               placeholder="<?php echo e($defaults['footer_phone']); ?>">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">رابط واتساب (كاملاً، مثل https://wa.me/20…)</label>
                        <input type="url" name="footer_whatsapp_url" value="<?php echo e(old('footer_whatsapp_url', $values['footer_whatsapp_url'])); ?>"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-sm" dir="ltr"
                               placeholder="<?php echo e($defaults['footer_whatsapp_url']); ?>">
                    </div>
                </section>

                <section class="space-y-4">
                    <h4 class="text-sm font-black text-slate-800 dark:text-slate-200 border-b border-slate-100 dark:border-slate-600 pb-2">وسائل التواصل الاجتماعي</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400">تظهر أيقونة المنصة في الفوتر فقط عند ملء الرابط. استخدم رابط الصفحة العامة لحسابك.</p>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <?php $__currentLoopData = [
                            'social_facebook_url' => 'Facebook',
                            'social_x_url' => 'X (Twitter)',
                            'social_instagram_url' => 'Instagram',
                            'social_youtube_url' => 'YouTube',
                            'social_linkedin_url' => 'LinkedIn',
                            'social_tiktok_url' => 'TikTok',
                            'social_telegram_url' => 'Telegram',
                            'social_snapchat_url' => 'Snapchat',
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1"><?php echo e($label); ?></label>
                            <input type="url" name="<?php echo e($field); ?>" value="<?php echo e(old($field, $values[$field])); ?>"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-sm" dir="ltr" placeholder="https://">
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </section>
            </div>
        </div>

        
        <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-600 bg-slate-50/80 dark:bg-slate-700/30 flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-200 text-sm font-black">
                    <i class="fas fa-credit-card"></i>
                </span>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-black text-slate-900 dark:text-slate-100">بوابة الدفع — فواتيرك (IFrame)</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">عند التفعيل، صفحة شراء الكورس تعرض نموذج الدفع الإلكتروني فقط ولا يُقبل رفع إيصال تحويل يدوي.</p>
                </div>
                <?php if($fawaterakGatewayEnabled): ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-700">مفعّل</span>
                <?php else: ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600">معطّل</span>
                <?php endif; ?>
            </div>
            <div class="p-6 space-y-4">
                <input type="hidden" name="fawaterak_gateway_enabled" value="0">
                <label class="flex items-start gap-4 cursor-pointer group">
                    <input type="checkbox" name="fawaterak_gateway_enabled" value="1" class="mt-1 rounded border-slate-300 text-amber-600 focus:ring-amber-500"
                           <?php if((string) old('fawaterak_gateway_enabled', $fawaterakGatewayEnabled ? '1' : '0') === '1'): echo 'checked'; endif; ?>>
                    <span class="text-sm text-slate-700 dark:text-slate-200 leading-7">
                        <span class="font-black text-slate-900 dark:text-slate-100 block mb-1">تفعيل الدفع عبر فواتيرك</span>
                        يظهر إطار الدفع الرسمي على صفحة إتمام طلب الكورس، ويُعطّل نموذج التحويل اليدوي ورفع الإيصال.
                    </span>
                </label>
                <div class="rounded-xl border px-4 py-3 text-xs sm:text-sm leading-7 <?php echo e($fawaterakEnvConfigured ? 'bg-emerald-50/80 dark:bg-emerald-900/15 border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-100' : 'bg-amber-50/80 dark:bg-amber-900/15 border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-100'); ?>">
                    <?php if($fawaterakEnvConfigured): ?>
                        <i class="fas fa-check-circle ml-1"></i>
                        مفاتيح API مضبوطة في ملف البيئة (<code class="text-[11px] bg-white/80 dark:bg-slate-800 px-1 rounded" dir="ltr">FAWATERAK_VENDOR_KEY</code> و<code class="text-[11px] bg-white/80 dark:bg-slate-800 px-1 rounded" dir="ltr">FAWATERAK_PROVIDER_KEY</code>).
                    <?php else: ?>
                        <i class="fas fa-exclamation-triangle ml-1"></i>
                        أضف في <code class="text-[11px] bg-white/80 dark:bg-slate-800 px-1 rounded" dir="ltr">.env</code> القيم <code class="text-[11px] px-1 rounded" dir="ltr">FAWATERAK_VENDOR_KEY</code> و<code class="text-[11px] px-1 rounded" dir="ltr">FAWATERAK_PROVIDER_KEY</code> ثم نفّذ <code class="text-[11px] px-1 rounded" dir="ltr">php artisan config:clear</code>. بدونها لن يظهر الدفع حتى مع تفعيل الخيار أعلاه.
                    <?php endif; ?>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-6">
                    في لوحة فواتيرك: <strong class="text-slate-700 dark:text-slate-300">Integrations → Fawaterak</strong> — سجّل نطاقات الـ IFrame بصيغة <strong class="text-slate-700 dark:text-slate-300">HTTPS</strong> بدون شرطة مائلة في النهاية، وطابق قيمة <code class="text-[10px] bg-slate-100 dark:bg-slate-700 px-1 rounded" dir="ltr">FAWATERAK_IFRAME_DOMAIN</code> أو <code class="text-[10px] bg-slate-100 dark:bg-slate-700 px-1 rounded" dir="ltr">APP_URL</code> مع ما تتوقعه فواتيرك في حساب الـ HMAC.
                </p>
                <div class="pt-2 border-t border-slate-100 dark:border-slate-600">
                    <label for="payment_gateway_fee_percent" class="block text-sm font-black text-slate-900 dark:text-slate-100 mb-2">عمولة بوابة الدفع (تقديرية %)</label>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-2 leading-6">نسبة مئوية من <strong>مبلغ العميل</strong> تُسجَّل كعمولة في المحاسبة (مدفوعات أونلاين: فواتيرك، كاشير، واشتراك الباقة عبر فواتيرك). اترك الحقل فارغاً أو 0 إن لم ترد تسجيل عمولة.</p>
                    <input type="text" name="payment_gateway_fee_percent" id="payment_gateway_fee_percent" inputmode="decimal"
                           value="<?php echo e(old('payment_gateway_fee_percent', $paymentGatewayFeePercent)); ?>"
                           class="w-full max-w-xs px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-sm font-mono" dir="ltr" placeholder="مثال: 2.5">
                    <?php $__errorArgs = ['payment_gateway_fee_percent'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-sm text-rose-600 mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
        </div>

        
        <div class="flex flex-wrap items-center gap-3 sticky bottom-4 z-10">
            <button type="submit" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 text-white text-sm font-black shadow-lg shadow-sky-500/25 hover:from-sky-700 hover:to-blue-700 transition-colors">
                <i class="fas fa-save"></i>
                حفظ كل الإعدادات
            </button>
            <a href="<?php echo e(route('home')); ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-sm font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                <i class="fas fa-external-link-alt"></i>
                معاينة الموقع العام
            </a>
        </div>
    </form>

    
    <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-600 bg-slate-50/80 dark:bg-slate-700/30 flex flex-wrap items-center gap-3">
            <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-300 text-sm font-black">3</span>
            <div class="flex-1 min-w-0">
                <h3 class="text-base font-black text-slate-900 dark:text-slate-100">المصادقة الثنائية للمنصة</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">عند التفعيل، يُطلب من حسابات <strong>المدير العام والأدمن</strong> فقط إدخال رمز يُرسل إلى البريد بعد كلمة المرور عند تسجيل الدخول. لا يؤثر على المدربين ولا الطلاب ولا الموظفين.</p>
            </div>
            <?php if($adminTwoFactorRequired): ?>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-700">
                    <i class="fas fa-shield-alt"></i> مفعّل
                </span>
            <?php else: ?>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                    غير مفعّل
                </span>
            <?php endif; ?>
        </div>
        <div class="p-6 space-y-5">
            <?php if($errors->has('two_factor')): ?>
                <div class="p-3 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-sm font-medium">
                    <?php echo e($errors->first('two_factor')); ?>

                </div>
            <?php endif; ?>
            <div class="rounded-xl bg-amber-50/80 dark:bg-amber-900/15 border border-amber-100 dark:border-amber-800/50 px-4 py-3 text-sm text-amber-900 dark:text-amber-100 leading-7">
                <i class="fas fa-exclamation-triangle ml-1"></i>
                تأكد أن إعدادات البريد في السيرفر تعمل قبل التفعيل. يمكنك أيضاً ضبط القيمة الافتراضية من ملف البيئة <code class="text-[11px] bg-white/80 dark:bg-slate-800 px-1 rounded" dir="ltr">ADMIN_2FA_REQUIRED</code> عند أول تشغيل قبل حفظ أي شيء من هنا.
            </div>
            <div class="rounded-xl bg-sky-50/80 dark:bg-sky-900/15 border border-sky-100 dark:border-sky-800/50 px-4 py-3 text-sm text-sky-900 dark:text-sky-100 leading-7">
                <i class="fas fa-info-circle ml-1"></i>
                لا يُفعّل الإلزام على الخادم إلا بعد الضغط على الزر أدناه، ثم إدخال الرمز في صفحة التأكيد. إن ظهر «مفعّل» هنا فقط بعد ذلك، سيُطلب رمز البريد عند الدخول.
            </div>
            <?php if(!$admin2faAppliesToCurrentUserRole): ?>
            <div class="rounded-xl bg-violet-50/90 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-800 px-4 py-3 text-sm text-violet-900 dark:text-violet-100 leading-7">
                <i class="fas fa-user-shield ml-1"></i>
                دور حسابك الحالي (<strong class="font-black"><?php echo e(auth()->user()->role); ?></strong>) ليس من ضمن «المدير العام والأدمن» في النظام؛ حتى مع تفعيل الإلزام لن يُطلب منك رمز بريد عند تسجيل الدخول. الإلزام ينطبق فقط على المستخدمين ذوي الدور <code class="text-[11px] bg-white/80 dark:bg-slate-800 px-1 rounded" dir="ltr">super_admin</code> أو <code class="text-[11px] bg-white/80 dark:bg-slate-800 px-1 rounded" dir="ltr">admin</code>.
            </div>
            <?php endif; ?>
            <?php if(!$adminTwoFactorRequired): ?>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-7">
                    اضغط الزر أدناه لإرسال رمز تحقق إلى بريدك، ثم ستُفتح صفحة لإدخال الرمز وتأكيد التفعيل.
                </p>
                <form method="post" action="<?php echo e(route('admin.system-settings.two-factor.enable-request')); ?>" class="inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-sm font-black shadow-lg shadow-violet-500/25 hover:from-violet-700 hover:to-indigo-700 transition-colors">
                        <i class="fas fa-paper-plane"></i>
                        تفعيل إلزام المصادقة الثنائية (إرسال الرمز بالبريد)
                    </button>
                </form>
            <?php else: ?>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-7">
                    الإلزام مفعّل حالياً. لتعطيله على مستوى المنصة، أدخل كلمة مرور حسابك للتأكيد.
                </p>
                <form method="post" action="<?php echo e(route('admin.system-settings.two-factor.disable')); ?>" class="max-w-md space-y-4">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">كلمة المرور</label>
                        <input type="password" name="password" required autocomplete="current-password"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-sm">
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-xs text-rose-600 dark:text-rose-400 mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border-2 border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-sm font-black hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors">
                        <i class="fas fa-power-off"></i>
                        تعطيل إلزام المصادقة الثنائية
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\system-settings\edit.blade.php ENDPATH**/ ?>