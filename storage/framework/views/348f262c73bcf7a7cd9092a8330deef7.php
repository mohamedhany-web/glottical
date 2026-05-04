<?php
    $pf = $publicFooter ?? \App\Services\PublicFooterSettings::payload();
    $isRtl = app()->getLocale() === 'ar';
    $telHref = '';
    if (! empty($pf['phone'])) {
        $digits = preg_replace('/[^\d+]/', '', $pf['phone']);
        $telHref = $digits !== '' ? 'tel:'.$digits : '';
    }
    $brandName = config('app.name', 'Muallimx');
    $footerLogoUrl = \App\Services\AdminPanelBranding::logoPublicUrl();
?>

<footer style="background:#283593;font-family:Tajawal,Cairo,sans-serif" class="text-white <?php echo e($footerExtraClass ?? ''); ?>">
    <div class="max-w-[1200px] mx-auto px-6 sm:px-8 pt-12 pb-8">
        <div class="grid md:grid-cols-4 gap-8 pb-8 border-b border-white/15">
            <div class="md:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-3 shrink-0 group">
                        <?php if(! empty($footerLogoUrl)): ?>
                            <span class="relative w-11 h-11 overflow-hidden rounded-full ring-1 ring-white/30 shadow-lg [box-shadow:0_4px_14px_-4px_rgba(0,0,0,.35)] transition-transform duration-200 group-hover:scale-[1.03]">
                                <img src="<?php echo e($footerLogoUrl); ?>" alt="<?php echo e($brandName); ?>" class="h-full w-full object-cover object-center" width="44" height="44" decoding="async">
                            </span>
                        <?php else: ?>
                            <span class="w-11 h-11 rounded-full bg-[#FB5607] text-white font-black flex items-center justify-center shadow-lg transition-transform duration-200 group-hover:scale-[1.03]" style="box-shadow:0 4px 16px -4px rgba(251,86,7,.35)">M</span>
                        <?php endif; ?>
                        <div class="min-w-0">
                            <p class="text-xl font-black text-white group-hover:text-[#FFE569] transition-colors"><?php echo e($brandName); ?></p>
                            <p class="text-xs text-white/70"><?php echo e($pf['brand_tagline']); ?></p>
                        </div>
                    </a>
                </div>
                <p class="text-sm text-white/85 leading-7 max-w-md"><?php echo e($pf['blurb']); ?></p>
            </div>
            <div>
                <h3 class="font-bold mb-3 text-white"><?php echo e(__('public.quick_links')); ?></h3>
                <ul class="space-y-2 text-sm text-white/85">
                    <li><a class="hover:text-[#FFE569] transition-colors" href="<?php echo e(route('home')); ?>"><?php echo e(__('public.home')); ?></a></li>
                    <?php if(\Illuminate\Support\Facades\Route::has('public.services.index')): ?>
                    <li><a class="hover:text-[#FFE569] transition-colors" href="<?php echo e(route('public.services.index')); ?>"><?php echo e(__('public.services_page_title')); ?></a></li>
                    <?php endif; ?>
                    <li><a class="hover:text-[#FFE569] transition-colors" href="<?php echo e(route('public.courses')); ?>"><?php echo e(__('public.courses')); ?></a></li>
                    <li><a class="hover:text-[#FFE569] transition-colors" href="<?php echo e(route('public.instructors.index')); ?>"><?php echo e(__('landing.nav.instructors')); ?></a></li>
                    <li><a class="hover:text-[#FFE569] transition-colors" href="<?php echo e(route('public.about')); ?>"><?php echo e(__('public.about')); ?></a></li>
                    <li><a class="hover:text-[#FFE569] transition-colors" href="<?php echo e(route('public.faq')); ?>"><?php echo e(__('public.faq')); ?></a></li>
                    <li><a class="hover:text-[#FFE569] transition-colors" href="<?php echo e(route('public.help')); ?>"><?php echo e(__('public.help_center')); ?></a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-bold mb-3 text-white"><?php echo e(__('public.contact_us')); ?></h3>
                <ul class="space-y-2 text-sm text-white/85">
                    <li><a class="hover:text-[#FFE569] transition-colors" href="<?php echo e(route('public.contact')); ?>"><?php echo e(__('public.contact_page_title')); ?></a></li>
                    <?php if(! empty($pf['email'])): ?>
                    <li>
                        <a class="hover:text-[#FFE569] transition-colors break-all" href="mailto:<?php echo e(e($pf['email'])); ?>"><?php echo e($pf['email']); ?></a>
                    </li>
                    <?php endif; ?>
                    <?php if(! empty($pf['phone']) && $telHref !== ''): ?>
                    <li>
                        <a class="hover:text-[#FFE569] transition-colors" href="<?php echo e($telHref); ?>" rel="nofollow"><?php echo e($pf['phone']); ?></a>
                    </li>
                    <?php elseif(! empty($pf['phone'])): ?>
                    <li><span class="text-white/85"><?php echo e($pf['phone']); ?></span></li>
                    <?php endif; ?>
                    <?php if(! empty($pf['whatsapp_url'])): ?>
                    <li>
                        <a class="hover:text-[#FFE569] transition-colors inline-flex items-center gap-2" href="<?php echo e(e($pf['whatsapp_url'])); ?>" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-whatsapp text-lg"></i>
                            <?php if(! empty($pf['phone'])): ?>
                                <?php echo e($isRtl ? 'واتساب: ' : 'WhatsApp: '); ?><?php echo e($pf['phone']); ?>

                            <?php else: ?>
                                WhatsApp
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="mt-4 space-y-2 text-xs text-white/70">
                    <li><a class="hover:text-[#FFE569] transition-colors" href="<?php echo e(route('public.terms')); ?>"><?php echo e(__('public.terms_conditions')); ?></a></li>
                    <li><a class="hover:text-[#FFE569] transition-colors" href="<?php echo e(route('public.privacy')); ?>"><?php echo e(__('public.privacy_policy')); ?></a></li>
                </ul>
                <?php if(! empty($pf['socials'])): ?>
                <p class="text-xs font-bold text-white/90 mt-4 mb-2"><?php echo e(__('public.follow_us')); ?></p>
                <div class="flex flex-wrap gap-2">
                    <?php $__currentLoopData = $pf['socials']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $soc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(e($soc['url'])); ?>"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="w-9 h-9 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors"
                       aria-label="<?php echo e(e($soc['label'])); ?>"
                       title="<?php echo e(e($soc['label'])); ?>">
                        <i class="<?php echo e(e($soc['icon'])); ?> text-sm"></i>
                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="pt-5 flex flex-col sm:flex-row gap-2 justify-between text-xs text-white/75">
            <p>&copy; <?php echo e(date('Y')); ?> <?php echo e($brandName); ?> — <?php echo e($isRtl ? 'جميع الحقوق محفوظة' : 'All rights reserved'); ?></p>
            <?php if(! empty($pf['bottom_tagline'])): ?>
            <p><?php echo e($pf['bottom_tagline']); ?></p>
            <?php endif; ?>
        </div>
    </div>
</footer>

<?php if(! empty($pf['whatsapp_url'])): ?>
    
    <a href="<?php echo e(e($pf['whatsapp_url'])); ?>"
       target="_blank"
       rel="noopener noreferrer"
       class="fixed z-[9998] flex items-center justify-center rounded-full shadow-lg"
       style="
           width: 56px;
           height: 56px;
           <?php echo e($isRtl ? 'left' : 'right'); ?>: 18px;
           bottom: 18px;
           background-color: #25D366;
           color: #ffffff;
           box-shadow: 0 10px 25px -10px rgba(0,0,0,.45);
       "
       aria-label="<?php echo e($isRtl ? 'تواصل عبر واتساب' : 'Chat on WhatsApp'); ?>">
        <i class="fab fa-whatsapp text-2xl"></i>
    </a>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\partials\public-site-footer.blade.php ENDPATH**/ ?>