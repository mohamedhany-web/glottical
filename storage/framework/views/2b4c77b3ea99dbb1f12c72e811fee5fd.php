<?php
    $isRtl = app()->getLocale() === 'ar';
    $navbarLogoUrl = $navbarLogoUrl ?? \App\Services\AdminPanelBranding::logoPublicUrl();
    $navbarBrandTagline = $navbarBrandTagline ?? \App\Services\PublicFooterSettings::payload()['brand_tagline'];
    $isHome = \Illuminate\Support\Facades\Route::currentRouteName() === 'home';
    $langSwitch = fn (string $lang) => request()->fullUrlWithQuery(array_merge(request()->query(), ['lang' => $lang]));
    $megaCategories = collect(range(1, 6))->map(function (int $i) {
        $iconKey = 'public.home_category_fallback_'.$i.'_icon';
        $iconRaw = __($iconKey);
        $icon = is_string($iconRaw) && preg_match('/\bfa-[a-z0-9-]+\b/i', $iconRaw, $m)
            ? strtolower($m[0])
            : 'fa-folder';

        return [
            'name' => __('public.home_category_fallback_'.$i.'_name'),
            'desc' => __('public.home_category_fallback_'.$i.'_desc'),
            'icon' => $icon,
            'url' => route('public.services.index'),
        ];
    });
    $navLinks = [
        ['href' => route('public.courses'), 'icon' => 'fa-graduation-cap', 'label' => __('public.courses')],
        ['href' => route('public.instructors.index'), 'icon' => 'fa-chalkboard-teacher', 'label' => __('landing.nav.instructors')],
        ['href' => route('public.about'), 'icon' => 'fa-circle-info', 'label' => __('public.about')],
    ];
