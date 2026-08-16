@php
    $tzName = $name ?? 'timezone';
    $tzId = $id ?? 'timezoneSelect';
    $tzClass = $class ?? 'w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 px-4 py-2.5 text-sm';
    $tzOptions = \App\Support\AppTimezone::commonZones();
    $tzCurrent = old($tzName, $value ?? auth()->user()?->timezoneCode() ?? \App\Support\AppTimezone::academy());
    if ($tzCurrent && ! array_key_exists($tzCurrent, $tzOptions)) {
        $tzOptions = [$tzCurrent => $tzCurrent] + $tzOptions;
    }
    $tzRequired = $required ?? true;
    $tzLabel = $label ?? (app()->getLocale() === 'ar' ? 'المنطقة الزمنية للميعاد' : 'Appointment timezone');
    $tzHint = $hint ?? (app()->getLocale() === 'ar'
        ? 'الساعة اللي هتكتبها هتتحسب حسب المنطقة دي. مثال: أمريكا — نيويورك + 6:00 مساءً = المقابلة 6 مساءً بتوقيت نيويورك.'
        : 'The time you type is in this zone. Example: America/New York + 6:00 PM means 6 PM Eastern.');
@endphp
<div>
    <label for="{{ $tzId }}" class="{{ $labelClass ?? 'block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1' }}">{{ $tzLabel }}</label>
    <select name="{{ $tzName }}" id="{{ $tzId }}" class="{{ $tzClass }}" data-timezone-select @if($tzRequired) required @endif>
        @foreach ($tzOptions as $tzIdValue => $tzOptionLabel)
            <option value="{{ $tzIdValue }}" @selected($tzCurrent === $tzIdValue)>{{ $tzOptionLabel }}</option>
        @endforeach
    </select>
    @if($tzHint)
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $tzHint }}</p>
    @endif
    @error($tzName)<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>
