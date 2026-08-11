@extends('layouts.student-timeline')

@section('title', $assignment->title)

@section('content')
@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $courseTitle = $assignment->course->title ?? __('student_timeline.course');
    $instrFiles = is_array($assignment->resource_attachments) ? $assignment->resource_attachments : [];
    $eventMasks = [
        asset('img/student-timeline/event-mask-1.svg'),
        asset('img/student-timeline/event-mask-2.svg'),
        asset('img/student-timeline/event-mask-3.svg'),
    ];

    $statusKey = 'pending';
    $statusLabel = __('student_timeline.asg_not_submitted');
    if ($submission) {
        if ($submission->status === 'submitted') {
            $statusKey = 'submitted';
            $statusLabel = __('student_timeline.asg_under_review');
        } elseif ($submission->status === 'graded') {
            $statusKey = 'graded';
            $statusLabel = __('student_timeline.asg_graded');
        } elseif ($submission->status === 'returned') {
            $statusKey = 'returned';
            $statusLabel = __('student_timeline.asg_returned');
        }
    }

    $dueFormatted = $assignment->due_date
        ? $assignment->due_date->timezone(config('app.timezone'))->translatedFormat($isRtl ? 'd M Y · g:i A' : 'M j, Y · g:i A')
        : null;
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => $assignment->title,
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.nav_assignments'), 'url' => route('student.assignments.index')],
        ['label' => \Illuminate\Support\Str::limit($assignment->title, 28), 'url' => null],
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="st-flash st-flash--err">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="st-flash st-flash--err">{{ $errors->first() }}</div>
@endif

<section class="st-event-card st-event-card--blue st-biz-banner st-asg-hero">
    <img class="st-event-card__mask" src="{{ $eventMasks[0] }}" alt="" width="160" height="160">
    <div class="st-biz-banner__row">
        <div>
            <p class="st-event-card__kicker">{{ $courseTitle }}</p>
            <h3>{{ $assignment->title }}</h3>
            <p class="st-event-card__sub">
                @if($assignment->lesson?->title)
                    {{ $assignment->lesson->title }}
                    @if($dueFormatted) · @endif
                @endif
                @if($dueFormatted)
                    {{ __('student_timeline.due_at', ['date' => $dueFormatted]) }}
                @endif
            </p>
            <div class="st-asg-hero__chips">
                <span class="st-assign-card__badge is-{{ $statusKey }}">{{ $statusLabel }}</span>
                <span class="st-asg-chip">
                    <i class="fas fa-star" aria-hidden="true"></i>
                    {{ __('student_timeline.asg_max_score', ['score' => $assignment->max_score]) }}
                </span>
                @if($assignment->allow_late_submission)
                    <span class="st-asg-chip st-asg-chip--soft">{{ __('student_timeline.asg_late_ok') }}</span>
                @endif
            </div>
        </div>
        <div class="st-biz-banner__actions">
            <a href="{{ route('student.assignments.index') }}" class="st-pill st-pill--light">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('student_timeline.nav_assignments') }}
            </a>
        </div>
    </div>
</section>