?>
<nav id="navbar"
     class="fixed top-0 inset-x-0 z-[999] transition-all duration-500 <?php echo e($isHome ? 'nav-home' : ''); ?>"
     style="font-family: 'Cairo', 'Tajawal', 'IBM Plex Sans Arabic', system-ui, sans-serif;">

    <div class="max-w-7xl mx-auto px-3 sm:px-5 lg:px-8 relative">
        <div class="flex items-center justify-between gap-2 sm:gap-3 h-14 sm:h-[60px] w-full min-w-0">

            
            <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-2 sm:gap-3 group min-w-0 max-w-[min(100%,12rem)] sm:max-w-[14rem] lg:max-w-none shrink-0 relative z-20">
                <?php if(!empty($navbarLogoUrl)): ?>
                    <span class="relative h-9 w-9 flex-shrink-0 overflow-hidden rounded-full ring-1 ring-white/25 shadow-md [box-shadow:0_3px_12px_-4px_rgba(0,0,0,.3)]">
                        <img src="<?php echo e($navbarLogoUrl); ?>" alt="<?php echo e(config('app.name')); ?>" class="h-full w-full object-cover object-center" decoding="async">
                    </span>
                <?php else: ?>
                    <div class="relative w-9 h-9 rounded-full flex items-center justify-center shadow-md transition-shadow duration-300" style="background:#F6C945;box-shadow:0 3px 14px -4px rgba(246,201,69,.38)">
                        <span class="text-[#082B63] font-black text-base select-none">G</span>
                    </div>
                <?php endif; ?>
                <div class="flex flex-col leading-none min-w-0">
                    <span class="nav-brand text-[15px] sm:text-[17px] font-bold tracking-tight truncate">Glottical</span>
                    <span class="nav-tag text-[9px] sm:text-[11px] font-medium mt-0.5 leading-tight truncate"><?php echo e($navbarBrandTagline); ?></span>
                </div>
            </a>

            
            <div class="hidden lg:flex flex-1 min-w-0 items-center justify-center px-2 xl:px-4 relative z-10">
                <div class="flex items-center gap-1.5 xl:gap-2 max-w-full overflow-x-auto overflow-y-visible no-scrollbar py-1 -my-1 justify-center">
                    <div class="relative group/mega">
                        <button type="button" class="nav-link relative px-2.5 xl:px-3.5 py-2 rounded-xl text-[13px] xl:text-sm font-semibold transition-all duration-200 flex items-center gap-1.5 whitespace-nowrap shrink-0">
                            <i class="fas fa-th-large text-[12px] opacity-70"></i>
                            <span><?php echo e(__('landing.academy.nav_categories')); ?></span>
                            <i class="fas fa-chevron-down text-[10px] opacity-60"></i>
                        </button>
                        <div class="mega-panel absolute top-full <?php echo e($isRtl ? 'right-0' : 'left-1/2 -translate-x-1/2'); ?> mt-2 w-[min(720px,calc(100vw-2rem))] rounded-2xl shadow-2xl shadow-black/40 opacity-0 invisible group-hover/mega:opacity-100 group-hover/mega:visible group-focus-within/mega:opacity-100 group-focus-within/mega:visible translate-y-1 group-hover/mega:translate-y-0 group-focus-within/mega:translate-y-0 transition-all duration-200 z-[1000] p-4 text-start border border-white/12 bg-slate-950/95 backdrop-blur-xl">
                            <div class="grid sm:grid-cols-2 gap-2">
                                <?php $__currentLoopData = $megaCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e($mc['url']); ?>" class="flex gap-3 rounded-xl p-3 transition hover:bg-white/8 border border-transparent hover:border-white/10">
                                        <span class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 bg-acad-cyan/15 text-acad-cyan"><i class="fas <?php echo e($mc['icon']); ?>"></i></span>
                                        <span class="min-w-0">
                                            <span class="block font-extrabold text-sm text-white"><?php echo e($mc['name']); ?></span>
                                            <span class="block text-xs line-clamp-2 mt-0.5 text-white/55"><?php echo e($mc['desc']); ?></span>
                                        </span>
                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <a href="<?php echo e(route('public.courses')); ?>" class="mt-3 block text-center text-sm font-extrabold py-2 text-acad-yellow hover:text-acad-cyan"><?php echo e(__('landing.academy.mega_see_all')); ?></a>
                        </div>
                    </div>
                    <?php $__currentLoopData = $navLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($link['href']); ?>"
                       class="nav-link relative px-2.5 xl:px-3.5 py-2 rounded-xl text-[13px] xl:text-sm font-semibold transition-all duration-200 flex items-center gap-1.5 whitespace-nowrap shrink-0">
                        <i class="fas <?php echo e($link['icon']); ?> text-[12px] opacity-70"></i>
                        <span><?php echo e($link['label']); ?></span>
                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            
            <div class="hidden lg:flex items-center gap-1.5 xl:gap-2 shrink-0 justify-end relative z-20 min-w-0">
                <?php if($isHome): ?>
                <button type="button"
                        class="nav-search-btn inline-flex items-center justify-center w-10 h-10 rounded-lg font-semibold text-sm transition-all duration-200 backdrop-blur-sm border border-white/10 text-white hover:bg-white/10"
                        data-open-search-btn
                        aria-label="<?php echo e(__('landing.academy.nav_search')); ?>">
                    <i class="fas fa-search text-sm"></i>
                </button>
                <?php endif; ?>
                <div class="flex shrink-0 items-stretch rounded-xl border border-white/15 overflow-hidden text-xs font-extrabold nav-lang-switch min-w-[6.5rem]">
                    <a href="<?php echo e($langSwitch('ar')); ?>" class="nav-lang-item flex-1 flex items-center justify-center px-2 sm:px-3 py-2.5 min-w-[2.75rem] text-center border-e border-white/15 <?php echo e($isRtl ? 'is-active' : ''); ?>" hreflang="ar">عربي</a>
                    <a href="<?php echo e($langSwitch('en')); ?>" class="nav-lang-item flex-1 flex items-center justify-center px-2 sm:px-3 py-2.5 min-w-[2.25rem] text-center <?php echo e(! $isRtl ? 'is-active' : ''); ?>" hreflang="en">EN</a>
                </div>
                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('my-courses.index')); ?>"
                       class="nav-mycourses inline-flex items-center justify-center w-10 h-10 rounded-lg font-semibold text-sm transition-all duration-200 backdrop-blur-sm border border-white/10"
                       title="<?php echo e(__('landing.academy.nav_my_learning')); ?>">
                        <i class="fas fa-play-circle text-sm"></i>
                        <span class="sr-only"><?php echo e(__('landing.academy.nav_my_learning')); ?></span>
                    </a>
                    <a href="<?php echo e(url('/dashboard')); ?>"
                       class="nav-dash inline-flex items-center gap-2 px-3.5 py-2 rounded-lg font-semibold text-sm transition-all duration-200 backdrop-blur-sm">
                        <i class="fas fa-th-large text-xs opacity-70"></i>
                        <?php echo e(__('landing.nav.dashboard')); ?>

                    </a>
                <?php endif; ?>
                <?php if(auth()->guard()->guest()): ?>
                    <a href="<?php echo e(route('login')); ?>"
                       class="nav-login px-2.5 xl:px-3 py-2 rounded-lg font-semibold text-[13px] xl:text-sm transition-all duration-200 whitespace-nowrap shrink-0">
                        <?php echo e(__('landing.nav.login')); ?>

                    </a>
                    <a href="<?php echo e(route('register')); ?>"
                       class="nav-cta inline-flex items-center gap-1.5 px-3.5 sm:px-4 py-2.5 rounded-xl font-extrabold text-[13px] xl:text-sm transition-all duration-300 tracking-tight whitespace-nowrap shrink-0 max-w-[9.5rem] sm:max-w-none shadow-lg shadow-black/25">
                        <?php echo e(__('landing.academy.nav_join')); ?>

                        <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?> text-[11px]"></i>
                    </a>
                <?php endif; ?>
            </div>

            <div class="flex items-center gap-1.5 lg:hidden shrink-0 relative z-20">
                <?php if($isHome): ?>
                <button type="button"
                        class="nav-search-btn w-10 h-10 rounded-xl flex items-center justify-center border border-white/12 bg-white/[0.06] text-white/90 hover:bg-white/10 hover:border-white/20 transition"
                        data-open-search-btn
                        aria-label="<?php echo e(__('landing.academy.nav_search')); ?>">
                    <i class="fas fa-search text-lg"></i>
                </button>
                <?php endif; ?>
                <button type="button"
                        id="mobile-menu-toggle"
                        class="nav-mobile-btn w-10 h-10 rounded-xl flex items-center justify-center border border-white/12 bg-white/[0.06] text-white/90 hover:bg-white/10 hover:border-white/20 active:scale-[0.97] transition-all duration-200"
                        aria-label="<?php echo e(__('landing.academy.nav_menu')); ?>"
                        aria-expanded="false"
                        aria-controls="mobile-menu-sidebar">
                    <span id="menu-bars-icon"><i class="fas fa-bars text-lg"></i></span>
                    <span id="menu-times-icon" style="display:none;"><i class="fas fa-times text-lg"></i></span>
                </button>
            </div>
        </div>
    </div>
