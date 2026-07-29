@php
    $isRtl = $isRtl ?? (app()->getLocale() === 'ar');
    $langSwitch = $langSwitch ?? fn (string $lang) => request()->fullUrlWithQuery(array_merge(request()->query(), ['lang' => $lang]));
    $currentLabel = $isRtl ? 'العربية' : 'English';
@endphp
<div class="sana-nav__lang-dd" data-lang-dropdown>
    <button
        type="button"
        class="sana-nav__lang-btn"
        aria-expanded="false"
        aria-haspopup="listbox"
        aria-label="{{ $isRtl ? 'تغيير اللغة' : 'Change language' }}"
    >
        <i class="fas fa-globe" aria-hidden="true"></i>
        <span class="sana-nav__lang-btn-label">{{ $currentLabel }}</span>
        <span class="sana-nav__lang-btn-code" aria-hidden="true">{{ $isRtl ? 'ع' : 'EN' }}</span>
        <i class="fas fa-chevron-down sana-nav__lang-chevron" aria-hidden="true"></i>
    </button>
    <div class="sana-nav__lang-menu" role="listbox" aria-label="{{ $isRtl ? 'اختر اللغة' : 'Choose language' }}">
        <a href="{{ $langSwitch('ar') }}" role="option" hreflang="ar" lang="ar" class="sana-nav__lang-option {{ $isRtl ? 'is-active' : '' }}">
            <span>العربية</span>
            @if($isRtl)<i class="fas fa-check" aria-hidden="true"></i>@endif
        </a>
        <a href="{{ $langSwitch('en') }}" role="option" hreflang="en" lang="en" class="sana-nav__lang-option {{ ! $isRtl ? 'is-active' : '' }}">
            <span>English</span>
            @if(! $isRtl)<i class="fas fa-check" aria-hidden="true"></i>@endif
        </a>
    </div>
</div>
