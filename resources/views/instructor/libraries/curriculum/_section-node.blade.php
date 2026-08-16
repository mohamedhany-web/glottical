@foreach($sections as $section)
    @php
        $matCount = $section->materials->count();
        $childCount = $section->treeChildren->count();
        $isEmpty = $matCount === 0 && $childCount === 0;
        $indent = (int) ($depth ?? 0);
    @endphp
    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" style="{{ $indent > 0 ? 'margin-inline-start: '.($indent * 1.25).'rem' : '' }}">
        <header class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 bg-slate-50 px-4 py-3">
            <div>
                <h3 class="font-semibold text-slate-900">{{ $section->title }}</h3>
                @if($section->description)
                    <p class="mt-0.5 text-xs text-slate-500">{{ $section->description }}</p>
                @endif
            </div>
            <span class="text-xs text-slate-500">{{ $matCount }} {{ $matCount === 1 ? 'مادة' : 'مواد' }}</span>
        </header>

        @if($matCount > 0)
            <ul class="divide-y divide-slate-100">
                @foreach($section->materials as $material)
                    <li class="flex flex-wrap items-center justify-between gap-3 px-4 py-3">
                        <div>
                            <div class="font-medium text-slate-900">{{ $material->displayTitle() }}</div>
                            <div class="text-xs text-slate-500">
                                {{ strtoupper((string) ($material->file_kind ?: 'ملف')) }}
                                @if(empty($material->path))
                                    · ملف غير مكتمل الرفع
                                @endif
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @if(! empty($material->path))
                                @if($material->file_kind === 'html' && $material->effectiveAllowViewInPlatform())
                                    <a href="{{ route('curriculum-library.material.html', [$item, $material]) }}" target="_blank" rel="noopener" class="rounded-lg bg-[#0B3D91] px-3 py-1.5 text-xs font-semibold text-white">عرض</a>
                                @elseif($material->file_kind === 'pptx' && $material->effectiveAllowViewInPlatform())
                                    <a href="{{ route('curriculum-library.material.presentation', [$item, $material]) }}" target="_blank" rel="noopener" class="rounded-lg bg-[#0B3D91] px-3 py-1.5 text-xs font-semibold text-white">عرض تفاعلي</a>
                                @elseif($material->file_kind === 'pdf' && $material->effectiveAllowViewInPlatform())
                                    <a href="{{ route('curriculum-library.material.pdf', [$item, $material]) }}" target="_blank" rel="noopener" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">عرض</a>
                                @endif
                                @if($material->effectiveAllowDownload())
                                    <a href="{{ route('curriculum-library.material.download', [$item, $material]) }}" class="rounded-lg bg-[#0B3D91] px-3 py-1.5 text-xs font-semibold text-white">تحميل</a>
                                @endif
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @elseif($isEmpty)
            <p class="px-4 py-6 text-sm text-slate-500">لا توجد مواد في هذا القسم بعد.</p>
        @endif

        @if($childCount > 0)
            <div class="space-y-3 p-3">
                @include('instructor.libraries.curriculum._section-node', [
                    'sections' => $section->treeChildren,
                    'item' => $item,
                    'depth' => $indent + 1,
                ])
            </div>
        @endif
    </article>
@endforeach
