<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($liveSession->title); ?> — بث مباشر</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        * { font-family: 'IBM Plex Sans Arabic', system-ui, sans-serif; }
        body { margin: 0; padding: 0; background: #0c1222; overflow: hidden; height: 100vh; }
        #mx-live-broadcast-root { width: 100%; flex: 1; min-height: 0; background: #0f172a; position: relative; }
        .room-body { position: relative; display: flex; flex-direction: column; height: calc(100vh - 72px); }
        #mx-live-broadcast-root iframe { width: 100% !important; height: 100% !important; border: none; }

        /* Session ended overlay */
        #mx-session-ended {
            display: none;
            position: fixed; inset: 0; z-index: 9999;
            background: rgba(10,17,32,0.95);
            flex-direction: column; align-items: center; justify-content: center;
            gap: 16px;
            backdrop-filter: blur(8px);
        }
        #mx-session-ended.show { display: flex; }
        #mx-session-ended .mx-icon { font-size: 56px; color: #f87171; }
        #mx-session-ended h2 { color: #f1f5f9; font-size: 20px; font-weight: 700; margin: 0; }
        #mx-session-ended p  { color: #94a3b8; font-size: 14px; margin: 0; }
        #mx-redir-bar {
            width: 200px; height: 4px; background: rgba(148,163,184,0.2);
            border-radius: 2px; overflow: hidden;
        }
        #mx-redir-fill {
            height: 100%; background: #38bdf8; border-radius: 2px;
            width: 0; transition: width 5s linear;
        }
    </style>
