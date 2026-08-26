<?php
    $appLocale = app()->getLocale();
    $appRtl = $appLocale === 'ar';
    $user = auth()->user();
    $firstName = explode(' ', trim((string) ($user?->name ?? '')))[0] ?? '';
    $avatarUrl = $user?->avatarDisplayUrl() ?? \App\Models\User::placeholderAvatarUrl();
    $brandLogoUrl = \App\Services\AdminPanelBranding::logoPublicUrl();
    $brandLogoFallback = \App\Services\AdminPanelBranding::inlineFallbackDataUri();

    $navItems = [
        ['route' => 'dashboard', 'match' => ['dashboard', 'student.school.*'], 'label' => __('student_timeline.nav_home'), 'icon' => 'home.svg'],
        ['route' => 'student.learn.index', 'match' => ['student.learn.*'], 'label' => __('student_timeline.nav_learn'), 'icon' => 'lessons.svg', 'ui' => 'show_private_lessons'],
        ['route' => 'student.classes.index', 'match' => ['student.classes.*'], 'label' => __('student_timeline.nav_classes'), 'icon' => 'classes.svg', 'ui' => 'show_classes'],
        ['route' => 'calendar', 'match' => ['calendar', 'calendar.events'], 'label' => __('student_timeline.calendar'), 'fa' => 'fas fa-calendar-alt'],
        ['route' => 'student.live-sessions.index', 'match' => ['student.live-sessions.*', 'student.live-recordings.*'], 'label' => __('student_timeline.nav_live_sessions'), 'fa' => 'fas fa-broadcast-tower', 'ui' => 'show_live_broadcast'],
        ['route' => 'student.private-messages.index', 'match' => ['student.private-messages.*'], 'label' => __('student_timeline.nav_feed'), 'icon' => 'community.svg'],
        ['route' => 'student.private-lectures.index', 'match' => ['student.private-lectures.*', 'student.one-to-one-sessions.*'], 'label' => __('student_timeline.nav_lessons'), 'icon' => 'lessons.svg', 'ui' => 'show_private_lessons'],
        ['route' => 'student.service-entitlements.index', 'match' => ['student.service-entitlements.*'], 'label' => __('student_timeline.nav_progress'), 'icon' => 'credits.svg', 'ui' => 'show_entitlements'],
        ['route' => 'referrals.index', 'match' => ['referrals.*'], 'label' => __('student_timeline.nav_referrals'), 'fa' => 'fas fa-user-friends', 'ui' => 'show_referrals'],
        ['route' => 'student.library.files', 'match' => ['student.library.home', 'student.library.files', 'student.library.materials'], 'label' => __('student_timeline.lib_files_title'), 'fa' => 'fas fa-folder-open', 'ui' => 'show_libraries'],
        ['route' => 'student.library.curriculum', 'match' => ['student.library.curriculum', 'curriculum-library.*'], 'label' => __('student_timeline.nav_library_curriculum'), 'fa' => 'fas fa-sitemap', 'ui' => 'show_libraries'],
        ['route' => 'student.library.videos', 'match' => ['student.library.videos'], 'label' => __('student_timeline.nav_library_videos'), 'fa' => 'fas fa-film', 'ui' => 'show_libraries'],
        ['route' => 'student.assignments.index', 'match' => ['student.assignments.*'], 'label' => __('student_timeline.nav_assignments'), 'fa' => 'fas fa-tasks', 'ui' => 'show_assignments', 'needs_libraries' => true],
        ['route' => 'student.lectures.index', 'match' => ['student.lectures.*'], 'label' => __('student_timeline.nav_lectures'), 'fa' => 'fas fa-chalkboard', 'ui' => 'show_libraries'],
        ['route' => 'orders.index', 'match' => ['orders.*'], 'label' => __('student_timeline.nav_orders'), 'fa' => 'fas fa-receipt', 'ui' => 'show_orders'],
        // أقسام نظام الكورسات — مخفية افتراضياً عبر config/student_ui.php
        ['route' => 'student.exams.index', 'match' => ['student.exams.*'], 'label' => __('student_timeline.nav_exams'), 'fa' => 'fas fa-file-alt', 'ui' => 'show_exams'],
        ['route' => 'student.invoices.index', 'match' => ['student.invoices.*'], 'label' => __('student_timeline.nav_invoices'), 'fa' => 'fas fa-file-invoice-dollar', 'ui' => 'show_invoices'],
        ['route' => 'student.wallet.index', 'match' => ['student.wallet.*'], 'label' => __('student_timeline.nav_wallet'), 'fa' => 'fas fa-wallet', 'ui' => 'show_wallet'],
        ['route' => 'student.certificates.index', 'match' => ['student.certificates.*'], 'label' => __('student_timeline.nav_certificates'), 'fa' => 'fas fa-certificate', 'ui' => 'show_certificates'],
        ['route' => 'student.support.index', 'match' => ['student.support.*'], 'label' => __('student_timeline.nav_support'), 'fa' => 'fas fa-headset', 'ui' => 'show_support'],
        ['route' => 'notifications', 'match' => ['notifications*'], 'label' => __('student_timeline.nav_messages'), 'icon' => 'notifications.svg', 'ui' => 'show_notifications'],
        ['route' => 'settings', 'match' => ['settings'], 'label' => __('student_timeline.nav_settings'), 'icon' => 'settings.svg', 'ui' => 'show_settings'],
    ];

    if ($user?->isAdmin() && Route::has('admin.dashboard')) {
        $navItems[] = [
            'route' => 'admin.dashboard',
            'match' => ['admin.*'],
            'label' => __('student.admin_panel'),
            'fa' => 'fas fa-shield-alt',
        ];
    }
