
<?php
    $mxAnnRole = $mxAnnRole ?? 'student_emit';
    $mxAnnPostUrl = $mxAnnPostUrl ?? '';
    $mxAnnPollUrl = $mxAnnPollUrl ?? '';
?>
<style>
    #mx-share-ann-layer { pointer-events: none; }
    #mx-share-ann-layer.mx-share-ann-drawing { pointer-events: auto; }
    #mx-share-ann-layer.mx-share-ann-drawing #mx-share-ann-canvas { touch-action: none; }
    #mx-share-ann-toolbar { pointer-events: auto; }
</style>
<div id="mx-share-ann-layer" class="absolute inset-0 z-[25] hidden"
     data-role="<?php echo e($mxAnnRole); ?>"
     data-post-url="<?php echo e(e($mxAnnPostUrl)); ?>"
     data-poll-url="<?php echo e(e($mxAnnPollUrl)); ?>"
     data-guest-token="">
    <canvas id="mx-share-ann-canvas" class="absolute inset-0 w-full h-full block"></canvas>
    <div id="mx-share-ann-toolbar" class="absolute bottom-3 left-1/2 -translate-x-1/2 flex flex-wrap items-center justify-center gap-2 px-3 py-2 rounded-2xl bg-slate-900/92 border border-slate-600 shadow-xl max-w-[95vw]">
        <span class="text-slate-400 text-[11px] px-1 hidden sm:inline">فوق عرض البث</span>
        <button type="button" data-mx-ann-tool="pen" class="mx-ann-tool-btn inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-amber-600/30 text-amber-100 text-xs font-semibold border border-amber-500/50 ring-2 ring-amber-400/60">
            <i class="fas fa-pen"></i> قلم
        </button>
        <button type="button" data-mx-ann-tool="eraser" class="mx-ann-tool-btn inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-600">
            <i class="fas fa-eraser"></i> ممحاة
        </button>
        <button type="button" data-mx-ann-action="clear" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-700 text-slate-200 text-xs font-medium border border-slate-600">
            <i class="fas fa-trash-alt"></i> مسح كتابتي
        </button>
        <button type="button" data-mx-ann-action="close" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-800 text-slate-400 text-xs border border-slate-600" title="إيقاف الرسم والتفاعل مع الاجتماع">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
