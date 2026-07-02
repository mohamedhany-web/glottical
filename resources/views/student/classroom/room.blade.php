<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glottical Classroom — {{ $meeting->title ?: $meeting->code }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        * { font-family: 'IBM Plex Sans Arabic', system-ui, sans-serif; }
        html { height: 100%; height: 100dvh; }
        body {
            margin: 0;
            padding: 0;
            background: #0c1222;
            overflow: hidden;
            min-height: 100vh;
            min-height: 100dvh;
            height: 100vh;
            height: 100dvh;
            display: flex;
            flex-direction: column;
        }
        #jitsi-container {
            width: 100%;
            flex: 1;
            min-height: 0;
            background: #0f172a;
        }
        .room-body {
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
            min-height: 0;
        }
        #jitsi-container iframe { width: 100% !important; height: 100% !important; border: none; }
        #meeting-stage { flex: 1; min-height: 0; position: relative; display: flex; flex-direction: column; width: 100%; }
        #wb-popup { z-index: 140; }
        /* عدم خلط display مع Tailwind: عند الإغلاق لا يبقى flex يتعارض مع hidden */
        #wb-popup.is-open {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* القوائم: fixed + فوق الدرج والـ iframe قدر الإمكان */
        #pkg-features-dd-panel,
        #mx-record-dd-panel {
            z-index: 220;
            will-change: auto;
        }
        #pkg-features-dd-panel.mx-dd-visible,
        #mx-record-dd-panel.mx-dd-visible {
            will-change: opacity;
        }
        #mx-record-dd-panel { box-shadow: 0 14px 36px rgba(0, 0, 0, 0.42), 0 0 0 1px rgba(148, 163, 184, 0.08); }
        #mx-classroom-nav-drawer { z-index: 205; }
        #mx-classroom-nav-drawer[data-open="1"] { visibility: visible !important; pointer-events: auto !important; }
        #mx-classroom-nav-drawer[data-open="1"] #mx-nav-drawer-backdrop { opacity: 1; pointer-events: auto; }
        #mx-classroom-nav-drawer[data-open="1"] #mx-nav-drawer-aside {
            transform: translateX(0) !important;
            pointer-events: auto;
        }
        .pkg-features-dd-panel-inner { box-shadow: 0 18px 40px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(34, 211, 238, 0.06); }
        .classroom-room-toolbar-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.3125rem 0.625rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            line-height: 1.25;
            transition: background-color 0.15s, border-color 0.15s, color 0.15s;
        }
        @media (min-width: 640px) {
            .classroom-room-toolbar-btn { padding: 0.375rem 0.75rem; }
        }
        .mx-mobile-toolbar-scroll {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .mx-mobile-toolbar-scroll::-webkit-scrollbar {
            display: none;
        }
        #pkg-features-dd-btn:focus-visible {
            outline: none;
            box-shadow: 0 0 0 2px rgba(15, 23, 42, 0.9), 0 0 0 4px rgba(34, 211, 238, 0.35);
        }
        #wb-popup-stage { min-height: 50vh; }
        .classroom-excalidraw-host {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }
        .classroom-excalidraw-host .excalidraw {
            --color-surface-lowest: #0f172a;
        }
        /* Glottical Whiteboard: مكتبة + روابط وخدمات خارجية داخل واجهة اللوحة */
        .mx-muallimx-whiteboard .excalidraw .layer-ui__library,
        .mx-muallimx-whiteboard .excalidraw .layer-ui__library-message,
        .mx-muallimx-whiteboard .excalidraw .library-menu,
        .mx-muallimx-whiteboard .excalidraw .library-menu-dropdown-container,
        .mx-muallimx-whiteboard .excalidraw .library-menu-dropdown-container--in-heading,
        .mx-muallimx-whiteboard .excalidraw .library-menu-items-container,
        .mx-muallimx-whiteboard .excalidraw .library-menu-control-buttons,
        .mx-muallimx-whiteboard .excalidraw .library-menu-control-buttons--at-bottom,
        .mx-muallimx-whiteboard .excalidraw .library-menu-browse-button,
        .mx-muallimx-whiteboard .excalidraw .library-menu-items-private-library-container,
        .mx-muallimx-whiteboard .excalidraw .library-actions-counter,
        .mx-muallimx-whiteboard .excalidraw .single-library-item,
        .mx-muallimx-whiteboard .excalidraw .single-library-item-wrapper,
        .mx-muallimx-whiteboard .excalidraw .library-unit,
        .mx-muallimx-whiteboard .excalidraw .selected-library-items,
        .mx-muallimx-whiteboard .excalidraw [class*="publish-library"] {
            display: none !important;
            visibility: hidden !important;
            pointer-events: none !important;
        }
        /* قائمة البرغر: روابط خارجية (GitHub / Discord / Twitter …) + عنوان المجموعة */
        .mx-muallimx-whiteboard .excalidraw .dropdown-menu a.dropdown-menu-item[href^="http://"],
        .mx-muallimx-whiteboard .excalidraw .dropdown-menu a.dropdown-menu-item[href^="https://"] {
            display: none !important;
            visibility: hidden !important;
            pointer-events: none !important;
        }
        .mx-muallimx-whiteboard .excalidraw .dropdown-menu .dropdown-menu-group:has(a.dropdown-menu-item[href^="http"]) {
            display: none !important;
        }
        .mx-muallimx-whiteboard .excalidraw .dropdown-menu .dropdown-menu-group:has(a.dropdown-menu-item[href^="https"]) {
            display: none !important;
        }
        /* مساعدة: شريط المدونة والتوثيق وGitHub */
        .mx-muallimx-whiteboard .excalidraw .HelpDialog__header {
            display: none !important;
        }
        /* تعاون مباشر (خوادم خارجية) */
        .mx-muallimx-whiteboard .excalidraw [data-testid="collab-button"] {
            display: none !important;
            pointer-events: none !important;
        }
        /* شاشة الترحيب: شعار Excalidraw وروابط ترحيب خارجية */
        .mx-muallimx-whiteboard .excalidraw .ExcalidrawLogo,
        .mx-muallimx-whiteboard .excalidraw .welcome-screen-center__logo {
            display: none !important;
            pointer-events: none !important;
        }
        .mx-muallimx-whiteboard .excalidraw a.welcome-screen-menu-item[href^="http://"],
        .mx-muallimx-whiteboard .excalidraw a.welcome-screen-menu-item[href^="https://"] {
            display: none !important;
            pointer-events: none !important;
        }
        /* حوارات محددة: روابط خارجية (بدون لمس نوافذ رابط الشكل على العناصر) */
        .mx-muallimx-whiteboard .excalidraw .ExportDialog a[href^="http://"],
        .mx-muallimx-whiteboard .excalidraw .ExportDialog a[href^="https://"],
        .mx-muallimx-whiteboard .excalidraw .ImageExportModal a[href^="http://"],
        .mx-muallimx-whiteboard .excalidraw .ImageExportModal a[href^="https://"],
        .mx-muallimx-whiteboard .excalidraw .OverwriteConfirm a[href^="http://"],
        .mx-muallimx-whiteboard .excalidraw .OverwriteConfirm a[href^="https://"],
        .mx-muallimx-whiteboard .excalidraw [class*="publish-library"] a[href^="http://"],
        .mx-muallimx-whiteboard .excalidraw [class*="publish-library"] a[href^="https://"],
        .mx-muallimx-whiteboard .excalidraw .HelpDialog a[href^="http://"],
        .mx-muallimx-whiteboard .excalidraw .HelpDialog a[href^="https://"] {
            display: none !important;
            pointer-events: none !important;
            visibility: hidden !important;
        }
        .classroom-excalidraw-loading {
            position: absolute;
            inset: 0;
            z-index: 5;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(15,23,42,0.75);
            color: #94a3b8;
            font-size: 14px;
        }
    </style>
</head>
<body class="bg-slate-950">
@php
    $academicObserverMode = !empty($academicObserverMode);
    $rp = ($useInstructorRoutes ?? false) ? 'instructor.' : 'student.';
    if ($academicObserverMode) {
        $roomExitUrl = $academicObserverExitUrl ?? route('employee.dashboard');
    } elseif (($useInstructorRoutes ?? false)) {
        $roomExitUrl = $meeting->consultation_request_id ? route('instructor.consultations.show', $meeting->consultation_request_id) : route('instructor.consultations.index');
    } else {
        $roomExitUrl = route('student.classroom.index');
    }
