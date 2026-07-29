(function () {
    var nav = document.getElementById('sana-nav');
    var progress = document.getElementById('sana-scroll-progress');
    var toggle = document.getElementById('sana-mobile-toggle');
    var menu = document.getElementById('sana-mobile-menu');
    var backdrop = document.getElementById('sana-mobile-backdrop');

    function onScroll() {
        var y = window.scrollY || document.documentElement.scrollTop;
        var catalogPage = document.body.classList.contains('sana-courses-page');
        if (nav) {
            nav.classList.toggle('is-scrolled', y > 20);
            if (catalogPage) {
                nav.classList.add('is-solid');
                nav.classList.remove('sana-nav--hero');
            } else {
                nav.classList.toggle('is-solid', y > 60);
                nav.classList.toggle('sana-nav--hero', y <= 60);
            }
        }
        var h = document.documentElement.scrollHeight - window.innerHeight;
        if (progress) progress.style.width = (h > 0 ? (y / h) * 100 : 0) + '%';
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    function setMenuOpen(open) {
        if (!menu || !toggle) return;
        menu.classList.toggle('is-open', open);
        menu.setAttribute('aria-hidden', open ? 'false' : 'true');
        nav?.classList.toggle('is-menu-open', open);
        document.body.classList.toggle('sana-menu-open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        var isAr = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';
        toggle.setAttribute('aria-label', open
            ? (isAr ? 'إغلاق القائمة' : 'Close menu')
            : (isAr ? 'فتح القائمة' : 'Open menu'));
        var icon = toggle.querySelector('i');
        if (icon) icon.className = open ? 'fas fa-times' : 'fas fa-bars';
        if (backdrop) backdrop.classList.toggle('is-visible', open);
        if (open) closeLangDropdowns(null);
    }

    if (toggle && menu) {
        toggle.addEventListener('click', function () {
            setMenuOpen(!menu.classList.contains('is-open'));
        });
        menu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () { setMenuOpen(false); });
        });
        backdrop?.addEventListener('click', function () { setMenuOpen(false); });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                setMenuOpen(false);
                closeLangDropdowns(null);
            }
        });
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 992) setMenuOpen(false);
        });
    }

    function closeLangDropdowns(except) {
        document.querySelectorAll('[data-lang-dropdown].is-open').forEach(function (dd) {
            if (dd === except) return;
            dd.classList.remove('is-open');
            var btn = dd.querySelector('.sana-nav__lang-btn');
            if (btn) btn.setAttribute('aria-expanded', 'false');
        });
    }

    document.querySelectorAll('[data-lang-dropdown]').forEach(function (dd) {
        var btn = dd.querySelector('.sana-nav__lang-btn');
        if (!btn) return;
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var open = !dd.classList.contains('is-open');
            if (open) setMenuOpen(false);
            closeLangDropdowns(open ? dd : null);
            dd.classList.toggle('is-open', open);
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        dd.querySelectorAll('.sana-nav__lang-option').forEach(function (link) {
            link.addEventListener('click', function () { closeLangDropdowns(null); });
        });
    });

    document.addEventListener('click', function () { closeLangDropdowns(null); });

    document.querySelectorAll('.sana-reveal').forEach(function (el) {
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function (entries, io) {
                entries.forEach(function (e) {
                    if (e.isIntersecting) { e.target.classList.add('is-visible'); io.unobserve(e.target); }
                });
            }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' }).observe(el);
        } else {
            el.classList.add('is-visible');
        }
    });

    document.querySelectorAll('[data-sana-counter]').forEach(function (el) {
        var target = parseInt(el.getAttribute('data-sana-counter'), 10) || 0;
        var suffix = el.getAttribute('data-sana-suffix') || '';
        var started = false;
        function animate() {
            if (started) return;
            started = true;
            var start = 0, dur = 1400, t0 = null;
            function step(ts) {
                if (!t0) t0 = ts;
                var p = Math.min((ts - t0) / dur, 1);
                var ease = 1 - Math.pow(1 - p, 3);
                el.textContent = Math.floor(start + (target - start) * ease).toLocaleString('ar-EG') + suffix;
                if (p < 1) requestAnimationFrame(step);
            }
            requestAnimationFrame(step);
        }
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function (entries, io) {
                if (entries[0].isIntersecting) { animate(); io.disconnect(); }
            }, { threshold: 0.3 }).observe(el);
        } else { animate(); }
    });

    document.getElementById('sana-faq')?.addEventListener('click', function (e) {
        var btn = e.target.closest('.sana-faq-q');
        if (!btn) return;
        var item = btn.closest('.sana-faq-item');
        var open = item.classList.contains('is-open');
        document.querySelectorAll('.sana-faq-item').forEach(function (i) {
            i.classList.remove('is-open');
            i.querySelector('.sana-faq-q')?.setAttribute('aria-expanded', 'false');
        });
        if (!open) {
            item.classList.add('is-open');
            btn.setAttribute('aria-expanded', 'true');
        }
    });
})();