</head>
<body class="bg-slate-950">

    
    <div id="mx-session-ended">
        <div class="mx-icon"><i class="fas fa-broadcast-tower"></i></div>
        <h2>انتهت الجلسة</h2>
        <p>قام المدرب بإنهاء البث المباشر</p>
        <div id="mx-redir-bar"><div id="mx-redir-fill"></div></div>
        <p style="font-size:12px;color:#64748b;">سيتم توجيهك تلقائياً...</p>
        <a href="<?php echo e(route('student.live-sessions.index')); ?>"
           class="mt-2 inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-sm font-semibold">
            <i class="fas fa-arrow-left"></i> العودة الآن
        </a>
    </div>

    
    <header class="h-[72px] bg-gradient-to-l from-slate-900 to-slate-800 border-b border-slate-700/50 flex items-center justify-between px-4 sm:px-6 shadow-lg">
        <div class="flex items-center gap-4">
            <a href="<?php echo e(route('student.live-sessions.index')); ?>" class="flex items-center gap-2 text-slate-300 hover:text-white transition-colors">
                <span class="w-10 h-10 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center">
                    <i class="fas fa-broadcast-tower text-lg"></i>
                </span>
                <span class="font-bold text-white hidden sm:inline">Muallimx</span>
            </a>
            <span class="w-px h-6 bg-slate-600 hidden sm:block"></span>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse shadow-lg shadow-red-500/50"></span>
                <span class="text-white font-semibold text-sm"><?php echo e($liveSession->title); ?></span>
                <?php if($liveSession->instructor): ?>
                <span class="text-slate-400 text-xs hidden sm:inline"><?php echo e($liveSession->instructor->name); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <div id="mx-student-wb-wrap" class="<?php echo e(($allowStudentWhiteboard ?? false) ? '' : 'hidden'); ?>">
                <button type="button" id="btn-mx-share-draw"
                        class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 rounded-xl bg-amber-600/25 hover:bg-amber-600/35 text-amber-100 text-sm font-semibold transition-colors border border-amber-500/40"
                        title="رسم فوق ما يظهر في الاجتماع (يُرى لدى المدرب فوق نفس العرض)">
                    <i class="fas fa-pen-fancy text-amber-300"></i>
                    <span class="hidden sm:inline">رسم فوق البث</span>
                </button>
            </div>
            <form method="POST" action="<?php echo e(route('student.live-sessions.leave', $liveSession)); ?>" class="inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-700/80 hover:bg-slate-600 text-slate-200 text-sm font-medium transition-colors border border-slate-600">
                    <i class="fas fa-sign-out-alt"></i> مغادرة
                </button>
            </form>
        </div>
    </header>

    <div class="room-body">
        <div id="mx-video-stack" class="relative flex-1 min-h-0 flex flex-col">
            <main id="mx-live-broadcast-root" class="flex-1 min-h-0 relative" role="application" aria-label="غرفة البث — Muallimx"></main>
            <?php echo $__env->make('partials.mx-share-annotation-overlay', [
                'mxAnnRole' => 'student_emit',
                'mxAnnPostUrl' => route('student.live-sessions.share-annotation', $liveSession),
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>

    <?php echo $__env->make('partials.jitsi-iframe-media-allow', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <script src="https://<?php echo e($jitsiDomain); ?>/external_api.js"></script>
    <script>
        const domain    = '<?php echo e($jitsiDomain); ?>';
        const indexUrl  = '<?php echo e(route("student.live-sessions.index")); ?>';
        const jitsiRoot = document.querySelector('#mx-live-broadcast-root');
        if (typeof muallimxEnsureJitsiIframeMediaAllow === 'function') {
            muallimxEnsureJitsiIframeMediaAllow(jitsiRoot);
        }

        const options = {
            roomName: '<?php echo e($liveSession->room_name); ?>',
            parentNode: jitsiRoot,
            width: '100%',
            height: '100%',
            userInfo: {
                displayName: '<?php echo e($user->name); ?>',
                email: '<?php echo e($user->email); ?>'
            },
            configOverwrite: {
                prejoinConfig: { enabled: false },
                prejoinPageEnabled: false,
                enableLobby: false,
                requireDisplayName: false,
                enableWelcomePage: false,
                disableDeepLinking: true,
                startWithAudioMuted: <?php echo e($liveSession->mute_on_join ? 'true' : 'false'); ?>,
                startWithVideoMuted: <?php echo e($liveSession->video_off_on_join ? 'true' : 'false'); ?>,
                enableNoisyMicDetection: false,
                <?php if(!$liveSession->allow_chat): ?>
                disableChat: true,
                <?php endif; ?>
            },
            interfaceConfigOverwrite: {
                APP_NAME: 'Muallimx',
                NATIVE_APP_NAME: 'Muallimx',
                PROVIDER_NAME: 'Muallimx',
                JITSI_WATERMARK_LINK: '',
                HIDE_DEEP_LINKING_LOGO: true,
                TOOLBAR_BUTTONS: [],
                TOOLBAR_ALWAYS_VISIBLE: false,
                SHOW_JITSI_WATERMARK: false,
                SHOW_WATERMARK_FOR_GUESTS: false,
                SHOW_BRAND_WATERMARK: false,
                SHOW_POWERED_BY: false,
                MOBILE_APP_PROMO: false,
                DEFAULT_BACKGROUND: '#0f172a',
                DISABLE_JOIN_LEAVE_NOTIFICATIONS: true,
                FILM_STRIP_MAX_HEIGHT: 120,
            }
        };

        const api = new JitsiMeetExternalAPI(domain, options);

        /* ══════════════════════════════════════════════
           إعادة التوجيه عند انتهاء الجلسة
        ══════════════════════════════════════════════ */
        function showSessionEndedAndRedirect() {
            var overlay = document.getElementById('mx-session-ended');
            var fill    = document.getElementById('mx-redir-fill');
            overlay.classList.add('show');
            // شريط التقدم
            setTimeout(function() { fill.style.width = '100%'; }, 100);
            // توجيه تلقائي بعد 5 ثوانٍ
            setTimeout(function() {
                window.location.href = indexUrl;
            }, 5500);
        }

        // عند مغادرة الطالب نفسه
        api.addEventListener('readyToClose', function() {
            window.location.href = indexUrl;
        });

        // عند إنهاء المعلم للجلسة أو قطع الاتصال
        api.addEventListener('videoConferenceLeft', function() {
            showSessionEndedAndRedirect();
        });

        (function () {
            var wrap = document.getElementById('mx-student-wb-wrap');
            var drawBtn = document.getElementById('btn-mx-share-draw');
            var statusUrl = '<?php echo e(route("student.live-sessions.status", $liveSession)); ?>';
            function applyAllow(on) {
                if (typeof window.__mxShareAnnSetAllowed === 'function') {
                    window.__mxShareAnnSetAllowed(!!on);
                }
                if (!wrap) return;
                if (on) wrap.classList.remove('hidden');
                else wrap.classList.add('hidden');
            }
            if (drawBtn && typeof window.__mxShareAnnOpenToolbar === 'function') {
                drawBtn.addEventListener('click', function () { window.__mxShareAnnOpenToolbar(); });
            }
            applyAllow(<?php echo e(($allowStudentWhiteboard ?? false) ? 'true' : 'false'); ?>);
            setInterval(function () {
                fetch(statusUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (r) { return r.ok ? r.json() : null; })
                    .then(function (data) {
                        if (!data) return;
                        if (data.status === 'ended' || data.ended === true) {
                            showSessionEndedAndRedirect();
                            return;
                        }
                        if (typeof data.allow_student_whiteboard !== 'undefined') {
                            applyAllow(!!data.allow_student_whiteboard);
                        }
                    })
                    .catch(function () {});
            }, 12000);
        })();
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\student\live-sessions\room.blade.php ENDPATH**/ ?>