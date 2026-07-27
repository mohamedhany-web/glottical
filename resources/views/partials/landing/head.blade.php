{{-- رأس صفحات اللاندنج + ألوان Glottical أزرق/أصفر --}}
@php
    $landingCss = $landingCss ?? ['theme'];
    $themeColor = config('academy-theme.blue', '#0B3D91');
@endphp
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@800;900&family=Tajawal:wght@500;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<meta name="theme-color" content="{{ $themeColor }}">
@foreach($landingCss as $sheet)
  @php
      $landingCssFile = resource_path('css/landing/'.$sheet.'.css');
      if (! is_file($landingCssFile)) {
          $landingCssFile = public_path('css/landing/'.$sheet.'.css');
      }
      $landingCssVer = is_file($landingCssFile) ? (string) filemtime($landingCssFile) : (string) time();
  @endphp
  <link rel="stylesheet" href="{{ route('assets.landing.css', ['sheet' => $sheet]) }}?v={{ $landingCssVer }}">
@endforeach
<style>
  :root {
    --p: {{ config('academy-theme.blue', '#0B3D91') }};
    --p-dark: {{ config('academy-theme.blue_dark', '#072A66') }};
    --p-deep: #051F4D;
    --p-light: #3D6BC4;
    --p-glow: #6B8FD4;
    --gold: {{ config('academy-theme.yellow', '#F5B800') }};
    --gold-dark: #D99E00;
  }
</style>