@endphp
    {{-- شريط Glottical العلوي — على الهاتف: صف علوي + زر سايدبار؛ من md: شريط أدوات أفقي --}}
    <header class="min-h-14 shrink-0 bg-gradient-to-l from-slate-900 to-slate-800 border-b border-slate-700/50 flex flex-col gap-2 px-3 sm:px-4 pt-[max(0.5rem,env(safe-area-inset-top))] pb-2 md:py-0 md:flex-row md:items-center md:justify-between md:gap-2 shadow-lg">
        <div class="flex items-center justify-between gap-2 w-full min-w-0 md:w-auto md:flex-1 md:justify-start">
            <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
            <a href="{{ $roomExitUrl }}" class="flex items-center gap-1.5 text-slate-300 hover:text-white transition-colors shrink-0">
                <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-cyan-500/20 text-cyan-400 flex items-center justify-center">
                    <i class="fas fa-video text-sm sm:text-[15px]"></i>
                </span>
                <span class="font-bold text-white text-[11px] sm:text-sm truncate max-w-[6.5rem] sm:max-w-[8rem] md:max-w-none">Glottical</span>
            </a>
            <span class="w-px h-5 bg-slate-600 hidden sm:block shrink-0"></span>
            <div class="flex items-center gap-1.5 min-w-0">
                <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse shadow shadow-emerald-400/40 shrink-0"></span>
                <span class="text-white font-semibold text-xs sm:text-sm truncate">{{ $meeting->title ?: 'غرفة ' . $meeting->code }}</span>
                <span class="text-slate-400 text-[10px] sm:text-xs px-1.5 py-0.5 rounded bg-slate-700/80 font-mono shrink-0">{{ $meeting->code }}</span>
            </div>
            </div>
            <button type="button" id="mx-nav-drawer-toggle" class="md:hidden shrink-0 inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-600 bg-slate-800 text-slate-100 hover:bg-slate-700 hover:border-cyan-500/30 transition-colors" aria-expanded="false" aria-controls="mx-classroom-nav-drawer" title="أدوات الغرفة">
                <i class="fas fa-bars text-lg" aria-hidden="true"></i>
            </button>
        </div>
        <div id="mx-toolbar-desktop-slot" class="hidden md:block w-full md:w-auto mx-mobile-toolbar-scroll overflow-x-auto overflow-y-visible pb-0.5 md:pb-0 touch-pan-x">
        <div id="mx-classroom-toolbar-inner" class="flex w-full flex-col items-stretch gap-3 md:w-auto md:min-w-max md:flex-row md:flex-nowrap md:items-center md:justify-end md:gap-1 md:gap-2 md:max-w-[min(100%,42rem)] lg:max-w-none pe-1 ps-0.5">
            <div class="flex flex-wrap items-center gap-1.5 md:flex-nowrap">
            <span class="hidden sm:inline-flex text-slate-300 text-[10px] sm:text-[11px] px-1.5 py-0.5 rounded-md bg-slate-700/80 whitespace-nowrap">
                طلاب: {{ (int) ($meeting->max_participants ?? 25) }}
            </span>
            <span class="inline-flex sm:hidden text-amber-200 text-[10px] px-1.5 py-0.5 rounded-md bg-amber-500/20 border border-amber-500/30 whitespace-nowrap" id="meeting-timer-chip-mobile">
                {{ (int) $effectiveDurationMinutes }} د
            </span>
            <span class="hidden sm:inline-flex text-amber-200 text-[10px] sm:text-[11px] px-1.5 py-0.5 rounded-md bg-amber-500/20 border border-amber-500/30 whitespace-nowrap" id="meeting-timer-chip">
                مدة الاجتماع: {{ (int) $effectiveDurationMinutes }} دقيقة (حد الباقة {{ (int) $maxDurationMinutes }})
            </span>
            <span class="hidden text-sky-200 text-[10px] sm:text-[11px] px-1.5 py-0.5 rounded-md bg-sky-500/20 border border-sky-500/30 max-w-[10rem] sm:max-w-[14rem] truncate" id="record-status-chip"></span>
            </div>
            <span class="hidden xl:block w-px h-4 bg-slate-600/50 shrink-0 rounded-full" aria-hidden="true"></span>
            <div class="flex w-full flex-col gap-2 md:w-auto md:flex-row md:flex-nowrap md:items-center md:justify-end md:gap-1.5">
            @unless($academicObserverMode)
            @if(!empty($subscriptionFeatureMenuItems))
            <div class="relative w-full shrink-0 md:w-auto" id="pkg-features-dd-wrap">
                <button type="button" id="pkg-features-dd-btn" class="classroom-room-toolbar-btn w-full justify-between bg-slate-700/80 hover:bg-slate-600/90 text-slate-100 border border-slate-600 hover:border-cyan-500/35 md:w-auto md:max-w-[11rem] lg:max-w-none" aria-expanded="false" aria-haspopup="true" title="مزايا اشتراكك — تفتح في تاب جديد">
                    <span class="flex h-6 w-6 sm:h-7 sm:w-7 shrink-0 items-center justify-center rounded-md bg-cyan-500/15 text-cyan-400 border border-cyan-500/20">
                        <i class="fas fa-layer-group text-[11px] sm:text-xs"></i>
                    </span>
                    <span class="flex min-w-0 flex-1 flex-col items-stretch text-right leading-tight">
                        <span class="truncate font-semibold text-slate-100 text-[11px] sm:text-xs">مزايا الباقة</span>
                        @if(!empty($subscriptionPackageLabel))
                        <span class="truncate text-[9px] sm:text-[10px] font-normal text-slate-400">{{ $subscriptionPackageLabel }}</span>
                        @else
                        <span class="text-[9px] sm:text-[10px] font-normal text-slate-500">اشتراكك النشط</span>
                        @endif
                    </span>
                    <i class="fas fa-chevron-down text-[9px] text-slate-400 shrink-0 transition-transform duration-200" id="pkg-features-dd-chevron" aria-hidden="true"></i>
                </button>
                <div id="pkg-features-dd-panel" class="pkg-features-dd-panel-inner hidden w-[min(100vw-2rem,19.5rem)] max-w-[calc(100vw-1rem)] rounded-xl border border-slate-600 bg-slate-900/98 backdrop-blur-md overflow-hidden" role="menu">
                    <div class="px-3 py-2.5 border-b border-slate-700/90 bg-slate-800/70 flex items-start gap-2">
                        <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-cyan-500/10 text-cyan-400">
                            <i class="fas fa-arrow-up-left-from-square text-[10px]"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-200 m-0 leading-snug">روابط سريعة</p>
                            <p class="text-[11px] text-slate-500 m-0 mt-0.5 leading-relaxed">كل رابط يُفتح في نافذة جديدة دون إغلاق الاجتماع.</p>
                        </div>
                    </div>
                    <div class="max-h-[min(58vh,20rem)] overflow-y-auto py-1.5 px-1">
                        @foreach($subscriptionFeatureMenuItems as $item)
                        <a href="{{ $item['url'] }}" target="_blank" rel="noopener noreferrer" role="menuitem" class="group flex items-center gap-3 px-2.5 py-2 mx-0.5 rounded-lg text-slate-200 hover:bg-slate-700/70 transition-colors border border-transparent hover:border-slate-600/80">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $item['icon_bg'] }} {{ $item['icon_text'] }} ring-1 ring-white/5 group-hover:ring-cyan-500/15 transition-[box-shadow]">
                                <i class="fas {{ $item['icon'] }} text-sm"></i>
                            </span>
                            <span class="min-w-0 flex-1 text-sm font-medium leading-snug text-right group-hover:text-white">{{ $item['label'] }}</span>
                            <i class="fas fa-arrow-up-left-from-square text-slate-500 group-hover:text-cyan-400/90 text-[11px] shrink-0 transition-colors"></i>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            <button type="button" id="btn-wb-popup-open" class="classroom-room-toolbar-btn w-full justify-center gap-2 bg-amber-600/25 hover:bg-amber-600/35 text-amber-100 border border-amber-500/40 md:w-auto md:justify-start" title="فتح الوايت بورد في نافذة منبثقة">
                <i class="fas fa-expand text-amber-300 text-[11px]"></i>
                <span class="sm:inline">الوايت بورد</span>
            </button>
            <label class="classroom-room-toolbar-btn w-full justify-between bg-slate-700/50 border border-slate-600 cursor-pointer select-none text-slate-200 md:w-auto md:max-w-[13rem]"
                   title="الضيف يرسم قلم/ممحاة فوق عرض الاجتماع؛ يظهر عندك فوق نفس الشاشة">
                <input type="checkbox" id="mx-classroom-toggle-guest-wb" class="rounded border-slate-500 text-amber-500 focus:ring-amber-500 shrink-0 scale-90"
                       {{ $meeting->allowsParticipantWhiteboard() ? 'checked' : '' }}>
                <span class="font-medium truncate"><span class="hidden sm:inline">رسم الضيف فوق العرض</span><span class="sm:hidden">رسم ضيف</span></span>
            </label>
            <div class="relative flex w-full flex-wrap items-center gap-2 md:inline-flex md:w-auto md:flex-nowrap md:gap-0" id="mx-record-dd-wrap">
                <div id="mx-record-idle-wrap" class="inline-flex w-full min-w-0 items-center rounded-lg border border-slate-600 overflow-hidden bg-slate-700/80 hover:bg-slate-600/90 transition-colors md:w-auto">
                    <button type="button" id="btn-record-menu" class="classroom-room-toolbar-btn w-full min-w-0 justify-between rounded-none border-0 bg-transparent text-slate-200 hover:bg-transparent md:w-auto" title="تسجيل المحاضرة أو تقرير صوتي" aria-expanded="false" aria-haspopup="true">
                        <i class="fas fa-circle-dot text-rose-400 text-[10px]" id="record-icon-idle"></i>
                        <span id="record-label-idle" class="min-w-0 flex-1 truncate sm:max-w-[9.5rem] lg:max-w-none">تسجيل أو تقرير</span>
                        <i class="fas fa-chevron-down text-[9px] text-slate-400 shrink-0 transition-transform duration-200" id="record-dd-chevron" aria-hidden="true"></i>
                    </button>
                </div>
                <button type="button" id="btn-record-stop" class="hidden classroom-room-toolbar-btn w-full justify-center rounded-lg bg-rose-600/90 hover:bg-rose-600 text-white font-semibold border border-rose-500/40 shadow-sm shadow-rose-900/25 md:w-auto md:max-w-[11rem]" title="إيقاف التسجيل">
                    <i class="fas fa-stop text-[10px] shrink-0" id="record-icon-active"></i>
                    <span id="record-label-active" class="truncate text-right">إيقاف</span>
                </button>
                <button type="button" id="btn-lecture-add-screen" class="hidden classroom-room-toolbar-btn w-full justify-center rounded-lg bg-cyan-600/20 hover:bg-cyan-600/30 text-cyan-100 font-medium border border-cyan-500/35 md:w-auto md:max-w-[13rem]" title="إضافة تبويب الاجتماع أو الشاشة إلى الفيديو المسجّل">
                    <i class="fas fa-desktop text-[10px] shrink-0 text-cyan-300"></i>
                    <span class="truncate hidden sm:inline">إضافة شاشة للتسجيل</span><span class="sm:hidden">+شاشة</span>
                </button>
                <div id="mx-record-dd-panel" class="hidden w-[min(100vw-1.5rem,18.5rem)] max-w-[calc(100vw-1rem)] rounded-lg border border-slate-600 bg-slate-900/98 backdrop-blur-md overflow-hidden" role="menu">
                    <p class="px-2.5 py-1.5 text-[10px] leading-snug text-slate-500 border-b border-slate-700/80 m-0">يبدأ التسجيل بالصوت فقط. أثناء التسجيل اضغط «إضافة شاشة للتسجيل» واختر <strong class="text-slate-400">تبويب الاجتماع</strong> ليظهر العرض والمشاركة في الفيديو.</p>
                    <button type="button" role="menuitem" data-mx-rec-mode="lecture" class="w-full text-right px-2.5 py-2 text-xs text-slate-200 hover:bg-slate-700/80 border-0 border-b border-slate-700/50 bg-transparent cursor-pointer flex items-center gap-2">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-rose-500/15 text-rose-300"><i class="fas fa-display text-[11px]"></i></span>
                        <span class="min-w-0 flex-1 leading-snug"><strong class="block text-slate-100 text-xs">تسجيل المحاضرة</strong><span class="text-[10px] text-slate-500">فيديو (اختياري) + ميكروفون</span></span>
                    </button>
                    <button type="button" role="menuitem" data-mx-rec-mode="report" class="w-full text-right px-2.5 py-2 text-xs text-slate-200 hover:bg-slate-700/80 border-0 bg-transparent cursor-pointer flex items-center gap-2">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-cyan-500/15 text-cyan-300"><i class="fas fa-file-audio text-[11px]"></i></span>
                        <span class="min-w-0 flex-1 leading-snug"><strong class="block text-slate-100 text-xs">إنشاء تقرير</strong><span class="text-[10px] text-slate-500">صوت فقط</span></span>
                    </button>
                </div>
            </div>
            <button type="button" id="btn-classroom-copy-join" class="classroom-room-toolbar-btn w-full justify-center gap-2 bg-slate-700/80 hover:bg-slate-600 text-slate-200 border border-slate-600 md:w-auto md:justify-start" title="نسخ رابط الانضمام" data-join-url="{{ url('classroom/join/' . $meeting->code) }}">
                <i class="fas fa-link text-[10px] btn-copy-join-ic"></i>
                <span class="btn-copy-join-tx min-w-0 truncate">مشاركة الرابط</span>
                <span class="btn-copy-join-tx-sm hidden min-w-0 truncate" aria-hidden="true">رابط</span>
            </button>
            <form method="POST" action="{{ route($rp.'classroom.end', $meeting) }}" class="inline w-full shrink-0 md:w-auto" id="mx-end-meeting-form" onsubmit="return confirm('إنهاء الاجتماع للجميع؟');">
                @csrf
                <button type="submit" id="mx-end-meeting-btn" class="classroom-room-toolbar-btn w-full justify-center bg-rose-600 hover:bg-rose-500 text-white font-semibold border border-rose-500/50 shadow-sm shadow-rose-900/20 md:w-auto md:justify-start">
                    <i class="fas fa-stop text-[10px]"></i><span class="hidden md:inline">إنهاء الاجتماع</span><span class="md:hidden">إنهاء</span>
                </button>
            </form>
            @else
            <span class="text-amber-200 text-[11px] px-2 py-1 rounded-md bg-amber-500/15 border border-amber-500/30 font-semibold">
                <i class="fas fa-eye text-[10px] ms-0.5"></i> مراقبة
            </span>
            @endunless
            </div>
        </div>
        </div>
    </header>

    {{-- درج أدوات الغرفة (هاتف فقط): نفس عناصر الشريط تُنقل هنا عبر JS --}}
    <div id="mx-classroom-nav-drawer" class="md:hidden fixed inset-0 invisible pointer-events-none" data-open="0" aria-hidden="true">
        <div id="mx-nav-drawer-backdrop" class="absolute inset-0 bg-slate-950/65 opacity-0 transition-opacity duration-200 pointer-events-none" aria-hidden="true"></div>
        <aside id="mx-nav-drawer-aside" class="absolute end-0 top-0 flex h-full min-h-0 w-[min(20rem,calc(100vw-2.5rem))] max-w-[100vw] flex-col border-s border-slate-600/80 bg-slate-900 shadow-2xl transition-transform duration-200 ease-out ltr:translate-x-full rtl:-translate-x-full pointer-events-none pt-[max(0.5rem,env(safe-area-inset-top))]" role="dialog" aria-modal="true" aria-labelledby="mx-nav-drawer-title">
            <div class="flex items-center justify-between gap-2 border-b border-slate-700/80 px-3 py-2.5 shrink-0">
                <h2 id="mx-nav-drawer-title" class="text-sm font-bold text-white m-0 truncate">أدوات الغرفة</h2>
                <button type="button" id="mx-nav-drawer-close" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-600 bg-slate-800 text-slate-200 hover:bg-slate-700 hover:text-white" aria-label="إغلاق القائمة">
                    <i class="fas fa-times text-sm" aria-hidden="true"></i>
                </button>
            </div>
            <div id="mx-toolbar-drawer-slot" class="flex min-h-0 flex-1 flex-col gap-3 overflow-y-auto overscroll-contain p-3 pb-[max(1rem,env(safe-area-inset-bottom))]"></div>
        </aside>
    </div>
    <script>
        (function () {
            var mq = window.matchMedia('(max-width: 767px)');
            var inner = document.getElementById('mx-classroom-toolbar-inner');
            var desk = document.getElementById('mx-toolbar-desktop-slot');
            var slot = document.getElementById('mx-toolbar-drawer-slot');
            var drawer = document.getElementById('mx-classroom-nav-drawer');
            var toggle = document.getElementById('mx-nav-drawer-toggle');
            var closeBtn = document.getElementById('mx-nav-drawer-close');
            var backdrop = document.getElementById('mx-nav-drawer-backdrop');
            var asideEl = document.getElementById('mx-nav-drawer-aside');

            function setDrawerOpen(open) {
                if (!drawer || !toggle) return;
                drawer.setAttribute('data-open', open ? '1' : '0');
                drawer.setAttribute('aria-hidden', open ? 'false' : 'true');
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                document.body.classList.toggle('mx-classroom-drawer-open', !!open);
                if (open) {
                    try { window.dispatchEvent(new Event('resize')); } catch (e) {}
                }
            }

            function placeToolbar() {
                if (!inner || !desk || !slot) return;
                if (mq.matches) {
                    slot.appendChild(inner);
                } else {
                    desk.appendChild(inner);
                    setDrawerOpen(false);
                }
            }

            placeToolbar();
            if (typeof mq.addEventListener === 'function') {
                mq.addEventListener('change', placeToolbar);
            } else if (typeof mq.addListener === 'function') {
                mq.addListener(placeToolbar);
            }
            window.addEventListener('resize', placeToolbar);

            if (toggle) {
                toggle.addEventListener('click', function (e) {
                    e.stopPropagation();
                    setDrawerOpen(drawer.getAttribute('data-open') !== '1');
                });
            }
            if (closeBtn) closeBtn.addEventListener('click', function () { setDrawerOpen(false); });
            if (backdrop) {
                backdrop.addEventListener('click', function () { setDrawerOpen(false); });
            }
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') setDrawerOpen(false);
            });
            document.addEventListener('mousedown', function (e) {
                if (!drawer || drawer.getAttribute('data-open') !== '1') return;
                if (toggle && toggle.contains(e.target)) return;
                if (backdrop && e.target === backdrop) {
                    setDrawerOpen(false);
                    return;
                }
                if (asideEl && asideEl.contains(e.target)) return;
                setDrawerOpen(false);
            }, true);
        })();
    </script>

    <div class="room-body">
    {{-- بوابة إذن الميكروفون/الكاميرا قبل تحميل Jitsi (تحل مشكلة بعض الأجهزة التي لا تُظهر الطلب تلقائياً) --}}
    <div id="permission-gate" class="absolute inset-0 z-20 bg-slate-950/95 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="w-full max-w-xl rounded-2xl border border-slate-700 bg-slate-900/95 shadow-2xl p-6 sm:p-7 text-center">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-cyan-500/15 text-cyan-400 flex items-center justify-center mb-4">
                <i class="fas fa-microphone-lines text-xl"></i>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-white mb-2">السماح بالميكروفون والكاميرا</h2>
            <p class="text-slate-300 text-sm leading-7 mb-5">
                قبل دخول الاجتماع، اضغط على الزر التالي للسماح بالوصول إلى
                <strong class="text-white">الميكروفون والكاميرا</strong>.
                هذا يساعد في حل مشكلة الأجهزة التي لا يظهر فيها طلب الإذن تلقائياً.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <button type="button" id="btn-request-media"
                        class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-cyan-500 hover:bg-cyan-600 text-white font-semibold transition-colors">
                    <i class="fas fa-shield-check"></i>
                    طلب الأذونات والدخول
                </button>
                <button type="button" id="btn-join-without-media"
                        class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-100 font-semibold transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    دخول بدون تفعيل الأجهزة
                </button>
            </div>
            <p id="permission-help" class="mt-4 text-xs text-slate-400"></p>
        </div>
    </div>

    {{-- تنبيه: الخادم التجريبي للاختبار فقط — يُقطع بعد 5 دقائق --}}
    @if(!empty($isDemoJitsi))
    <div class="bg-amber-500/15 border-b border-amber-500/40 px-4 py-2 flex items-center justify-between gap-3 text-amber-800 dark:text-amber-200 text-sm flex-shrink-0">
        <span class="flex items-center gap-2">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>للاختبار فقط:</strong> استخدام خادم الاجتماعات التجريبي قد يقطع المكالمة بعد 5 دقائق. للإنتاج استخدم خادم الاجتماعات الخاص بك من إعدادات نظام اللايف.
        </span>
        <button type="button" onclick="this.parentElement.remove()" class="text-amber-600 hover:text-amber-800 p-1" aria-label="إغلاق"><i class="fas fa-times"></i></button>
    </div>
    @endif

    {{-- منطقة الاجتماع --}}
    <div id="meeting-stage" class="flex-1 min-h-0 relative w-full">
        <main id="jitsi-container" class="flex-1 min-h-0 relative w-full" role="application" aria-label="غرفة الاجتماع">
            <div id="jitsi-loading" class="flex flex-col items-center justify-center h-full text-slate-400 text-sm gap-3">
                <i class="fas fa-spinner fa-spin text-2xl text-cyan-400"></i>
                <span>جاري تحميل غرفة الاجتماع…</span>
            </div>
            <div id="jitsi-error" class="hidden flex-col items-center justify-center h-full p-6 text-center max-w-lg mx-auto" style="display: none;">
                <i class="fas fa-exclamation-triangle text-amber-500 text-4xl mb-3"></i>
                <p class="font-bold text-slate-200 mb-2">لا يمكن تحميل غرفة الاجتماع</p>
                <p class="text-slate-400 text-sm mb-3">المتصفح لم يستطع الاتصال بـ <strong class="text-slate-300">{{ $jitsiDomain }}</strong>.</p>
                <ul class="text-right text-slate-400 text-sm mb-4 list-none space-y-1">
                    <li>• النطاق يجب أن يكون <strong class="text-slate-300">النطاق الصحيح لخادم الاجتماعات</strong> (مثلاً <code class="bg-slate-700 px-1 rounded">meet.glottical.com</code> وليس بالضرورة الموقع الرئيسي).</li>
                    <li>• جرّب فتح <a href="https://{{ $jitsiDomain }}/external_api.js" target="_blank" rel="noopener" class="text-cyan-400 hover:underline">هذا الرابط</a> في تاب جديد — إن لم يُحمّل، فخادم الاجتماعات غير متاح من جهازك أو غير مضبوط على هذا النطاق.</li>
                    <li>• إن كان خادم الاجتماعات على نطاق فرعي (مثل meet.glottical.com)، حدّث النطاق من: <strong>لوحة الإدارة → سيرفرات البث</strong> ثم «استخدام كنطاق افتراضي» للسيرفر الصحيح.</li>
                </ul>
                <a href="https://{{ $jitsiDomain }}/{{ $meeting->room_name }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-600 text-white font-semibold transition-colors">
                    <i class="fas fa-external-link-alt"></i> فتح الغرفة في نافذة جديدة
                </a>
            </div>
        </main>
        @unless(!empty($academicObserverMode))
        @include('partials.mx-share-annotation-overlay', [
            'mxAnnRole' => 'viewer_poll',
            'mxAnnPollUrl' => route($rp . 'classroom.share-annotations', $meeting),
        ])
        @endunless
    </div>
    </div>

    {{-- لوحة بيضاء منبثقة بشاشة كبيرة --}}
    <div id="wb-popup" class="hidden fixed inset-0 p-2 sm:p-4" inert aria-hidden="true" role="dialog" aria-labelledby="wb-popup-title" aria-modal="true">
        <div id="wb-popup-backdrop" class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm cursor-pointer" aria-hidden="true"></div>
        <div id="wb-popup-panel" class="relative z-[141] flex flex-col w-full max-w-[min(1680px,99vw)] h-[min(92vh,calc(100dvh-1rem))] rounded-2xl border border-slate-600 bg-slate-900 shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between gap-3 px-4 py-3 border-b border-slate-700 bg-slate-800/95 shrink-0">
                <h2 id="wb-popup-title" class="text-base font-bold text-white m-0 flex items-center gap-2">
                    <i class="fas fa-chalkboard text-amber-400"></i>
                    الوايت بورد
                </h2>
                <div class="flex items-center gap-2">
                    <button type="button" id="btn-wb-popup-fullscreen" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 text-slate-200 text-xs font-medium border border-slate-600" title="ملء الشاشة (اخرج بـ Esc)">
                        <i class="fas fa-expand"></i>
                        <span class="hidden sm:inline">ملء الشاشة</span>
                    </button>
                    <button type="button" id="wb-popup-close" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-slate-700 hover:bg-rose-600/80 text-white text-lg leading-none border border-slate-600" aria-label="إغلاق اللوحة">&times;</button>
                </div>
            </div>
            <div id="wb-popup-stage" class="relative flex-1 min-h-0 bg-[#121212]">
                <div id="classroom-excalidraw-root" class="classroom-excalidraw-host mx-muallimx-whiteboard" data-view-only="0" data-lang="ar"></div>
                <div id="classroom-excalidraw-loading" class="classroom-excalidraw-loading">جاري تحميل Glottical Whiteboard…</div>
            </div>
            <div id="wb-popup-toolbar" class="flex flex-wrap items-center justify-center gap-2 px-4 py-2.5 border-t border-slate-700 bg-slate-800/95 shrink-0">
                <span class="text-slate-400 text-[11px] leading-relaxed text-center max-w-3xl">
                    <strong class="text-slate-200">Glottical Whiteboard</strong> — أدوات رسم كاملة (أشكال، نص، تصدير PNG/SVG من القائمة). الرسم محلي على جهازك فقط.
                </span>
            </div>
        </div>
    </div>

    {{-- نافذة رفع التسجيل — يمكن تصغيرها والمتابعة داخل الغرفة --}}
    <div id="mx-upload-modal" class="hidden fixed inset-0 z-[180] flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-sm" aria-hidden="true">
        <div class="w-full max-w-md rounded-2xl border border-slate-600 bg-slate-900 shadow-2xl p-5 sm:p-6" role="dialog" aria-labelledby="mx-upload-modal-title" aria-modal="true">
            <h3 id="mx-upload-modal-title" class="text-lg font-bold text-white m-0 mb-1">جاري رفع التسجيل</h3>
            <p id="mx-upload-modal-sub" class="text-xs text-slate-500 m-0 mb-4">يتم رفع وحفظ التسجيل. يمكنك تصغير هذه النافذة والمتابعة في الاجتماع.</p>
            <div class="h-2.5 rounded-full bg-slate-700 overflow-hidden mb-2">
                <div id="mx-upload-modal-bar" class="h-full w-0 bg-cyan-500 transition-[width] duration-150"></div>
            </div>
            <p id="mx-upload-modal-status" class="text-sm text-slate-300 mb-4 min-h-[2.75rem] whitespace-pre-wrap m-0"></p>
            <div class="flex flex-wrap gap-2">
                <button type="button" id="mx-upload-modal-bg" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-100 text-sm font-medium border border-slate-600">متابعة في الخلفية</button>
                <button type="button" id="mx-upload-modal-retry" class="hidden inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-600/90 hover:bg-amber-600 text-white text-sm font-medium border border-amber-500/40">إعادة المحاولة</button>
            </div>
        </div>
    </div>
    <button type="button" id="mx-upload-chip" class="hidden fixed bottom-4 start-4 z-[185] max-w-[min(calc(100vw-2rem),18rem)] inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-800/95 text-slate-100 text-xs font-medium border border-slate-600 shadow-xl hover:bg-slate-700 transition-colors" title="عرض تقدم الرفع">
        <i class="fas fa-cloud-arrow-up text-cyan-400"></i>
        <span id="mx-upload-chip-text" class="truncate">رفع التسجيل</span>
    </button>

    @include('partials.jitsi-iframe-media-allow')
    @php
        $mxBp = rtrim((string) request()->getBasePath(), '/');
        $mxP = $mxBp !== '' ? $mxBp : '';
        $mxExBases = array_values(array_unique(array_filter([
            $mxP . '/mx-vendor/excalidraw/',
            '/mx-vendor/excalidraw/',
            $mxP . '/vendor/excalidraw/',
            '/vendor/excalidraw/',
        ])));
    @endphp
    {{-- محاذاة القوائم المنسدلة خارج منطقة overflow-x (الهاتف) --}}
    <script>
        /**
         * @param {HTMLElement} panel
         * @param {HTMLElement} alignEl — عنصر المحاذاة الأفقية (مثل الحاوية relative + end-0)
         * @param {HTMLElement} [topEl] — أسفل هذا العنصر يُفتح اللوح (افتراضياً alignEl)
         */
        window.mxPositionClassroomDropdown = function (panel, alignEl, topEl) {
            if (!panel || !alignEl) return;
            if (!topEl) topEl = alignEl;

            var gap = 6;
            var vw = window.innerWidth || document.documentElement.clientWidth || 0;
            var vh = window.innerHeight || document.documentElement.clientHeight || 0;
            var ar = alignEl.getBoundingClientRect();
            var tr = topEl.getBoundingClientRect();
            var rtl = ((document.documentElement.getAttribute('dir') || 'ltr').toLowerCase()) === 'rtl';

            panel.style.position = 'fixed';
            panel.style.zIndex = '220';

            var pw = panel.offsetWidth;

            var wantLeft = rtl ? ar.left : (ar.right - pw);
            if (wantLeft + pw > vw - 8) wantLeft = vw - 8 - pw;
            if (wantLeft < 8) wantLeft = 8;
            panel.style.left = wantLeft + 'px';
            panel.style.right = 'auto';

            var ph = panel.offsetHeight;
            var top = tr.bottom + gap;
            if (top + ph > vh - 8) top = tr.top - gap - ph;
            if (top < 8) top = 8;
            panel.style.top = top + 'px';
            panel.style.bottom = 'auto';
        };
        window.mxClearClassroomDropdownPosition = function (panel) {
            if (!panel) return;
            panel.classList.remove('mx-dd-visible');
            ['position', 'top', 'left', 'right', 'width', 'maxWidth', 'bottom', 'zIndex', 'opacity', 'transition', 'pointerEvents'].forEach(function (k) {
                panel.style[k] = '';
            });
        };
    </script>
    {{-- Glottical Whiteboard: تحميل ديناميكي + أكثر من مسار (Laravel ثم ملفات public المباشرة) --}}
    <script>
        (function() {
            var jitsiDomain = '{{ $jitsiDomain }}';
            var roomName = '{{ $meeting->room_name }}';
            var userName = {!! json_encode($jitsiDisplayName ?? $user->name) !!};
            var userEmail = {!! json_encode($user->email ?? '') !!};
            var container = document.getElementById('jitsi-container');
            var loadingEl = document.getElementById('jitsi-loading');
            var errorEl = document.getElementById('jitsi-error');
            var meetingEndsAt = {!! json_encode(optional($meetingEndsAt)->toIso8601String()) !!};
            var timerChip = document.getElementById('meeting-timer-chip');
            var timerChipMobile = document.getElementById('meeting-timer-chip-mobile');
            var mxMeetingId = {{ (int) $meeting->id }};
            var recordDdWrap = document.getElementById('mx-record-dd-wrap');
            var btnRecordMenu = document.getElementById('btn-record-menu');
            var btnRecordStop = document.getElementById('btn-record-stop');
            var endMeetingForm = document.getElementById('mx-end-meeting-form');
            var endMeetingBtn = document.getElementById('mx-end-meeting-btn');
            var recordIdleWrap = document.getElementById('mx-record-idle-wrap');
            var recordDdPanel = document.getElementById('mx-record-dd-panel');
            var recordDdChevron = document.getElementById('record-dd-chevron');
            var recordIconIdle = document.getElementById('record-icon-idle');
            var recordLabelIdle = document.getElementById('record-label-idle');
            var recordIconActive = document.getElementById('record-icon-active');
            var recordLabelActive = document.getElementById('record-label-active');
            var recordStatusChip = document.getElementById('record-status-chip');
            var mxUploadModal = document.getElementById('mx-upload-modal');
            var mxUploadModalBar = document.getElementById('mx-upload-modal-bar');
            var mxUploadModalStatus = document.getElementById('mx-upload-modal-status');
            var mxUploadModalTitle = document.getElementById('mx-upload-modal-title');
            var mxUploadModalSub = document.getElementById('mx-upload-modal-sub');
            var mxUploadModalBg = document.getElementById('mx-upload-modal-bg');
            var mxUploadModalRetry = document.getElementById('mx-upload-modal-retry');
            var mxUploadChip = document.getElementById('mx-upload-chip');
            var mxUploadChipText = document.getElementById('mx-upload-chip-text');
            var uploadRecordingUrl = '{{ route($rp . 'classroom.recording.upload', $meeting) }}';
            var presignRecordingUrl = '{{ route($rp . 'classroom.recording.presign', $meeting) }}';
            var completeRecordingUrl = '{{ route($rp . 'classroom.recording.complete', $meeting) }}';
            var presignAudioUrl = '{{ route($rp . 'classroom.recording-audio.presign', $meeting) }}';
            var uploadAudioUrl = '{{ route($rp . 'classroom.recording-audio.upload', $meeting) }}';
            var completeAudioUrl = '{{ route($rp . 'classroom.recording-audio.complete', $meeting) }}';
            var recordingUploadTabBaseUrl = '{{ route($rp . 'classroom.recording.upload-tab', $meeting) }}';
            var csrfToken = '{{ csrf_token() }}';
            var participantWbUrl = '{{ route($rp . 'classroom.participant-whiteboard', $meeting) }}';
            var mxClassroomGuestWbToggle = document.getElementById('mx-classroom-toggle-guest-wb');
            var mxClassroomGuestWbSaving = false;
            if (mxClassroomGuestWbToggle) {
                mxClassroomGuestWbToggle.addEventListener('change', function () {
                    if (mxClassroomGuestWbSaving) return;
                    mxClassroomGuestWbSaving = true;
                    var want = mxClassroomGuestWbToggle.checked;
                    fetch(participantWbUrl, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                        body: JSON.stringify({ allow: want }),
                    }).then(function (r) {
                        if (!r.ok) mxClassroomGuestWbToggle.checked = !want;
                    }).catch(function () {
                        mxClassroomGuestWbToggle.checked = !want;
                    }).finally(function () {
                        mxClassroomGuestWbSaving = false;
                    });
                });
            }
            var btnClassroomCopyJoin = document.getElementById('btn-classroom-copy-join');
            if (btnClassroomCopyJoin) {
                btnClassroomCopyJoin.addEventListener('click', function () {
                    var joinUrl = btnClassroomCopyJoin.getAttribute('data-join-url') || '';
                    var ic = btnClassroomCopyJoin.querySelector('.btn-copy-join-ic');
                    var tx = btnClassroomCopyJoin.querySelector('.btn-copy-join-tx');
                    var txSm = btnClassroomCopyJoin.querySelector('.btn-copy-join-tx-sm');
                    function restoreJoinBtn() {
                        if (ic) {
                            ic.className = 'fas fa-link text-[10px] btn-copy-join-ic';
                        }
                        if (tx) tx.textContent = 'مشاركة الرابط';
                        if (txSm) txSm.textContent = 'رابط';
                    }
                    function showCopied() {
                        if (ic) {
                            ic.className = 'fas fa-check text-[10px] btn-copy-join-ic text-emerald-400';
                        }
                        if (tx) tx.textContent = 'تم النسخ';
                        if (txSm) txSm.textContent = 'تم';
                    }
                    navigator.clipboard.writeText(joinUrl).then(function () {
                        showCopied();
                        setTimeout(restoreJoinBtn, 2000);
                    }).catch(function () {
                        if (tx) tx.textContent = 'فشل النسخ';
                        if (txSm) tx.textContent = '!';
                        setTimeout(restoreJoinBtn, 2500);
                    });
                });
            }
            var roomExitUrl = {!! json_encode($roomExitUrl) !!};
            var permissionGate = document.getElementById('permission-gate');
            var permissionHelp = document.getElementById('permission-help');
            var requestMediaBtn = document.getElementById('btn-request-media');
            var joinWithoutMediaBtn = document.getElementById('btn-join-without-media');
            var api = null;
            var hasJoinedConference = false;
            var isRecording = false;
            var recordingKind = null;
            var mediaRecorder = null;
            var recordedChunks = [];
            var recordingStartedAt = null;
            var activeRecordingStream = null;
            var micStream = null;
            var audioRecorder = null;
            var recordedAudioChunks = [];
            var audioOnlyStream = null;
            var mxRecordDdOpen = false;
            var mxUploadModalMinimized = false;
            var mxCurrentUploadJob = null;
            var mxLastFailedJob = null;
            var pendingEndMeetingSubmit = false;
            var lectureCanvas = null;
            var lectureCtx = null;
            var lectureCanvasStream = null;
            var lectureDisplayStream = null;
            var lectureDisplayVideo = null;
            var lectureRafId = null;
            var btnLectureAddScreen = document.getElementById('btn-lecture-add-screen');

            var wbCanvas = null;
            var wbCtx = null;

            var wbPopup = document.getElementById('wb-popup');
            var wbPopupStage = document.getElementById('wb-popup-stage');
            var wbPopupPanel = document.getElementById('wb-popup-panel');
            var excRoot = document.getElementById('classroom-excalidraw-root');
            var excLoading = document.getElementById('classroom-excalidraw-loading');
            var excReactRoot = null;
            var excMounted = false;
            var excMountPromise = null;
            var wbPopupClosing = false;
            var mxExcalidrawBases = {!! json_encode($mxExBases) !!};
            var excVendorPromise = null;

            function excShowLoading(on) {
                if (excLoading) excLoading.style.display = on ? 'flex' : 'none';
            }

            function nudgeClassroomExLayout() {
                window.dispatchEvent(new Event('resize'));
                if (window.requestAnimationFrame) {
                    requestAnimationFrame(function() { window.dispatchEvent(new Event('resize')); });
                }
            }

            function mxAbsAssetUrl(basePath) {
                var b = String(basePath || '').replace(/\/?$/, '/');
                if (b.indexOf('http') === 0) return b;
                if (b.charAt(0) !== '/') b = '/' + b;
                return window.location.origin + b;
            }

            function loadScriptSequential(url) {
                return new Promise(function(resolve, reject) {
                    var s = document.createElement('script');
                    s.src = url;
                    s.async = false;
                    s.onerror = function() {
                        s.onerror = s.onload = null;
                        reject(new Error('فشل تحميل: ' + url));
                    };
                    s.onload = function() {
                        s.onerror = s.onload = null;
                        resolve();
                    };
                    (document.head || document.documentElement).appendChild(s);
                });
            }

            function getExcalidrawLib() {
                if (typeof ExcalidrawLib !== 'undefined') return ExcalidrawLib;
                if (typeof window.ExcalidrawLib !== 'undefined') return window.ExcalidrawLib;
                return null;
            }

            function ensureExcalidrawVendorLoaded() {
                if (window.React && window.ReactDOM && getExcalidrawLib()) {
                    return Promise.resolve();
                }
                if (excVendorPromise) return excVendorPromise;
                var bases = Array.isArray(mxExcalidrawBases) ? mxExcalidrawBases : [];
                if (!bases.length) bases = ['/mx-vendor/excalidraw/', '/vendor/excalidraw/'];

                function loadFromBase(basePath) {
                    var root = String(basePath || '').replace(/\/?$/, '/');
                    window.EXCALIDRAW_ASSET_PATH = root + 'dist/';
                    var prefix = mxAbsAssetUrl(root);
                    return loadScriptSequential(prefix + 'react.production.min.js')
                        .then(function() { return loadScriptSequential(prefix + 'react-dom.production.min.js'); })
                        .then(function() { return loadScriptSequential(prefix + 'dist/excalidraw.production.min.js'); })
                        .then(function() {
                            if (!window.React || !window.ReactDOM || !getExcalidrawLib()) {
                                throw new Error('تعذّر تعريف مكوّنات Glottical Whiteboard بعد التحميل');
                            }
                        });
                }

                function tryNext(i) {
                    if (i >= bases.length) {
                        return Promise.reject(new Error('فشل كل مسارات التحميل. تأكد من وجود public/vendor/excalidraw ومسار Laravel /mx-vendor/excalidraw'));
                    }
                    return loadFromBase(bases[i]).catch(function() { return tryNext(i + 1); });
                }

                excVendorPromise = tryNext(0).catch(function(e) {
                    excVendorPromise = null;
                    throw e;
                });
                return excVendorPromise;
            }

            function mountClassroomExcalidrawOnce() {
                if (excMounted) return Promise.resolve();
                if (excMountPromise) return excMountPromise;
                if (!excRoot) return Promise.reject(new Error('no excalidraw root'));
                excShowLoading(true);

                function failMount(err) {
                    console.error('[Glottical Whiteboard]', err);
                    excMountPromise = null;
                    excShowLoading(false);
                    if (excLoading) {
                        var detail = (err && err.message) ? String(err.message) : '';
                        if (detail.length > 240) detail = detail.slice(0, 237) + '…';
                        excLoading.textContent = 'تعذّر تهيئة Glottical Whiteboard.' + (detail ? (' ' + detail) : '') + ' — Network: جرّب ‎/mx-vendor/excalidraw/react.production.min.js‎ أو ‎/vendor/excalidraw/…‎ برمز 200.';
                        excLoading.style.display = 'flex';
                    }
                }

                excMountPromise = ensureExcalidrawVendorLoaded()
                    .then(function() {
                        return new Promise(function(resolve, reject) {
                            var deadline = Date.now() + 5000;
                            function tryMount() {
                                var Lib = getExcalidrawLib();
                                var ReactMod = window.React;
                                var ReactDOM = window.ReactDOM;
                                if (!Lib || !ReactMod || !ReactDOM) {
                                    failMount(new Error('المكتبات غير متاحة بعد التحميل'));
                                    reject(new Error('missing after load'));
                                    return;
                                }
                                var rect = excRoot.getBoundingClientRect();
                                if (rect.width < 8 || rect.height < 8) {
                                    if (Date.now() > deadline) {
                                        failMount(new Error('الحاوية بلا أبعاد كافية بعد فتح النافذة.'));
                                        reject(new Error('container size'));
                                        return;
                                    }
                                    requestAnimationFrame(tryMount);
                                    return;
                                }
                                try {
                                    var Excalidraw = Lib.Excalidraw;
                                    var createRoot = ReactDOM.createRoot;
                                    // مكوّن اللوحة مُصدَّر كـ React.memo — typeof يكون "object" وليس "function"
                                    if (Excalidraw == null || (typeof Excalidraw !== 'function' && typeof Excalidraw !== 'object')) {
                                        throw new Error('حزمة Glottical Whiteboard غير صالحة (مكوّن اللوحة).');
                                    }
                                    if (typeof createRoot !== 'function') {
                                        throw new Error('ReactDOM.createRoot غير متاح (تحقق من react-dom 18).');
                                    }
                                    var viewOnly = excRoot.getAttribute('data-view-only') === '1';
                                    var lang = excRoot.getAttribute('data-lang') || '';
                                    var props = {
                                        viewModeEnabled: viewOnly,
                                        excalidrawAPI: function(api) {
                                            window.__mxClassroomExcalidrawAPI = api;
                                        }
                                    };
                                    if (lang.indexOf('ar') === 0) props.langCode = 'ar-SA';
                                    excReactRoot = createRoot(excRoot);
                                    excReactRoot.render(ReactMod.createElement(Excalidraw, props));
                                    excMounted = true;
                                    excShowLoading(false);
                                    nudgeClassroomExLayout();
                                    resolve();
                                } catch (err) {
                                    failMount(err);
                                    reject(err);
                                }
                            }
                            requestAnimationFrame(tryMount);
                        });
                    })
                    .catch(function(err) {
                        failMount(err);
                        excMountPromise = null;
                        return Promise.reject(err);
                    });

                return excMountPromise;
            }

            function mergeExcalidrawToMain(done) {
                var api = window.__mxClassroomExcalidrawAPI;
                if (!api || !wbCanvas || !wbCtx) {
                    if (done) done();
                    return;
                }
                function runExport() {
                    var Lib = getExcalidrawLib();
                    var exportToBlob = Lib && Lib.exportToBlob;
                    if (typeof exportToBlob !== 'function') {
                        if (done) done();
                        return;
                    }
                    exportToBlob({
                        elements: api.getSceneElements(),
                        appState: api.getAppState(),
                        files: api.getFiles ? api.getFiles() : null,
                        mimeType: 'image/png',
                        exportWithDarkMode: false,
                        exportBackground: true
                    }).then(function(blob) {
                        if (!blob) {
                            if (done) done();
                            return;
                        }
                        var url = URL.createObjectURL(blob);
                        var img = new Image();
                        img.onload = function() {
                            resizeWbCanvas();
                            wbCtx.save();
                            wbCtx.setTransform(1, 0, 0, 1, 0, 0);
                            wbCtx.clearRect(0, 0, wbCanvas.width, wbCanvas.height);
                            wbCtx.drawImage(img, 0, 0, wbCanvas.width, wbCanvas.height);
                            wbCtx.restore();
                            var dpr = window.devicePixelRatio || 1;
                            wbCtx.setTransform(dpr, 0, 0, dpr, 0, 0);
                            wbCtx.lineCap = 'round';
                            wbCtx.lineJoin = 'round';
                            URL.revokeObjectURL(url);
                            if (done) done();
                        };
                        img.onerror = function() {
                            URL.revokeObjectURL(url);
                            if (done) done();
                        };
                        img.src = url;
                    }).catch(function() {
                        if (done) done();
                    });
                }
                if (getExcalidrawLib()) {
                    runExport();
                } else {
                    ensureExcalidrawVendorLoaded().then(runExport).catch(function() { if (done) done(); });
                }
            }

            function openWbPopup() {
                if (!wbPopup) return;
                wbPopup.removeAttribute('inert');
                wbPopup.classList.remove('hidden');
                wbPopup.classList.add('is-open');
                wbPopup.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
                mountClassroomExcalidrawOnce().then(function() {
                    setTimeout(nudgeClassroomExLayout, 80);
                    setTimeout(nudgeClassroomExLayout, 400);
                }).catch(function() {});
            }

            function closeWbPopup() {
                if (wbPopupClosing) return;
                if (!wbPopup || wbPopup.classList.contains('hidden')) return;
                wbPopupClosing = true;

                function detachWhiteboardFromMeetingUi() {
                    var ae = document.activeElement;
                    if (ae && typeof ae.blur === 'function' && wbPopup.contains(ae)) {
                        ae.blur();
                    }
                    try {
                        var sel = window.getSelection && window.getSelection();
                        if (sel && typeof sel.removeAllRanges === 'function') {
                            sel.removeAllRanges();
                        }
                    } catch (eSel) {}

                    wbPopup.classList.add('hidden');
                    wbPopup.classList.remove('is-open');
                    wbPopup.setAttribute('aria-hidden', 'true');
                    wbPopup.setAttribute('inert', '');
                    document.body.style.overflow = '';

                    var reopenBtn = document.getElementById('btn-wb-popup-open');
                    if (reopenBtn && typeof reopenBtn.focus === 'function') {
                        try {
                            reopenBtn.focus({ preventScroll: true });
                        } catch (eF) {
                            try { reopenBtn.focus(); } catch (eF2) {}
                        }
                    }

                    wbPopupClosing = false;
                }

                function runClosePipeline() {
                    mergeExcalidrawToMain(function() {
                        detachWhiteboardFromMeetingUi();
                    });
                }

                var fsEl = document.fullscreenElement;
                if (fsEl && wbPopup.contains(fsEl)) {
                    var p = document.exitFullscreen && document.exitFullscreen();
                    if (p && typeof p.then === 'function') {
                        p.then(runClosePipeline).catch(runClosePipeline);
                    } else {
                        runClosePipeline();
                    }
                } else {
                    runClosePipeline();
                }
            }

            function resizeWbCanvas() {}

            if (wbPopup) {
                var wbOpenPopupBtn = document.getElementById('btn-wb-popup-open');
                if (wbOpenPopupBtn) wbOpenPopupBtn.addEventListener('click', openWbPopup);
                var wbClosePopupBtn = document.getElementById('wb-popup-close');
                if (wbClosePopupBtn) wbClosePopupBtn.addEventListener('click', closeWbPopup);
                var wbBackdropEl = document.getElementById('wb-popup-backdrop');
                if (wbBackdropEl) wbBackdropEl.addEventListener('click', closeWbPopup);
                var wbFsBtn = document.getElementById('btn-wb-popup-fullscreen');
                if (wbFsBtn && wbPopupPanel) {
                    wbFsBtn.addEventListener('click', function() {
                        if (!document.fullscreenElement) {
                            wbPopupPanel.requestFullscreen().catch(function() {});
                        } else {
                            try { document.exitFullscreen(); } catch (ex) {}
                        }
                    });
                }

                document.addEventListener('keydown', function(ev) {
                    if (ev.key === 'Escape' && wbPopup && !wbPopup.classList.contains('hidden')) {
                        closeWbPopup();
                    }
                });

                if (wbPopupStage && typeof ResizeObserver !== 'undefined') {
                    new ResizeObserver(function() {
                        if (wbPopup && !wbPopup.classList.contains('hidden')) {
                            nudgeClassroomExLayout();
                        }
                    }).observe(wbPopupStage);
                }
            }

            function showError() {
                if (loadingEl) loadingEl.classList.add('hidden');
                if (errorEl) { errorEl.style.display = 'flex'; errorEl.classList.add('flex'); }
            }

            function setRecordDdOpen(open) {
                mxRecordDdOpen = !!open;
                if (btnRecordMenu) btnRecordMenu.setAttribute('aria-expanded', open ? 'true' : 'false');
                if (recordDdChevron) recordDdChevron.style.transform = open ? 'rotate(180deg)' : '';
                if (!open) {
                    if (recordDdPanel) recordDdPanel.classList.add('hidden');
                    if (typeof window.mxClearClassroomDropdownPosition === 'function') {
                        window.mxClearClassroomDropdownPosition(recordDdPanel);
                    }
                } else if (recordDdPanel && recordDdWrap && typeof window.mxPositionClassroomDropdown === 'function') {
                    recordDdPanel.style.opacity = '0';
                    recordDdPanel.style.pointerEvents = 'none';
                    recordDdPanel.classList.remove('hidden');
                    recordDdPanel.classList.add('mx-dd-visible');
                    window.mxPositionClassroomDropdown(recordDdPanel, recordDdWrap, btnRecordMenu);
                    requestAnimationFrame(function () {
                        requestAnimationFrame(function () {
                            recordDdPanel.style.transition = 'opacity 0.14s ease-out';
                            recordDdPanel.style.opacity = '1';
                            recordDdPanel.style.pointerEvents = '';
                            window.mxPositionClassroomDropdown(recordDdPanel, recordDdWrap, btnRecordMenu);
                        });
                    });
                }
            }

            function setRecordButtonState(recording) {
                if (recordIdleWrap) recordIdleWrap.classList.toggle('hidden', !!recording);
                if (btnRecordStop) btnRecordStop.classList.toggle('hidden', !recording);
                if (btnLectureAddScreen) {
                    btnLectureAddScreen.classList.toggle('hidden', !recording || recordingKind !== 'lecture');
                }
                if (recording) {
                    if (recordIconActive) recordIconActive.className = 'fas fa-stop';
                    if (recordLabelActive) {
                        recordLabelActive.textContent = recordingKind === 'report' ? 'إيقاف — تقرير صوتي' : 'إيقاف — تسجيل المحاضرة';
                    }
                } else {
                    if (recordIconIdle) recordIconIdle.className = 'fas fa-circle-dot text-rose-400';
                    if (recordLabelIdle) recordLabelIdle.textContent = 'تسجيل أو تقرير';
                }
            }

            function setRecordButtonBusy(isBusy) {
                if (btnRecordMenu) {
                    btnRecordMenu.disabled = isBusy;
                    btnRecordMenu.classList.toggle('opacity-70', isBusy);
                    btnRecordMenu.classList.toggle('cursor-not-allowed', isBusy);
                }
                if (btnRecordStop) {
                    btnRecordStop.disabled = isBusy;
                    btnRecordStop.classList.toggle('opacity-70', isBusy);
                    btnRecordStop.classList.toggle('cursor-not-allowed', isBusy);
                }
                if (btnLectureAddScreen) {
                    btnLectureAddScreen.disabled = isBusy;
                    btnLectureAddScreen.classList.toggle('opacity-70', isBusy);
                    btnLectureAddScreen.classList.toggle('cursor-not-allowed', isBusy);
                }
            }

            function setRecordStatus(message, isError) {
                if (!recordStatusChip) return;
                if (!message) {
                    recordStatusChip.classList.add('hidden');
                    recordStatusChip.textContent = '';
                    return;
                }
                recordStatusChip.classList.remove('hidden');
                recordStatusChip.textContent = message;
                recordStatusChip.classList.remove('bg-sky-500/20', 'border-sky-500/30', 'text-sky-200', 'bg-rose-600/20', 'border-rose-500/30', 'text-rose-200');
                if (isError) {
                    recordStatusChip.classList.add('bg-rose-600/20', 'border-rose-500/30', 'text-rose-200');
                } else {
                    recordStatusChip.classList.add('bg-sky-500/20', 'border-sky-500/30', 'text-sky-200');
                }
            }

            function stopCaptureTracks(stream) {
                if (!stream) return;
                try {
                    stream.getTracks().forEach(function(track) { track.stop(); });
                } catch (err) {
                    console.warn('Track stop warning:', err);
                }
            }

            function pickMediaRecorderOptions() {
                var candidates = [
                    'video/webm;codecs=vp9,opus',
                    'video/webm;codecs=vp8,opus',
                    'video/webm'
                ];
                var mimeType = '';
                for (var i = 0; i < candidates.length; i++) {
                    if (MediaRecorder.isTypeSupported(candidates[i])) {
                        mimeType = candidates[i];
                        break;
                    }
                }
                var opts = { videoBitsPerSecond: 1200000, audioBitsPerSecond: 96000 };
                if (mimeType) {
                    opts.mimeType = mimeType;
                }
                return opts;
            }

            function pickAudioRecorderOptions() {
                var candidates = [
                    'audio/webm;codecs=opus',
                    'audio/webm',
                    'audio/ogg;codecs=opus',
                    'audio/ogg',
                    'audio/mp4'
                ];
                for (var i = 0; i < candidates.length; i++) {
                    if (MediaRecorder.isTypeSupported(candidates[i])) {
                        return { mimeType: candidates[i], audioBitsPerSecond: 96000 };
                    }
                }
                return { audioBitsPerSecond: 96000 };
            }

            function normalizeAudioMimeType(mime) {
                var raw = String(mime || '').toLowerCase();
                if (!raw) return 'audio/webm';
                if (raw.indexOf('audio/') === 0) return raw;
                if (raw.indexOf('video/webm') === 0) return 'audio/webm';
                if (raw.indexOf('video/ogg') === 0) return 'audio/ogg';
                if (raw.indexOf('video/mp4') === 0) return 'audio/mp4';
                return 'audio/webm';
            }

            function audioFileNameByMime(mime) {
                var m = normalizeAudioMimeType(mime);
                if (m.indexOf('audio/mpeg') === 0) return 'meeting-audio.mp3';
                if (m.indexOf('audio/mp4') === 0) return 'meeting-audio.m4a';
                if (m.indexOf('audio/ogg') === 0) return 'meeting-audio.ogg';
                return 'meeting-audio.webm';
            }

            function formatBytes(n) {
                var x = Number(n) || 0;
                if (x < 1024) {
                    return x + ' B';
                }
                if (x < 1048576) {
                    return (x / 1024).toFixed(1) + ' KB';
                }
                if (x < 1073741824) {
                    return (x / 1048576).toFixed(1) + ' MB';
                }
                return (x / 1073741824).toFixed(2) + ' GB';
            }

            var mxUploadDbPromise = null;

            function mxOpenUploadDb() {
                if (mxUploadDbPromise) return mxUploadDbPromise;
                mxUploadDbPromise = new Promise(function(resolve, reject) {
                    var req = indexedDB.open('mxClassroomRecordings', 1);
                    req.onerror = function() { reject(req.error); };
                    req.onsuccess = function() { resolve(req.result); };
                    req.onupgradeneeded = function(e) {
                        var db = e.target.result;
                        if (!db.objectStoreNames.contains('pendingUploads')) {
                            db.createObjectStore('pendingUploads', { keyPath: 'id' });
                        }
                    };
                });
                return mxUploadDbPromise;
            }

            function mxIdbPutJob(job) {
                return mxOpenUploadDb().then(function(db) {
                    return new Promise(function(resolve, reject) {
                        var tx = db.transaction('pendingUploads', 'readwrite');
                        tx.oncomplete = function() { resolve(); };
                        tx.onerror = function() { reject(tx.error); };
                        tx.objectStore('pendingUploads').put(job);
                    });
                });
            }

            function mxIdbDeleteJob(id) {
                return mxOpenUploadDb().then(function(db) {
                    return new Promise(function(resolve, reject) {
                        var tx = db.transaction('pendingUploads', 'readwrite');
                        tx.oncomplete = function() { resolve(); };
                        tx.onerror = function() { reject(tx.error); };
                        tx.objectStore('pendingUploads').delete(id);
                    });
                });
            }

            function mxOpenRecordingUploadTab(jobId) {
                if (!jobId || !recordingUploadTabBaseUrl) return null;
                var sep = recordingUploadTabBaseUrl.indexOf('?') >= 0 ? '&' : '?';
                var url = recordingUploadTabBaseUrl + sep + 'job=' + encodeURIComponent(jobId);
                try {
                    return window.open(url, '_blank', 'noopener,noreferrer');
                } catch (e) {
                    return null;
                }
            }

            function mxIdbListMeetingJobs() {
                return mxOpenUploadDb().then(function(db) {
                    return new Promise(function(resolve, reject) {
                        var out = [];
                        var tx = db.transaction('pendingUploads', 'readonly');
                        var rq = tx.objectStore('pendingUploads').openCursor();
                        rq.onerror = function() { reject(rq.error); };
                        rq.onsuccess = function(e) {
                            var c = e.target.result;
                            if (!c) {
                                resolve(out);
                                return;
                            }
                            var v = c.value;
                            if (v && Number(v.meetingId) === Number(mxMeetingId)) {
                                out.push(v);
                            }
                            c.continue();
                        };
                    });
                });
            }

            function mxSetUploadBar(percent) {
                var p = percent == null ? 0 : Math.max(0, Math.min(100, Number(percent)));
                if (mxUploadModalBar) mxUploadModalBar.style.width = p + '%';
            }

            function mxRefreshUploadChipText(line, percent) {
                if (!mxUploadChipText) return;
                if (percent != null && !isNaN(percent)) {
                    mxUploadChipText.textContent = line + ' — ' + Math.round(percent) + '%';
                } else {
                    mxUploadChipText.textContent = line;
                }
            }

            function mxShowUploadModal(full) {
                mxUploadModalMinimized = !full;
                if (mxUploadModal) {
                    mxUploadModal.classList.remove('hidden');
                    mxUploadModal.setAttribute('aria-hidden', 'false');
                }
                if (full) {
                    if (mxUploadChip) mxUploadChip.classList.add('hidden');
                } else {
                    if (mxUploadModal) mxUploadModal.classList.add('hidden');
                    if (mxUploadChip) mxUploadChip.classList.remove('hidden');
                }
            }

            function mxHideUploadUi() {
                if (mxUploadModal) {
                    mxUploadModal.classList.add('hidden');
                    mxUploadModal.setAttribute('aria-hidden', 'true');
                }
                if (mxUploadChip) mxUploadChip.classList.add('hidden');
                mxUploadModalMinimized = false;
                mxCurrentUploadJob = null;
                if (mxUploadModalRetry) mxUploadModalRetry.classList.add('hidden');
            }

            function putBlobToPresignedUrl(url, blob, contentType, extraHeaders, onPercent) {
                return new Promise(function(resolve, reject) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('PUT', url, true);
                    xhr.timeout = 0;
                    if (contentType) {
                        xhr.setRequestHeader('Content-Type', contentType);
                    }
                    if (extraHeaders && typeof extraHeaders === 'object') {
                        Object.keys(extraHeaders).forEach(function(k) {
                            try {
                                xhr.setRequestHeader(k, extraHeaders[k]);
                            } catch (hErr) {}
                        });
                    }
                    xhr.upload.onprogress = function(e) {
                        if (typeof onPercent !== 'function') return;
                        if (e.lengthComputable && e.total > 0) {
                            onPercent(Math.min(99, Math.round((e.loaded / e.total) * 100)));
                        }
                    };
                    xhr.onload = function() {
                        if (xhr.status >= 200 && xhr.status < 300) {
                            resolve();
                            return;
                        }
                        reject(new Error('فشل رفع التسجيل (HTTP ' + xhr.status + '). إن تكرر ذلك، تحقق من إعدادات الموقع أو تواصل مع الدعم.'));
                    };
                    xhr.onerror = function() {
                        reject(new Error('انقطع الاتصال أثناء رفع التسجيل.'));
                    };
                    xhr.ontimeout = function() {
                        reject(new Error('انتهت مهلة رفع التسجيل.'));
                    };
                    xhr.send(blob);
                });
            }

            function mxReportUploadProgress(opts) {
                opts = opts || {};
                var t = opts.text || '';
                var pct = opts.percent;
                if (mxUploadModalStatus && (!mxUploadModalMinimized || opts.forceChip)) {
                    mxUploadModalStatus.textContent = t;
                }
                if (pct != null && !isNaN(pct)) {
                    mxSetUploadBar(pct);
                }
                if (mxUploadModalMinimized || opts.toChip) {
                    mxRefreshUploadChipText(t.replace(/\s+/g, ' ').slice(0, 80), pct);
                }
                if (opts.shortStatus) {
                    setRecordStatus(opts.shortStatus, !!opts.isError);
                }
            }

            function uploadRecordedBlobViaFormData(blob, durationSeconds, onProgress) {
                return new Promise(function(resolve, reject) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', uploadRecordingUrl, true);
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.timeout = 0;

                    xhr.upload.onprogress = function(e) {
                        if (typeof onProgress === 'function') {
                            if (e.lengthComputable && e.total > 0) {
                                var p = Math.min(100, Math.round((e.loaded / e.total) * 100));
                                onProgress({ text: 'جاري الرفع عبر الخادم ' + p + '%...', percent: p, toChip: true });
                            } else if (e.loaded) {
                                onProgress({ text: 'جاري الرفع عبر الخادم... ' + formatBytes(e.loaded), percent: null, toChip: true });
                            }
                        }
                        if (e.lengthComputable && e.total > 0) {
                            var p2 = Math.min(100, Math.round((e.loaded / e.total) * 100));
                            setRecordStatus('جاري الرفع عبر الخادم ' + p2 + '%.', false);
                        }
                    };

                    xhr.onerror = function() {
                        reject(new Error('فشل الاتصال أثناء الرفع. تحقق من الإنترنت وحاول مرة أخرى.'));
                    };
                    xhr.ontimeout = function() {
                        reject(new Error('انتهت مهلة الرفع. جرّب شبكة أسرع أو قسّم المحاضرة إلى جزئين.'));
                    };

                    xhr.onload = function() {
                        var raw = xhr.responseText || '';
                        var data = {};
                        try {
                            data = raw ? JSON.parse(raw) : {};
                        } catch (parseErr) {
                            if (xhr.status === 413) {
                                reject(new Error('حجم الملف يتجاوز حد السيرفر الحالي. جرّب رفع التسجيل عبر اتصال مباشر، أو راجع إعدادات حجم الرفع في الاستضافة.'));
                                return;
                            }
                            reject(new Error('استجابة غير متوقعة من الخادم (رمز ' + xhr.status + ').'));
                            return;
                        }

                        if (xhr.status >= 200 && xhr.status < 300) {
                            resolve({ ok: true, data: data });
                            return;
                        }

                        var msg = (data && data.message) ? data.message : 'فشل رفع التسجيل.';
                        if (data && data.errors) {
                            var firstKey = Object.keys(data.errors)[0];
                            if (firstKey && data.errors[firstKey] && data.errors[firstKey][0]) {
                                msg = data.errors[firstKey][0];
                            }
                        }
                        if (xhr.status === 413) {
                            msg = 'حجم الملف كبير جداً لإعدادات السيرفر الحالية.';
                        }
                        reject(new Error(msg));
                    };

                    var formData = new FormData();
                    formData.append('recording', blob, 'meeting-recording.webm');
                    formData.append('duration_seconds', String(durationSeconds || 0));
                    xhr.send(formData);
                });
            }

            async function uploadRecordedBlob(blob, durationSeconds, onProgress) {
                var putSucceeded = false;
                var ct = blob.type || 'audio/webm';
                try {
                    if (typeof onProgress === 'function') {
                        onProgress({ text: 'جاري تجهيز رابط الرفع...', percent: 2, toChip: true });
                    }
                    var presignRes = await fetch(presignRecordingUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            content_type: ct,
                        }),
                    });
                    var presignData = {};
                    try {
                        presignData = await presignRes.json();
                    } catch (je) {
                        presignData = {};
                    }

                    if (presignRes.ok && presignData.direct_upload === false) {
                        return uploadRecordedBlobViaFormData(blob, durationSeconds, onProgress);
                    }

                    if (presignRes.ok && presignData.upload_url && presignData.upload_token && presignData.content_type) {
                        if (typeof onProgress === 'function') {
                            onProgress({ text: 'جاري رفع التسجيل (' + formatBytes(blob.size) + ')...', percent: 5, toChip: true });
                        }
                        await putBlobToPresignedUrl(
                            presignData.upload_url,
                            blob,
                            presignData.content_type,
                            presignData.headers || {},
                            function(p) {
                                if (typeof onProgress === 'function') {
                                    var scaled = 5 + Math.round((p / 100) * 80);
                                    onProgress({ text: 'جاري رفع التسجيل...', percent: scaled, toChip: true });
                                }
                            }
                        );
                        putSucceeded = true;
                        if (typeof onProgress === 'function') {
                            onProgress({ text: 'جاري تأكيد الملف على الخادم...', percent: 90, toChip: true });
                        }

                        var completeRes = await fetch(completeRecordingUrl, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                upload_token: presignData.upload_token,
                                duration_seconds: durationSeconds || 0,
                            }),
                        });
                        var completeData = {};
                        try {
                            completeData = await completeRes.json();
                        } catch (je2) {
                            completeData = {};
                        }
                        if (!completeRes.ok) {
                            var cmsg = (completeData && completeData.message) ? completeData.message : 'فشل ربط الملف بالاجتماع بعد الرفع.';
                            throw new Error(cmsg);
                        }
                        if (typeof onProgress === 'function') {
                            onProgress({ text: 'تم الرفع بنجاح.', percent: 100, toChip: true });
                        }
                        return { ok: true, data: completeData };
                    }
                } catch (err) {
                    if (putSucceeded) {
                        throw err;
                    }
                    console.warn('Direct upload path skipped or failed, using server upload:', err);
                }
                return uploadRecordedBlobViaFormData(blob, durationSeconds, onProgress);
            }

            async function uploadAudioBlob(blob, durationSeconds, onProgress) {
                var effectiveAudioMime = normalizeAudioMimeType(blob && blob.type ? blob.type : 'audio/webm');
                function uploadAudioBlobViaFormData() {
                    return new Promise(function(resolve, reject) {
                        var formData = new FormData();
                        formData.append('recording_audio', blob, audioFileNameByMime(effectiveAudioMime));
                        formData.append('duration_seconds', String(durationSeconds || 0));

                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', uploadAudioUrl, true);
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                        xhr.upload.onprogress = function(e) {
                            if (typeof onProgress === 'function' && e.lengthComputable && e.total > 0) {
                                var p = Math.min(100, Math.round((e.loaded / e.total) * 100));
                                onProgress({ text: 'جاري رفع التقرير الصوتي عبر الخادم ' + p + '%...', percent: p, toChip: true });
                            }
                        };
                        xhr.onload = function() {
                            var data = {};
                            try { data = xhr.responseText ? JSON.parse(xhr.responseText) : {}; } catch (e) {}
                            if (xhr.status >= 200 && xhr.status < 300) {
                                resolve({ ok: true, data: data });
                                return;
                            }
                            reject(new Error((data && data.message) ? data.message : 'فشل رفع ملف الصوت عبر السيرفر.'));
                        };
                        xhr.onerror = function() {
                            reject(new Error('فشل الاتصال أثناء رفع ملف الصوت.'));
                        };
                        xhr.send(formData);
                    });
                }

                if (typeof onProgress === 'function') {
                    onProgress({ text: 'جاري تجهيز رابط رفع التقرير...', percent: 2, toChip: true });
                }

                var presignRes = await fetch(presignAudioUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        content_type: effectiveAudioMime,
                    }),
                });
                var presignData = {};
                try {
                    presignData = await presignRes.json();
                } catch (je) {
                    presignData = {};
                }

                if (presignRes.ok && presignData.direct_upload === false) {
                    return uploadAudioBlobViaFormData();
                }

                if (!presignRes.ok || !presignData.upload_url || !presignData.upload_token || !presignData.content_type) {
                    return uploadAudioBlobViaFormData();
                }

                if (typeof onProgress === 'function') {
                    onProgress({ text: 'جاري رفع التقرير الصوتي...', percent: 5, toChip: true });
                }
                await putBlobToPresignedUrl(
                    presignData.upload_url,
                    blob,
                    presignData.content_type,
                    presignData.headers || {},
                    function(p) {
                        if (typeof onProgress === 'function') {
                            var scaled = 5 + Math.round((p / 100) * 80);
                            onProgress({ text: 'جاري رفع التسجيل...', percent: scaled, toChip: true });
                        }
                    }
                );

                if (typeof onProgress === 'function') {
                    onProgress({ text: 'جاري تأكيد ملف التقرير...', percent: 90, toChip: true });
                }

                var completeRes = await fetch(completeAudioUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        upload_token: presignData.upload_token,
                        duration_seconds: durationSeconds || 0,
                    }),
                });
                var completeData = {};
                try {
                    completeData = await completeRes.json();
                } catch (je2) {
                    completeData = {};
                }
                if (!completeRes.ok) {
                    throw new Error((completeData && completeData.message) ? completeData.message : 'فشل حفظ ملف الصوت.');
                }
                if (typeof onProgress === 'function') {
                    onProgress({ text: 'تم رفع التقرير الصوتي.', percent: 100, toChip: true });
                }
                return { ok: true, data: completeData };
            }

            function mxMakeUploadJobId() {
                return 'mx-' + mxMeetingId + '-' + Date.now() + '-' + Math.random().toString(36).slice(2, 10);
            }

            async function mxRunUploadJob(job) {
                mxCurrentUploadJob = job;
                mxLastFailedJob = null;
                if (mxUploadModalRetry) mxUploadModalRetry.classList.add('hidden');
                mxShowUploadModal(true);
                if (mxUploadModalTitle) {
                    mxUploadModalTitle.textContent = job.kind === 'report' ? 'جاري رفع التقرير الصوتي' : 'جاري رفع تسجيل المحاضرة';
                }
                mxSetUploadBar(0);
                mxReportUploadProgress({
                    text: 'جاري حفظ نسخة محلية ثم رفع التسجيل...',
                    percent: 1,
                    toChip: true,
                });

                var persisted = Object.assign({}, job, { status: 'uploading', updatedAt: Date.now() });
                try {
                    await mxIdbPutJob(persisted);
                } catch (idbErr) {
                    console.warn('IndexedDB persist failed:', idbErr);
                }

                var onProg = function(o) {
                    mxReportUploadProgress(o);
                };

                try {
                    if (job.kind === 'report') {
                        await uploadAudioBlob(job.blob, job.durationSeconds, onProg);
                    } else {
                        await uploadRecordedBlob(job.blob, job.durationSeconds, onProg);
                        if (job.secondaryBlob && job.secondaryBlob.size > 0) {
                            if (typeof onProg === 'function') {
                                onProg({ text: 'جاري رفع ملف الصوت المصاحب للفيديو...', percent: 92, toChip: true });
                            }
                            await uploadAudioBlob(job.secondaryBlob, job.durationSeconds, onProg);
                        }
                    }
                    await mxIdbDeleteJob(job.id);
                    mxReportUploadProgress({ text: 'تم رفع وحفظ التسجيل بنجاح.', percent: 100, toChip: true });
                    setRecordStatus(job.kind === 'report' ? 'تم رفع التقرير الصوتي.' : 'تم رفع تسجيل المحاضرة.', false);
                    setTimeout(function() {
                        mxHideUploadUi();
                    }, 2200);
                } catch (err) {
                    console.error('mxRunUploadJob:', err);
                    var msg = (err && err.message) ? err.message : 'فشل الرفع.';
                    persisted.status = 'failed';
                    persisted.lastError = msg;
                    persisted.updatedAt = Date.now();
                    try {
                        await mxIdbPutJob(persisted);
                    } catch (idbErr2) {}
                    mxLastFailedJob = persisted;
                    mxReportUploadProgress({
                        text: msg + '\n\nيمكنك الضغط على «إعادة المحاولة» أو انتظار عودة الإنترنت لإعادة المحاولة تلقائياً.',
                        percent: null,
                        isError: true,
                        shortStatus: 'فشل الرفع — يمكن إعادة المحاولة من النافذة أو الشريط.',
                        toChip: true,
                    });
                    if (mxUploadModalRetry) mxUploadModalRetry.classList.remove('hidden');
                    setRecordStatus('فشل الرفع — أعد المحاولة أو انتظر الاتصال.', true);
                    throw err;
                }
            }

            function mxQueueBlobUpload(blob, durationSeconds, kind, secondaryBlob) {
                var job = {
                    id: mxMakeUploadJobId(),
                    meetingId: mxMeetingId,
                    kind: kind,
                    blob: blob,
                    secondaryBlob: (secondaryBlob && secondaryBlob.size > 0) ? secondaryBlob : null,
                    durationSeconds: durationSeconds || 0,
                    status: 'pending',
                    createdAt: Date.now(),
                };
                mxIdbPutJob(job).then(function() {
                    var w = mxOpenRecordingUploadTab(job.id);
                    if (!w) {
                        setRecordStatus('المتصفح منع التاب الجديد — سيتم الرفع من هذه الصفحة.', true);
                        mxRunUploadJob(Object.assign({}, job, { status: 'pending' })).catch(function() {});
                        return;
                    }
                    setRecordStatus('تم فتح تاب الرفع في نافذة جديدة — أكمل الرفع هناك وتابع الاجتماع في هذا التاب.', false);
                }).catch(function(idbErr) {
                    console.warn('IndexedDB before upload tab:', idbErr);
                    mxRunUploadJob(Object.assign({}, job, { status: 'pending' })).catch(function() {});
                });
            }

            function cleanupLectureRecordingVisuals() {
                if (lectureRafId != null) {
                    cancelAnimationFrame(lectureRafId);
                    lectureRafId = null;
                }
                stopCaptureTracks(lectureDisplayStream);
                lectureDisplayStream = null;
                if (lectureDisplayVideo) {
                    try {
                        lectureDisplayVideo.pause();
                        lectureDisplayVideo.srcObject = null;
                    } catch (e) {}
                }
                lectureCanvasStream = null;
                lectureCtx = null;
                lectureCanvas = null;
            }

            function lectureCompositeTick() {
                if (!lectureCtx || !lectureCanvas) return;
                var w = lectureCanvas.width;
                var h = lectureCanvas.height;
                var v = lectureDisplayVideo;
                if (v && v.srcObject && v.readyState >= 2 && v.videoWidth > 0) {
                    var vw = v.videoWidth;
                    var vh = v.videoHeight;
                    var scale = Math.min(w / vw, h / vh);
                    var dw = Math.floor(vw * scale);
                    var dh = Math.floor(vh * scale);
                    var ox = Math.floor((w - dw) / 2);
                    var oy = Math.floor((h - dh) / 2);
                    lectureCtx.fillStyle = '#0f172a';
                    lectureCtx.fillRect(0, 0, w, h);
                    try {
                        lectureCtx.drawImage(v, ox, oy, dw, dh);
                    } catch (drawErr) {
                        lectureCtx.fillStyle = '#0f172a';
                        lectureCtx.fillRect(0, 0, w, h);
                    }
                } else {
                    lectureCtx.fillStyle = '#0f172a';
                    lectureCtx.fillRect(0, 0, w, h);
                    lectureCtx.fillStyle = 'rgba(148,163,184,0.4)';
                    lectureCtx.font = '600 20px sans-serif';
                    lectureCtx.textAlign = 'center';
                    lectureCtx.textBaseline = 'middle';
                    lectureCtx.fillText('التسجيل صوتي — اضغط «إضافة شاشة» لإظهار التبويب في الفيديو', w / 2, h / 2);
                }
                lectureRafId = requestAnimationFrame(lectureCompositeTick);
            }

            async function attachLectureDisplayStream() {
                if (!navigator.mediaDevices || typeof navigator.mediaDevices.getDisplayMedia !== 'function') {
                    alert('هذا المتصفح لا يدعم مشاركة الشاشة. جرّب Chrome أو Edge.');
                    return;
                }
                if (lectureDisplayStream) {
                    setRecordStatus('مشاركة الشاشة مفعّلة بالفعل.', false);
                    return;
                }
                var stream = await navigator.mediaDevices.getDisplayMedia({ video: true, audio: false });
                lectureDisplayStream = stream;
                if (!lectureDisplayVideo) {
                    lectureDisplayVideo = document.createElement('video');
                    lectureDisplayVideo.setAttribute('playsinline', '');
                    lectureDisplayVideo.setAttribute('muted', '');
                    lectureDisplayVideo.muted = true;
                    lectureDisplayVideo.playsInline = true;
                }
                lectureDisplayVideo.srcObject = stream;
                try {
                    await lectureDisplayVideo.play();
                } catch (playErr) {
                    console.warn('Display video play:', playErr);
                }
                stream.getVideoTracks().forEach(function(track) {
                    track.addEventListener('ended', function() {
                        if (recordingKind !== 'lecture') return;
                        stopCaptureTracks(lectureDisplayStream);
                        lectureDisplayStream = null;
                        if (lectureDisplayVideo) {
                            try {
                                lectureDisplayVideo.pause();
                                lectureDisplayVideo.srcObject = null;
                            } catch (e) {}
                        }
                        setRecordStatus('انتهت مشاركة الشاشة — يستمر التسجيل صوتيًا.', false);
                    });
                });
                setRecordStatus('تم ربط الشاشة بالفيديو المسجّل.', false);
            }

            async function startLectureRecording() {
                if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
                    alert('المتصفح لا يدعم تسجيل الصوت من الميكروفون.');
                    return;
                }
                if (!hasJoinedConference) {
                    alert('ادخل الغرفة أولاً ثم أعد محاولة التسجيل.');
                    return;
                }

                setRecordDdOpen(false);
                setRecordButtonBusy(true);
                recordingKind = 'lecture';
                audioRecorder = null;
                recordedAudioChunks = [];
                stopCaptureTracks(audioOnlyStream);
                audioOnlyStream = null;

                cleanupLectureRecordingVisuals();

                try {
                    micStream = await navigator.mediaDevices.getUserMedia({ audio: true, video: false });
                } catch (err) {
                    setRecordButtonBusy(false);
                    recordingKind = null;
                    micStream = null;
                    alert('لم يُسمح بالميكروفون أو تعذر تشغيله. تحقق من أذونات المتصفح.');
                    return;
                }

                lectureCanvas = document.createElement('canvas');
                lectureCanvas.width = 1280;
                lectureCanvas.height = 720;
                lectureCtx = lectureCanvas.getContext('2d', { alpha: false });
                lectureCtx.fillStyle = '#0f172a';
                lectureCtx.fillRect(0, 0, lectureCanvas.width, lectureCanvas.height);

                try {
                    lectureCanvasStream = lectureCanvas.captureStream(15);
                } catch (capErr) {
                    stopCaptureTracks(micStream);
                    micStream = null;
                    cleanupLectureRecordingVisuals();
                    setRecordButtonBusy(false);
                    recordingKind = null;
                    alert('تعذر تهيئة مسار الفيديو. جرّب Chrome أو Edge بإصدار حديث.');
                    return;
                }

                var vidTracks = lectureCanvasStream.getVideoTracks();
                var micTracks = micStream.getAudioTracks();
                if (!vidTracks.length) {
                    stopCaptureTracks(micStream);
                    micStream = null;
                    cleanupLectureRecordingVisuals();
                    setRecordButtonBusy(false);
                    recordingKind = null;
                    alert('تعذر إنشاء مسار الفيديو للتسجيل.');
                    return;
                }
                if (!micTracks.length) {
                    stopCaptureTracks(micStream);
                    micStream = null;
                    cleanupLectureRecordingVisuals();
                    setRecordButtonBusy(false);
                    recordingKind = null;
                    alert('لم يُسمح بمسار الصوت للتسجيل.');
                    return;
                }

                activeRecordingStream = new MediaStream([vidTracks[0], micTracks[0]]);
                lectureCompositeTick();

                var recorderOpts = pickMediaRecorderOptions();
                try {
                    mediaRecorder = new MediaRecorder(activeRecordingStream, recorderOpts);
                } catch (err) {
                    try {
                        var fallbackLec = recorderOpts.mimeType ? { mimeType: recorderOpts.mimeType } : {};
                        mediaRecorder = new MediaRecorder(activeRecordingStream, fallbackLec);
                    } catch (err2) {
                        stopCaptureTracks(activeRecordingStream);
                        activeRecordingStream = null;
                        stopCaptureTracks(micStream);
                        micStream = null;
                        cleanupLectureRecordingVisuals();
                        setRecordButtonBusy(false);
                        recordingKind = null;
                        alert('تعذر بدء تسجيل الفيديو. جرّب Chrome أو Edge بإصدار حديث.');
                        return;
                    }
                }

                recordedChunks = [];
                recordingStartedAt = Date.now();

                mediaRecorder.addEventListener('dataavailable', function(event) {
                    if (event.data && event.data.size > 0) {
                        recordedChunks.push(event.data);
                    }
                });

                mediaRecorder.addEventListener('stop', async function onLectureRecorderStopped() {
                    isRecording = false;
                    setRecordButtonState(false);
                    recordingKind = null;

                    stopCaptureTracks(activeRecordingStream);
                    activeRecordingStream = null;
                    stopCaptureTracks(micStream);
                    micStream = null;
                    cleanupLectureRecordingVisuals();

                    var durationSeconds = recordingStartedAt ? Math.max(1, Math.round((Date.now() - recordingStartedAt) / 1000)) : 0;
                    var outType = (mediaRecorder && mediaRecorder.mimeType) ? mediaRecorder.mimeType : 'video/webm';
                    var blob = new Blob(recordedChunks, { type: outType });

                    if (!blob.size) {
                        setRecordButtonBusy(false);
                        setRecordStatus('لا يوجد محتوى في تسجيل المحاضرة.', true);
                        alert('لا يوجد محتوى في التسجيل. تأكد من عمل الميكروفون ثم أعد المحاولة.');
                        recordedChunks = [];
                        return;
                    }

                    setRecordButtonBusy(false);
                    setRecordStatus('تم إيقاف تسجيل المحاضرة. جاري فتح تاب الرفع...', false);
                    mxQueueBlobUpload(blob, durationSeconds, 'lecture', null);
                    recordedChunks = [];
                    if (pendingEndMeetingSubmit && endMeetingForm) {
                        pendingEndMeetingSubmit = false;
                        setTimeout(function() {
                            endMeetingForm.submit();
                        }, 400);
                    }
                });

                mediaRecorder.start(3000);
                isRecording = true;
                setRecordButtonState(true);
                setRecordStatus('جاري تسجيل المحاضرة (صوت منذ البداية). اضغط «إضافة شاشة» لإظهار التبويب في الفيديو.', false);
                setRecordButtonBusy(false);
            }

            async function startMicRecording() {
                if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
                    alert('المتصفح لا يدعم تسجيل الصوت من الميكروفون.');
                    return;
                }
                if (!hasJoinedConference) {
                    alert('ادخل الغرفة أولاً ثم أعد محاولة التسجيل.');
                    return;
                }

                setRecordDdOpen(false);
                setRecordButtonBusy(true);
                recordingKind = 'report';

                try {
                    activeRecordingStream = await navigator.mediaDevices.getUserMedia({ audio: true, video: false });
                } catch (err) {
                    setRecordButtonBusy(false);
                    recordingKind = null;
                    alert('لم يُسمح بالميكروفون أو تعذر تشغيله. تحقق من أذونات المتصفح.');
                    return;
                }

                var recorderOpts = pickAudioRecorderOptions();
                try {
                    mediaRecorder = new MediaRecorder(activeRecordingStream, recorderOpts);
                } catch (err) {
                    try {
                        var fallback2 = recorderOpts.mimeType ? { mimeType: recorderOpts.mimeType, audioBitsPerSecond: 96000 } : { audioBitsPerSecond: 96000 };
                        mediaRecorder = new MediaRecorder(activeRecordingStream, fallback2);
                    } catch (err2) {
                        stopCaptureTracks(activeRecordingStream);
                        activeRecordingStream = null;
                        setRecordButtonBusy(false);
                        recordingKind = null;
                        alert('تعذر بدء التسجيل الصوتي. جرّب Chrome أو Edge بإصدار حديث.');
                        return;
                    }
                }

                recordedChunks = [];
                recordingStartedAt = Date.now();

                mediaRecorder.addEventListener('dataavailable', function(event) {
                    if (event.data && event.data.size > 0) {
                        recordedChunks.push(event.data);
                    }
                });

                mediaRecorder.addEventListener('stop', async function onReportRecorderStopped() {
                    isRecording = false;
                    setRecordButtonState(false);
                    recordingKind = null;

                    stopCaptureTracks(activeRecordingStream);
                    activeRecordingStream = null;

                    var durationSeconds = recordingStartedAt ? Math.max(1, Math.round((Date.now() - recordingStartedAt) / 1000)) : 0;
                    var outType = normalizeAudioMimeType((mediaRecorder && mediaRecorder.mimeType) ? mediaRecorder.mimeType : 'audio/webm');
                    var blob = new Blob(recordedChunks, { type: outType });

                    if (!blob.size) {
                        setRecordButtonBusy(false);
                        setRecordStatus('لا يوجد محتوى في التسجيل.', true);
                        alert('لا يوجد محتوى في التسجيل الصوتي.');
                        recordedChunks = [];
                        return;
                    }

                    setRecordButtonBusy(false);
                    setRecordStatus('تم إيقاف تسجيل التقرير. جاري فتح تاب الرفع...', false);
                    mxQueueBlobUpload(blob, durationSeconds, 'report', null);
                    recordedChunks = [];
                    if (pendingEndMeetingSubmit && endMeetingForm) {
                        pendingEndMeetingSubmit = false;
                        setTimeout(function() {
                            endMeetingForm.submit();
                        }, 400);
                    }
                });

                mediaRecorder.start(4000);
                isRecording = true;
                setRecordButtonState(true);
                setRecordStatus('تسجيل تقرير صوتي (يمكنك متابعة الاجتماع)...', false);
                setRecordButtonBusy(false);
            }

            function stopBrowserRecording() {
                if (!mediaRecorder || mediaRecorder.state !== 'recording') {
                    if (pendingEndMeetingSubmit && endMeetingForm) {
                        pendingEndMeetingSubmit = false;
                        endMeetingForm.submit();
                    }
                    return;
                }
                setRecordButtonBusy(true);
                setRecordStatus(recordingKind === 'lecture' ? 'جاري إنهاء تسجيل المحاضرة...' : 'جاري إنهاء التسجيل ودمج المقاطع...', false);
                try {
                    if (typeof mediaRecorder.requestData === 'function') {
                        mediaRecorder.requestData();
                    }
                    if (audioRecorder && audioRecorder.state === 'recording' && typeof audioRecorder.requestData === 'function') {
                        audioRecorder.requestData();
                    }
                } catch (reqErr) {
                    console.warn('requestData:', reqErr);
                }
                if (audioRecorder && audioRecorder.state === 'recording') {
                    audioRecorder.stop();
                }
                mediaRecorder.stop();
            }

            if (btnRecordMenu && recordDdPanel && recordDdWrap) {
                btnRecordMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (isRecording) return;
                    setRecordDdOpen(recordDdPanel.classList.contains('hidden'));
                });
                recordDdPanel.querySelectorAll('[data-mx-rec-mode]').forEach(function(el) {
                    el.addEventListener('click', function() {
                        var mode = el.getAttribute('data-mx-rec-mode');
                        setRecordDdOpen(false);
                        if (mode === 'lecture') {
                            startLectureRecording();
                        } else if (mode === 'report') {
                            startMicRecording();
                        }
                    });
                });
                document.addEventListener('mousedown', function (e) {
                    if (!recordDdWrap.contains(e.target)) setRecordDdOpen(false);
                }, true);
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') setRecordDdOpen(false);
                });
                var recResizeT = null;
                window.addEventListener('resize', function () {
                    if (!mxRecordDdOpen || !recordDdPanel || typeof window.mxPositionClassroomDropdown !== 'function') return;
                    if (recResizeT) clearTimeout(recResizeT);
                    recResizeT = setTimeout(function () {
                        recResizeT = null;
                        window.mxPositionClassroomDropdown(recordDdPanel, recordDdWrap, btnRecordMenu);
                    }, 80);
                });
            }

            if (btnRecordStop) {
                btnRecordStop.addEventListener('click', function() {
                    stopBrowserRecording();
                });
            }

            if (endMeetingForm && endMeetingBtn) {
                endMeetingForm.addEventListener('submit', function(e) {
                    if (!isRecording) return;
                    e.preventDefault();
                    pendingEndMeetingSubmit = true;
                    setRecordStatus('سيتم إنهاء الاجتماع بعد حفظ التسجيل وبدء الرفع...', false);
                    stopBrowserRecording();
                });
            }

            if (btnLectureAddScreen) {
                btnLectureAddScreen.addEventListener('click', function() {
                    if (!isRecording || recordingKind !== 'lecture') return;
                    setRecordButtonBusy(true);
                    attachLectureDisplayStream().then(function() {
                        setRecordButtonBusy(false);
                    }).catch(function() {
                        setRecordButtonBusy(false);
                        if (lectureDisplayStream) {
                            stopCaptureTracks(lectureDisplayStream);
                            lectureDisplayStream = null;
                        }
                        alert('تم الإلغاء أو لم يُسمح بمشاركة الشاشة.');
                    });
                });
            }

            if (mxUploadModalBg) {
                mxUploadModalBg.addEventListener('click', function() {
                    mxShowUploadModal(false);
                });
            }
            if (mxUploadChip) {
                mxUploadChip.addEventListener('click', function() {
                    if (mxLastFailedJob && mxLastFailedJob.id && (mxLastFailedJob.status === 'failed' || mxLastFailedJob.status === 'uploading')) {
                        if (!mxOpenRecordingUploadTab(mxLastFailedJob.id)) {
                            mxShowUploadModal(true);
                            if (mxUploadModalTitle) mxUploadModalTitle.textContent = 'رفع معلّق';
                            if (mxUploadModalStatus) {
                                mxUploadModalStatus.textContent = 'تعذر فتح تاب جديد. اضغط «إعادة المحاولة» للرفع من هذه الصفحة.';
                            }
                            if (mxUploadModalRetry) mxUploadModalRetry.classList.remove('hidden');
                        } else {
                            setRecordStatus('تم فتح تاب الرفع لاستكمال الرفع.', false);
                        }
                        return;
                    }
                    mxShowUploadModal(true);
                });
            }
            if (mxUploadModalRetry) {
                mxUploadModalRetry.addEventListener('click', function() {
                    if (!mxLastFailedJob || !mxLastFailedJob.blob) return;
                    if (mxLastFailedJob.id && mxOpenRecordingUploadTab(mxLastFailedJob.id)) {
                        mxHideUploadUi();
                        setRecordStatus('تم فتح تاب الرفع لإعادة المحاولة.', false);
                        return;
                    }
                    var retryJob = Object.assign({}, mxLastFailedJob, { status: 'pending' });
                    mxRunUploadJob(retryJob).catch(function() {});
                });
            }

            if (!window.__mxClassroomOnlineHook) {
                window.__mxClassroomOnlineHook = true;
                window.addEventListener('online', function() {
                    if (mxLastFailedJob && mxLastFailedJob.id && (mxLastFailedJob.status === 'failed' || mxLastFailedJob.status === 'uploading')) {
                        if (mxOpenRecordingUploadTab(mxLastFailedJob.id)) {
                            setRecordStatus('عاد الاتصال — تم فتح تاب الرفع لإكمال الرفع.', false);
                            return;
                        }
                        var retryJob = Object.assign({}, mxLastFailedJob, { status: 'pending' });
                        mxShowUploadModal(true);
                        if (mxUploadModalStatus) {
                            mxUploadModalStatus.textContent = 'عاد الاتصال — جاري إعادة المحاولة من هذا التاب...';
                        }
                        mxRunUploadJob(retryJob).catch(function() {});
                    }
                });
            }

            mxIdbListMeetingJobs().then(function(list) {
                if (!list || !list.length) return;
                var failed = list.filter(function(j) { return j.status === 'failed' || j.status === 'uploading'; });
                if (!failed.length) return;
                mxLastFailedJob = failed.sort(function(a, b) { return (b.updatedAt || b.createdAt || 0) - (a.updatedAt || a.createdAt || 0); })[0];
                if (mxUploadChip && mxUploadChipText) {
                    mxUploadChipText.textContent = 'رفع معلّق — اضغط للمتابعة';
                    mxUploadChip.classList.remove('hidden');
                }
                setRecordStatus('يوجد رفع غير مكتمل — اضغط الشريط بالأسفل لفتح تاب الرفع وإكمال الرفع.', true);
            }).catch(function() {});

            function hidePermissionGate() {
                if (!permissionGate) return;
                permissionGate.classList.add('hidden');
            }

            function setPermissionHelp(message, isError) {
                if (!permissionHelp) return;
                permissionHelp.textContent = message || '';
                permissionHelp.className = 'mt-4 text-xs ' + (isError ? 'text-rose-300' : 'text-slate-400');
            }

            function mapMediaErrorToArabic(err) {
                var code = err && err.name ? String(err.name) : '';
                if (code === 'NotAllowedError' || code === 'PermissionDeniedError') {
                    return 'المتصفح رفض الإذن. افتح رمز القفل بجانب الرابط ثم اسمح للكاميرا والميكروفون.';
                }
                if (code === 'NotFoundError' || code === 'DevicesNotFoundError') {
                    return 'لا توجد كاميرا أو ميكروفون متصل بالجهاز.';
                }
                if (code === 'NotReadableError' || code === 'TrackStartError') {
                    return 'تعذر تشغيل الكاميرا/الميكروفون (قد يكون مستخدمًا في تطبيق آخر مثل Zoom/Teams).';
                }
                if (code === 'OverconstrainedError' || code === 'ConstraintNotSatisfiedError') {
                    return 'إعدادات الجهاز غير متوافقة مع طلب الفيديو/الصوت. جرّب إغلاق الكاميرا من التطبيقات الأخرى.';
                }
                if (code === 'SecurityError') {
                    return 'حظر أمني من المتصفح. تأكد من فتح الموقع عبر HTTPS أو localhost.';
                }
                return 'تعذر الوصول للكاميرا أو الميكروفون. جرّب مرة أخرى أو تحقق من إعدادات المتصفح.';
            }

            async function requestMediaPermission() {
                if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
                    setPermissionHelp('المتصفح لا يدعم طلب الأذونات تلقائياً. سنحاول الدخول مباشرة.', true);
                    hidePermissionGate();
                    initJitsi();
                    return;
                }

                // على غير HTTPS قد يفشل طلب الإذن (عدا localhost)
                if (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                    setPermissionHelp('المتصفح يشترط HTTPS لطلب إذن الميكروفون والكاميرا.', true);
                    hidePermissionGate();
                    initJitsi();
                    return;
                }

                try {
                    if (requestMediaBtn) {
                        requestMediaBtn.disabled = true;
                        requestMediaBtn.classList.add('opacity-70', 'cursor-not-allowed');
                    }
                    setPermissionHelp('جاري طلب الإذن من المتصفح...', false);

                    var stream = await navigator.mediaDevices.getUserMedia({ audio: true, video: true });
                    stream.getTracks().forEach(function(track) { track.stop(); });

                    setPermissionHelp('تم منح الإذن بنجاح. جاري فتح الاجتماع...', false);
                    hidePermissionGate();
                    initJitsi();
                } catch (err) {
                    console.error('Media permission error:', err);
                    setPermissionHelp(mapMediaErrorToArabic(err), true);
                    if (requestMediaBtn) {
                        requestMediaBtn.disabled = false;
                        requestMediaBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                    }
                }
            }

            function initJitsi() {
                if (typeof JitsiMeetExternalAPI === 'undefined') {
                    showError();
                    return;
                }
                try {
                    container.innerHTML = '';
                    if (typeof muallimxEnsureJitsiIframeMediaAllow === 'function') {
                        muallimxEnsureJitsiIframeMediaAllow(container);
                    }
                    var options = {
                        roomName: roomName,
                        parentNode: container,
                        width: '100%',
                        height: '100%',
                        userInfo: { displayName: userName, email: userEmail },
                        configOverwrite: {
                            prejoinConfig: { enabled: false },
                            prejoinPageEnabled: false,
                            enableLobby: false,
                            requireDisplayName: false,
                            enableWelcomePage: false,
                            disableDeepLinking: true,
                            enableRecording: true,
                            startWithAudioMuted: true,
                            startWithVideoMuted: true,
                            disableAudioLevels: false,
                            enableNoisyMicDetection: false,
                        },
                        interfaceConfigOverwrite: {
                            APP_NAME: 'Glottical Classroom',
                            NATIVE_APP_NAME: 'Glottical Classroom',
                            PROVIDER_NAME: 'Glottical',
                            JITSI_WATERMARK_LINK: '',
                            HIDE_DEEP_LINKING_LOGO: true,
                            TOOLBAR_BUTTONS: [
                                'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
                                'fodeviceselection', 'hangup', 'chat', 'recording',
                                'raisehand', 'invite', 'tileview', 'videoquality', 'filmstrip',
                                'whiteboard'
                            ],
                            SHOW_JITSI_WATERMARK: false,
                            SHOW_WATERMARK_FOR_GUESTS: false,
                            SHOW_BRAND_WATERMARK: false,
                            SHOW_POWERED_BY: false,
                            MOBILE_APP_PROMO: false,
                            DEFAULT_BACKGROUND: '#0f172a',
                            DISABLE_JOIN_LEAVE_NOTIFICATIONS: false,
                            FILM_STRIP_MAX_HEIGHT: 100,
                        }
                    };
                    api = new JitsiMeetExternalAPI(jitsiDomain, options);

                    if (loadingEl) loadingEl.classList.add('hidden');
                    setTimeout(resizeWbCanvas, 300);
                    setTimeout(resizeWbCanvas, 1200);

                    api.addEventListener('readyToClose', function() {
                        if (isRecording) {
                            stopBrowserRecording();
                        }
                        window.location.href = roomExitUrl;
                    });

                    api.addEventListener('videoConferenceJoined', function() {
                        hasJoinedConference = true;
                        resizeWbCanvas();
                        setTimeout(resizeWbCanvas, 500);
                    });
                } catch (e) {
                    console.error('Jitsi init error:', e);
                    showError();
                }
            }

            function tickMeetingTimer() {
                if (!meetingEndsAt || (!timerChip && !timerChipMobile)) return;
                var end = new Date(meetingEndsAt).getTime();
                var nowTs = Date.now();
                var diff = end - nowTs;
                if (diff <= 0) {
                    if (timerChip) {
                        timerChip.textContent = 'انتهت المدة المسموح بها';
                        timerChip.classList.remove('bg-amber-500/20', 'border-amber-500/30', 'text-amber-200');
                        timerChip.classList.add('bg-rose-600/20', 'border-rose-500/30', 'text-rose-200');
                    }
                    if (timerChipMobile) {
                        timerChipMobile.textContent = 'انتهت المدة';
                        timerChipMobile.classList.remove('bg-amber-500/20', 'border-amber-500/30', 'text-amber-200');
                        timerChipMobile.classList.add('bg-rose-600/20', 'border-rose-500/30', 'text-rose-200');
                    }
                    window.location.href = roomExitUrl;
                    return;
                }
                var mins = Math.floor(diff / 60000);
                var secs = Math.floor((diff % 60000) / 1000);
                var fullText = 'الوقت المتبقي: ' + mins + ':' + String(secs).padStart(2, '0');
                var shortText = mins + ':' + String(secs).padStart(2, '0');
                if (timerChip) timerChip.textContent = fullText;
                if (timerChipMobile) timerChipMobile.textContent = shortText;
            }
            setInterval(tickMeetingTimer, 1000);
            tickMeetingTimer();

            var script = document.createElement('script');
            script.src = 'https://' + jitsiDomain + '/external_api.js';
            script.async = false;
            script.onload = function() {
                if (requestMediaBtn) {
                    requestMediaBtn.addEventListener('click', requestMediaPermission);
                }
                if (joinWithoutMediaBtn) {
                    joinWithoutMediaBtn.addEventListener('click', function() {
                        hidePermissionGate();
                        initJitsi();
                    });
                }
            };
            script.onerror = function() {
                console.error('Failed to load Jitsi external_api.js from ' + script.src);
                showError();
            };
            document.head.appendChild(script);
        })();
    </script>
    <script>
        (function () {
            var wrap = document.getElementById('pkg-features-dd-wrap');
            var btn = document.getElementById('pkg-features-dd-btn');
            var panel = document.getElementById('pkg-features-dd-panel');
            var chev = document.getElementById('pkg-features-dd-chevron');
            if (!wrap || !btn || !panel) return;
            function setOpen(open) {
                btn.setAttribute('aria-expanded', open ? 'true' : 'false');
                if (chev) chev.style.transform = open ? 'rotate(180deg)' : '';
                if (!open) {
                    panel.classList.add('hidden');
                    if (typeof window.mxClearClassroomDropdownPosition === 'function') {
                        window.mxClearClassroomDropdownPosition(panel);
                    }
                } else if (typeof window.mxPositionClassroomDropdown === 'function') {
                    panel.style.opacity = '0';
                    panel.style.pointerEvents = 'none';
                    panel.classList.remove('hidden');
                    panel.classList.add('mx-dd-visible');
                    window.mxPositionClassroomDropdown(panel, wrap, btn);
                    requestAnimationFrame(function () {
                        requestAnimationFrame(function () {
                            panel.style.transition = 'opacity 0.14s ease-out';
                            panel.style.opacity = '1';
                            panel.style.pointerEvents = '';
                            window.mxPositionClassroomDropdown(panel, wrap, btn);
                        });
                    });
                }
            }
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                setOpen(panel.classList.contains('hidden'));
            });
            document.addEventListener('mousedown', function (e) {
                if (!wrap.contains(e.target)) setOpen(false);
            }, true);
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') setOpen(false);
            });
            var pkgResizeT = null;
            window.addEventListener('resize', function () {
                if (panel.classList.contains('hidden') || typeof window.mxPositionClassroomDropdown !== 'function') return;
                if (pkgResizeT) clearTimeout(pkgResizeT);
                pkgResizeT = setTimeout(function () {
                    pkgResizeT = null;
                    window.mxPositionClassroomDropdown(panel, wrap, btn);
                }, 80);
            });
        })();
    </script>
</body>
</html>
