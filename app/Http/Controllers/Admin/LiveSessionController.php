<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveSession;
use App\Models\LiveServer;
use App\Models\LiveSetting;
use App\Models\AdvancedCourse;
use App\Models\User;
use App\Support\AppTimezone;
use Illuminate\Http\Request;

class LiveSessionController extends Controller
{
    public function index(Request $request)
    {
        $query = LiveSession::with(['course', 'instructor', 'server'])
            ->withCount('attendance');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        if ($request->filled('instructor_id')) {
            $query->where('instructor_id', $request->instructor_id);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('room_name', 'like', "%{$request->search}%");
            });
        }

        $sessions = $query->latest('scheduled_at')->paginate(20)->withQueryString();

        $stats = [
            'total'     => LiveSession::count(),
            'live'      => LiveSession::where('status', 'live')->count(),
            'scheduled' => LiveSession::where('status', 'scheduled')->count(),
            'ended'     => LiveSession::where('status', 'ended')->count(),
        ];

        $courses = AdvancedCourse::select('id', 'title')->orderBy('title')->get();
        // المعلم = المدرب: إما مدرب داخلي أو طالب مشترك لدينا (يشترون منا الخدمة)
        $instructors = User::canHostLiveSession()
            ->select('id', 'name', 'email', 'role')
            ->orderBy('name')
            ->get();

        return view('admin.live-sessions.index', compact('sessions', 'stats', 'courses', 'instructors'));
    }

    public function create()
    {
        $courses = AdvancedCourse::select('id', 'title')->orderBy('title')->get();
        $instructors = User::canHostLiveSession()
            ->select('id', 'name', 'email', 'role')
            ->orderBy('name')
            ->get();
        $servers = LiveServer::where('status', 'active')->get();

        return view('admin.live-sessions.create', compact('courses', 'instructors', 'servers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'course_id'        => 'nullable|exists:advanced_courses,id',
            'instructor_id'    => 'required|exists:users,id',
            'server_id'        => 'nullable|exists:live_servers,id',
            'scheduled_at'     => 'required|date',
            'timezone'         => AppTimezone::inputRules(),
            'max_participants' => 'nullable|integer|min:2|max:1000',
            'is_recorded'      => 'boolean',
            'allow_chat'       => 'boolean',
            'allow_screen_share' => 'boolean',
            'require_enrollment' => 'boolean',
            'mute_on_join'     => 'boolean',
            'video_off_on_join' => 'boolean',
            'password'         => 'nullable|string|max:50',
        ]);
        $validated = AppTimezone::shiftRequestDateTime($request, $validated, 'scheduled_at', mustBeFuture: true);
        unset($validated['timezone']);

        $validated['is_recorded'] = $request->boolean('is_recorded');
        $validated['allow_chat'] = $request->boolean('allow_chat', true);
        $validated['allow_screen_share'] = $request->boolean('allow_screen_share', true);
        $validated['require_enrollment'] = $request->boolean('require_enrollment', true);
        $validated['mute_on_join'] = $request->boolean('mute_on_join', true);
        $validated['video_off_on_join'] = $request->boolean('video_off_on_join', true);
        $validated['status'] = 'scheduled';

        $session = LiveSession::create($validated);

        return redirect()->route('admin.live-sessions.show', $session)
            ->with('success', 'تم إنشاء جلسة البث بنجاح');
    }

    public function show(LiveSession $liveSession)
    {
        $liveSession->load(['course', 'instructor', 'server', 'recordings']);
        $attendees = $liveSession->attendance()
            ->with('user')
            ->orderByDesc('joined_at')
            ->get();

        return view('admin.live-sessions.show', compact('liveSession', 'attendees'));
    }

    public function edit(LiveSession $liveSession)
    {
        $courses = AdvancedCourse::select('id', 'title')->orderBy('title')->get();
        $instructors = User::canHostLiveSession()
            ->select('id', 'name', 'email', 'role')
            ->orderBy('name')
            ->get();
        $servers = LiveServer::where('status', 'active')->get();

        return view('admin.live-sessions.edit', compact('liveSession', 'courses', 'instructors', 'servers'));
    }

    public function update(Request $request, LiveSession $liveSession)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'course_id'        => 'nullable|exists:advanced_courses,id',
            'instructor_id'    => 'required|exists:users,id',
            'server_id'        => 'nullable|exists:live_servers,id',
            'scheduled_at'     => 'required|date',
            'timezone'         => AppTimezone::inputRules(),
            'max_participants' => 'nullable|integer|min:2|max:1000',
            'is_recorded'      => 'boolean',
            'allow_chat'       => 'boolean',
            'allow_screen_share' => 'boolean',
            'require_enrollment' => 'boolean',
            'mute_on_join'     => 'boolean',
            'video_off_on_join' => 'boolean',
            'password'         => 'nullable|string|max:50',
        ]);
        $validated = AppTimezone::shiftRequestDateTime($request, $validated, 'scheduled_at', mustBeFuture: false);
        unset($validated['timezone']);

        $validated['is_recorded'] = $request->boolean('is_recorded');
        $validated['allow_chat'] = $request->boolean('allow_chat', true);
        $validated['allow_screen_share'] = $request->boolean('allow_screen_share', true);
        $validated['require_enrollment'] = $request->boolean('require_enrollment', true);
        $validated['mute_on_join'] = $request->boolean('mute_on_join', true);
        $validated['video_off_on_join'] = $request->boolean('video_off_on_join', true);

        $liveSession->update($validated);

        return redirect()->route('admin.live-sessions.show', $liveSession)
            ->with('success', 'تم تحديث جلسة البث بنجاح');
    }

    public function destroy(LiveSession $liveSession)
    {
        if ($liveSession->isLive()) {
            return back()->with('error', 'لا يمكن حذف جلسة بث مباشر قيد التشغيل');
        }
        $liveSession->delete();
        return redirect()->route('admin.live-sessions.index')
            ->with('success', 'تم حذف الجلسة بنجاح');
    }

    public function forceEnd(LiveSession $liveSession)
    {
        $liveSession->end();
        return back()->with('success', 'تم إنهاء الجلسة بنجاح');
    }

    public function cancel(LiveSession $liveSession)
    {
        $liveSession->cancel();
        return back()->with('success', 'تم إلغاء الجلسة');
    }
}
