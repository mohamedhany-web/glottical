@php
    $t = config('academy-theme');
@endphp
acad: {
    blue: '{{ $t['blue'] }}',
    blueDark: '{{ $t['blue_dark'] }}',
    blueSoft: '{{ $t['blue_soft'] }}',
    cyan: '{{ $t['cyan'] }}',
    yellow: '{{ $t['yellow'] }}',
    yellowSoft: '{{ $t['yellow_soft'] }}',
    gray: '{{ $t['gray'] }}',
    ink: '{{ $t['ink'] }}',
    navy: '{{ $t['navy'] }}',
    navyMid: '{{ $t['navy_mid'] }}',
    neon: '{{ $t['neon'] }}',
    canvas: '{{ $t['canvas'] ?? '#F3F5F7' }}',
    surface: '{{ $t['surface'] ?? '#FFFFFF' }}',
    muted: '{{ $t['muted'] ?? '#5B6577' }}',
    line: '{{ $t['line'] ?? '#D7DDE6' }}',
},
