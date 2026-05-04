<?php
    $isRtl = app()->getLocale() === 'ar';
    $navbarLogoUrl = $navbarLogoUrl ?? \App\Services\AdminPanelBranding::logoPublicUrl();
    $navbarBrandTagline = $navbarBrandTagline ?? \App\Services\PublicFooterSettings::payload()['brand_tagline'];
?>
<nav id="navbar"
     class="fixed top-0 inset-x-0 z-[999] transition-all duration-500"
     :class="navSolid ? 'nav-solid' : 'nav-transparent'"
     style="font-family: 'Cairo', 'Tajawal', 'IBM Plex Sans Arabic', sans-serif;">

    <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-10">
        <div class="flex items-center justify-between h-[68px] lg:h-[76px]">

            
            <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-3 group flex-shrink-0">
                <?php if(!empty($navbarLogoUrl)): ?>
                    <span class="relative h-10 w-10 lg:h-11 lg:w-11 flex-shrink-0 overflow-hidden rounded-full ring-1 ring-white/25 shadow-lg [box-shadow:0_4px_14px_-4px_rgba(0,0,0,.35)]">
                        <img src="<?php echo e($navbarLogoUrl); ?>" alt="<?php echo e(config('app.name')); ?>" class="h-full w-full object-cover object-center" decoding="async">
                    </span>
                <?php else: ?>
                    <div class="relative w-10 h-10 lg:w-11 lg:h-11 rounded-full flex items-center justify-center shadow-lg transition-shadow duration-300" style="background:#FB5607;box-shadow:0 4px 16px -4px rgba(251,86,7,.3)">
                        <span class="text-white font-black text-lg lg:text-xl select-none">M</span>
                    </div>
                <?php endif; ?>
                <div class="flex flex-col leading-none">
                    <span class="text-[18px] lg:text-[20px] font-black text-white tracking-tight">Muallimx</span>
                    <span class="text-[11px] lg:text-[12px] text-white/60 font-semibold mt-0.5"><?php echo e($navbarBrandTagline); ?></span>
                </div>
            </a>

            
            <div class="hidden lg:flex items-center gap-1">
                <?php
                $navLinks = [
                    ['route' => 'public.courses', 'icon' => 'fa-graduation-cap', 'label' => __('landing.nav.courses')],
                    ['route' => 'public.portfolio.index', 'icon' => 'fa-briefcase', 'label' => __('landing.nav.portfolio')],
                    ['route' => 'public.instructors.index', 'icon' => 'fa-chalkboard-teacher', 'label' => __('landing.nav.instructors')],
                    ['route' => 'public.services.index', 'icon' => 'fa-concierge-bell', 'label' => __('landing.nav.services')],
                    ['route' => 'public.pricing', 'icon' => 'fa-tags', 'label' => __('landing.nav.pricing')],
                ];
                ?>

                <?php $__currentLoopData = $navLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route($link['route'])); ?>"
                   class="nav-link relative px-4 py-2 rounded-xl text-[16px] font-bold text-white/80 hover:text-white transition-all duration-200 flex items-center gap-2">
                    <i class="fas <?php echo e($link['icon']); ?> text-[13px] opacity-60"></i>
                    <span><?php echo e($link['label']); ?></span>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <div class="hidden lg:flex items-center gap-3 flex-shrink-0">
                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(url('/dashboard')); ?>"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/[0.08] hover:bg-white/[0.14] border border-white/[0.1] text-white font-bold text-[15px] transition-all duration-200 backdrop-blur-sm">
                        <i class="fas fa-th-large text-xs opacity-70"></i>
                        <?php echo e(__('landing.nav.dashboard')); ?>

                    </a>
                <?php endif; ?>
                <?php if(auth()->guard()->guest()): ?>
                    <a href="<?php echo e(route('login')); ?>"
                       class="px-4 py-2.5 rounded-xl text-white/85 hover:text-white font-bold text-[15px] transition-all duration-200 hover:bg-white/[0.06]">
                        <?php echo e(__('landing.nav.login')); ?>

                    </a>
                    <a href="<?php echo e(route('register')); ?>"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm text-white shadow-lg transition-all duration-300" style="background:#FB5607;box-shadow:0 4px 16px -4px rgba(251,86,7,.3)">
                        <?php echo e(__('landing.nav.register')); ?>

                        <i class="fas fa-arrow-left text-[11px]"></i>
                    </a>
                <?php endif; ?>
            </div>

            
            <button type="button"
                    id="mobile-menu-toggle"
                    class="lg:hidden w-11 h-11 rounded-xl flex items-center justify-center text-white/80 hover:text-white hover:bg-white/[0.08] transition-all duration-200 flex-shrink-0"
                    aria-label="القائمة"
                    aria-expanded="false">
                <span id="menu-bars-icon"><i class="fas fa-bars text-lg"></i></span>
                <span id="menu-times-icon" style="display:none;"><i class="fas fa-times text-lg"></i></span>
            </button>
        </div>
    </div>