</nav>


<?php if(! request()->routeIs('home')): ?>
<div class="navbar-spacer h-14 sm:h-[60px]"></div>
<?php endif; ?>

<?php echo $__env->make('partials.mobile-nav-drawer', [
    'isRtl' => $isRtl,
    'isHome' => $isHome,
    'navbarLogoUrl' => $navbarLogoUrl,
    'navbarBrandTagline' => $navbarBrandTagline,
    'langSwitch' => $langSwitch,
    'megaCategories' => $megaCategories,
    'navLinks' => $navLinks,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<style>
/* Navbar — سطح زجاجي داكن موحّد مع صفحة الأكاديمية (#050b18) */
#navbar {
    background: linear-gradient(180deg, rgba(5, 11, 24, 0.97) 0%, rgba(8, 22, 48, 0.94) 100%);
    backdrop-filter: blur(22px) saturate(185%);
    -webkit-backdrop-filter: blur(22px) saturate(185%);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 8px 32px -16px rgba(0, 0, 0, 0.55), 0 0 0 1px rgba(0, 212, 255, 0.06) inset;
    transition: background .4s cubic-bezier(0.22, 1, 0.36, 1), box-shadow .45s ease, border-color .35s ease, backdrop-filter .45s ease;
}
#navbar:not(.nav-home)::after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 2px;
    pointer-events: none;
    background: linear-gradient(90deg, transparent 0%, rgba(245, 184, 0, 0.4) 22%, rgba(0, 212, 255, 0.45) 50%, rgba(245, 184, 0, 0.4) 78%, transparent 100%);
    opacity: 0.42;
}
#navbar.nav-home {
    background: linear-gradient(180deg, rgba(5,11,24,0.35) 0%, rgba(5,11,24,0.08) 55%, transparent 100%) !important;
    border-bottom: 1px solid rgba(255,255,255,0.06) !important;
    box-shadow: 0 4px 30px -20px rgba(0, 212, 255, 0.12) !important;
}
#navbar.nav-home::after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 2px;
    pointer-events: none;
    background: linear-gradient(90deg, transparent 0%, rgba(245,184,0,0.45) 22%, rgba(0,212,255,0.5) 50%, rgba(245,184,0,0.45) 78%, transparent 100%);
    opacity: 0.35;
    transition: opacity .5s ease, filter .5s ease;
}
#navbar.nav-home.nav-solid {
    background: linear-gradient(180deg, rgba(5, 11, 24, 0.97) 0%, rgba(8, 22, 48, 0.94) 100%) !important;
    backdrop-filter: blur(22px) saturate(185%) !important;
    -webkit-backdrop-filter: blur(22px) saturate(185%) !important;
    border-bottom: 1px solid rgba(255,255,255,0.1) !important;
    box-shadow: 0 8px 32px -12px rgba(0,0,0,.65), 0 0 0 1px rgba(0,212,255,0.08) inset !important;
}
#navbar.nav-home.nav-solid::after {
    opacity: 1;
    filter: drop-shadow(0 0 12px rgba(245,184,0,0.35));
}

