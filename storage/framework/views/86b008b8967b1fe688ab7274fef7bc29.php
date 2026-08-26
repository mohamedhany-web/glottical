<?php $__env->startSection('title', $session->course->title ?? __('student_timeline.private_lesson')); ?>

<?php $__env->startSection('content'); ?>
<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $viewerTz = auth()->user()?->timezoneCode() ?? \App\Support\AppTimezone::academy();
    $status = $session->status;
    $isScheduled = $status === \App\Models\OneToOneSession::STATUS_SCHEDULED;
    $isPending = $status === \App\Models\OneToOneSession::STATUS_PENDING;
    $isCompleted = $status === \App\Models\OneToOneSession::STATUS_COMPLETED;
    $awaiting = method_exists($session, 'isAwaitingTeacherStart') && $session->isAwaitingTeacherStart();
    $joinHref = ($isScheduled && $session->classroomMeeting)
        ? route('classroom.secure-enter', $session->classroomMeeting)
        : null;
    $recordingHref = ($session->classroomMeeting
        && $session->classroomMeeting->hasBrowserRecording()
        && (
            $isCompleted
            || $session->classroomMeeting->ended_at
        )
        && Route::has('student.classroom.recording'))
        ? route('student.classroom.recording', $session->classroomMeeting)
        : null;
    $recordingStatusUrl = ($session->classroomMeeting
        && (
            $isCompleted
            || $session->classroomMeeting->ended_at
        )
        && ! $recordingHref
        && Route::has('student.classroom.recording.status'))
        ? route('student.classroom.recording.status', $session->classroomMeeting)
        : null;
    $duration = (int) ($session->duration_minutes ?: 50);
    $instructor = $session->instructor;
    $title = $session->course->title ?? __('student_timeline.private_lesson');
    $lessonsUrl = Route::has('student.private-lectures.index')
        ? route('student.private-lectures.index')
        : route('student.one-to-one-sessions.index');
    $badgeTone = match ($status) {
        \App\Models\OneToOneSession::STATUS_SCHEDULED => 'live',
        \App\Models\OneToOneSession::STATUS_COMPLETED => 'done',
        \App\Models\OneToOneSession::STATUS_PENDING => 'soon',
        default => 'off',
    };
    $groupedSlots = collect();
    if ($isPending && $availableSlots->isNotEmpty()) {
        $groupedSlots = $availableSlots->groupBy(fn ($s) => $s['starts_at']->copy()->timezone($viewerTz)->format('Y-m-d'));
    }
    $avatar = $instructor?->avatarDisplayUrl() ?? asset('img/student-timeline/avatar.png');
?>

<?php echo $__env->make('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => $title,
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.nav_lessons'), 'url' => $lessonsUrl],
        ['label' => __('student.one_to_one_session_number', ['n' => $session->session_number]), 'url' => null],
    ],
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(session('success')): ?>
    <div class="st-flash st-flash--ok"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if($errors->any()): ?>
    <div class="st-flash st-flash--err"><?php echo e($errors->first()); ?></div>
<?php endif; ?>

<?php if($isScheduled): ?>
    <section class="st-join-hero" aria-label="<?php echo e(__('student.one_to_one_join_session')); ?>">
        <div class="st-join-hero__copy">
            <p class="st-join-hero__kicker">
                <?php echo e($awaiting ? __('student_timeline.teacher_starting') : __('student_timeline.next_private_lesson')); ?>

            </p>
            <h2 class="st-join-hero__title"><?php echo e($title); ?></h2>
            <p class="st-join-hero__meta">
                <?php if($instructor): ?>
                    <?php echo e($instructor->name); ?> ·
                <?php endif; ?>
                <?php if($session->scheduled_at): ?>
                    <?php if (isset($component)) { $__componentOriginal4bdb6aac1f9ecf59585773b3a0097468 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4bdb6aac1f9ecf59585773b3a0097468 = $attributes; } ?>
