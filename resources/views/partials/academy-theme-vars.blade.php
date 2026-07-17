@php
    $acadTheme = config('academy-theme');
@endphp
<style>
    :root {
        --acad-navy: {{ $acadTheme['navy'] }};
        --acad-navy-mid: {{ $acadTheme['navy_mid'] }};
        --acad-navy-gradient: {{ $acadTheme['navy_gradient'] }};
        --acad-navy-deep: {{ $acadTheme['navy_deep'] }};
        --acad-navy-hero-mid: {{ $acadTheme['navy_hero_mid'] }};
        --acad-navy-rgb: {{ $acadTheme['navy_rgb'] }};
        --acad-navy-mid-rgb: {{ $acadTheme['navy_mid_rgb'] }};
        --acad-blue: {{ $acadTheme['blue'] }};
        --acad-blue-dark: {{ $acadTheme['blue_dark'] }};
        --acad-cyan: {{ $acadTheme['cyan'] }};
        --acad-cyan-rgb: {{ $acadTheme['cyan_rgb'] }};
        --acad-yellow: {{ $acadTheme['yellow'] }};
        --acad-yellow-rgb: {{ $acadTheme['yellow_rgb'] }};
        --acad-ink: {{ $acadTheme['ink'] }};
    }
</style>
