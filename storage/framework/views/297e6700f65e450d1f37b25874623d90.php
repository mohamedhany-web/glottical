
<script>
(function (w) {
    var ALLOW = 'camera; microphone; fullscreen; display-capture; autoplay; clipboard-write';
    w.muallimxEnsureJitsiIframeMediaAllow = function (rootEl) {
        if (!rootEl || typeof MutationObserver === 'undefined') return;
        function patch(ifr) {
            if (!ifr || !ifr.getAttribute || String(ifr.tagName).toUpperCase() !== 'IFRAME') return;
            ifr.setAttribute('allow', ALLOW);
            ifr.setAttribute('allowfullscreen', 'true');
        }
        var existing = rootEl.querySelector('iframe');
        if (existing) {
            patch(existing);
            return;
        }
        var mo = new MutationObserver(function () {
            var f = rootEl.querySelector('iframe');
            if (f) {
                patch(f);
                mo.disconnect();
            }
        });
        mo.observe(rootEl, { childList: true, subtree: true });
    };
})(window);
</script>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\partials\jitsi-iframe-media-allow.blade.php ENDPATH**/ ?>