<?php $component = App\View\Components\AppDatetime::resolve(['at' => $session->scheduled_at,'timezone' => $viewerTz,'pattern' => $isRtl ? 'l، d M · g:i A' : 'D, M j · g:i A'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-datetime'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppDatetime::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4bdb6aac1f9ecf59585773b3a0097468)): ?>
<?php $attributes = $__attributesOriginal4bdb6aac1f9ecf59585773b3a0097468; ?>
<?php unset($__attributesOriginal4bdb6aac1f9ecf59585773b3a0097468); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4bdb6aac1f9ecf59585773b3a0097468)): ?>
<?php $component = $__componentOriginal4bdb6aac1f9ecf59585773b3a0097468; ?>
<?php unset($__componentOriginal4bdb6aac1f9ecf59585773b3a0097468); ?>
<?php endif; ?>
                <?php endif; ?>
                · <?php echo e($duration); ?> <?php echo e(__('student_timeline.minutes')); ?>

            </p>
        </div>
        <div class="st-join-hero__actions">
            <?php if($joinHref): ?>
                <a href="<?php echo e($joinHref); ?>" class="st-pill st-pill--solid st-pill--lg">
                    <i class="fas fa-video" aria-hidden="true"></i>
                    <?php echo e(__('student_timeline.join_now')); ?>

                </a>
            <?php elseif($awaiting): ?>
                <span class="st-pill st-pill--outline"><?php echo e(__('student_timeline.teacher_starting')); ?></span>
            <?php endif; ?>
            <a href="<?php echo e($lessonsUrl); ?>" class="st-pill st-pill--outline"><?php echo e(__('student_timeline.nav_lessons')); ?></a>
        </div>
    </section>
<?php endif; ?>

<section class="st-stats st-stats--classes" aria-label="<?php echo e(__('student_timeline.oto_session_details')); ?>">
    <article class="st-stat-card">
        <p class="st-stat-card__label"><?php echo e(__('student_timeline.oto_status')); ?></p>
        <p class="st-stat-card__value" style="font-size:1.05rem"><?php echo e($session->statusLabel()); ?></p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label"><?php echo e(__('student_timeline.oto_duration')); ?></p>
        <p class="st-stat-card__value"><?php echo e($duration); ?></p>
        <p class="st-stat-card__hint"><?php echo e(__('student_timeline.minutes')); ?></p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label"><?php echo e(__('student_timeline.oto_your_clock')); ?></p>
        <p class="st-stat-card__value" style="font-size:.95rem;line-height:1.35"><?php echo e(\App\Support\AppTimezone::label($viewerTz)); ?></p>
    </article>
</section>

<div class="st-class-detail">
    <div class="st-class-detail__main">
        <section class="st-panel">
            <div class="st-section-head">
                <div>
                    <h2><?php echo e(__('student_timeline.oto_session_details')); ?></h2>
                    <p><?php echo e(__('student.one_to_one_session_number', ['n' => $session->session_number])); ?></p>
                </div>
            </div>
            <div class="st-lesson-card__main" style="margin-bottom:14px">
                <img class="st-lesson-card__avatar" src="<?php echo e($avatar); ?>" alt="" width="48" height="48">
                <div class="st-lesson-card__copy">
                    <div class="st-lesson-card__badges">
                        <span class="st-lesson-card__badge"><?php echo e(__('student_timeline.private_lesson')); ?></span>
                        <span class="st-session-badge st-session-badge--<?php echo e($badgeTone); ?>"><?php echo e($session->statusLabel()); ?></span>
                    </div>
                    <h3 style="margin:0;font-size:1.05rem;font-weight:900"><?php echo e($title); ?></h3>
                    <p class="st-lesson-card__meta"><?php echo e($instructor->name ?? '—'); ?></p>
                </div>
            </div>
            <dl class="st-oto-facts">
                <div>
                    <dt><?php echo e(__('student.one_to_one_appointment')); ?></dt>
                    <dd>
                        <?php if($session->scheduled_at): ?>
                            <?php if (isset($component)) { $__componentOriginal4bdb6aac1f9ecf59585773b3a0097468 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4bdb6aac1f9ecf59585773b3a0097468 = $attributes; } ?>