</nav>


<div class="navbar-spacer h-[68px] lg:h-[76px]"></div>


<div id="mobile-menu-overlay"
     class="lg:hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[9999]"
     style="display:none; opacity:0; transition: opacity 0.25s ease; will-change: opacity;"></div>


<div id="mobile-menu-sidebar"
     class="lg:hidden fixed top-0 <?php echo e($isRtl ? 'right-0' : 'left-0'); ?> h-full w-[min(320px,85vw)] z-[10000] overflow-y-auto overscroll-contain"
     style="display:none; transform: translate3d(<?php echo e($isRtl ? '100%' : '-100%'); ?>, 0, 0); transition: transform 0.3s cubic-bezier(0.32, 0.72, 0, 1); -webkit-overflow-scrolling: touch; will-change: transform;">

    <div class="min-h-full flex flex-col" style="background: linear-gradient(180deg, #283593 0%, #1A237E 100%); font-family: 'Tajawal', 'IBM Plex Sans Arabic', sans-serif;">

        
        <div class="absolute top-0 <?php echo e($isRtl ? 'left-0' : 'right-0'); ?> w-40 h-40 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-20 <?php echo e($isRtl ? 'right-0' : 'left-0'); ?> w-32 h-32 bg-blue-500/8 rounded-full blur-3xl pointer-events-none"></div>

        
        <div class="relative flex items-center justify-between px-5 py-5 border-b border-white/[0.06]" style="padding-top: max(1.25rem, env(safe-area-inset-top));">
            <div class="flex items-center gap-3">
                <?php if(!empty($navbarLogoUrl)): ?>
                    <span class="relative h-10 w-10 flex-shrink-0 overflow-hidden rounded-full ring-1 ring-white/20 shadow-md [box-shadow:0_3px_12px_-4px_rgba(0,0,0,.4)]">
                        <img src="<?php echo e($navbarLogoUrl); ?>" alt="" class="h-full w-full object-cover object-center" decoding="async">
                    </span>
                <?php else: ?>
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shadow-lg" style="background:#FB5607">
                        <span class="text-white font-black text-lg">M</span>
                    </div>
                <?php endif; ?>
                <div>
                    <p class="text-white font-black text-[17px]">Muallimx</p>
                    <p class="text-white/50 text-[12px] font-semibold"><?php echo e($navbarBrandTagline); ?></p>
                </div>
            </div>
            <button type="button" id="mobile-menu-close"
                    class="w-10 h-10 rounded-xl flex items-center justify-center text-white/60 hover:text-white hover:bg-white/[0.08] active:bg-white/[0.12] transition-colors"
                    style="-webkit-tap-highlight-color: transparent;">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        
        <div class="relative flex-1 px-4 py-5 space-y-1">
            <?php
            $mobileLinks = [
                ['route' => 'public.courses', 'icon' => 'fa-graduation-cap', 'label' => __('landing.nav.courses'), 'color' => 'blue'],
                ['route' => 'public.portfolio.index', 'icon' => 'fa-briefcase', 'label' => __('landing.nav.portfolio'), 'color' => 'purple'],
                ['route' => 'public.instructors.index', 'icon' => 'fa-chalkboard-teacher', 'label' => __('landing.nav.instructors'), 'color' => 'emerald'],
                ['route' => 'public.services.index', 'icon' => 'fa-concierge-bell', 'label' => __('landing.nav.services'), 'color' => 'orange'],
                ['route' => 'public.pricing', 'icon' => 'fa-tags', 'label' => __('landing.nav.pricing'), 'color' => 'cyan'],
            ];
            ?>

            <?php $__currentLoopData = $mobileLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mLink): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route($mLink['route'])); ?>"
               class="flex items-center gap-4 px-4 py-3.5 rounded-2xl text-white/80 hover:text-white hover:bg-white/[0.06] active:bg-white/[0.1] transition-colors"
               style="-webkit-tap-highlight-color: transparent;">
                <span class="w-10 h-10 rounded-xl bg-<?php echo e($mLink['color']); ?>-500/10 border border-<?php echo e($mLink['color']); ?>-500/15 flex items-center justify-center flex-shrink-0">
                    <i class="fas <?php echo e($mLink['icon']); ?> text-<?php echo e($mLink['color']); ?>-400 text-base"></i>
                </span>
                <span class="flex-1 font-extrabold text-[16px]"><?php echo e($mLink['label']); ?></span>
                <i class="fas fa-chevron-<?php echo e($isRtl ? 'left' : 'right'); ?> text-white/20 text-xs"></i>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <div class="my-2 h-px bg-white/[0.06]"></div>

            
            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(url('/dashboard')); ?>"
                   class="flex items-center justify-center gap-2 mx-1 px-5 py-4 rounded-2xl text-white font-bold text-[15px] shadow-lg active:scale-[0.98] transition-transform"
                   style="background:#FB5607;-webkit-tap-highlight-color: transparent;">
                    <i class="fas fa-th-large text-sm"></i>
                    <?php echo e(__('landing.nav.dashboard')); ?>

                </a>
            <?php endif; ?>
            <?php if(auth()->guard()->guest()): ?>
                <a href="<?php echo e(route('login')); ?>"
                   class="flex items-center gap-4 px-4 py-3.5 rounded-2xl text-white/80 hover:text-white hover:bg-white/[0.06] active:bg-white/[0.1] transition-colors"
                   style="-webkit-tap-highlight-color: transparent;">
                    <span class="w-10 h-10 rounded-xl bg-white/[0.06] border border-white/[0.08] flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-sign-in-alt text-white/60 text-base"></i>
                    </span>
                    <span class="flex-1 font-extrabold text-[16px]"><?php echo e(__('landing.nav.login')); ?></span>
                </a>
                <a href="<?php echo e(route('register')); ?>"
                   class="flex items-center justify-center gap-2 mx-1 px-5 py-4 rounded-2xl text-white font-bold text-[15px] shadow-lg active:scale-[0.98] transition-transform"
                   style="background:#FB5607;-webkit-tap-highlight-color: transparent;">
                    <i class="fas fa-user-plus text-sm"></i>
                    <?php echo e(__('landing.nav.register')); ?>

                </a>
            <?php endif; ?>
        </div>

        
        <?php if(auth()->guard()->check()): ?>
        <div class="relative px-5 py-5 border-t border-white/[0.06]" style="padding-bottom: max(1.25rem, env(safe-area-inset-bottom));">
            <div class="flex items-center gap-3 p-3 rounded-2xl bg-white/[0.04]">
                <div class="w-11 h-11 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0" style="background:#FB5607">
                    <?php echo e(mb_substr(auth()->user()->name, 0, 1)); ?>

                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white font-bold text-sm truncate"><?php echo e(auth()->user()->name); ?></p>
                    <p class="text-white/40 text-xs truncate"><?php echo e(auth()->user()->email); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Navbar states — defaults to solid so inner pages work correctly */
