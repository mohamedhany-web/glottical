@foreach($sections as $section)
    <div class="rounded-xl border border-slate-200 bg-slate-50/50 dark:bg-slate-800/40 dark:border-slate-600 overflow-hidden" style="margin-right: {{ ($depth ?? 0) * 0.75 }}rem">
        <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-600 bg-white/80 dark:bg-slate-800/80">
            <h3 class="text-base font-black text-slate-800 dark:text-slate-100">{{ $section->title }}</h3>
            @if($section->description)
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">{{ $section->description }}</p>
            @endif
        </div>
        <div class="p-4 space-y-2">
            @foreach($section->materials as $material)
                <div class="flex flex-wrap items-center gap-3 p-3 rounded-xl bg-white dark:bg-slate-800/90 border border-slate-100 dark:border-slate-600">
                    @if($material->file_kind === 'pptx')
                        <span class="w-10 h-10 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center"><i class="fas fa-file-powerpoint"></i></span>
                    @elseif($material->file_kind === 'pdf')
                        <span class="w-10 h-10 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center"><i class="fas fa-file-pdf"></i></span>
                    @elseif($material->file_kind === 'html')
                        <span class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center"><i class="fas fa-code"></i></span>
                    @else
                        <span class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center"><i class="fas fa-file"></i></span>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-800 dark:text-slate-100 truncate">{{ $material->displayTitle() }}</p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                            @if($material->effectiveAllowViewInPlatform()) عرض داخل المنصة @else — @endif
                            @if($material->effectiveAllowDownload()) · تحميل @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        @if($material->file_kind === 'html' && $material->effectiveAllowViewInPlatform())
                            <a href="{{ route('curriculum-library.material.html', [$item, $material]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        @elseif($material->file_kind === 'pptx' && $material->effectiveAllowViewInPlatform())
                            <a href="{{ route('curriculum-library.material.presentation', [$item, $material]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-semibold hover:bg-amber-700">
                                <i class="fas fa-play"></i> عرض تفاعلي
                            </a>
                        @elseif($material->file_kind === 'pdf' && $material->effectiveAllowViewInPlatform())
                            <a href="{{ route('curriculum-library.material.pdf', [$item, $material]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        @endif
                        @if($material->effectiveAllowDownload())
                            <a href="{{ route('curriculum-library.material.download', [$item, $material]) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">
                                <i class="fas fa-download"></i> تحميل
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach

            @if($section->treeChildren->isNotEmpty())
                <div class="mt-3 space-y-3 border-t border-dashed border-slate-200 dark:border-slate-600 pt-3">
                    @include('student.curriculum-library._section-node', ['sections' => $section->treeChildren, 'item' => $item, 'depth' => ($depth ?? 0) + 1])
                </div>
            @endif
        </div>
    </div>
@endforeach
