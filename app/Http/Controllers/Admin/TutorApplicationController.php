<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\StorageFileController;
use App\Models\TutorApplication;
use App\Models\User;
use App\Services\TutorApplicationActivationService;
use App\Services\TutorApplicationStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

class TutorApplicationController extends Controller
{
    public function hub(): View
    {
        $stats = [
            'draft' => TutorApplication::draft()->count(),
            'pending' => TutorApplication::pending()->count(),
            'approved' => TutorApplication::awaitingActivation()->count(),
            'activated' => TutorApplication::activated()->count(),
            'rejected' => TutorApplication::where('status', TutorApplication::STATUS_REJECTED)->count(),
            'total' => TutorApplication::count(),
            'instructors' => User::query()->whereIn('role', ['instructor', 'teacher'])->where('is_active', true)->count(),
        ];

        $recentPending = TutorApplication::pending()->orderByDesc('id')->limit(6)->get();
        $awaitingActivation = TutorApplication::awaitingActivation()->orderByDesc('id')->limit(6)->get();
        $applyUrl = route('public.tutor.apply');

        return view('admin.tutor-applications.hub', compact(
            'stats',
            'recentPending',
            'awaitingActivation',
            'applyUrl'
        ));
    }

    public function index(Request $request): View
    {
        $query = TutorApplication::query()->with(['user:id,name,email', 'reviewedByUser:id,name'])->orderByDesc('id');

        if ($request->filled('status') && in_array($request->status, [
            TutorApplication::STATUS_DRAFT,
            TutorApplication::STATUS_PENDING,
            TutorApplication::STATUS_APPROVED,
            TutorApplication::STATUS_ACTIVATED,
            TutorApplication::STATUS_REJECTED,
        ], true)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $query->where(function ($q) use ($s) {
                $q->where('full_name', 'like', '%'.$s.'%')
                    ->orWhere('email', 'like', '%'.$s.'%')
                    ->orWhere('phone', 'like', '%'.$s.'%');
            });
        }

        $applications = $query->paginate(20)->withQueryString();
        $stats = [
            'draft' => TutorApplication::draft()->count(),
            'pending' => TutorApplication::pending()->count(),
            'approved' => TutorApplication::awaitingActivation()->count(),
            'activated' => TutorApplication::activated()->count(),
            'rejected' => TutorApplication::where('status', TutorApplication::STATUS_REJECTED)->count(),
            'total' => TutorApplication::count(),
        ];
        $applyUrl = route('public.tutor.apply');

        return view('admin.tutor-applications.index', compact('applications', 'stats', 'applyUrl'));
    }

    public function activated(Request $request): View
    {
        $query = TutorApplication::query()
            ->activated()
            ->with(['user:id,name,email,phone,is_active,role,created_at', 'activatedByUser:id,name'])
            ->orderByDesc('activated_at')
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $query->where(function ($q) use ($s) {
                $q->where('full_name', 'like', '%'.$s.'%')
                    ->orWhere('email', 'like', '%'.$s.'%')
                    ->orWhereHas('user', function ($uq) use ($s) {
                        $uq->where('name', 'like', '%'.$s.'%')
                            ->orWhere('email', 'like', '%'.$s.'%');
                    });
            });
        }

        $applications = $query->paginate(20)->withQueryString();

        return view('admin.tutor-applications.activated', compact('applications'));
    }

    public function show(TutorApplication $tutorApplication): View
    {
        $tutorApplication->load([
            'reviewedByUser:id,name',
            'activatedByUser:id,name',
            'user:id,name,email,phone,is_active,role,created_at',
        ]);

        $applyUrl = route('public.tutor.apply');
        $photoInline = TutorApplicationStorage::inlineDataUri($tutorApplication->photo_path);
        $idInline = $tutorApplication->idDocumentIsPdf()
            ? null
            : TutorApplicationStorage::inlineDataUri($tutorApplication->id_document_path);
        $certificateInline = $tutorApplication->certificateIsPdf()
            ? null
            : TutorApplicationStorage::inlineDataUri($tutorApplication->certificate_path);

        return view('admin.tutor-applications.show', [
            'application' => $tutorApplication,
            'applyUrl' => $applyUrl,
            'photoInline' => $photoInline,
            'idInline' => $idInline,
            'certificateInline' => $certificateInline,
        ]);
    }

    public function file(TutorApplication $tutorApplication, string $kind): Response
    {
        $path = match ($kind) {
            'photo' => $tutorApplication->photo_path,
            'id' => $tutorApplication->id_document_path,
            'certificate' => $tutorApplication->certificate_path,
            'video' => $tutorApplication->intro_video_path,
            default => abort(404),
        };

        $relative = TutorApplicationStorage::storedRelativePath($path);
        if ($relative === null) {
            abort(404);
        }

        return app(StorageFileController::class)->show(request(), $relative);
    }

    public function approve(TutorApplication $tutorApplication): RedirectResponse
    {
        if ($tutorApplication->isActivated()) {
            return back()->with('error', 'الحساب مفعّل مسبقاً.');
        }

        if ($tutorApplication->status === TutorApplication::STATUS_REJECTED) {
            return back()->with('error', 'الطلب مرفوض — أعده للمراجعة أولاً إن لزم.');
        }

        $tutorApplication->update([
            'status' => TutorApplication::STATUS_APPROVED,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم قبول الطلب. يمكنك الآن تفعيل حساب المعلم.');
    }

    public function activate(Request $request, TutorApplication $tutorApplication): RedirectResponse
    {
        try {
            $result = TutorApplicationActivationService::activate(
                $tutorApplication,
                $request->user()
            );
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.tutor-applications.show', $tutorApplication)
            ->with('success', 'تم تفعيل الملف العام للمعلم. الحساب كان مُنشأ عند التسجيل: '.$result['user']->email)
            ->with('activated_email', $result['user']->email)
            ->with('activated_user_id', $result['user']->id);
    }

    public function reject(Request $request, TutorApplication $tutorApplication): RedirectResponse
    {
        if ($tutorApplication->isActivated()) {
            return back()->with('error', 'لا يمكن رفض طلب بعد تفعيل الحساب.');
        }

        $data = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $tutorApplication->update([
            'status' => TutorApplication::STATUS_REJECTED,
            'admin_notes' => $data['admin_notes'] ?? $tutorApplication->admin_notes,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم رفض الطلب.');
    }

    public function destroy(TutorApplication $tutorApplication): RedirectResponse
    {
        if ($tutorApplication->isActivated()) {
            return back()->with('error', 'لا تحذف طلباً مرتبطاً بحساب مفعّل. عطّل الحساب من إدارة المستخدمين إن لزم.');
        }

        TutorApplicationStorage::delete($tutorApplication->photo_path);
        TutorApplicationStorage::delete($tutorApplication->id_document_path);
        TutorApplicationStorage::delete($tutorApplication->certificate_path);
        TutorApplicationStorage::delete($tutorApplication->intro_video_path);

        $tutorApplication->delete();

        return redirect()
            ->route('admin.tutor-applications.index')
            ->with('success', 'تم حذف الطلب.');
    }
}
