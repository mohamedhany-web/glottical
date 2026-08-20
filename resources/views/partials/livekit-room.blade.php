{{-- غرفة LiveKit مضمّنة — يتطلب $livekitUrl و $livekitToken و $user --}}
@php
    $lkRole = $lkRole ?? 'participant';
    $lkLeaveUrl = $lkLeaveUrl ?? url('/');
    $displayName = $user->name ?? ('User #'.($user->id ?? ''));
    $lkStartAudio = $lkStartAudio ?? true;
    $lkStartVideo = $lkStartVideo ?? true;
@endphp
<div id="lk-room-shell" class="relative flex-1 min-h-0 flex flex-col bg-slate-950">
    <div id="lk-status" class="absolute top-3 left-1/2 -translate-x-1/2 z-20 px-4 py-1.5 rounded-full bg-slate-900/90 text-slate-200 text-xs font-semibold border border-slate-700 hidden"></div>
    <div id="lk-stage" class="flex-1 min-h-0 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-2 p-2 overflow-auto"></div>
    <div class="shrink-0 border-t border-slate-800 bg-slate-900/95 px-4 py-3 flex flex-wrap items-center justify-center gap-2">
        <button type="button" id="lk-toggle-mic" class="lk-btn{{ $lkStartAudio ? '' : ' is-off' }}"><i class="fas fa-microphone"></i> ميكروفون</button>
        <button type="button" id="lk-toggle-cam" class="lk-btn{{ $lkStartVideo ? '' : ' is-off' }}"><i class="fas fa-video"></i> كاميرا</button>
        <button type="button" id="lk-toggle-screen" class="lk-btn"><i class="fas fa-desktop"></i> شاشة</button>
        <a href="{{ $lkLeaveUrl }}" id="lk-leave" class="lk-btn lk-btn-danger"><i class="fas fa-phone-slash"></i> مغادرة</a>
    </div>
</div>
<style>
    .lk-btn{display:inline-flex;align-items:center;gap:.5rem;border-radius:.75rem;background:#1e293b;color:#e2e8f0;padding:.55rem 1rem;font-size:.8rem;font-weight:600;border:1px solid #334155}
    .lk-btn:hover{background:#334155}
    .lk-btn.is-off{background:#7f1d1d;border-color:#991b1b;color:#fecaca}
    .lk-btn-danger{background:#be123c;border-color:#e11d48;color:#fff}
    .lk-tile{position:relative;background:#0f172a;border:1px solid #1e293b;border-radius:1rem;overflow:hidden;min-height:180px}
    .lk-tile video{width:100%;height:100%;object-fit:cover;background:#020617}
    .lk-tile-label{position:absolute;left:.75rem;bottom:.75rem;background:rgba(15,23,42,.85);color:#e2e8f0;font-size:.7rem;font-weight:700;padding:.25rem .55rem;border-radius:.5rem}
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
    const stage = document.getElementById('lk-stage');
    const statusEl = document.getElementById('lk-status');
    const micBtn = document.getElementById('lk-toggle-mic');
    const camBtn = document.getElementById('lk-toggle-cam');
    const screenBtn = document.getElementById('lk-toggle-screen');

    if (!window.LivekitClient) {
        setStatus('تعذر تحميل مكتبة LiveKit', true);
        return;
    }
    if (!url || !token) {
        setStatus('إعدادات LiveKit غير مكتملة (رابط أو توكن)', true);
        return;
    }

    const { Room, RoomEvent, Track, createLocalTracks, createLocalScreenTracks } = window.LivekitClient;
    const room = new Room({ adaptiveStream: true, dynacast: true });
    const tiles = new Map();
    let micOn = !!startAudio;
    let camOn = !!startVideo;
    let screenTrack = null;

    function setStatus(msg, isError) {
        statusEl.textContent = msg;
        statusEl.classList.remove('hidden');
        statusEl.classList.toggle('bg-rose-900/90', !!isError);
        statusEl.classList.toggle('border-rose-700', !!isError);
    }

    function tileKey(participant, source) {
        return participant.identity + ':' + source;
    }

    function ensureTile(participant, source) {
        const key = tileKey(participant, source);
        if (tiles.has(key)) return tiles.get(key);
        const el = document.createElement('div');
        el.className = 'lk-tile';
        el.dataset.key = key;
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
        if (track.kind === Track.Kind.Audio && !participant.isLocal) {
            const audio = track.attach();
            audio.autoplay = true;
            document.body.appendChild(audio);
            return;
        }
        if (track.kind !== Track.Kind.Video) return;
        const tile = ensureTile(participant, track.source);
        track.attach(tile.video);
    }

    function detachTrack(track, participant) {
        track.detach().forEach((el) => el.remove());
        if (track.kind === Track.Kind.Video) {
            removeTile(participant, track.source);
        }
    }

    room
        .on(RoomEvent.TrackSubscribed, (track, publication, participant) => attachTrack(track, participant))
        .on(RoomEvent.TrackUnsubscribed, (track, publication, participant) => detachTrack(track, participant))
        .on(RoomEvent.LocalTrackPublished, (publication, participant) => {
            if (publication.track) attachTrack(publication.track, participant);
        })
        .on(RoomEvent.LocalTrackUnpublished, (publication, participant) => {
            if (publication.track) detachTrack(publication.track, participant);
        })
        .on(RoomEvent.Disconnected, () => setStatus('تم قطع الاتصال بالغرفة', true));

    async function connect() {
        try {
            setStatus('جارٍ الاتصال بـ LiveKit…');
            await room.connect(url, token);
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
                setTimeout(() => statusEl.classList.add('hidden'), 2500);
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
            setStatus('فشل الاتصال بـ LiveKit: ' + (err && err.message ? err.message : err), true);
        }
    }

    micBtn?.addEventListener('click', async () => {
        try {
            micOn = !micOn;
            await room.localParticipant.setMicrophoneEnabled(micOn);
            micBtn.classList.toggle('is-off', !micOn);
        } catch (e) {
            micOn = !micOn;
            setStatus('تعذر تفعيل الميكروفون', true);
        }
    });
    camBtn?.addEventListener('click', async () => {
        try {
            camOn = !camOn;
            await room.localParticipant.setCameraEnabled(camOn);
            camBtn.classList.toggle('is-off', !camOn);
        } catch (e) {
            camOn = !camOn;
            setStatus('تعذر تفعيل الكاميرا', true);
        }
    });
    screenBtn?.addEventListener('click', async () => {
        try {
            if (screenTrack) {
                await room.localParticipant.unpublishTrack(screenTrack);
                screenTrack.stop();
                screenTrack = null;
                screenBtn.classList.remove('is-off');
                return;
            }
            const tracks = await createLocalScreenTracks({ audio: false });
            screenTrack = tracks[0];
            await room.localParticipant.publishTrack(screenTrack);
            attachTrack(screenTrack, room.localParticipant);
            screenBtn.classList.add('is-off');
        } catch (err) {
            console.error(err);
            setStatus('تعذر مشاركة الشاشة', true);
        }
    });

    connect();
})();
</script>