#navbar {
    background: rgba(40, 53, 147, 0.95);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}
#navbar.nav-transparent {
    background: rgba(40, 53, 147, 0.92) !important;
    backdrop-filter: blur(20px) saturate(180%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(180%) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    box-shadow: 0 4px 30px rgba(26, 35, 126, 0.26) !important;
}
#navbar.nav-solid {
    background: rgba(40, 53, 147, 0.92) !important;
    backdrop-filter: blur(20px) saturate(180%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(180%) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    box-shadow: 0 4px 30px rgba(26, 35, 126, 0.3) !important;
}

/* Nav link hover effect */
.nav-link::after {
    content: '';
    position: absolute;
    bottom: 4px;
    left: 50%;
    transform: translateX(-50%) scaleX(0);
    width: 20px;
    height: 2px;
    border-radius: 1px;
    background: #FB5607;
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.nav-link:hover::after {
    transform: translateX(-50%) scaleX(1);
}

/* Mobile sidebar performance */
#mobile-menu-sidebar {
    overscroll-behavior: contain;
}

@media (max-width: 380px) {
    #mobile-menu-sidebar { width: min(290px, 90vw) !important; }
}
</style>

<script>
(function() {
    'use strict';

    /* ─── Scroll-based navbar background ─── */
    var navbar = document.getElementById('navbar');
    var solid = false;

    function checkScroll() {
        var y = window.pageYOffset || document.documentElement.scrollTop;
        var shouldBeSolid = y > 40;
        if (shouldBeSolid !== solid) {
            solid = shouldBeSolid;
            if (solid) { navbar.classList.add('nav-solid'); navbar.classList.remove('nav-transparent'); }
            else { navbar.classList.remove('nav-solid'); navbar.classList.add('nav-transparent'); }
        }
    }
    window.addEventListener('scroll', checkScroll, { passive: true });
    checkScroll();

    /* Expose state for Alpine (x-bind) */
    window.navSolid = solid;

    /* ─── Mobile menu ─── */
    var isRtl = document.documentElement.dir === 'rtl';
    var hiddenTransform = isRtl ? 'translate3d(100%,0,0)' : 'translate3d(-100%,0,0)';

    function initMobileMenu() {
        var toggle = document.getElementById('mobile-menu-toggle');
        var sidebar = document.getElementById('mobile-menu-sidebar');
        var overlay = document.getElementById('mobile-menu-overlay');
        var closeBtn = document.getElementById('mobile-menu-close');
        var barsIcon = document.getElementById('menu-bars-icon');
        var timesIcon = document.getElementById('menu-times-icon');
        if (!toggle || !sidebar || !overlay) return;

        var open = false;

        function openMenu() {
            if (open) return;
            open = true;
            sidebar.style.display = 'block';
            overlay.style.display = 'block';
            document.body.classList.add('overflow-hidden');
            requestAnimationFrame(function() {
                requestAnimationFrame(function() {
                    sidebar.style.transform = 'translate3d(0,0,0)';
                    overlay.style.opacity = '1';
                });
            });
            if (barsIcon) barsIcon.style.display = 'none';
            if (timesIcon) timesIcon.style.display = 'block';
            toggle.setAttribute('aria-expanded', 'true');
        }

        function closeMenu() {
            if (!open) return;
            open = false;
            sidebar.style.transform = hiddenTransform;
            overlay.style.opacity = '0';
            document.body.classList.remove('overflow-hidden');
            document.body.style.overflow = '';
            document.body.style.overflowY = 'auto';
            setTimeout(function() {
                sidebar.style.display = 'none';
                overlay.style.display = 'none';
                document.body.classList.remove('overflow-hidden');
            }, 300);
            if (barsIcon) barsIcon.style.display = 'block';
            if (timesIcon) timesIcon.style.display = 'none';
            toggle.setAttribute('aria-expanded', 'false');
        }

        toggle.addEventListener('click', function(e) { e.stopPropagation(); open ? closeMenu() : openMenu(); });
        if (closeBtn) closeBtn.addEventListener('click', function(e) { e.stopPropagation(); closeMenu(); });
        overlay.addEventListener('click', closeMenu);
        sidebar.querySelectorAll('a').forEach(function(a) { a.addEventListener('click', closeMenu); });
        document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && open) closeMenu(); });
        window.addEventListener('resize', function() { if (window.innerWidth >= 1024 && open) closeMenu(); });
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initMobileMenu);
    else initMobileMenu();

    /* Ensure scrolling on load */
    function ensureScroll() {
        var menu = document.getElementById('mobile-menu-sidebar');
        if (!menu || menu.style.display !== 'block') {
            document.body.style.overflow = '';
            document.body.style.overflowY = 'auto';
            document.body.classList.remove('overflow-hidden');
        }
    }
    ensureScroll();
    window.addEventListener('load', ensureScroll);
})();
</script>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\components\unified-navbar.blade.php ENDPATH**/ ?>