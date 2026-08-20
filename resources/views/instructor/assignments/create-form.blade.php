@php
    $courses = $courses ?? [];
@endphp

<form action="{{ route('instructor.assignments.store') }}" method="POST" enctype="multipart/form-data" id="assignmentForm">
    @csrf

    <div class="su-form-grid" style="grid-template-columns:1fr 1fr">
        <div class="su-field" style="grid-column:1 / -1">
            <label for="advanced_course_id">{{ __('instructor.course_label') }} <span style="color:#b91c1c">*</span></label>
            <select name="advanced_course_id" id="advanced_course_id" required class="su-select">
                <option value="">{{ __('instructor.choose_course_option') }}</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ old('advanced_course_id', request('advanced_course_id')) == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                @endforeach
            </select>
            @error('advanced_course_id')
                <p class="su-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="su-field" style="grid-column:1 / -1">
            <label for="lesson_id">{{ __('instructor.lesson_optional') }}</label>
            <select name="lesson_id" id="lesson_id" class="su-select">
                <option value="">{{ __('instructor.no_lesson_option') }}</option>
            </select>
            <p style="margin:6px 0 0;font-size:12px;color:var(--su-ink-40)">{{ __('instructor.lesson_filled_by_course') }}</p>
            @error('lesson_id')
                <p class="su-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="su-field" style="grid-column:1 / -1">
            <label for="title">{{ __('instructor.assignment_title_required') }} <span style="color:#b91c1c">*</span></label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required class="su-input"
                   placeholder="{{ __('instructor.assignment_title_required') }}">
            @error('title')
                <p class="su-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="su-field" style="grid-column:1 / -1">
            <label for="description">{{ __('instructor.description') }}</label>
            <textarea name="description" id="description" rows="3" class="su-input" style="min-height:88px;resize:vertical"
                      placeholder="{{ __('instructor.description') }}...">{{ old('description') }}</textarea>
            @error('description')
                <p class="su-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="su-field" style="grid-column:1 / -1">
            <label for="instructions">{{ __('instructor.instructions_label') }}</label>
            <textarea name="instructions" id="instructions" rows="4" class="su-input" style="min-height:110px;resize:vertical"
                      placeholder="{{ __('instructor.instructions_label') }}...">{{ old('instructions') }}</textarea>
            @error('instructions')
                <p class="su-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="su-field" style="grid-column:1 / -1">
            <label>{{ __('instructor.assignment_attachments_optional') }}</label>
            <p style="margin:0 0 8px;font-size:12px;color:var(--su-ink-40)">{{ __('instructor.assignment_attachments_hint') }}</p>
            <input type="file" name="resource_files[]" multiple accept=".pdf,.doc,.docx,.zip,.rar,.jpg,.jpeg,.png,.gif,.webp,.ppt,.pptx,.txt" class="su-input">
            @error('resource_files')
                <p class="su-field-error">{{ $message }}</p>
            @enderror
            @error('resource_files.*')
                <p class="su-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="su-field">
            <label for="due_date">{{ __('instructor.due_date') }}</label>
            <input type="datetime-local" name="due_date" id="due_date" value="{{ old('due_date') }}" class="su-input">
            @error('due_date')
                <p class="su-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="su-field">
            <label for="max_score">{{ __('instructor.total_score_label') }} <span style="color:#b91c1c">*</span></label>
            <input type="number" name="max_score" id="max_score" value="{{ old('max_score', 100) }}" min="1" max="1000" required class="su-input">
            @error('max_score')
                <p class="su-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="su-field" style="grid-column:1 / -1">
            <label class="su-chip" style="cursor:pointer;justify-content:flex-start;height:auto;padding:12px 14px;width:100%">
                <input type="checkbox" name="allow_late_submission" id="allow_late_submission" value="1" {{ old('allow_late_submission') ? 'checked' : '' }} style="margin-inline-end:10px">
                {{ __('instructor.allow_late_submission_label') }}
            </label>
        </div>

        <div class="su-field" style="grid-column:1 / -1">
            <label for="status">{{ __('common.status') }} <span style="color:#b91c1c">*</span></label>
            <select name="status" id="status" required class="su-select">
                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>{{ __('instructor.draft') }}</option>
                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>{{ __('instructor.published') }}</option>
                <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>{{ __('instructor.archived') }}</option>
            </select>
            @error('status')
                <p class="su-field-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="su-page-head__actions" style="margin-top:20px;justify-content:flex-end;border-top:1px solid var(--su-line);padding-top:16px">
        @if(isset($isModal) && $isModal)
            <button type="button" onclick="closeCreateModal()" class="su-btn">
                <i class="fas fa-times" aria-hidden="true"></i>
                {{ __('common.cancel') }}
            </button>
        @else
            <a href="{{ route('instructor.assignments.index') }}" class="su-btn">
                <i class="fas fa-times" aria-hidden="true"></i>
                {{ __('common.cancel') }}
            </a>
        @endif
        <button type="submit" class="su-btn su-btn--primary">
            <i class="fas fa-save" aria-hidden="true"></i>
            {{ __('instructor.create_assignment') }}
        </button>
    </div>
</form>

<script>
if (typeof updateLessonsOnCourseChange === 'undefined') {
    window.updateLessonsOnCourseChange = function() {
        var courseSelect = document.getElementById('advanced_course_id');
        if (!courseSelect) return;
        courseSelect.addEventListener('change', function() {
            var courseId = this.value;
            var lessonSelect = document.getElementById('lesson_id');
            if (!lessonSelect) return;
            while (lessonSelect.children.length > 1) lessonSelect.removeChild(lessonSelect.lastChild);
            if (courseId) {
                var loadingOption = document.createElement('option');
                loadingOption.value = '';
                loadingOption.textContent = @json(__('instructor.loading_text'));
                loadingOption.disabled = true;
                lessonSelect.appendChild(loadingOption);
                lessonSelect.disabled = true;
                fetch('/instructor/api/courses/' + courseId + '/lessons-list', { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        loadingOption.remove();
                        var lessons = Array.isArray(data) ? data : (data.lessons || []);
                        if (lessons.length > 0) {
                            lessons.forEach(function(lesson) {
                                var opt = document.createElement('option');
                                opt.value = lesson.id;
                                opt.textContent = lesson.title || 'درس ' + (lesson.order || '');
                                lessonSelect.appendChild(opt);
                            });
                        } else {
                            var noOpt = document.createElement('option');
                            noOpt.value = '';
                            noOpt.textContent = @json(__('instructor.no_lessons_in_course'));
                            noOpt.disabled = true;
                            lessonSelect.appendChild(noOpt);
                        }
                        lessonSelect.disabled = false;
                    })
                    .catch(function() {
                        loadingOption.remove();
                        var errOpt = document.createElement('option');
                        errOpt.value = '';
                        errOpt.textContent = @json(__('instructor.error_occurred'));
                        errOpt.disabled = true;
                        lessonSelect.appendChild(errOpt);
                        lessonSelect.disabled = false;
                    });
            } else {
                lessonSelect.disabled = false;
            }
        });
    };
    document.addEventListener('DOMContentLoaded', function() { updateLessonsOnCourseChange(); });
}
</script>