/* Home navbar: text colors switch */
#navbar .nav-brand { color: #ffffff; }
#navbar .nav-tag { color: rgba(255,255,255,.62); }
#navbar .nav-link { color: rgba(255,255,255,.82); }
#navbar .nav-link:hover { color: #ffffff; background: rgba(255,255,255,.06); }
#navbar .nav-login { color: rgba(255,255,255,.86); }
#navbar .nav-login:hover { color: #ffffff; background: rgba(255,255,255,.06); }
#navbar .nav-mycourses { color: #ffffff; background: rgba(255,255,255,.08); }
#navbar .nav-mycourses:hover { background: rgba(255,255,255,.14); }
#navbar .nav-dash { color: #ffffff; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.10); }
#navbar .nav-dash:hover { background: rgba(255,255,255,.14); }
#navbar .nav-cta { background: #F5B800; color: #0B3D91; box-shadow: 0 8px 22px -14px rgba(245,184,0,.45); }
#navbar .nav-cta:hover { transform: translateY(-1px) scale(1.02); box-shadow: 0 14px 32px -14px rgba(245,184,0,.55); }
#navbar .nav-lang-switch { border-color: rgba(255, 255, 255, 0.18); }
#navbar .nav-lang-item { color: rgba(255, 255, 255, 0.78); }
#navbar .nav-lang-item:hover { color: #fff; background: rgba(255, 255, 255, 0.08); }
#navbar .nav-lang-item.is-active { background: rgba(255, 255, 255, 0.15); color: #fff; font-weight: 800; }
#navbar .nav-mobile-btn { color: rgba(255,255,255,.86); }
#navbar .nav-mobile-btn:hover { color: #ffffff; background: rgba(255,255,255,.08); }
#navbar.nav-home .nav-search-btn { color: #ffffff; }