?>
<!DOCTYPE html>
<html lang="<?php echo e($appLocale); ?>" dir="<?php echo e($appRtl ? 'rtl' : 'ltr'); ?>" class="<?php echo e($appRtl ? 'st-rtl' : 'st-ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name')); ?> — <?php echo $__env->yieldContent('title', __('student_timeline.timeline')); ?></title>
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Poppins:wght@400;500;600;700&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo e(route('assets.student-timeline.css')); ?>?v=st-cal-1">
    <script>
        (function () {
            try {
                var mobile = window.matchMedia('(max-width: 768px)').matches;
                if (!mobile && localStorage.getItem('st-rail-open') === '1') {
                    document.documentElement.classList.add('st-rail-pref-open');
                }
            } catch (e) {}
        })();
    </script>
    <?php
        $showContentProtection = ! empty(trim((string) ($__env->yieldContent('enable-content-protection') ?? '')));
    ?>
    <?php if($showContentProtection): ?>
        <script>
            window.Laravel = { user: { name: '<?php echo e(auth()->check() ? auth()->user()->name : "زائر"); ?>' } };
        </script>
        <script src="<?php echo e(versioned_asset('js/platform-protection.js')); ?>"></script>
    <?php endif; ?>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="st-dash">

<script>
(function () {
  try {
    if (localStorage.getItem('st-rail-open') === '1') {
      window.__stRailPreferOpen = true;
    }
  } catch (e) {}
})();
</script>

<div class="st-shell<?php echo e($appRtl ? ' is-rtl' : ' is-ltr'); ?><?php echo e(View::hasSection('events') ? ' has-events' : ' is-wide'); ?>" id="stShell">
    <script>
    (function () {
        var shell = document.getElementById('stShell');
        if (!shell) return;
        try {
            if (window.matchMedia('(min-width: 769px)').matches && localStorage.getItem('st-rail-open') === '1') {
                shell.classList.add('is-rail-open');
            }
        } catch (e) {}
    })();
    </script>
    <button type="button" class="st-rail-backdrop" id="stRailBackdrop" aria-label="<?php echo e(__('student_timeline.close_sidebar')); ?>" tabindex="-1"></button>
    <aside class="st-rail" id="stRail" aria-label="<?php echo e(__('student_timeline.nav_home')); ?>">
        <div class="st-rail__head">
            <a href="<?php echo e(route('dashboard')); ?>" class="st-rail__brand" title="<?php echo e(config('app.name')); ?>">
                <span class="st-rail__mark">
                    <img src="<?php echo e($brandLogoUrl); ?>" alt="<?php echo e(config('app.name')); ?>" class="st-rail__logo" width="40" height="40" loading="eager" decoding="async" onerror="this.onerror=null;this.src='<?php echo e($brandLogoFallback); ?>';">
                </span>
                <span class="st-rail__brand-name"><?php echo e(config('app.name')); ?></span>
            </a>
            <button type="button" class="st-rail__toggle" id="stRailToggle" aria-expanded="false" aria-controls="stRail" title="<?php echo e(__('student_timeline.toggle_sidebar')); ?>">
                <i class="fas fa-angles-<?php echo e($appRtl ? 'left' : 'right'); ?>" aria-hidden="true"></i>
                <span class="st-rail__toggle-text"><?php echo e(__('student_timeline.open_sidebar')); ?></span>
            </button>
        </div>

        <a href="<?php echo e(route('profile')); ?>" class="st-rail__profile" title="<?php echo e(__('student_timeline.profile')); ?>">
            <img src="<?php echo e($avatarUrl); ?>" alt="" class="st-rail__avatar" width="40" height="40">
            <div class="st-rail__who">
                <span class="st-rail__name"><?php echo e($firstName); ?></span>
                <span class="st-rail__role"><?php echo e(__('student_timeline.student_role')); ?></span>
            </div>
        </a>

        <nav class="st-rail__nav">
            <?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    if (! Route::has($item['route'])) {
                        continue;
                    }
                    if (! empty($item['ui']) && ! student_ui($item['ui'], true)) {
                        continue;
                    }
                    if (! empty($item['needs_libraries']) && ! student_ui('show_libraries', true)) {
                        continue;
                    }
                    $href = route($item['route']);
                    $active = request()->routeIs(...$item['match']);
                ?>
                <a href="<?php echo e($href); ?>" class="st-rail__link <?php echo e($active ? 'is-active' : ''); ?>" title="<?php echo e($item['label']); ?>" aria-label="<?php echo e($item['label']); ?>">
                    <span class="st-rail__icon-box">
                        <?php if(! empty($item['icon'])): ?>
                            <img class="st-rail__icon" src="<?php echo e(asset('img/student-timeline/nav/'.$item['icon'])); ?>" alt="" width="24" height="24">
                        <?php elseif(! empty($item['fa'])): ?>
                            <i class="<?php echo e($item['fa']); ?>" aria-hidden="true" style="font-size:16px;opacity:.88"></i>
                        <?php endif; ?>
                    </span>
                    <span class="st-rail__label"><?php echo e($item['label']); ?></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>

        <div class="st-rail__foot" hidden aria-hidden="true"></div>
    </aside>

    <main class="st-main">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <aside class="st-events" aria-label="<?php echo e(__('student_timeline.events')); ?>">
        <?php echo $__env->yieldContent('events'); ?>
    </aside>
