<?php $__env->startSection('title', __('public.contact_page_title') . ' - ' . __('public.site_suffix')); ?>
<?php $__env->startSection('meta_description', 'تواصل مع فريق ' . config('app.name') . ' — استفسارات، دعم فني، واقتراحات. نرد في أقرب وقت.'); ?>
<?php $__env->startSection('meta_keywords', 'تواصل, دعم, ' . config('app.name') . ', مساعدة'); ?>
<?php $__env->startSection('canonical_url', url('/contact')); ?>

<?php
    $appName = config('app.name');
?>

<?php $__env->startSection('content'); ?>

<section class="pt-8 sm:pt-12 lg:pt-14 pb-12 sm:pb-16 overflow-hidden relative" style="background:radial-gradient(circle at 12% 80%,rgba(255,229,247,.65),transparent 28%),radial-gradient(circle at 88% 20%,rgba(40,53,147,.10),transparent 30%),linear-gradient(180deg,#f4f6ff 0%,#fbfbff 55%,#ffffff 100%)">
    <div class="absolute inset-0 pointer-events-none opacity-40" style="background-image:radial-gradient(circle at 1px 1px,rgba(40,53,147,.08) 1px,transparent 0);background-size:30px 30px"></div>

    <div class="max-w-[1200px] mx-auto px-6 sm:px-8 relative z-10">
        <div class="max-w-3xl mx-auto text-center mb-10 sm:mb-12">
            <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs sm:text-sm font-bold mb-5" style="background:#FFE5F7;color:#283593;border:1px solid #f5c7e8">
                <i class="fas fa-envelope-open-text"></i> <?php echo e(__('public.contact_page_title')); ?>

            </span>
            <h1 class="text-[1.75rem] sm:text-[2.35rem] lg:text-[2.85rem] leading-[1.2] font-black mb-4" style="color:#1F2A7A;font-family:Tajawal,Cairo,sans-serif">
                نحن بجانبك
                <span class="block mt-1" style="color:#FB5607">في أي استفسار أو دعم</span>
            </h1>
            <p class="text-slate-600 dark:text-slate-400 text-base sm:text-lg leading-8 max-w-2xl mx-auto">
                املأ النموذج وسيتواصل فريق <?php echo e($appName); ?> معك قريباً، أو استخدم البريد والهاتف أدناه للوصول المباشر.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
            <div class="lg:col-span-7">
                <div class="rounded-[24px] border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 shadow-[0_20px_44px_-26px_rgba(31,42,122,.28)] p-6 sm:p-8">
                    <?php if(session('success')): ?>
                        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 dark:bg-emerald-950/40 dark:border-emerald-800 px-4 py-3 text-emerald-800 dark:text-emerald-200 text-sm font-medium flex items-start gap-3">
                            <i class="fas fa-check-circle mt-0.5 flex-shrink-0"></i>
                            <span><?php echo e(session('success')); ?></span>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?php echo e(route('public.contact.store')); ?>" class="space-y-5">
                        <?php echo csrf_field(); ?>
                        <div>
                            <label for="name" class="block text-sm font-bold text-[#1F2A7A] dark:text-slate-200 mb-2">الاسم الكامل</label>
                            <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" required maxlength="255"
                                class="w-full rounded-2xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-3 text-slate-900 dark:text-slate-100 focus:border-[#283593] focus:ring-2 focus:ring-[#283593]/20 outline-none transition-colors"
                                placeholder="اسمك">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1.5 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="grid sm:grid-cols-2 gap-5">
                            <div>
                                <label for="email" class="block text-sm font-bold text-[#1F2A7A] dark:text-slate-200 mb-2">البريد الإلكتروني</label>
                                <input type="email" name="email" id="email" value="<?php echo e(old('email')); ?>" required maxlength="255"
                                    class="w-full rounded-2xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-3 text-slate-900 dark:text-slate-100 focus:border-[#283593] focus:ring-2 focus:ring-[#283593]/20 outline-none transition-colors"
                                    placeholder="you@example.com">
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-bold text-[#1F2A7A] dark:text-slate-200 mb-2">رقم الجوال <span class="text-slate-400 font-normal">(اختياري)</span></label>
                                <input type="text" name="phone" id="phone" value="<?php echo e(old('phone')); ?>" maxlength="20"
                                    class="w-full rounded-2xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-3 text-slate-900 dark:text-slate-100 focus:border-[#283593] focus:ring-2 focus:ring-[#283593]/20 outline-none transition-colors"
                                    placeholder="05xxxxxxxx">
                                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div>
                            <label for="subject" class="block text-sm font-bold text-[#1F2A7A] dark:text-slate-200 mb-2">الموضوع</label>
                            <input type="text" name="subject" id="subject" value="<?php echo e(old('subject')); ?>" required maxlength="255"
                                class="w-full rounded-2xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-3 text-slate-900 dark:text-slate-100 focus:border-[#283593] focus:ring-2 focus:ring-[#283593]/20 outline-none transition-colors"
                                placeholder="موجز لطلبك">
                            <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1.5 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-bold text-[#1F2A7A] dark:text-slate-200 mb-2">الرسالة</label>
                            <textarea name="message" id="message" rows="5" required maxlength="5000"
                                class="w-full rounded-2xl border-2 border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 px-4 py-3 text-slate-900 dark:text-slate-100 focus:border-[#283593] focus:ring-2 focus:ring-[#283593]/20 outline-none transition-colors resize-y min-h-[140px]"
                                placeholder="اكتب تفاصيل رسالتك..."><?php echo e(old('message')); ?></textarea>
                            <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1.5 text-sm text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-2xl font-bold text-white px-8 py-3.5 bg-[#FB5607] hover:bg-[#e84d00] shadow-[0_12px_28px_-10px_rgba(251,86,7,.45)] hover:shadow-[0_16px_32px_-10px_rgba(251,86,7,.5)] transition-all">
                            <i class="fas fa-paper-plane"></i>
                            إرسال الرسالة
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-5 space-y-6">
                <div class="rounded-[24px] border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 shadow-[0_20px_44px_-26px_rgba(31,42,122,.28)] p-6 sm:p-7">
                    <h2 class="text-lg font-black mb-4 flex items-center gap-2" style="color:#1F2A7A;font-family:Tajawal,Cairo,sans-serif">
                        <span class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:#FFE5F7;color:#283593"><i class="fas fa-info-circle"></i></span>
                        معلومات التواصل
                    </h2>
                    <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed mb-5">
                        منصة <strong class="text-[#1F2A7A] dark:text-slate-100"><?php echo e($appName); ?></strong> — يمكنك مراسلتنا أو الاتصال بنا عبر القنوات التالية.
                    </p>
                    <?php if($supportEmail !== '' || $supportPhone !== ''): ?>
                    <ul class="space-y-3">
                        <?php if($supportEmail !== ''): ?>
                        <li>
                            <a href="mailto:<?php echo e($supportEmail); ?>" class="flex items-start gap-3 rounded-2xl p-4 border border-slate-200 dark:border-slate-600 hover:border-[#283593]/40 dark:hover:border-slate-500 transition-colors no-underline text-inherit bg-slate-50/80 dark:bg-slate-900/40">
                                <span class="w-11 h-11 rounded-xl bg-[#283593] text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-envelope"></i></span>
                                <div>
                                    <span class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">البريد</span>
                                    <span class="font-semibold text-slate-800 dark:text-slate-100 break-all"><?php echo e($supportEmail); ?></span>
                                </div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($supportPhone !== ''): ?>
                        <li>
                            <a href="tel:<?php echo e(preg_replace('/\s+/', '', $supportPhone)); ?>" class="flex items-start gap-3 rounded-2xl p-4 border border-slate-200 dark:border-slate-600 hover:border-[#283593]/40 dark:hover:border-slate-500 transition-colors no-underline text-inherit bg-slate-50/80 dark:bg-slate-900/40">
                                <span class="w-11 h-11 rounded-xl bg-[#FB5607] text-white flex items-center justify-center flex-shrink-0"><i class="fas fa-phone-alt"></i></span>
                                <div>
                                    <span class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">الهاتف</span>
                                    <span class="font-semibold text-slate-800 dark:text-slate-100 dir-ltr text-right block"><?php echo e($supportPhone); ?></span>
                                </div>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                    <?php else: ?>
                    <p class="text-sm text-slate-500 dark:text-slate-400 rounded-2xl border border-dashed border-slate-200 dark:border-slate-600 px-4 py-3">
                        <?php echo e(__('public.contact_channels_empty_hint')); ?>

                    </p>
                    <?php endif; ?>
                </div>

                <div class="rounded-[24px] border border-slate-200 dark:border-slate-600 bg-gradient-to-br from-white to-[#fff7f0] dark:from-slate-800 dark:to-slate-800 p-6 shadow-[0_16px_40px_-24px_rgba(31,42,122,.22)]">
                    <h3 class="font-bold text-[#1F2A7A] dark:text-white mb-2 flex items-center gap-2" style="font-family:Tajawal,Cairo,sans-serif">
                        <i class="fas fa-question-circle" style="color:#FB5607"></i>
                        أسئلة سريعة؟
                    </h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm mb-4">قد تجد إجابتك فوراً في مركز المساعدة أو الأسئلة الشائعة.</p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="<?php echo e(route('public.faq')); ?>" class="inline-flex items-center justify-center gap-2 rounded-2xl border-2 font-semibold px-4 py-2.5 text-sm transition-colors text-[#283593] border-[#283593] hover:bg-[#283593] hover:text-white">
                            <i class="fas fa-comments"></i>
                            الأسئلة الشائعة
                        </a>
                        <a href="<?php echo e(route('public.help')); ?>" class="inline-flex items-center justify-center gap-2 rounded-2xl border-2 border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 font-semibold px-4 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors text-sm">
                            <i class="fas fa-book-open"></i>
                            مركز المساعدة
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-10 sm:py-12 border-t border-slate-200/80 dark:border-slate-700" style="background:linear-gradient(180deg,#f4f7ff 0%,#ffffff 100%)">
    <div class="max-w-[1200px] mx-auto px-6 sm:px-8 text-center">
        <p class="text-slate-600 dark:text-slate-400 text-sm sm:text-base max-w-xl mx-auto">
            تفضّل تصفح <a href="<?php echo e(route('public.faq')); ?>" class="font-bold text-[#283593] hover:text-[#FB5607] underline-offset-2">الأسئلة الشائعة</a>
            أو العودة إلى <a href="<?php echo e(route('home')); ?>" class="font-bold text-[#283593] hover:text-[#FB5607] underline-offset-2">الرئيسية</a>.
        </p>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\public\contact.blade.php ENDPATH**/ ?>