@php
    $usName = $name ?? 'us_state';
    $usId = $id ?? 'usStateSelect';
    $usClass = $class ?? 'w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm';
    $usStates = \App\Support\AppTimezone::usStates();
    $usCurrent = old($usName, $value ?? '');
    $usRequired = $required ?? false;
    $usLabel = $label ?? (app()->getLocale() === 'ar' ? 'ولاية الإقامة (أمريكا)' : 'US state of residence');
    $usHint = $hint ?? (app()->getLocale() === 'ar'
        ? 'نحسب توقيتكم تلقائيًا من الولاية لعرض المواعيد المناسبة.'
        : 'We infer your timezone from the state to show suitable slots.');
    $tzTarget = $timezoneSelectId ?? null;
@endphp
<div>
    <label for="{{ $usId }}" class="{{ $labelClass ?? 'block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1' }}">{{ $usLabel }}</label>
    <select name="{{ $usName }}" id="{{ $usId }}" class="{{ $usClass }}" data-us-state-select @if($tzTarget) data-timezone-target="{{ $tzTarget }}" @endif @if($usRequired) required @endif>
        <option value="">{{ app()->getLocale() === 'ar' ? '— اختياري —' : '— optional —' }}</option>
        @foreach ($usStates as $stateName => $stateTz)
            <option value="{{ $stateName }}" data-timezone="{{ $stateTz }}" @selected($usCurrent === $stateName)>
                {{ $stateName }} ({{ \App\Support\AppTimezone::label($stateTz) }})
            </option>
        @endforeach
    </select>
    @if($usHint)
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $usHint }}</p>
    @endif
    @error($usName)<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>
@once
<script>
(function () {
    document.querySelectorAll('[data-us-state-select]').forEach(function (sel) {
        if (sel.dataset.bound === '1') return;
        sel.dataset.bound = '1';
        sel.addEventListener('change', function () {
            var opt = sel.options[sel.selectedIndex];
            var tz = opt ? opt.getAttribute('data-timezone') : '';
            var targetId = sel.getAttribute('data-timezone-target');
            if (!tz || !targetId) return;
            var target = document.getElementById(targetId);
            if (!target) return;
            var match = Array.prototype.find.call(target.options, function (o) { return o.value === tz; });
            if (match) target.value = tz;
            target.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });
})();
</script>
@endonce
