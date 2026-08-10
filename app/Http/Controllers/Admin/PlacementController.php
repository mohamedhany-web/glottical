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
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();
        $recentGroups = TutoringGroupBooking::query()
            ->with(['user:id,name', 'instructor:id,name', 'tutoringGroup:id,title'])
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
            ->get(['id', 'name', 'email']);

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

            $duration = OneToOneSession::defaultDurationMinutes();
            $slots = OneToOneAvailabilityService::availableSlots(
                $instructorId,
                now()->addHour(),
                now()->addWeeks(3),
                $duration
            )->take(48)->map(fn ($slot) => [
                'starts_at' => $slot['starts_at'] instanceof Carbon
                    ? $slot['starts_at']->format('Y-m-d H:i:s')
                    : (string) $slot['starts_at'],
                'label' => $slot['label'] ?? null,
            ])->values();

            // Build labels if service didn't include them
            $slots = $slots->map(function (array $slot) {
                if (! empty($slot['label'])) {
                    return $slot;
                }
                $at = Carbon::parse($slot['starts_at']);

                return [
                    'starts_at' => $at->format('Y-m-d H:i:s'),
                    'label' => $at->locale('ar')->translatedFormat('D j M · H:i'),
                ];
            });

            return response()->json([
                'ok' => true,
                'mode' => 'private',
                'duration_minutes' => $duration,
                'slots' => $slots,
                'empty_hint' => $slots->isEmpty()
                    ? 'لا مواعيد متاحة لهذا المعلم خلال الأسابيع القادمة. تأكد من ضبط جدول توافره 1:1.'
                    : null,
            ]);
        }

        $groupId = (int) ($data['tutoring_group_id'] ?? 0);
        if ($groupId < 1) {
            return response()->json(['ok' => false, 'message' => 'اختر مجموعة أولاً.'], 422);
        }

        $group = TutoringGroup::query()->active()->findOrFail($groupId);
        $slots = TutoringGroupAvailabilityService::availableSlots(
            $group,
            now()->addHour(),
            now()->addDays(21)
        )->take(48)->map(fn ($slot) => [
            'starts_at' => $slot['starts_at'] instanceof Carbon
                ? $slot['starts_at']->format('Y-m-d H:i:s')
                : (string) ($slot['starts_at'] ?? ''),
            'label' => $slot['label'] ?? Carbon::parse($slot['starts_at'])->locale('ar')->translatedFormat('D j M · H:i'),
        ])->values();

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
            'student_id' => ['required', 'integer', 'exists:users,id'],
            'student_service_entitlement_id' => ['required', 'integer', 'exists:student_service_entitlements,id'],
            'instructor_id' => ['nullable', 'integer', 'exists:users,id'],
            'tutoring_group_id' => ['nullable', 'integer', 'exists:tutoring_groups,id'],
            'scheduled_at' => ['required', 'date', 'after:now'],
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

        $startsAt = Carbon::parse($data['scheduled_at']);
        $duration = OneToOneSession::defaultDurationMinutes();

        if (! OneToOneAvailabilityService::isSlotAvailable($instructorId, $startsAt, $duration)) {
            return back()->withInput()->with('error', 'الموعد غير متاح في جدول المعلم أو يتعارض مع حجز آخر.');
        }

        try {
            $session = OneToOneSession::create([
                'student_service_entitlement_id' => $entitlement->id,
                'instructor_id' => $instructor->id,
                'student_id' => $entitlement->user_id,
                'session_number' => (int) OneToOneSession::query()->where('student_id', $entitlement->user_id)->max('session_number') + 1,
                'duration_minutes' => $duration,
                'status' => OneToOneSession::STATUS_PENDING,
                'booked_by_user_id' => $request->user()->id,
                'notes' => trim(($data['notes'] ?? '')."\nتسكين من لوحة الطلاب والخدمات"),
            ]);

            OneToOneSessionService::scheduleSession(
                $session,
                $startsAt,
                $duration,
                $request->user(),
                true
            );
        } catch (\InvalidArgumentException $e) {
            if (isset($session) && $session->exists) {
                $session->delete();
            }

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

                $startsAt = Carbon::parse($data['scheduled_at']);
                $endsAt = $startsAt->copy()->addMinutes(max(30, (int) ($group->duration_minutes ?: 60)));

                $allowed = TutoringGroupAvailabilityService::availableSlots(
                    $group,
                    $startsAt->copy()->subMinute(),
                    $startsAt->copy()->addDay()
                )->contains(function ($slot) use ($startsAt) {
                    $slotStart = $slot['starts_at'] instanceof Carbon
                        ? $slot['starts_at']
                        : Carbon::parse($slot['starts_at']);

                    return $slotStart->format('Y-m-d H:i') === $startsAt->format('Y-m-d H:i');
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
}
