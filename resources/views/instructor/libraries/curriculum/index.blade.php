@extends('layouts.app')

@section('title', __('instructor.lib_curriculum_title') . ' - ' . config('app.name'))
@section('page_title', __('instructor.lib_curriculum_title'))

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-sitemap su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.lib_curriculum_title') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.lib_curriculum_subtitle') }}</p>
        </div>
    </div>

    <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(245,158,11,.35);background:rgba(245,158,11,.08);color:#92400e;font-size:13px">
        <i class="fas fa-info-circle" aria-hidden="true"></i>
        {{ __('instructor.lib_curriculum_info') }}
    </div>

    <section class="su-card" style="margin-bottom:20px">
        <form method="GET" class="su-form-grid">
            <div class="su-field">
                <label for="q">{{ __('common.search') }}</label>
                <input type="search" name="q" id="q" value="{{ request('q') }}"
                       placeholder="{{ __('instructor.lib_curriculum_search_ph') }}"
                       class="su-input">
            </div>
            <div class="su-field">
                <label for="category_id">{{ __('instructor.lib_curriculum_all_categories') }}</label>
                <select name="category_id" id="category_id" class="su-select">
                    <option value="">{{ __('instructor.lib_curriculum_all_categories') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected((string) request('category_id') === (string) $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="su-field">
                <label for="language">{{ __('instructor.lib_curriculum_all_languages') }}</label>
                <select name="language" id="language" class="su-select">
                    <option value="">{{ __('instructor.lib_curriculum_all_languages') }}</option>
                    <option value="ar" @selected(request('language') === 'ar')>العربية</option>
                    <option value="en" @selected(request('language') === 'en')>English</option>
                    <option value="fr" @selected(request('language') === 'fr')>Français</option>
                </select>
            </div>
            <div class="su-form-actions">
                <button type="submit" class="su-btn su-btn--primary" style="flex:1;justify-content:center;height:40px">
                    <i class="fas fa-filter" aria-hidden="true"></i>
                    {{ __('instructor.lib_curriculum_filter') }}
                </button>
            </div>
        </form>
    </section>

    <div class="su-list" style="margin-bottom:20px">
        @forelse($items as $item)
            <a href="{{ route('instructor.libraries.curriculum.show', $item) }}" class="su-list-item" style="text-decoration:none;color:inherit">
                <span class="su-list-item__ico su-soft-1"><i class="fas fa-book-open" aria-hidden="true"></i></span>
                <div class="su-list-item__body">
                    <div class="su-list-item__title">{{ $item->title }}</div>
                    <div class="su-list-item__meta">
                        @if($item->category)
                            <span class="su-chip" style="height:22px;font-size:11px">{{ $item->category->name }}</span>
                        @endif
                        @if($item->subject)
                            {{ $item->subject }}
                        @endif
                        @if($item->description)
                            · {{ \Illuminate\Support\Str::limit($item->description, 80) }}
                        @endif
                    </div>
                </div>
                <span class="su-list-item__actions">
                    <span class="su-btn" style="height:32px;pointer-events:none">{{ __('instructor.lib_curriculum_open') }}</span>
                </span>
            </a>
        @empty
            <div class="su-empty">
                <i class="fas fa-sitemap" aria-hidden="true"></i>
                <p>{{ __('instructor.lib_curriculum_empty') }}</p>
            </div>
        @endforelse
    </div>

    @if(method_exists($items, 'hasPages') && $items->hasPages())
        <div class="su-pager" style="margin-bottom:20px">{{ $items->links() }}</div>
    @endif

    @if(($teachingCourses ?? collect())->isNotEmpty())
        <section class="su-card su-card--flush">
            <div style="padding:14px 16px;border-bottom:0.5px solid var(--su-line)">
                <h3 class="su-card__title" style="margin:0">{{ __('instructor.lib_curriculum_courses_structure') }}</h3>
                <p style="margin:4px 0 0;font-size:12px;color:var(--su-ink-40)">{{ __('instructor.lib_curriculum_courses_structure_sub') }}</p>
            </div>
            <div class="su-list" style="padding:12px">
                @foreach($teachingCourses as $course)
                    <div class="su-list-item">
                        <span class="su-list-item__ico su-soft-2"><i class="fas fa-graduation-cap" aria-hidden="true"></i></span>
                        <div class="su-list-item__body">
                            <div class="su-list-item__title">{{ $course->title }}</div>
                            <div class="su-list-item__meta">
                                {{ $course->academicSubject?->academicYear?->name ?? '—' }}
                                · {{ $course->academicSubject?->name ?? '—' }}
                                · {{ __('instructor.lib_curriculum_sections_count', ['count' => $course->sections_count]) }}
                            </div>
                        </div>
                        <div class="su-list-item__actions">
                            <a href="{{ route('instructor.libraries.curriculum.course', $course) }}" class="su-btn" style="height:32px">
                                {{ __('instructor.lib_curriculum_view_structure') }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
