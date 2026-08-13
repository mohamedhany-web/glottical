{{-- كشف وحفظ المنطقة الزمنية من المتصفح --}}
<script>
(function () {
    try {
        var tz = (Intl.DateTimeFormat().resolvedOptions().timeZone || '').trim();
        if (!tz) return;

        var meta = document.querySelector('meta[name="csrf-token"]');
        var token = meta ? meta.getAttribute('content') : '';
        var syncUrl = @json($timezoneSyncUrl ?? null);
        var input = document.getElementById('timezone_auto');
        if (input) input.value = tz;

        var selects = document.querySelectorAll('[data-timezone-select]');
        selects.forEach(function (el) {
            if (!el.value && tz) {
                var opt = Array.prototype.find.call(el.options, function (o) { return o.value === tz; });
                if (opt) el.value = tz;
            }
        });

        if (!syncUrl || !token) return;

        var key = 'glottical_tz_synced';
        var last = sessionStorage.getItem(key);
        if (last === tz) return;

        fetch(syncUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ timezone: tz, force: false })
        }).then(function (r) { return r.json(); }).then(function (data) {
            if (data && data.ok) sessionStorage.setItem(key, tz);
        }).catch(function () {});
    } catch (e) {}
})();
</script>
