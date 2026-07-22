<?php $__env->startSection('title', 'إعدادات النظام'); ?>
<?php $__env->startSection('page_title', 'إعدادات النظام'); ?>
<?php $__env->startSection('header', 'إعدادات النظام'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $input = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $label = 'mb-1.5 block text-xs font-medium text-muted';
    $sectionHead = 'flex flex-wrap items-center gap-3 border-b border-line bg-canvas/60 px-5 py-4';
    $sectionBody = 'space-y-5 p-5 sm:p-6';
    $panel = 'overflow-hidden rounded-2xl border border-line bg-surface shadow-soft';
?>

<div class="space-y-5 pb-8">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0 max-w-3xl">
            <p class="text-xs font-medium text-muted">إعدادات المنصة</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink">مركز إعدادات النظام</h2>
            <p class="mt-2 text-sm leading-7 text-muted">
                ضبط الفوتر والسوشيال، شعار اللوحة، بوابة الدفع، و<strong class="font-medium text-ink">المصادقة الثنائية لحسابات الأدمن</strong>.
                اترك أي حقل فوتر فارغاً واحفظ لاستعادة الافتراضي لهذا الحقل.
            </p>
        </div>
    </section>

    <?php if(session('success')): ?>
        <div class="flex items-center gap-3 rounded-2xl border border-success/20 bg-[#eef8f2] px-4 py-3 text-sm font-medium text-success shadow-soft" role="status">
            <i class="fas fa-check-circle"></i>
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('info')): ?>
        <div class="flex items-center gap-3 rounded-2xl border border-accent/20 bg-accent-soft px-4 py-3 text-sm font-medium text-accent shadow-soft" role="status">
            <i class="fas fa-info-circle"></i>
            <?php echo e(session('info')); ?>

        </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="rounded-2xl border border-danger/20 bg-[#fdf2f1] p-4 text-sm text-danger shadow-soft">
            <p class="mb-2 font-semibold">يرجى تصحيح ما يلي:</p>
            <ul class="list-inside list-disc space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($err); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo e(route('admin.system-settings.update')); ?>" enctype="multipart/form-data" class="space-y-5">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        
        <article class="<?php echo e($panel); ?>">
            <div class="<?php echo e($sectionHead); ?>">
                <span class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-sm font-semibold text-accent">1</span>
                <div class="min-w-0">
                    <h3 class="text-base font-semibold text-ink">شعار لوحة التحكم وأيقونة الموقع</h3>
                    <p class="mt-0.5 text-xs leading-6 text-muted">يظهر في الشريط الجانبي، النافبار العام، الشريط العلوي للأدمن، وكـ favicon.</p>
                </div>
            </div>
            <div class="<?php echo e($sectionBody); ?>">
                <div class="rounded-xl border border-line bg-canvas px-4 py-3 text-xs leading-6 text-muted">
                    <strong class="text-ink">محلياً:</strong> نفّذ <code class="rounded bg-surface px-1 text-[10px]" dir="ltr">php artisan storage:link</code>
                    وتأكد أن <code class="rounded bg-surface px-1 text-[10px]" dir="ltr">APP_URL</code> يطابق المتصفح.
                    <br>
                    <strong class="text-ink">Cloudflare R2:</strong>
                    <code class="rounded bg-surface px-1 text-[10px]" dir="ltr">ADMIN_BRANDING_DISK=r2</code>
                    مع مفاتيح AWS ثم <code class="rounded bg-surface px-1 text-[10px]" dir="ltr">php artisan config:clear</code>.
                </div>
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                    <div class="shrink-0">
                        <?php if($adminPanelLogoUrl): ?>
                            <div class="flex size-24 items-center justify-center rounded-2xl border border-dashed border-line bg-canvas p-2">
                                <img src="<?php echo e($adminPanelLogoUrl); ?>" alt="" class="max-h-full max-w-full object-contain">
                            </div>
                        <?php else: ?>
                            <div class="flex size-24 items-center justify-center rounded-2xl bg-accent text-3xl font-semibold text-white">G</div>
                        <?php endif; ?>
                    </div>
                    <div class="min-w-0 flex-1 space-y-3">
                        <label class="<?php echo e($label); ?>">رفع شعار جديد</label>
                        <input type="file" name="admin_panel_logo" accept="image/jpeg,image/png,image/webp,image/gif"
                               class="block w-full text-sm text-muted file:me-4 file:rounded-xl file:border-0 file:bg-accent-soft file:px-4 file:py-2 file:text-sm file:font-medium file:text-accent hover:file:bg-[#d9ebe9]">
                        <p class="text-xs text-muted">JPG, PNG, WebP, GIF — حتى 2 ميغابايت. يُفضّل مربع بخلفية شفافة أو فاتحة.</p>
                        <?php if($adminPanelLogoUrl): ?>
                            <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-danger">
                                <input type="checkbox" name="remove_admin_panel_logo" value="1" class="rounded border-line text-danger focus:ring-accent" <?php if(old('remove_admin_panel_logo')): echo 'checked'; endif; ?>>
                                <span>حذف الشعار الحالي والعودة للحرف الافتراضي</span>
                            </label>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </article>

        
        <article class="<?php echo e($panel); ?>">
            <div class="<?php echo e($sectionHead); ?>">
                <span class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-metal"><i class="fas fa-cloud text-sm"></i></span>
                <div class="min-w-0">
                    <h3 class="text-base font-semibold text-ink">رابط عرض الصور (Cloudflare R2)</h3>
                    <p class="mt-0.5 text-xs leading-6 text-muted">للشعار، السلايدر، صور الكورسات، وكل ملفات المنصة.</p>
                </div>
            </div>
            <div class="<?php echo e($sectionBody); ?>">
                <div>
                    <label class="<?php echo e($label); ?>" for="r2_public_url">R2 Public URL</label>
                    <input type="url" id="r2_public_url" name="r2_public_url" value="<?php echo e(old('r2_public_url', $r2PublicUrl)); ?>"
                           class="<?php echo e($input); ?> font-mono" dir="ltr" placeholder="https://pub-xxxxxxxx.r2.dev">
                    <p class="mt-2 text-xs leading-6 text-muted">
                        من Cloudflare: R2 → Bucket → Public access → انسخ رابط <code class="rounded bg-canvas px-1 text-[10px]" dir="ltr">r2.dev</code>.
                        لا تستخدم <code class="rounded bg-canvas px-1 text-[10px]" dir="ltr">cloudflarestorage.com</code>.
                    </p>
                    <?php $__errorArgs = ['r2_public_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1.5 text-xs font-medium text-danger"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
        </article>

        
        <article class="<?php echo e($panel); ?>">
            <div class="<?php echo e($sectionHead); ?>">
                <span class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-sm font-semibold text-accent">2</span>
                <div class="min-w-0">
                    <h3 class="text-base font-semibold text-ink">فوتر الموقع العام</h3>
                    <p class="mt-0.5 text-xs leading-6 text-muted">يُعرض في الصفحة الرئيسية والصفحات العامة.</p>
                </div>
            </div>
            <div class="<?php echo e($sectionBody); ?> space-y-8">
                <section class="space-y-4">
                    <h4 class="border-b border-line pb-2 text-sm font-semibold text-ink">الهوية والنص التعريفي</h4>
                    <div>
                        <label class="<?php echo e($label); ?>">السطر تحت اسم Glottical</label>
                        <input type="text" name="footer_brand_tagline" value="<?php echo e(old('footer_brand_tagline', $values['footer_brand_tagline'])); ?>"
                               class="<?php echo e($input); ?>" placeholder="<?php echo e($defaults['footer_brand_tagline']); ?>">
                    </div>
                    <div>
                        <label class="<?php echo e($label); ?>">فقرة تعريفية قصيرة</label>
                        <textarea name="footer_blurb" rows="3" class="min-h-[96px] w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20"
                                  placeholder="<?php echo e($defaults['footer_blurb']); ?>"><?php echo e(old('footer_blurb', $values['footer_blurb'])); ?></textarea>
                    </div>
                    <div>
                        <label class="<?php echo e($label); ?>">السطر بجانب حقوق النشر</label>
                        <input type="text" name="footer_bottom_tagline" value="<?php echo e(old('footer_bottom_tagline', $values['footer_bottom_tagline'])); ?>"
                               class="<?php echo e($input); ?>" placeholder="<?php echo e($defaults['footer_bottom_tagline']); ?>">
                    </div>
                </section>

                <section class="space-y-4">
                    <h4 class="border-b border-line pb-2 text-sm font-semibold text-ink">التواصل</h4>
                    <div>
                        <label class="<?php echo e($label); ?>">البريد الإلكتروني</label>
                        <input type="email" name="footer_email" value="<?php echo e(old('footer_email', $values['footer_email'])); ?>"
                               class="<?php echo e($input); ?>" dir="ltr" placeholder="<?php echo e($defaults['footer_email']); ?>">
                    </div>
                    <div>
                        <label class="<?php echo e($label); ?>">رقم الهاتف</label>
                        <input type="text" name="footer_phone" value="<?php echo e(old('footer_phone', $values['footer_phone'])); ?>"
                               class="<?php echo e($input); ?>" dir="ltr" placeholder="<?php echo e($defaults['footer_phone']); ?>">
                    </div>
                    <div>
                        <label class="<?php echo e($label); ?>">رابط واتساب</label>
                        <input type="url" name="footer_whatsapp_url" value="<?php echo e(old('footer_whatsapp_url', $values['footer_whatsapp_url'])); ?>"
                               class="<?php echo e($input); ?>" dir="ltr" placeholder="<?php echo e($defaults['footer_whatsapp_url']); ?>">
                    </div>
                </section>

                <section class="space-y-4">
                    <h4 class="border-b border-line pb-2 text-sm font-semibold text-ink">وسائل التواصل الاجتماعي</h4>
                    <p class="text-xs text-muted">تظهر الأيقونة في الفوتر فقط عند ملء الرابط.</p>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <?php $__currentLoopData = [
                            'social_facebook_url' => 'Facebook',
                            'social_x_url' => 'X (Twitter)',
                            'social_instagram_url' => 'Instagram',
                            'social_youtube_url' => 'YouTube',
                            'social_linkedin_url' => 'LinkedIn',
                            'social_tiktok_url' => 'TikTok',
                            'social_telegram_url' => 'Telegram',
                            'social_snapchat_url' => 'Snapchat',
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $socialLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div>
                                <label class="<?php echo e($label); ?>"><?php echo e($socialLabel); ?></label>
                                <input type="url" name="<?php echo e($field); ?>" value="<?php echo e(old($field, $values[$field])); ?>"
                                       class="<?php echo e($input); ?>" dir="ltr" placeholder="https://">
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </section>
            </div>
        </article>

        
        <article class="<?php echo e($panel); ?>">
            <div class="<?php echo e($sectionHead); ?>">
                <span class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent"><i class="fas fa-credit-card text-sm"></i></span>
                <div class="min-w-0 flex-1">
                    <h3 class="text-base font-semibold text-ink">بوابة الدفع — فواتيرك (IFrame)</h3>
                    <p class="mt-0.5 text-xs leading-6 text-muted">عند التفعيل تُعرض بوابة الدفع فقط دون رفع إيصال يدوي.</p>
                </div>
                <?php if($fawaterakGatewayEnabled): ?>
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-semibold text-accent">مفعّل</span>
                <?php else: ?>
                    <span class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-canvas px-2.5 py-1 text-xs font-medium text-muted">معطّل</span>
                <?php endif; ?>
            </div>
            <div class="<?php echo e($sectionBody); ?>">
                <input type="hidden" name="fawaterak_gateway_enabled" value="0">
                <label class="flex cursor-pointer items-start gap-3">
                    <input type="checkbox" name="fawaterak_gateway_enabled" value="1" class="mt-1 rounded border-line text-accent focus:ring-accent"
                           <?php if((string) old('fawaterak_gateway_enabled', $fawaterakGatewayEnabled ? '1' : '0') === '1'): echo 'checked'; endif; ?>>
                    <span class="text-sm leading-7 text-muted">
                        <span class="mb-0.5 block font-semibold text-ink">تفعيل الدفع عبر فواتيرك</span>
                        يظهر إطار الدفع الرسمي على صفحة إتمام طلب الكورس، ويُعطّل التحويل اليدوي ورفع الإيصال.
                    </span>
                </label>

                <div class="rounded-xl border px-4 py-3 text-xs leading-7 sm:text-sm <?php echo e($fawaterakEnvConfigured ? 'border-success/20 bg-[#eef8f2] text-success' : 'border-metal/30 bg-[#fbf7ef] text-ink'); ?>">
                    <?php if($fawaterakEnvConfigured): ?>
                        <i class="fas fa-check-circle ms-1"></i>
                        مفاتيح API مضبوطة في البيئة (<code class="rounded bg-surface px-1 text-[11px]" dir="ltr">FAWATERAK_VENDOR_KEY</code> /
                        <code class="rounded bg-surface px-1 text-[11px]" dir="ltr">FAWATERAK_PROVIDER_KEY</code>).
                    <?php else: ?>
                        <i class="fas fa-exclamation-triangle ms-1 text-metal"></i>
                        أضف المفاتيح في <code class="rounded bg-surface px-1 text-[11px]" dir="ltr">.env</code> ثم
                        <code class="rounded bg-surface px-1 text-[11px]" dir="ltr">php artisan config:clear</code>.
                    <?php endif; ?>
                </div>

                <p class="text-xs leading-6 text-muted">
                    في لوحة فواتيرك: <strong class="text-ink">Integrations → Fawaterak</strong> — سجّل نطاقات الـ IFrame بصيغة HTTPS بدون شرطة مائلة في النهاية.
                </p>

                <div class="border-t border-line pt-4">
                    <label for="payment_gateway_fee_percent" class="mb-1.5 block text-sm font-semibold text-ink">عمولة بوابة الدفع (تقديرية %)</label>
                    <p class="mb-2 text-xs leading-6 text-muted">نسبة من مبلغ العميل تُسجَّل كعمولة في المحاسبة. اتركها فارغة أو 0 لإيقافها.</p>
                    <input type="text" name="payment_gateway_fee_percent" id="payment_gateway_fee_percent" inputmode="decimal"
                           value="<?php echo e(old('payment_gateway_fee_percent', $paymentGatewayFeePercent)); ?>"
                           class="<?php echo e($input); ?> max-w-xs font-mono" dir="ltr" placeholder="مثال: 2.5">
                    <?php $__errorArgs = ['payment_gateway_fee_percent'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1.5 text-sm text-danger"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
        </article>

        <div class="sticky bottom-4 z-10 flex flex-wrap items-center gap-3">
            <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-6 text-sm font-medium text-white shadow-soft transition hover:bg-[#0d4f4a]">
                <i class="fas fa-save"></i>
                حفظ كل الإعدادات
            </button>
            <a href="<?php echo e(route('home')); ?>" target="_blank" rel="noopener" class="inline-flex h-11 items-center gap-2 rounded-xl border border-line bg-surface px-5 text-sm font-medium text-ink-soft transition hover:border-accent/30 hover:bg-accent-soft hover:text-accent">
                <i class="fas fa-external-link-alt"></i>
                معاينة الموقع العام
            </a>
        </div>
    </form>

    
    <article class="<?php echo e($panel); ?>">
        <div class="<?php echo e($sectionHead); ?>">
            <span class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-sm font-semibold text-accent">3</span>
            <div class="min-w-0 flex-1">
                <h3 class="text-base font-semibold text-ink">المصادقة الثنائية للمنصة</h3>
                <p class="mt-0.5 text-xs leading-6 text-muted">إلزام رمز البريد بعد كلمة المرور لحسابات المدير العام والأدمن فقط.</p>
            </div>
            <?php if($adminTwoFactorRequired): ?>
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-semibold text-accent">
                    <i class="fas fa-shield-alt"></i> مفعّل
                </span>
            <?php else: ?>
                <span class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-canvas px-2.5 py-1 text-xs font-medium text-muted">غير مفعّل</span>
            <?php endif; ?>
        </div>
        <div class="<?php echo e($sectionBody); ?>">
            <?php if($errors->has('two_factor')): ?>
                <div class="rounded-xl border border-danger/20 bg-[#fdf2f1] px-4 py-3 text-sm font-medium text-danger">
                    <?php echo e($errors->first('two_factor')); ?>

                </div>
            <?php endif; ?>

            <div class="rounded-xl border border-metal/25 bg-[#fbf7ef] px-4 py-3 text-sm leading-7 text-ink">
                <i class="fas fa-exclamation-triangle ms-1 text-metal"></i>
                تأكد أن إعدادات البريد تعمل قبل التفعيل. يمكن ضبط
                <code class="rounded bg-surface px-1 text-[11px]" dir="ltr">ADMIN_2FA_REQUIRED</code>
                في البيئة عند أول تشغيل.
            </div>

            <div class="rounded-xl border border-accent/20 bg-accent-soft px-4 py-3 text-sm leading-7 text-accent">
                <i class="fas fa-info-circle ms-1"></i>
                لا يُفعّل الإلزام إلا بعد إرسال الرمز ثم تأكيده في الصفحة التالية.
            </div>

            <?php if(!$admin2faAppliesToCurrentUserRole): ?>
                <div class="rounded-xl border border-line bg-canvas px-4 py-3 text-sm leading-7 text-muted">
                    <i class="fas fa-user-shield ms-1 text-accent"></i>
                    دور حسابك الحالي (<strong class="text-ink"><?php echo e(auth()->user()->role); ?></strong>) ليس ضمن الأدوار الملزَمة.
                    الإلزام ينطبق على <code class="rounded bg-surface px-1 text-[11px]" dir="ltr">super_admin</code> و
                    <code class="rounded bg-surface px-1 text-[11px]" dir="ltr">admin</code> فقط.
                </div>
            <?php endif; ?>

            <?php if(!$adminTwoFactorRequired): ?>
                <p class="text-sm leading-7 text-muted">اضغط الزر لإرسال رمز تحقق إلى بريدك، ثم أكّد التفعيل في الصفحة التالية.</p>
                <form method="post" action="<?php echo e(route('admin.system-settings.two-factor.enable-request')); ?>" class="inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white transition hover:bg-[#0d4f4a]">
                        <i class="fas fa-paper-plane"></i>
                        تفعيل إلزام المصادقة الثنائية
                    </button>
                </form>
            <?php else: ?>
                <p class="text-sm leading-7 text-muted">الإلزام مفعّل. لتعطيله أدخل كلمة مرور حسابك.</p>
                <form method="post" action="<?php echo e(route('admin.system-settings.two-factor.disable')); ?>" class="max-w-md space-y-4">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label class="<?php echo e($label); ?>">كلمة المرور</label>
                        <input type="password" name="password" required autocomplete="current-password" class="<?php echo e($input); ?>">
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1.5 text-xs text-danger"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <button type="submit" class="inline-flex h-11 items-center gap-2 rounded-xl border border-danger/30 bg-[#fdf2f1] px-5 text-sm font-medium text-danger transition hover:bg-[#fbe8e6]">
                        <i class="fas fa-power-off"></i>
                        تعطيل إلزام المصادقة الثنائية
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </article>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views/admin/system-settings/edit.blade.php ENDPATH**/ ?>