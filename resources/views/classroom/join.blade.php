<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>انضم إلى Glottical Classroom — {{ $code }}</title>
    @include('partials.favicon-links')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/classroom-curriculum-presenter.css') }}">
    <script src="{{ asset('js/classroom-curriculum-presenter.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/livekit-client@2.9.1/dist/livekit-client.umd.min.js"></script>
    <style>
        :root { --st-brand: #0B3D91; --st-blue: #0997d9; --st-gold: #F5B800; }
        * { font-family: 'Cairo', 'Tajawal', system-ui, sans-serif; box-sizing: border-box; }
        body { margin: 0; padding: 0; background: #f8f9fa; min-height: 100vh; color: #4f4f4f; }
        .room-body { position: relative; display: flex; flex-direction: column; height: calc(100vh - 72px); background: #0b1220; }
        #lk-guest-stage { flex: 1; min-height: 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 8px; padding: 8px; overflow: auto; }
        .lk-tile { position: relative; background: #0f172a; border: 1px solid #1e293b; border-radius: 1rem; overflow: hidden; min-height: 180px; }
        .lk-tile video { width: 100%; height: 100%; object-fit: cover; background: #020617; }
        .lk-tile-label { position: absolute; left: .75rem; bottom: .75rem; background: rgba(15,23,42,.85); color: #e2e8f0; font-size: .7rem; font-weight: 700; padding: .25rem .55rem; border-radius: .5rem; }
        .lk-btn { display: inline-flex; align-items: center; gap: .5rem; border-radius: 999px; background: #1e293b; color: #e2e8f0; padding: .55rem 1rem; font-size: .8rem; font-weight: 700; border: 1px solid #334155; }
        .lk-btn.is-off { background: #7f1d1d; border-color: #991b1b; color: #fecaca; }
        .join-card {
            background: #fff;
            border: 1px solid #ebebeb;
            border-radius: 18px;
            box-shadow: 0 14px 36px rgba(7,18,38,.08);
        }
        .join-hero-bar {
            background: linear-gradient(135deg, var(--st-brand), var(--st-blue));
            color: #fff;
        }
    </style>
</head>
<body>
    <div id="join-screen" class="min-h-screen flex flex-col items-center justify-center p-4">
        <div class="w-full max-w-md join-card overflow-hidden">
            <div class="join-hero-bar px-6 py-5 text-center">
                <div class="w-14 h-14 rounded-2xl bg-white/15 text-[var(--st-gold)] flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-video text-2xl"></i>
                </div>
                <h1 class="text-xl font-black m-0">{{ config('app.name') }} Classroom</h1>
                <p class="text-white/85 text-sm mt-1 mb-0 font-semibold">انضم عبر LiveKit</p>
            </div>
            <div class="p-6">
            @if(!empty($meetingEnded))
                <div class="text-center">
                    <h2 class="text-lg font-bold text-slate-800">انتهى الاجتماع</h2>
                    <p class="text-slate-500 text-sm mt-3 leading-relaxed">قام منظم الاجتماع بإنهائه. لا يمكن الانضمام من هذا الرابط.</p>
                    <p class="text-slate-400 text-xs mt-4">كود الغرفة: <span class="font-mono">{{ $code }}</span></p>
                </div>
            @elseif(!empty($meetingNotStarted))
                <div class="text-center">
                    <h2 class="text-lg font-bold text-slate-800">المحاضرة لم تبدأ بعد</h2>
                    <p class="text-slate-500 text-sm mt-3 leading-relaxed">انتظر حتى يبدأ المدرب الاجتماع، ثم حدّث الصفحة وانضم.</p>
                    <p class="text-slate-400 text-xs mb-4">كود الغرفة: <span class="font-mono">{{ $code }}</span></p>
                    <button type="button" onclick="window.location.reload()" class="w-full px-6 py-3 rounded-xl bg-[var(--st-brand)] hover:opacity-95 text-white font-bold transition-colors">
                        <i class="fas fa-sync-alt ml-2"></i> تحديث الصفحة
                    </button>
                </div>
            @else
                @if($meeting && $meeting->title)
                    <p class="text-slate-700 text-sm mb-3 text-center font-bold">{{ $meeting->title }}</p>
                @endif
                <p class="text-slate-500 text-xs mb-2 text-center">كود الغرفة: <span class="font-mono font-bold text-[var(--st-blue)] text-lg">{{ $code }}</span></p>
                <p class="text-slate-500 text-xs mb-4 text-center">الحد الأقصى: <span class="font-bold text-amber-600">{{ $maxParticipants }}</span></p>
                @if(empty($livekitConfigured))
                    <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 text-amber-800 text-xs p-3 font-semibold">
                        إعدادات LiveKit غير مكتملة على الخادم. تواصل مع الإدارة.
                    </div>
                @endif
                <div class="space-y-3">
                    <label class="block text-sm font-bold text-slate-700">اسمك (يظهر للمشاركين)</label>
                    <input type="text" id="guest-name" placeholder="أدخل اسمك" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-[var(--st-blue)] focus:border-transparent">
                </div>
                <div class="mt-6">
                    <button type="button" id="btn-join" class="w-full px-6 py-3 rounded-xl bg-[var(--st-gold)] hover:brightness-95 text-[#072A66] font-black transition-colors" {{ empty($livekitConfigured) ? 'disabled' : '' }}>
                        <i class="fas fa-video ml-2"></i> انضم الآن
                    </button>
                </div>
                <p class="text-slate-400 text-xs mt-4 text-center">لا تحتاج إلى حساب. ادخل باسمك وانضم مباشرة.</p>
            @endif
            </div>
        </div>
    </div>

    <div id="meeting-screen" class="hidden h-screen flex flex-col bg-slate-950 text-white">
        <header class="h-[72px] join-hero-bar flex items-center justify-between px-4 sm:px-6 shadow-lg flex-shrink-0 gap-2">
            <div class="flex items-center gap-3 min-w-0">
                <span class="w-10 h-10 rounded-xl bg-white/15 text-[var(--st-gold)] flex items-center justify-center shrink-0">
                    <i class="fas fa-video text-lg"></i>
                </span>
                <span class="font-bold text-white truncate">{{ config('app.name') }} Classroom</span>
                <span class="text-white/75 text-sm shrink-0">— {{ $code }}</span>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <div id="mx-guest-wb-wrap" class="hidden">
                    <button type="button" id="btn-mx-share-draw-guest"
                            class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-sm font-semibold transition-colors border border-white/30">
                        <i class="fas fa-pen-fancy text-[var(--st-gold)]"></i>
                        <span class="hidden sm:inline">رسم فوق العرض</span>
                    </button>
                </div>
                <button type="button" id="btn-leave" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-sm font-semibold transition-colors">
                    <i class="fas fa-sign-out-alt"></i> مغادرة
                </button>
            </div>
        </header>
        <div class="room-body">
            <div id="mx-video-stack" class="relative flex-1 min-h-0 flex flex-col">
                <div id="lk-status" class="absolute top-3 left-1/2 -translate-x-1/2 z-20 px-4 py-1.5 rounded-full bg-slate-900/90 text-slate-200 text-xs font-semibold border border-slate-700 hidden"></div>
                <div id="lk-guest-stage"></div>
                <div class="shrink-0 border-t border-slate-800 bg-slate-900/95 px-4 py-3 flex flex-wrap items-center justify-center gap-2">
                    <button type="button" id="lk-toggle-mic" class="lk-btn"><i class="fas fa-microphone"></i> ميكروفون</button>
                    <button type="button" id="lk-toggle-cam" class="lk-btn"><i class="fas fa-video"></i> كاميرا</button>
                    <button type="button" id="lk-toggle-screen" class="lk-btn"><i class="fas fa-desktop"></i> شاشة</button>
                </div>
                @include('partials.mx-share-annotation-overlay', [
                    'mxAnnRole' => 'classroom_guest_emit',
                    'mxAnnPostUrl' => route('classroom.join.share-annotation', $code),
                ])
            </div>
        </div>
    </div>

    @if(empty($meetingEnded) && empty($meetingNotStarted))
    <script>
        const code = @json($code);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let joinToken = null;
        let heartbeatTimer = null;
        let room = null;

        function applyGuestWhiteboardAllowed(on) {
            if (typeof window.__mxShareAnnSetAllowed === 'function') {
                window.__mxShareAnnSetAllowed(!!on);
            }
            var wrap = document.getElementById('mx-guest-wb-wrap');
            if (!wrap) return;
            if (on) wrap.classList.remove('hidden');
            else wrap.classList.add('hidden');
        }

        function setStatus(msg, isError) {
            var el = document.getElementById('lk-status');
            if (!el) return;
            el.textContent = msg;
            el.classList.remove('hidden');
            el.classList.toggle('bg-rose-900/90', !!isError);
        }

        async function connectLiveKit(livekit) {
            if (!window.LivekitClient) {
                setStatus('تعذر تحميل مكتبة LiveKit', true);
                return;
            }
            const url = livekit && livekit.livekitUrl;
            const token = livekit && livekit.livekitToken;
            if (!url || !token) {
                setStatus('توكن LiveKit غير متاح', true);
                return;
            }
            const { Room, RoomEvent, Track, createLocalTracks, createLocalScreenTracks } = window.LivekitClient;
            room = new Room({ adaptiveStream: true, dynacast: true });
            const stage = document.getElementById('lk-guest-stage');
            const tiles = new Map();
            let micOn = true, camOn = true, screenTrack = null;

            function tileKey(participant, source) {
                return participant.identity + ':' + source;
            }
            function ensureTile(participant, source) {
                const key = tileKey(participant, source);
                if (tiles.has(key)) return tiles.get(key);
                const el = document.createElement('div');
                el.className = 'lk-tile';
                const video = document.createElement('video');
                video.autoplay = true;
                video.playsInline = true;
                if (participant.isLocal) video.muted = true;
                const label = document.createElement('div');
                label.className = 'lk-tile-label';
                label.textContent = (participant.name || participant.identity) + (source === Track.Source.ScreenShare ? ' · شاشة' : '');
                el.appendChild(video);
                el.appendChild(label);
                stage.appendChild(el);
                const tile = { el, video };
                tiles.set(key, tile);
                return tile;
            }
            function attachTrack(track, participant) {
                if (track.kind !== 'video' && track.kind !== 'audio') return;
                if (track.kind === 'audio' && !participant.isLocal) {
                    const audio = track.attach();
                    audio.autoplay = true;
                    document.body.appendChild(audio);
                    return;
                }
                if (track.kind === 'video') {
                    const tile = ensureTile(participant, track.source);
                    track.attach(tile.video);
                }
            }
            room.on(RoomEvent.TrackSubscribed, (track, _pub, participant) => attachTrack(track, participant));
            room.on(RoomEvent.LocalTrackPublished, (pub) => {
                if (pub.track) attachTrack(pub.track, room.localParticipant);
            });
            room.on(RoomEvent.Disconnected, () => setStatus('انقطع الاتصال', true));

            setStatus('جاري الاتصال...');
            await room.connect(url, token);
            const localTracks = await createLocalTracks({ audio: true, video: true });
            await Promise.all(localTracks.map((t) => room.localParticipant.publishTrack(t)));
            localTracks.forEach((t) => attachTrack(t, room.localParticipant));
            setStatus('متصل');

            document.getElementById('lk-toggle-mic')?.addEventListener('click', async function () {
                micOn = !micOn;
                await room.localParticipant.setMicrophoneEnabled(micOn);
                this.classList.toggle('is-off', !micOn);
            });
            document.getElementById('lk-toggle-cam')?.addEventListener('click', async function () {
                camOn = !camOn;
                await room.localParticipant.setCameraEnabled(camOn);
                this.classList.toggle('is-off', !camOn);
            });
            document.getElementById('lk-toggle-screen')?.addEventListener('click', async function () {
                if (screenTrack) {
                    await room.localParticipant.unpublishTrack(screenTrack);
                    screenTrack.stop();
                    screenTrack = null;
                    this.classList.remove('is-off');
                    return;
                }
                const tracks = await createLocalScreenTracks({ audio: true });
                screenTrack = tracks[0];
                await room.localParticipant.publishTrack(screenTrack);
                this.classList.add('is-off');
            });
        }

        document.getElementById('btn-join')?.addEventListener('click', async function() {
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
                if (window.MxClassroomCurriculumPresenter) {
                    if (window.__mxCurriculumPresenter && typeof window.__mxCurriculumPresenter.destroy === 'function') {
                        window.__mxCurriculumPresenter.destroy();
                    }
                    window.__mxCurriculumPresenter = window.MxClassroomCurriculumPresenter.attach(null, {
                        isHost: false,
                        guestToken: joinToken,
                        stateUrl: @json(route('classroom.join.curriculum.state', $code)),
                        pollIntervalMs: 1500,
                    });
                }

                document.getElementById('join-screen').classList.add('hidden');
                document.getElementById('meeting-screen').classList.remove('hidden');

                await connectLiveKit(enterData.livekit || {});

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
            } catch (e) {
                console.error(e);
                alert('تعذر الاتصال بالخادم. حاول مرة أخرى.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-video ml-2"></i> انضم الآن';
            }
        });

        async function leaveMeetingAndReload() {
            if (heartbeatTimer) clearInterval(heartbeatTimer);
            if (window.__mxCurriculumPresenter && typeof window.__mxCurriculumPresenter.destroy === 'function') {
                try { window.__mxCurriculumPresenter.destroy(); } catch (e) {}
                window.__mxCurriculumPresenter = null;
            }
            try { if (room) await room.disconnect(); } catch (e) {}
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

        document.getElementById('btn-leave')?.addEventListener('click', leaveMeetingAndReload);
        window.addEventListener('beforeunload', function() {
            if (!joinToken) return;
            navigator.sendBeacon(`/classroom/join/${code}/leave`, new Blob([JSON.stringify({ token: joinToken, _token: csrfToken })], { type: 'application/json' }));
        });
    </script>
    @endif
</body>
</html>