/* When scrolled (home) -> premium dark glass (streaming UI) */
#navbar.nav-home.nav-solid .nav-brand { color: #ffffff; }
#navbar.nav-home.nav-solid .nav-tag { color: rgba(255,255,255,.62); }
#navbar.nav-home.nav-solid .nav-link { color: rgba(255,255,255,.82); }
#navbar.nav-home.nav-solid .nav-link:hover { color: #ffffff; background: rgba(255,255,255,.08); }
#navbar.nav-home.nav-solid .nav-login { color: rgba(255,255,255,.86); }
#navbar.nav-home.nav-solid .nav-login:hover { color: #ffffff; background: rgba(255,255,255,.08); }
#navbar.nav-home.nav-solid .nav-mycourses { color: #ffffff; background: rgba(255,255,255,.10); border-color: rgba(255,255,255,.14); }
#navbar.nav-home.nav-solid .nav-mycourses:hover { background: rgba(255,255,255,.16); }
#navbar.nav-home.nav-solid .nav-dash { color: #ffffff; background: rgba(255,255,255,.10); border: 1px solid rgba(255,255,255,.12); }
#navbar.nav-home.nav-solid .nav-dash:hover { background: rgba(255,255,255,.16); }
#navbar.nav-home.nav-solid .nav-search-btn { color: #ffffff; border-color: rgba(255,255,255,.14); }
#navbar.nav-home.nav-solid .nav-search-btn:hover { background: rgba(255,255,255,.12); }
#navbar.nav-home.nav-solid .nav-mobile-btn { color: rgba(255,255,255,.86); }
#navbar.nav-home.nav-solid .nav-mobile-btn:hover { color: #ffffff; background: rgba(255,255,255,.08); }
#navbar.nav-home .nav-lang-item { color: rgba(255,255,255,.78); }
#navbar.nav-home .nav-lang-item:hover { color: #fff; background: rgba(255,255,255,.08); }
#navbar.nav-home .nav-lang-item.is-active { background: rgba(255,255,255,.15); color: #fff; }
#navbar.nav-home.nav-solid .nav-lang-switch { border-color: rgba(255,255,255,.18) !important; }
#navbar.nav-home.nav-solid .nav-lang-item { color: rgba(255,255,255,.78); }
#navbar.nav-home.nav-solid .nav-lang-item:hover { color: #fff; background: rgba(255,255,255,.08); }
#navbar.nav-home.nav-solid .nav-lang-item.is-active { background: rgba(255,255,255,.15); color: #fff; font-weight: 800; }
/* شريط أعلى الصفحة (غير الرئيسية): تدرّج خفيف عند أعلى التمرير */
#navbar.nav-transparent:not(.nav-home) {
    background: linear-gradient(180deg, rgba(5, 11, 24, 0.9) 0%, rgba(8, 22, 48, 0.82) 100%) !important;
    backdrop-filter: blur(20px) saturate(180%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(180%) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    box-shadow: 0 4px 28px -18px rgba(0, 0, 0, 0.5) !important;
}
/* الصفحة الرئيسية: شريط شفاف يذوب في الهيرو (لا يغطي الصورة بالأزرق) */
#navbar.nav-home.nav-transparent {
    background: linear-gradient(180deg, rgba(5, 11, 24, 0.55) 0%, rgba(5, 11, 24, 0.18) 50%, rgba(5, 11, 24, 0.02) 100%) !important;
    backdrop-filter: blur(18px) saturate(165%) !important;
    -webkit-backdrop-filter: blur(18px) saturate(165%) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.07) !important;
    box-shadow: 0 12px 40px -28px rgba(0, 0, 0, 0.55), 0 0 40px -20px rgba(0, 212, 255, 0.08) !important;
}
#navbar.nav-solid:not(.nav-home) {
    background: linear-gradient(180deg, rgba(5, 11, 24, 0.99) 0%, rgba(10, 26, 52, 0.97) 100%) !important;
    backdrop-filter: blur(22px) saturate(185%) !important;
    -webkit-backdrop-filter: blur(22px) saturate(185%) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
    box-shadow: 0 10px 36px -14px rgba(0, 0, 0, 0.65), 0 0 0 1px rgba(0, 212, 255, 0.07) inset !important;
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
    background: #F5B800;
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.nav-link:hover::after {
    transform: translateX(-50%) scaleX(1);
}

