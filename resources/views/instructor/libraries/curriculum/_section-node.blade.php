@foreach($sections as $section)
    @php
        $matCount = $section->materials->count();
        $childCount = $section->treeChildren->count();
        $isEmpty = $matCount === 0 && $childCount === 0;
        $indent = (int) ($depth ?? 0);
    @endphp
    <article class="su-card su-card--flush" style="{{ $indent > 0 ? 'margin-inline-start: '.($indent * 1.25).'rem;margin-bottom:12px' : 'margin-bottom:12px' }}">
        <header style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:8px;padding:14px 16px;border-bottom:0.5px solid var(--su-line);background:var(--su-bg)">
            <div class="min-w-0">
                <h3 class="su-card__title" style="margin:0">{{ $section->title }}</h3>
                @if($section->description)
                    <p style="margin:4px 0 0;font-size:12px;color:var(--su-ink-40)">{{ $section->description }}</p>
                @endif
            </div>
            <span class="su-chip">
                {{ $matCount === 1 ? __('instructor.lib_curriculum_materials_one') : __('instructor.lib_curriculum_materials_many', ['count' => $matCount]) }}
            </span>
        </header>

        @if($matCount > 0)
            <div class="su-list" style="padding:12px">
                @foreach($section->materials as $material)
                    <div class="su-list-item">
                        <span class="su-list-item__ico su-soft-2"><i class="fas fa-file-alt" aria-hidden="true"></i></span>
                        <div class="su-list-item__body">
                            <div class="su-list-item__title">{{ $material->displayTitle() }}</div>
                            <div class="su-list-item__meta">
                                {{ strtoupper((string) ($material->file_kind ?: __('instructor.lib_curriculum_file_fallback'))) }}
                                @if(empty($material->path))
                                    · {{ __('instructor.lib_curriculum_incomplete_file') }}
                                @endif
                            </div>
                        </div>
                        <div class="su-list-item__actions">
                            @if(! empty($material->path))
                                @if($material->file_kind === 'html' && $material->effectiveAllowViewInPlatform())
                                    <a href="{{ route('curriculum-library.material.html', [$item, $material]) }}" target="_blank" rel="noopener" class="su-btn su-btn--primary" style="height:32px">{{ __('common.view') }}</a>
                                @elseif($material->file_kind === 'pptx' && $material->effectiveAllowViewInPlatform())
                                    <a href="{{ route('curriculum-library.material.presentation', [$item, $material]) }}" target="_blank" rel="noopener" class="su-btn su-btn--primary" style="height:32px">{{ __('instructor.lib_curriculum_interactive_view') }}</a>
                                @elseif($material->file_kind === 'pdf' && $material->effectiveAllowViewInPlatform())
                                    <a href="{{ route('curriculum-library.material.pdf', [$item, $material]) }}" target="_blank" rel="noopener" class="su-btn" style="height:32px">{{ __('common.view') }}</a>
                                @endif
                                @if($material->effectiveAllowDownload())
                                    <a href="{{ route('curriculum-library.material.download', [$item, $material]) }}" class="su-btn su-btn--primary" style="height:32px">{{ __('instructor.download') }}</a>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @elseif($isEmpty)
            <p style="padding:16px;margin:0;font-size:13px;color:var(--su-ink-40)">{{ __('instructor.lib_curriculum_no_materials') }}</p>
        @endif

        @if($childCount > 0)
            <div style="padding:12px;display:flex;flex-direction:column;gap:12px">
                @include('instructor.libraries.curriculum._section-node', [
                    'sections' => $section->treeChildren,
                    'item' => $item,
                    'depth' => $indent + 1,
                ])
            </div>
        @endif
    </article>
@endforeach