<section class="st-asg-layout">
    <div class="st-asg-main">
        <article class="st-panel st-asg-panel">
            <div class="st-section-head">
                <div>
                    <h2>{{ __('student_timeline.asg_description') }}</h2>
                    <p>{{ __('student_timeline.asg_description_hint') }}</p>
                </div>
            </div>
            @if($assignment->description)
                <div class="st-asg-prose">{!! nl2br(e($assignment->description)) !!}</div>
            @else
                <p class="st-asg-muted">{{ __('student_timeline.asg_no_description') }}</p>
            @endif

            @if($assignment->instructions)
                <div class="st-asg-box">
                    <p class="st-asg-box__label">{{ __('student_timeline.asg_instructions') }}</p>
                    <div class="st-asg-prose">{{ $assignment->instructions }}</div>
                </div>
            @endif
        </article>

        @if(count($instrFiles) > 0)
            <article class="st-panel st-asg-panel">
                <div class="st-section-head">
                    <div>
                        <h2>{{ __('student_timeline.asg_teacher_files') }}</h2>
                        <p>{{ __('student_timeline.asg_teacher_files_hint') }}</p>
                    </div>
                </div>
                <ul class="st-asg-files">
                    @foreach($instrFiles as $att)
                        @php
                            $p = is_array($att) ? ($att['path'] ?? '') : '';
                            $url = $p ? (\App\Services\AssignmentFileStorage::publicUrl($p)) : null;
                            $label = is_array($att) ? ($att['original_name'] ?? basename($p)) : '';
                            $mime = is_array($att) ? ($att['mime'] ?? '') : '';
                            $isImg = $mime && str_starts_with((string) $mime, 'image/');
                        @endphp
                        @if($url)
                            <li class="st-asg-file">
                                @if($isImg)
                                    <a href="{{ $url }}" target="_blank" rel="noopener" class="st-asg-file__preview">
                                        <img src="{{ $url }}" alt="{{ $label }}">
                                    </a>
                                @else
                                    <span class="st-asg-file__icon" aria-hidden="true"><i class="fas fa-paperclip"></i></span>
                                @endif
                                <div class="st-asg-file__copy">
                                    <a href="{{ $url }}" target="_blank" rel="noopener">{{ $label }}</a>
                                </div>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </article>
        @endif
    </div>

    <aside class="st-asg-side">
        @if($submission)
            <article class="st-panel st-asg-panel">
                <div class="st-section-head">
                    <div>
                        <h2>{{ __('student_timeline.asg_your_submission') }}</h2>
                        <p>
                            <span class="st-assign-card__badge is-{{ $statusKey }}">{{ $statusLabel }}</span>
                            @if($submission->status === 'graded' && $submission->score !== null)
                                <span class="st-asg-chip">{{ $submission->score }} / {{ $assignment->max_score }}</span>
                            @endif
                        </p>
                    </div>
                </div>

                @if($submission->submitted_at)
                    <p class="st-asg-muted">
                        {{ __('student_timeline.asg_last_sent', [
                            'date' => $submission->submitted_at->timezone(config('app.timezone'))->translatedFormat($isRtl ? 'd M · g:i A' : 'M j · g:i A'),
                        ]) }}
                    </p>
                @endif

                @if($submission->content)
                    <div class="st-asg-box st-asg-box--scroll">{{ $submission->content }}</div>
                @endif

                @if(is_array($submission->attachments) && count($submission->attachments))
                    <ul class="st-asg-attach-list">
                        @foreach($submission->attachments as $att)
                            @php
                                $p = is_array($att) ? ($att['path'] ?? null) : null;
                                $name = is_array($att) ? ($att['original_name'] ?? basename((string) $p)) : '';
                                $fileUrl = $p ? \App\Services\AssignmentFileStorage::publicUrl($p) : null;
                            @endphp
                            @if($fileUrl)
                                <li>
                                    <a href="{{ $fileUrl }}" target="_blank" rel="noopener">
                                        <i class="fas fa-paperclip" aria-hidden="true"></i>
                                        {{ $name }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endif

                @if($submission->feedback)
                    <div class="st-asg-box st-asg-box--feedback">
                        <p class="st-asg-box__label">{{ __('student_timeline.asg_feedback') }}</p>
                        <p>{{ $submission->feedback }}</p>
                    </div>
                @endif

                @if(!empty($canDeleteSubmission))
                    <form action="{{ route('student.assignments.submission.destroy', $assignment) }}" method="post"
                          onsubmit="return confirm(@json(__('student_timeline.asg_delete_confirm')));">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="st-asg-danger">
                            <i class="fas fa-trash-alt" aria-hidden="true"></i>
                            {{ __('student_timeline.asg_delete_submission') }}
                        </button>
                    </form>
                @endif
            </article>
        @endif

        @if($canSubmit)
            <article class="st-panel st-asg-panel st-asg-panel--submit">
                <div class="st-section-head">
                    <div>
                        <h2>
                            @if($submission && $submission->status === 'returned')
                                {{ __('student_timeline.asg_resubmit') }}
                            @else
                                {{ __('student_timeline.asg_submit') }}
                            @endif
                        </h2>
                        <p>{{ __('student_timeline.asg_submit_hint') }}</p>
                    </div>
                </div>

                @if(!empty($directUploadToCloud))
                    <p class="st-asg-cloud">
                        <i class="fas fa-cloud-upload-alt" aria-hidden="true"></i>
                        {{ __('student_timeline.asg_cloud_hint') }}
                    </p>
                @endif

                <form id="assignment-submit-form" action="{{ route('student.assignments.submit', $assignment) }}" method="post" enctype="multipart/form-data" class="st-asg-form">
                    @csrf
                    <div id="assignment-direct-tokens"></div>

                    <label class="st-asg-field">
                        <span>{{ __('student_timeline.asg_content_label') }}</span>
                        <textarea name="content" rows="7" placeholder="{{ __('student_timeline.asg_content_placeholder') }}">{{ old('content', ($submission && $submission->status === 'returned') ? ($submission->content ?? '') : '') }}</textarea>
                        <em>{{ __('student_timeline.asg_content_optional') }}</em>
                    </label>

                    <div class="st-asg-field">
                        <span>{{ __('student_timeline.asg_attachments_label') }}</span>
                        @if(!empty($directUploadToCloud))
                            <div class="st-asg-upload">
                                <input type="file" id="mx-assignment-direct-picker" multiple accept=".pdf,.doc,.docx,.zip,.rar,.jpg,.jpeg,.png,.gif,.webp">
                                <p id="mx-assignment-upload-status" class="st-asg-upload__status is-hidden" hidden></p>
                                <ul id="mx-assignment-remote-list" class="st-asg-remote-list"></ul>
                            </div>
                        @endif
                        <div id="mx-assignment-classic-files-wrap" class="{{ !empty($directUploadToCloud) ? 'is-hidden' : '' }}">
                            @if(!empty($directUploadToCloud))
                                <p class="st-asg-muted">{{ __('student_timeline.asg_classic_fallback') }}</p>
                            @endif
                            <input type="file" name="attachments[]" multiple accept=".pdf,.doc,.docx,.zip,.rar,.jpg,.jpeg,.png,.gif,.webp">
                        </div>
                        @if(!empty($directUploadToCloud))
                            <button type="button" id="mx-assignment-toggle-classic" class="st-asg-linkbtn">
                                {{ __('student_timeline.asg_toggle_classic') }}
                            </button>
                        @endif
                        <em>
                            {{ __('student_timeline.asg_attachments_hint') }}
                            @if($submission && $submission->status === 'returned')
                                {{ __('student_timeline.asg_attachments_returned_note') }}
                            @endif
                        </em>
                    </div>

                    <button type="submit" class="st-pill st-pill--solid st-pill--lg st-asg-submit">
                        <i class="fas fa-paper-plane" aria-hidden="true"></i>
                        {{ __('student_timeline.asg_send') }}
                    </button>
                </form>
            </article>
        @elseif($submitBlockReason)
            <div class="st-asg-block">
                <i class="fas fa-info-circle" aria-hidden="true"></i>
                <p>{{ $submitBlockReason }}</p>
            </div>
        @endif
    </aside>
</section>
@endsection

@section('events')
<div class="st-events__top">
    <h2>{{ __('student_timeline.nav_assignments') }}</h2>
</div>
<a href="{{ route('student.assignments.index') }}" class="st-event-card st-event-card--orange">
    <h3>{{ __('student_timeline.assignments_title') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.assignments_hint') }}</p>
</a>
@if(Route::has('student.library.materials'))
    <a href="{{ route('student.library.materials') }}" class="st-event-card st-event-card--blue">
        <h3>{{ __('student_timeline.nav_library_materials') }}</h3>
        <p class="st-event-card__sub">{{ __('student_timeline.materials_hint') }}</p>
    </a>
@endif
<div class="st-events__see">
    <a href="{{ route('dashboard') }}">{{ __('student_timeline.school_gate') }}</a>
</div>
@endsection

@if(!empty($directUploadToCloud))
@push('scripts')
<script>
(function() {
    var cfg = {
        presignUrl: @json(route('student.assignments.submission.presign-upload', $assignment)),
        completeUrl: @json(route('student.assignments.submission.complete-upload', $assignment)),
        abandonUrl: @json(route('student.assignments.submission.abandon-upload', $assignment)),
        csrf: @json(csrf_token()),
        maxFiles: 10,
        maxBytes: 40960 * 1024,
        msgWait: @json(__('student_timeline.asg_js_wait_upload')),
        msgTooBig: @json(__('student_timeline.asg_js_too_big')),
        msgMaxFiles: @json(__('student_timeline.asg_js_max_files')),
        msgUploading: @json(__('student_timeline.asg_js_uploading')),
        msgRemove: @json(__('student_timeline.asg_js_remove')),
        msgFail: @json(__('student_timeline.asg_js_fail'))
    };
    var form = document.getElementById('assignment-submit-form');
    var tokenBox = document.getElementById('assignment-direct-tokens');
    var picker = document.getElementById('mx-assignment-direct-picker');
    var listEl = document.getElementById('mx-assignment-remote-list');
    var statusEl = document.getElementById('mx-assignment-upload-status');
    var classicWrap = document.getElementById('mx-assignment-classic-files-wrap');
    var toggleClassic = document.getElementById('mx-assignment-toggle-classic');
    if (!form || !tokenBox || !picker || !listEl) return;

    var remoteItems = [];

    function setStatus(msg, isErr) {
        if (!statusEl) return;
        if (!msg) {
            statusEl.hidden = true;
            statusEl.classList.add('is-hidden');
            statusEl.textContent = '';
            return;
        }
        statusEl.hidden = false;
        statusEl.classList.remove('is-hidden');
        statusEl.textContent = msg;
        statusEl.classList.toggle('is-err', !!isErr);
    }

    function syncHiddenTokens() {
        tokenBox.innerHTML = '';
        remoteItems.forEach(function(item) {
            if (!item.file_token) return;
            var inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'direct_file_tokens[]';
            inp.value = item.file_token;
            tokenBox.appendChild(inp);
        });
    }

    function totalAttachmentSlots() {
        var classicInput = classicWrap ? classicWrap.querySelector('input[type="file"]') : null;
        var nClassic = 0;
        if (classicInput && classicInput.files) nClassic = classicInput.files.length;
        return remoteItems.length + nClassic;
    }

    function putBlobToPresignedUrl(url, blob, contentType, extraHeaders) {
        return new Promise(function(resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.open('PUT', url, true);
            xhr.timeout = 0;
            if (contentType) xhr.setRequestHeader('Content-Type', contentType);
            if (extraHeaders && typeof extraHeaders === 'object') {
                Object.keys(extraHeaders).forEach(function(k) {
                    try { xhr.setRequestHeader(k, extraHeaders[k]); } catch (e) {}
                });
            }
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) resolve();
                else reject(new Error('HTTP ' + xhr.status));
            };
            xhr.onerror = function() { reject(new Error('network')); };
            xhr.send(blob);
        });
    }

    function renderList() {
        listEl.innerHTML = '';
        remoteItems.forEach(function(item, idx) {
            var li = document.createElement('li');
            var label = document.createElement('span');
            label.textContent = item.name + (item.uploading ? ' — …' : '');
            li.appendChild(label);
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = cfg.msgRemove;
            btn.disabled = !!item.uploading;
            btn.addEventListener('click', function() {
                if (item.uploading) return;
                if (item.file_token) {
                    fetch(cfg.abandonUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': cfg.csrf,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ file_token: item.file_token })
                    }).catch(function() {});
                }
                remoteItems.splice(idx, 1);
                syncHiddenTokens();
                renderList();
            });
            li.appendChild(btn);
            listEl.appendChild(li);
        });
        syncHiddenTokens();
    }

    async function uploadFile(file) {
        if (file.size > cfg.maxBytes) {
            setStatus(cfg.msgTooBig.replace(':name', file.name), true);
            return;
        }
        if (totalAttachmentSlots() >= cfg.maxFiles) {
            setStatus(cfg.msgMaxFiles, true);
            return;
        }
        var entry = { name: file.name, file_token: null, uploading: true };
        remoteItems.push(entry);
        renderList();
        setStatus(cfg.msgUploading.replace(':name', file.name), false);
        try {
            var presignRes = await fetch(cfg.presignUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': cfg.csrf,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    content_type: file.type || 'application/octet-stream',
                    original_name: file.name
                })
            });
            var presignData = {};
            try { presignData = await presignRes.json(); } catch (e) { presignData = {}; }
            if (!presignRes.ok || !presignData.direct_upload || !presignData.upload_url || !presignData.upload_token) {
                throw new Error((presignData && presignData.message) ? presignData.message : cfg.msgFail);
            }
            await putBlobToPresignedUrl(
                presignData.upload_url,
                file,
                presignData.content_type || file.type || 'application/octet-stream',
                presignData.headers || {}
            );
            var completeRes = await fetch(cfg.completeUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': cfg.csrf,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    upload_token: presignData.upload_token,
                    original_name: file.name
                })
            });
            var completeData = {};
            try { completeData = await completeRes.json(); } catch (e2) { completeData = {}; }
            if (!completeRes.ok || !completeData.file_token) {
                throw new Error((completeData && completeData.message) ? completeData.message : cfg.msgFail);
            }
            entry.uploading = false;
            entry.file_token = completeData.file_token;
            setStatus('', false);
        } catch (err) {
            var ix = remoteItems.indexOf(entry);
            if (ix >= 0) remoteItems.splice(ix, 1);
            setStatus((err && err.message) ? err.message : cfg.msgFail, true);
        }
        renderList();
        picker.value = '';
    }

    picker.addEventListener('change', function() {
        var files = picker.files;
        if (!files || !files.length) return;
        setStatus('', false);
        var chain = Promise.resolve();
        for (var i = 0; i < files.length; i++) {
            (function(f) {
                chain = chain.then(function() { return uploadFile(f); });
            })(files[i]);
        }
    });

    if (toggleClassic && classicWrap) {
        toggleClassic.addEventListener('click', function() {
            classicWrap.classList.toggle('is-hidden');
        });
    }

    form.addEventListener('submit', function(e) {
        if (remoteItems.some(function(x) { return x.uploading; })) {
            e.preventDefault();
            setStatus(cfg.msgWait, true);
            return;
        }
        if (totalAttachmentSlots() > cfg.maxFiles) {
            e.preventDefault();
            setStatus(cfg.msgMaxFiles, true);
        }
    });
})();
</script>
@endpush
@endif
