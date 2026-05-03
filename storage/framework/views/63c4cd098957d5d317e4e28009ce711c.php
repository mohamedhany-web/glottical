<?php $adminLocale = app()->getLocale(); $adminRtl = $adminLocale === 'ar'; ?>
<!DOCTYPE html>
<html lang="<?php echo e($adminLocale); ?>" dir="<?php echo e($adminRtl ? 'rtl' : 'ltr'); ?>" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', __('auth.dashboard')); ?> - <?php echo e(config('app.name')); ?></title>
    
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        (function() {
            var s = localStorage.getItem('theme');
            var d = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (s === 'dark' || (!s && d)) {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
            }
        })();
    </script>
    <script>
        document.addEventListener('alpine:init', function () {
            Alpine.data('adminNavNotifications', function (config) {
                config = config || {};
                var initialUnread = Number(config.unread) || 0;
                var initialItems = Array.isArray(config.items) ? config.items : [];
                var pollUrl = config.pollUrl || '';
                return {
                    openNotif: false,
                    unread: initialUnread,
                    lastSynced: initialUnread,
                    firstPoll: true,
                    items: initialItems.slice(),
                    pollUrl: pollUrl,
                    audioUnlocked: false,
                    _pollTimer: null,
                    init: function () {
                        var self = this;
                        document.body.addEventListener('click', function () { self.audioUnlocked = true; }, { once: true });
                        document.body.addEventListener('keydown', function () { self.audioUnlocked = true; }, { once: true });
                        this._pollTimer = setInterval(function () { self.poll(); }, 5000);
                        this.poll();
                    },
                    destroy: function () {
                        if (this._pollTimer) clearInterval(this._pollTimer);
                    },
                    poll: async function () {
                        try {
                            var token = document.querySelector('meta[name="csrf-token"]');
                            var res = await fetch(this.pollUrl, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': token ? token.getAttribute('content') : ''
                                },
                                credentials: 'same-origin'
                            });
                            if (!res.ok) return;
                            var d = await res.json();
                            if (!this.firstPoll && d.unread_count > this.lastSynced) {
                                this.playBeep();
                            }
                            this.firstPoll = false;
                            this.lastSynced = d.unread_count;
                            this.unread = d.unread_count;
                            this.items = Array.isArray(d.items) ? d.items : [];
                        } catch (e) { /* ignore */ }
                    },
                    playBeep: function () {
                        if (!this.audioUnlocked) return;
                        try {
                            var Ctx = window.AudioContext || window.webkitAudioContext;
                            if (!Ctx) return;
                            var ctx = new Ctx();
                            var osc = ctx.createOscillator();
                            var gain = ctx.createGain();
                            osc.type = 'sine';
                            osc.frequency.value = 880;
                            gain.gain.setValueAtTime(0.0001, ctx.currentTime);
                            gain.gain.exponentialRampToValueAtTime(0.12, ctx.currentTime + 0.02);
                            gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.22);
                            osc.connect(gain);
                            gain.connect(ctx.destination);
                            osc.start(ctx.currentTime);
                            osc.stop(ctx.currentTime + 0.25);
                            setTimeout(function () { ctx.close(); }, 400);
                        } catch (e) { /* ignore */ }
                    }
                };
            });
        });
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        heading: ['Tajawal', 'IBM Plex Sans Arabic', 'sans-serif'],
                        body: ['IBM Plex Sans Arabic', 'sans-serif'],
                    },
                    colors: {
                        navy: {
                            50: '#f0f4ff', 100: '#dfe6ff', 200: '#c7d6fe',
                            300: '#a4b8fc', 400: '#818cf8', 500: '#6366f1',
                            600: '#4f46e5', 700: '#1E3A8A', 800: '#0F172A',
                            900: '#0B1120', 950: '#060B16',
                        },
                        brand: { DEFAULT: '#1E3A8A', light: '#2563EB', dark: '#1E3A5F' }
                    }
                }
            }
        }
    </script>

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        * { font-family: 'IBM Plex Sans Arabic', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Tajawal', 'IBM Plex Sans Arabic', sans-serif; }
        
        html, body { margin: 0; padding: 0; height: 100%; overflow-x: hidden;
            -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
        [x-cloak] { display: none !important; }

        /* ========== SIDEBAR (فاتح) ========== */
        .admin-sidebar {
            background: #ffffff;
            border-left: 1px solid #e2e8f0;
            box-shadow: 1px 0 12px rgba(0, 0, 0, 0.04);
            transition: width 0.28s cubic-bezier(0.4, 0, 0.2, 1), background 0.3s, border-color 0.3s;
            will-change: width;
        }
        .dark .admin-sidebar {
            background: #1e293b;
            border-left-color: #334155;
            box-shadow: 1px 0 12px rgba(0, 0, 0, 0.3);
        }

        .sidebar-nav {
            scrollbar-width: thin;
            scrollbar-color: rgba(148, 163, 184, 0.4) transparent;
        }
        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.35); border-radius: 10px; }

        .sidebar-link {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.5rem 0.875rem; border-radius: 0.5rem;
            color: #475569;
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative; font-size: 0.8125rem; font-weight: 500;
            white-space: nowrap; overflow: hidden;
        }
        .sidebar-link:hover { background: #f1f5f9; color: #1e293b; }
        .sidebar-link.active {
            background: linear-gradient(135deg, #eff6ff, #f0f9ff);
            color: #1d4ed8; font-weight: 600;
        }
        .sidebar-link.active::before {
            content: ''; position: absolute; top: 0.375rem; bottom: 0.375rem;
            width: 3px; border-radius: 0 4px 4px 0;
            background: linear-gradient(180deg, #3B82F6, #1d4ed8);
        }
        [dir="ltr"] .sidebar-link.active::before { left: 0; }
        [dir="rtl"] .sidebar-link.active::before { right: 0; border-radius: 4px 0 0 4px; }
        .sidebar-link i { width: 1.25rem; text-align: center; font-size: 0.8125rem; flex-shrink: 0;
            transition: color 0.18s ease; }

        .sidebar-group-btn {
            display: flex; align-items: center; justify-content: space-between;
            width: 100%; padding: 0.5rem 0.875rem; border-radius: 0.5rem;
            color: #475569;
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.8125rem; font-weight: 500;
            white-space: nowrap; overflow: hidden;
        }
        .sidebar-group-btn:hover { background: #f1f5f9; color: #1e293b; }
        .sidebar-group-btn i.chevron {
            font-size: 0.5625rem; color: #94a3b8;
            transition: transform 0.22s ease;
        }

        .sidebar-sub-link {
            display: flex; align-items: center; gap: 0.5rem;
            padding: 0.35rem 0.75rem; border-radius: 0.375rem;
            color: #64748b;
            transition: all 0.15s ease;
            font-size: 0.75rem; font-weight: 400;
            white-space: nowrap; overflow: hidden;
        }
        .sidebar-sub-link:hover { background: #f1f5f9; color: #334155; }
        .sidebar-sub-link.active { color: #1d4ed8; background: #eff6ff; font-weight: 600; }
        .sidebar-sub-link i { width: 0.875rem; text-align: center; font-size: 0.6875rem; flex-shrink: 0; }

        .sidebar-badge {
            margin-right: auto; font-size: 0.5625rem; font-weight: 700;
            padding: 0.0625rem 0.375rem; border-radius: 9999px;
            min-width: 1.125rem; text-align: center; line-height: 1.4;
        }

        .sidebar-section-label {
            padding: 0.75rem 0.875rem 0.375rem;
            font-size: 0.5625rem; font-weight: 700; letter-spacing: 0.08em;
            color: #64748b; text-transform: uppercase;
            transition: opacity 0.2s ease;
        }
        .dark .sidebar-link { color: #94a3b8; }
        .dark .sidebar-link:hover { background: rgba(51, 65, 85, 0.6); color: #e2e8f0; }
        .dark .sidebar-link.active { background: rgba(59, 130, 246, 0.2); color: #93c5fd; }
        .dark .sidebar-group-btn { color: #94a3b8; }
        .dark .sidebar-group-btn:hover { background: rgba(51, 65, 85, 0.6); color: #e2e8f0; }
        .dark .sidebar-group-btn i.chevron { color: #64748b; }
        .dark .sidebar-sub-link { color: #94a3b8; }
        .dark .sidebar-sub-link:hover { background: rgba(51, 65, 85, 0.5); color: #cbd5e1; }
        .dark .sidebar-sub-link.active { color: #93c5fd; background: rgba(59, 130, 246, 0.15); }
        .dark .sidebar-section-label { color: #64748b; }

        /* ========== COLLAPSED SIDEBAR ========== */
        .admin-sidebar.collapsed { width: 64px !important; }
        .admin-sidebar.collapsed .sidebar-link,
        .admin-sidebar.collapsed .sidebar-group-btn {
            justify-content: center; padding: 0.5rem;
        }
        .admin-sidebar.collapsed .sidebar-link span,
        .admin-sidebar.collapsed .sidebar-group-btn > span > span,
        .admin-sidebar.collapsed .sidebar-group-btn i.chevron,
        .admin-sidebar.collapsed .sidebar-badge,
        .admin-sidebar.collapsed .sidebar-section-label,
        .admin-sidebar.collapsed .sidebar-sub-link,
        .admin-sidebar.collapsed [x-show] ul,
        .admin-sidebar.collapsed .sidebar-logo-text,
        .admin-sidebar.collapsed .sidebar-user-info,
        .admin-sidebar.collapsed .border-r { 
            display: none !important; 
        }
        .admin-sidebar.collapsed .sidebar-link i,
        .admin-sidebar.collapsed .sidebar-group-btn i:first-of-type {
            width: auto; font-size: 1rem;
        }
        .admin-sidebar.collapsed .sidebar-logo { justify-content: center; }
        .admin-sidebar.collapsed .sidebar-user-wrap { justify-content: center; padding: 0.5rem; }
        .admin-sidebar.collapsed .sidebar-user-wrap img,
        .admin-sidebar.collapsed .sidebar-user-wrap > div:first-child { margin: 0 auto; }
        .admin-sidebar.collapsed .sidebar-collapse-btn i { transform: rotate(180deg); }
        [dir="rtl"] .admin-sidebar.collapsed .sidebar-collapse-btn i { transform: rotate(0deg); }

        /* ========== TOP NAVBAR ========== */
        .top-navbar {
            height: 70px;
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(16px) saturate(180%);
            border-bottom: 1px solid rgba(226, 232, 240, 0.7);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.03), 0 1px 2px -1px rgba(0, 0, 0, 0.03);
            transition: background 0.3s, border-color 0.3s;
        }
        .dark .top-navbar {
            background: rgba(30, 41, 59, 0.97);
            border-bottom-color: rgba(51, 65, 85, 0.8);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.2);
        }

        /* ========== CARDS ========== */
        .stat-card {
            background: white;
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative; overflow: hidden;
        }
        .stat-card::after {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, transparent 60%, rgba(30, 58, 138, 0.02) 100%);
            pointer-events: none; border-radius: 16px;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px -12px rgba(15, 23, 42, 0.08), 0 4px 6px -2px rgba(15, 23, 42, 0.03);
            border-color: rgba(30, 58, 138, 0.12);
        }
        .stat-card:active { transform: translateY(-1px); }

        .stat-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.125rem; color: white;
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .stat-card:hover .stat-icon { transform: scale(1.08) rotate(2deg); }

        .section-card {
            background: white;
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .section-card:hover {
            box-shadow: 0 12px 28px -6px rgba(15, 23, 42, 0.06);
            border-color: rgba(30, 58, 138, 0.08);
        }
        .section-card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(241, 245, 249, 0.9);
            display: flex; align-items: center; justify-content: space-between;
            background: rgba(248, 250, 252, 0.4);
        }

        .list-row {
            display: flex; align-items: center; gap: 0.875rem;
            padding: 0.75rem 1.5rem;
            transition: background 0.12s ease;
            border-bottom: 1px solid rgba(241, 245, 249, 0.7);
        }
        .list-row:last-child { border-bottom: none; }
        .list-row:hover { background: rgba(248, 250, 252, 0.7); }

        /* ========== DARK MODE — بطاقات ونصوص كل الصفحات ========== */
        .dark .stat-card,
        .dark .section-card,
        .dark .glass-card,
        .dark .dashboard-card,
        .dark .list-item-card,
        .dark .card-hover-effect,
        .dark .bg-white { background: #1e293b !important; border-color: #334155 !important; }
        /* بطاقات شبه شفافة (مثل سجل النشاطات) — بدونها يبقى النص الفاتح على خلفية فاتحة */
        .dark [class*="bg-white/95"],
        .dark [class*="bg-white/85"],
        .dark [class*="bg-white/80"],
        .dark [class*="bg-white/70"] {
            background-color: rgba(30, 41, 59, 0.97) !important;
            border-color: #334155 !important;
        }
        .dark [class*="bg-slate-50/50"],
        .dark [class*="bg-slate-50/60"] {
            background-color: rgba(30, 41, 59, 0.65) !important;
        }
        .dark main .bg-gray-50,
        .dark .content-wrapper .bg-gray-50 { background-color: #0f172a !important; }
        .dark main .bg-gray-100,
        .dark .content-wrapper .bg-gray-100 { background-color: #1e293b !important; }
        .dark main .min-h-screen.bg-white,
        .dark .content-wrapper .min-h-screen.bg-white { background-color: #0f172a !important; }
        /* شريط البحث والحقول: Tailwind focus-within:bg-white يبقى أبيضاً في الوضع الداكن */
        .dark .focus-within\:bg-white:focus-within { background-color: #1e293b !important; }
        .dark .hover\:bg-white:hover { background-color: #334155 !important; }
        .dark main [class*="bg-slate-200"],
        .dark .content-wrapper [class*="bg-slate-200"] { background-color: #475569 !important; }
        .dark .stat-card:hover,
        .dark .section-card:hover { box-shadow: 0 12px 28px -6px rgba(0, 0, 0, 0.3); border-color: #475569 !important; }
        .dark .stat-card::after { background: linear-gradient(135deg, transparent 60%, rgba(59, 130, 246, 0.05) 100%); }
        .dark .section-card-header { background: rgba(30, 41, 59, 0.8) !important; border-bottom-color: #334155 !important; }
        .dark .section-header { background: rgba(30, 41, 59, 0.6) !important; border-bottom-color: #334155 !important; }
        .dark .list-row { border-bottom-color: #334155 !important; }
        .dark .list-row:hover { background: rgba(51, 65, 85, 0.5) !important; }
        .dark .glass-card:hover { background: rgba(30, 41, 59, 0.95) !important; border-color: #475569 !important; }
        .dark .list-item-card:hover { background: #334155 !important; border-color: #475569 !important; }
        .dark .bg-slate-50 { background: #334155 !important; }
        .dark .bg-slate-50\/80 { background: rgba(51, 65, 85, 0.8) !important; }
        .dark .rounded-xl.bg-slate-50 { background: #334155 !important; }
        .dark .border-slate-100 { border-color: #334155 !important; }
        .dark .border-slate-200 { border-color: #475569 !important; }
        .dark main,
        .dark .content-wrapper { color: #e2e8f0; }
        .dark main h1, .dark main h2, .dark main h3, .dark main h4, .dark main h5, .dark main h6,
        .dark .content-wrapper h1, .dark .content-wrapper h2, .dark .content-wrapper h3,
        .dark .content-wrapper h4, .dark .content-wrapper h5, .dark .content-wrapper h6 { color: #f1f5f9 !important; }
        .dark [class*="text-slate-8"], .dark [class*="text-slate-9"], .dark [class*="text-slate-7"] { color: #e2e8f0 !important; }
        .dark [class*="text-slate-6"], .dark [class*="text-slate-5"] { color: #94a3b8 !important; }
        .dark [class*="text-slate-4"] { color: #cbd5e1 !important; }
        .dark [class*="text-gray-8"], .dark [class*="text-gray-9"], .dark [class*="text-gray-7"] { color: #e2e8f0 !important; }
        .dark [class*="text-gray-6"], .dark [class*="text-gray-5"] { color: #94a3b8 !important; }
        .dark [class*="text-navy-8"], .dark [class*="text-navy-7"] { color: #e2e8f0 !important; }
        .dark main [class*="text-mx-indigo"], .dark main [class*="text-mx-navy"],
        .dark .content-wrapper [class*="text-mx-indigo"], .dark .content-wrapper [class*="text-mx-navy"] { color: #c7d2fe !important; }
        .dark main [class*="text-[#1C"], .dark main [class*="text-[#1F3"], .dark main [class*="text-[#1F2"], .dark main [class*="text-[#283593]"],
        .dark .content-wrapper [class*="text-[#1C"], .dark .content-wrapper [class*="text-[#1F3"], .dark .content-wrapper [class*="text-[#1F2"], .dark .content-wrapper [class*="text-[#283593]"] { color: #f1f5f9 !important; }
        .dark main [class*="text-[#2CA9BD]"], .dark .content-wrapper [class*="text-[#2CA9BD]"] { color: #67e8f9 !important; }
        .dark .content-wrapper input::placeholder,
        .dark .content-wrapper textarea::placeholder { color: #64748b; }
        .dark .content-wrapper input:not([type="submit"]):not([type="button"]),
        .dark .content-wrapper textarea,
        .dark .content-wrapper select { background: #334155 !important; border-color: #475569 !important; color: #e2e8f0 !important; }
        .dark table { border-color: #475569; }
        .dark th, .dark td { border-color: #334155; color: #e2e8f0; }
        .dark thead th { background: #334155 !important; color: #f1f5f9 !important; }
        .dark tbody tr:hover { background: rgba(51, 65, 85, 0.5) !important; }
        .dark .border-gray-200 { border-color: #475569 !important; }
        .dark hr { border-color: #334155; }
        .dark a:not(.btn-primary):not(.sidebar-link):not(.sidebar-sub-link) { color: #93c5fd; }
        .dark a:not(.btn-primary):not(.sidebar-link):not(.sidebar-sub-link):hover { color: #bfdbfe; }
        .dark .bg-emerald-50 { background: rgba(16, 185, 129, 0.15) !important; }
        .dark .bg-rose-50 { background: rgba(244, 63, 94, 0.15) !important; }
        .dark .bg-amber-50 { background: rgba(245, 158, 11, 0.15) !important; }
        .dark .bg-sky-50 { background: rgba(14, 165, 233, 0.15) !important; }
        .dark .bg-indigo-50 { background: rgba(99, 102, 241, 0.15) !important; }
        .dark .border-emerald-100 { border-color: rgba(16, 185, 129, 0.3) !important; }
        .dark .border-rose-100 { border-color: rgba(244, 63, 94, 0.3) !important; }
        .dark .border-amber-100 { border-color: rgba(245, 158, 11, 0.3) !important; }
        .dark .text-emerald-800 { color: #6ee7b7 !important; }
        .dark .text-rose-800 { color: #fda4af !important; }
        .dark .text-amber-600 { color: #fcd34d !important; }
        .dark .text-amber-700 { color: #fde047 !important; }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(16px) saturate(180%);
            border: 1px solid rgba(226, 232, 240, 0.5);
            border-radius: 16px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.06);
        }

        /* ========== ANIMATIONS ========== */
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeSlideUp 0.5s cubic-bezier(0.22, 1, 0.36, 1) both; }
        .animate-fade-in-1 { animation-delay: 0.06s; }
        .animate-fade-in-2 { animation-delay: 0.12s; }
        .animate-fade-in-3 { animation-delay: 0.18s; }
        .animate-fade-in-4 { animation-delay: 0.24s; }
        .animate-fade-in-5 { animation-delay: 0.30s; }

        /* ========== BUTTONS ========== */
        .btn-primary { background: #1E3A8A; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 600; transition: all 0.2s; }
        .btn-primary:hover { background: #1e3a6f; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(30, 58, 138, 0.25); }
        .btn-primary:active { transform: translateY(0); }
        .btn-secondary { background: #64748b; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 600; transition: all 0.2s; }
        .btn-secondary:hover { background: #475569; }
        .btn-success { background: #059669; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 600; transition: all 0.2s; }
        .btn-success:hover { background: #047857; }
        .btn-danger { background: #dc2626; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 600; transition: all 0.2s; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-warning { background: #d97706; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 600; transition: all 0.2s; }
        .btn-warning:hover { background: #b45309; }

        /* ========== COMPAT for other admin pages ========== */
        .nav-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 1rem; border-radius: 0.5rem; color: #475569; transition: all 0.2s; }
        .nav-link:hover { background: #f1f5f9; color: #1e293b; }
        .nav-link.active { background: #eef2ff; color: #1E3A8A; }
        .dashboard-card { background: white; border: 1px solid rgba(226, 232, 240, 0.8); border-radius: 16px; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
        .dashboard-card:hover { box-shadow: 0 16px 32px -8px rgba(0, 0, 0, 0.06); }
        .card-hover-effect { transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover-effect:hover { transform: translateY(-2px); }
        .card-icon { background: linear-gradient(135deg, #1E3A8A, #2563EB); box-shadow: 0 4px 14px rgba(30, 58, 138, 0.25); }
        .card-icon:hover { transform: scale(1.08); }
        .section-header { padding: 1rem 1.5rem; border-bottom: 1px solid rgba(226, 232, 240, 0.6); background: rgba(248, 250, 252, 0.4); }
        .list-item-card { background: white; border: 1px solid rgba(226, 232, 240, 0.6); border-radius: 12px; transition: all 0.2s; }
        .list-item-card:hover { background: #f8fafc; border-color: rgba(30, 58, 138, 0.12); }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 1023px) {
            .sidebar-desktop { display: none !important; }
        }
        @media (min-width: 1024px) {
            .sidebar-desktop { display: flex !important; }
            .content-wrapper { transition: margin 0.28s cubic-bezier(0.4, 0, 0.2, 1); }
            .content-expanded { margin-right: 260px; }
            .content-collapsed { margin-right: 64px; }
        }

        /* ========== FOCUS STATES ========== */
        button:focus-visible, a:focus-visible, input:focus-visible {
            outline: 2px solid rgba(30, 58, 138, 0.4);
            outline-offset: 2px;
            border-radius: 0.375rem;
        }
    </style>
    
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-[#F8FAFC] font-body transition-colors duration-300 dark:bg-slate-900"
      x-data="{ 
          sidebarOpen: false,
          sidebarCollapsed: localStorage.getItem('sidebar_collapsed') === 'true',
          darkMode: document.documentElement.classList.contains('dark')
      }" 
      x-init="
          $watch('sidebarCollapsed', v => localStorage.setItem('sidebar_collapsed', v));
          function persistTheme(isDark) {
              if (isDark) { document.documentElement.classList.add('dark'); document.documentElement.classList.remove('light'); }
              else { document.documentElement.classList.remove('dark'); document.documentElement.classList.add('light'); }
              localStorage.setItem('theme', isDark ? 'dark' : 'light');
          }
          $watch('darkMode', v => persistTheme(v));
          window.addEventListener('close-sidebar', () => sidebarOpen = false);
          window.addEventListener('resize', () => { if (window.innerWidth >= 1024) sidebarOpen = false; });
      "
      @close-sidebar.window="sidebarOpen = false">

    <!-- ===== Desktop Sidebar ===== -->
    <aside class="sidebar-desktop admin-sidebar fixed top-0 right-0 bottom-0 z-30 flex flex-col"
           :class="sidebarCollapsed ? 'collapsed w-[64px]' : 'w-[260px]'"
           style="box-shadow: -4px 0 24px rgba(15, 23, 42, 0.08);">
        <?php echo $__env->make('layouts.admin-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </aside>

    <!-- ===== Mobile Sidebar Overlay ===== -->
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-50 lg:hidden"
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-[2px]" @click="sidebarOpen = false"></div>
        <div class="absolute inset-y-0 right-0 w-[260px] admin-sidebar flex flex-col"
             style="box-shadow: -4px 0 32px rgba(15, 23, 42, 0.15);"
             x-show="sidebarOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">
            <button @click="sidebarOpen = false" class="absolute top-4 left-3 z-50 w-8 h-8 rounded-lg bg-white/8 hover:bg-white/15 text-slate-400 flex items-center justify-center transition-colors">
                <i class="fas fa-times text-sm"></i>
            </button>
            <?php echo $__env->make('layouts.admin-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>

    <!-- ===== Main Content ===== -->
    <div class="content-wrapper min-h-screen flex flex-col"
         :class="sidebarCollapsed ? 'content-collapsed' : 'content-expanded'">
        
        <!-- Top Navbar -->
        <header class="top-navbar sticky top-0 z-40 flex items-center px-5 lg:px-8 gap-4">
            <!-- Mobile hamburger -->
            <button @click="sidebarOpen = true" class="lg:hidden w-10 h-10 rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-700 dark:hover:bg-slate-600 flex items-center justify-center text-slate-500 dark:text-slate-300 transition-all active:scale-95">
                <i class="fas fa-bars text-sm"></i>
            </button>

            <?php if(! empty($adminPanelLogoUrl)): ?>
            <div class="flex items-center shrink-0" title="Muallimx">
                <img src="<?php echo e($adminPanelLogoUrl); ?>" alt="" width="36" height="36" class="h-8 w-8 sm:h-9 sm:w-9 rounded-xl object-contain bg-white border border-slate-200/80 dark:border-slate-600 dark:bg-slate-800 shadow-sm">
            </div>
            <?php endif; ?>

            <!-- Page title -->
            <div class="flex-1 min-w-0">
                <h1 class="text-lg font-heading font-bold text-slate-800 dark:text-slate-100 truncate">
                    <?php if (! empty(trim($__env->yieldContent('header')))): ?>
                        <?php echo $__env->yieldContent('header'); ?>
                    <?php else: ?>
                        <?php echo $__env->yieldContent('page_title', 'لوحة الإدارة'); ?>
                    <?php endif; ?>
                </h1>
            </div>

            <!-- Right actions -->
            <div class="flex items-center gap-2">
                <!-- Search -->
                <div class="hidden md:flex items-center bg-slate-50 dark:bg-slate-800 rounded-xl px-3.5 h-10 gap-2.5 w-60 border border-transparent focus-within:border-brand/20 focus-within:bg-white dark:focus-within:bg-slate-700 focus-within:shadow-sm transition-all">
                    <i class="fas fa-search text-slate-300 dark:text-slate-500 text-xs"></i>
                    <input type="text" placeholder="بحث سريع..." class="bg-transparent border-none outline-none text-sm text-slate-700 dark:text-slate-200 w-full placeholder-slate-400 dark:placeholder-slate-500">
                </div>

                <!-- تبديل الوضع الليلي / النهاري (الافتراضي: نهاري) -->
                <button @click="darkMode = !darkMode"
                        type="button"
                        class="w-10 h-10 rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-700 dark:hover:bg-slate-600 flex items-center justify-center text-slate-500 hover:text-slate-700 dark:text-slate-300 dark:hover:text-amber-400 transition-all active:scale-95"
                        :title="darkMode ? 'الوضع النهاري' : 'الوضع الليلي'"
                        :aria-label="darkMode ? 'تفعيل الوضع النهاري' : 'تفعيل الوضع الليلي'">
                    <i class="fas fa-moon text-sm" x-show="!darkMode" x-transition></i>
                    <i class="fas fa-sun text-sm" x-show="darkMode" x-transition x-cloak></i>
                </button>

                <!-- Notifications (تحديث تلقائي ~8ث + صوت عند زيادة العدد) -->
                <?php
                    $adminUnreadCount = \App\Models\Notification::where('user_id', auth()->id())
                        ->unread()
                        ->valid()
                        ->count();
                    $adminUnreadNotifications = \App\Models\Notification::where('user_id', auth()->id())
                        ->unread()
                        ->valid()
                        ->orderByDesc('created_at')
                        ->limit(5)
                        ->get();
                    $adminNavItems = $adminUnreadNotifications->map(fn ($n) => [
                        'id' => $n->id,
                        'title' => $n->title,
                        'message' => $n->message,
                        'priority' => $n->priority,
                        'href' => $n->action_url ?: route('admin.notifications.show', $n),
                        'time' => $n->created_at->diffForHumans(),
                        'icon' => $n->type_icon,
                    ])->values();
                    $adminNavBellConfig = [
                        'unread' => (int) $adminUnreadCount,
                        'items' => $adminNavItems->all(),
                        'pollUrl' => route('admin.api.nav-notifications'),
                    ];
                ?>
                <div class="relative"
                     x-data="adminNavNotifications(<?php echo e(\Illuminate\Support\Js::from($adminNavBellConfig)); ?>)"
                     @click.outside="openNotif = false">
                    <button type="button"
                            @click="openNotif = !openNotif"
                            class="w-10 h-10 rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-700 dark:hover:bg-slate-600 flex items-center justify-center text-slate-400 dark:text-slate-400 transition-all active:scale-95 relative">
                        <i class="fas fa-bell text-sm"></i>
                        <span x-show="unread > 0" x-cloak
                              class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-rose-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center ring-2 ring-white dark:ring-slate-800 px-1"
                              x-text="unread > 9 ? '9+' : unread"></span>
                    </button>
                    <!-- Notifications dropdown -->
                    <div x-show="openNotif" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         class="absolute left-0 mt-2 w-80 max-h-[420px] overflow-hidden rounded-2xl bg-white dark:bg-slate-800 shadow-xl shadow-slate-200/60 border border-slate-100 dark:border-slate-600 z-50">
                        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-600 flex items-center justify-between bg-slate-50/80 dark:bg-slate-700/50">
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                                    <i class="fas fa-bell text-amber-500"></i>
                                    <?php echo e(__('أحدث الإشعارات')); ?>

                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5" x-text="unread > 0 ? ('لديك ' + unread + ' إشعار غير مقروء') : 'لا توجد إشعارات جديدة حالياً'"></p>
                            </div>
                            <a href="<?php echo e(route('admin.notifications.inbox')); ?>" class="text-xs font-semibold text-sky-600 hover:text-sky-700">
                                <?php echo e(__('عرض الكل')); ?>

                            </a>
                        </div>
                        <div class="max-h-[320px] overflow-y-auto">
                            <template x-for="item in items" :key="item.id">
                                <a :href="item.href"
                                   class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors border-b border-slate-50 dark:border-slate-600 last:border-b-0">
                                    <div class="mt-0.5">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-xs font-semibold"
                                              :class="{
                                                  'bg-rose-100 text-rose-600': item.priority === 'urgent',
                                                  'bg-amber-100 text-amber-600': item.priority === 'high',
                                                  'bg-sky-100 text-sky-600': item.priority !== 'urgent' && item.priority !== 'high'
                                              }">
                                            <i :class="item.icon"></i>
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate" x-text="item.title"></p>
                                        <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5 line-clamp-2" x-text="item.message"></p>
                                        <p class="text-[10px] text-slate-400 mt-1" x-text="item.time"></p>
                                    </div>
                                </a>
                            </template>
                            <div x-show="items.length === 0" class="px-4 py-6 text-center text-xs text-slate-500">
                                <p><?php echo e($adminRtl ? 'لا توجد إشعارات جديدة' : 'No new notifications'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                    $adminNavSettingsUrl = auth()->user()->hasPermission('manage.system-settings')
                        ? route('admin.system-settings.edit')
                        : route('admin.profile');
                ?>
                <!-- إعدادات → صفحة إعدادات النظام (أو الملف الشخصي إن لم تتوفر الصلاحية) -->
                <a href="<?php echo e($adminNavSettingsUrl); ?>" title="إعدادات النظام" aria-label="إعدادات النظام" class="flex w-10 h-10 rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-slate-700 dark:hover:bg-slate-600 items-center justify-center text-slate-400 dark:text-slate-400 transition-all active:scale-95">
                    <i class="fas fa-cog text-sm"></i>
                </a>

                <!-- Divider -->
                <div class="hidden lg:block w-px h-8 bg-slate-200/80 mx-1"></div>

                <!-- Profile dropdown -->
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click.stop="open = !open" class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/60 transition-all active:scale-[0.98]" :aria-expanded="open">
                        <?php if(auth()->user()->profile_image): ?>
                            <img src="<?php echo e(auth()->user()->profile_image_url); ?>" alt="<?php echo e(auth()->user()->name); ?>" class="w-9 h-9 rounded-xl object-cover ring-2 ring-slate-100" onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                            <div class="w-9 h-9 bg-gradient-to-br from-brand to-brand-light rounded-xl hidden flex items-center justify-center text-white font-bold text-sm"><?php echo e(mb_substr(auth()->user()->name, 0, 1)); ?></div>
                        <?php else: ?>
                            <div class="w-9 h-9 bg-gradient-to-br from-brand to-brand-light rounded-xl flex items-center justify-center text-white font-bold text-sm">
                                <?php echo e(mb_substr(auth()->user()->name, 0, 1)); ?>

                            </div>
                        <?php endif; ?>
                        <div class="hidden lg:block text-right">
                            <p class="text-[13px] font-semibold text-slate-700 dark:text-slate-200 max-w-[100px] truncate leading-tight"><?php echo e(auth()->user()->name); ?></p>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500 leading-tight">مدير</p>
                        </div>
                        <i class="fas fa-chevron-down text-slate-300 text-[9px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute left-0 mt-2 w-56 rounded-2xl bg-white dark:bg-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-black/40 border border-slate-100 dark:border-slate-600 overflow-hidden" style="z-index: 9999;">
                        <div class="px-4 py-3 bg-slate-50/80 dark:bg-slate-700/50 border-b border-slate-100 dark:border-slate-600">
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate"><?php echo e(auth()->user()->name); ?></p>
                            <p class="text-xs text-slate-400 dark:text-slate-400 truncate mt-0.5"><?php echo e(auth()->user()->email ?? auth()->user()->phone); ?></p>
                        </div>
                        <div class="py-1.5">
                            <a href="<?php echo e(route('admin.dashboard')); ?>" class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:text-slate-800 dark:hover:text-slate-100 transition-colors">
                                <i class="fas fa-home w-4 text-slate-400 text-xs"></i> لوحة التحكم
                            </a>
                            <a href="<?php echo e(route('admin.profile')); ?>" class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:text-slate-800 dark:hover:text-slate-100 transition-colors">
                                <i class="fas fa-user w-4 text-slate-400 text-xs"></i> الملف الشخصي
                            </a>
                            <a href="<?php echo e($adminNavSettingsUrl); ?>" class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:text-slate-800 dark:hover:text-slate-100 transition-colors">
                                <i class="fas fa-cog w-4 text-slate-400 text-xs"></i> إعدادات النظام
                            </a>
                        </div>
                        <div class="border-t border-slate-100 dark:border-slate-600 py-1.5">
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="flex items-center gap-3 w-full text-right px-4 py-2.5 text-[13px] text-slate-600 dark:text-slate-300 hover:bg-rose-50 dark:hover:bg-rose-950/40 hover:text-rose-600 dark:hover:text-rose-400 transition-colors">
                                    <i class="fas fa-sign-out-alt w-4 text-slate-400 text-xs"></i> تسجيل الخروج
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto overflow-x-hidden dark:bg-slate-900 transition-colors duration-300">
            <div class="px-5 lg:px-8 pt-5 space-y-3">
                <?php if(session('success')): ?>
                    <div class="flex items-center gap-3 px-5 py-3 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-800 animate-fade-in" role="alert">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0"><i class="fas fa-check text-emerald-600 text-xs"></i></div>
                        <span class="text-sm font-medium flex-1"><?php echo e(session('success')); ?></span>
                        <button onclick="this.parentElement.remove()" class="text-emerald-300 hover:text-emerald-500 transition-colors"><i class="fas fa-times text-xs"></i></button>
                    </div>
                <?php endif; ?>
                <?php if(session('error')): ?>
                    <div class="flex items-center gap-3 px-5 py-3 rounded-2xl bg-rose-50 border border-rose-100 text-rose-800 animate-fade-in" role="alert">
                        <div class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center flex-shrink-0"><i class="fas fa-exclamation text-rose-600 text-xs"></i></div>
                        <span class="text-sm font-medium flex-1"><?php echo e(session('error')); ?></span>
                        <button onclick="this.parentElement.remove()" class="text-rose-300 hover:text-rose-500 transition-colors"><i class="fas fa-times text-xs"></i></button>
                    </div>
                <?php endif; ?>
                <?php if(session('warning')): ?>
                    <div class="flex items-center gap-3 px-5 py-3 rounded-2xl bg-amber-50 border border-amber-100 text-amber-800 animate-fade-in" role="alert">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0"><i class="fas fa-exclamation-triangle text-amber-600 text-xs"></i></div>
                        <span class="text-sm font-medium flex-1"><?php echo e(session('warning')); ?></span>
                        <button onclick="this.parentElement.remove()" class="text-amber-300 hover:text-amber-500 transition-colors"><i class="fas fa-times text-xs"></i></button>
                    </div>
                <?php endif; ?>
            </div>

            <div class="px-5 lg:px-8 py-6">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </main>
    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>

    <script>
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && window.innerWidth < 1024) {
                const sidebar = link.closest('.admin-sidebar');
                if (sidebar) window.dispatchEvent(new CustomEvent('close-sidebar'));
            }
        }, true);
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views/layouts/admin.blade.php ENDPATH**/ ?>