<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassroomMeeting;
use App\Models\ConsultationRequest;
use App\Models\ConsultationSetting;
use App\Models\AgreementPayment;
use App\Models\InstructorAgreement;
use App\Models\Notification;
use App\Models\WalletTransaction;
use App\Support\AppTimezone;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        if (! Schema::hasTable('consultation_requests')) {
            Log::warning('Consultations index requested but table is missing.', [
                'url' => $request->fullUrl(),
                'user_id' => auth()->id(),
            ]);

            $requests = new LengthAwarePaginator(
                collect(),
                0,
                25,
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            $settings = (object) [
                'default_price' => 0,
                'default_duration_minutes' => 60,
                'payment_instructions' => null,
                'is_active' => false,
            ];

            $stats = [
                'pending' => 0,
                'payment_reported' => 0,
                'awaiting_verification' => 0,
                'paid' => 0,
                'scheduled' => 0,
            ];

            $status = (string) $request->get('status', 'all');
            return view('admin.consultations.index', compact('requests', 'settings', 'stats', 'status'))
                ->with('error', 'ميزة الاستشارات غير مفعلة حالياً لأن جدول البيانات غير موجود. يرجى تشغيل الترحيلات (migrations).');
        }

        $status = (string) $request->get('status', 'all');
        $query = ConsultationRequest::query()
            ->with(['instructor', 'student', 'classroomMeeting'])
            ->latest();

        if (in_array($status, array_keys(ConsultationRequest::statusLabels()), true)) {
            $query->where('status', $status);
        }

        $requests = $query->paginate(25)->withQueryString();
        $settings = ConsultationSetting::current();

        $stats = [
            'pending' => ConsultationRequest::where('status', ConsultationRequest::STATUS_PENDING)->count(),
            'payment_reported' => ConsultationRequest::where('status', ConsultationRequest::STATUS_PAYMENT_REPORTED)->count(),
            'awaiting_verification' => ConsultationRequest::where('status', ConsultationRequest::STATUS_AWAITING_VERIFICATION)->count(),
            'paid' => ConsultationRequest::where('status', ConsultationRequest::STATUS_PAID)->count(),
            'scheduled' => ConsultationRequest::where('status', ConsultationRequest::STATUS_SCHEDULED)->count(),
        ];

        return view('admin.consultations.index', compact('requests', 'settings', 'stats', 'status'));
    }

    public function updateSettings(Request $request)
    {
        $settings = ConsultationSetting::current();
        $data = $request->validate([
            'default_price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'default_duration_minutes' => ['required', 'integer', 'min:15', 'max:480'],
            'payment_instructions' => ['nullable', 'string', 'max:20000'],
        ]);

        $settings->update([
            'default_price' => $data['default_price'],
            'default_duration_minutes' => $data['default_duration_minutes'],
            'payment_instructions' => $data['payment_instructions'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'تم حفظ إعدادات الاستشارات.');
    }

    public function show(ConsultationRequest $consultation)
    {
        $consultation->load(['instructor', 'student', 'classroomMeeting', 'paidConfirmedBy', 'platformWallet', 'walletTransaction']);

        return view('admin.consultations.show', compact('consultation'));
    }

    public function confirmPayment(ConsultationRequest $consultation)
    {
        if (! in_array($consultation->status, [
            ConsultationRequest::STATUS_PENDING,
            ConsultationRequest::STATUS_PAYMENT_REPORTED,
            ConsultationRequest::STATUS_AWAITING_VERIFICATION,
        ], true)) {
            return back()->with('error', 'لا يمكن تأكيد الدفع لهذه الحالة.');
        }

        $viaWallet = $consultation->paidViaWallet();
        $viaPlatform = $consultation->paidViaPlatformAccounts();
        $msg = $viaWallet
            ? 'تم قبول طلب الاستشارة والدفع عبر محفظة الرصيد مع المدرب ' . ($consultation->instructor->name ?? '') . '. سيتم جدولة الموعد من لوحة الإدارة.'
            : ($viaPlatform
                ? 'تم تأكيد استلام مبلغ الاستشارة (تحويل على حسابات المنصة) مع المدرب ' . ($consultation->instructor->name ?? '') . '. سيتم جدولة الموعد قريباً.'
                : 'تم تأكيد استلام مبلغ الاستشارة مع المدرب ' . ($consultation->instructor->name ?? '') . '. سيتم جدولة الموعد قريباً.');

        $consultation->update([
            'status' => ConsultationRequest::STATUS_PAID,
            'paid_confirmed_at' => now(),
            'paid_confirmed_by' => auth()->id(),
        ]);

        Notification::create([
            'user_id' => $consultation->student_id,
            'sender_id' => auth()->id(),
            'title' => $viaWallet ? 'تم قبول طلب الاستشارة' : 'تم تأكيد دفع الاستشارة',
            'message' => $msg,
            'type' => 'general',
            'priority' => 'normal',
            'audience' => 'student',
            'action_url' => route('consultations.show', $consultation),
            'action_text' => 'تفاصيل الطلب',
        ]);

        return back()->with('success', 'تم تأكيد الدفع.');
    }

    public function schedule(Request $request, ConsultationRequest $consultation)
    {
        if ($consultation->status !== ConsultationRequest::STATUS_PAID) {
            return back()->with('error', 'يجب تأكيد الدفع قبل الجدولة.');
        }

        $data = $request->validate([
            'scheduled_at' => ['required', 'date'],
            'timezone' => AppTimezone::inputRules(),
            'duration_minutes' => ['nullable', 'integer', 'min:15', 'max:480'],
        ]);
        $data = AppTimezone::shiftRequestDateTime(
            $request,
            $data,
            'scheduled_at',
            mustBeFuture: true,
            fallbackUser: $consultation->instructor
        );

        $scheduledAt = $data['scheduled_at'];
        $duration = (int) ($data['duration_minutes'] ?? $consultation->duration_minutes);

        $meeting = ClassroomMeeting::create([
            'user_id' => $consultation->instructor_id,
            'consultation_request_id' => $consultation->id,
            'code' => ClassroomMeeting::generateCode(),
            'room_name' => 'consultation-' . $consultation->id . '-' . Str::lower(Str::random(6)),
            'title' => 'استشارة: ' . ($consultation->student->name ?? 'طالب'),
            'scheduled_for' => $scheduledAt,
            'planned_duration_minutes' => $duration,
            'max_participants' => 12,
            'settings' => [
                'allow_guest_join' => false,
                'consultation' => true,
            ],
        ]);

        $consultation->update([
            'status' => ConsultationRequest::STATUS_SCHEDULED,
            'scheduled_at' => $scheduledAt,
            'duration_minutes' => $duration,
            'classroom_meeting_id' => $meeting->id,
        ]);

        $joinUrl = \App\Services\ClassroomMeetingAccessService::platformEnterUrl($meeting);

        Notification::create([
            'user_id' => $consultation->student_id,
            'sender_id' => auth()->id(),
            'title' => 'تم جدولة الاستشارة',
            'message' => 'موعد الاستشارة: ' . $scheduledAt->format('Y-m-d H:i') . ' — رابط الدخول: ' . $joinUrl,
            'type' => 'reminder',
            'priority' => 'high',
            'audience' => 'student',
            'action_url' => route('consultations.show', $consultation),
            'action_text' => 'تفاصيل الاستشارة',
        ]);

        Notification::create([
            'user_id' => $consultation->instructor_id,
            'sender_id' => auth()->id(),
            'title' => 'استشارة مجدولة مع طالب',
            'message' => 'الطالب: ' . ($consultation->student->name ?? '') . ' — الموعد: ' . $scheduledAt->format('Y-m-d H:i'),
            'type' => 'reminder',
            'priority' => 'normal',
            'audience' => 'instructor',
            'action_url' => route('instructor.consultations.show', $consultation),
            'action_text' => 'تفاصيل الاستشارة',
        ]);

        return back()->with('success', 'تم إنشاء الغرفة وإشعار الطالب والمدرب.');
    }

    public function updateNotes(Request $request, ConsultationRequest $consultation)
    {
        $data = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:20000'],
        ]);
        $consultation->update(['admin_notes' => $data['admin_notes'] ?? null]);

        return back()->with('success', 'تم حفظ الملاحظات.');
    }

    public function cancel(ConsultationRequest $consultation)
    {
        if (in_array($consultation->status, [ConsultationRequest::STATUS_COMPLETED, ConsultationRequest::STATUS_CANCELLED], true)) {
            return back()->with('error', 'لا يمكن إلغاء هذا الطلب.');
        }

        $refundedToWallet = false;

        DB::transaction(function () use ($consultation, &$refundedToWallet) {
            if ($consultation->status === ConsultationRequest::STATUS_AWAITING_VERIFICATION
                && $consultation->wallet_transaction_id) {
                $tx = WalletTransaction::with('wallet')->find($consultation->wallet_transaction_id);
                if ($tx && $tx->wallet && (int) $tx->wallet->user_id === (int) $consultation->student_id) {
                    $tx->wallet->deposit(
                        (float) $consultation->price_amount,
                        null,
                        null,
                        'استرجاع — إلغاء طلب استشارة #'.$consultation->id
                    );
                    $refundedToWallet = true;
                }
            }

            $consultation->update(['status' => ConsultationRequest::STATUS_CANCELLED]);
        });

        Notification::create([
            'user_id' => $consultation->student_id,
            'sender_id' => auth()->id(),
            'title' => 'تم إلغاء طلب الاستشارة',
            'message' => 'تم إلغاء طلب الاستشارة من قبل الإدارة.' . ($refundedToWallet ? ' وعُيد المبلغ إلى محفظتك.' : ''),
            'type' => 'general',
            'priority' => 'normal',
            'audience' => 'student',
        ]);

        return back()->with('success', 'تم إلغاء الطلب.' . ($refundedToWallet ? ' وتم استرجاع المبلغ لمحفظة الطالب.' : ''));
    }

    public function markCompleted(ConsultationRequest $consultation)
    {
        if ($consultation->status !== ConsultationRequest::STATUS_SCHEDULED) {
            return back()->with('error', 'يمكن الإكمال للطلبات المجدولة فقط.');
        }

        DB::transaction(function () use ($consultation) {
            $consultation->update(['status' => ConsultationRequest::STATUS_COMPLETED]);

            $agreement = InstructorAgreement::query()
                ->where('instructor_id', $consultation->instructor_id)
                ->where('status', InstructorAgreement::STATUS_ACTIVE)
                ->where(function ($q) use ($consultation) {
                    $q->whereNull('start_date')
                        ->orWhereDate('start_date', '<=', $consultation->scheduled_at ?? now());
                })
                ->where(function ($q) use ($consultation) {
                    $q->whereNull('end_date')
                        ->orWhereDate('end_date', '>=', $consultation->scheduled_at ?? now());
                })
                ->where(function ($q) {
                    $q->where('type', 'consultation_session')
                        ->orWhere('billing_type', InstructorAgreement::BILLING_CONSULTATION);
                })
                ->latest('start_date')
                ->latest('id')
                ->first();

            if (! $agreement || (float) $agreement->rate <= 0) {
                return;
            }

            $existing = AgreementPayment::query()
                ->where('agreement_id', $agreement->id)
                ->where('instructor_id', $consultation->instructor_id)
                ->where('type', AgreementPayment::TYPE_CONSULTATION_SESSION)
                ->where('description', 'like', '%#'.$consultation->id.'%')
                ->exists();

            if ($existing) {
                return;
            }

            AgreementPayment::create([
                'agreement_id' => $agreement->id,
                'instructor_id' => $consultation->instructor_id,
                'type' => AgreementPayment::TYPE_CONSULTATION_SESSION,
                'amount' => (float) $agreement->rate,
                'status' => AgreementPayment::STATUS_APPROVED,
                'description' => 'مستحق استشارة مكتملة #'.$consultation->id.' — الطالب: '.($consultation->student->name ?? '—'),
                'payment_date' => now(),
                'created_by' => auth()->id(),
            ]);
        });

        return back()->with('success', 'تم تسجيل الاستشارة كمكتملة.');
    }
}
