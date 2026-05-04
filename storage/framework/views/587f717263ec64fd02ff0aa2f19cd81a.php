<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>انضم إلى Muallimx Classroom — <?php echo e($code); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        * { font-family: 'IBM Plex Sans Arabic', system-ui, sans-serif; }
        body { margin: 0; padding: 0; background: #0c1222; min-height: 100vh; }
        .room-body { position: relative; display: flex; flex-direction: column; height: calc(100vh - 72px); }
        #jitsi-container { width: 100%; flex: 1; min-height: 0; background: #0f172a; }
        #jitsi-container iframe { width: 100% !important; height: 100% !important; border: none; }
    </style>
</head>
<body class="bg-slate-950 text-white">
    
    <div id="join-screen" class="min-h-screen flex flex-col items-center justify-center p-4">
        <div class="w-full max-w-md rounded-2xl bg-slate-800/90 border border-slate-600 p-6 shadow-2xl shadow-black/30">
            <?php if(!empty($meetingEnded)): ?>
                <div class="text-center mb-2">
                    <div class="w-16 h-16 rounded-2xl bg-slate-600/40 text-slate-400 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-door-closed text-3xl"></i>
                    </div>
                    <h1 class="text-xl font-bold text-white">انتهى الاجتماع</h1>
                    <p class="text-slate-400 text-sm mt-3 leading-relaxed">قام منظم الاجتماع بإنهائه. لا يمكن إعادة فتح الغرفة أو الانضمام مرة أخرى من هذا الرابط.</p>
                </div>
                <?php if($meeting && $meeting->title): ?>
                    <p class="text-slate-500 text-sm mb-4 text-center"><?php echo e($meeting->title); ?></p>
                <?php endif; ?>
                <p class="text-slate-500 text-xs text-center">كود الغرفة: <span class="font-mono text-slate-400"><?php echo e($code); ?></span></p>
            <?php else: ?>
            <div class="text-center mb-6">
                <div class="w-16 h-16 rounded-2xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-video text-3xl"></i>
                </div>
                <h1 class="text-xl font-bold text-white">Muallimx Classroom</h1>
                <p class="text-slate-400 text-sm mt-1">انضم إلى الاجتماع باستخدام الكود أو الرابط</p>
            </div>
            <?php if($meeting && $meeting->title): ?>
                <p class="text-slate-300 text-sm mb-4 text-center"><?php echo e($meeting->title); ?></p>
            <?php endif; ?>
            <p class="text-slate-400 text-xs mb-4 text-center">كود الغرفة: <span class="font-mono font-bold text-cyan-400 text-lg"><?php echo e($code); ?></span></p>
            <p class="text-slate-400 text-xs mb-4 text-center">الحد الأقصى للمشاركين: <span class="font-bold text-amber-300"><?php echo e($maxParticipants); ?></span></p>
            <div class="space-y-3">
                <label class="block text-sm font-medium text-slate-300">اسمك (يظهر للمشاركين)</label>
                <input type="text" id="guest-name" placeholder="أدخل اسمك" value="" class="w-full px-4 py-3 rounded-xl bg-slate-700 border border-slate-600 text-white placeholder-slate-500 focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
            </div>
            <div class="mt-6">
                <button type="button" id="btn-join" class="w-full px-6 py-3 rounded-xl bg-rose-500 hover:bg-rose-400 text-white font-bold transition-colors">
                    <i class="fas fa-video ml-2"></i>
                    انضم الآن
                </button>
            </div>
            <p class="text-slate-500 text-xs mt-4 text-center">لا تحتاج إلى حساب. ادخل باسمك وانضم مباشرة.</p>
            <?php endif; ?>
        </div>
    </div>

    
    <div id="meeting-screen" class="hidden h-screen flex flex-col">
        <header class="h-[72px] bg-gradient-to-l from-slate-900 to-slate-800 border-b border-slate-700/50 flex items-center justify-between px-4 sm:px-6 shadow-lg flex-shrink-0 gap-2">
            <div class="flex items-center gap-3 min-w-0">
                <span class="w-10 h-10 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center shrink-0">
                    <i class="fas fa-video text-lg"></i>
                </span>
                <span class="font-bold text-white truncate">Muallimx Classroom</span>
                <span class="text-slate-400 text-sm shrink-0">— <?php echo e($code); ?></span>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <div id="mx-guest-wb-wrap" class="hidden">
                    <button type="button" id="btn-mx-share-draw-guest"
                            class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 rounded-xl bg-amber-600/25 hover:bg-amber-600/35 text-amber-100 text-sm font-semibold transition-colors border border-amber-500/40"
                            title="رسم فوق ما يظهر في الاجتماع (يُرى لدى المنظم فوق نفس العرض)">
                        <i class="fas fa-pen-fancy text-amber-300"></i>
                        <span class="hidden sm:inline">رسم فوق العرض</span>
                    </button>
                </div>
                <button type="button" id="btn-leave" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-sm font-semibold transition-colors shadow-lg shadow-rose-500/20">
                    <i class="fas fa-sign-out-alt"></i> مغادرة
                </button>
            </div>
        </header>
        <div class="room-body">
            <div id="mx-video-stack" class="relative flex-1 min-h-0 flex flex-col">
                <main id="jitsi-container" class="flex-1 min-h-0 relative" role="application" aria-label="غرفة الاجتماع"></main>
                <?php echo $__env->make('partials.mx-share-annotation-overlay', [
                    'mxAnnRole' => 'classroom_guest_emit',
                    'mxAnnPostUrl' => route('classroom.join.share-annotation', $code),
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </div>
    </div>

    <?php if(empty($meetingEnded)): ?>
    <?php echo $__env->make('partials.jitsi-iframe-media-allow', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <script src="https://<?php echo e($jitsiDomain); ?>/external_api.js"></script>
    <script>
        const domain = '<?php echo e($jitsiDomain); ?>';
        const roomName = '<?php echo e($roomName); ?>';
        const code = '<?php echo e($code); ?>';
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let api = null;
        let joinToken = null;
        let heartbeatTimer = null;

        function applyGuestWhiteboardAllowed(on) {
            if (typeof window.__mxShareAnnSetAllowed === 'function') {
                window.__mxShareAnnSetAllowed(!!on);
            }
            var wrap = document.getElementById('mx-guest-wb-wrap');
            if (!wrap) return;
            if (on) wrap.classList.remove('hidden');
            else wrap.classList.add('hidden');
        }

        document.getElementById('btn-join').addEventListener('click', async function() {
            const name = document.getElementById('guest-name').value.trim() || 'ضيف';
            const btn = document.getElementById('btn-join');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري التحقق...';

            try {
                const enterResp = await fetch(`/classroom/join/${code}/enter`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ display_name: name })
                });
                const enterData = await enterResp.json();
                if (!enterResp.ok || !enterData.ok) {
                    alert(enterData.message || 'لا يمكن الانضمام الآن.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-video ml-2"></i> انضم الآن';
                    return;
                }
                joinToken = enterData.token;
                if (typeof window.__mxShareAnnSetGuestToken === 'function') {
                    window.__mxShareAnnSetGuestToken(joinToken);
                }
                applyGuestWhiteboardAllowed(!!enterData.allow_participant_whiteboard);
            } catch (e) {
                alert('تعذر الاتصال بالخادم. حاول مرة أخرى.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-video ml-2"></i> انضم الآن';
                return;
            }

            document.getElementById('join-screen').classList.add('hidden');
            document.getElementById('meeting-screen').classList.remove('hidden');

            const jitsiRoot = document.querySelector('#jitsi-container');
            if (typeof muallimxEnsureJitsiIframeMediaAllow === 'function') {
                muallimxEnsureJitsiIframeMediaAllow(jitsiRoot);
            }

            const options = {
                roomName: roomName,
                parentNode: jitsiRoot,
                width: '100%',
                height: '100%',
                userInfo: { displayName: name },
                configOverwrite: {
                    prejoinConfig: { enabled: false },
                    prejoinPageEnabled: false,
                    enableLobby: false,
                    requireDisplayName: false,
                    enableWelcomePage: false,
                    disableDeepLinking: true,
                    enableRecording: false,
                    startWithAudioMuted: true,
                    startWithVideoMuted: true,
                    enableNoisyMicDetection: false,
                },
                interfaceConfigOverwrite: {
                    APP_NAME: 'Muallimx Classroom',
                    NATIVE_APP_NAME: 'Muallimx Classroom',
                    PROVIDER_NAME: 'Muallimx',
                    JITSI_WATERMARK_LINK: '',
                    HIDE_DEEP_LINKING_LOGO: true,
                    TOOLBAR_BUTTONS: [
                        'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
                        'fodeviceselection', 'hangup', 'chat',
                        'raisehand', 'tileview', 'videoquality', 'filmstrip'
                    ],
                    SHOW_JITSI_WATERMARK: false,
                    SHOW_WATERMARK_FOR_GUESTS: false,
                    SHOW_BRAND_WATERMARK: false,
                    SHOW_POWERED_BY: false,
                    MOBILE_APP_PROMO: false,
                    DEFAULT_BACKGROUND: '#0f172a',
                    FILM_STRIP_MAX_HEIGHT: 100,
                }
            };
            api = new JitsiMeetExternalAPI(domain, options);
            var drawGuestBtn = document.getElementById('btn-mx-share-draw-guest');
            if (drawGuestBtn && typeof window.__mxShareAnnOpenToolbar === 'function') {
                drawGuestBtn.addEventListener('click', function () { window.__mxShareAnnOpenToolbar(); });
            }
            heartbeatTimer = setInterval(async function() {
                if (!joinToken) return;
                try {
                    const hbRes = await fetch(`/classroom/join/${code}/heartbeat`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ token: joinToken })
                    });
                    if (hbRes.ok) {
                        const hbData = await hbRes.json();
                        if (typeof hbData.allow_participant_whiteboard !== 'undefined') {
                            applyGuestWhiteboardAllowed(!!hbData.allow_participant_whiteboard);
                        }
                    }
                } catch (e) {}
            }, 30000);

            api.addEventListener('readyToClose', function() {
                leaveMeetingAndReload();
            });

            document.getElementById('btn-leave').onclick = function() {
                if (api) api.executeCommand('hangup');
            };
        });

        async function leaveMeetingAndReload() {
            if (heartbeatTimer) clearInterval(heartbeatTimer);
            if (joinToken) {
                try {
                    await fetch(`/classroom/join/${code}/leave`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ token: joinToken, _token: csrfToken })
                    });
                } catch (e) {}
            }
            window.location.reload();
        }

        window.addEventListener('beforeunload', function() {
            if (!joinToken) return;
            navigator.sendBeacon(`/classroom/join/${code}/leave`, new Blob([JSON.stringify({ token: joinToken, _token: csrfToken })], { type: 'application/json' }));
        });
    </script>
    <?php endif; ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\classroom\join.blade.php ENDPATH**/ ?>