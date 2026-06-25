<?php $__env->startSection('title', __('student.referrals_title') . ' - ' . config('app.name', 'Muallimx')); ?>
<?php $__env->startSection('header', __('student.referrals_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-gray-200 dark:border-slate-700 shadow-sm">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-slate-100 mb-1"><?php echo e(__('student.referrals_title')); ?></h1>
        <p class="text-sm text-gray-500 dark:text-slate-300"><?php echo e(__('student.referrals_subtitle')); ?></p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-gray-200 dark:border-slate-700 shadow-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide"><?php echo e(__('student.total_referrals')); ?></p>
            <p class="text-2xl font-bold text-sky-600 mt-1"><?php echo e(number_format($stats['total_referrals'])); ?></p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-gray-200 dark:border-slate-700 shadow-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide"><?php echo e(__('student.completed_referrals')); ?></p>
            <p class="text-2xl font-bold text-emerald-600 mt-1"><?php echo e(number_format($stats['completed_referrals'])); ?></p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-gray-200 dark:border-slate-700 shadow-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide"><?php echo e(__('student.pending_referrals')); ?></p>
            <p class="text-2xl font-bold text-amber-600 mt-1"><?php echo e(number_format($stats['pending_referrals'])); ?></p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-gray-200 dark:border-slate-700 shadow-sm">
            <p class="text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wide">إجمالي المكافآت</p>
            <p class="text-2xl font-bold text-purple-600 mt-1"><?php echo e(number_format($stats['total_rewards'], 2)); ?> ج.م</p>
        </div>
    </div>

    <?php if($activeProgram): ?>
    <div class="rounded-xl border border-emerald-200 dark:border-emerald-800 bg-white dark:bg-slate-800 p-5 shadow-sm">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
            <i class="fas fa-gift text-emerald-600"></i>
            القواعد الحالية (برنامج: <?php echo e($activeProgram->name); ?>)
        </h3>
        <ul class="text-sm text-gray-700 dark:text-slate-300 space-y-2 list-disc list-inside">
            <li>خصم للصديق المدعو:
                <?php if($activeProgram->discount_type === 'percentage'): ?>
                    <?php echo e(rtrim(rtrim(number_format($activeProgram->discount_value, 2), '0'), '.')); ?>% على أول شراء (كورس) ضمن الشروط
                <?php else: ?>
                    <?php echo e(number_format($activeProgram->discount_value, 2)); ?> ج.م
                <?php endif; ?>
                <?php if($activeProgram->maximum_discount): ?>
                    — حد أقصى للخصم <?php echo e(number_format($activeProgram->maximum_discount, 2)); ?> ج.م
                <?php endif; ?>
            </li>
            <li>مدة صلاحية كوبون الخصم: <?php echo e($activeProgram->discount_valid_days); ?> يوماً من تاريخ التسجيل.</li>
            <li>مكافأتك عند اكتمال الطلب:
                <?php if($activeProgram->referrer_reward_type === 'points'): ?>
                    <?php echo e(number_format($activeProgram->referrer_reward_value ?? 0, 0)); ?> نقطة
                <?php elseif($activeProgram->referrer_reward_type === 'percentage'): ?>
                    <?php echo e(rtrim(rtrim(number_format($activeProgram->referrer_reward_value ?? 0, 2), '0'), '.')); ?>% من قيمة الطلب
                <?php else: ?>
                    <?php echo e(number_format($activeProgram->referrer_reward_value ?? 0, 2)); ?> ج.م
                <?php endif; ?>
                <?php if(!$activeProgram->referrer_reward_value): ?>
                    (غير محددة في البرنامج)
                <?php endif; ?>
            </li>
            <?php if($activeProgram->max_referrals_per_user): ?>
            <li>حد أقصى <?php echo e($activeProgram->max_referrals_per_user); ?> إحالة لكل حساب ضمن هذا البرنامج.</li>
            <?php endif; ?>
        </ul>
    </div>
    <?php else: ?>
    <div class="rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 p-5 text-amber-900 dark:text-amber-200 text-sm">
        <i class="fas fa-exclamation-triangle ml-2"></i>
        لا يوجد برنامج إحالات نشط حالياً. يمكنك نسخ رابطك، لكن لن تُسجَّل إحالات أو مكافآت حتى يفعّل المشرف برنامجاً من لوحة الإدارة.
    </div>
    <?php endif; ?>

    <!-- Referral Code Card -->
    <div class="bg-gradient-to-br from-sky-500 via-sky-600 to-indigo-600 rounded-xl shadow-sm p-5 sm:p-6 text-white relative overflow-hidden border border-sky-400/30">
        <div class="absolute top-0 right-0 w-80 h-80 bg-white/10 rounded-full -mr-40 -mt-40 blur-3xl group-hover:bg-white/15 transition-all duration-500"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/10 rounded-full -ml-32 -mb-32 blur-3xl group-hover:bg-white/15 transition-all duration-500"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent"></div>
        
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-link text-2xl text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold mb-1 flex items-center gap-2">
                                كود الإحالة الخاص بك
                            </h2>
                            <p class="text-sky-100 text-sm">شارك هذا الكود واحصل على مكافآت</p>
                        </div>
                    </div>
                    
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-5 mb-4 border border-white/20 shadow-lg">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex-1">
                                <p class="text-sm text-sky-100 mb-2 font-medium flex items-center gap-2">
                                    <i class="fas fa-tag"></i>
                                    كود الإحالة
                                </p>
                                <p class="text-2xl font-bold tracking-wider bg-white/10 px-4 py-2 rounded-lg inline-block"><?php echo e($referralCode); ?></p>
                            </div>
                            <button type="button" onclick="copyReferralCode('<?php echo e($referralCode); ?>')" 
                                    class="bg-white text-sky-600 px-6 py-3 rounded-xl font-bold hover:bg-sky-50 hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2 shadow-lg transform hover:scale-105">
                                <i class="fas fa-copy"></i>
                                نسخ الكود
                            </button>
                        </div>
                    </div>

                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-5 border border-white/20 shadow-lg">
                        <p class="text-sm text-sky-100 mb-3 font-medium flex items-center gap-2">
                            <i class="fas fa-link"></i>
                            رابط الإحالة
                        </p>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <input type="text" 
                                   id="referralLink" 
                                   value="<?php echo e($referralLink); ?>" 
                                   readonly
                                   class="flex-1 bg-white/30 backdrop-blur-sm border border-white/30 rounded-lg px-4 py-3 text-white font-medium focus:outline-none focus:ring-2 focus:ring-white/50 text-sm">
                            <button type="button" onclick="copyReferralLink()" 
                                    class="bg-white text-sky-600 px-6 py-3 rounded-lg font-bold hover:bg-sky-50 hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2 shadow-lg transform hover:scale-105">
                                <i class="fas fa-copy"></i>
                                نسخ الرابط
                            </button>
                            <a href="https://wa.me/?text=<?php echo e(urlencode('سجّل في المنصة عبر رابطي واحصل على خصم: ' . $referralLink)); ?>" target="_blank" rel="noopener"
                               class="bg-emerald-500 text-white px-5 py-3 rounded-lg font-bold hover:bg-emerald-600 transition-all flex items-center justify-center gap-2 shadow-lg">
                                <i class="fab fa-whatsapp"></i>
                                واتساب
                            </a>
                        </div>
                    </div>
                </div>

                <div class="text-center md:text-right flex-shrink-0">
                    <div class="w-36 h-36 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto md:mx-0 mb-4 border border-white/20 shadow-lg group-hover:scale-105 transition-transform duration-300">
                        <i class="fas fa-qrcode text-7xl text-white opacity-80"></i>
                    </div>
                    <p class="text-sm text-sky-100 font-medium">شارك الكود عبر وسائل التواصل</p>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="px-4 sm:px-5 py-4 border-b border-gray-100 dark:border-slate-700">
            <h3 class="text-base font-bold text-gray-900 dark:text-slate-100 flex items-center gap-2">
                <i class="fas fa-info-circle text-sky-600 ml-2"></i>
                كيف يعمل برنامج الإحالات؟
            </h3>
        </div>
        <div class="p-4 sm:p-5 grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="p-4 rounded-lg border border-sky-100 dark:border-sky-900/50 bg-sky-50 dark:bg-sky-900/20">
                <h4 class="font-bold text-gray-900 dark:text-slate-100 mb-2">1) شارك كود الإحالة</h4>
                <p class="text-sm text-gray-600 dark:text-slate-300">انسخ كود الإحالة أو الرابط وشاركه مع أصدقائك.</p>
            </div>
            <div class="p-4 rounded-lg border border-purple-100 dark:border-purple-900/50 bg-purple-50 dark:bg-purple-900/20">
                <h4 class="font-bold text-gray-900 dark:text-slate-100 mb-2">2) صديقك يسجّل بالرابط</h4>
                <p class="text-sm text-gray-600 dark:text-slate-300">يفتح <span class="font-mono text-xs bg-white/80 dark:bg-slate-700 px-1 rounded">/register?ref=كودك</span> ويكمل التسجيل.</p>
            </div>
            <div class="p-4 rounded-lg border border-emerald-100 dark:border-emerald-900/50 bg-emerald-50 dark:bg-emerald-900/20">
                <h4 class="font-bold text-gray-900 dark:text-slate-100 mb-2">3) اكتمال الإحالة</h4>
                <p class="text-sm text-gray-600 dark:text-slate-300">عند اعتماد أول طلب شراء للمدعو، تُسجَّل الإحالة «مكتملة» وتظهر مكافأتك.</p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="px-4 sm:px-5 py-4 border-b border-gray-100 dark:border-slate-700">
            <h3 class="text-base font-bold text-gray-900 dark:text-slate-100 flex items-center gap-2">
                <i class="fas fa-list text-sky-600 ml-2"></i>
                قائمة الإحالات
            </h3>
        </div>

        <?php if($referrals->count() > 0): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                <thead class="bg-gray-50 dark:bg-slate-900/40">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">المستخدم المحال</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">تاريخ الإحالة</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">الحالة</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">الخصم</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">المكافأة</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                    <?php $__currentLoopData = $referrals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $referral): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/40">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-sky-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                        <?php echo e(substr($referral->referred->name ?? 'N', 0, 1)); ?>

                                    </div>
                                </div>
                                <div class="mr-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-slate-100"><?php echo e($referral->referred->name ?? 'غير معروف'); ?></div>
                                    <div class="text-sm text-gray-500 dark:text-slate-400"><?php echo e($referral->referred->phone ?? 'N/A'); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-slate-400">
                            <?php echo e($referral->created_at->format('d/m/Y')); ?>

                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                <?php if($referral->status == 'completed'): ?> bg-emerald-100 text-emerald-800
                                <?php elseif($referral->status == 'pending'): ?> bg-amber-100 text-amber-800
                                <?php else: ?> bg-red-100 text-red-800
                                <?php endif; ?>">
                                <?php if($referral->status == 'completed'): ?> مكتملة
                                <?php elseif($referral->status == 'pending'): ?> قيد الانتظار
                                <?php else: ?> ملغاة
                                <?php endif; ?>
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-slate-100">
                            <?php echo e(number_format($referral->discount_amount ?? 0, 2)); ?> ج.م
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-bold text-emerald-600">
                            <?php echo e(number_format($referral->reward_amount ?? 0, 2)); ?> ج.م
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 dark:border-slate-700"><?php echo e($referrals->links()); ?></div>
        <?php else: ?>
        <div class="p-10 sm:p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-400">
                <i class="fas fa-user-friends text-2xl"></i>
            </div>
            <p class="text-gray-700 dark:text-slate-200 text-base font-semibold mb-1">لا توجد إحالات حتى الآن</p>
            <p class="text-gray-500 dark:text-slate-400 text-sm mb-5">ابدأ بمشاركة كود الإحالة مع أصدقائك واحصل على مكافآت.</p>
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-sky-50 dark:bg-sky-900/20 border border-sky-200 dark:border-sky-800 rounded-lg">
                <i class="fas fa-info-circle text-sky-600"></i>
                <span class="text-sm text-gray-700 dark:text-slate-300">كلما زادت الإحالات، زادت المكافآت.</span>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div id="referral-toast" class="fixed top-4 left-4 rtl:left-auto rtl:right-4 z-[100] hidden px-4 py-3 rounded-xl bg-emerald-600 text-white text-sm font-semibold shadow-lg max-w-sm" role="status"></div>

<script>
function showReferralToast(msg) {
    var el = document.getElementById('referral-toast');
    if (!el) { alert(msg); return; }
    el.textContent = msg;
    el.classList.remove('hidden');
    clearTimeout(window._refToastT);
    window._refToastT = setTimeout(function() { el.classList.add('hidden'); }, 3200);
}
function copyReferralCode(code) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(code).then(function() {
            showReferralToast('تم نسخ كود الإحالة');
        }).catch(function() { showReferralToast('انسخ الكود يدوياً: ' + code); });
    } else {
        showReferralToast('انسخ الكود يدوياً: ' + code);
    }
}
function copyReferralLink() {
    var link = document.getElementById('referralLink').value;
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(link).then(function() {
            showReferralToast('تم نسخ رابط الإحالة');
        }).catch(function() { showReferralToast('انسخ الرابط يدوياً من الحقل'); });
    } else {
        showReferralToast('انسخ الرابط يدوياً من الحقل');
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\student\referrals\index.blade.php ENDPATH**/ ?>