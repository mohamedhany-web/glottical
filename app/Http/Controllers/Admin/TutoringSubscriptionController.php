<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentTutoringSubscription;
use App\Services\StudentEntitlementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TutoringSubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (! $user || (! $user->isAdmin() && ! $user->hasPermission('manage.packages') && ! $user->hasPermission('manage.tutoring-groups') && ! $user->hasPermission('manage.users'))) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $query = StudentTutoringSubscription::query()
            ->with([
                'user:id,name,email',
                'tutoringGroup:id,title',
                'package:id,name,sessions_count',
                'order:id,status,order_type',
                'entitlement:id,units_total,units_used,status,expires_at',
            ])
            ->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->user_id);
        }
        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $query->whereHas('user', function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $subscriptions = $query->paginate(25)->withQueryString();

        $stats = [
            'active' => StudentTutoringSubscription::query()->where('status', StudentTutoringSubscription::STATUS_ACTIVE)->count(),
            'sessions_left' => (int) StudentTutoringSubscription::query()
                ->where('status', StudentTutoringSubscription::STATUS_ACTIVE)
                ->selectRaw('COALESCE(SUM(GREATEST(sessions_total - sessions_used, 0)), 0) as left_sessions')
                ->value('left_sessions'),
        ];

        return view('admin.tutoring-subscriptions.index', compact('subscriptions', 'stats'));
    }

    public function show(StudentTutoringSubscription $tutoringSubscription): View
    {
        $tutoringSubscription->load([
            'user:id,name,email,phone',
            'tutoringGroup:id,title,slug,type',
            'package',
            'order',
            'entitlement',
            'bookings' => fn ($q) => $q->orderByDesc('starts_at')->limit(40),
        ]);

        return view('admin.tutoring-subscriptions.show', [
            'subscription' => $tutoringSubscription,
        ]);
    }

    public function sync(StudentTutoringSubscription $tutoringSubscription): RedirectResponse
    {
        $entitlement = $tutoringSubscription->entitlement;
        if (! $entitlement) {
            return back()->with('error', 'لا يوجد رصيد مرتبط بهذا الاشتراك.');
        }

        StudentEntitlementService::syncLegacySubscription($tutoringSubscription, $entitlement);

        return back()->with('success', 'تمت مزامنة الاشتراك من الرصيد المرتبط.');
    }
}
