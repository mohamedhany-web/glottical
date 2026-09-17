{{-- رأس صفحات اللاندنج + ألوان المنصة الجديدة --}}
@php
    $landingCss = $landingCss ?? ['theme'];
    $themeColor = config('academy-theme.blue', '#4B3A78');
@endphp
<script>document.documentElement.classList.add('js');</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@800;900&family=Tajawal:wght@500;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<meta name="theme-color" content="{{ $themeColor }}">
@foreach($landingCss as $sheet)
  @php
      $landingCssFile = public_path('css/landing/'.$sheet.'.css');
      if (! is_file($landingCssFile)) {
          $landingCssFile = resource_path('css/landing/'.$sheet.'.css');
      }
      $landingCssVer = is_file($landingCssFile) ? (string) filemtime($landingCssFile) : (string) time();
  @endphp
  <link rel="stylesheet" href="{{ route('assets.landing.css', ['sheet' => $sheet]) }}?v={{ $landingCssVer }}">
@endforeach
<style>
  :root {
    --p: {{ config('academy-theme.blue', '#4B3A78') }};
    --p-dark: {{ config('academy-theme.blue_dark', '#3A2C5C') }};
    --p-deep: {{ config('academy-theme.navy_deep', '#2E234A') }};
    --p-light: {{ config('academy-theme.lavender', '#B77CFF') }};
    --p-glow: {{ config('academy-theme.blue_glow', '#C9A0FF') }};
    --gold: {{ config('academy-theme.peach', '#FFB7A5') }};
    --gold-dark: {{ config('academy-theme.peach_dark', '#F59A84') }};
    --mint: {{ config('academy-theme.mint', '#C9FFD8') }};
    --cream: {{ config('academy-theme.canvas', '#FFFBE6') }};
    --bg: {{ config('academy-theme.canvas', '#FFFBE6') }};
    --text: {{ config('academy-theme.ink', '#2E234A') }};
  }
</style>
