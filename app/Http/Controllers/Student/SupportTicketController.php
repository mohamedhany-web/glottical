<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SupportInquiryCategory;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Services\SupportTicketAlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SupportTicketController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $tickets = SupportTicket::query()
            ->where('user_id', $user->id)
            ->with('inquiryCategory')
            ->latest()
            ->paginate(12);

        $inquiryCategories = SupportInquiryCategory::query()->active()->ordered()->get();

        return view('student.support.index', compact('tickets', 'inquiryCategories'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'support_inquiry_category_id' => [
                'required',
                'integer',
                Rule::exists('support_inquiry_categories', 'id')->where(fn ($q) => $q->where('is_active', true)),
            ],
            'subject' => ['required', 'string', 'max:180'],
            'priority' => ['required', 'in:low,normal,high,urgent'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'support_inquiry_category_id' => $data['support_inquiry_category_id'],
            'subject' => $data['subject'],
            'priority' => $data['priority'],
            'status' => 'open',
            'message' => $data['message'],
            'last_reply_at' => now(),
        ]);

        SupportTicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'sender_type' => 'student',
            'message' => $data['message'],
        ]);

        try {
            app(SupportTicketAlertService::class)->notifyNewTicket($ticket);
        } catch (\Throwable $e) {
            report($e);
            Log::error('Support ticket in-app/email alert failed', ['ticket_id' => $ticket->id]);
        }

        return redirect()->route('student.support.show', $ticket)->with('success', 'تم إنشاء تذكرة الدعم بنجاح.');
    }

    public function show(SupportTicket $ticket)
    {
        $user = auth()->user();
        abort_unless((int) $ticket->user_id === (int) $user->id, 403);

        $ticket->load(['replies.user', 'inquiryCategory']);

        return view('student.support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $user = auth()->user();
        abort_unless((int) $ticket->user_id === (int) $user->id, 403);
        abort_if(in_array($ticket->status, ['resolved', 'closed'], true), 422, 'هذه التذكرة مغلقة ولا يمكن الرد عليها.');

        $data = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:5000'],
        ]);

        SupportTicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'sender_type' => 'student',
            'message' => $data['message'],
        ]);

        $ticket->update([
            'status' => 'open',
            'last_reply_at' => now(),
        ]);

        return back()->with('success', 'تم إرسال ردك لفريق الدعم.');
    }
}
