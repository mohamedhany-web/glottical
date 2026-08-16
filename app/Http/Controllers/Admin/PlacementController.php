<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OneToOneSession;
use App\Models\ServicePackage;
use App\Models\StudentServiceEntitlement;
use App\Models\TutoringGroup;
use App\Models\TutoringGroupBooking;
use App\Models\User;
use App\Services\OneToOneAvailabilityService;
use App\Services\OneToOneSessionService;
use App\Services\StudentEntitlementService;
use App\Services\TutoringGroupAvailabilityService;
use App\Services\TutoringGroupOrchestrationService;
use App\Support\AppTimezone;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class PlacementController extends Controller
{
    public function index(): View
    {
        $pendingOneToOne = OneToOneSession::query()
            ->where('status', OneToOneSession::STATUS_PENDING)
            ->count();
        $upcomingOneToOne = OneToOneSession::query()
            ->where('status', OneToOneSession::STATUS_SCHEDULED)
            ->where('scheduled_at', '>=', now())
            ->count();
        $upcomingGroups = TutoringGroupBooking::query()
            ->where('status', TutoringGroupBooking::STATUS_CONFIRMED)
            ->where('starts_at', '>=', now())
            ->count();
        $bookableCredits = StudentServiceEntitlement::query()
            ->active()
            ->get()
            ->sum(fn (StudentServiceEntitlement $e) => StudentEntitlementService::bookableUnitsLeft($e));

        $recentPrivate = OneToOneSession::query()
            ->with(['student:id,name', 'instructor:id,name'])
            ->where('status', '!=', OneToOneSession::STATUS_CANCELLED)
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();
        $recentGroups = TutoringGroupBooking::query()
            ->with(['user:id,name', 'instructor:id,name', 'tutoringGroup:id,title'])
            ->where('status', '!=', TutoringGroupBooking::STATUS_CANCELLED)
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        return view('admin.placement.index', compact(
            'pendingOneToOne',
            'upcomingOneToOne',
            'upcomingGroups',
            'bookableCredits',
            'recentPrivate',
            'recentGroups'
        ));
    }

    public function create(Request $request): View
    {
        $selectedStudentId = (int) $request->integer('student_id');
        $selectedEntitlementId = (int) $request->integer('entitlement_id');

        if ($selectedEntitlementId > 0 && $selectedStudentId < 1) {
            $selectedStudentId = (int) (StudentServiceEntitlement::query()
                ->whereKey($selectedEntitlementId)
                ->value('user_id') ?: 0);
        }

        $students = User::query()
            ->where('role', 'student')
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(800)
            ->get(['id', 'name', 'email', 'phone']);

        $instructors = User::query()
            ->whereIn('role', ['instructor', 'teacher'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'timezone']);

        $groups = TutoringGroup::query()
            ->active()
            ->with('instructor:id,name')
            ->orderBy('title')
            ->get(['id', 'title', 'type', 'instructor_id', 'duration_minutes', 'academic_year_id', 'academic_subject_id']);

        return view('admin.placement.create', [
            'students' => $students,
            'instructors' => $instructors,
            'groups' => $groups,
            'selectedStudentId' => $selectedStudentId,
            'selectedEntitlementId' => $selectedEntitlementId,
            'mode' => in_array($request->query('mode'), ['private', 'group'], true)
                ? $request->query('mode')
                : 'private',
            'grantUrl' => Route::has('admin.student-entitlements.create')
                ? route('admin.student-entitlements.create')
                : null,
            'durationMinutes' => OneToOneSession::defaultDurationMinutes(),
            'dayLabels' => OneToOneAvailabilityService::dayLabels(),
        ]);
    }

    public function studentContext(Request $request): JsonResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $student = User::query()->findOrFail($data['student_id']);
        if ($student->role !== 'student') {
            return response()->json(['ok' => false, 'message' => 'المستخدم المحدد ليس طالباً.'], 422);
        }

        StudentEntitlementService::expireStaleForUser((int) $student->id);

        $entitlements = StudentServiceEntitlement::query()
            ->forUser((int) $student->id)
            ->active()
            ->with(['servicePackage:id,name', 'tutoringGroup:id,title,type'])
            ->orderBy('expires_at')
            ->get()
            ->map(function (StudentServiceEntitlement $entitlement) {
                $left = StudentEntitlementService::bookableUnitsLeft($entitlement);
                $kind = match ($entitlement->scope) {
                    ServicePackage::SCOPE_PRIVATE_LESSONS, ServicePackage::SCOPE_GLOBAL => 'private',
                    ServicePackage::SCOPE_TUTORING_INDIVIDUAL, ServicePackage::SCOPE_TUTORING_COLLECTIVE => 'group',
                    default => 'other',
                };

                return [
                    'id' => $entitlement->id,
                    'scope' => $entitlement->scope,
                    'scope_label' => ServicePackage::scopes()[$entitlement->scope] ?? $entitlement->scope,
                    'kind' => $kind,
                    'units_left' => $left,
                    'bookable' => $left > 0,
                    'package' => $entitlement->servicePackage?->name,
                    'group_id' => $entitlement->tutoring_group_id,
                    'group_title' => $entitlement->tutoringGroup?->title,
                    'expires_at' => optional($entitlement->expires_at)->format('Y-m-d'),
                ];
            })
            ->values();

        $bookable = $entitlements->where('bookable', true)->values();

        return response()->json([
            'ok' => true,
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'phone' => $student->phone,
            ],
            'has_package' => $bookable->isNotEmpty(),
            'entitlements' => $entitlements,
            'bookable_entitlements' => $bookable,
            'private_units' => (int) $bookable->where('kind', 'private')->sum('units_left'),
            'group_units' => (int) $bookable->where('kind', 'group')->sum('units_left'),
        ]);
    }

    public function slots(Request $request): JsonResponse
    {
        $data = $request->validate([
            'mode' => ['required', 'in:private,group'],
            'instructor_id' => ['nullable', 'integer', 'exists:users,id'],
            'tutoring_group_id' => ['nullable', 'integer', 'exists:tutoring_groups,id'],
        ]);

        if ($data['mode'] === 'private') {
            $instructorId = (int) ($data['instructor_id'] ?? 0);
            if ($instructorId < 1) {
                return response()->json(['ok' => false, 'message' => 'اختر معلماً أولاً.'], 422);
            }

            $instructor = User::query()->find($instructorId);
            $clockTz = AppTimezone::forUser($instructor);
            $duration = OneToOneSession::defaultDurationMinutes();
            $slots = OneToOneAvailabilityService::availableSlots(
                $instructorId,
                now()->addHour(),
                now()->addWeeks(5),
                $duration
            )->take(120)->map(function ($slot) use ($clockTz) {
                $at = $slot['starts_at'] instanceof Carbon
                    ? $slot['starts_at']->copy()->utc()
                    : Carbon::parse($slot['starts_at'])->utc();
                $clock = $at->copy()->timezone($clockTz);
                $viewer = $at->copy()->timezone($viewerTz);

                return [
                    'starts_at' => $at->toIso8601String(),
                    'label' => $clock->locale('ar')->translatedFormat('D j M · g:i A').' · '.AppTimezone::label($clockTz),
                    'day_of_week' => (int) $clock->dayOfWeekIso,
                    'time' => $clock->format('H:i'),
                ];
            })->values();

            $dayLabels = OneToOneAvailabilityService::dayLabels();
            $weeklyWindows = OneToOneAvailabilityService::rulesForInstructor($instructorId)
                ->map(function ($rule) use ($dayLabels) {
                    $start = is_string($rule->start_time) ? substr($rule->start_time, 0, 5) : $rule->start_time->format('H:i');
                    $end = is_string($rule->end_time) ? substr($rule->end_time, 0, 5) : $rule->end_time->format('H:i');
                    $day = (int) $rule->day_of_week;

                    return [
                        'day_of_week' => $day,
                        'day_label' => $dayLabels[$day] ?? (string) $day,
                        'start_time' => $start,
                        'end_time' => $end,
                        'slot_duration_minutes' => (int) ($rule->slot_duration_minutes ?: OneToOneSession::defaultDurationMinutes()),
                        'label' => ($dayLabels[$day] ?? $day).' · '.$start,
                    ];
                })
                ->unique(fn ($w) => $w['day_of_week'].'|'.$w['start_time'])
                ->values();

            return response()->json([
                'ok' => true,
                'mode' => 'private',
                'duration_minutes' => $duration,
                'slots' => $slots,
                'weekly_windows' => $weeklyWindows,
                'day_labels' => $dayLabels,
                'timezone' => $clockTz,
                'timezone_label' => AppTimezone::label($clockTz),
                'empty_hint' => $slots->isEmpty()
                    ? 'لا نوافذ في جدول التوافر — اكتب الموعد يدوياً بتوقيت المعلم بعد التنسيق على واتساب.'
                    : null,
            ]);
        }

        $groupId = (int) ($data['tutoring_group_id'] ?? 0);
        if ($groupId < 1) {
            return response()->json(['ok' => false, 'message' => 'اختر مجموعة أولاً.'], 422);
        }

        $group = TutoringGroup::query()->active()->with('instructor:id,name,timezone')->findOrFail($groupId);
        $viewerTz = AppTimezone::forUser($request->user());
        $slots = TutoringGroupAvailabilityService::availableSlots(
            $group,
            now()->addHour(),
            now()->addDays(21)
        )->take(48)->map(function ($slot) use ($viewerTz) {
            $at = Carbon::parse($slot['starts_at'])->utc();

            return [
                'starts_at' => $at->toIso8601String(),
                'label' => $at->copy()->timezone($viewerTz)->locale('ar')->translatedFormat('D j M · g:i A'),
            ];
        })->values();

        return response()->json([
            'ok' => true,
            'mode' => 'group',
            'instructor_id' => (int) $group->instructor_id,
            'instructor_name' => $group->instructor?->name,
            'duration_minutes' => (int) ($group->duration_minutes ?: 60),
            'slots' => $slots,
            'empty_hint' => $slots->isEmpty()
                ? 'لا مواعيد متاحة لهذه المجموعة. راجع جدول عمل المدرب للمجموعات.'
                : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'mode' => ['required', 'in:private,group'],
            'booking_style' => ['nullable', 'in:single,monthly,multi'],
            'student_id' => ['required', 'integer', 'exists:users,id'],
            'student_service_entitlement_id' => ['required', 'integer', 'exists:student_service_entitlements,id'],
            'instructor_id' => ['nullable', 'integer', 'exists:users,id'],
            'tutoring_group_id' => ['nullable', 'integer', 'exists:tutoring_groups,id'],
            'scheduled_at' => ['nullable', 'date'],
            'manual_scheduled_at' => ['nullable', 'date'],
            'timezone' => AppTimezone::inputRules(),
            'scheduled_ats' => ['nullable', 'array', 'max:40'],
            'scheduled_ats.*' => ['date', 'after:now'],
            'weeks' => ['nullable', 'integer', 'min:1', 'max:8'],
            'weekly_slots' => ['nullable', 'array', 'max:7'],
            'weekly_slots.*.day_of_week' => ['nullable', 'integer', 'min:1', 'max:7'],
            'weekly_slots.*.time' => ['nullable', 'string', 'max:8'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $student = User::query()->findOrFail($data['student_id']);
        if ($student->role !== 'student') {
            return back()->withInput()->with('error', 'المستخدم المحدد ليس طالباً.');
        }

        $entitlement = StudentServiceEntitlement::query()
            ->with('user')
            ->findOrFail($data['student_service_entitlement_id']);

        if ((int) $entitlement->user_id !== (int) $student->id) {
            return back()->withInput()->with('error', 'الرصيد لا يخص هذا الطالب.');
        }
        if (! $entitlement->isActive() || StudentEntitlementService::bookableUnitsLeft($entitlement) < 1) {
            return back()->withInput()->with('error', 'الطالب ليس لديه باقة/رصيد قابل للحجز.');
        }

        if ($data['mode'] === 'private') {
            return $this->storePrivate($request, $data, $entitlement);
        }

        return $this->storeGroup($request, $data, $entitlement);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function storePrivate(Request $request, array $data, StudentServiceEntitlement $entitlement): RedirectResponse
    {
        if (! in_array($entitlement->scope, [ServicePackage::SCOPE_PRIVATE_LESSONS, ServicePackage::SCOPE_GLOBAL], true)) {
            return back()->withInput()->with('error', 'هذا الرصيد ليس لحصص خاصة 1:1. اختر رصيد حصص خاصة أو عام.');
        }

        $instructorId = (int) ($data['instructor_id'] ?? 0);
        if ($instructorId < 1) {
            return back()->withInput()->with('error', 'اختر معلماً.');
        }

        $instructor = User::query()->findOrFail($instructorId);
        if (! $instructor->isInstructor()) {
            return back()->withInput()->with('error', 'المستخدم المحدد ليس معلماً.');
        }

        $style = $data['booking_style'] ?? 'single';
        $student = User::query()->findOrFail($entitlement->user_id);
        $notes = trim(($data['notes'] ?? '')."\nتسكين من لوحة الطلاب والخدمات");

        try {
            if ($style === 'monthly') {
                $weekly = collect($data['weekly_slots'] ?? [])
                    ->filter(fn ($row) => ! empty($row['day_of_week']) && ! empty($row['time']))
                    ->values()
                    ->all();
                $sessions = OneToOneSessionService::bookMonthlySeriesWithInstructor(
                    $student,
                    $instructor,
                    $weekly,
                    (int) ($data['weeks'] ?? 4),
                    $entitlement,
                    $request->user(),
                    $notes,
                    null,
                    false
                );
                $first = $sessions->first();

                return redirect()
                    ->route('admin.one-to-one-sessions.index', ['student_id' => $student->id])
                    ->with('success', 'تم تثبيت '.$sessions->count().' حصص شهرياً مع المعلم'.($first?->series_id ? ' (سلسلة '.$first->series_id.')' : '').'.');
            }

            if ($style === 'multi') {
                $ats = $data['scheduled_ats'] ?? [];
                if (count($ats) < 1 && ! empty($data['manual_scheduled_at'])) {
                    $ats = [$data['manual_scheduled_at']];
                }
                if (count($ats) < 1) {
                    return back()->withInput()->with('error', 'اختر موعداً واحداً على الأقل.');
                }
                $clockTz = AppTimezone::resolveInput(
                    is_string($data['timezone'] ?? null) ? $data['timezone'] : null,
                    $instructor
                );
                $parsedAts = collect($ats)->map(fn ($at) => AppTimezone::parseAppointmentInput((string) $at, $clockTz) ?? $at)->all();
                $sessions = OneToOneSessionService::bookMultipleWithInstructor(
                    $student,
                    $instructor,
                    $parsedAts,
                    $entitlement,
                    $request->user(),
                    $notes,
                    false
                );
                $first = $sessions->first();

                return redirect()
                    ->route('admin.one-to-one-sessions.show', $first)
                    ->with('success', 'تم حجز '.$sessions->count().' حصص مع نفس المعلم.');
            }

            $rawAt = $data['scheduled_at'] ?? $data['manual_scheduled_at'] ?? null;
            if (empty($rawAt)) {
                return back()->withInput()->with('error', 'اكتب الموعد بتوقيت المعلم أو اختر من الجدول إن وُجد.');
            }

            $clockTz = AppTimezone::resolveInput(
                is_string($data['timezone'] ?? null) ? $data['timezone'] : null,
                $instructor
            );
            $sessions = OneToOneSessionService::bookMultipleWithInstructor(
                $student,
                $instructor,
                [AppTimezone::parseAppointmentInput((string) $rawAt, $clockTz) ?? $rawAt],
                $entitlement,
                $request->user(),
                $notes,
                false
            );
            $session = $sessions->first();
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.one-to-one-sessions.show', $session)
            ->with('success', 'تم تسكين الطالب مع المعلم في موعد متاح وإنشاء غرفة Live.');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function storeGroup(Request $request, array $data, StudentServiceEntitlement $entitlement): RedirectResponse
    {
        $groupId = (int) ($data['tutoring_group_id'] ?? 0);
        if ($groupId < 1) {
            return back()->withInput()->with('error', 'اختر مجموعة.');
        }

        try {
            $booking = DB::transaction(function () use ($data, $entitlement, $groupId) {
                $entitlement = StudentServiceEntitlement::query()
                    ->lockForUpdate()
                    ->findOrFail($entitlement->id);
                $group = TutoringGroup::query()->active()->findOrFail($groupId);

                if (! $entitlement->isActive() || StudentEntitlementService::bookableUnitsLeft($entitlement) < 1) {
                    throw new \InvalidArgumentException('الطالب ليس لديه باقة/رصيد قابل للحجز.');
                }

                $requiredScope = StudentEntitlementService::scopeForTutoringGroup($group);
                if (! in_array($entitlement->scope, [$requiredScope, ServicePackage::SCOPE_GLOBAL], true)) {
                    throw new \InvalidArgumentException('نطاق الرصيد لا يسمح بالحجز في هذه المجموعة.');
                }
                if ($entitlement->tutoring_group_id && (int) $entitlement->tutoring_group_id !== (int) $group->id) {
                    throw new \InvalidArgumentException('هذا الرصيد مخصص لمجموعة أخرى.');
                }

                $instructorId = (int) ($data['instructor_id'] ?? $group->instructor_id);
                $instructor = User::query()->findOrFail($instructorId);
                if (! $instructor->isInstructor()) {
                    throw new \InvalidArgumentException('المستخدم المحدد ليس معلماً.');
                }

                $startsAt = AppTimezone::parseAppointmentInput((string) $data['scheduled_at'])
                    ?? Carbon::parse($data['scheduled_at'])->utc();
                $endsAt = $startsAt->copy()->addMinutes(max(30, (int) ($group->duration_minutes ?: 60)));

                $allowed = TutoringGroupAvailabilityService::availableSlots(
                    $group,
                    $startsAt->copy()->subMinute(),
                    $startsAt->copy()->addDay()
                )->contains(function ($slot) use ($startsAt) {
                    $slotStart = $slot['starts_at'] instanceof Carbon
                        ? $slot['starts_at']->copy()->utc()
                        : Carbon::parse($slot['starts_at'])->utc();

                    return $slotStart->equalTo($startsAt->copy()->utc());
                });

                if (! $allowed) {
                    throw new \InvalidArgumentException('الموعد غير متاح في جدول المعلم لهذه المجموعة.');
                }

                $conflict = TutoringGroupBooking::query()
                    ->where('instructor_id', $instructor->id)
                    ->whereIn('status', [TutoringGroupBooking::STATUS_PENDING, TutoringGroupBooking::STATUS_CONFIRMED])
                    ->where('starts_at', '<', $endsAt)
                    ->where('ends_at', '>', $startsAt)
                    ->exists();

                if ($conflict) {
                    throw new \InvalidArgumentException('المعلم لديه حجز متداخل في هذا الموعد.');
                }

                $booking = TutoringGroupBooking::create([
                    'tutoring_group_id' => $group->id,
                    'student_service_entitlement_id' => $entitlement->id,
                    'order_id' => $entitlement->order_id,
                    'payment_status' => TutoringGroupBooking::PAYMENT_PAID,
                    'instructor_id' => $instructor->id,
                    'user_id' => $entitlement->user_id,
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'status' => TutoringGroupBooking::STATUS_PENDING,
                    'admin_notes' => trim(($data['notes'] ?? '')."\nتسكين من لوحة الطلاب والخدمات"),
                ]);

                return TutoringGroupOrchestrationService::confirmBooking($booking);
            });
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.tutoring-group-bookings.show', $booking)
            ->with('success', 'تم تسكين الطالب في المجموعة وإنشاء غرفة Live.');
    }

    public function destroyPrivate(OneToOneSession $oneToOneSession): RedirectResponse
    {
        try {
            $count = OneToOneSessionService::cancelSession(
                $oneToOneSession,
                filled($oneToOneSession->series_id),
                'حذف التسكين من لوحة الإدارة'
            );
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        if ($count < 1) {
            return back()->with('error', 'لا يوجد تسكين قائم للحذف.');
        }

        return redirect()
            ->route('admin.placement.index')
            ->with('success', $count > 1
                ? 'تم حذف التسكين وإلغاء '.$count.' حصص. الرصيد المحجوز عاد للطالب.'
                : 'تم حذف التسكين وإرجاع الرصيد المحجوز.');
    }

    public function destroyGroup(TutoringGroupBooking $tutoringGroupBooking): RedirectResponse
    {
        if (! $tutoringGroupBooking->isOpenPlacement()) {
            return back()->with('error', 'لا يمكن حذف تسكين مكتمل أو ملغى.');
        }

        TutoringGroupOrchestrationService::cancelBooking(
            $tutoringGroupBooking,
            'حذف التسكين من لوحة الإدارة'
        );

        return redirect()
            ->route('admin.placement.index')
            ->with('success', 'تم حذف تسكين المجموعة وإرجاع الرصيد المحجوز.');
    }
}
