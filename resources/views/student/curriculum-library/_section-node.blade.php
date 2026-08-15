@foreach($sections as $section)
    @php
        $matCount = $section->materials->count();
        $childCount = $section->treeChildren->count();
        $isEmpty = $matCount === 0 && $childCount === 0;
    @endphp
    <article class="st-cl-section" style="--st-cl-depth: {{ (int) ($depth ?? 0) }}">
        <header class="st-cl-section__head">
            <div>
                <h4>{{ $section->title }}</h4>
                @if($section->description)
                    <p>{{ $section->description }}</p>
                @endif
            </div>
            <span class="st-cl-section__meta">
                {{ $matCount }} {{ $matCount === 1 ? 'مادة' : 'مواد' }}
                @if($childCount > 0)
                    · {{ $childCount }} {{ $childCount === 1 ? 'فرع' : 'فروع' }}
                @endif
            </span>
        </header>

        <div class="st-cl-section__body">
            @if($matCount > 0)
                <ul class="st-cl-mat-list">
                    @foreach($section->materials as $material)
                        <li class="st-cl-mat">
                            <span class="st-cl-mat__icon st-cl-mat__icon--{{ $material->file_kind ?: 'other' }}">
                                @if($material->file_kind === 'pptx')
                                    <i class="fas fa-file-powerpoint" aria-hidden="true"></i>
                                @elseif($material->file_kind === 'pdf')
                                    <i class="fas fa-file-pdf" aria-hidden="true"></i>
                                @elseif($material->file_kind === 'html')
                                    <i class="fas fa-code" aria-hidden="true"></i>
                                @else
                                    <i class="fas fa-file" aria-hidden="true"></i>
                                @endif
                            </span>
                            <div class="st-cl-mat__body">
                                <strong>{{ $material->displayTitle() }}</strong>
                                <em>
                                    @if($material->effectiveAllowViewInPlatform())
                                        عرض داخل المنصة
                                    @else
                                        غير متاح للعرض
                                    @endif
                                    @if($material->effectiveAllowDownload())
                                        · تحميل
                                    @endif
                                    @if(empty($material->path))
                                        · ملف غير مكتمل الرفع
                                    @endif
                                </em>
                            </div>
                            <div class="st-cl-mat__actions">
                                @if(! empty($material->path))
                                    @if($material->file_kind === 'html' && $material->effectiveAllowViewInPlatform())
                                        <a href="{{ route('curriculum-library.material.html', [$item, $material]) }}" target="_blank" rel="noopener" class="st-pill st-pill--solid">عرض</a>
                                    @elseif($material->file_kind === 'pptx' && $material->effectiveAllowViewInPlatform())
                                        <a href="{{ route('curriculum-library.material.presentation', [$item, $material]) }}" target="_blank" rel="noopener" class="st-pill st-pill--solid">عرض تفاعلي</a>
                                    @elseif($material->file_kind === 'pdf' && $material->effectiveAllowViewInPlatform())
                                        <a href="{{ route('curriculum-library.material.pdf', [$item, $material]) }}" target="_blank" rel="noopener" class="st-pill st-pill--outline">عرض</a>
                                    @endif
                                    @if($material->effectiveAllowDownload())
                                        <a href="{{ route('curriculum-library.material.download', [$item, $material]) }}" class="st-pill st-pill--solid">تحميل</a>
                                    @endif
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @elseif($isEmpty)
                <p class="st-cl-empty">لا توجد مواد في هذا القسم بعد. ارفع الملفات من هيكل المنهج في لوحة الإدارة.</p>
            @endif

            @if($childCount > 0)
                <div class="st-cl-section__children">
                    @include('student.curriculum-library._section-node', [
                        'sections' => $section->treeChildren,
                        'item' => $item,
                        'depth' => ((int) ($depth ?? 0)) + 1,
                    ])
                </div>
            @endif
        </div>
    </article>
@endforeach
