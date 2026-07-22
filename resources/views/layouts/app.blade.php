@php
    $appLocale = app()->getLocale();
    $appRtl = $appLocale === 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ $appLocale }}" dir="{{ $appRtl ? 'rtl' : 'ltr' }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} - @yield('title', __('auth.dashboard'))</title>

    @include('partials.favicon-links')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        navy: { 50:'#f0f4ff',100:'#dbe4ff',200:'#bac8ff',300:'#91a7ff',400:'#748ffc',500:'#5c7cfa',600:'#4c6ef5',700:'#4263eb',800:'#3b5bdb',900:'#364fc7',950:'#0c1222' },
                        brand: { 50:'#FFF3E0',100:'#FFE0B2',200:'#FFCC80',300:'#FFB74D',400:'#FFA726',500:'#FB5607',600:'#E04D00',700:'#BF360C',800:'#8D2600',900:'#5D1A00' },
                        mx: { navy:'#283593', indigo:'#1F2A7A', orange:'#FB5607', rose:'#FFE5F7', gold:'#FFE569', soft:'#F7F8FF' },
                        surface: { 50:'#fafbfc', 100:'#f4f5f7', 200:'#e8eaed', 300:'#dadce0' }
                    }
                }
            }
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @php
        $showContentProtection = !empty(trim((string) ($__env->yieldContent('enable-content-protection') ?? '')));
    @endphp
    @if($showContentProtection)
    <script>
        window.Laravel = { user: { name: '{{ auth()->check() ? auth()->user()->name : "زائر" }}' } };
    </script>
    <script src="{{ versioned_asset('js/platform-protection.js') }}"></script>
    @endif

    {{-- نفس منطق الإدارة: الافتراضي فاتح؛ الوضع الداكن فقط عند theme=dark في localStorage --}}
    <script>
        (function() {
            var s = localStorage.getItem('theme');
            if (s === 'dark') {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
            }
        })();
    </script>

    <style>
        * { font-family: 'Cairo', 'IBM Plex Sans Arabic', 'Tajawal', system-ui, -apple-system, sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Cairo', 'Tajawal', 'IBM Plex Sans Arabic', sans-serif; }
        [x-cloak] { display: none !important; }
        html { scroll-behavior: smooth; }
        html.light { color-scheme: light; }
        html.dark { color-scheme: dark; }
        body { background: #f7f8ff; overflow-x: hidden; }
        .dark body { background: #0c1222; }

        /* ── Sidebar ── */
        .app-sidebar {
            width: 260px;
            background: #fff;
            border-left: 1px solid #e8eaf6;
        }
        .dark .app-sidebar {
            background: #111827;
            border-left-color: #1f2937;
        }
        @media (max-width: 1023px) {
            .app-sidebar { width: 280px; }
        }

        .app-sidebar::-webkit-scrollbar,
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .app-sidebar::-webkit-scrollbar-thumb,
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
        .dark .app-sidebar::-webkit-scrollbar-thumb,
        .dark .sidebar-scroll::-webkit-scrollbar-thumb { background: #374151; }

        /* Sidebar nav items */
        .s-nav {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 12px; border-radius: 8px;
            font-size: 13px; font-weight: 500;
            color: #4b5563; transition: all .15s;
            border: 1px solid transparent;
        }
        .s-nav:hover { background: #f3f4f6; color: #111827; }
        .s-nav.active { background: #f2f4ff; color: #1F2A7A; border-color: #d6dcff; }
        .dark .s-nav { color: #9ca3af; }
        .dark .s-nav:hover { background: #1f2937; color: #e5e7eb; }
        .dark .s-nav.active { background: #172554; color: #60a5fa; border-color: #1e3a5f; }

        .s-nav .s-icon {
            width: 32px; height: 32px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; flex-shrink: 0; transition: all .15s;
        }

        /* ── Header ── */
        .app-header {
            height: 56px; background: #fff;
            border-bottom: 1px solid #e8eaf6;
            backdrop-filter: blur(10px);
        }
        .dark .app-header {
            background: #111827; border-bottom-color: #1f2937;
        }

        /* Header buttons */
        .h-btn {
            width: 36px; height: 36px; border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            color: #6b7280; border: 1px solid #e5e7eb;
            transition: all .15s; background: transparent;
        }
        .h-btn:hover { background: #f3f4f6; color: #111827; border-color: #d1d5db; }
        .dark .h-btn { color: #9ca3af; border-color: #374151; }
        .dark .h-btn:hover { background: #1f2937; color: #e5e7eb; border-color: #4b5563; }

        /* Search input */
        .search-box {
            background: #f3f4f6; border: 1px solid transparent;
            border-radius: 8px; padding: 7px 12px;
            transition: all .2s;
        }
        .search-box:focus-within { background: #fff; border-color: #283593; box-shadow: 0 0 0 3px rgba(40,53,147,.1); }
        .dark .search-box { background: #1f2937; }
        .dark .search-box:focus-within { background: #111827; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.15); }
        .dark .search-box input { color: #e5e7eb; }

        /* Dropdown */
        .dd-menu {
            background: #fff; border: 1px solid #e5e7eb;
            border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,.08);
            overflow: hidden;
        }
        .dark .dd-menu { background: #1f2937; border-color: #374151; box-shadow: 0 10px 40px rgba(0,0,0,.3); }
        .dd-item { display: flex; align-items: center; transition: background .1s; }
        .dd-item:hover { background: #f3f4f6; }
        .dark .dd-item:hover { background: #374151; }

        /* User avatar */
        .u-avatar {
            width: 32px; height: 32px; border-radius: 8px;
            background: linear-gradient(135deg, #283593, #FB5607);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 600; font-size: 13px;
        }
        .u-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; }

        /* Logo fix */
        .logo-section img,
        .app-sidebar img[alt*="Logo"] {
            transform: none !important; rotate: 0deg !important;
            object-fit: contain !important; object-position: center !important;
        }

        /* Notification badge */
        .n-badge {
            position: absolute; top: -3px; right: -3px;
            min-width: 16px; height: 16px; padding: 0 4px;
            background: #ef4444; border-radius: 99px;
            font-size: 9px; color: #fff; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid #fff;
        }
        .dark .n-badge { border-color: #111827; }

        /* Stat mini cards (student sidebar) */
        .stat-mini { border-radius: 8px; padding: 8px 10px; }

        /* Student sidebar nav-item compat */
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 12px; border-radius: 8px;
            font-size: 13px; font-weight: 500;
            color: #4b5563; transition: all .15s;
        }
        .nav-item:hover { background: #f3f4f6; color: #111827; }
        .nav-item.active { background: #eff6ff; color: #1d4ed8; }
        .dark .nav-item { color: #9ca3af; }
        .dark .nav-item:hover { background: #1f2937; color: #e5e7eb; }
        .dark .nav-item.active { background: #172554; color: #60a5fa; }
        .nav-icon {
            width: 32px; height: 32px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; flex-shrink: 0;
        }

        /* Student sidebar bottom card */
        .user-card-bottom { border-top: 1px solid #e5e7eb; }
        .dark .user-card-bottom { border-top-color: #1f2937; }
        .logo-area { border-bottom: 1px solid #e5e7eb; }
        .dark .logo-area { border-bottom-color: #1f2937; }

        /* ── Instructor sidebar: clean light header ── */
        .ins-sidebar-brand {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            position: relative;
        }
        .dark .ins-sidebar-brand {
            background: #1e293b;
            border-bottom-color: #334155;
        }
        .ins-stat-card {
            border-radius: 12px; padding: 12px 14px;
            transition: transform .2s, box-shadow .2s, border-color .2s;
        }
        .ins-stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px -8px rgba(0,0,0,.12); border-color: #c7d2fe !important; }
        .dark .ins-stat-card:hover { box-shadow: 0 8px 20px -8px rgba(0,0,0,.35); border-color: #475569 !important; }
        .ins-nav-group {
            font-size: 10px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
            color: #6b7280; padding: 14px 16px 6px;
            display: flex; align-items: center;
        }
        .ins-nav-group > span { display: inline-flex; align-items: center; gap: 5px; }
        .dark .ins-nav-group { color: #64748b; }
        .ins-nav {
            display: flex; align-items: center; gap: 11px;
            padding: 8px 12px; border-radius: 10px; margin: 1px 8px;
            font-size: 13px; font-weight: 500; color: #374151;
            transition: all .2s cubic-bezier(0.4,0,0.2,1);
            border: 1px solid transparent;
            position: relative;
        }
        .ins-nav::before {
            content: ''; position: absolute; right: 0; top: 50%; transform: translateY(-50%);
            width: 3px; height: 0; border-radius: 3px 0 0 3px;
            background: linear-gradient(180deg, #283593, #FB5607);
            transition: height .2s ease;
        }
        .ins-nav:hover { background: #f8fafc; color: #0f172a; }
        .ins-nav.active { background: #f2f4ff; color: #1F2A7A; border-color: #d6dcff; font-weight: 600; }
        .ins-nav.active::before { height: 22px; }
        .dark .ins-nav { color: #9ca3af; }
        .dark .ins-nav:hover { background: #1f2937; color: #f1f5f9; }
        .dark .ins-nav.active { background: #1e2b5a; color: #c7d2fe; border-color: #33407a; font-weight: 600; }
        .dark .ins-nav.active::before { background: linear-gradient(180deg, #818cf8, #fb7185); }
        .ins-nav .ins-icon {
            width: 34px; height: 34px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; flex-shrink: 0;
            transition: transform .2s, box-shadow .2s;
        }
        .ins-nav:hover .ins-icon { transform: scale(1.08); }
        .ins-nav.active .ins-icon { box-shadow: 0 2px 8px -2px rgba(40, 53, 147, 0.3); }
        .ins-nav-badge {
            min-width: 20px; height: 20px; padding: 0 6px;
            border-radius: 10px; font-size: 11px; font-weight: 700;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .ins-user-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0; border-radius: 12px;
            padding: 12px 14px; transition: all .2s;
        }
        .ins-user-card:hover { border-color: #cbd5e1; box-shadow: 0 4px 12px -4px rgba(0,0,0,.08); }
        .dark .ins-user-card { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-color: #334155; }
        .dark .ins-user-card:hover { border-color: #475569; box-shadow: 0 4px 12px -4px rgba(0,0,0,.25); }

        /* ========== DARK MODE — نفس منطق admin.blade.php (بطاقات/نصوص/حقول داخل المحتوى فقط) ========== */
        .dark .stat-card,
        .dark .section-card,
        .dark .glass-card,
        .dark .dashboard-card,
        .dark .list-item-card,
        .dark .card-hover-effect,
        .dark .bg-white { background: #1e293b !important; border-color: #334155 !important; }
        .dark main .bg-gray-50,
        .dark main .bg-gray-100 { background-color: #0c1222 !important; }
        .dark main .min-h-screen.bg-gray-50,
        .dark main .min-h-screen.bg-white { background-color: #0c1222 !important; }
        .dark .focus-within\:bg-white:focus-within { background-color: #1e293b !important; }
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
        .dark .bg-slate-100 { background: rgba(51, 65, 85, 0.75) !important; }
        .dark .bg-slate-50\/80 { background: rgba(51, 65, 85, 0.8) !important; }
        .dark .rounded-xl.bg-slate-50 { background: #334155 !important; }
        .dark .border-slate-100 { border-color: #334155 !important; }
        .dark .border-slate-200 { border-color: #475569 !important; }
        .dark main { color: #e2e8f0; }
        .dark main h1, .dark main h2, .dark main h3, .dark main h4, .dark main h5, .dark main h6 { color: #f1f5f9 !important; }
        .dark [class*="text-slate-8"], .dark [class*="text-slate-9"], .dark [class*="text-slate-7"] { color: #e2e8f0 !important; }
        .dark [class*="text-slate-6"], .dark [class*="text-slate-5"] { color: #94a3b8 !important; }
        .dark [class*="text-slate-4"] { color: #cbd5e1 !important; }
        .dark [class*="text-gray-8"], .dark [class*="text-gray-9"], .dark [class*="text-gray-7"] { color: #e2e8f0 !important; }
        .dark [class*="text-gray-6"], .dark [class*="text-gray-5"] { color: #94a3b8 !important; }
        .dark [class*="text-navy-8"], .dark [class*="text-navy-7"] { color: #e2e8f0 !important; }
        /* نصوص بألوان hex / براند ثابتة (لا تُطابق text-slate-*) */
        .dark main [class*="text-mx-indigo"], .dark main [class*="text-mx-navy"] { color: #c7d2fe !important; }
        .dark main [class*="text-[#1C"], .dark main [class*="text-[#1F3"], .dark main [class*="text-[#1F2"], .dark main [class*="text-[#283593]"] { color: #f1f5f9 !important; }
        .dark main [class*="text-[#2CA9BD]"] { color: #67e8f9 !important; }
        .dark main input::placeholder,
        .dark main textarea::placeholder { color: #64748b; }
        .dark main input:not([type="submit"]):not([type="button"]),
        .dark main textarea,
        .dark main select { background: #334155 !important; border-color: #475569 !important; color: #e2e8f0 !important; }
        .dark main table { border-color: #475569; }
        .dark main th, .dark main td { border-color: #334155; color: #e2e8f0; }
        .dark main thead th { background: #334155 !important; color: #f1f5f9 !important; }
        .dark main tbody tr:hover { background: rgba(51, 65, 85, 0.5) !important; }
        .dark .border-gray-200 { border-color: #475569 !important; }
        .dark main hr { border-color: #334155; }
        .dark main a:not(.btn-primary) { color: #93c5fd; }
        .dark main a:not(.btn-primary):hover { color: #bfdbfe; }
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
    </style>

    @stack('styles')
</head>
<body x-data="{ sidebarOpen: window.innerWidth >= 1024 }"
      x-init="window.addEventListener('resize', () => { sidebarOpen = window.innerWidth >= 1024; })">

<script>
function themeManager() {
    return {
        dark: false,
        init() {
            this.syncFromDom();
            var self = this;
            window.addEventListener('storage', function(e) {
                if (e.key === 'theme' && e.newValue) {
                    self.dark = e.newValue === 'dark';
                    document.documentElement.classList.toggle('dark', self.dark);
                    document.documentElement.classList.toggle('light', !self.dark);
                }
            });
        },
        syncFromDom() {
            this.dark = document.documentElement.classList.contains('dark');
        },
        toggle() {
            this.dark = !this.dark;
            document.documentElement.classList.toggle('dark', this.dark);
            document.documentElement.classList.toggle('light', !this.dark);
            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
        }
    };
}
</script>

    <div class="flex h-screen overflow-hidden">
        @auth
            <aside x-show="sidebarOpen || window.innerWidth >= 1024"
                   x-transition:enter="transition ease-out duration-200"
                   x-transition:enter-start="opacity-0 translate-x-full"
                   x-transition:enter-end="opacity-100 translate-x-0"
                   x-transition:leave="transition ease-in duration-150"
                   x-transition:leave-start="opacity-100 translate-x-0"
                   x-transition:leave-end="opacity-0 translate-x-full"
                   class="app-sidebar flex-shrink-0 fixed lg:static inset-y-0 right-0 z-50 lg:z-auto overflow-y-auto">
                @if(auth()->user()->isInstructor() || auth()->user()->isTeacher())
                    @include('layouts.instructor-sidebar')
                @else
                    @include('layouts.student-sidebar')
                @endif
            </aside>

            <div x-show="sidebarOpen && window.innerWidth < 1024"
                 @click="sidebarOpen = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 lg:hidden"></div>
        @endauth

        <div class="flex flex-col flex-1 min-w-0">
            @auth
                <header class="app-header flex items-center justify-between px-4 md:px-6 flex-shrink-0 sticky top-0 z-30">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <button @click="sidebarOpen = !sidebarOpen"
                                class="lg:hidden h-btn flex-shrink-0">
                            <i class="fas fa-bars text-sm"></i>
                        </button>

                        <div class="hidden md:flex items-center flex-1 max-w-md">
                            <div class="search-box flex items-center gap-2 w-full">
                                <i class="fas fa-search text-gray-400 dark:text-gray-500 text-xs"></i>
                                <input type="text" placeholder="{{ __('common.nav_search_placeholder_long') }}" class="flex-1 bg-transparent border-none outline-none text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 dark:placeholder-gray-500 min-w-0">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        {{-- Theme toggle --}}
                        <div x-data="themeManager()" x-init="init()">
                            <button @click="toggle()" type="button" class="h-btn"
                                    :title="dark ? '{{ $appRtl ? 'الوضع النهاري' : 'Light mode' }}' : '{{ $appRtl ? 'الوضع الليلي' : 'Dark mode' }}'">
                                <i class="text-sm" :class="dark ? 'fas fa-sun text-amber-400' : 'fas fa-moon text-gray-400'"></i>
                            </button>
                        </div>

                        @php
                            $currentUser = auth()->user();
                            $isInstructorLike = $currentUser && ($currentUser->isInstructor() || $currentUser->isTeacher());
                            $audiences = $isInstructorLike
                                ? [null, 'instructor', 'teacher']
                                : [null, 'student'];

                            $navNotificationsQuery = $currentUser
                                ? $currentUser->customNotifications()
                                    ->with('sender')
                                    ->whereIn('audience', $audiences)
                                    ->where(function ($q) {
                                        $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                                    })
                                : null;

                            $navUnreadCount = $navNotificationsQuery
                                ? (clone $navNotificationsQuery)->where('is_read', false)->count()
                                : 0;

                            $navRecentNotifications = $navNotificationsQuery
                                ? (clone $navNotificationsQuery)->orderBy('created_at', 'desc')->limit(8)->get()
                                : collect();
                        @endphp

                        {{-- Notifications --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="h-btn relative">
                                <i class="fas fa-bell text-sm"></i>
                                @if($navUnreadCount > 0)
                                    <span class="n-badge">{{ $navUnreadCount > 99 ? '99+' : $navUnreadCount }}</span>
                                @endif
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                 class="absolute left-0 mt-2 w-80 dd-menu z-50">
                                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between gap-2">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $appRtl ? 'الإشعارات' : 'Notifications' }}</h3>
                                    @if(Route::has('notifications'))
                                        <a href="{{ route('notifications') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                                            {{ $appRtl ? 'عرض الكل' : 'View all' }}
                                        </a>
                                    @endif
                                </div>
                                @if($navRecentNotifications->isNotEmpty())
                                    <div class="max-h-96 overflow-y-auto">
                                        @foreach($navRecentNotifications as $notification)
                                            @php
                                                $notificationUrl = $notification->action_url ?: (Route::has('notifications.show') ? route('notifications.show', $notification) : '#');
                                            @endphp
                                            <a href="{{ $notificationUrl }}"
                                               class="block px-4 py-3 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                                <div class="flex items-start gap-3">
                                                    <div class="mt-0.5">
                                                        <i class="{{ $notification->type_icon }} text-blue-500 text-sm"></i>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <div class="flex items-center justify-between gap-2">
                                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $notification->title }}</p>
                                                            @if(!$notification->is_read)
                                                                <span class="w-2 h-2 rounded-full bg-rose-500 flex-shrink-0"></span>
                                                            @endif
                                                        </div>
                                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">{{ $notification->message }}</p>
                                                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">{{ optional($notification->created_at)->diffForHumans() }}</p>
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-6 text-center text-gray-400 dark:text-gray-500 text-sm">
                                        <i class="fas fa-bell-slash text-xl mb-2 block"></i>
                                        <p>{{ $appRtl ? 'لا توجد إشعارات جديدة' : 'No new notifications' }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- User menu --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                <div class="u-avatar flex-shrink-0">
                                    @if(auth()->user()->profile_image)
                                        <img src="{{ auth()->user()->profile_image_url }}" alt="">
                                    @else
                                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                                    @endif
                                </div>
                                <span class="hidden lg:block text-sm font-medium text-gray-700 dark:text-gray-300 max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down text-[10px] text-gray-400 hidden lg:block transition-transform" :class="{ 'rotate-180': open }"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                 class="absolute left-0 mt-2 w-56 dd-menu z-50">
                                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">{{ auth()->user()->email ?? '—' }}</p>
                                </div>
                                <div class="p-1.5">
                                    @php
                                        $profileRoute = (auth()->user()->isInstructor() || auth()->user()->isTeacher() || in_array(strtolower(auth()->user()->role ?? ''), ['instructor', 'teacher'])) ? route('instructor.profile') : route('profile');
                                    @endphp
                                    <a href="{{ $profileRoute }}" class="dd-item px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 gap-2.5">
                                        <i class="fas fa-user text-gray-400 text-xs w-4"></i>
                                        {{ $appRtl ? 'الملف الشخصي' : 'Profile' }}
                                    </a>
                                    <a href="{{ route('settings') }}" class="dd-item px-3 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-300 gap-2.5">
                                        <i class="fas fa-cog text-gray-400 text-xs w-4"></i>
                                        {{ $appRtl ? 'الإعدادات' : 'Settings' }}
                                    </a>
                                    <hr class="my-1.5 border-gray-100 dark:border-gray-700">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full dd-item px-3 py-2 rounded-lg text-sm text-red-600 dark:text-red-400 gap-2.5 text-right">
                                            <i class="fas fa-sign-out-alt text-xs w-4"></i>
                                            {{ $appRtl ? 'تسجيل الخروج' : 'Sign out' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
            @endauth

            <main class="flex-1 overflow-auto bg-surface-50 dark:bg-navy-950">
                <div class="p-4 md:p-6 lg:p-8 w-full max-w-full">
                    @if(session('success'))
                        <div class="mb-5 flex items-center gap-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 px-4 py-3 rounded-xl text-sm">
                            <i class="fas fa-check-circle flex-shrink-0"></i>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mb-5 flex items-center gap-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl text-sm">
                            <i class="fas fa-exclamation-circle flex-shrink-0"></i>
                            {{ session('error') }}
                        </div>
                    @endif
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
