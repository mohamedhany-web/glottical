{{--
  Admin workflow helper banner.
  Usage:
  @include('admin.partials.workflow-guide', [
      'title' => 'كيف تسير العملية؟', // optional
      'body'  => 'نص يشرح الصفحة…',
      'steps' => ['خطوة 1', 'خطوة 2'], // optional
  ])
--}}
@php
    $guideTitle = $title ?? 'كيف تسير العملية؟';
    $guideBody = $body ?? '';
    $guideSteps = $steps ?? [];
@endphp
@if($guideBody !== '' || count($guideSteps) > 0)
<aside class="rounded-2xl border border-accent/25 bg-gradient-to-l from-accent-soft/70 via-surface to-surface px-4 py-3.5 shadow-soft" role="note" aria-label="{{ $guideTitle }}">
    <div class="flex gap-3">
        <span class="mt-0.5 inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-accent text-white shadow-soft" aria-hidden="true">
            <i class="fas fa-route text-sm"></i>
        </span>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold text-ink">{{ $guideTitle }}</p>
            @if($guideBody !== '')
                <p class="mt-1 text-sm leading-relaxed text-ink-soft">{{ $guideBody }}</p>
            @endif
            @if(count($guideSteps) > 0)
                <ol class="mt-2.5 space-y-1.5">
                    @foreach($guideSteps as $i => $step)
                        <li class="flex gap-2 text-sm leading-snug text-ink-soft">
                            <span class="mt-0.5 inline-flex size-5 shrink-0 items-center justify-center rounded-full bg-accent-soft text-[11px] font-bold text-accent">{{ $i + 1 }}</span>
                            <span>{{ $step }}</span>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>
    </div>
</aside>
@endif
