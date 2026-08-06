@extends('layouts.app')

@section('title', 'رصيد الحصص')
@section('page_title', 'باقاتي ورصيد الحصص')

@section('content')
@php $isRtl = app()->getLocale() === 'ar'; @endphp
<div class="space-y-5">
  <section class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
    <h2 class="text-xl font-semibold text-ink">{{ $isRtl ? 'رصيدك الحالي' : 'Your balance' }}</h2>
    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
      <div class="rounded-xl bg-accent-soft p-4"><p class="text-xs text-muted">فردي</p><p class="text-2xl font-bold text-accent">{{ $totals['individual'] }}</p></div>
      <div class="rounded-xl bg-canvas-muted p-4"><p class="text-xs text-muted">جماعي/مدرسة</p><p class="text-2xl font-bold text-ink">{{ $totals['collective'] }}</p></div>
      <div class="rounded-xl bg-canvas-muted p-4"><p class="text-xs text-muted">حصص خاصة</p><p class="text-2xl font-bold text-ink">{{ $totals['private'] }}</p></div>
      <div class="rounded-xl bg-canvas-muted p-4"><p class="text-xs text-muted">عام</p><p class="text-2xl font-bold text-ink">{{ $totals['global'] }}</p></div>
    </div>
    <a href="{{ route('public.service-packages.index') }}" class="mt-4 inline-flex h-10 items-center rounded-xl bg-accent px-4 text-sm font-medium text-white">{{ $isRtl ? 'اشحن باقة' : 'Recharge package' }}</a>
    <a href="{{ route('public.groups') }}" class="mt-4 inline-flex h-10 items-center rounded-xl border border-line px-4 text-sm font-medium text-ink">{{ $isRtl ? 'احجز حصة من رصيدك' : 'Book a session' }}</a>
  </section>

  <section class="overflow-hidden rounded-2xl border border-line bg-surface">
    <div class="border-b border-line px-4 py-3 font-semibold text-ink">{{ $isRtl ? 'سجل الأرصدة' : 'Entitlement history' }}</div>
    <table class="min-w-full text-sm">
      <thead class="bg-canvas-muted text-xs text-muted">
        <tr>
          <th class="px-4 py-2 text-start">الباقة</th>
          <th class="px-4 py-2 text-start">المتبقي</th>
          <th class="px-4 py-2 text-start">القابل للحجز</th>
          <th class="px-4 py-2 text-start">الصلاحية</th>
          <th class="px-4 py-2 text-start">الحالة</th>
        </tr>
      </thead>
      <tbody>
        @forelse($entitlements as $ent)
          <tr class="border-t border-line">
            <td class="px-4 py-3">
              {{ $ent->servicePackage?->name ?: (\App\Models\ServicePackage::scopes()[$ent->scope] ?? $ent->scope) }}
              @if($ent->academicYear || $ent->academicSubject)
                <div class="text-xs text-muted">{{ collect([$ent->academicYear?->name, $ent->academicSubject?->name])->filter()->implode(' · ') }}</div>
              @endif
              @if($ent->tutoringGroup)<div class="text-xs text-muted">{{ $ent->tutoringGroup->title }}</div>@endif
            </td>
            <td class="px-4 py-3 font-semibold">{{ $ent->unitsLeft() }} / {{ $ent->units_total }}</td>
            <td class="px-4 py-3">
              <span class="font-semibold text-accent">{{ \App\Services\StudentEntitlementService::bookableUnitsLeft($ent) }}</span>
              @if($ent->unitsLeft() > \App\Services\StudentEntitlementService::bookableUnitsLeft($ent))
                <div class="text-[11px] text-muted">الباقي محجوز لمواعيد قادمة</div>
              @endif
            </td>
            <td class="px-4 py-3 text-xs">{{ $ent->expires_at?->format('Y-m-d') ?: '—' }}</td>
            <td class="px-4 py-3">{{ $ent->status }}</td>
          </tr>
        @empty
          <tr><td colspan="5" class="px-4 py-8 text-center text-muted">{{ $isRtl ? 'لا يوجد رصيد بعد. اشترِ باقة للبدء.' : 'No credits yet.' }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </section>
  <div>{{ $entitlements->links() }}</div>

  @if($packages->isNotEmpty())
    <section class="grid gap-3 sm:grid-cols-3">
      @foreach($packages as $package)
        <a href="{{ route('public.service-packages.checkout', $package) }}" class="rounded-2xl border border-line bg-surface p-4 hover:border-accent">
          <div class="font-semibold text-ink">{{ $package->name }}</div>
          <div class="text-sm text-muted">
            {{ $package->units_count }} {{ $isRtl ? 'حصة' : 'sessions' }} ×
            {{ $package->sessionMinutes() }} {{ $isRtl ? 'دقيقة' : 'min' }}
          </div>
          <div class="text-xs text-muted">{{ $isRtl ? 'صلاحية' : 'Valid' }} {{ $package->validityLabel() }}</div>
          <div class="mt-2 font-bold text-accent">{{ $package->formattedPrice() }}</div>
          <div class="text-xs text-muted">{{ $package->formattedPricePerUnit() }} / {{ $isRtl ? 'حصة' : 'session' }}</div>
        </a>
      @endforeach
    </section>
  @endif
</div>
@endsection
