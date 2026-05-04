<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $wallets = $wallets ?? collect();
    $fawaterakUseGateway = !empty($fawaterakUseGateway);
    $fawaterakMisconfigured = !empty($fawaterakMisconfigured);
    $fawaterakIntegration = $fawaterakIntegration ?? 'iframe';
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <title>دفع اشتراك الباقة - <?php echo e($plan['label'] ?? 'الباقة'); ?> - <?php echo e(config('app.name')); ?></title>
    <meta name="theme-color" content="#283593">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    navy: { 950:'#020617' },
                    brand: { 50:'#FFF3E0',100:'#FFE0B2',200:'#FFCC80',300:'#FFB74D',400:'#FFA726',500:'#FB5607',600:'#E04D00',700:'#BF360C',800:'#8D2600',900:'#5D1A00' },
                    mx: { navy:'#283593', indigo:'#1F2A7A', orange:'#FB5607', rose:'#FFE5F7', gold:'#FFE569', soft:'#F7F8FF' }
                },
                fontFamily: {
                    heading: ['Cairo','Tajawal','IBM Plex Sans Arabic','sans-serif'],
                    body: ['Cairo','IBM Plex Sans Arabic','Tajawal','sans-serif'],
                }
            }
        }
    }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        *{font-family:'Cairo','IBM Plex Sans Arabic','Tajawal',system-ui,sans-serif}
        h1,h2,h3,h4,h5,h6,.font-heading{font-family:'Cairo','Tajawal','IBM Plex Sans Arabic',sans-serif}
        html{scroll-behavior:smooth;overflow-x:hidden}
        body{background:#fff;overflow-x:hidden}
        .container-1200{max-width:1200px;margin-inline:auto;padding-inline:24px}
        @media (max-width:768px){.container-1200{padding-inline:16px}}
        .card-hover{transition:all .4s cubic-bezier(.16,1,.3,1)}
        .card-hover:hover{transform:translateY(-6px);box-shadow:0 20px 40px -18px rgba(15,23,42,.35)}
        .btn-primary{position:relative;overflow:hidden;transition:all .3s cubic-bezier(.16,1,.3,1)}
        .btn-primary::before{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.25),transparent);transition:left .5s}
        .btn-primary:hover::before{left:100%}
        .btn-primary:hover{transform:translateY(-1px)}
        #navbar,#navbar.nav-transparent,#navbar.nav-solid{
            background:rgba(31,42,122,.92)!important;
            backdrop-filter:blur(12px)!important;
            -webkit-backdrop-filter:blur(12px)!important;
            border-bottom:1px solid rgba(255,255,255,.08)!important;
        }
    </style>
