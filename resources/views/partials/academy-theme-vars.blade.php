@php
    $acadTheme = config('academy-theme');
@endphp
<style>
    :root {
        --acad-navy: {{ $acadTheme['navy'] }};
        --acad-navy-mid: {{ $acadTheme['navy_mid'] }};
        --acad-navy-gradient: {{ $acadTheme['navy_gradient'] }};
        --acad-navy-deep: {{ $acadTheme['navy_deep'] }};
        --acad-navy-rgb: {{ $acadTheme['navy_rgb'] }};
        --acad-navy-mid-rgb: {{ $acadTheme['navy_mid_rgb'] }};
    }
</style>
