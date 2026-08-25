{{-- غرفة LiveKit — $livekitUrl + $livekitToken + $user — اختياري: $lkTheme instructor|student --}}
@php
    $lkRole = $lkRole ?? 'participant';
    $lkLeaveUrl = $lkLeaveUrl ?? url('/');
    $displayName = $user->name ?? ('User #'.($user->id ?? ''));
    $lkStartAudio = $lkStartAudio ?? true;
    $lkStartVideo = $lkStartVideo ?? true;
    $lkAllowScreenShare = $lkAllowScreenShare ?? true;
    $lkTheme = $lkTheme ?? 'default';
    $lkHideLeave = $lkHideLeave ?? false;
    $lkZoomMax = $lkZoomMax ?? (($lkTheme === 'student' || ($lkRole ?? '') === 'participant') ? 5 : 3);
    $lkDefaultScreenZoom = $lkDefaultScreenZoom ?? (($lkTheme === 'student' || ($lkRole ?? '') === 'participant') ? 1.35 : 1);
@endphp
<div id="lk-room-shell" class="lk-room lk-theme-{{ $lkTheme }} relative flex-1 min-h-0 flex flex-col" data-lk-theme="{{ $lkTheme }}" data-lk-role="{{ $lkRole }}">
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
                        <input type="range" id="lk-zoom-slider" class="lk-zoom-slider" min="75" max="{{ (int) round($lkZoomMax * 100) }}" step="5" value="100" aria-label="مستوى التكبير">
                        <span id="lk-zoom-label" class="lk-zoom-label">100%</span>
                        <button type="button" id="lk-zoom-in" class="lk-icon-btn" title="تكبير"><i class="fas fa-search-plus"></i></button>
                        <button type="button" id="lk-zoom-reset" class="lk-icon-btn" title="إعادة"><i class="fas fa-compress"></i></button>
                        <button type="button" id="lk-zoom-fit" class="lk-icon-btn" title="ملء المساحة"><i class="fas fa-expand"></i></button>
                        <button type="button" id="lk-zoom-fill" class="lk-icon-btn lk-icon-btn--accent" title="أقصى تكبير"><i class="fas fa-up-right-and-down-left-from-center"></i></button>
                    </div>
                </div>
            </div>
            <div id="lk-stage" class="lk-stage flex-1 min-h-0"></div>

            {{-- نافذة عائمة للكاميرات أثناء الشير --}}
            <div id="lk-pip" class="lk-pip lk-pip--cols-2 hidden" data-pip-cols="2" aria-label="كاميرات المشاركين">
                <div class="lk-pip__head">
                    <span class="lk-pip__title"><i class="fas fa-video"></i> <span id="lk-pip-count-label">الكاميرات</span></span>
                    <div class="lk-pip__actions">
                        <div class="lk-pip__grid-switch" role="group" aria-label="تخطيط الشبكة">
                            <button type="button" class="lk-icon-btn lk-pip-cols-btn" data-pip-cols="1" title="عمود واحد"><i class="fas fa-square"></i></button>
                            <button type="button" class="lk-icon-btn lk-pip-cols-btn is-active" data-pip-cols="2" title="شبكتان"><i class="fas fa-th-large"></i></button>
                            <button type="button" class="lk-icon-btn lk-pip-cols-btn" data-pip-cols="3" title="شبكة 3"><i class="fas fa-th"></i></button>
                        </div>
                        <button type="button" id="lk-pip-os" class="lk-icon-btn" title="نافذة عائمة فوق الجهاز كله"><i class="fas fa-external-link-alt"></i></button>
                        <button type="button" id="lk-pip-toggle" class="lk-icon-btn" title="طي/فتح"><i class="fas fa-chevron-down"></i></button>
                    </div>
                </div>
                <div class="lk-pip__body" id="lk-pip-body"></div>
                <div class="lk-pip__empty hidden" id="lk-pip-empty">لا توجد كاميرات مفتوحة حالياً</div>
            </div>
        </div>
    </div>

    <div class="lk-toolbar shrink-0">
        <div id="lk-toolbar-zoom" class="lk-toolbar-zoom hidden" aria-label="تحكم التكبير">
            <button type="button" id="lk-toolbar-zoom-out" class="lk-icon-btn" title="تصغير"><i class="fas fa-search-minus"></i></button>
            <input type="range" id="lk-toolbar-zoom-slider" class="lk-zoom-slider lk-zoom-slider--toolbar" min="75" max="{{ (int) round($lkZoomMax * 100) }}" step="5" value="100" aria-label="مستوى التكبير">
            <span id="lk-toolbar-zoom-label" class="lk-zoom-label">100%</span>
            <button type="button" id="lk-toolbar-zoom-in" class="lk-icon-btn" title="تكبير"><i class="fas fa-search-plus"></i></button>
            <button type="button" id="lk-toolbar-zoom-fit" class="lk-icon-btn" title="ملء المساحة"><i class="fas fa-expand"></i></button>
        </div>
        <button type="button" id="lk-toggle-mic" class="lk-btn{{ $lkStartAudio ? ' is-on-active' : ' is-off' }}" aria-pressed="{{ $lkStartAudio ? 'true' : 'false' }}" title="{{ $lkStartAudio ? 'إيقاف الميكروفون' : 'تشغيل الميكروفون' }}"><i class="fas fa-{{ $lkStartAudio ? 'microphone' : 'microphone-slash' }}"></i><span>{{ $lkStartAudio ? 'ميكروفون' : 'ميكروفون مقفول' }}</span></button>
        <button type="button" id="lk-toggle-cam" class="lk-btn{{ $lkStartVideo ? ' is-on-active' : ' is-off' }}" aria-pressed="{{ $lkStartVideo ? 'true' : 'false' }}" title="{{ $lkStartVideo ? 'إيقاف الكاميرا' : 'تشغيل الكاميرا' }}"><i class="fas fa-{{ $lkStartVideo ? 'video' : 'video-slash' }}"></i><span>{{ $lkStartVideo ? 'كاميرا' : 'كاميرا مقفولة' }}</span></button>
        @if($lkAllowScreenShare)
        <button type="button" id="lk-toggle-screen" class="lk-btn"><i class="fas fa-desktop"></i><span>مشاركة الشاشة</span></button>
        @endif
        <button type="button" id="lk-toggle-os-pip" class="lk-btn" title="نافذة عائمة للكاميرات فوق الجهاز كله"><i class="fas fa-external-link-alt"></i><span>عائمة</span></button>
        @unless($lkHideLeave)
        <a href="{{ $lkLeaveUrl }}" id="lk-leave" class="lk-btn lk-btn--danger"><i class="fas fa-phone-slash"></i><span>مغادرة</span></a>
        @endunless
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
.lk-theme-student .lk-focus__viewport{display:flex;flex-direction:column}
.lk-theme-student .lk-focus__scaler{flex:1;width:100%;height:100%;min-height:0;padding:.2rem;box-sizing:border-box}
.lk-theme-student .lk-focus__scaler video{width:100%;height:100%;max-width:100%;max-height:100%;min-width:0;min-height:0;border-radius:10px}
.lk-focus__bar{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:.5rem;padding:.55rem .85rem;border-top:1px solid var(--lk-line);background:rgba(15,23,42,.92)}
.lk-focus__title{font-size:.8rem;font-weight:800;color:var(--lk-text)}
.lk-focus__zoom{display:inline-flex;align-items:center;gap:.35rem;flex-wrap:wrap}
.lk-zoom-label{font-size:.72rem;font-weight:800;min-width:3.2rem;text-align:center;color:var(--lk-muted)}
.lk-zoom-slider{width:min(120px,28vw);height:4px;accent-color:var(--lk-gold);cursor:pointer}
.lk-zoom-slider--toolbar{width:min(96px,22vw)}
.lk-toolbar-zoom{display:none;align-items:center;gap:.35rem;padding:.15rem .45rem;border-radius:999px;border:1px solid var(--lk-line);background:color-mix(in srgb, var(--lk-panel) 88%, #000)}
.lk-toolbar-zoom:not(.hidden){display:inline-flex}
.lk-room.is-screen-focus .lk-toolbar{flex-wrap:wrap;gap:.55rem}
.lk-icon-btn{display:inline-flex;align-items:center;justify-content:center;width:2rem;height:2rem;border-radius:8px;border:1px solid var(--lk-line);background:var(--lk-panel);color:var(--lk-text);cursor:pointer}
.lk-icon-btn:hover{border-color:var(--lk-accent);color:var(--lk-gold)}
.lk-icon-btn--accent{border-color:var(--lk-gold);color:var(--lk-gold);background:color-mix(in srgb, var(--lk-gold) 16%, var(--lk-panel))}

/* tiles */
.lk-tile{position:relative;background:var(--lk-panel);border:1px solid var(--lk-line);border-radius:14px;overflow:hidden;min-height:0;width:100%;height:100%}
.lk-stage.layout-solo .lk-tile,
.lk-stage.layout-duo .lk-tile,
.lk-stage.layout-trio .lk-tile,
.lk-stage.layout-class .lk-tile{aspect-ratio:unset;min-height:0}
.lk-tile video{width:100%;height:100%;object-fit:cover;background:#020617}
.lk-tile.is-screen{grid-column:1/-1;min-height:280px;aspect-ratio:auto}
.lk-theme-student .lk-tile.is-screen{min-height:min(78vh,640px)}
.lk-tile.is-screen video{object-fit:contain;background:#000}
.lk-theme-student .lk-stage.layout-solo .lk-tile video{object-fit:contain}
.lk-tile-label{position:absolute;inset-inline-start:.65rem;bottom:.65rem;background:rgba(2,6,23,.82);color:var(--lk-text);font-size:.68rem;font-weight:800;padding:.2rem .5rem;border-radius:.45rem;z-index:2}
.lk-tile.is-local{outline:2px solid color-mix(in srgb, var(--lk-accent) 55%, transparent)}
.lk-tile.is-host{outline:2px solid color-mix(in srgb, var(--lk-gold) 70%, transparent)}
.lk-tile.is-host .lk-tile-label::after{content:' · مضيف';color:var(--lk-gold)}

/* floating pip — fixed داخل الصفحة + Document PiP للتبويبات/الجهاز */
.lk-pip{position:fixed;z-index:99990;inset-inline-end:12px;bottom:calc(72px + env(safe-area-inset-bottom,0px));width:min(320px,52vw);border-radius:16px;border:1px solid var(--lk-line);background:rgba(15,23,42,.96);backdrop-filter:blur(12px);box-shadow:0 16px 40px rgba(0,0,0,.4);overflow:hidden}
.lk-theme-instructor .lk-pip{width:min(360px,56vw);border-color:rgba(255,255,255,.14);background:rgba(20,20,20,.96)}
.lk-pip.hidden{display:none!important}
.lk-pip.is-collapsed .lk-pip__body,.lk-pip.is-collapsed .lk-pip__empty{display:none}
.lk-pip__head{display:flex;align-items:center;justify-content:space-between;gap:.5rem;padding:.5rem .65rem;font-size:.72rem;font-weight:800;border-bottom:1px solid var(--lk-line);cursor:move;user-select:none}
.lk-pip__title{display:inline-flex;align-items:center;gap:.4rem;min-width:0}
.lk-pip__actions{display:inline-flex;align-items:center;gap:.25rem;flex-shrink:0}
.lk-pip__grid-switch{display:inline-flex;align-items:center;gap:2px;padding:2px;border-radius:8px;background:rgba(0,0,0,.25);border:1px solid var(--lk-line)}
.lk-pip__grid-switch .lk-icon-btn{width:1.65rem;height:1.65rem;border:0;background:transparent;opacity:.65}
.lk-pip__grid-switch .lk-icon-btn.is-active{opacity:1;background:color-mix(in srgb, var(--lk-accent) 35%, transparent);color:var(--lk-gold)}
.lk-pip__body{display:grid;gap:.4rem;padding:.5rem;max-height:min(42vh,320px);overflow:auto;align-content:start}
.lk-pip--cols-1 .lk-pip__body{grid-template-columns:1fr}
.lk-pip--cols-2 .lk-pip__body{grid-template-columns:1fr 1fr}
.lk-pip--cols-3 .lk-pip__body{grid-template-columns:1fr 1fr 1fr}
.lk-pip__empty{padding:.75rem .65rem;font-size:.7rem;font-weight:700;color:var(--lk-muted);text-align:center}
.lk-pip__empty.hidden{display:none!important}
.lk-pip-tile{position:relative;border-radius:10px;overflow:hidden;background:#000;border:1px solid var(--lk-line);aspect-ratio:4/3;min-height:72px}
.lk-pip--cols-1 .lk-pip-tile{aspect-ratio:16/10;min-height:110px}
.lk-pip-tile.is-local{outline:1px solid color-mix(in srgb, var(--lk-accent) 60%, transparent)}
.lk-pip-tile.is-host{outline:1px solid color-mix(in srgb, var(--lk-gold) 70%, transparent)}
.lk-pip-tile video{width:100%;height:100%;object-fit:cover;display:block;background:#020617}
.lk-pip-tile span{position:absolute;inset-inline-start:.3rem;bottom:.3rem;font-size:.58rem;font-weight:800;background:rgba(0,0,0,.72);padding:.12rem .35rem;border-radius:.3rem;max-width:92%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.lk-btn.is-os-pip{background:color-mix(in srgb, var(--lk-gold) 28%, var(--lk-surface));border-color:var(--lk-gold);color:#fff}
.lk-os-pip-shell{position:fixed;inset:0;background:#0c0c0c;color:var(--lk-text);display:flex;flex-direction:column;overflow:hidden;font-family:inherit}
.lk-os-pip-shell.is-cameras-only{background:#111}
.lk-os-pip-shell .lk-focus{flex:1;min-height:0;display:flex!important}
.lk-os-pip-shell .lk-focus__viewport{flex:1}
.lk-os-pip-shell .lk-pip{position:relative;inset:auto;bottom:auto;right:auto;width:100%;max-height:none;flex:1;border-radius:0;border:0;box-shadow:none;display:flex!important;flex-direction:column}
.lk-os-pip-shell.is-cameras-only .lk-pip{max-height:none}
.lk-os-pip-shell .lk-pip__head{cursor:default;flex-shrink:0}
.lk-os-pip-shell .lk-pip__body{flex:1;max-height:none;overflow:auto}
.lk-os-pip-compact{flex:1;display:flex;align-items:center;justify-content:center;padding:.5rem;background:#020617}
.lk-os-pip-compact video{max-width:100%;max-height:100%;object-fit:contain;border-radius:12px;background:#000}
@media(max-width:640px){
  .lk-pip{width:min(240px,68vw)}
  .lk-pip__grid-switch .lk-icon-btn[data-pip-cols="3"]{display:none}
}

/* toolbar */
.lk-toolbar{display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:.45rem;padding:.7rem .85rem;border-top:1px solid var(--lk-line);background:color-mix(in srgb, var(--lk-panel) 92%, #000)}
.lk-btn{display:inline-flex;align-items:center;gap:.45rem;border-radius:999px;background:var(--lk-surface);color:var(--lk-text);padding:.55rem 1rem;font-size:.78rem;font-weight:800;border:1px solid var(--lk-line);cursor:pointer;text-decoration:none}
.lk-btn:hover{border-color:var(--lk-accent)}
.lk-btn.is-off{background:color-mix(in srgb, var(--lk-danger) 24%, var(--lk-surface));border-color:color-mix(in srgb, var(--lk-danger) 72%, var(--lk-line));color:#fecaca;box-shadow:inset 0 0 0 1px color-mix(in srgb, var(--lk-danger) 35%, transparent)}
.lk-btn.is-off i{color:#fca5a5}
.lk-btn.is-on-active{background:color-mix(in srgb, var(--lk-ok) 16%, var(--lk-surface));border-color:color-mix(in srgb, var(--lk-ok) 55%, var(--lk-line));color:#ecfdf5}
.lk-btn.is-on-active i{color:#86efac}
.lk-btn.is-sharing{background:color-mix(in srgb, var(--lk-accent) 35%, var(--lk-surface));border-color:var(--lk-accent);color:#fff}
.lk-btn--danger{background:var(--lk-danger);border-color:var(--lk-danger);color:#fff}
.lk-btn--accent{background:var(--lk-accent);border-color:var(--lk-accent);color:#fff}
.lk-theme-student .lk-btn--accent,.lk-theme-student .lk-btn.is-sharing{background:linear-gradient(135deg,#0B3D91,#0997d9);border:0}
.lk-theme-instructor .lk-btn{border-radius:12px}
@media(max-width:640px){
  .lk-btn span{display:none}
  .lk-pip{width:min(240px,68vw)}
  .lk-pip__grid-switch .lk-icon-btn[data-pip-cols="3"]{display:none}
  .lk-zoom-slider{width:min(88px,24vw)}
  .lk-focus__zoom .lk-icon-btn:nth-child(n+6){display:none}
}
.lk-room.is-screen-focus .lk-main{min-height:0}
.lk-theme-student.is-screen-focus .lk-main{min-height:min(78vh,780px)}
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
    const lkTheme = @json($lkTheme);
    const lkDefaultScreenZoom = @json($lkDefaultScreenZoom);
    const ZOOM_MAX = @json($lkZoomMax);
    const shell = document.getElementById('lk-room-shell');
    const stage = document.getElementById('lk-stage');
    const focusBox = document.getElementById('lk-focus');
    const focusVideo = document.getElementById('lk-focus-video');
    const focusScaler = document.getElementById('lk-focus-scaler');
    const focusViewport = document.getElementById('lk-focus-viewport');
    const focusTitle = document.getElementById('lk-focus-title');
    const pip = document.getElementById('lk-pip');
    const pipBody = document.getElementById('lk-pip-body');
    const pipEmpty = document.getElementById('lk-pip-empty');
    const pipCountLabel = document.getElementById('lk-pip-count-label');
    let pipCols = Math.min(3, Math.max(1, parseInt(pip?.dataset?.pipCols || '2', 10) || 2));
    const statusEl = document.getElementById('lk-status');
    const micBtn = document.getElementById('lk-toggle-mic');
    const camBtn = document.getElementById('lk-toggle-cam');
    const screenBtn = document.getElementById('lk-toggle-screen');
    const zoomLabel = document.getElementById('lk-zoom-label');
    const zoomSlider = document.getElementById('lk-zoom-slider');
    const toolbarZoom = document.getElementById('lk-toolbar-zoom');
    const toolbarZoomSlider = document.getElementById('lk-toolbar-zoom-slider');
    const toolbarZoomLabel = document.getElementById('lk-toolbar-zoom-label');

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
    let osPipShell = null;
    let osPipCompact = null;
    let osPipRestore = null;
    let osPipActive = false;
    let osPipAutoTried = false;
    let osPipOpening = false;
    const ZOOM_MIN = 0.75;
    const ZOOM_STEP = lkTheme === 'student' ? 0.15 : 0.25;
    const isStudentView = lkTheme === 'student' || role === 'participant';

    function syncZoomUi() {
        const pct = Math.round(zoom * 100);
        if (zoomLabel) zoomLabel.textContent = pct + '%';
        if (toolbarZoomLabel) toolbarZoomLabel.textContent = pct + '%';
        if (zoomSlider) zoomSlider.value = String(pct);
        if (toolbarZoomSlider) toolbarZoomSlider.value = String(pct);
    }

    function setZoom(next, opts) {
        opts = opts || {};
        zoom = Math.min(ZOOM_MAX, Math.max(ZOOM_MIN, next));
        applyZoom();
        if (opts.scrollReset && focusViewport) {
            focusViewport.scrollTo({ left: 0, top: 0 });
        }
    }

    function computeFitZoom() {
        if (!focusVideo || !focusViewport) return lkDefaultScreenZoom || 1;
        const vw = focusVideo.videoWidth || focusVideo.clientWidth;
        const vh = focusVideo.videoHeight || focusVideo.clientHeight;
        const cw = focusViewport.clientWidth;
        const ch = focusViewport.clientHeight;
        if (!vw || !vh || !cw || !ch) return lkDefaultScreenZoom || 1;
        const contained = Math.min(cw / vw, ch / vh);
        const boost = isStudentView ? 1.08 : 1.02;
        return Math.min(ZOOM_MAX, Math.max(lkDefaultScreenZoom || 1, contained * boost));
    }

    function autoFitZoom() {
        setZoom(computeFitZoom());
    }

    function maxFillZoom() {
        setZoom(ZOOM_MAX);
    }

    function syncMicButton() {
        if (!micBtn) return;
        micBtn.classList.toggle('is-off', !micOn);
        micBtn.classList.toggle('is-on-active', !!micOn);
        micBtn.setAttribute('aria-pressed', micOn ? 'true' : 'false');
        micBtn.setAttribute('title', micOn ? 'إيقاف الميكروفون' : 'تشغيل الميكروفون');
        const icon = micBtn.querySelector('i');
        const label = micBtn.querySelector('span');
        if (icon) icon.className = micOn ? 'fas fa-microphone' : 'fas fa-microphone-slash';
        if (label) label.textContent = micOn ? 'ميكروفون' : 'ميكروفون مقفول';
    }

    function syncCamButton() {
        if (!camBtn) return;
        camBtn.classList.toggle('is-off', !camOn);
        camBtn.classList.toggle('is-on-active', !!camOn);
        camBtn.setAttribute('aria-pressed', camOn ? 'true' : 'false');
        camBtn.setAttribute('title', camOn ? 'إيقاف الكاميرا' : 'تشغيل الكاميرا');
        const icon = camBtn.querySelector('i');
        const label = camBtn.querySelector('span');
        if (icon) icon.className = camOn ? 'fas fa-video' : 'fas fa-video-slash';
        if (label) label.textContent = camOn ? 'كاميرا' : 'كاميرا مقفولة';
    }

    function syncLocalMediaStateFromRoom() {
        if (!room?.localParticipant) return;
        try {
            micOn = room.localParticipant.isMicrophoneEnabled;
            camOn = room.localParticipant.isCameraEnabled;
        } catch (e) {}
        syncMicButton();
        syncCamButton();
    }

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
        syncZoomUi();
    }
    function setScreenFocus(on, title) {
        shell?.classList.toggle('is-screen-focus', !!on);
        if (focusBox) focusBox.classList.toggle('hidden', !on);
        if (toolbarZoom) toolbarZoom.classList.toggle('hidden', !on);
        if (title && focusTitle) focusTitle.textContent = title;
        if (!on) {
            setZoom(1);
            if (focusVideo) {
                focusVideo.srcObject = null;
                focusVideo.removeAttribute('src');
            }
            focusTrack = null;
            if (documentPictureInPicture?.window || osPipActive || document.pictureInPictureElement) {
                closeOsFloatingWindow().catch(() => restoreOsPipDom());
            }
            osPipAutoTried = false;
            syncFloatingPipExclusive();
            updateStageLayout();
        } else {
            // نافذة كاميرات واحدة فقط داخل الصفحة أثناء الشير — النافذة فوق النظام بضغطة يدوية
            rebuildPip();
            syncFloatingPipExclusive();
        }
    }
    function attachToFocus(track, label) {
        focusTrack = track;
        if (focusVideo) {
            track.attach(focusVideo);
            focusVideo.muted = true;
            focusVideo.playsInline = true;
            focusVideo.play?.().catch(() => {});
            if (!focusVideo.__mxZoomBound) {
                focusVideo.__mxZoomBound = true;
                focusVideo.addEventListener('loadedmetadata', function onMeta() {
                    if (!isStudentView || !shell?.classList.contains('is-screen-focus')) return;
                    setTimeout(autoFitZoom, 80);
                });
            }
        }
        setScreenFocus(true, label || 'مشاركة الشاشة');
        if (isStudentView) {
            setZoom(lkDefaultScreenZoom || 1.35);
            setTimeout(autoFitZoom, 120);
        } else {
            setZoom(1);
        }
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
        const ref = { el, video, participant, source, track: null };
        tiles.set(key, ref);
        updateStageLayout();
        return ref;
    }
    function removeTile(participant, source) {
        const key = tileKey(participant, source);
        const ref = tiles.get(key);
        if (!ref) return;
        if (ref.track && ref.pipVideo) {
            try { ref.track.detach(ref.pipVideo); } catch (e) {}
        }
        ref.el.remove();
        tiles.delete(key);
        updateStageLayout();
    }

    function applyPipCols(cols) {
        pipCols = Math.min(3, Math.max(1, parseInt(cols, 10) || 2));
        if (!pip) return;
        pip.dataset.pipCols = String(pipCols);
        pip.classList.remove('lk-pip--cols-1', 'lk-pip--cols-2', 'lk-pip--cols-3');
        pip.classList.add('lk-pip--cols-' + pipCols);
        pip.querySelectorAll('.lk-pip-cols-btn').forEach((btn) => {
            btn.classList.toggle('is-active', String(btn.getAttribute('data-pip-cols')) === String(pipCols));
        });
    }

    function cameraTrackIsLive(ref) {
        if (!ref || ref.source === Track.Source.ScreenShare) return false;
        if (ref.source === Track.Source.ScreenShareAudio) return false;
        const mediaTrack = ref.track?.mediaStreamTrack;
        if (mediaTrack) {
            return mediaTrack.readyState === 'live' && mediaTrack.enabled !== false && !ref.track.isMuted;
        }
        const videoEl = ref.video;
        if (!videoEl || !videoEl.srcObject) return false;
        const stream = videoEl.srcObject;
        if (!(stream instanceof MediaStream)) return false;
        const tracks = stream.getVideoTracks();
        if (!tracks.length) return false;
        return tracks.some((t) => t.readyState === 'live' && t.enabled !== false);
    }

    function playPipVideo(v) {
        if (!v) return;
        const p = v.play?.();
        if (p && typeof p.catch === 'function') p.catch(() => {});
    }

    function isPipHostedInOsWindow() {
        return !!(pip && osPipShell && pip.parentElement === osPipShell);
    }

    function isOsDocumentPipOpen() {
        try {
            return !!(typeof documentPictureInPicture !== 'undefined' && documentPictureInPicture.window);
        } catch (e) {
            return false;
        }
    }

    /**
     * ضمان نافذة عائمة واحدة فقط:
     * - إما شريط الكاميرات داخل الصفحة
     * - أو نفس الشريط داخل Document PiP
     * ولا نفتح Video PiP للشاشة بالتوازي مع شريط الكاميرات.
     */
    function syncFloatingPipExclusive() {
        if (!pip) return;
        const screenFocus = !!shell?.classList.contains('is-screen-focus');
        const inOs = isPipHostedInOsWindow();

        if (inOs) {
            pip.classList.remove('hidden');
            return;
        }

        // نافذة نظام مفتوحة لكن العنصر رُدّ للصفحة بالخطأ → أخفِ النسخة داخل الصفحة لتفادي التكرار
        if (isOsDocumentPipOpen() && !inOs) {
            pip.classList.add('hidden');
            return;
        }

        // لا نسمح بـ Video PiP للشاشة + شريط كاميرات داخل الصفحة معاً
        if (screenFocus && document.pictureInPictureElement && focusVideo
            && document.pictureInPictureElement === focusVideo) {
            document.exitPictureInPicture().catch(() => {});
        }

        pip.classList.toggle('hidden', !screenFocus && !osPipActive);
    }

    function rebuildPip() {
        if (!pipBody) return;
        // detach previous pip attaches
        tiles.forEach((ref) => {
            if (ref.track && ref.pipVideo) {
                try { ref.track.detach(ref.pipVideo); } catch (e) {}
            }
            ref.pipVideo = null;
        });
        pipBody.innerHTML = '';
        pipTiles.clear();

        // كاميرا واحدة لكل مشارك (تفادي تكرار نفس الشخص من مصادر فيديو متعددة)
        const byIdentity = new Map();
        tiles.forEach((ref, key) => {
            if (!cameraTrackIsLive(ref)) return;
            const id = String(ref.participant?.identity || key);
            const prev = byIdentity.get(id);
            if (!prev) {
                byIdentity.set(id, [key, ref]);
                return;
            }
            const preferNew = ref.source === Track.Source.Camera && prev[1].source !== Track.Source.Camera;
            if (preferNew) byIdentity.set(id, [key, ref]);
        });

        const liveCams = [...byIdentity.values()].sort((a, b) => {
            const aHost = isHostParticipant(a[1].participant) ? 0 : 1;
            const bHost = isHostParticipant(b[1].participant) ? 0 : 1;
            if (aHost !== bHost) return aHost - bHost;
            const aLocal = a[1].participant.isLocal ? 0 : 1;
            const bLocal = b[1].participant.isLocal ? 0 : 1;
            return aLocal - bLocal;
        });

        liveCams.forEach(([key, ref]) => {
            const wrap = document.createElement('div');
            wrap.className = 'lk-pip-tile'
                + (ref.participant.isLocal ? ' is-local' : '')
                + (isHostParticipant(ref.participant) ? ' is-host' : '');
            wrap.dataset.pipIdentity = String(ref.participant?.identity || key);
            const v = document.createElement('video');
            v.autoplay = true;
            v.playsInline = true;
            v.muted = true;
            v.setAttribute('playsinline', '');
            v.setAttribute('autoplay', '');
            if (ref.track && typeof ref.track.attach === 'function') {
                try {
                    ref.track.attach(v);
                    ref.pipVideo = v;
                } catch (attachErr) {
                    if (ref.video?.srcObject) v.srcObject = ref.video.srcObject;
                }
            } else if (ref.video?.srcObject) {
                v.srcObject = ref.video.srcObject;
            }
            playPipVideo(v);
            const name = document.createElement('span');
            let label = (ref.participant.name || ref.participant.identity || '').slice(0, 18);
            if (isHostParticipant(ref.participant)) label += ' · مضيف';
            name.textContent = label;
            wrap.appendChild(v);
            wrap.appendChild(name);
            pipBody.appendChild(wrap);
            pipTiles.set(key, wrap);
            // re-kick play after layout / Document PiP move
            requestAnimationFrame(function () { playPipVideo(v); });
            setTimeout(function () { playPipVideo(v); }, 120);
            setTimeout(function () { playPipVideo(v); }, 400);
        });

        const count = liveCams.length;
        if (pipCountLabel) {
            pipCountLabel.textContent = count ? ('الكاميرات · ' + count) : 'الكاميرات';
        }
        if (pipEmpty) pipEmpty.classList.toggle('hidden', count > 0);
        if (pipBody) pipBody.classList.toggle('hidden', count === 0);

        syncFloatingPipExclusive();
        if (pip && shell?.classList.contains('is-screen-focus') && !isPipHostedInOsWindow() && !isOsDocumentPipOpen()) {
            if (count === 0 && !osPipActive) {
                pip.classList.toggle('hidden', lkTheme !== 'instructor' && role !== 'host');
            }
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
            tile.track = track;
            track.attach(tile.video);
            tile.el.style.display = 'none';
            return;
        }

        const tile = ensureTile(participant, track.source);
        tile.track = track;
        track.attach(tile.video);
        updateStageLayout();
        if (shell?.classList.contains('is-screen-focus') || osPipActive) rebuildPip();
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
        .on(RoomEvent.Disconnected, () => { connected = false; setStatus('تم قطع الاتصال بالغرفة', true); })
        .on(RoomEvent.TrackMuted, (publication, participant) => {
            if (participant?.isLocal) {
                if (publication?.source === Track.Source.Microphone) {
                    micOn = false;
                    syncMicButton();
                }
                if (publication?.source === Track.Source.Camera) {
                    camOn = false;
                    syncCamButton();
                }
            }
            if (publication?.source === Track.Source.Camera || publication?.kind === Track.Kind.Video) {
                if (shell?.classList.contains('is-screen-focus') || osPipActive) rebuildPip();
            }
        })
        .on(RoomEvent.TrackUnmuted, (publication, participant) => {
            if (participant?.isLocal) {
                if (publication?.source === Track.Source.Microphone) {
                    micOn = true;
                    syncMicButton();
                }
                if (publication?.source === Track.Source.Camera) {
                    camOn = true;
                    syncCamButton();
                }
            }
            if (publication?.source === Track.Source.Camera || publication?.kind === Track.Kind.Video) {
                if (shell?.classList.contains('is-screen-focus') || osPipActive) rebuildPip();
            }
        })
        .on(RoomEvent.LocalTrackPublished, (publication, participant) => {
            if (!participant?.isLocal || !publication) return;
            if (publication.source === Track.Source.Microphone) {
                micOn = true;
                syncMicButton();
            }
            if (publication.source === Track.Source.Camera) {
                camOn = true;
                syncCamButton();
            }
        })
        .on(RoomEvent.LocalTrackUnpublished, (publication, participant) => {
            if (!participant?.isLocal || !publication) return;
            if (publication.source === Track.Source.Microphone) {
                micOn = false;
                syncMicButton();
            }
            if (publication.source === Track.Source.Camera) {
                camOn = false;
                syncCamButton();
            }
        });

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
                syncMicButton();
                syncCamButton();
                setStatus('متصل · ' + (role === 'host' ? 'مضيف' : 'مشارك') + ' · ' + displayName);
                hideStatusSoon();
                updateStageLayout();
            } catch (mediaErr) {
                console.warn(mediaErr);
                micOn = false; camOn = false;
                syncMicButton();
                syncCamButton();
                setStatus('متصل بدون ميكروفون/كاميرا — فعّل الأذونات من الأزرار', true);
            }
            syncLocalMediaStateFromRoom();
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
            syncMicButton();
        } catch (e) { setStatus(errMsg(e, 'تعذر تفعيل الميكروفون'), true); }
    });
    camBtn?.addEventListener('click', async () => {
        if (!connected) return;
        try {
            const next = !camOn;
            await room.localParticipant.setCameraEnabled(next);
            camOn = next;
            syncCamButton();
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
        setZoom(zoom + ZOOM_STEP);
    });
    document.getElementById('lk-zoom-out')?.addEventListener('click', () => {
        setZoom(zoom - ZOOM_STEP);
    });
    document.getElementById('lk-zoom-reset')?.addEventListener('click', () => {
        setZoom(1, { scrollReset: true });
    });
    document.getElementById('lk-zoom-fit')?.addEventListener('click', () => {
        autoFitZoom();
    });
    document.getElementById('lk-zoom-fill')?.addEventListener('click', () => {
        maxFillZoom();
    });
    function bindZoomSlider(el) {
        if (!el) return;
        el.addEventListener('input', function () {
            setZoom(parseInt(el.value, 10) / 100);
        });
    }
    bindZoomSlider(zoomSlider);
    bindZoomSlider(toolbarZoomSlider);
    document.getElementById('lk-toolbar-zoom-in')?.addEventListener('click', () => setZoom(zoom + ZOOM_STEP));
    document.getElementById('lk-toolbar-zoom-out')?.addEventListener('click', () => setZoom(zoom - ZOOM_STEP));
    document.getElementById('lk-toolbar-zoom-fit')?.addEventListener('click', () => autoFitZoom());
    document.getElementById('lk-pip-toggle')?.addEventListener('click', () => {
        pip?.classList.toggle('is-collapsed');
    });
    pip?.querySelectorAll('.lk-pip-cols-btn').forEach((btn) => {
        btn.addEventListener('click', function () {
            applyPipCols(btn.getAttribute('data-pip-cols'));
        });
    });
    applyPipCols(pipCols);

    const osPipBtn = document.getElementById('lk-toggle-os-pip');
    const osPipFocusBtn = document.getElementById('lk-os-pip');
    const osPipCamBtn = document.getElementById('lk-pip-os');

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
            html,body{margin:0;padding:0;width:100%;height:100%;overflow:hidden;background:#0c0c0c}
            .lk-os-pip-shell{position:fixed;inset:0;background:#0c0c0c;color:#f5f5f5;display:flex;flex-direction:column;font-family:Inter,Cairo,Tajawal,system-ui,sans-serif}
            .lk-os-pip-shell.is-cameras-only{background:#111}
            .lk-os-pip-shell .lk-focus{flex:1;min-height:0;display:flex!important;flex-direction:column;background:#020617}
            .lk-os-pip-shell .lk-focus.hidden{display:none!important}
            .lk-os-pip-shell .lk-focus__viewport{flex:1;min-height:0;overflow:auto;background:#020617}
            .lk-os-pip-shell .lk-focus__scaler{display:flex;align-items:center;justify-content:center;min-height:100%;padding:.35rem}
            .lk-os-pip-shell .lk-focus__scaler video{max-width:100%;max-height:100%;object-fit:contain;background:#000;border-radius:8px}
            .lk-os-pip-shell .lk-focus__bar{display:flex;align-items:center;justify-content:space-between;padding:.35rem .55rem;border-top:1px solid rgba(255,255,255,.1);background:rgba(20,20,20,.95);font-size:.68rem;font-weight:800}
            .lk-os-pip-shell .lk-pip{position:relative!important;inset:auto!important;width:100%;max-height:42vh;border-radius:0;border:0;border-top:1px solid rgba(255,255,255,.1);box-shadow:none;background:rgba(20,20,20,.98);display:flex!important;flex-direction:column}
            .lk-os-pip-shell.is-cameras-only .lk-pip{max-height:none;flex:1;border-top:0}
            .lk-os-pip-shell .lk-pip.hidden{display:none!important}
            .lk-os-pip-shell .lk-pip__head{cursor:default;flex-shrink:0;padding:.55rem .7rem;border-bottom:1px solid rgba(255,255,255,.1)}
            .lk-os-pip-shell .lk-pip__body{flex:1;max-height:none;overflow:auto;padding:.55rem;gap:.45rem;display:grid;align-content:start}
            .lk-os-pip-shell .lk-pip--cols-1 .lk-pip__body{grid-template-columns:1fr}
            .lk-os-pip-shell .lk-pip--cols-2 .lk-pip__body{grid-template-columns:1fr 1fr}
            .lk-os-pip-shell .lk-pip--cols-3 .lk-pip__body{grid-template-columns:1fr 1fr 1fr}
            .lk-os-pip-shell .lk-pip-tile{position:relative;border-radius:10px;overflow:hidden;background:#000;border:1px solid rgba(255,255,255,.12);aspect-ratio:4/3;min-height:78px}
            .lk-os-pip-shell .lk-pip--cols-1 .lk-pip-tile{aspect-ratio:16/10;min-height:120px}
            .lk-os-pip-shell .lk-pip-tile video{width:100%;height:100%;object-fit:cover;display:block}
            .lk-os-pip-shell .lk-pip-tile span{position:absolute;inset-inline-start:.35rem;bottom:.35rem;font-size:.62rem;font-weight:800;background:rgba(0,0,0,.72);padding:.12rem .35rem;border-radius:.3rem}
            .lk-os-pip-shell .lk-pip__empty{padding:1rem;text-align:center;color:rgba(245,245,245,.55);font-size:.75rem;font-weight:700}
            .lk-os-pip-shell .lk-pip__empty.hidden{display:none!important}
            .lk-os-pip-shell .lk-icon-btn{display:inline-flex;align-items:center;justify-content:center;width:1.75rem;height:1.75rem;border-radius:8px;border:1px solid rgba(255,255,255,.12);background:#181818;color:#f5f5f5;cursor:pointer}
            .lk-os-pip-shell .lk-pip__grid-switch{display:inline-flex;gap:2px;padding:2px;border-radius:8px;border:1px solid rgba(255,255,255,.1);background:rgba(0,0,0,.3)}
            .lk-os-pip-shell .lk-pip__grid-switch .lk-icon-btn{width:1.55rem;height:1.55rem;border:0;background:transparent;opacity:.65}
            .lk-os-pip-shell .lk-pip__grid-switch .lk-icon-btn.is-active{opacity:1;background:rgba(149,164,252,.35);color:#ffcb9a}
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
        syncFloatingPipExclusive();
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
    async function openDocumentPiP(opts) {
        opts = opts || {};
        if (osPipOpening) return false;
        const camerasOnly = !!opts.camerasOnly || (lkTheme === 'instructor' || role === 'host');
        // للكاميرات فقط: لا نفتح Video PiP للشاشة (يتسبب بنافذتين)
        if (!supportsDocumentPiP()) {
            if (camerasOnly) {
                syncFloatingPipExclusive();
                setStatus('استخدم شريط الكاميرات العائم داخل الصفحة — المتصفح لا يدعم نافذة النظام للكاميرات', true);
                return false;
            }
            return openVideoPiP();
        }
        if (documentPictureInPicture.window) {
            documentPictureInPicture.window.focus();
            updateOsPipButtons(true);
            syncFloatingPipExclusive();
            return true;
        }
        if (document.pictureInPictureElement) {
            try { await document.exitPictureInPicture(); } catch (e) {}
        }

        osPipOpening = true;
        try {
        rebuildPip();
        const camCount = pipBody ? pipBody.children.length : 0;
        const inScreenFocus = shell?.classList.contains('is-screen-focus');
        const wantCamerasOnly = camerasOnly || !inScreenFocus;
        const width = wantCamerasOnly
            ? (pipCols >= 3 ? 540 : (pipCols === 1 ? 280 : 420))
            : 560;
        const height = wantCamerasOnly
            ? Math.min(520, Math.max(220, 120 + camCount * (pipCols === 1 ? 140 : 110)))
            : 380;

        const pipWindow = await documentPictureInPicture.requestWindow({ width, height });
        injectOsPipStyles(pipWindow.document);
        osPipShell = pipWindow.document.createElement('div');
        osPipShell.className = 'lk-os-pip-shell' + (wantCamerasOnly ? ' is-cameras-only' : '');
        pipWindow.document.body.appendChild(osPipShell);

        osPipRestore = {
            focusParent: focusBox?.parentElement || null,
            focusNext: focusBox?.nextSibling || null,
            pipParent: pip?.parentElement || null,
            pipNext: pip?.nextSibling || null,
            compactParent: null,
        };

        if (wantCamerasOnly) {
            if (pip) {
                pip.classList.remove('hidden');
                osPipShell.appendChild(pip);
            }
        } else if (inScreenFocus && focusBox) {
            focusBox.classList.remove('hidden');
            osPipShell.appendChild(focusBox);
            if (pip) {
                pip.classList.remove('hidden');
                osPipShell.appendChild(pip);
            }
        } else {
            osPipCompact = buildOsPipCompact();
            osPipRestore.compactParent = osPipShell;
            osPipShell.appendChild(osPipCompact);
            if (pip && pipBody?.children.length) {
                pip.classList.remove('hidden');
                osPipShell.appendChild(pip);
            }
        }

        pipWindow.addEventListener('pagehide', restoreOsPipDom, { once: true });
        // إعادة ربط فيديوهات الكاميرا بعد نقل الـ DOM لنافذة PiP (حتى لا تتجمد كصورة)
        requestAnimationFrame(function () {
            rebuildPip();
            pipBody?.querySelectorAll('video').forEach(playPipVideo);
            syncFloatingPipExclusive();
        });
        setTimeout(function () {
            rebuildPip();
            pipBody?.querySelectorAll('video').forEach(playPipVideo);
            syncFloatingPipExclusive();
        }, 250);
        updateOsPipButtons(true);
        syncFloatingPipExclusive();
        setStatus(wantCamerasOnly
            ? 'نافذة الكاميرات العائمة نشطة — تتنقل معك فوق كل التطبيقات'
            : 'النافذة العائمة نشطة — تبقى فوق التبويبات والتطبيقات');
        hideStatusSoon();
        return true;
        } finally {
            osPipOpening = false;
        }
    }
    async function toggleOsFloatingWindow(forceCamerasOnly) {
        try {
            if (osPipOpening) return;
            if (osPipActive || documentPictureInPicture?.window || document.pictureInPictureElement) {
                await closeOsFloatingWindow();
                syncFloatingPipExclusive();
                return;
            }
            await openDocumentPiP({
                camerasOnly: forceCamerasOnly === true || lkTheme === 'instructor' || role === 'host',
            });
        } catch (err) {
            if (err?.name === 'NotAllowedError') {
                setStatus('اسمح للموقع بفتح النافذة العائمة من إعدادات المتصفح', true);
                syncFloatingPipExclusive();
                return;
            }
            console.warn(err);
            // لا نفتح Video PiP كبديل للمدرب حتى لا تتكرر النوافذ مع شريط الكاميرات
            if (lkTheme === 'instructor' || role === 'host' || forceCamerasOnly === true) {
                syncFloatingPipExclusive();
                setStatus(errMsg(err, 'تعذر فتح نافذة النظام — شريط الكاميرات يبقى داخل الصفحة'), true);
                return;
            }
            try { await openVideoPiP(); } catch (e2) {
                setStatus(errMsg(e2, 'تعذر فتح النافذة العائمة'), true);
            }
        }
    }
    function maybeAutoOpenOsPip() {
        // معطّل عمداً: الفتح التلقائي يسبب نافذة نظام + شريط داخل الصفحة أو فشلاً صامتاً
        return;
    }
    osPipBtn?.addEventListener('click', () => toggleOsFloatingWindow(true));
    osPipFocusBtn?.addEventListener('click', () => toggleOsFloatingWindow(true));
    osPipCamBtn?.addEventListener('click', () => toggleOsFloatingWindow(true));
    document.addEventListener('leavepictureinpicture', () => {
        updateOsPipButtons(false);
        syncFloatingPipExclusive();
    });
    if (documentPictureInPicture) {
        documentPictureInPicture.addEventListener('enter', () => {
            updateOsPipButtons(true);
            syncFloatingPipExclusive();
        });
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
            setZoom(zoom + (e.deltaY < 0 ? ZOOM_STEP : -ZOOM_STEP));
        }, { passive: false });
        focusViewport.addEventListener('dblclick', () => {
            if (zoom >= ZOOM_MAX * 0.95) {
                autoFitZoom();
            } else {
                maxFillZoom();
            }
        });
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