<?php $component = App\View\Components\AppDatetime::resolve(['at' => $session->scheduled_at,'timezone' => $viewerTz,'pattern' => $isRtl ? 'l، d M · g:i A' : 'D, M j · g:i A'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-datetime'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppDatetime::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4bdb6aac1f9ecf59585773b3a0097468)): ?>
<?php $attributes = $__attributesOriginal4bdb6aac1f9ecf59585773b3a0097468; ?>
<?php unset($__attributesOriginal4bdb6aac1f9ecf59585773b3a0097468); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4bdb6aac1f9ecf59585773b3a0097468)): ?>
<?php $component = $__componentOriginal4bdb6aac1f9ecf59585773b3a0097468; ?>
<?php unset($__componentOriginal4bdb6aac1f9ecf59585773b3a0097468); ?>
<?php endif; ?>
                        <?php else: ?>
                            <?php echo e(__('student_timeline.awaiting_schedule')); ?>

                        <?php endif; ?>
                    </dd>
                </div>
                <div>
                    <dt><?php echo e(__('landing.nav.instructors')); ?></dt>
                    <dd><?php echo e($instructor->name ?? '—'); ?></dd>
                </div>
            </dl>
        </section>

        <?php if($isPending): ?>
            <section class="st-panel">
                <div class="st-section-head">
                    <div>
                        <h2><?php echo e(__('student.one_to_one_pick_slot')); ?></h2>
                        <p><?php echo e(__('student_timeline.oto_pick_available')); ?></p>
                    </div>
                </div>
                <?php if($groupedSlots->isEmpty()): ?>
                    <div class="st-empty-panel">
                        <h3><?php echo e(__('student_timeline.awaiting_schedule')); ?></h3>
                        <p><?php echo e(__('student_timeline.oto_waiting_placement')); ?></p>
                    </div>
                <?php else: ?>
                    <form method="POST" action="<?php echo e(route('student.one-to-one-sessions.book', $session)); ?>" class="st-oto-form">
                        <?php echo csrf_field(); ?>
                        <?php $__currentLoopData = $groupedSlots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $daySlots): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="st-slot-day">
                                <p class="st-slot-day__label"><?php echo e(\Carbon\Carbon::parse($date, $viewerTz)->locale($locale)->isoFormat('dddd D MMMM')); ?></p>
                                <div class="st-slot-chips">
                                    <?php $__currentLoopData = $daySlots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="st-slot-chip">
                                            <input type="radio" name="scheduled_at" value="<?php echo e($slot['starts_at']->copy()->utc()->toIso8601String()); ?>" required>
                                            <span><?php echo e($slot['starts_at']->copy()->timezone($viewerTz)->format('g:i A')); ?></span>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <button type="submit" class="st-pill st-pill--solid">
                            <i class="fas fa-calendar-check" aria-hidden="true"></i>
                            <?php echo e(__('student_timeline.oto_confirm')); ?>

                        </button>
                    </form>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <?php if($isCompleted || $recordingHref || $recordingStatusUrl): ?>
            <div class="st-empty-panel" id="mx-oto-recording-panel">
                <h3><?php echo e($session->statusLabel()); ?></h3>
                <p>
                    <?php if($session->scheduled_at): ?>
                        <?php if (isset($component)) { $__componentOriginal4bdb6aac1f9ecf59585773b3a0097468 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4bdb6aac1f9ecf59585773b3a0097468 = $attributes; } ?>
