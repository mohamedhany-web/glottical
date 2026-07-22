<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Services\ContactMessageAlertService;
use App\Services\PublicFooterSettings;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $footer = PublicFooterSettings::payload();
        $supportEmail = trim((string) ($footer['email'] ?? ''));
        $supportPhone = trim((string) ($footer['phone'] ?? ''));

        return view('public.contact', compact('supportEmail', 'supportPhone'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $message = ContactMessage::create($validated);

        try {
            app(ContactMessageAlertService::class)->notifyAdmins($message);
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('public.contact')
            ->with('success', __('public.contact_success'));
    }
}