/* ─── Mobile drawer (موحّد مع #050b18) ─── */
.mob-menu-overlay {
    background: rgba(2, 6, 16, 0.72);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    transition: opacity 0.28s ease;
    will-change: opacity;
}
.mob-menu-panel {
    font-family: 'Cairo', 'Tajawal', 'IBM Plex Sans Arabic', system-ui, sans-serif;
    box-shadow: <?php echo e($isRtl ? '-24px' : '24px'); ?> 0 48px rgba(0, 0, 0, 0.55);
    transition: transform 0.32s cubic-bezier(0.32, 0.72, 0, 1);
    overscroll-behavior: contain;
    -webkit-overflow-scrolling: touch;
    will-change: transform;
}
.mob-menu-inner {
    background: linear-gradient(165deg, #0c1a32 0%, #050b18 42%, #081220 100%);
}
.mob-menu-glow {
    background:
        radial-gradient(ellipse 90% 55% at 50% -8%, rgba(0, 212, 255, 0.14), transparent 58%),
        radial-gradient(ellipse 55% 45% at <?php echo e($isRtl ? '0%' : '100%'); ?> 100%, rgba(245, 184, 0, 0.1), transparent 52%);
}
.mob-menu-dots {
    background-image: radial-gradient(rgba(255, 255, 255, 0.045) 1px, transparent 1px);
    background-size: 16px 16px;
}
.mob-menu-safe-top { padding-top: max(1rem, env(safe-area-inset-top)); }
.mob-menu-safe-bottom { padding-bottom: max(0.75rem, env(safe-area-inset-bottom)); }
.mob-menu-scroll { scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.15) transparent; }
.mob-menu-scroll::-webkit-scrollbar { width: 4px; }
.mob-menu-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }
.mob-menu-item,
.mob-menu-search {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    width: 100%;
    padding: 0.7rem 0.85rem;
    border-radius: 0.75rem;
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    transition: background 0.2s ease, color 0.2s ease;
    -webkit-tap-highlight-color: transparent;
}
.mob-menu-item:hover,
.mob-menu-item:focus-visible,
.mob-menu-search:hover {
    color: #fff;
    background: rgba(255, 255, 255, 0.07);
}
.mob-menu-chevron { font-size: 0.65rem; color: rgba(255, 255, 255, 0.22); flex-shrink: 0; }
.mob-menu-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.65rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 0.95rem;
}
.mob-menu-icon--gold { background: rgba(245, 184, 0, 0.12); border: 1px solid rgba(245, 184, 0, 0.22); color: #F5B800; }
.mob-menu-icon--cyan { background: rgba(0, 163, 196, 0.14); border: 1px solid rgba(0, 212, 255, 0.22); color: #00d4ff; }
.mob-menu-icon--blue { background: rgba(59, 130, 246, 0.12); border: 1px solid rgba(96, 165, 250, 0.2); color: #93c5fd; }
.mob-menu-icon--violet { background: rgba(139, 92, 246, 0.12); border: 1px solid rgba(167, 139, 250, 0.2); color: #c4b5fd; }
.mob-menu-icon--slate { background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.1); color: rgba(255, 255, 255, 0.65); }
.mob-menu-cta {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 0.85rem 1rem;
    border-radius: 0.75rem;
    font-weight: 800;
    font-size: 0.95rem;
    text-decoration: none;
    transition: transform 0.15s ease, box-shadow 0.2s ease;
    -webkit-tap-highlight-color: transparent;
}
.mob-menu-cta:active { transform: scale(0.98); }
.mob-menu-cta--primary {
    background: linear-gradient(135deg, #F5B800 0%, #f0a500 100%);
    color: #0B3D91;
    box-shadow: 0 10px 28px -12px rgba(245, 184, 0, 0.55);
}
.mob-menu-close { -webkit-tap-highlight-color: transparent; }
.mob-menu-details .mob-menu-summary { list-style: none; }
.mob-menu-details .mob-menu-summary::-webkit-details-marker { display: none; }
.mob-menu-details[open] .mob-menu-chevron--toggle { transform: rotate(180deg); }
.mob-menu-chevron--toggle { transition: transform 0.2s ease; }
.mob-menu-subitem {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    padding: 0.55rem 0.5rem;
    border-radius: 0.65rem;
    color: rgba(255, 255, 255, 0.85);
    text-decoration: none;
    transition: background 0.2s ease;
    -webkit-tap-highlight-color: transparent;
}
.mob-menu-subitem:hover { background: rgba(255, 255, 255, 0.06); }
.mob-menu-subitem--cta { padding: 0.65rem 0.5rem; margin-top: 0.25rem; }
.mob-menu-subitem--cta:hover { background: rgba(245, 184, 0, 0.08); }
.mob-menu-icon--sm { width: 2rem; height: 2rem; font-size: 0.8rem; }
body.mob-menu-open { overflow: hidden !important; touch-action: none; }

@media (max-width: 380px) {
    .mob-menu-panel { width: min(288px, 92vw) !important; }
}

/* شريط روابط الوسط: إخفاء شريط التمرير مع الإبقاء على السحب */
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.no-scrollbar::-webkit-scrollbar {
    display: none;
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
        var shouldBeSolid = y > 12;
        if (shouldBeSolid !== solid) {
            solid = shouldBeSolid;
            if (solid) { navbar.classList.add('nav-solid'); navbar.classList.remove('nav-transparent'); }
            else { navbar.classList.remove('nav-solid'); navbar.classList.add('nav-transparent'); }
        }
    }
    window.addEventListener('scroll', checkScroll, { passive: true });
    checkScroll();

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
            sidebar.style.display = 'flex';
            overlay.style.display = 'block';
            overlay.setAttribute('aria-hidden', 'false');
            document.body.classList.add('mob-menu-open', 'overflow-hidden');
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
            overlay.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('mob-menu-open', 'overflow-hidden');
            document.body.style.overflow = '';
            document.body.style.overflowY = '';
            setTimeout(function() {
                sidebar.style.display = 'none';
                overlay.style.display = 'none';
                document.body.classList.remove('mob-menu-open', 'overflow-hidden');
            }, 320);
            if (barsIcon) barsIcon.style.display = 'block';
            if (timesIcon) timesIcon.style.display = 'none';
            toggle.setAttribute('aria-expanded', 'false');
        }

        toggle.addEventListener('click', function(e) { e.stopPropagation(); open ? closeMenu() : openMenu(); });
        if (closeBtn) closeBtn.addEventListener('click', function(e) { e.stopPropagation(); closeMenu(); });
        overlay.addEventListener('click', closeMenu);
        sidebar.querySelectorAll('a[href]').forEach(function(a) {
            a.addEventListener('click', function() { closeMenu(); });
        });
        sidebar.querySelectorAll('[data-mob-close-then-search]').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeMenu();
                setTimeout(function() {
                    var searchTriggers = document.querySelectorAll('#navbar [data-open-search-btn]');
                    if (searchTriggers.length) {
                        searchTriggers[0].click();
                        return;
                    }
                    document.dispatchEvent(new CustomEvent('glottical:open-search'));
                }, 330);
            });
        });
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
<?php /**PATH C:\xampp\htdocs\glottical\resources\views/components/unified-navbar.blade.php ENDPATH**/ ?>