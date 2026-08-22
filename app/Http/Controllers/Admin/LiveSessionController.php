<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\LiveServer;
use App\Models\LiveSession;
use App\Models\SessionAttendance;
use App\Models\User;
use App\Services\LiveMeetingProvider;
use App\Support\AppTimezone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiveSessionController extends Controller
{
    /**
     * @return \Illuminate\Support\Collection<int, User>
     */
    private function liveHostOptions()
    {
        try {
            $hosts = User::canHostLiveSession()
                ->select('id', 'name', 'email', 'role')
                ->orderBy('name')
                ->get();
        } catch (\Throwable $e) {
            report($e);

            $hosts = User::query()
                ->whereIn('role', ['instructor', 'teacher'])
                ->select('id', 'name', 'email', 'role')
                ->orderBy('name')
                ->get();
        }

        $admin = Auth::user();
        if ($admin && ! $hosts->contains('id', $admin->id)) {
            $hosts = $hosts->prepend($admin)->values();
        }

        return $hosts;
    }

    private function preferredServerId(): ?int
    {
        $server = app(LiveMeetingProvider::class)->preferredLiveKitServer()
            ?: LiveServer::query()->where('status', 'active')->orderByDesc('id')->first();

        return $server?->id;
    }

    private function recordHostAttendance(LiveSession $liveSession): void
    {
        $exists = SessionAttendance::query()
            ->where('session_id', $liveSession->id)
            ->where('user_id', Auth::id())
            ->whereNull('left_at')
            ->exists();

        if ($exists) {
            return;
        }

        SessionAttendance::create([
            'session_id' => $liveSession->id,
            'user_id' => Auth::id(),
            'joined_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'role_in_session' => 'instructor',
        ]);
    }

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
        $instructors = $this->liveHostOptions();

        return view('admin.live-sessions.index', compact('sessions', 'stats', 'courses', 'instructors'));
    }

    public function create()
    {
        $courses = AdvancedCourse::select('id', 'title')->orderBy('title')->get();
        $instructors = $this->liveHostOptions();
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
        if (empty($validated['server_id'])) {
            $validated['server_id'] = $this->preferredServerId();
        }

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
        $instructors = $this->liveHostOptions();
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

    /**
     * إنشاء بث فوري للإدارة والدخول للغرفة مباشرة (مضيف).
     */
    public function instant(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
        ]);

        $title = trim((string) ($validated['title'] ?? ''));
        if ($title === '') {
            $title = 'بث إداري — '.now()->timezone(config('app.timezone'))->format('Y/m/d H:i');
        }

        $session = LiveSession::create([
            'title' => $title,
            'description' => 'جلسة مباشرة أنشأتها الإدارة للانضمام الفوري.',
            'instructor_id' => Auth::id(),
            'server_id' => $this->preferredServerId(),
            'scheduled_at' => now(),
            'status' => 'scheduled',
            'max_participants' => 200,
            'is_recorded' => false,
            'allow_chat' => true,
            'allow_screen_share' => true,
            'require_enrollment' => false,
            'mute_on_join' => false,
            'video_off_on_join' => false,
        ]);

        $session->start();
        $this->recordHostAttendance($session);

        return redirect()
            ->route('admin.live-sessions.room', $session)
            ->with('success', 'تم فتح البث المباشر — أنت المضيف الآن');
    }

    /**
     * بدء جلسة مجدولة والدخول كمضيف من لوحة الإدارة.
     */
    public function start(LiveSession $liveSession)
    {
        if ($liveSession->isLive()) {
            $this->recordHostAttendance($liveSession);

            return redirect()->route('admin.live-sessions.room', $liveSession);
        }

        if (! $liveSession->isScheduled()) {
            return back()->with('error', 'لا يمكن بدء هذه الجلسة — الحالة الحالية: '.$liveSession->status);
        }

        $liveSession->start();
        $this->recordHostAttendance($liveSession);

        return redirect()->route('admin.live-sessions.room', $liveSession);
    }

    public function room(LiveSession $liveSession)
    {
        if (! $liveSession->isLive()) {
            return redirect()
                ->route('admin.live-sessions.show', $liveSession)
                ->with('info', 'الجلسة ليست في وضع البث حالياً');
        }

        $user = Auth::user();
        $this->recordHostAttendance($liveSession);
        $meeting = app(LiveMeetingProvider::class)->roomPayload($liveSession, $user, true);

        return view('admin.live-sessions.room', array_merge([
            'liveSession' => $liveSession,
            'user' => $user,
        ], $meeting));
    }

    public function end(LiveSession $liveSession)
    {
        if ($liveSession->isLive()) {
            $liveSession->end();
        }

        return redirect()
            ->route('admin.live-sessions.show', $liveSession)
            ->with('success', 'تم إنهاء البث');
    }
}
