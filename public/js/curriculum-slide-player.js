/**
 * Muallimx curriculum native slide player (vanilla).
 * window.MXCurriculumSlidePlayer.mount(root, options)
 */
(function (global) {
  'use strict';

  function clamp(n, min, max) {
    return Math.max(min, Math.min(max, n));
  }

  function isTypingTarget(el) {
    if (!el || !el.tagName) return false;
    var tag = el.tagName.toLowerCase();
    return tag === 'input' || tag === 'textarea' || tag === 'select' || el.isContentEditable;
  }

  function qs(root, sel) {
    return root.querySelector(sel);
  }

  function revealOfficeFallback(fallbackEl, playerRoot) {
    if (playerRoot) {
      playerRoot.classList.add('hidden');
      playerRoot.setAttribute('aria-hidden', 'true');
    }
    if (!fallbackEl) return;
    fallbackEl.classList.remove('hidden');
    fallbackEl.removeAttribute('hidden');
    fallbackEl.setAttribute('aria-hidden', 'false');
  }

  function createPlayer(root, opts) {
    opts = opts || {};
    var features = Object.assign({
      thumbs: true,
      keyboard: true,
      fullscreen: true,
      zoom: true,
      laser: true,
      autoplay: true,
      transitions: true
    }, opts.features || {});
    var defaults = Object.assign({
      transition: 'fade',
      autoplayMs: 0,
      minZoom: 1,
      maxZoom: 3.5
    }, opts.defaults || {});

    var manifestUrl = opts.manifestUrl || root.getAttribute('data-manifest-url');
    var fallbackSelector = opts.fallbackSelector || '#mx-office-fallback';
    var fallbackEl = typeof fallbackSelector === 'string'
      ? document.querySelector(fallbackSelector)
      : fallbackSelector;
    var rtl = opts.rtl !== false;

    var thumbsEl = qs(root, '[data-mx-thumbs]');
    var stage = qs(root, '[data-mx-stage]');
    var viewport = qs(root, '[data-mx-viewport]');
    var canvas = qs(root, '[data-mx-canvas]');
    var img = qs(root, '[data-mx-slide-img]');
    var laserLayer = qs(root, '[data-mx-laser-layer]');
    var loadingEl = qs(root, '[data-mx-loading]');
    var statusEl = qs(root, '[data-mx-status]');
    var zoomLabelBtn = qs(root, '[data-mx-zoom-label]');
    var transitionSelect = qs(root, '[data-mx-transition]');
    var btnPrev = qs(root, '[data-mx-prev]');
    var btnNext = qs(root, '[data-mx-next]');
    var btnZoomIn = qs(root, '[data-mx-zoom-in]');
    var btnZoomOut = qs(root, '[data-mx-zoom-out]');
    var btnLaser = qs(root, '[data-mx-laser]');
    var btnAutoplay = qs(root, '[data-mx-autoplay]');
    var btnFullscreen = qs(root, '[data-mx-fullscreen]');

    var slides = [];
    var index = Math.max(1, parseInt(opts.initialIndex || 1, 10) || 1);
    var transition = defaults.transition || 'fade';
    var autoplayMs = defaults.autoplayMs || 0;
    var autoplayTimer = null;
    var autoplayOn = false;
    var laserOn = false;
    var laserDot = null;
    var scale = 1;
    var tx = 0;
    var ty = 0;
    var minZoom = defaults.minZoom || 1;
    var maxZoom = defaults.maxZoom || 3.5;
    var pointers = new Map();
    var pinchStartDist = 0;
    var pinchStartScale = 1;
    var panStart = null;
    var destroyed = false;
    var prefetchCache = {};

    function setLoading(on) {
      if (!loadingEl) return;
      loadingEl.classList.toggle('is-visible', !!on);
      loadingEl.setAttribute('aria-hidden', on ? 'false' : 'true');
    }

    function updateStatus() {
      if (!statusEl) return;
      statusEl.textContent = slides.length ? (index + ' / ' + slides.length) : '—';
    }

    function updateNavDisabled() {
      if (btnPrev) btnPrev.disabled = index <= 1;
      if (btnNext) btnNext.disabled = index >= slides.length;
    }

    function applyZoom() {
      if (!canvas) return;
      canvas.style.transform = 'translate(' + tx + 'px,' + ty + 'px) scale(' + scale + ')';
      if (zoomLabelBtn) zoomLabelBtn.textContent = Math.round(scale * 100) + '%';
    }

    function resetZoom() {
      scale = 1;
      tx = 0;
      ty = 0;
      applyZoom();
    }

    function setZoom(next, cx, cy) {
      var prev = scale;
      scale = clamp(next, minZoom, maxZoom);
      if (typeof cx === 'number' && typeof cy === 'number' && prev > 0 && viewport) {
        var vr = viewport.getBoundingClientRect();
        var ox = cx - (vr.left + vr.width / 2) - tx;
        var oy = cy - (vr.top + vr.height / 2) - ty;
        var ratio = scale / prev;
        tx -= ox * (ratio - 1);
        ty -= oy * (ratio - 1);
      }
      if (scale <= 1.02) {
        scale = 1;
        tx = 0;
        ty = 0;
      }
      applyZoom();
    }

    function markActiveThumb() {
      if (!thumbsEl) return;
      var nodes = thumbsEl.querySelectorAll('[data-mx-thumb]');
      for (var i = 0; i < nodes.length; i++) {
        var n = nodes[i];
        var active = parseInt(n.getAttribute('data-index'), 10) === index;
        n.classList.toggle('is-active', active);
        n.setAttribute('aria-current', active ? 'true' : 'false');
      }
    }

    function prefetch(url) {
      if (!url || prefetchCache[url]) return;
      prefetchCache[url] = true;
      var pre = new Image();
      pre.decoding = 'async';
      pre.src = url;
    }

    function prefetchNeighbors() {
      var cur = slides[index - 1];
      if (!cur) return;
      var prev = slides[index - 2];
      var next = slides[index];
      if (prev) prefetch(prev.image_url);
      if (next) prefetch(next.image_url);
    }

    function stopAutoplay() {
      autoplayOn = false;
      if (autoplayTimer) {
        clearInterval(autoplayTimer);
        autoplayTimer = null;
      }
      if (btnAutoplay) {
        btnAutoplay.setAttribute('aria-pressed', 'false');
        btnAutoplay.classList.remove('is-active');
        var icon = btnAutoplay.querySelector('i');
        if (icon) {
          icon.classList.remove('fa-pause');
          icon.classList.add('fa-play');
        }
      }
    }

    function startAutoplay() {
      if (!features.autoplay) return;
      stopAutoplay();
      var ms = autoplayMs > 0 ? autoplayMs : 4000;
      autoplayOn = true;
      if (btnAutoplay) {
        btnAutoplay.setAttribute('aria-pressed', 'true');
        btnAutoplay.classList.add('is-active');
        var icon = btnAutoplay.querySelector('i');
        if (icon) {
          icon.classList.remove('fa-play');
          icon.classList.add('fa-pause');
        }
      }
      autoplayTimer = setInterval(function () {
        if (index >= slides.length) {
          stopAutoplay();
          return;
        }
        goTo(index + 1, { fromAutoplay: true });
      }, ms);
    }

    function showSlide(slide, optsShow) {
      optsShow = optsShow || {};
      if (!img || !slide) return Promise.resolve();
      setLoading(true);
      resetZoom();

      var useTransition = features.transitions && transition !== 'none' && !optsShow.instant;
      var outClass = transition === 'slide' ? 'is-slide-out' : 'is-fade-out';

      function swap() {
        return new Promise(function (resolve, reject) {
          var done = false;
          function finishOk() {
            if (done) return;
            done = true;
            img.classList.remove('is-fade-out', 'is-slide-out', 'is-slide-in');
            setLoading(false);
            resolve();
          }
          function finishErr() {
            if (done) return;
            done = true;
            setLoading(false);
            reject(new Error('slide image failed'));
          }
          img.onload = finishOk;
          img.onerror = finishErr;
          img.alt = 'شريحة ' + slide.index;
          img.src = slide.image_url;
          if (img.complete && img.naturalWidth > 0) {
            finishOk();
          }
        });
      }

      if (!useTransition) {
        return swap().then(function () {
          prefetchNeighbors();
        });
      }

      img.classList.add(outClass);
      return new Promise(function (resolve) {
        setTimeout(function () {
          if (transition === 'slide') {
            img.classList.remove('is-slide-out');
            img.classList.add('is-slide-in');
          }
          swap().then(function () {
            requestAnimationFrame(function () {
              img.classList.remove('is-slide-in', 'is-fade-out', 'is-slide-out');
            });
            prefetchNeighbors();
            resolve();
          }).catch(function () {
            resolve();
            failToOffice();
          });
        }, 160);
      });
    }

    function goTo(nextIndex, navOpts) {
      navOpts = navOpts || {};
      nextIndex = clamp(nextIndex, 1, slides.length || 1);
      if (!slides.length) return;
      if (nextIndex === index && img && img.src) {
        updateStatus();
        updateNavDisabled();
        markActiveThumb();
        return;
      }
      if (!navOpts.fromAutoplay) stopAutoplay();
      index = nextIndex;
      updateStatus();
      updateNavDisabled();
      markActiveThumb();
      showSlide(slides[index - 1], navOpts);
    }

    function failToOffice() {
      stopAutoplay();
      revealOfficeFallback(fallbackEl, root);
    }

    function buildThumbs() {
      if (!features.thumbs || !thumbsEl) return;
      thumbsEl.innerHTML = '';
      for (var i = 0; i < slides.length; i++) {
        var s = slides[i];
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'mx-sp-thumb';
        btn.setAttribute('data-mx-thumb', '1');
        btn.setAttribute('data-index', String(s.index));
        btn.setAttribute('role', 'listitem');
        btn.setAttribute('aria-label', 'الانتقال إلى الشريحة ' + s.index);
        var badge = document.createElement('span');
        badge.className = 'mx-sp-thumb-index';
        badge.textContent = String(s.index);
        btn.appendChild(badge);
        var tImg = document.createElement('img');
        tImg.alt = '';
        tImg.loading = 'lazy';
        tImg.decoding = 'async';
        tImg.src = s.thumb_url || s.image_url;
        btn.appendChild(tImg);
        btn.addEventListener('click', function (ev) {
          var idx = parseInt(ev.currentTarget.getAttribute('data-index'), 10);
          goTo(idx);
        });
        thumbsEl.appendChild(btn);
      }
    }

    function onKeyDown(e) {
      if (!features.keyboard || destroyed) return;
      if (isTypingTarget(e.target)) return;
      if (!root.contains(document.activeElement) && document.activeElement !== document.body) {
        // Still allow keys when focus is inside player or body after click
        if (!root.contains(e.target) && e.target !== document.body && e.target !== document.documentElement) {
          return;
        }
      }
      var key = e.key;
      if (key === 'ArrowLeft') {
        e.preventDefault();
        goTo(rtl ? index + 1 : index - 1);
      } else if (key === 'ArrowRight') {
        e.preventDefault();
        goTo(rtl ? index - 1 : index + 1);
      } else if (key === ' ' || key === 'Spacebar' || key === 'PageDown') {
        e.preventDefault();
        goTo(index + 1);
      } else if (key === 'PageUp' || key === 'Backspace') {
        e.preventDefault();
        goTo(index - 1);
      } else if (key === 'Home') {
        e.preventDefault();
        goTo(1);
      } else if (key === 'End') {
        e.preventDefault();
        goTo(slides.length);
      } else if ((key === 'f' || key === 'F') && features.fullscreen) {
        e.preventDefault();
        toggleFullscreen();
      } else if ((key === '+' || key === '=') && features.zoom) {
        e.preventDefault();
        setZoom(scale + 0.25);
      } else if ((key === '-' || key === '_') && features.zoom) {
        e.preventDefault();
        setZoom(scale - 0.25);
      } else if (key === '0' && features.zoom) {
        e.preventDefault();
        resetZoom();
      } else if ((key === 'l' || key === 'L') && features.laser) {
        e.preventDefault();
        toggleLaser();
      } else if ((key === 'p' || key === 'P') && features.autoplay) {
        e.preventDefault();
        if (autoplayOn) stopAutoplay();
        else startAutoplay();
      } else if (key === 'Escape') {
        if (autoplayOn) stopAutoplay();
        if (laserOn) toggleLaser(false);
      }
    }

    function toggleFullscreen() {
      if (!features.fullscreen) return;
      var doc = document;
      if (!doc.fullscreenElement && !doc.webkitFullscreenElement) {
        var req = root.requestFullscreen || root.webkitRequestFullscreen;
        if (req) req.call(root);
      } else {
        var exit = doc.exitFullscreen || doc.webkitExitFullscreen;
        if (exit) exit.call(doc);
      }
    }

    function ensureLaserDot() {
      if (laserDot || !laserLayer) return;
      laserDot = document.createElement('div');
      laserDot.className = 'mx-sp-laser-dot';
      laserLayer.appendChild(laserDot);
    }

    function toggleLaser(force) {
      if (!features.laser) return;
      laserOn = typeof force === 'boolean' ? force : !laserOn;
      root.classList.toggle('is-laser-on', laserOn);
      if (btnLaser) {
        btnLaser.setAttribute('aria-pressed', laserOn ? 'true' : 'false');
        btnLaser.classList.toggle('is-active', laserOn);
      }
      if (!laserOn && laserDot) {
        laserDot.classList.remove('is-visible');
      } else if (laserOn) {
        ensureLaserDot();
      }
    }

    function onStagePointerMove(e) {
      if (!laserOn || !laserDot || !stage) return;
      var rect = stage.getBoundingClientRect();
      if (!rect.width || !rect.height) return;
      var x = ((e.clientX - rect.left) / rect.width) * 100;
      var y = ((e.clientY - rect.top) / rect.height) * 100;
      laserDot.style.left = clamp(x, 0, 100) + '%';
      laserDot.style.top = clamp(y, 0, 100) + '%';
      laserDot.classList.add('is-visible');
    }

    function onStagePointerLeave() {
      if (laserDot) laserDot.classList.remove('is-visible');
    }

    function distance(a, b) {
      var dx = a.clientX - b.clientX;
      var dy = a.clientY - b.clientY;
      return Math.sqrt(dx * dx + dy * dy);
    }

    function onPointerDown(e) {
      if (!features.zoom || !viewport) return;
      if (laserOn) return;
      pointers.set(e.pointerId, e);
      if (viewport.setPointerCapture) viewport.setPointerCapture(e.pointerId);
      if (pointers.size === 2) {
        var pts = Array.from(pointers.values());
        pinchStartDist = distance(pts[0], pts[1]) || 1;
        pinchStartScale = scale;
        panStart = null;
      } else if (pointers.size === 1 && scale > 1.02) {
        panStart = { x: e.clientX - tx, y: e.clientY - ty };
        viewport.classList.add('is-panning');
      }
    }

    function onPointerMove(e) {
      if (!features.zoom) return;
      if (laserOn) {
        onStagePointerMove(e);
        return;
      }
      if (!pointers.has(e.pointerId)) return;
      pointers.set(e.pointerId, e);
      if (pointers.size === 2) {
        var pts = Array.from(pointers.values());
        var dist = distance(pts[0], pts[1]) || 1;
        setZoom(pinchStartScale * (dist / pinchStartDist), (pts[0].clientX + pts[1].clientX) / 2, (pts[0].clientY + pts[1].clientY) / 2);
      } else if (pointers.size === 1 && panStart && scale > 1.02) {
        tx = e.clientX - panStart.x;
        ty = e.clientY - panStart.y;
        applyZoom();
      }
    }

    function onPointerUp(e) {
      pointers.delete(e.pointerId);
      if (pointers.size < 2) {
        pinchStartDist = 0;
      }
      if (pointers.size === 0) {
        panStart = null;
        if (viewport) viewport.classList.remove('is-panning');
      }
    }

    function onWheel(e) {
      if (!features.zoom) return;
      if (!e.ctrlKey && !e.metaKey) return;
      e.preventDefault();
      var delta = e.deltaY > 0 ? -0.12 : 0.12;
      setZoom(scale + delta, e.clientX, e.clientY);
    }

    function bindUi() {
      if (btnPrev) btnPrev.addEventListener('click', function () { goTo(index - 1); });
      if (btnNext) btnNext.addEventListener('click', function () { goTo(index + 1); });
      if (btnZoomIn) btnZoomIn.addEventListener('click', function () { setZoom(scale + 0.25); });
      if (btnZoomOut) btnZoomOut.addEventListener('click', function () { setZoom(scale - 0.25); });
      if (zoomLabelBtn) zoomLabelBtn.addEventListener('click', function () { resetZoom(); });
      if (btnLaser) btnLaser.addEventListener('click', function () { toggleLaser(); });
      if (btnAutoplay) {
        btnAutoplay.addEventListener('click', function () {
          if (autoplayOn) stopAutoplay();
          else startAutoplay();
        });
      }
      if (btnFullscreen) btnFullscreen.addEventListener('click', function () { toggleFullscreen(); });
      if (transitionSelect) {
        transitionSelect.addEventListener('change', function () {
          transition = transitionSelect.value || 'fade';
        });
      }
      if (features.keyboard) {
        document.addEventListener('keydown', onKeyDown);
      }
      if (stage) {
        stage.addEventListener('pointermove', onStagePointerMove);
        stage.addEventListener('pointerleave', onStagePointerLeave);
      }
      if (viewport && features.zoom) {
        viewport.addEventListener('pointerdown', onPointerDown);
        viewport.addEventListener('pointermove', onPointerMove);
        viewport.addEventListener('pointerup', onPointerUp);
        viewport.addEventListener('pointercancel', onPointerUp);
        viewport.addEventListener('wheel', onWheel, { passive: false });
      }
    }

    function destroy() {
      destroyed = true;
      stopAutoplay();
      document.removeEventListener('keydown', onKeyDown);
    }

    function bootFromManifest(data) {
      if (!data || !Array.isArray(data.slides) || !data.slides.length) {
        failToOffice();
        return;
      }
      slides = data.slides.map(function (s) {
        return {
          index: parseInt(s.index, 10),
          image_url: s.image_url,
          thumb_url: s.thumb_url || null
        };
      }).filter(function (s) {
        return s.index > 0 && !!s.image_url;
      });
      if (!slides.length) {
        failToOffice();
        return;
      }
      if (data.player && data.player.transition) {
        transition = data.player.transition;
        if (transitionSelect) transitionSelect.value = transition;
      }
      if (data.player && typeof data.player.autoplay_ms === 'number') {
        autoplayMs = data.player.autoplay_ms;
      }
      buildThumbs();
      goTo(clamp(index, 1, slides.length), { instant: true });
      if (stage) stage.focus({ preventScroll: true });
    }

    bindUi();
    applyZoom();
    setLoading(true);

    if (!manifestUrl) {
      failToOffice();
      return { destroy: destroy, goTo: goTo };
    }

    fetch(manifestUrl, {
      credentials: 'same-origin',
      headers: { 'Accept': 'application/json' }
    }).then(function (res) {
      if (!res.ok) throw new Error('manifest ' + res.status);
      return res.json();
    }).then(bootFromManifest).catch(function () {
      failToOffice();
    });

    return {
      destroy: destroy,
      goTo: goTo,
      getIndex: function () { return index; },
      getSlideCount: function () { return slides.length; }
    };
  }

  function mount(rootOrSelector, options) {
    var root = typeof rootOrSelector === 'string'
      ? document.querySelector(rootOrSelector)
      : rootOrSelector;
    if (!root) return null;
    return createPlayer(root, options || {});
  }

  global.MXCurriculumSlidePlayer = {
    mount: mount
  };
})(typeof window !== 'undefined' ? window : this);
