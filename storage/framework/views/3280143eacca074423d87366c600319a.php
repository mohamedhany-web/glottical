<?php
    $isRtl = $isRtl ?? app()->getLocale() === 'ar';
    $isHome = $isHome ?? request()->routeIs('home');
    $navbarLogoUrl = $navbarLogoUrl ?? null;
    $navbarBrandTagline = $navbarBrandTagline ?? '';
    $langSwitch = $langSwitch ?? fn (string $lang) => request()->fullUrlWithQuery(array_merge(request()->query(), ['lang' => $lang]));
    $megaCategories = $megaCategories ?? collect();
    $navLinks = $navLinks ?? [];
?>


<div id="mobile-menu-overlay"
     class="mob-menu-overlay lg:hidden fixed inset-0 z-[10040]"
     style="display:none; opacity:0;"
     aria-hidden="true"></div>


<div id="mobile-menu-sidebar"
     class="mob-menu-panel lg:hidden fixed top-0 <?php echo e($isRtl ? 'right-0' : 'left-0'); ?> h-full w-[min(300px,88vw)] z-[10041] flex flex-col"
     style="display:none; transform: translate3d(<?php echo e($isRtl ? '100%' : '-100%'); ?>, 0, 0);"
     role="dialog"
     aria-modal="true"
     aria-label="<?php echo e(__('landing.academy.nav_menu')); ?>">

    <div class="mob-menu-inner relative flex flex-col min-h-full overflow-hidden">
        <div class="mob-menu-glow pointer-events-none absolute inset-0"></div>
        <div class="mob-menu-dots pointer-events-none absolute inset-0 opacity-[0.35]"></div>

        <div class="relative shrink-0 flex items-center justify-between gap-3 px-4 py-4 border-b border-white/10 mob-menu-safe-top">
            <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-3 min-w-0 group">
                <?php if(!empty($navbarLogoUrl)): ?>
                    <span class="h-10 w-10 shrink-0 overflow-hidden rounded-full ring-1 ring-white/20 shadow-lg">
                        <img src="<?php echo e($navbarLogoUrl); ?>" alt="" class="h-full w-full object-cover" decoding="async" fetchpriority="high" onerror="this.onerror=null;this.src='<?php echo e(\App\Services\AdminPanelBranding::inlineFallbackDataUri()); ?>';">
                    </span>
                <?php else: ?>
                    <span class="h-10 w-10 shrink-0 rounded-full flex items-center justify-center bg-[#F5B800] text-[#0B3D91] font-black text-lg shadow-lg">G</span>
                <?php endif; ?>
                <div class="min-w-0">
                    <p class="text-white font-black text-base leading-tight truncate group-hover:text-[#F5B800] transition-colors">Glottical</p>
                    <p class="text-white/50 text-[11px] font-semibold truncate"><?php echo e($navbarBrandTagline); ?></p>
                </div>
            </a>
            <button type="button" id="mobile-menu-close"
                    class="mob-menu-close shrink-0 w-10 h-10 rounded-xl flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 border border-white/10 transition-colors"
                    aria-label="Close">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <div class="relative flex-1 overflow-y-auto overscroll-contain px-3 py-4 space-y-4 mob-menu-scroll">

            <?php if($isHome): ?>
            <button type="button"
                    class="mob-menu-search w-full flex items-center gap-3 px-4 py-3 rounded-xl border border-white/12 bg-white/[0.06] text-white/80 hover:bg-white/10 hover:border-[#00A3C4]/35 transition-colors text-start"
                    data-mob-close-then-search>
                <span class="mob-menu-icon mob-menu-icon--cyan"><i class="fas fa-search"></i></span>
                <span class="flex-1 text-sm font-bold text-white/75"><?php echo e(__('landing.academy.nav_search')); ?>…</span>
                <i class="fas fa-chevron-<?php echo e($isRtl ? 'left' : 'right'); ?> mob-menu-chevron"></i>
            </button>
            <?php endif; ?>

            <div class="flex items-stretch rounded-xl border border-white/15 overflow-hidden text-xs font-extrabold bg-white/[0.04]">
                <a href="<?php echo e($langSwitch('ar')); ?>" class="flex-1 py-2.5 text-center transition-colors <?php echo e($isRtl ? 'bg-white/15 text-white' : 'text-white/65 hover:text-white'); ?>" hreflang="ar">عربي</a>
                <span class="w-px bg-white/15 shrink-0" aria-hidden="true"></span>
                <a href="<?php echo e($langSwitch('en')); ?>" class="flex-1 py-2.5 text-center transition-colors <?php echo e(! $isRtl ? 'bg-white/15 text-white' : 'text-white/65 hover:text-white'); ?>" hreflang="en">EN</a>
            </div>

            <nav class="space-y-1">
                
                <details class="mob-menu-details">
                    <summary class="mob-menu-item mob-menu-item--gold mob-menu-summary list-none cursor-pointer">
                        <span class="mob-menu-icon mob-menu-icon--gold"><i class="fas fa-th-large"></i></span>
                        <span class="flex-1 font-bold text-[15px]"><?php echo e(__('landing.academy.nav_categories')); ?></span>
                        <i class="fas fa-chevron-down mob-menu-chevron mob-menu-chevron--toggle text-[10px]"></i>
                    </summary>
                    <div class="mt-1 ms-2 space-y-0.5 border-s border-white/10 ps-3 pb-1">
                        <?php $__currentLoopData = $megaCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e($mc['url']); ?>" class="mob-menu-subitem">
                            <?php if(!empty($mc['thumb_url'])): ?>
                                <span class="mob-menu-icon mob-menu-icon--cyan mob-menu-icon--sm overflow-hidden p-0">
                                    <img src="<?php echo e($mc['thumb_url']); ?>" alt="" width="32" height="32" class="w-full h-full object-cover" loading="lazy" decoding="async">
                                </span>
                            <?php else: ?>
                                <span class="mob-menu-icon mob-menu-icon--cyan mob-menu-icon--sm"><i class="fas <?php echo e($mc['icon']); ?>"></i></span>
                            <?php endif; ?>
                            <span class="min-w-0 flex-1">
                                <span class="flex items-center gap-2">
                                    <span class="block font-bold text-[14px] text-white/90 leading-tight truncate"><?php echo e($mc['name']); ?></span>
                                    <?php if(!empty($mc['count'])): ?>
                                        <span class="shrink-0 text-[9px] font-black px-1.5 py-0.5 rounded bg-acad-yellow/15 text-acad-yellow"><?php echo e($mc['count']); ?></span>
                                    <?php endif; ?>
                                </span>
                                <span class="block text-[11px] text-white/45 line-clamp-1 mt-0.5"><?php echo e($mc['desc']); ?></span>
                            </span>
                        </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('public.courses')); ?>" class="mob-menu-subitem mob-menu-subitem--cta">
                            <span class="flex-1 text-center text-sm font-extrabold text-[#F5B800]"><?php echo e(__('landing.academy.mega_see_all')); ?></span>
                        </a>
                    </div>
                </details>

                <?php $__currentLoopData = $navLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $tone = match(true) {
                        str_contains($link['href'], 'courses') => 'cyan',
                        str_contains($link['href'], 'instructors') => 'blue',
                        default => 'slate',
                    };
                ?>
                <a href="<?php echo e($link['href']); ?>" class="mob-menu-item mob-menu-item--<?php echo e($tone); ?>">
                    <span class="mob-menu-icon mob-menu-icon--<?php echo e($tone); ?>"><i class="fas <?php echo e($link['icon']); ?>"></i></span>
                    <span class="flex-1 font-bold text-[15px]"><?php echo e($link['label']); ?></span>
                    <i class="fas fa-chevron-<?php echo e($isRtl ? 'left' : 'right'); ?> mob-menu-chevron"></i>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </nav>

            <div class="h-px bg-gradient-to-r from-transparent via-white/15 to-transparent"></div>

            <div class="space-y-2 pb-2">
                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('my-courses.index')); ?>" class="mob-menu-item mob-menu-item--cyan">
                        <span class="mob-menu-icon mob-menu-icon--cyan"><i class="fas fa-play-circle"></i></span>
                        <span class="flex-1 font-bold text-[15px]"><?php echo e(__('landing.academy.nav_my_learning')); ?></span>
                        <i class="fas fa-chevron-<?php echo e($isRtl ? 'left' : 'right'); ?> mob-menu-chevron"></i>
                    </a>
                    <a href="<?php echo e(url('/dashboard')); ?>" class="mob-menu-cta mob-menu-cta--primary">
                        <i class="fas fa-th-large text-sm"></i>
                        <?php echo e(__('landing.nav.dashboard')); ?>

                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="mob-menu-item mob-menu-item--slate">
                        <span class="mob-menu-icon mob-menu-icon--slate"><i class="fas fa-sign-in-alt"></i></span>
                        <span class="flex-1 font-bold text-[15px]"><?php echo e(__('landing.nav.login')); ?></span>
                        <i class="fas fa-chevron-<?php echo e($isRtl ? 'left' : 'right'); ?> mob-menu-chevron"></i>
                    </a>
                    <a href="<?php echo e(route('register')); ?>" class="mob-menu-cta mob-menu-cta--primary">
                        <i class="fas fa-user-plus text-sm"></i>
                        <?php echo e(__('landing.academy.nav_join')); ?>

                    </a>
                <?php endif; ?>
            </div>

            <?php if(! $isHome): ?>
            <a href="<?php echo e(route('home')); ?>" class="flex items-center justify-center gap-2 py-2 text-sm font-semibold text-white/45 hover:text-[#F5B800] transition-colors mob-menu-safe-bottom">
                <i class="fas fa-arrow-<?php echo e($isRtl ? 'right' : 'left'); ?> text-xs"></i>
                <?php echo e(__('auth.back_to_home')); ?>

            </a>
            <?php endif; ?>
        </div>

        <?php if(auth()->guard()->check()): ?>
        <div class="relative shrink-0 px-4 py-4 border-t border-white/10 bg-[#0d1528]/90 mob-menu-safe-bottom">
            <div class="flex items-center gap-3 p-3 rounded-xl border border-white/10 bg-white/[0.05]">
                <span class="w-11 h-11 rounded-full flex items-center justify-center text-[#0B3D91] font-black text-sm shrink-0 bg-[#F5B800]">
                    <?php echo e(mb_substr(auth()->user()->name, 0, 1)); ?>

                </span>
                <div class="flex-1 min-w-0 text-start">
                    <p class="text-white font-bold text-sm truncate"><?php echo e(auth()->user()->name); ?></p>
                    <p class="text-white/45 text-xs truncate"><?php echo e(auth()->user()->email); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\partials\mobile-nav-drawer.blade.php ENDPATH**/ ?>