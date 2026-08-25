/**
 * مزامنة سبورة Excalidraw بين المعلّم والطالب.
 * القناة الأساسية: LiveKit Data (فورية + مجزّأة).
 * الاحتياط: لقطة HTTP في الكاش لمتأخرين عن اللحاق / إعادة الاتصال.
 */
(function (global) {
    'use strict';

    var TOPIC = 'mx-wb';
    var CHUNK = 11000;
    var DRAW_MS = 48;
    var IDLE_MS = 220;
    var HTTP_MS = 900;
    var POLL_MS = 2200;

    function now() {
        return Date.now();
    }

    function safeJsonParse(str) {
        try {
            return JSON.parse(str);
        } catch (e) {
            return null;
        }
    }

    function compactAppState(appState) {
        if (!appState || typeof appState !== 'object') return null;
        return {
            viewBackgroundColor: appState.viewBackgroundColor || '#ffffff',
            gridSize: appState.gridSize || null,
            theme: appState.theme || 'light',
        };
    }

    function elementsSignature(elements) {
        if (!Array.isArray(elements) || !elements.length) return '0';
        var n = elements.length;
        var last = elements[n - 1] || {};
        var sum = 0;
        for (var i = 0; i < n; i++) {
            var el = elements[i] || {};
            sum = (sum + (el.version || 0) + (el.versionNonce || 0)) >>> 0;
        }
        return n + ':' + sum + ':' + (last.id || '') + ':' + (last.updated || last.version || 0);
    }

    function mergeElements(a, b) {
        var map = Object.create(null);
        function ingest(list) {
            if (!Array.isArray(list)) return;
            for (var i = 0; i < list.length; i++) {
                var el = list[i];
                if (!el || !el.id) continue;
                var prev = map[el.id];
                if (!prev) {
                    map[el.id] = el;
                    continue;
                }
                var pv = prev.version || 0;
                var ev = el.version || 0;
                if (ev > pv || (ev === pv && (el.versionNonce || 0) > (prev.versionNonce || 0))) {
                    map[el.id] = el;
                }
            }
        }
        ingest(a);
        ingest(b);
        var out = [];
        for (var id in map) {
            if (Object.prototype.hasOwnProperty.call(map, id) && !map[id].isDeleted) {
                out.push(map[id]);
            }
        }
        return out;
    }

    function utf8Bytes(str) {
        if (typeof TextEncoder !== 'undefined') {
            return new TextEncoder().encode(str);
        }
        var utf8 = unescape(encodeURIComponent(str));
        var arr = new Uint8Array(utf8.length);
        for (var i = 0; i < utf8.length; i++) arr[i] = utf8.charCodeAt(i);
        return arr;
    }

    function utf8String(buf) {
        if (typeof TextDecoder !== 'undefined') {
            return new TextDecoder().decode(buf);
        }
        var s = '';
        var u8 = buf instanceof Uint8Array ? buf : new Uint8Array(buf);
        for (var i = 0; i < u8.length; i++) s += String.fromCharCode(u8[i]);
        try {
            return decodeURIComponent(escape(s));
        } catch (e) {
            return s;
        }
    }

    function publishLk(obj, reliable) {
        if (typeof global.__mxLkPublishData !== 'function') return false;
        try {
            return !!global.__mxLkPublishData(obj, { reliable: reliable !== false, topic: TOPIC });
        } catch (e) {
            return false;
        }
    }

    function attach(opts) {
        opts = opts || {};
        var getApi = opts.getApi;
        var canEmit = !!opts.canEmit;
        var canReceive = opts.canReceive !== false;
        var stateUrl = opts.stateUrl || '';
        var pushUrl = opts.pushUrl || '';
        var csrf = opts.csrf || '';
        var role = opts.role || 'participant';
        var mergeRemote = !!opts.mergeRemote;

        var applying = false;
        var lastSig = '';
        var lastAppliedV = 0;
        var localV = 0;
        var drawTimer = null;
        var idleTimer = null;
        var httpTimer = null;
        var pollTimer = null;
        var chunkBuf = Object.create(null);
        var destroyed = false;
        var pendingHttp = null;

        function api() {
            try {
                return typeof getApi === 'function' ? getApi() : null;
            } catch (e) {
                return null;
            }
        }

        function snapshot(includeFiles) {
            var a = api();
            if (!a || typeof a.getSceneElements !== 'function') return null;
            var elements = a.getSceneElements();
            var appState = compactAppState(typeof a.getAppState === 'function' ? a.getAppState() : null);
            var files = null;
            if (includeFiles && typeof a.getFiles === 'function') {
                try {
                    files = a.getFiles() || null;
                } catch (e) {
                    files = null;
                }
            }
            return { elements: elements, appState: appState, files: files };
        }

        function applyRemote(payload, force) {
            if (!canReceive || !payload) return;
            var a = api();
            if (!a || typeof a.updateScene !== 'function') return;
            var v = Number(payload.v || 0);
            if (!force && v && v <= lastAppliedV) return;
            var remoteEls = Array.isArray(payload.elements) ? payload.elements : [];
            var nextEls = remoteEls;
            if (mergeRemote) {
                var local = snapshot(false);
                nextEls = mergeElements(local && local.elements, remoteEls);
            }
            applying = true;
            try {
                var scene = {
                    elements: nextEls,
                    commitToHistory: false,
                };
                if (payload.appState && typeof payload.appState === 'object') {
                    scene.appState = {
                        viewBackgroundColor: payload.appState.viewBackgroundColor || '#ffffff',
                        gridSize: payload.appState.gridSize || null,
                        theme: payload.appState.theme || 'light',
                    };
                }
                if (payload.files && typeof payload.files === 'object') {
                    scene.files = payload.files;
                }
                a.updateScene(scene);
                if (v) lastAppliedV = Math.max(lastAppliedV, v);
                lastSig = elementsSignature(nextEls);
            } catch (e) {
                try {
                    // مسار احتياطي بدون appState إن فشل التطبيق الكامل
                    a.updateScene({ elements: nextEls, commitToHistory: false });
                    if (v) lastAppliedV = Math.max(lastAppliedV, v);
                    lastSig = elementsSignature(nextEls);
                } catch (e2) {}
            } finally {
                setTimeout(function () {
                    applying = false;
                }, 0);
            }
        }

        function buildWire(includeFiles) {
            var snap = snapshot(includeFiles);
            if (!snap) return null;
            localV = Math.max(localV + 1, now());
            return {
                t: 'wb',
                v: localV,
                role: role,
                elements: snap.elements,
                appState: snap.appState,
                files: includeFiles ? snap.files : null,
            };
        }

        function sendLk(payload) {
            if (!payload) return;
            var json = JSON.stringify(payload);
            if (json.length <= CHUNK) {
                publishLk(payload, true);
                return;
            }
            var id = String(payload.v) + '-' + Math.random().toString(36).slice(2, 8);
            var parts = Math.ceil(json.length / CHUNK);
            for (var i = 0; i < parts; i++) {
                publishLk(
                    {
                        t: 'wb_chunk',
                        id: id,
                        i: i,
                        n: parts,
                        d: json.slice(i * CHUNK, (i + 1) * CHUNK),
                    },
                    true
                );
            }
        }

        function pushHttp(payload) {
            if (!pushUrl || !payload) return;
            pendingHttp = payload;
            if (httpTimer) return;
            httpTimer = setTimeout(function () {
                httpTimer = null;
                var body = pendingHttp;
                pendingHttp = null;
                if (!body) return;
                var headers = {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                };
                if (csrf) headers['X-CSRF-TOKEN'] = csrf;
                fetch(pushUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: headers,
                    body: JSON.stringify({
                        v: body.v,
                        elements: body.elements,
                        appState: body.appState,
                        files: body.files || null,
                    }),
                }).catch(function () {});
            }, HTTP_MS);
        }

        function emit(reason) {
            if (!canEmit || applying || destroyed) return;
            var includeFiles = reason === 'idle' || reason === 'flush';
            var snap = snapshot(false);
            if (!snap) return;
            var sig = elementsSignature(snap.elements);
            if (reason !== 'flush' && sig === lastSig) return;
            lastSig = sig;
            var wire = buildWire(includeFiles);
            if (!wire) return;
            sendLk(wire);
            if (includeFiles) {
                pushHttp(wire);
            } else if (!httpTimer) {
                // أثناء الرسم: أخّر HTTP قليلاً ثم ادفع لقطة كاملة
                var soft = buildWire(true);
                pushHttp(soft || wire);
            }
        }

        function scheduleEmit() {
            if (!canEmit || applying || destroyed) return;
            if (!drawTimer) {
                drawTimer = setTimeout(function () {
                    drawTimer = null;
                    emit('draw');
                }, DRAW_MS);
            }
            if (idleTimer) clearTimeout(idleTimer);
            idleTimer = setTimeout(function () {
                idleTimer = null;
                emit('idle');
            }, IDLE_MS);
        }

        function onLkMessage(msg) {
            if (!msg || destroyed) return;
            if (msg.t === 'wb_chunk') {
                var b = chunkBuf[msg.id] || (chunkBuf[msg.id] = { n: msg.n, parts: [] });
                b.parts[msg.i] = msg.d || '';
                var ready = b.parts.length === b.n;
                if (ready) {
                    for (var i = 0; i < b.n; i++) {
                        if (typeof b.parts[i] !== 'string') {
                            ready = false;
                            break;
                        }
                    }
                }
                if (!ready) return;
                var full = b.parts.join('');
                delete chunkBuf[msg.id];
                var parsed = safeJsonParse(full);
                if (parsed && parsed.t === 'wb') applyRemote(parsed, false);
                return;
            }
            if (msg.t === 'wb') applyRemote(msg, false);
            if (msg.t === 'wb_req' && canEmit) {
                emit('flush');
            }
        }

        function onLkRaw(ev) {
            var detail = ev && ev.detail;
            if (!detail) return;
            if (detail.topic && detail.topic !== TOPIC) return;
            var data = detail.data;
            if (data == null) return;
            var msg = typeof data === 'string' ? safeJsonParse(data) : data;
            if (!msg && detail.payload) {
                try {
                    msg = safeJsonParse(utf8String(detail.payload));
                } catch (e) {
                    msg = null;
                }
            }
            onLkMessage(msg);
        }

        function pullHttp(force) {
            if (!stateUrl || destroyed) return;
            fetch(stateUrl, {
                credentials: 'same-origin',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            })
                .then(function (r) {
                    return r.ok ? r.json() : null;
                })
                .then(function (data) {
                    if (!data || !Array.isArray(data.elements)) return;
                    applyRemote(data, !!force);
                })
                .catch(function () {});
        }

        function onPointerUp() {
            if (canEmit) emit('idle');
        }

        if (canReceive) {
            global.addEventListener('mx-lk-data', onLkRaw);
            // اطلب لقطة فورية من الطرف الآخر عند الاتصال
            setTimeout(function () {
                publishLk({ t: 'wb_req', role: role }, true);
                pullHttp(true);
            }, 600);
            pollTimer = setInterval(function () {
                pullHttp(false);
            }, POLL_MS);
        }

        global.addEventListener('pointerup', onPointerUp, true);
        global.addEventListener('mouseup', onPointerUp, true);
        global.addEventListener('touchend', onPointerUp, true);

        return {
            onLocalChange: function () {
                scheduleEmit();
            },
            flush: function () {
                emit('flush');
            },
            requestRemote: function () {
                publishLk({ t: 'wb_req', role: role }, true);
                pullHttp(true);
            },
            applyPayload: applyRemote,
            destroy: function () {
                destroyed = true;
                global.removeEventListener('mx-lk-data', onLkRaw);
                global.removeEventListener('pointerup', onPointerUp, true);
                global.removeEventListener('mouseup', onPointerUp, true);
                global.removeEventListener('touchend', onPointerUp, true);
                if (drawTimer) clearTimeout(drawTimer);
                if (idleTimer) clearTimeout(idleTimer);
                if (httpTimer) clearTimeout(httpTimer);
                if (pollTimer) clearInterval(pollTimer);
            },
        };
    }

    global.MxClassroomWhiteboardSync = {
        TOPIC: TOPIC,
        attach: attach,
        mergeElements: mergeElements,
        elementsSignature: elementsSignature,
    };
})(typeof window !== 'undefined' ? window : this);
