
<?php
    $lkRole = $lkRole ?? 'participant';
    $lkLeaveUrl = $lkLeaveUrl ?? url('/');
    $displayName = $user->name ?? ('User #'.($user->id ?? ''));
    $lkStartAudio = $lkStartAudio ?? true;
    $lkStartVideo = $lkStartVideo ?? true;
    $lkAllowScreenShare = $lkAllowScreenShare ?? true;
    $lkTheme = $lkTheme ?? 'default';
    $lkHideLeave = $lkHideLeave ?? false;
?>
<div id="lk-room-shell" class="lk-room lk-theme-<?php echo e($lkTheme); ?> relative flex-1 min-h-0 flex flex-col" data-lk-theme="<?php echo e($lkTheme); ?>" data-lk-role="<?php echo e($lkRole); ?>">
    <div id="lk-status" class="lk-status hidden" role="status"></div>

    <div class="lk-body flex-1 min-h-0 flex flex-col md:flex-row">
        <div class="lk-main flex-1 min-h-0 flex flex-col relative">
            <div id="lk-focus" class="lk-focus hidden" aria-live="polite">
                <div class="lk-focus__viewport" id="lk-focus-viewport">
                    <div class="lk-focus__scaler" id="lk-focus-scaler">
                        <video id="lk-focus-video" autoplay playsinline></video>
                    </div>
                </div>
                <div class="lk-focus__bar">
                    <span class="lk-focus__title" id="lk-focus-title">مشاركة الشاشة</span>
                    <div class="lk-focus__zoom">
                        <button type="button" id="lk-os-pip" class="lk-icon-btn" title="نافذة عائمة فوق التبويبات والتطبيقات"><i class="fas fa-external-link-alt"></i></button>
                        <button type="button" id="lk-zoom-out" class="lk-icon-btn" title="تصغير"><i class="fas fa-search-minus"></i></button>
                        <span id="lk-zoom-label" class="lk-zoom-label">100%</span>
                        <button type="button" id="lk-zoom-in" class="lk-icon-btn" title="تكبير"><i class="fas fa-search-plus"></i></button>
                        <button type="button" id="lk-zoom-reset" class="lk-icon-btn" title="إعادة"><i class="fas fa-compress"></i></button>
                        <button type="button" id="lk-zoom-fit" class="lk-icon-btn" title="ملاءمة"><i class="fas fa-expand"></i></button>
                    </div>
                </div>
            </div>
            <div id="lk-stage" class="lk-stage flex-1 min-h-0"></div>

            
            <div id="lk-pip" class="lk-pip hidden" aria-label="كاميرات المشاركين">
                <div class="lk-pip__head">
                    <span><i class="fas fa-video"></i> الكاميرات</span>
                    <div class="lk-pip__actions">
                        <button type="button" id="lk-pip-os" class="lk-icon-btn" title="نافذة عائمة فوق التبويبات"><i class="fas fa-external-link-alt"></i></button>
                        <button type="button" id="lk-pip-toggle" class="lk-icon-btn" title="طي/فتح"><i class="fas fa-chevron-down"></i></button>
                    </div>
                </div>
                <div class="lk-pip__body" id="lk-pip-body"></div>
            </div>
        </div>
    </div>

    <div class="lk-toolbar shrink-0">
        <button type="button" id="lk-toggle-mic" class="lk-btn<?php echo e($lkStartAudio ? '' : ' is-off'); ?>"><i class="fas fa-microphone"></i><span>ميكروفون</span></button>
        <button type="button" id="lk-toggle-cam" class="lk-btn<?php echo e($lkStartVideo ? '' : ' is-off'); ?>"><i class="fas fa-video"></i><span>كاميرا</span></button>
        <?php if($lkAllowScreenShare): ?>
        <button type="button" id="lk-toggle-screen" class="lk-btn"><i class="fas fa-desktop"></i><span>مشاركة الشاشة</span></button>
        <?php endif; ?>
        <button type="button" id="lk-toggle-os-pip" class="lk-btn" title="نافذة عائمة فوق التبويبات والتطبيقات"><i class="fas fa-external-link-alt"></i><span>عائمة</span></button>
        <?php if (! ($lkHideLeave)): ?>
        <a href="<?php echo e($lkLeaveUrl); ?>" id="lk-leave" class="lk-btn lk-btn--danger"><i class="fas fa-phone-slash"></i><span>مغادرة</span></a>
        <?php endif; ?>
    </div>
</div>

