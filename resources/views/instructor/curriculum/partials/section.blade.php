@php
    $depth = $depth ?? 0;
@endphp
<div class="su-card section-block" data-section-id="{{ $section->id }}" style="margin-bottom:16px;{{ $depth > 0 ? 'margin-inline-start: '.($depth * 1.25).'rem;border-inline-start:2px solid var(--su-line)' : '' }}">
    <div class="section-header" onclick="toggleSection({{ $section->id }})" style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:4px 0;cursor:pointer">
        <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:0">
            <span class="section-chevron" data-section-id="{{ $section->id }}" style="color:var(--su-ink-40);transition:transform .2s">
                <i class="fas fa-chevron-down" aria-hidden="true"></i>
            </span>
            <div class="min-w-0">
                <h3 class="su-card__title" style="margin:0">{{ $section->title }}</h3>
                @if($section->description)
                    <p style="margin:4px 0 0;font-size:12px;color:var(--su-ink-40);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $section->description }}</p>
                @endif
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:6px;flex-shrink:0" onclick="event.stopPropagation();">
            <button type="button" onclick="event.stopPropagation(); editSection({{ $section->id }}, '{{ addslashes($section->title) }}', '{{ addslashes($section->description ?? '') }}', {{ $section->parent_id ?? 'null' }}, '{{ $section->unlock_rule ?? 'previous_all_items' }}', {{ $section->unlock_percent !== null ? (int)$section->unlock_percent : 'null' }})"
                    class="su-icon-link su-icon-link--ghost" title="{{ __('common.edit') }}">
                <i class="fas fa-edit" aria-hidden="true"></i>
            </button>
            <button type="button" onclick="event.stopPropagation(); deleteSection({{ $section->id }})"
                    class="su-icon-link" style="background:#fee2e2;color:#b91c1c;border:0;cursor:pointer" title="{{ __('common.delete') }}">
                <i class="fas fa-trash" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <div class="section-body" style="margin-top:12px;padding-top:12px;border-top:0.5px solid var(--su-line)">
        <div style="margin-bottom:12px;display:flex;flex-wrap:wrap;align-items:center;gap:8px;padding:10px 12px;border-radius:12px;background:var(--su-bg);border:0.5px solid var(--su-line)">
            <span style="font-size:11px;font-weight:600;color:var(--su-ink-40)">{{ __('instructor.curr_add_label') }}</span>
            <button type="button" onclick="event.stopPropagation(); showAddSubSectionModal({{ $section->id }})"
                    class="su-btn" style="height:30px;font-size:12px"
                    title="{{ __('instructor.curr_subsection') }}">
                <i class="fas fa-folder-plus" aria-hidden="true"></i>
                {{ __('instructor.curr_subsection') }}
            </button>
            <button type="button" onclick="event.stopPropagation(); showAddLectureModal({{ $section->id }})"
                    class="su-btn su-btn--primary" style="height:30px;font-size:12px">
                <i class="fas fa-chalkboard-teacher" aria-hidden="true"></i>
                {{ __('instructor.curr_lecture') }}
            </button>
            <button type="button" onclick="event.stopPropagation(); showAddExamModal({{ $section->id }})"
                    class="su-btn" style="height:30px;font-size:12px">
                <i class="fas fa-clipboard-check" aria-hidden="true"></i>
                {{ __('instructor.curr_exam') }}
            </button>
            <button type="button" onclick="event.stopPropagation(); showAddAssignmentModal({{ $section->id }})"
                    class="su-btn" style="height:30px;font-size:12px">
                <i class="fas fa-tasks" aria-hidden="true"></i>
                {{ __('instructor.curr_assignment') }}
            </button>
        </div>

        <div class="items-container" data-section-id="{{ $section->id }}">
            @php $sectionItems = $section->items->filter(fn($i) => !($i->item instanceof \App\Models\CourseLesson)); @endphp
            @forelse($sectionItems as $item)
                <div class="item-card su-list-item" style="margin-bottom:8px;cursor:move"
                     data-item-id="{{ $item->id }}"
                     @if($item->item instanceof \App\Models\Lecture)
                     onclick="if (event.target.closest('button') || event.target.closest('a') || event.target.closest('.fa-grip-vertical')) return; editLectureFromCurriculum({{ $item->item->id }}, {{ $section->id }});"
                     @endif
                >
                    <i class="fas fa-grip-vertical drag-handle" style="color:var(--su-ink-40);flex-shrink:0" title="drag" aria-hidden="true"></i>
                    @if($item->item instanceof \App\Models\Lecture)
                        <span class="su-list-item__ico su-soft-1"><i class="fas fa-chalkboard-teacher" aria-hidden="true"></i></span>
                        <div class="su-list-item__body">
                            <div class="su-list-item__title">{{ $item->item->title }}</div>
                            <div class="su-list-item__meta">{{ __('instructor.curr_lecture') }}</div>
                        </div>
                        <div class="su-list-item__actions">
                            <button type="button" onclick="event.stopPropagation(); openVideoQuestionsModal({{ $item->item->id }}, '{{ addslashes($item->item->title) }}')" class="su-icon-link su-icon-link--ghost" title="?"><i class="fas fa-question-circle" aria-hidden="true"></i></button>
                            <button type="button" onclick="event.stopPropagation(); editLectureFromCurriculum({{ $item->item->id }}, {{ $section->id }})" class="su-icon-link su-icon-link--ghost" title="{{ __('common.edit') }}"><i class="fas fa-edit" aria-hidden="true"></i></button>
                            <button type="button" onclick="event.stopPropagation(); deleteLectureFromCurriculum({{ $item->item->id }}, {{ $item->id }})" class="su-icon-link" style="background:#fee2e2;color:#b91c1c;border:0;cursor:pointer" title="{{ __('common.delete') }}"><i class="fas fa-trash" aria-hidden="true"></i></button>
                        </div>
                    @elseif($item->item instanceof \App\Models\Assignment)
                        <span class="su-list-item__ico su-soft-2"><i class="fas fa-tasks" aria-hidden="true"></i></span>
                        <div class="su-list-item__body">
                            <div class="su-list-item__title">{{ $item->item->title }}</div>
                            <div class="su-list-item__meta">{{ __('instructor.curr_assignment') }}</div>
                        </div>
                        <div class="su-list-item__actions">
                            <a href="{{ route('instructor.assignments.edit', $item->item) }}" class="su-icon-link su-icon-link--ghost" title="{{ __('common.edit') }}"><i class="fas fa-edit" aria-hidden="true"></i></a>
                            <button type="button" onclick="event.stopPropagation(); removeItem({{ $item->id }})" class="su-icon-link" style="background:#fee2e2;color:#b91c1c;border:0;cursor:pointer" title="{{ __('common.delete') }}"><i class="fas fa-times" aria-hidden="true"></i></button>
                        </div>
                    @elseif($item->item instanceof \App\Models\AdvancedExam || $item->item instanceof \App\Models\Exam)
                        <span class="su-list-item__ico su-soft-3"><i class="fas fa-clipboard-check" aria-hidden="true"></i></span>
                        <div class="su-list-item__body">
                            <div class="su-list-item__title">{{ $item->item->title }}</div>
                            <div class="su-list-item__meta">{{ __('instructor.curr_exam') }}</div>
                        </div>
                        <div class="su-list-item__actions">
                            @if($item->item instanceof \App\Models\AdvancedExam)
                                <a href="{{ route('instructor.exams.edit', $item->item) }}" class="su-icon-link su-icon-link--ghost" title="{{ __('common.edit') }}"><i class="fas fa-edit" aria-hidden="true"></i></a>
                            @endif
                            <button type="button" onclick="event.stopPropagation(); removeItem({{ $item->id }})" class="su-icon-link" style="background:#fee2e2;color:#b91c1c;border:0;cursor:pointer" title="{{ __('common.delete') }}"><i class="fas fa-times" aria-hidden="true"></i></button>
                        </div>
                    @endif
                </div>
            @empty
                <div class="su-empty" style="padding:20px;border:1px dashed var(--su-line);border-radius:12px">
                    <i class="fas fa-inbox" aria-hidden="true"></i>
                    <p>{{ __('instructor.curr_no_items_section') }}</p>
                    <p style="margin-top:4px;font-size:12px;color:var(--su-ink-40)">{{ __('instructor.curr_no_items_hint') }}</p>
                </div>
            @endforelse
        </div>

        @if($section->children && $section->children->count() > 0)
            <div class="sections-children" data-parent-id="{{ $section->id }}" style="margin-top:16px;margin-inline-start:1rem;padding-inline-start:12px;border-inline-start:2px solid var(--su-line)">
                @foreach($section->children as $child)
                    @include('instructor.curriculum.partials.section', ['section' => $child, 'depth' => $depth + 1])
                @endforeach
            </div>
        @else
            <div class="sections-children empty-drop-zone" data-parent-id="{{ $section->id }}" data-empty="1"
                 style="margin-top:16px;margin-inline-start:1rem;min-height:52px;border-radius:12px;border:1px dashed var(--su-line);background:var(--su-bg);display:flex;align-items:center;justify-content:center">
                <span class="curriculum-drag-hint" style="font-size:11px;color:var(--su-ink-40);opacity:0">{{ __('instructor.curr_drop_section') }}</span>
            </div>
        @endif
    </div>
</div>
