{{-- غرفة LiveKit مضمّنة — يتطلب $livekitUrl و $livekitToken و $user --}}
@php
    $lkRole = $lkRole ?? 'participant';
    $lkLeaveUrl = $lkLeaveUrl ?? url('/');
    $displayName = $user->name ?? ('User #'.($user->id ?? ''));
    $lkStartAudio = $lkStartAudio ?? true;
    $lkStartVideo = $lkStartVideo ?? true;
    $lkAllowScreenShare = $lkAllowScreenShare ?? true;
    $lkAllowChat = $lkAllowChat ?? true;
@endphp
<div id="lk-room-shell" class="relative flex-1 min-h-0 flex flex-col bg-slate-950">
    <div id="lk-status" class="absolute top-3 left-1/2 -translate-x-1/2 z-20 px-4 py-1.5 rounded-full bg-slate-900/90 text-slate-200 text-xs font-semibold border border-slate-700 hidden"></div>
    <div class="flex-1 min-h-0 flex flex-col md:flex-row">
        <div id="lk-stage" class="flex-1 min-h-0 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-2 p-2 overflow-auto"></div>
        @if($lkAllowChat)
        <aside id="lk-chat-panel" class="shrink-0 w-full md:w-72 border-t md:border-t-0 md:border-s border-slate-800 bg-slate-900/90 flex flex-col max-h-52 md:max-h-none">
            <div class="px-3 py-2 border-b border-slate-800 text-xs font-bold text-slate-300 flex items-center gap-2">
                <i class="fas fa-comments text-cyan-400"></i> الدردشة
            </div>
            <div id="lk-chat-log" class="flex-1 overflow-y-auto px-3 py-2 space-y-2 text-xs text-slate-200"></div>
            <form id="lk-chat-form" class="p-2 border-t border-slate-800 flex gap-2">
                <input id="lk-chat-input" type="text" maxlength="500" autocomplete="off" placeholder="اكتب رسالة…"
                       class="flex-1 rounded-lg bg-slate-800 border border-slate-700 text-slate-100 text-xs px-2.5 py-2 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                <button type="submit" class="lk-btn shrink-0" title="إرسال"><i class="fas fa-paper-plane"></i></button>
            </form>
        </aside>
        @endif
    </div>
    <div class="shrink-0 border-t border-slate-800 bg-slate-900/95 px-4 py-3 flex flex-wrap items-center justify-center gap-2">
        <button type="button" id="lk-toggle-mic" class="lk-btn{{ $lkStartAudio ? '' : ' is-off' }}"><i class="fas fa-microphone"></i> ميكروفون</button>
        <button type="button" id="lk-toggle-cam" class="lk-btn{{ $lkStartVideo ? '' : ' is-off' }}"><i class="fas fa-video"></i> كاميرا</button>
        @if($lkAllowScreenShare)
        <button type="button" id="lk-toggle-screen" class="lk-btn"><i class="fas fa-desktop"></i> مشاركة الشاشة</button>
        @endif
        <a href="{{ $lkLeaveUrl }}" id="lk-leave" class="lk-btn lk-btn-danger"><i class="fas fa-phone-slash"></i> مغادرة</a>
    </div>