<?php $component = App\View\Components\AppDatetime::resolve(['at' => $session->scheduled_at,'timezone' => $viewerTz,'pattern' => $isRtl ? 'l، d M · g:i A' : 'D, M j · g:i A'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-datetime'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppDatetime::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4bdb6aac1f9ecf59585773b3a0097468)): ?>
<?php $attributes = $__attributesOriginal4bdb6aac1f9ecf59585773b3a0097468; ?>
<?php unset($__attributesOriginal4bdb6aac1f9ecf59585773b3a0097468); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4bdb6aac1f9ecf59585773b3a0097468)): ?>
<?php $component = $__componentOriginal4bdb6aac1f9ecf59585773b3a0097468; ?>
<?php unset($__componentOriginal4bdb6aac1f9ecf59585773b3a0097468); ?>
<?php endif; ?>
                    <?php endif; ?>
                </p>
                <?php if($recordingHref): ?>
                    <div class="st-biz-banner__actions" style="margin-top:14px" id="mx-oto-recording-actions">
                        <a href="<?php echo e($recordingHref); ?>" class="st-pill st-pill--solid" target="_blank" rel="noopener">
                            <i class="fas fa-play-circle" aria-hidden="true"></i>
                            <?php echo e(__('student_timeline.watch_recording')); ?>

                        </a>
                    </div>
                <?php elseif($recordingStatusUrl): ?>
                    <div class="st-biz-banner__actions" style="margin-top:14px" id="mx-oto-recording-actions">
                        <span class="st-pill st-pill--outline" id="mx-oto-recording-wait">
                            <i class="fas fa-spinner fa-spin" aria-hidden="true"></i>
                            <?php echo e($isRtl ? 'جاري تجهيز التسجيل… سيظهر هنا تلقائياً' : 'Preparing recording… it will appear here shortly'); ?>

                        </span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <aside class="st-class-detail__side">
        <section class="st-panel st-panel--side">
            <div class="st-section-head">
                <div>
                    <h2><?php echo e(__('student_timeline.nav_lessons')); ?></h2>
                    <p><?php echo e(__('student_timeline.private_lessons_hint')); ?></p>
                </div>
            </div>
            <div class="st-event-card__actions" style="margin-top:0;flex-direction:column" id="mx-oto-recording-side">
                <?php if($recordingHref): ?>
                    <a href="<?php echo e($recordingHref); ?>" class="st-pill st-pill--solid st-pill--block" target="_blank" rel="noopener">
                        <i class="fas fa-play-circle" aria-hidden="true"></i>
                        <?php echo e(__('student_timeline.watch_recording')); ?>

                    </a>
                <?php elseif($recordingStatusUrl): ?>
                    <span class="st-pill st-pill--outline st-pill--block" id="mx-oto-recording-side-wait">
                        <i class="fas fa-spinner fa-spin" aria-hidden="true"></i>
                        <?php echo e($isRtl ? 'جاري تجهيز التسجيل…' : 'Preparing recording…'); ?>

                    </span>
                <?php endif; ?>
                <a href="<?php echo e($lessonsUrl); ?>" class="st-pill st-pill--<?php echo e(($recordingHref || $recordingStatusUrl) ? 'outline' : 'solid'); ?> st-pill--block"><?php echo e(__('student_timeline.nav_lessons')); ?></a>
                <?php if(Route::has('calendar')): ?>
                    <a href="<?php echo e(route('calendar')); ?>" class="st-pill st-pill--outline st-pill--block"><?php echo e(__('student.calendar_title')); ?></a>
                <?php endif; ?>
                <?php if($instructor && Route::has('student.private-messages.with')): ?>
                    <a href="<?php echo e(route('student.private-messages.with', $instructor)); ?>" class="st-pill st-pill--outline st-pill--block"><?php echo e(__('student_timeline.open_chats')); ?></a>
                <?php elseif(Route::has('student.private-messages.index')): ?>
                    <a href="<?php echo e(route('student.private-messages.index')); ?>" class="st-pill st-pill--outline st-pill--block"><?php echo e(__('student_timeline.nav_feed')); ?></a>
                <?php endif; ?>
            </div>
        </section>
    </aside>
</div>
<?php if($recordingStatusUrl): ?>
<script>
(function () {
    var statusUrl = <?php echo json_encode($recordingStatusUrl, 15, 512) ?>;
    var label = <?php echo json_encode(__('student_timeline.watch_recording'), 15, 512) ?>;
    var tries = 0;
    var maxTries = 60;
    function paint(url) {
        if (!url) return;
        var main = document.getElementById('mx-oto-recording-actions');
        if (main) {
            main.innerHTML = '<a href="' + url + '" class="st-pill st-pill--solid" target="_blank" rel="noopener"><i class="fas fa-play-circle" aria-hidden="true"></i> ' + label + '</a>';
        }
        var sideWait = document.getElementById('mx-oto-recording-side-wait');
        if (sideWait && sideWait.parentNode) {
            var a = document.createElement('a');
            a.href = url;
            a.className = 'st-pill st-pill--solid st-pill--block';
            a.target = '_blank';
            a.rel = 'noopener';
            a.innerHTML = '<i class="fas fa-play-circle" aria-hidden="true"></i> ' + label;
            sideWait.parentNode.replaceChild(a, sideWait);
        }
    }
    function tick() {
        if (tries++ >= maxTries) return;
        fetch(statusUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) {
                if (data && data.ready && data.watch_url) {
                    paint(data.watch_url);
                    return;
                }
                setTimeout(tick, 3000);
            })
            .catch(function () { setTimeout(tick, 4000); });
    }
    setTimeout(tick, 1500);
})();
</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student-timeline', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/glottical/resources/views/student/one-to-one-sessions/show.blade.php ENDPATH**/ ?>