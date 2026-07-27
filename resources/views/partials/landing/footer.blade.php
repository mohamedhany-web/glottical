@php
    $isRtl = app()->getLocale() === 'ar';
    $footer = \App\Services\PublicFooterSettings::payload();
    $waUrl = $footer['whatsapp_url'] ?? '#';
    $brand = config('app.name', 'Glottical');
@endphp
<footer class="sana-foot-m">
  <div class="sana-container">
    <div class="sana-foot-m__grid">
      <div class="sana-foot-m__brand">
        <a href="{{ route('home') }}" class="sana-foot-m__logo"><span>{{ $brand }}</span></a>
        <p>{{ $footer['blurb'] ?? __('landing.academy.identity_sub') }}</p>
      </div>
      <div>
        <h4>{{ $isRtl ? 'تصفّح' : 'Explore' }}</h4>
        <ul>
          <li><a href="{{ route('public.groups') }}">{{ __('landing.nav.groups') }}</a></li>
          <li><a href="{{ route('public.courses') }}">{{ __('landing.nav.courses') }}</a></li>
          <li><a href="{{ route('public.instructors.index') }}">{{ __('landing.nav.instructors') }}</a></li>
          <li><a href="{{ route('public.categories') }}">{{ __('landing.nav.categories') }}</a></li>
        </ul>
      </div>
      <div>
        <h4>{{ $isRtl ? 'روابط مهمة' : 'Links' }}</h4>
        <ul>
          <li><a href="{{ route('public.pricing') }}">{{ __('landing.nav.pricing') }}</a></li>
          <li><a href="{{ route('public.contact') }}">{{ $isRtl ? 'اتصل بنا' : 'Contact' }}</a></li>
          <li><a href="{{ route('register') }}">{{ __('landing.nav.register') }}</a></li>
        </ul>
      </div>
      <div>
        <h4>{{ $isRtl ? 'تواصل معنا' : 'Contact' }}</h4>
        <ul>
          <li><a href="{{ $waUrl }}" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> {{ $isRtl ? 'واتساب' : 'WhatsApp' }}</a></li>
          @if(!empty($footer['email']))
            <li><a href="mailto:{{ $footer['email'] }}" dir="ltr">{{ $footer['email'] }}</a></li>
          @endif
        </ul>
      </div>
    </div>
    <div class="sana-foot-m__bottom">
      <p>&copy; {{ date('Y') }} {{ $brand }}. {{ $isRtl ? 'جميع الحقوق محفوظة.' : 'All rights reserved.' }}</p>
    </div>
  </div>
</footer>
<script src="{{ versioned_asset('js/landing/site.js') }}" defer></script>