<script>
(function () {
    var layer = document.getElementById('mx-share-ann-layer');
    var canvas = document.getElementById('mx-share-ann-canvas');
    if (!layer || !canvas) return;

    var role = layer.getAttribute('data-role') || '';
    var postUrl = layer.getAttribute('data-post-url') || '';
    var pollUrl = layer.getAttribute('data-poll-url') || '';
    var ctx = canvas.getContext('2d');

    var polylines = [];
    var tool = 'pen';
    var drawing = false;
    var drawEnabled = false;
    var currentPts = null;
    var postTimer = null;
    var pollTimer = null;
    var allowed = false;
    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    function isEmitter() {
        return role === 'student_emit' || role === 'classroom_guest_emit';
    }
    function isViewer() {
        return role === 'viewer_poll';
    }

    function resizeCanvas() {
        var rect = layer.getBoundingClientRect();
        var dpr = window.devicePixelRatio || 1;
        var w = Math.max(1, Math.floor(rect.width));
        var h = Math.max(1, Math.floor(rect.height));
        canvas.width = Math.floor(w * dpr);
        canvas.height = Math.floor(h * dpr);
        canvas.style.width = w + 'px';
        canvas.style.height = h + 'px';
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        redrawLocal();
        if (isViewer()) redrawRemote(lastRemotePayload);
    }

    var lastRemotePayload = null;

    function normToPx(nx, ny) {
        var rect = layer.getBoundingClientRect();
        return [nx * rect.width, ny * rect.height];
    }

    function pxToNorm(x, y) {
        var rect = layer.getBoundingClientRect();
        if (rect.width < 1 || rect.height < 1) return [0, 0];
        return [x / rect.width, y / rect.height];
    }

    function redrawLocal() {
        var rect = layer.getBoundingClientRect();
        ctx.clearRect(0, 0, rect.width, rect.height);
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        polylines.forEach(function (line) {
            if (!line || line.length < 2) return;
            ctx.beginPath();
            var p0 = normToPx(line[0][0], line[0][1]);
            ctx.moveTo(p0[0], p0[1]);
            for (var i = 1; i < line.length; i++) {
                var pi = normToPx(line[i][0], line[i][1]);
                ctx.lineTo(pi[0], pi[1]);
            }
            ctx.strokeStyle = 'rgba(250, 204, 21, 0.92)';
            ctx.lineWidth = 3;
            ctx.stroke();
        });
    }

    function hueFromKey(key) {
        var s = String(key);
        var h = 0;
        for (var i = 0; i < s.length; i++) h = (h * 31 + s.charCodeAt(i)) % 360;
        return h;
    }

    function redrawRemote(payload) {
        lastRemotePayload = payload;
        if (!isViewer()) return;
        var rect = layer.getBoundingClientRect();
        ctx.clearRect(0, 0, rect.width, rect.height);
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        if (!payload || !payload.layers) return;
        Object.keys(payload.layers).forEach(function (k) {
            var L = payload.layers[k];
            if (!L || !L.polylines) return;
            var col = 'hsl(' + hueFromKey(k) + ', 82%, 62%)';
            L.polylines.forEach(function (line) {
                if (!line || line.length < 2) return;
                ctx.beginPath();
                var p0 = normToPx(line[0][0], line[0][1]);
                ctx.moveTo(p0[0], p0[1]);
                for (var i = 1; i < line.length; i++) {
                    var pi = normToPx(line[i][0], line[i][1]);
                    ctx.lineTo(pi[0], pi[1]);
                }
                ctx.strokeStyle = col;
                ctx.lineWidth = 3;
                ctx.globalAlpha = 0.9;
                ctx.stroke();
                ctx.globalAlpha = 1;
            });
        });
    }

    function distPointSeg(px, py, x1, y1, x2, y2) {
        var dx = x2 - x1, dy = y2 - y1;
        if (dx === 0 && dy === 0) return Math.hypot(px - x1, py - y1);
        var t = ((px - x1) * dx + (py - y1) * dy) / (dx * dx + dy * dy);
        t = Math.max(0, Math.min(1, t));
        var qx = x1 + t * dx, qy = y1 + t * dy;
        return Math.hypot(px - qx, py - qy);
    }

    function distPointPolyline(px, py, line) {
        var m = Infinity;
        for (var i = 1; i < line.length; i++) {
            var a = normToPx(line[i - 1][0], line[i - 1][1]);
            var b = normToPx(line[i][0], line[i][1]);
            var d = distPointSeg(px, py, a[0], a[1], b[0], b[1]);
            if (d < m) m = d;
        }
        return m;
    }

    function eraseAt(px, py, radius) {
        polylines = polylines.filter(function (line) {
            return distPointPolyline(px, py, line) > radius;
        });
    }

    function schedulePost() {
        if (!isEmitter() || !postUrl || !allowed) return;
        if (postTimer) clearTimeout(postTimer);
        postTimer = setTimeout(function () {
            postTimer = null;
            var body = { polylines: polylines };
            var headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            };
            if (role === 'classroom_guest_emit') {
                body.token = layer.getAttribute('data-guest-token') || '';
            }
            fetch(postUrl, { method: 'POST', headers: headers, body: JSON.stringify(body) }).catch(function () {});
        }, 380);
    }

    function setDrawActive(on) {
        drawEnabled = !!on;
        if (!isEmitter()) return;
        if (drawEnabled) {
            layer.classList.add('mx-share-ann-drawing');
        } else {
            layer.classList.remove('mx-share-ann-drawing');
            drawing = false;
            currentPts = null;
        }
    }

    function updateToolbarTools() {
        var btns = layer.querySelectorAll('.mx-ann-tool-btn');
        btns.forEach(function (b) {
            var t = b.getAttribute('data-mx-ann-tool');
            var on = t === tool;
            b.classList.toggle('ring-2', on);
            b.classList.toggle('ring-amber-400/60', on);
            b.classList.toggle('bg-amber-600/30', on && t === 'pen');
            b.classList.toggle('border-amber-500/50', on && t === 'pen');
            b.classList.toggle('bg-slate-700', !on || t === 'eraser');
        });
    }

    function bindEmitter() {
        layer.querySelectorAll('[data-mx-ann-tool]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                tool = btn.getAttribute('data-mx-ann-tool') || 'pen';
                updateToolbarTools();
            });
        });
        layer.querySelector('[data-mx-ann-action="clear"]').addEventListener('click', function () {
            polylines = [];
            redrawLocal();
            schedulePost();
        });
        layer.querySelector('[data-mx-ann-action="close"]').addEventListener('click', function () {
            setDrawActive(false);
        });

        function pos(ev) {
            var rect = layer.getBoundingClientRect();
            var cx = ev.clientX, cy = ev.clientY;
            if (ev.touches && ev.touches[0]) {
                cx = ev.touches[0].clientX;
                cy = ev.touches[0].clientY;
            }
            return [cx - rect.left, cy - rect.top];
        }

        canvas.addEventListener('pointerdown', function (ev) {
            if (!drawEnabled || !allowed) return;
            ev.preventDefault();
            canvas.setPointerCapture(ev.pointerId);
            drawing = true;
            var p = pos(ev);
            if (tool === 'pen') {
                currentPts = [pxToNorm(p[0], p[1])];
            } else {
                eraseAt(p[0], p[1], 14);
                redrawLocal();
                schedulePost();
            }
        });
        canvas.addEventListener('pointermove', function (ev) {
            if (!drawEnabled || !allowed || !drawing) return;
            ev.preventDefault();
            var p = pos(ev);
            if (tool === 'pen' && currentPts) {
                currentPts.push(pxToNorm(p[0], p[1]));
                redrawLocal();
            } else if (tool === 'eraser') {
                eraseAt(p[0], p[1], 14);
                redrawLocal();
                schedulePost();
            }
        });
        canvas.addEventListener('pointerup', function (ev) {
            if (!drawing) return;
            drawing = false;
            try { canvas.releasePointerCapture(ev.pointerId); } catch (e) {}
            if (tool === 'pen' && currentPts && currentPts.length > 1) {
                polylines.push(currentPts);
                if (polylines.length > 120) polylines.shift();
            }
            currentPts = null;
            redrawLocal();
            schedulePost();
        });
        updateToolbarTools();
    }

    function bindViewer() {
        layer.querySelector('#mx-share-ann-toolbar').style.display = 'none';
    }

    window.__mxShareAnnSetAllowed = function (on) {
        allowed = !!on;
        if (!allowed) {
            setDrawActive(false);
            layer.classList.add('hidden');
            polylines = [];
            redrawLocal();
            if (isEmitter()) schedulePost();
            return;
        }
        if (isEmitter()) {
            layer.classList.add('hidden');
            setDrawActive(false);
        }
    };

    window.__mxShareAnnOpenToolbar = function () {
        if (!allowed || !isEmitter()) return;
        layer.classList.remove('hidden');
        setDrawActive(true);
        resizeCanvas();
    };

    window.__mxShareAnnSetGuestToken = function (tok) {
        layer.setAttribute('data-guest-token', tok || '');
    };

    if (isEmitter()) {
        bindEmitter();
    } else if (isViewer()) {
        bindViewer();
        layer.classList.remove('hidden');
        function poll() {
            if (!pollUrl) return;
            fetch(pollUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (r) { return r.ok ? r.json() : null; })
                .then(function (data) {
                    if (data && data.layers) redrawRemote(data);
                })
                .catch(function () {});
        }
        poll();
        pollTimer = setInterval(poll, 1400);
        window.addEventListener('beforeunload', function () {
            if (pollTimer) clearInterval(pollTimer);
        });
    }

    var ro = typeof ResizeObserver !== 'undefined' ? new ResizeObserver(function () { resizeCanvas(); }) : null;
    if (ro) ro.observe(layer);

    window.addEventListener('resize', function () { resizeCanvas(); });

    if (isViewer() || isEmitter()) {
        requestAnimationFrame(function () { resizeCanvas(); });
    }
})();
</script>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\partials\mx-share-annotation-overlay.blade.php ENDPATH**/ ?>