</div>

<script>
(function () {
    var shell = document.getElementById('stShell');
    var rail = document.getElementById('stRail');
    var btn = document.getElementById('stRailToggle');
    var backdrop = document.getElementById('stRailBackdrop');
    if (!shell || !btn) return;

    var isRtl = document.documentElement.getAttribute('dir') === 'rtl';
    var openLabel = <?php echo json_encode(__('student_timeline.open_sidebar'), 15, 512) ?>;
    var closeLabel = <?php echo json_encode(__('student_timeline.close_sidebar'), 15, 512) ?>;
    var mq = window.matchMedia('(max-width: 768px)');

    function isMobile() {
        return mq.matches;
    }

    function isOpen() {
        return shell.classList.contains('is-rail-open');
    }

    function syncToggleIcon(open) {
        var mobileIcon = open ? 'fas fa-xmark' : 'fas fa-bars';
        var icon = btn.querySelector('i');
        var text = btn.querySelector('.st-rail__toggle-text');
        if (icon) {
            if (isMobile()) {
                icon.className = mobileIcon;
            } else {
                icon.className = open
                    ? ('fas fa-angles-' + (isRtl ? 'right' : 'left'))
                    : ('fas fa-angles-' + (isRtl ? 'left' : 'right'));
            }
        }
        if (text) text.textContent = open ? closeLabel : openLabel;
        var topMenu = document.getElementById('stTopMenu');
        if (topMenu) {
            var topIcon = topMenu.querySelector('i');
            if (topIcon && isMobile()) topIcon.className = mobileIcon;
            topMenu.setAttribute('aria-expanded', open ? 'true' : 'false');
        }
    }

    function setScrollLock(locked) {
        document.documentElement.classList.toggle('st-rail-lock', locked);
        document.body.classList.toggle('st-rail-lock', locked);
    }

    function setOpen(open, persist) {
        shell.classList.toggle('is-rail-open', open);
        document.documentElement.classList.toggle('st-rail-pref-open', open && !isMobile());
        setScrollLock(open && isMobile());
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (rail) rail.setAttribute('aria-hidden', isMobile() && !open ? 'true' : 'false');
        syncToggleIcon(open);

        if (persist !== false && !isMobile()) {
            try { localStorage.setItem('st-rail-open', open ? '1' : '0'); } catch (e) {}
        }
    }

    var preferOpen = false;
    try { preferOpen = localStorage.getItem('st-rail-open') === '1' || window.__stRailPreferOpen === true; } catch (e) {}
    setOpen(isMobile() ? false : (shell.classList.contains('is-rail-open') || preferOpen), false);

    btn.addEventListener('click', function () {
        setOpen(!isOpen());
    });

    var topMenu = document.getElementById('stTopMenu');
    if (topMenu) {
        topMenu.addEventListener('click', function () {
            setOpen(!isOpen());
        });
    }

    shell.addEventListener('click', function (e) {
        if (!isMobile() || !isOpen()) return;
        var link = e.target.closest('.st-rail a[href]');
        if (link) setOpen(false);
    });

    if (backdrop) {
        backdrop.addEventListener('click', function () { setOpen(false); });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isOpen()) {
            setOpen(false);
        }
    });

    function onViewportChange() {
        if (isMobile()) {
            setOpen(false, false);
        } else {
            var stored = false;
            try { stored = localStorage.getItem('st-rail-open') === '1'; } catch (e) {}
            setOpen(stored, false);
        }
        syncToggleIcon(isOpen());
    }

    if (typeof mq.addEventListener === 'function') mq.addEventListener('change', onViewportChange);
    else if (typeof mq.addListener === 'function') mq.addListener(onViewportChange);
})();
</script>
<?php echo $__env->yieldPushContent('scripts'); ?>
<?php echo $__env->make('partials.timezone-sync', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH /Users/cityphone/Documents/glottical/resources/views/layouts/student-timeline.blade.php ENDPATH**/ ?>