<style>
/* ─── Base room ─── */
.lk-room{--lk-bg:#0b1220;--lk-surface:#111827;--lk-panel:#0f172a;--lk-line:#1e293b;--lk-text:#e2e8f0;--lk-muted:#94a3b8;--lk-accent:#0B3D91;--lk-gold:#F5B800;--lk-danger:#ef4444;--lk-ok:#22c55e;background:var(--lk-bg);color:var(--lk-text)}
.lk-theme-instructor{--lk-bg:#0c0c0c;--lk-surface:#181818;--lk-panel:#141414;--lk-line:rgba(255,255,255,.1);--lk-text:#f5f5f5;--lk-muted:rgba(245,245,245,.45);--lk-accent:#95a4fc;--lk-gold:#ffcb9a;--lk-danger:#ef4444;font-family:"Inter","Cairo","IBM Plex Sans Arabic",system-ui,sans-serif}
.lk-theme-student{--lk-bg:#071226;--lk-surface:#0b1a33;--lk-panel:#0f2447;--lk-line:rgba(255,255,255,.12);--lk-text:#f8fafc;--lk-muted:#a8b3c7;--lk-accent:#0B3D91;--lk-gold:#F5B800;font-family:"Cairo","Tajawal",system-ui,sans-serif}
.lk-status{position:absolute;top:.75rem;left:50%;transform:translateX(-50%);z-index:30;padding:.4rem 1rem;border-radius:999px;background:rgba(15,23,42,.92);border:1px solid var(--lk-line);font-size:.75rem;font-weight:700}
.lk-status.is-error{background:rgba(127,29,29,.92);border-color:#991b1b;color:#fecaca}
.lk-body{min-height:0}
.lk-main{min-height:0;background:var(--lk-surface)}
.lk-stage{
  display:grid;
  flex:1;
  min-height:0;
  gap:.45rem;
  padding:.45rem;
  overflow:hidden;
  align-content:stretch;
  height:100%;
}
.lk-stage.layout-solo{grid-template-columns:1fr;grid-template-rows:1fr}
.lk-stage.layout-duo{grid-template-columns:1fr 1fr;grid-template-rows:1fr}
.lk-stage.layout-trio{grid-template-columns:1fr 1fr;grid-template-rows:1fr 1fr}
.lk-stage.layout-class{grid-template-columns:1fr 1fr;grid-auto-rows:1fr}
.lk-stage.layout-grid{grid-template-columns:repeat(auto-fit,minmax(220px,1fr));grid-auto-rows:minmax(160px,1fr);overflow:auto;align-content:start}
.lk-room.is-screen-focus .lk-stage{display:none}
.lk-room.is-screen-focus #lk-focus{display:flex}
.lk-focus{display:none;flex-direction:column;flex:1;min-height:0;background:#020617}
.lk-focus__viewport{flex:1;min-height:0;overflow:auto;position:relative;cursor:grab;background:
  radial-gradient(circle at 20% 20%, rgba(11,61,145,.18), transparent 45%),
  radial-gradient(circle at 80% 0%, rgba(245,184,0,.12), transparent 40%),
  #020617}
.lk-focus__viewport.is-dragging{cursor:grabbing}
.lk-focus__scaler{transform-origin:center center;transition:transform .12s ease;min-width:100%;min-height:100%;display:flex;align-items:center;justify-content:center;padding:.75rem}
.lk-focus__scaler video{max-width:100%;max-height:calc(100vh - 220px);width:auto;height:auto;object-fit:contain;background:#000;border-radius:12px;box-shadow:0 18px 50px rgba(0,0,0,.45);border:1px solid var(--lk-line)}
.lk-focus__bar{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:.5rem;padding:.55rem .85rem;border-top:1px solid var(--lk-line);background:rgba(15,23,42,.92)}
.lk-focus__title{font-size:.8rem;font-weight:800;color:var(--lk-text)}
.lk-focus__zoom{display:inline-flex;align-items:center;gap:.35rem}
.lk-zoom-label{font-size:.72rem;font-weight:800;min-width:3.2rem;text-align:center;color:var(--lk-muted)}
.lk-icon-btn{display:inline-flex;align-items:center;justify-content:center;width:2rem;height:2rem;border-radius:8px;border:1px solid var(--lk-line);background:var(--lk-panel);color:var(--lk-text);cursor:pointer}
.lk-icon-btn:hover{border-color:var(--lk-accent);color:var(--lk-gold)}

/* tiles */
.lk-tile{position:relative;background:var(--lk-panel);border:1px solid var(--lk-line);border-radius:14px;overflow:hidden;min-height:0;width:100%;height:100%}
.lk-stage.layout-solo .lk-tile,
.lk-stage.layout-duo .lk-tile,
.lk-stage.layout-trio .lk-tile,
.lk-stage.layout-class .lk-tile{aspect-ratio:unset;min-height:0}
.lk-tile video{width:100%;height:100%;object-fit:cover;background:#020617}
.lk-tile.is-screen{grid-column:1/-1;min-height:280px;aspect-ratio:auto}
.lk-tile.is-screen video{object-fit:contain;background:#000}
.lk-tile-label{position:absolute;inset-inline-start:.65rem;bottom:.65rem;background:rgba(2,6,23,.82);color:var(--lk-text);font-size:.68rem;font-weight:800;padding:.2rem .5rem;border-radius:.45rem;z-index:2}
.lk-tile.is-local{outline:2px solid color-mix(in srgb, var(--lk-accent) 55%, transparent)}
.lk-tile.is-host{outline:2px solid color-mix(in srgb, var(--lk-gold) 70%, transparent)}
.lk-tile.is-host .lk-tile-label::after{content:' · مضيف';color:var(--lk-gold)}

/* floating pip — fixed داخل الصفحة + Document PiP للتبويبات/الجهاز */
.lk-pip{position:fixed;z-index:99990;inset-inline-end:12px;bottom:calc(72px + env(safe-area-inset-bottom,0px));width:min(280px,46vw);border-radius:16px;border:1px solid var(--lk-line);background:rgba(15,23,42,.94);backdrop-filter:blur(10px);box-shadow:0 16px 40px rgba(0,0,0,.35);overflow:hidden}
.lk-pip.hidden{display:none!important}
.lk-pip.is-collapsed .lk-pip__body{display:none}
.lk-pip__head{display:flex;align-items:center;justify-content:space-between;gap:.5rem;padding:.45rem .65rem;font-size:.72rem;font-weight:800;border-bottom:1px solid var(--lk-line);cursor:move;user-select:none}
.lk-pip__actions{display:inline-flex;align-items:center;gap:.25rem}
.lk-pip__body{display:grid;grid-template-columns:1fr 1fr;gap:.35rem;padding:.45rem;max-height:220px;overflow:auto}
.lk-pip-tile{position:relative;border-radius:10px;overflow:hidden;background:#000;border:1px solid var(--lk-line);aspect-ratio:4/3}
.lk-pip-tile video{width:100%;height:100%;object-fit:cover}
.lk-pip-tile span{position:absolute;inset-inline-start:.3rem;bottom:.3rem;font-size:.58rem;font-weight:800;background:rgba(0,0,0,.7);padding:.1rem .3rem;border-radius:.3rem}
.lk-btn.is-os-pip{background:color-mix(in srgb, var(--lk-gold) 28%, var(--lk-surface));border-color:var(--lk-gold);color:#fff}
.lk-os-pip-shell{position:fixed;inset:0;background:#020617;color:var(--lk-text);display:flex;flex-direction:column;overflow:hidden;font-family:inherit}
.lk-os-pip-shell .lk-focus{flex:1;min-height:0;display:flex!important}
.lk-os-pip-shell .lk-focus__viewport{flex:1}
.lk-os-pip-shell .lk-pip{position:relative;inset:auto;bottom:auto;right:auto;width:100%;max-height:38vh;border-radius:0;border-inline:0;border-bottom:0;box-shadow:none}
.lk-os-pip-shell .lk-pip__head{cursor:default}
.lk-os-pip-compact{flex:1;display:flex;align-items:center;justify-content:center;padding:.5rem;background:#020617}
.lk-os-pip-compact video{max-width:100%;max-height:100%;object-fit:contain;border-radius:12px;background:#000}

/* toolbar */
.lk-toolbar{display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:.45rem;padding:.7rem .85rem;border-top:1px solid var(--lk-line);background:color-mix(in srgb, var(--lk-panel) 92%, #000)}
.lk-btn{display:inline-flex;align-items:center;gap:.45rem;border-radius:999px;background:var(--lk-surface);color:var(--lk-text);padding:.55rem 1rem;font-size:.78rem;font-weight:800;border:1px solid var(--lk-line);cursor:pointer;text-decoration:none}
.lk-btn:hover{border-color:var(--lk-accent)}
.lk-btn.is-sharing{background:color-mix(in srgb, var(--lk-accent) 35%, var(--lk-surface));border-color:var(--lk-accent);color:#fff}
.lk-btn--danger{background:var(--lk-danger);border-color:var(--lk-danger);color:#fff}
.lk-btn--accent{background:var(--lk-accent);border-color:var(--lk-accent);color:#fff}
.lk-theme-student .lk-btn--accent,.lk-theme-student .lk-btn.is-sharing{background:linear-gradient(135deg,#0B3D91,#0997d9);border:0}
.lk-theme-instructor .lk-btn{border-radius:12px}
@media(max-width:640px){.lk-btn span{display:none}.lk-pip{width:min(200px,52vw)}}
</style>

<script src="https://cdn.jsdelivr.net/npm/livekit-client@2.9.1/dist/livekit-client.umd.min.js"></script>
<script>
(function () {
    const url = <?php echo json_encode($livekitUrl, 15, 512) ?>;
    const token = <?php echo json_encode($livekitToken, 15, 512) ?>;
    const displayName = <?php echo json_encode($displayName, 15, 512) ?>;
    const role = <?php echo json_encode($lkRole, 15, 512) ?>;
    const startAudio = <?php echo json_encode($lkStartAudio, 15, 512) ?>;
    const startVideo = <?php echo json_encode($lkStartVideo, 15, 512) ?>;
    const allowScreenShare = <?php echo json_encode($lkAllowScreenShare, 15, 512) ?>;
    const shell = document.getElementById('lk-room-shell');
    const stage = document.getElementById('lk-stage');
    const focusBox = document.getElementById('lk-focus');
    const focusVideo = document.getElementById('lk-focus-video');
    const focusScaler = document.getElementById('lk-focus-scaler');
    const focusViewport = document.getElementById('lk-focus-viewport');
    const focusTitle = document.getElementById('lk-focus-title');
    const pip = document.getElementById('lk-pip');
    const pipBody = document.getElementById('lk-pip-body');
    const statusEl = document.getElementById('lk-status');
    const micBtn = document.getElementById('lk-toggle-mic');
    const camBtn = document.getElementById('lk-toggle-cam');
    const screenBtn = document.getElementById('lk-toggle-screen');
    const zoomLabel = document.getElementById('lk-zoom-label');

    function setStatus(msg, isError) {
        if (!statusEl) return;
        statusEl.textContent = msg;
        statusEl.classList.remove('hidden');
        statusEl.classList.toggle('is-error', !!isError);
    }
    function hideStatusSoon() {
        setTimeout(() => statusEl?.classList.add('hidden'), 2200);
    }

    if (!window.LivekitClient) { setStatus('تعذر تحميل مكتبة LiveKit', true); return; }
    if (!url || !token) { setStatus('إعدادات LiveKit غير مكتملة (رابط أو توكن)', true); return; }

    const { Room, RoomEvent, Track, createLocalTracks, createLocalScreenTracks } = window.LivekitClient;
    const room = new Room({
        adaptiveStream: true,
        dynacast: true,
        videoCaptureDefaults: { resolution: { width: 1280, height: 720, frameRate: 30 } },
    });

    const tiles = new Map();
    const pipTiles = new Map();
    let micOn = !!startAudio;
    let camOn = !!startVideo;
    let screenOn = false;
    let screenTrack = null;
    let focusTrack = null;
    let connected = false;
    let zoom = 1;
    const ZOOM_MIN = 0.75;
    const ZOOM_MAX = 3;
    const ZOOM_STEP = 0.25;

    function errMsg(err, fallback) {
        if (!err) return fallback;
        const name = err.name || '';
        const message = err.message || String(err);
        if (name === 'NotAllowedError' || /Permission|NotAllowed|denied/i.test(message)) {
            return 'تم رفض الإذن من المتصفح — اسمح بالوصول ثم أعد المحاولة';
        }
        if (/AbortError|cancelled|canceled/i.test(name + message)) return 'تم إلغاء مشاركة الشاشة';
        return fallback + (message ? ': ' + message : '');
    }
    function tileKey(participant, source) {
        return participant.identity + ':' + source;
    }

    function parseParticipantMeta(participant) {
        if (!participant?.metadata) return {};
        try { return JSON.parse(participant.metadata); } catch (e) { return {}; }
    }

    function isHostParticipant(participant) {
        if (!participant) return false;
        if (participant.isLocal && role === 'host') return true;
        const meta = parseParticipantMeta(participant);
        const r = String(meta.role || '').toLowerCase();
        if (meta.is_host === true || meta.is_host === 'true') return true;
        return r === 'instructor' || r === 'admin' || r === 'teacher';
    }

    function cameraTiles() {
        return [...tiles.values()].filter((ref) => {
            return ref.source !== Track.Source.ScreenShare
                && ref.el.style.display !== 'none'
                && ref.source !== Track.Source.ScreenShareAudio;
        });
    }

    function findHostTile(list) {
        const host = list.find((t) => isHostParticipant(t.participant));
        if (host) return host;
        if (role === 'host') {
            const local = list.find((t) => t.participant.isLocal);
            if (local) return local;
        }
        return list[0];
    }

    function resetTilePlacement(ref) {
        ref.el.classList.remove('is-host');
        ref.el.style.gridColumn = '';
        ref.el.style.gridRow = '';
    }

    function updateStageLayout() {
        if (!stage || shell?.classList.contains('is-screen-focus')) return;

        const list = cameraTiles();
        const layouts = ['layout-solo', 'layout-duo', 'layout-trio', 'layout-class', 'layout-grid'];
        stage.classList.remove(...layouts);
        list.forEach(resetTilePlacement);

        const n = list.length;
        if (n === 0) return;

        if (n === 1) {
            stage.classList.add('layout-solo');
            return;
        }

        if (n === 2) {
            stage.classList.add('layout-duo');
            return;
        }

        if (n === 3) {
            stage.classList.add('layout-trio');
            const hostTile = findHostTile(list);
            const others = list.filter((t) => t !== hostTile);
            hostTile.el.classList.add('is-host');
            hostTile.el.style.gridColumn = '1';
            hostTile.el.style.gridRow = '1 / -1';
            if (others[0]) {
                others[0].el.style.gridColumn = '2';
                others[0].el.style.gridRow = '1';
            }
            if (others[1]) {
                others[1].el.style.gridColumn = '2';
                others[1].el.style.gridRow = '2';
            }
            return;
        }

        // 4+ — المعلم/المضيف نصف الشاشة والباقي عمودياً في النصف الآخر
        stage.classList.add('layout-class');
        const hostTile = findHostTile(list);
        const others = list.filter((t) => t !== hostTile);
        hostTile.el.classList.add('is-host');
        hostTile.el.style.gridColumn = '1';
        hostTile.el.style.gridRow = '1 / -1';
        others.forEach((t, i) => {
            t.el.style.gridColumn = '2';
            t.el.style.gridRow = String(i + 1);
        });
    }

    function applyZoom() {
        if (!focusScaler) return;
        focusScaler.style.transform = 'scale(' + zoom + ')';
        if (zoomLabel) zoomLabel.textContent = Math.round(zoom * 100) + '%';
    }
    function setScreenFocus(on, title) {
        shell?.classList.toggle('is-screen-focus', !!on);
        if (focusBox) focusBox.classList.toggle('hidden', !on);
        if (pip) pip.classList.toggle('hidden', !on);
        if (title && focusTitle) focusTitle.textContent = title;
        if (!on) {
            zoom = 1;
            applyZoom();
            if (focusVideo) {
                focusVideo.srcObject = null;
                focusVideo.removeAttribute('src');
            }
            focusTrack = null;
            if (documentPictureInPicture?.window || osPipActive) {
                closeOsFloatingWindow().catch(() => restoreOsPipDom());
            }
            osPipAutoTried = false;
            updateStageLayout();
        } else {
            rebuildPip();
            maybeAutoOpenOsPip();
        }
    }
    function attachToFocus(track, label) {
        focusTrack = track;
        if (focusVideo) {
            track.attach(focusVideo);
            focusVideo.muted = true;
            focusVideo.playsInline = true;
            focusVideo.play?.().catch(() => {});
        }
        setScreenFocus(true, label || 'مشاركة الشاشة');
        zoom = 1;
        applyZoom();
    }

    function ensureTile(participant, source) {
        const key = tileKey(participant, source);
        if (tiles.has(key)) return tiles.get(key);
        const el = document.createElement('div');
        el.className = 'lk-tile' + (source === Track.Source.ScreenShare ? ' is-screen' : '') + (participant.isLocal ? ' is-local' : '');
        el.dataset.key = key;
        const video = document.createElement('video');
        video.autoplay = true;
        video.playsInline = true;
        video.muted = !!participant.isLocal;
        const label = document.createElement('div');
        label.className = 'lk-tile-label';
        label.textContent = (participant.name || participant.identity) + (source === Track.Source.ScreenShare ? ' · شاشة' : '');
        el.appendChild(video);
        el.appendChild(label);
        stage.appendChild(el);
        const ref = { el, video, participant, source };
        tiles.set(key, ref);
        updateStageLayout();
        return ref;
    }
    function removeTile(participant, source) {
        const key = tileKey(participant, source);
        const ref = tiles.get(key);
        if (!ref) return;
        ref.el.remove();
        tiles.delete(key);
        updateStageLayout();
    }

    function rebuildPip() {
        if (!pipBody) return;
        pipBody.innerHTML = '';
        pipTiles.clear();
        tiles.forEach((ref, key) => {
            if (ref.source === Track.Source.ScreenShare) return;
            const videoEl = ref.video;
            if (!videoEl || !videoEl.srcObject) return;
            const wrap = document.createElement('div');
            wrap.className = 'lk-pip-tile';
            const v = document.createElement('video');
            v.autoplay = true;
            v.playsInline = true;
            v.muted = true;
            v.srcObject = videoEl.srcObject;
            const name = document.createElement('span');
            name.textContent = (ref.participant.name || ref.participant.identity || '').slice(0, 18);
            wrap.appendChild(v);
            wrap.appendChild(name);
            pipBody.appendChild(wrap);
            pipTiles.set(key, wrap);
        });
        if (pip && shell?.classList.contains('is-screen-focus')) {
            pip.classList.toggle('hidden', pipBody.children.length === 0);
        }
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

        if (track.source === Track.Source.ScreenShare) {
            const label = (participant.name || participant.identity) + ' · شاشة';
            attachToFocus(track, label);
            // keep a hidden tile for stream reference / cleanup
            const tile = ensureTile(participant, track.source);
            track.attach(tile.video);
            tile.el.style.display = 'none';
            return;
        }

        const tile = ensureTile(participant, track.source);
        track.attach(tile.video);
        updateStageLayout();
        if (shell?.classList.contains('is-screen-focus')) rebuildPip();
    }

    function detachTrack(track, participant) {
        if (!track) return;
        track.detach().forEach((el) => el.remove());
        if (track.kind === Track.Kind.Video) {
            if (track.source === Track.Source.ScreenShare) {
                if (focusTrack === track || (participant && focusTitle)) {
                    setScreenFocus(false);
                }
            }
            removeTile(participant, track.source);
            updateStageLayout();
            if (shell?.classList.contains('is-screen-focus')) rebuildPip();
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
        updateStageLayout();
        if (shell?.classList.contains('is-screen-focus')) rebuildPip();
    }

    room
        .on(RoomEvent.TrackSubscribed, (track, publication, participant) => attachTrack(track, participant))
        .on(RoomEvent.TrackUnsubscribed, (track, publication, participant) => detachTrack(track, participant))
        .on(RoomEvent.ParticipantConnected, () => updateStageLayout())
        .on(RoomEvent.LocalTrackPublished, (publication, participant) => {
            if (publication.track) attachTrack(publication.track, participant);
        })
        .on(RoomEvent.LocalTrackUnpublished, (publication, participant) => {
            if (publication.track) detachTrack(publication.track, participant);
            if (publication.source === Track.Source.ScreenShare) {
                screenOn = false;
                screenTrack = null;
                screenBtn?.classList.remove('is-sharing');
                setScreenFocus(false);
            }
        })
        .on(RoomEvent.ParticipantDisconnected, (participant) => clearParticipantTiles(participant))
        .on(RoomEvent.Disconnected, () => { connected = false; setStatus('تم قطع الاتصال بالغرفة', true); });

    async function connect() {
        try {
            setStatus('جارٍ الاتصال…');
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
                micOn = wantAudio; camOn = wantVideo;
                micBtn?.classList.toggle('is-off', !micOn);
                camBtn?.classList.toggle('is-off', !camOn);
                setStatus('متصل · ' + (role === 'host' ? 'مضيف' : 'مشارك') + ' · ' + displayName);
                hideStatusSoon();
                updateStageLayout();
            } catch (mediaErr) {
                console.warn(mediaErr);
                micOn = false; camOn = false;
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
        } catch (e) { setStatus(errMsg(e, 'تعذر تفعيل الميكروفون'), true); }
    });
    camBtn?.addEventListener('click', async () => {
        if (!connected) return;
        try {
            const next = !camOn;
            await room.localParticipant.setCameraEnabled(next);
            camOn = next;
            camBtn.classList.toggle('is-off', !camOn);
            updateStageLayout();
            if (shell?.classList.contains('is-screen-focus')) setTimeout(rebuildPip, 200);
        } catch (e) { setStatus(errMsg(e, 'تعذر تفعيل الكاميرا'), true); }
    });

    async function stopScreenShare() {
        try {
            if (typeof room.localParticipant.setScreenShareEnabled === 'function') {
                await room.localParticipant.setScreenShareEnabled(false);
            }
        } catch (e) {}
        if (screenTrack) {
            try { await room.localParticipant.unpublishTrack(screenTrack); } catch (e) {}
            try { screenTrack.stop(); } catch (e) {}
            screenTrack = null;
        }
        screenOn = false;
        screenBtn?.classList.remove('is-sharing');
        setScreenFocus(false);
    }
    async function startScreenShare() {
        if (typeof room.localParticipant.setScreenShareEnabled === 'function') {
            await room.localParticipant.setScreenShareEnabled(true, { audio: true });
            screenOn = true;
            screenBtn?.classList.add('is-sharing');
            return;
        }
        let tracks;
        try { tracks = await createLocalScreenTracks({ audio: true }); }
        catch (e) { tracks = await createLocalScreenTracks({ audio: false }); }
        screenTrack = tracks[0];
        await room.localParticipant.publishTrack(screenTrack);
        if (tracks[1]) { try { await room.localParticipant.publishTrack(tracks[1]); } catch (e) {} }
        attachTrack(screenTrack, room.localParticipant);
        screenOn = true;
        screenBtn?.classList.add('is-sharing');
    }
    screenBtn?.addEventListener('click', async () => {
        if (!connected || !allowScreenShare) return;
        try {
            if (screenOn) { await stopScreenShare(); return; }
            await startScreenShare();
            setStatus('مشاركة الشاشة مفعّلة — استخدم الزووم والنافذة العائمة');
            hideStatusSoon();
        } catch (err) {
            console.error(err);
            screenOn = false;
            screenBtn?.classList.remove('is-sharing');
            setStatus(errMsg(err, 'تعذر مشاركة الشاشة'), true);
        }
    });

    document.getElementById('lk-zoom-in')?.addEventListener('click', () => {
        zoom = Math.min(ZOOM_MAX, zoom + ZOOM_STEP); applyZoom();
    });
    document.getElementById('lk-zoom-out')?.addEventListener('click', () => {
        zoom = Math.max(ZOOM_MIN, zoom - ZOOM_STEP); applyZoom();
    });
    document.getElementById('lk-zoom-reset')?.addEventListener('click', () => {
        zoom = 1; applyZoom();
        if (focusViewport) focusViewport.scrollTo({ left: 0, top: 0 });
    });
    document.getElementById('lk-zoom-fit')?.addEventListener('click', () => {
        zoom = 1; applyZoom();
    });
    document.getElementById('lk-pip-toggle')?.addEventListener('click', () => {
        pip?.classList.toggle('is-collapsed');
    });

    const osPipBtn = document.getElementById('lk-toggle-os-pip');
    const osPipFocusBtn = document.getElementById('lk-os-pip');
    const osPipCamBtn = document.getElementById('lk-pip-os');
    let osPipShell = null;
    let osPipCompact = null;
    let osPipRestore = null;
    let osPipActive = false;
    let osPipAutoTried = false;

    function supportsDocumentPiP() {
        return typeof window.documentPictureInPicture !== 'undefined'
            && typeof window.documentPictureInPicture.requestWindow === 'function';
    }
    function supportsVideoPiP() {
        return typeof HTMLVideoElement !== 'undefined'
            && HTMLVideoElement.prototype.requestPictureInPicture;
    }
    function updateOsPipButtons(active) {
        osPipActive = !!active;
        [osPipBtn, osPipFocusBtn, osPipCamBtn].forEach((btn) => {
            if (!btn) return;
            btn.classList.toggle('is-os-pip', osPipActive);
            btn.title = osPipActive ? 'إغلاق النافذة العائمة' : (btn.title || 'نافذة عائمة فوق التبويبات والتطبيقات');
        });
    }
    function lkMainHost() {
        return shell?.querySelector('.lk-main') || shell;
    }
    function primaryStageVideo() {
        const hostTile = stage?.querySelector('.lk-tile.is-host video');
        if (hostTile?.srcObject) return hostTile;
        const any = stage?.querySelector('.lk-tile video');
        return any?.srcObject ? any : null;
    }
    function injectOsPipStyles(doc) {
        if (!doc) return;
        const style = doc.createElement('style');
        style.textContent = `
            html,body{margin:0;padding:0;width:100%;height:100%;overflow:hidden;background:#020617}
            .lk-os-pip-shell{position:fixed;inset:0;background:#020617;color:#e2e8f0;display:flex;flex-direction:column;font-family:Cairo,Tajawal,system-ui,sans-serif}
            .lk-os-pip-shell .lk-focus{flex:1;min-height:0;display:flex!important;flex-direction:column;background:#020617}
            .lk-os-pip-shell .lk-focus.hidden{display:none!important}
            .lk-os-pip-shell .lk-focus__viewport{flex:1;min-height:0;overflow:auto;background:#020617}
            .lk-os-pip-shell .lk-focus__scaler{display:flex;align-items:center;justify-content:center;min-height:100%;padding:.35rem}
            .lk-os-pip-shell .lk-focus__scaler video{max-width:100%;max-height:100%;object-fit:contain;background:#000;border-radius:8px}
            .lk-os-pip-shell .lk-focus__bar{display:flex;align-items:center;justify-content:space-between;padding:.35rem .55rem;border-top:1px solid #1e293b;background:rgba(15,23,42,.95);font-size:.68rem;font-weight:800}
            .lk-os-pip-shell .lk-pip{position:relative!important;inset:auto!important;width:100%;max-height:38vh;border-radius:0;border:0;border-top:1px solid #1e293b;box-shadow:none;background:rgba(15,23,42,.98)}
            .lk-os-pip-shell .lk-pip.hidden{display:none!important}
            .lk-os-pip-shell .lk-pip__body{max-height:160px}
            .lk-os-pip-compact{flex:1;display:flex;align-items:center;justify-content:center;padding:.35rem;background:#020617}
            .lk-os-pip-compact video{width:100%;height:100%;max-height:100%;object-fit:contain;background:#000;border-radius:8px}
        `;
        doc.head.appendChild(style);
    }
    function restoreOsPipDom() {
        const host = lkMainHost();
        if (!host) return;
        if (osPipRestore) {
            const { focusParent, focusNext, pipParent, pipNext, compactParent } = osPipRestore;
            if (focusBox && focusParent) {
                if (focusNext) focusParent.insertBefore(focusBox, focusNext);
                else focusParent.appendChild(focusBox);
            } else if (focusBox && host && focusBox.parentElement !== host) {
                host.insertBefore(focusBox, stage);
            }
            if (pip && pipParent) {
                if (pipNext) pipParent.insertBefore(pip, pipNext);
                else pipParent.appendChild(pip);
            } else if (pip && host && pip.parentElement !== host) {
                host.appendChild(pip);
            }
            if (osPipCompact && compactParent) compactParent.removeChild(osPipCompact);
            osPipRestore = null;
        }
        osPipShell = null;
        osPipCompact = null;
        updateOsPipButtons(false);
    }
    function buildOsPipCompact() {
        const wrap = document.createElement('div');
        wrap.className = 'lk-os-pip-compact';
        const video = document.createElement('video');
        video.autoplay = true;
        video.playsInline = true;
        video.muted = true;
        const src = (shell?.classList.contains('is-screen-focus') && focusVideo?.srcObject)
            ? focusVideo
            : primaryStageVideo();
        if (src?.srcObject) video.srcObject = src.srcObject;
        wrap.appendChild(video);
        return wrap;
    }
    async function closeOsFloatingWindow() {
        if (documentPictureInPicture?.window) {
            documentPictureInPicture.window.close();
            return;
        }
        if (document.pictureInPictureElement) {
            await document.exitPictureInPicture();
        }
        restoreOsPipDom();
    }
    async function openVideoPiP() {
        const video = (shell?.classList.contains('is-screen-focus') && focusVideo?.srcObject)
            ? focusVideo
            : primaryStageVideo();
        if (!video || !supportsVideoPiP()) {
            setStatus('المتصفح لا يدعم النافذة العائمة — جرّب Chrome أو Edge', true);
            return false;
        }
        if (document.pictureInPictureElement === video) {
            await document.exitPictureInPicture();
            updateOsPipButtons(false);
            return true;
        }
        await video.requestPictureInPicture();
        updateOsPipButtons(true);
        setStatus('النافذة العائمة نشطة — تبقى فوق التبويبات والتطبيقات');
        hideStatusSoon();
        return true;
    }
    async function openDocumentPiP() {
        if (!supportsDocumentPiP()) return openVideoPiP();
        if (documentPictureInPicture.window) {
            documentPictureInPicture.window.focus();
            updateOsPipButtons(true);
            return true;
        }
        const inScreenFocus = shell?.classList.contains('is-screen-focus');
        const pipWindow = await documentPictureInPicture.requestWindow({
            width: inScreenFocus ? 520 : 380,
            height: inScreenFocus ? 360 : 260,
        });
        injectOsPipStyles(pipWindow.document);
        osPipShell = pipWindow.document.createElement('div');
        osPipShell.className = 'lk-os-pip-shell';
        pipWindow.document.body.appendChild(osPipShell);

        osPipRestore = {
            focusParent: focusBox?.parentElement || null,
            focusNext: focusBox?.nextSibling || null,
            pipParent: pip?.parentElement || null,
            pipNext: pip?.nextSibling || null,
            compactParent: null,
        };

        if (inScreenFocus && focusBox) {
            focusBox.classList.remove('hidden');
            osPipShell.appendChild(focusBox);
            if (pip && pipBody?.children.length) osPipShell.appendChild(pip);
        } else {
            osPipCompact = buildOsPipCompact();
            osPipRestore.compactParent = osPipShell;
            osPipShell.appendChild(osPipCompact);
            if (pip && pipBody?.children.length) osPipShell.appendChild(pip);
        }

        pipWindow.addEventListener('pagehide', restoreOsPipDom, { once: true });
        updateOsPipButtons(true);
        setStatus('النافذة العائمة نشطة — تبقى فوق التبويبات والتطبيقات');
        hideStatusSoon();
        return true;
    }
    async function toggleOsFloatingWindow() {
        try {
            if (osPipActive || documentPictureInPicture?.window || document.pictureInPictureElement) {
                await closeOsFloatingWindow();
                return;
            }
            await openDocumentPiP();
        } catch (err) {
            if (err?.name === 'NotAllowedError') {
                setStatus('اسمح للموقع بفتح النافذة العائمة من إعدادات المتصفح', true);
                return;
            }
            console.warn(err);
            try { await openVideoPiP(); } catch (e2) {
                setStatus(errMsg(e2, 'تعذر فتح النافذة العائمة'), true);
            }
        }
    }
    function maybeAutoOpenOsPip() {
        if (osPipAutoTried || osPipActive) return;
        if (!supportsDocumentPiP() && !supportsVideoPiP()) return;
        osPipAutoTried = true;
        openDocumentPiP().catch(() => {});
    }
    osPipBtn?.addEventListener('click', toggleOsFloatingWindow);
    osPipFocusBtn?.addEventListener('click', toggleOsFloatingWindow);
    osPipCamBtn?.addEventListener('click', toggleOsFloatingWindow);
    document.addEventListener('leavepictureinpicture', () => updateOsPipButtons(false));
    if (documentPictureInPicture) {
        documentPictureInPicture.addEventListener('enter', () => updateOsPipButtons(true));
    }

    // drag focus viewport while zoomed
    if (focusViewport) {
        let dragging = false, sx = 0, sy = 0, sl = 0, st = 0;
        focusViewport.addEventListener('mousedown', (e) => {
            if (zoom <= 1) return;
            dragging = true;
            focusViewport.classList.add('is-dragging');
            sx = e.clientX; sy = e.clientY;
            sl = focusViewport.scrollLeft; st = focusViewport.scrollTop;
        });
        window.addEventListener('mousemove', (e) => {
            if (!dragging) return;
            focusViewport.scrollLeft = sl - (e.clientX - sx);
            focusViewport.scrollTop = st - (e.clientY - sy);
        });
        window.addEventListener('mouseup', () => {
            dragging = false;
            focusViewport.classList.remove('is-dragging');
        });
        focusViewport.addEventListener('wheel', (e) => {
            if (!e.ctrlKey && !e.metaKey) return;
            e.preventDefault();
            zoom = Math.min(ZOOM_MAX, Math.max(ZOOM_MIN, zoom + (e.deltaY < 0 ? ZOOM_STEP : -ZOOM_STEP)));
            applyZoom();
        }, { passive: false });
    }

    // draggable pip (fixed — يتحرك داخل نافذة المتصفح)
    if (pip) {
        const head = pip.querySelector('.lk-pip__head');
        let drag = false, ox = 0, oy = 0;
        head?.addEventListener('mousedown', (e) => {
            if (e.target.closest('button')) return;
            drag = true;
            const r = pip.getBoundingClientRect();
            ox = e.clientX - r.left;
            oy = e.clientY - r.top;
            e.preventDefault();
        });
        window.addEventListener('mousemove', (e) => {
            if (!drag) return;
            let left = e.clientX - ox;
            let top = e.clientY - oy;
            left = Math.max(8, Math.min(window.innerWidth - pip.offsetWidth - 8, left));
            top = Math.max(8, Math.min(window.innerHeight - pip.offsetHeight - 8, top));
            pip.style.left = left + 'px';
            pip.style.top = top + 'px';
            pip.style.right = 'auto';
            pip.style.bottom = 'auto';
            pip.style.insetInlineEnd = 'auto';
        });
        window.addEventListener('mouseup', () => { drag = false; });
    }

    window.addEventListener('beforeunload', () => {
        try { if (documentPictureInPicture?.window) documentPictureInPicture.window.close(); } catch (e) {}
        try { room.disconnect(); } catch (e) {}
    });
    window.__mxLkLeaveRoom = function () {
        try { room.disconnect(); } catch (e) {}
    };
    connect();
})();
</script>
<?php /**PATH /Users/cityphone/Documents/glottical/resources/views/partials/livekit-room.blade.php ENDPATH**/ ?>