</head>
<body class="bg-white text-slate-900 antialiased">
    <?php echo $__env->make('components.unified-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>.navbar-spacer{display:block}</style>

    <main class="flex-1">
        
        <section class="pt-10 sm:pt-14 lg:pt-16 pb-10 sm:pb-12 overflow-hidden relative"
                 style="background:radial-gradient(circle at 12% 80%,rgba(255,229,247,.65),transparent 28%),radial-gradient(circle at 88% 20%,rgba(40,53,147,.10),transparent 30%),linear-gradient(180deg,#f4f6ff 0%,#fbfbff 55%,#ffffff 100%)">
            <div class="absolute inset-0 pointer-events-none opacity-40" style="background-image:radial-gradient(circle at 1px 1px,rgba(40,53,147,.08) 1px,transparent 0);background-size:30px 30px"></div>

            <div class="container-1200 relative z-10 text-center">
                <nav class="text-sm text-slate-500 mb-6 flex items-center justify-center gap-2 flex-wrap">
                    <a href="<?php echo e(url('/')); ?>" class="hover:text-mx-indigo transition-colors">الرئيسية</a>
                    <span>/</span>
                    <a href="<?php echo e(route('public.pricing')); ?>" class="hover:text-mx-indigo transition-colors">الأسعار والباقات</a>
                    <span>/</span>
                    <span class="text-mx-indigo font-semibold">دفع الاشتراك</span>
                </nav>
                <div class="inline-flex items-center gap-2.5 px-5 py-2 rounded-full text-sm font-medium mb-6" style="background:#FFE5F7;color:#283593;border:1px solid #f5c7e8">
                    <span class="w-2 h-2 rounded-full bg-[#FB5607] animate-pulse"></span>
                    <?php if($fawaterakUseGateway): ?>
                        الدفع الإلكتروني عبر بوابة فواتيرك
                    <?php else: ?>
                        تحويل مبلغ الاشتراك ثم رفع إيصال الدفع
                    <?php endif; ?>
                </div>
                <h1 class="font-heading text-3xl sm:text-4xl md:text-5xl font-black leading-tight text-mx-indigo mb-4">
                    دفع اشتراك الباقة
                    <br>
                    <span style="color:#FB5607">
                        <?php echo e($plan['label'] ?? 'باقة المعلم'); ?>

                    </span>
                </h1>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                    <?php if($fawaterakUseGateway): ?>
                        أتمم دفع <strong class="text-mx-indigo"><?php echo e(number_format($plan['price'] ?? 0, 0)); ?> ج.م</strong> عبر البوابة أدناه؛ يُفعَّل اشتراكك تلقائياً بعد نجاح العملية.
                    <?php else: ?>
                        قم بتحويل <strong class="text-mx-indigo"><?php echo e(number_format($plan['price'] ?? 0, 0)); ?> ج.م</strong> إلى أحد الحسابات أدناه، ثم ارفع صورة إيصال الدفع ليتم مراجعته وتفعيل اشتراكك.
                    <?php endif; ?>
                </p>
            </div>
        </section>

        
        <section class="py-16 md:py-20 bg-white">
            <div class="container-1200">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-3xl shadow-lg border border-slate-100 p-6 sticky top-24 card-hover">
                            <h3 class="font-heading text-xl font-black text-slate-900 mb-4 flex items-center gap-2">
                                <i class="fas fa-receipt text-[#FB5607]"></i>
                                ملخص الدفع
                            </h3>
                            <div class="rounded-2xl bg-amber-50 border border-amber-200 p-4 mb-4">
                                <p class="text-sm font-semibold text-amber-800 mb-1"><?php echo e($fawaterakUseGateway ? 'مبلغ الاشتراك' : 'مبلغ الاشتراك المطلوب تحويله'); ?></p>
                                <p class="text-3xl font-black text-amber-900">
                                    <?php echo e(number_format($plan['price'] ?? 0, 0)); ?>

                                    <span class="text-lg font-bold text-amber-700">ج.م</span>
                                </p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 mb-4">
                                <label class="block text-sm font-bold text-slate-700 mb-2">كوبون خصم الباقة (اختياري)</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" id="subscription_coupon_code" name="coupon_code" value="<?php echo e(old('coupon_code')); ?>"
                                           class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm uppercase font-mono"
                                           placeholder="مثال: PRO20" dir="ltr" autocomplete="off">
                                </div>
                                <p class="text-xs text-slate-500 mt-2">إذا كان الكوبون صالحاً سيتم تطبيقه عند إرسال الطلب أو بدء الدفع الإلكتروني.</p>
                            </div>
                            <p class="text-sm text-slate-600 mb-2"><strong><?php echo e($plan['label'] ?? 'الباقة'); ?></strong> · <?php echo e($billingLabel); ?></p>
                            <?php if(!empty($plan['features'])): ?>
                                <p class="text-xs font-semibold text-slate-500 mt-3 mb-2">ما ستحصل عليه:</p>
                                <ul class="space-y-1.5 text-xs text-slate-600">
                                    <?php $__currentLoopData = array_slice($plan['features'], 0, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="flex items-center gap-2"><i class="fas fa-check text-sky-500 text-[10px]"></i> <?php echo e(__("student.subscription_feature.{$f}") ?: $f); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(count($plan['features']) > 5): ?>
                                        <li class="text-slate-500">+ <?php echo e(count($plan['features']) - 5); ?> ميزة أخرى</li>
                                    <?php endif; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="lg:col-span-2 space-y-6">
                        <input type="hidden" id="sub_checkout_upgrade" value="<?php echo e(!empty($upgrade) ? '1' : '0'); ?>">
                        <input type="hidden" id="sub_checkout_from" value="<?php echo e($fromSubscriptionId ?? ''); ?>">

                        <?php if($fawaterakMisconfigured): ?>
                            <div class="rounded-3xl border border-rose-200 bg-rose-50 px-6 py-5 shadow-sm">
                                <p class="text-sm font-bold text-rose-900 mb-2 flex items-center gap-2">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    إعدادات الدفع الإلكتروني غير مكتملة
                                </p>
                                <p class="text-sm text-rose-800 leading-7">
                                    تم تفعيل فواتيرك من إعدادات النظام، لكن مفاتيح الربط غير مكتملة على الخادم.
                                    راجع صفحة إعدادات النظام أو ملف البيئة ثم نفّذ <code class="text-xs bg-white/80 px-1 rounded" dir="ltr">php artisan config:clear</code>.
                                    يمكنك أدناه متابعة التحويل اليدوي ورفع الإيصال.
                                </p>
                            </div>
                        <?php elseif($fawaterakUseGateway && $fawaterakIntegration === 'api'): ?>
                            <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden card-hover">
                                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                                    <h3 class="font-heading text-lg font-black text-slate-900 flex items-center gap-2">
                                        <i class="fas fa-lock text-sky-500"></i>
                                        الدفع الإلكتروني عبر فواتيرك (API)
                                    </h3>
                                    <p class="text-xs text-slate-600 mt-1">بعد اكتمال الدفع يُفعَّل اشتراكك تلقائياً دون انتظار مراجعة يدوية.</p>
                                </div>
                                <div class="p-6">
                                    <div id="sub-fawaterk-api-error" class="hidden mb-4 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 text-sm font-medium"></div>
                                    <div id="sub-fawaterk-api-loading" class="mb-4 flex items-center gap-3 text-slate-600 text-sm">
                                        <i class="fas fa-spinner fa-spin text-[#283593]"></i>
                                        جاري تحميل وسائل الدفع...
                                    </div>
                                    <div id="sub-fawaterk-api-methods" class="hidden mb-4 grid grid-cols-1 sm:grid-cols-2 gap-3"></div>
                                    <div id="sub-fawaterk-api-wallet-wrap" class="hidden mb-4">
                                        <label class="block text-sm font-bold text-slate-700 mb-2">رقم المحفظة (عند الحاجة)</label>
                                        <input type="text" id="sub-fawaterk-api-wallet" dir="ltr" class="w-full rounded-xl border border-slate-300 px-4 py-3" placeholder="01xxxxxxxxx" autocomplete="tel">
                                    </div>
                                    <div id="sub-fawaterk-api-result" class="hidden mb-4 rounded-2xl border border-slate-200 bg-slate-50 p-5 text-sm text-slate-800 space-y-2"></div>
                                    <button type="button" id="sub-fawaterk-api-pay-btn" disabled
                                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-[#FB5607] hover:bg-[#e84d00] text-white font-bold shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                                        متابعة الدفع
                                    </button>
                                </div>
                            </div>
                        <?php elseif($fawaterakUseGateway): ?>
                            <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden card-hover">
                                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                                    <h3 class="font-heading text-lg font-black text-slate-900 flex items-center gap-2">
                                        <i class="fas fa-lock text-sky-500"></i>
                                        الدفع الإلكتروني عبر فواتيرك
                                    </h3>
                                    <p class="text-xs text-slate-600 mt-1">اختر وسيلة الدفع في الإطار أدناه؛ عند النجاح يُفعَّل اشتراكك مباشرة.</p>
                                </div>
                                <div class="p-6">
                                    <div id="sub-fawaterk-checkout-error" class="hidden mb-4 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 text-sm font-medium"></div>
                                    <div id="fawaterkDivId" class="min-h-[520px] w-full rounded-2xl border border-slate-200 bg-white shadow-inner overflow-hidden ring-1 ring-slate-200/60"></div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (! ($fawaterakUseGateway)): ?>
                        <?php if($wallets->count() > 0): ?>
                            <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden card-hover">
                                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                                    <h3 class="font-heading text-lg font-black text-slate-900 flex items-center gap-2">
                                        <i class="fas fa-university text-sky-500"></i>
                                        قم بتحويل المبلغ إلى أحد الحسابات التالية
                                    </h3>
                                    <p class="text-xs text-slate-600 mt-1">اختر المحفظة أو الحساب الذي ستُجري التحويل إليه ثم ارفع إيصال الدفع أدناه.</p>
                                </div>
                                <div class="p-6 space-y-4">
                                    <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                                            <div class="flex items-center gap-3 mb-2">
                                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-[#FFE5F7] text-[#283593]">
                                                    <i class="fas fa-<?php echo e($w->type === 'bank_transfer' ? 'university' : 'wallet'); ?>"></i>
                                                </span>
                                                <span class="font-bold text-slate-900"><?php echo e($w->name ?? \App\Models\Wallet::typeLabel($w->type)); ?></span>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-slate-700">
                                                <?php if($w->account_number): ?>
                                                    <p><span class="text-slate-500">رقم الحساب:</span> <strong class="font-mono"><?php echo e($w->account_number); ?></strong></p>
                                                <?php endif; ?>
                                                <?php if($w->bank_name): ?>
                                                    <p><span class="text-slate-500">البنك:</span> <?php echo e($w->bank_name); ?></p>
                                                <?php endif; ?>
                                                <?php if($w->account_holder): ?>
                                                    <p><span class="text-slate-500">صاحب الحساب:</span> <?php echo e($w->account_holder); ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <?php if($w->notes): ?>
                                                <p class="text-xs text-slate-500 mt-2 pt-2 border-t border-slate-200"><?php echo e($w->notes); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        
                        <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden card-hover">
                            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                                    <h3 class="font-heading text-lg font-black text-slate-900 flex items-center gap-2">
                                        <i class="fas fa-file-invoice text-[#FB5607]"></i>
                                    بعد التحويل ارفع إيصال الدفع
                                </h3>
                                <p class="text-xs text-slate-600 mt-1">سيظهر طلبك في لوحة الإدارة لمراجعة الدفع وتفعيل الاشتراك.</p>
                            </div>
                            <div class="p-6">
                                <?php if(session('error')): ?>
                                    <div class="mb-4 p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-sm">
                                        <?php echo e(session('error')); ?>

                                    </div>
                                <?php endif; ?>
                                <?php if($errors->any()): ?>
                                    <div class="mb-4 p-4 bg-rose-50 border border-rose-200 rounded-xl">
                                        <ul class="list-disc list-inside text-rose-700 text-sm space-y-1">
                                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($err); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                                <form action="<?php echo e(route('public.subscription.checkout.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-5">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="plan" value="<?php echo e($planKey); ?>">
                                    <input type="hidden" name="coupon_code" id="subscription_coupon_code_hidden" value="<?php echo e(old('coupon_code')); ?>">

                                    <?php $__errorArgs = ['coupon_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">طريقة الدفع <span class="text-rose-500">*</span></label>
                                        <select name="payment_method" id="payment_method" required class="w-full rounded-xl border border-slate-300 px-4 py-3 text-slate-900 focus:ring-2 focus:ring-[#283593] focus:border-[#283593]">
                                            <option value="bank_transfer" <?php echo e(old('payment_method', $wallets->count() > 0 ? '' : 'bank_transfer') === 'bank_transfer' ? 'selected' : ''); ?>>تحويل بنكي</option>
                                            <?php if($wallets->count() > 0): ?>
                                            <option value="wallet" <?php echo e(old('payment_method') === 'wallet' ? 'selected' : ''); ?>>محفظة إلكترونية</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <?php if($wallets->count() > 0): ?>
                                        <div id="wallet_id_wrap" class="<?php echo e(old('payment_method') === 'wallet' ? '' : 'hidden'); ?>">
                                            <label class="block text-sm font-bold text-slate-700 mb-2">المحفظة / الحساب الذي تم التحويل إليه <span class="text-rose-500">*</span></label>
                                            <select name="wallet_id" id="wallet_id" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-slate-900 focus:ring-2 focus:ring-[#283593] focus:border-[#283593]">
                                                <option value="">اختر المحفظة أو الحساب</option>
                                                <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($w->id); ?>" <?php echo e((string)old('wallet_id') === (string)$w->id ? 'selected' : ''); ?>>
                                                        <?php echo e($w->name ?? \App\Models\Wallet::typeLabel($w->type)); ?>

                                                        <?php if($w->account_number): ?> — <?php echo e($w->account_number); ?> <?php endif; ?>
                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    <?php endif; ?>

                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">صورة إيصال الدفع <span class="text-rose-500">*</span></label>
                                        <input type="file" name="payment_proof" accept="image/jpeg,image/png,image/jpg" required
                                               class="w-full rounded-xl border border-slate-300 px-4 py-3 text-slate-900 focus:ring-2 focus:ring-[#283593] focus:border-[#283593] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-[#FFE5F7] file:text-[#283593] file:font-semibold">
                                        <p class="text-xs text-slate-500 mt-1">صيغ مقبولة: jpeg, png, jpg — حجم أقصى 40 ميجابايت</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2">ملاحظات (اختياري)</label>
                                        <textarea name="notes" rows="2" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-slate-900 focus:ring-2 focus:ring-[#283593] focus:border-[#283593]" placeholder="أي ملاحظات إضافية..."><?php echo e(old('notes')); ?></textarea>
                                    </div>

                                    <button type="submit" class="btn-primary w-full inline-flex items-center justify-center gap-2 px-6 py-4 rounded-xl bg-[#FB5607] hover:bg-[#e84d00] text-white font-bold text-base shadow-lg">
                                        <i class="fas fa-paper-plane"></i>
                                        إرسال إيصال الدفع
                                    </button>
                                </form>
                                <p class="text-xs text-slate-500 mt-4 text-center">
                                    بعد الإرسال سيظهر طلبك في لوحة الإدارة. عند التحقق من الدفع سيتم تفعيل اشتراكك وتظهر أقسام الباقة في لوحتك.
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <a href="<?php echo e(route('public.pricing')); ?>" class="mt-8 inline-flex items-center gap-2 text-[#283593] hover:text-[#1f2a7a] font-semibold transition-colors">
                    <i class="fas fa-arrow-<?php echo e($isRtl ? 'right' : 'left'); ?>"></i>
                    العودة إلى الأسعار والباقات
                </a>
            </div>
        </section>
    </main>

    <?php echo $__env->make('components.unified-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script>
    (function(){
        var method = document.getElementById('payment_method');
        var wrap = document.getElementById('wallet_id_wrap');
        var walletSelect = document.getElementById('wallet_id');
        if (method && wrap) {
            function toggle() {
                wrap.classList.toggle('hidden', method.value !== 'wallet');
                if (walletSelect) walletSelect.required = (method.value === 'wallet');
            }
            method.addEventListener('change', toggle);
            toggle();
        }
    })();
    </script>

    <?php if($fawaterakUseGateway && !$fawaterakMisconfigured && $fawaterakIntegration === 'iframe'): ?>
    <script>
    (function(){
        var prepareUrl = <?php echo json_encode(route('public.subscription.checkout.fawaterak.prepare', $planKey), 512) ?>;
        var meta = document.querySelector('meta[name="csrf-token"]');
        var token = (meta && meta.getAttribute('content')) || <?php echo json_encode(csrf_token(), 15, 512) ?>;
        var errEl = document.getElementById('sub-fawaterk-checkout-error');
        var couponInput = document.getElementById('subscription_coupon_code');
        var lastPreparedCoupon = null;
        var couponTimer = null;
        var runInFlight = false;
        var rerunRequested = false;
        function showErr(msg) {
            if (!errEl) { alert(msg); return; }
            errEl.textContent = msg;
            errEl.classList.remove('hidden');
        }
        function hideErr() {
            if (errEl) errEl.classList.add('hidden');
        }
        function waitForFawaterkFn(resolve, reject) {
            window.requestAnimationFrame(function() {
                if (typeof fawaterkCheckout === 'function') resolve();
                else setTimeout(function() {
                    typeof fawaterkCheckout === 'function' ? resolve() : reject(new Error('no_fn'));
                }, 80);
            });
        }
        function loadScriptTag(url) {
            return new Promise(function(resolve, reject) {
                var s = document.createElement('script');
                s.src = url;
                s.async = true;
                s.onload = function() { waitForFawaterkFn(resolve, reject); };
                s.onerror = function() { reject(new Error('network')); };
                document.head.appendChild(s);
            });
        }
        function loadScriptViaBlob(url) {
            return fetch(url, { credentials: 'same-origin', cache: 'no-store' })
                .then(function(r) { if (!r.ok) throw new Error('fetch ' + r.status); return r.text(); })
                .then(function(code) {
                    if (!code || code.trim().indexOf('<') === 0) throw new Error('not_js');
                    var blob = new Blob([code], { type: 'application/javascript' });
                    var blobUrl = URL.createObjectURL(blob);
                    return new Promise(function(resolve, reject) {
                        var s = document.createElement('script');
                        s.onload = function() { URL.revokeObjectURL(blobUrl); waitForFawaterkFn(resolve, reject); };
                        s.onerror = function() { URL.revokeObjectURL(blobUrl); reject(new Error('blob_load')); };
                        s.src = blobUrl;
                        document.head.appendChild(s);
                    });
                });
        }
        function loadScript(src) {
            var sep = src.indexOf('?') >= 0 ? '&' : '?';
            var url = src + sep + '_fk=' + Date.now();
            return loadScriptTag(url).catch(function() { return loadScriptViaBlob(url); });
        }
        function parseJsonSafe(text) { try { return JSON.parse(text); } catch (e) { return null; } }
        function appendUpgrade(fd) {
            var u = document.getElementById('sub_checkout_upgrade');
            var f = document.getElementById('sub_checkout_from');
            var coupon = document.getElementById('subscription_coupon_code');
            fd.append('upgrade', u && u.value === '1' ? '1' : '0');
            fd.append('from', f && f.value ? f.value : '');
            fd.append('coupon_code', coupon && coupon.value ? coupon.value.trim() : '');
        }
        function run() {
            if (runInFlight) {
                rerunRequested = true;
                return;
            }
            runInFlight = true;
            var fd = new FormData();
            fd.append('_token', token);
            appendUpgrade(fd);
            fetch(prepareUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: fd,
                credentials: 'same-origin'
            })
            .then(function(r) {
                return r.text().then(function(text) {
                    return { ok: r.ok, status: r.status, data: parseJsonSafe(text), raw: text };
                });
            })
            .then(function(res) {
                hideErr();
                if (res.status === 401) { showErr('انتهت الجلسة. سجّل الدخول ثم أعد فتح الصفحة.'); return; }
                if (res.status === 419) { showErr('انتهت صلاحية الجلسة (CSRF). حدّث الصفحة (F5).'); return; }
                if (!res.data) { showErr('استجابة غير متوقعة من الخادم.'); return; }
                if (!res.ok) { showErr(res.data.message || ('تعذّر تجهيز الدفع (رمز ' + res.status + ').')); return; }
                if (res.data.mode === 'completed' && res.data.redirect) {
                    window.location.href = res.data.redirect;
                    return;
                }
                if ((res.data.mode && res.data.mode !== 'iframe') || !res.data.pluginScriptUrl || !res.data.pluginConfig) {
                    showErr('استجابة غير صالحة من الخادم (تأكد أن FAWATERAK_INTEGRATION=iframe).');
                    return;
                }
                return loadScript(res.data.pluginScriptUrl).then(function() {
                    var host = document.getElementById('fawaterkDivId');
                    if (host) host.innerHTML = '';
                    window.pluginConfig = res.data.pluginConfig;
                    fawaterkCheckout(res.data.pluginConfig);
                }).catch(function(err) {
                    showErr(err && err.message ? err.message : 'تعذّر تحميل ملف الدفع.');
                });
            })
            .catch(function() { showErr('تعذّر الاتصال بالخادم.'); })
            .finally(function() {
                runInFlight = false;
                if (rerunRequested) {
                    rerunRequested = false;
                    run();
                }
            });
        }
        function schedulePrepareOnCouponChange() {
            if (!couponInput) return;
            var current = (couponInput.value || '').trim().toUpperCase();
            if (current === lastPreparedCoupon) return;
            if (couponTimer) clearTimeout(couponTimer);
            couponTimer = setTimeout(function() {
                lastPreparedCoupon = current;
                run();
            }, 450);
        }
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', run);
        else run();
        if (couponInput) {
            lastPreparedCoupon = (couponInput.value || '').trim().toUpperCase();
            couponInput.addEventListener('input', schedulePrepareOnCouponChange);
            couponInput.addEventListener('change', schedulePrepareOnCouponChange);
        }
    })();
    </script>
    <?php endif; ?>

    <?php if($fawaterakUseGateway && !$fawaterakMisconfigured && $fawaterakIntegration === 'api'): ?>
    <script>
    (function(){
        var prepareUrl = <?php echo json_encode(route('public.subscription.checkout.fawaterak.prepare', $planKey), 512) ?>;
        var methodsUrl = <?php echo json_encode(route('public.subscription.checkout.fawaterak.methods', $planKey), 512) ?>;
        var payUrl = <?php echo json_encode(route('public.subscription.checkout.fawaterak.pay', $planKey), 512) ?>;
        var meta = document.querySelector('meta[name="csrf-token"]');
        var token = (meta && meta.getAttribute('content')) || <?php echo json_encode(csrf_token(), 15, 512) ?>;
        var errEl = document.getElementById('sub-fawaterk-api-error');
        var loadEl = document.getElementById('sub-fawaterk-api-loading');
        var methodsEl = document.getElementById('sub-fawaterk-api-methods');
        var payBtn = document.getElementById('sub-fawaterk-api-pay-btn');
        var resultEl = document.getElementById('sub-fawaterk-api-result');
        var walletWrap = document.getElementById('sub-fawaterk-api-wallet-wrap');
        var walletInput = document.getElementById('sub-fawaterk-api-wallet');
        var couponInput = document.getElementById('subscription_coupon_code');
        var couponTimer = null;
        var lastPreparedCoupon = null;
        var runInFlight = false;
        var rerunRequested = false;
        var selectedId = null;
        function showErr(msg) {
            if (!errEl) { alert(msg); return; }
            errEl.textContent = msg;
            errEl.classList.remove('hidden');
        }
        function hideErr() {
            if (errEl) errEl.classList.add('hidden');
        }
        function parseJsonSafe(text) { try { return JSON.parse(text); } catch (e) { return null; } }
        function appendUpgrade(fd) {
            var u = document.getElementById('sub_checkout_upgrade');
            var f = document.getElementById('sub_checkout_from');
            var coupon = document.getElementById('subscription_coupon_code');
            fd.append('upgrade', u && u.value === '1' ? '1' : '0');
            fd.append('from', f && f.value ? f.value : '');
            fd.append('coupon_code', coupon && coupon.value ? coupon.value.trim() : '');
        }
        function renderMethods(list) {
            if (!methodsEl) return;
            methodsEl.innerHTML = '';
            list.forEach(function(m) {
                var id = m.paymentId;
                var name = (document.documentElement.getAttribute('dir') === 'rtl' && m.name_ar) ? m.name_ar : (m.name_en || m.name_ar || ('#' + id));
                var card = document.createElement('button');
                card.type = 'button';
                card.className = 'flex items-center gap-4 p-4 rounded-2xl border-2 border-slate-200 bg-white text-start hover:border-[#283593]/40 transition-colors';
                card.setAttribute('data-pid', String(id));
                var title = document.createElement('span');
                title.className = 'font-bold text-slate-900 flex-1 min-w-0';
                title.textContent = name;
                card.appendChild(title);
                card.addEventListener('click', function() {
                    methodsEl.querySelectorAll('button').forEach(function(b) {
                        b.classList.remove('border-[#283593]', 'ring-2', 'ring-[#283593]/25');
                        b.classList.add('border-slate-200');
                    });
                    card.classList.remove('border-slate-200');
                    card.classList.add('border-[#283593]', 'ring-2', 'ring-[#283593]/25');
                    selectedId = id;
                    if (payBtn) payBtn.disabled = false;
                });
                methodsEl.appendChild(card);
            });
            methodsEl.classList.remove('hidden');
            if (walletWrap) walletWrap.classList.remove('hidden');
        }
        function resetUiBeforePrepare() {
            selectedId = null;
            if (methodsEl) {
                methodsEl.innerHTML = '';
                methodsEl.classList.add('hidden');
            }
            if (walletWrap) walletWrap.classList.add('hidden');
            if (resultEl) {
                resultEl.classList.add('hidden');
                resultEl.innerHTML = '';
            }
            if (loadEl) loadEl.classList.remove('hidden');
            if (payBtn) payBtn.disabled = true;
            hideErr();
        }
        function showPaymentResult(pd) {
            if (!resultEl || !pd) return;
            resultEl.classList.remove('hidden');
            var html = '';
            if (pd.redirectTo) { window.location.href = pd.redirectTo; return; }
            if (pd.fawryCode) html += '<p><strong>رمز فوري:</strong> <span dir="ltr">' + pd.fawryCode + '</span></p>';
            if (!html) html = '<pre class="text-xs whitespace-pre-wrap break-all" dir="ltr">' + JSON.stringify(pd, null, 2) + '</pre>';
            resultEl.innerHTML = '<p class="font-bold text-[#283593] mb-2">أكمل الدفع حسب التعليمات:</p>' + html;
        }
        function run() {
            if (runInFlight) {
                rerunRequested = true;
                return;
            }
            runInFlight = true;
            resetUiBeforePrepare();
            var fd = new FormData();
            fd.append('_token', token);
            appendUpgrade(fd);
            fetch(prepareUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: fd,
                credentials: 'same-origin'
            })
            .then(function(r) { return r.text().then(function(t) { return { ok: r.ok, status: r.status, data: parseJsonSafe(t), raw: t }; }); })
            .then(function(res) {
                if (res.status === 401) { showErr('انتهت الجلسة. سجّل الدخول ثم أعد فتح الصفحة.'); return; }
                if (res.status === 419) { showErr('انتهت صلاحية الجلسة (CSRF). حدّث الصفحة (F5).'); return; }
                if (!res.data || !res.ok) { showErr((res.data && res.data.message) || 'تعذّر تجهيز الطلب.'); return; }
                if (res.data.mode !== 'api') { showErr('الخادم ليس في وضع API.'); return; }
                return fetch(methodsUrl, { method: 'GET', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
            })
            .then(function(r) { if (!r) return; return r.text().then(function(t) { return { ok: r.ok, data: parseJsonSafe(t) }; }); })
            .then(function(res) {
                if (!res) return;
                if (loadEl) loadEl.classList.add('hidden');
                if (!res.ok || !res.data || res.data.status !== 'success' || !Array.isArray(res.data.data)) {
                    showErr((res.data && res.data.message) || 'تعذّر جلب وسائل الدفع.');
                    return;
                }
                renderMethods(res.data.data);
            })
            .catch(function() { if (loadEl) loadEl.classList.add('hidden'); showErr('تعذّر الاتصال بالخادم.'); })
            .finally(function() {
                runInFlight = false;
                if (rerunRequested) {
                    rerunRequested = false;
                    run();
                }
            });
        }
        function schedulePrepareOnCouponChange() {
            if (!couponInput) return;
            var current = (couponInput.value || '').trim().toUpperCase();
            if (current === lastPreparedCoupon) return;
            if (couponTimer) clearTimeout(couponTimer);
            couponTimer = setTimeout(function() {
                lastPreparedCoupon = current;
                run();
            }, 450);
        }
        if (payBtn) {
            payBtn.addEventListener('click', function() {
                if (!selectedId) return;
                if (errEl) errEl.classList.add('hidden');
                payBtn.disabled = true;
                var body = { payment_method_id: selectedId };
                var w = walletInput && walletInput.value ? walletInput.value.trim() : '';
                if (w) body.mobile_wallet_number = w;
                fetch(payUrl, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                    body: JSON.stringify(body)
                })
                .then(function(r) { return r.text().then(function(t) { return { ok: r.ok, data: parseJsonSafe(t) }; }); })
                .then(function(res) {
                    payBtn.disabled = false;
                    if (!res.data) { showErr('استجابة غير متوقعة.'); return; }
                    if (!res.ok) { showErr(res.data.message || 'تعذّر بدء الدفع.'); return; }
                    var pd = res.data.data && res.data.data.payment_data;
                    showPaymentResult(pd);
                })
                .catch(function() { payBtn.disabled = false; showErr('تعذّر إكمال الطلب.'); });
            });
        }
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', run);
        else run();
        if (couponInput) {
            lastPreparedCoupon = (couponInput.value || '').trim().toUpperCase();
            couponInput.addEventListener('input', schedulePrepareOnCouponChange);
            couponInput.addEventListener('change', schedulePrepareOnCouponChange);
        }
    })();
    </script>
    <?php endif; ?>
    <script>
    (function() {
        var couponInput = document.getElementById('subscription_coupon_code');
        var hiddenCoupon = document.getElementById('subscription_coupon_code_hidden');
        if (!couponInput || !hiddenCoupon) return;
        function sync() { hiddenCoupon.value = (couponInput.value || '').trim(); }
        couponInput.addEventListener('input', sync);
        couponInput.addEventListener('change', sync);
        sync();
    })();
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\public\subscription-checkout.blade.php ENDPATH**/ ?>