</div>
<style>
    .lk-btn{display:inline-flex;align-items:center;gap:.5rem;border-radius:.75rem;background:#1e293b;color:#e2e8f0;padding:.55rem 1rem;font-size:.8rem;font-weight:600;border:1px solid #334155}
    .lk-btn:hover{background:#334155}
    .lk-btn.is-off{background:#7f1d1d;border-color:#991b1b;color:#fecaca}
    .lk-btn.is-sharing{background:#0e7490;border-color:#06b6d4;color:#ecfeff}
    .lk-btn-danger{background:#be123c;border-color:#e11d48;color:#fff}
    .lk-tile{position:relative;background:#0f172a;border:1px solid #1e293b;border-radius:1rem;overflow:hidden;min-height:180px}
    .lk-tile.is-screen{grid-column:1 / -1;min-height:260px}
    .lk-tile video{width:100%;height:100%;object-fit:cover;background:#020617}
    .lk-tile.is-screen video{object-fit:contain;background:#020617}
    .lk-tile-label{position:absolute;left:.75rem;bottom:.75rem;background:rgba(15,23,42,.85);color:#e2e8f0;font-size:.7rem;font-weight:700;padding:.25rem .55rem;border-radius:.5rem}
    .lk-chat-bubble{background:#1e293b;border:1px solid #334155;border-radius:.75rem;padding:.45rem .65rem}
    .lk-chat-bubble strong{display:block;color:#67e8f9;font-size:.65rem;margin-bottom:.15rem}
</style>
<script src="https://cdn.jsdelivr.net/npm/livekit-client@2.9.1/dist/livekit-client.umd.min.js"></script>
<script>
(function () {
    const url = @json($livekitUrl);
    const token = @json($livekitToken);
    const displayName = @json($displayName);
    const role = @json($lkRole);
    const startAudio = @json($lkStartAudio);
    const startVideo = @json($lkStartVideo);
    const allowScreenShare = @json($lkAllowScreenShare);
    const allowChat = @json($lkAllowChat);
    const stage = document.getElementById('lk-stage');
    const statusEl = document.getElementById('lk-status');
    const micBtn = document.getElementById('lk-toggle-mic');
    const camBtn = document.getElementById('lk-toggle-cam');
    const screenBtn = document.getElementById('lk-toggle-screen');
    const chatLog = document.getElementById('lk-chat-log');
    const chatForm = document.getElementById('lk-chat-form');
    const chatInput = document.getElementById('lk-chat-input');

    if (!window.LivekitClient) {
        setStatus('تعذر تحميل مكتبة LiveKit', true);
        return;
    }
    if (!url || !token) {
        setStatus('إعدادات LiveKit غير مكتملة (رابط أو توكن)', true);
        return;
    }

    const {
        Room,
        RoomEvent,
        Track,
        createLocalTracks,
        createLocalScreenTracks,
    } = window.LivekitClient;

    const room = new Room({
        adaptiveStream: true,
        dynacast: true,
        videoCaptureDefaults: { resolution: { width: 1280, height: 720, frameRate: 30 } },
    });
    const tiles = new Map();
    let micOn = !!startAudio;
    let camOn = !!startVideo;
    let screenOn = false;
    let screenTrack = null;
    let connected = false;

    function setStatus(msg, isError) {
        if (!statusEl) return;
        statusEl.textContent = msg;
        statusEl.classList.remove('hidden');
        statusEl.classList.toggle('bg-rose-900/90', !!isError);
        statusEl.classList.toggle('border-rose-700', !!isError);
    }

    function errMsg(err, fallback) {
        if (!err) return fallback;
        const name = err.name || '';
        const message = err.message || String(err);
        if (name === 'NotAllowedError' || /Permission|NotAllowed|denied/i.test(message)) {
            return 'تم رفض الإذن من المتصفح — اسمح بالوصول من شريط العنوان ثم أعد المحاولة';
        }
        if (name === 'NotFoundError') {
            return 'لم يُعثر على جهاز (ميكروفون/كاميرا)';
        }
        if (/AbortError|cancelled|canceled/i.test(name + message)) {
            return 'تم إلغاء مشاركة الشاشة';
        }
        return fallback + (message ? ': ' + message : '');
    }

    function tileKey(participant, source) {
        return participant.identity + ':' + source;
    }

    function ensureTile(participant, source) {
        const key = tileKey(participant, source);
        if (tiles.has(key)) return tiles.get(key);
        const el = document.createElement('div');
        el.className = 'lk-tile' + (source === Track.Source.ScreenShare ? ' is-screen' : '');
        el.dataset.key = key;
        const video = document.createElement('video');
        video.autoplay = true;
        video.playsInline = true;
        video.muted = !!participant.isLocal;
        const label = document.createElement('div');
        label.className = 'lk-tile-label';
        label.textContent = (participant.name || participant.identity)
            + (source === Track.Source.ScreenShare ? ' · شاشة' : '');
        el.appendChild(video);
        el.appendChild(label);
        stage.appendChild(el);
        const ref = { el, video };
        tiles.set(key, ref);
        return ref;
    }

    function removeTile(participant, source) {
        const key = tileKey(participant, source);
        const ref = tiles.get(key);
        if (!ref) return;
        ref.el.remove();
        tiles.delete(key);
    }

    function attachTrack(track, participant) {
        if (!track) return;
        if (track.kind === Track.Kind.Audio && !participant.isLocal) {
            const audio = track.attach();
            audio.autoplay = true;
            audio.dataset.lkAudio = tileKey(participant, track.source || 'mic');
            document.body.appendChild(audio);
            return;
        }
        if (track.kind !== Track.Kind.Video) return;
        const tile = ensureTile(participant, track.source);
        track.attach(tile.video);
    }

    function detachTrack(track, participant) {
        if (!track) return;
        track.detach().forEach((el) => el.remove());
        if (track.kind === Track.Kind.Video) {
            removeTile(participant, track.source);
        }
    }

    function attachExistingRemoteTracks() {
        room.remoteParticipants.forEach((participant) => {
            participant.trackPublications.forEach((pub) => {
                if (pub.track) attachTrack(pub.track, participant);
            });
        });
        room.localParticipant.trackPublications.forEach((pub) => {
            if (pub.track) attachTrack(pub.track, room.localParticipant);
        });
    }

    function clearParticipantTiles(participant) {
        [...tiles.keys()].forEach((key) => {
            if (key.startsWith(participant.identity + ':')) {
                const ref = tiles.get(key);
                if (ref) ref.el.remove();
                tiles.delete(key);
            }
        });
        document.querySelectorAll('audio[data-lk-audio^="' + participant.identity + ':"]').forEach((el) => el.remove());
    }

    function appendChat(name, text, isLocal) {
        if (!chatLog) return;
        const bubble = document.createElement('div');
        bubble.className = 'lk-chat-bubble';
        if (isLocal) bubble.style.borderColor = '#0e7490';
        bubble.innerHTML = '<strong></strong><span></span>';
        bubble.querySelector('strong').textContent = name || 'مشارك';
        bubble.querySelector('span').textContent = text;
        chatLog.appendChild(bubble);
        chatLog.scrollTop = chatLog.scrollHeight;
    }

    room
        .on(RoomEvent.TrackSubscribed, (track, publication, participant) => attachTrack(track, participant))
        .on(RoomEvent.TrackUnsubscribed, (track, publication, participant) => detachTrack(track, participant))
        .on(RoomEvent.LocalTrackPublished, (publication, participant) => {
            if (publication.track) attachTrack(publication.track, participant);
        })
        .on(RoomEvent.LocalTrackUnpublished, (publication, participant) => {
            if (publication.track) detachTrack(publication.track, participant);
            if (publication.source === Track.Source.ScreenShare) {
                screenOn = false;
                screenTrack = null;
                screenBtn?.classList.remove('is-sharing');
            }
        })
        .on(RoomEvent.ParticipantDisconnected, (participant) => clearParticipantTiles(participant))
        .on(RoomEvent.Disconnected, () => {
            connected = false;
            setStatus('تم قطع الاتصال بالغرفة', true);
        })
        .on(RoomEvent.DataReceived, (payload, participant) => {
            if (!allowChat) return;
            try {
                const raw = new TextDecoder().decode(payload);
                const data = JSON.parse(raw);
                if (data && data.type === 'chat' && data.text) {
                    appendChat(data.name || participant?.name || participant?.identity || 'مشارك', String(data.text).slice(0, 500), false);
                }
            } catch (e) {}
        });

    async function connect() {
        try {
            setStatus('جارٍ الاتصال بـ LiveKit…');
            await room.connect(url, token);
            connected = true;
            attachExistingRemoteTracks();

            try {
                const wantAudio = !!startAudio;
                const wantVideo = !!startVideo;
                if (wantAudio || wantVideo) {
                    const localTracks = await createLocalTracks({ audio: wantAudio, video: wantVideo });
                    await Promise.all(localTracks.map((t) => room.localParticipant.publishTrack(t)));
                    localTracks.forEach((t) => attachTrack(t, room.localParticipant));
                }
                micOn = wantAudio;
                camOn = wantVideo;
                micBtn?.classList.toggle('is-off', !micOn);
                camBtn?.classList.toggle('is-off', !camOn);
                setStatus('متصل · ' + (role === 'host' ? 'مضيف' : 'مشارك') + ' · ' + displayName);
                setTimeout(() => statusEl?.classList.add('hidden'), 2500);
            } catch (mediaErr) {
                console.warn('Local media unavailable, joining without mic/cam', mediaErr);
                micOn = false;
                camOn = false;
                micBtn?.classList.add('is-off');
                camBtn?.classList.add('is-off');
                setStatus('متصل بدون ميكروفون/كاميرا — فعّل الأذونات من الأزرار', true);
            }
        } catch (err) {
            console.error(err);
            setStatus(errMsg(err, 'فشل الاتصال بـ LiveKit'), true);
        }
    }

    micBtn?.addEventListener('click', async () => {
        if (!connected) return;
        try {
            const next = !micOn;
            await room.localParticipant.setMicrophoneEnabled(next);
            micOn = next;
            micBtn.classList.toggle('is-off', !micOn);
        } catch (e) {
            setStatus(errMsg(e, 'تعذر تفعيل الميكروفون'), true);
        }
    });

    camBtn?.addEventListener('click', async () => {
        if (!connected) return;
        try {
            const next = !camOn;
            await room.localParticipant.setCameraEnabled(next);
            camOn = next;
            camBtn.classList.toggle('is-off', !camOn);
        } catch (e) {
            setStatus(errMsg(e, 'تعذر تفعيل الكاميرا'), true);
        }
    });

    async function stopScreenShare() {
        try {
            if (typeof room.localParticipant.setScreenShareEnabled === 'function') {
                await room.localParticipant.setScreenShareEnabled(false);
            }
        } catch (e) {}
        if (screenTrack) {
            try {
                await room.localParticipant.unpublishTrack(screenTrack);
            } catch (e) {}
            try { screenTrack.stop(); } catch (e) {}
            screenTrack = null;
        }
        screenOn = false;
        screenBtn?.classList.remove('is-sharing');
    }

    async function startScreenShare() {
        // API الحديث أولاً
        if (typeof room.localParticipant.setScreenShareEnabled === 'function') {
            await room.localParticipant.setScreenShareEnabled(true, { audio: true });
            screenOn = true;
            screenBtn?.classList.add('is-sharing');
            return;
        }
        let tracks;
        try {
            tracks = await createLocalScreenTracks({ audio: true });
        } catch (e) {
            tracks = await createLocalScreenTracks({ audio: false });
        }
        screenTrack = tracks[0];
        await room.localParticipant.publishTrack(screenTrack);
        if (tracks[1]) {
            try { await room.localParticipant.publishTrack(tracks[1]); } catch (e) {}
        }
        attachTrack(screenTrack, room.localParticipant);
        screenOn = true;
        screenBtn?.classList.add('is-sharing');
    }

    screenBtn?.addEventListener('click', async () => {
        if (!connected || !allowScreenShare) return;
        try {
            if (screenOn) {
                await stopScreenShare();
                return;
            }
            await startScreenShare();
            setStatus('جارٍ مشاركة الشاشة');
            setTimeout(() => statusEl?.classList.add('hidden'), 2000);
        } catch (err) {
            console.error(err);
            screenOn = false;
            screenBtn?.classList.remove('is-sharing');
            setStatus(errMsg(err, 'تعذر مشاركة الشاشة'), true);
        }
    });

    chatForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!connected || !allowChat || !chatInput) return;
        const text = (chatInput.value || '').trim();
        if (!text) return;
        const payload = JSON.stringify({
            type: 'chat',
            text: text.slice(0, 500),
            name: displayName,
            at: Date.now(),
        });
        try {
            const bytes = new TextEncoder().encode(payload);
            await room.localParticipant.publishData(bytes, { reliable: true });
            appendChat(displayName, text, true);
            chatInput.value = '';
        } catch (err) {
            console.error(err);
            setStatus('تعذر إرسال الرسالة', true);
        }
    });

    window.addEventListener('beforeunload', () => {
        try { room.disconnect(); } catch (e) {}
    });

    connect();
})();
</script>
