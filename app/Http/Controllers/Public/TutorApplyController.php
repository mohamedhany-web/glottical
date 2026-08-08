<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\InstructorProfile;
use App\Models\TutorApplication;
use App\Models\User;
use App\Services\TutorApplicationStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class TutorApplyController extends Controller
{
    /**
     * الخطوة ١: إنشاء حساب معلم (إيميل + كلمة مرور).
     */
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()?->isInstructor()) {
            return redirect()->route('public.tutor.apply.profile');
        }

        return view('tutor.apply-register');
    }

    public function register(Request $request): RedirectResponse
    {
        if ($request->user()?->isInstructor()) {
            return redirect()->route('public.tutor.apply.profile');
        }

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:40', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'max:72', 'confirmed'],
        ], [
            'full_name.required' => 'الاسم الكامل مطلوب.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.unique' => 'هذا البريد مسجّل مسبقاً. سجّل الدخول ثم أكمل بياناتك.',
            'phone.required' => 'رقم الجوال مطلوب.',
            'phone.unique' => 'رقم الجوال مسجّل مسبقاً.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.confirmed' => 'تأكيد كلمة المرور غير مطابق.',
            'password.min' => 'كلمة المرور يجب ألا تقل عن 8 أحرف.',
        ]);

        $email = strtolower(trim($data['email']));
        $phone = preg_replace('/\s+/', '', trim($data['phone'])) ?: trim($data['phone']);

        $user = DB::transaction(function () use ($data, $email, $phone) {
            $user = User::create([
                'name' => trim($data['full_name']),
                'email' => $email,
                'phone' => $phone,
                'password' => Hash::make($data['password']),
                'role' => 'instructor',
                'is_active' => true,
            ]);

            TutorApplication::create([
                'user_id' => $user->id,
                'full_name' => trim($data['full_name']),
                'email' => $email,
                'phone' => $phone,
                'status' => TutorApplication::STATUS_DRAFT,
            ]);

            InstructorProfile::firstOrCreate(
                ['user_id' => $user->id],
                ['status' => InstructorProfile::STATUS_DRAFT]
            );

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('public.tutor.apply.profile')
            ->with('success', app()->getLocale() === 'ar'
                ? 'تم إنشاء حسابك بنجاح. أكمل بياناتك الشخصية والمستندات الآن.'
                : 'Your account was created. Please complete your personal details and documents.');
    }

    /**
     * الخطوة ٢: إكمال البيانات الشخصية والمستندات (بعد تسجيل الدخول).
     */
    public function profile(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        if (! $user || ! $user->isInstructor()) {
            return redirect()->route('public.tutor.apply');
        }

        $application = TutorApplication::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->first();

        if (! $application) {
            $application = TutorApplication::create([
                'user_id' => $user->id,
                'full_name' => $user->name,
                'email' => $user->email,
                'status' => TutorApplication::STATUS_DRAFT,
            ]);
        }

        if ($application->status === TutorApplication::STATUS_PENDING) {
            return view('tutor.apply-submitted', compact('application'));
        }

        if (in_array($application->status, [
            TutorApplication::STATUS_APPROVED,
            TutorApplication::STATUS_ACTIVATED,
        ], true)) {
            return redirect()
                ->route('instructor.personal-branding.edit')
                ->with('success', 'طلبك معتمد. يمكنك تحديث ملفك التعريفي من هنا.');
        }

        return view('tutor.apply-profile', [
            'application' => $application,
            'user' => $user,
        ]);
    }

    public function storeProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && $user->isInstructor(), 403);

        $application = TutorApplication::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->firstOrFail();

        if (! in_array($application->status, [
            TutorApplication::STATUS_DRAFT,
            TutorApplication::STATUS_REJECTED,
        ], true)) {
            return redirect()
                ->route('public.tutor.apply.profile')
                ->with('error', 'تم إرسال طلبك مسبقاً وهو قيد المراجعة أو مفعّل.');
        }

        $maxKb = (int) config('upload_limits.max_upload_kb', 40960);
        $videoMaxKb = min(max($maxKb, 40960), 102400);

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:160'],
            'phone' => ['required', 'string', 'max:40', 'unique:users,phone,'.$user->id],
            'nationality' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'gender' => ['nullable', 'in:male,female'],
            'headline' => ['required', 'string', 'max:255'],
            'bio' => ['required', 'string', 'max:5000'],
            'experience' => ['required', 'string', 'max:20000'],
            'education' => ['nullable', 'string', 'max:255'],
            'years_experience' => ['nullable', 'integer', 'min:0', 'max:60'],
            'photo' => [$application->photo_path ? 'nullable' : 'required', 'image', 'max:'.$maxKb],
            'id_document' => [$application->id_document_path ? 'nullable' : 'required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:'.$maxKb],
            'certificate' => [$application->certificate_path ? 'nullable' : 'required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:'.$maxKb],
            'intro_video' => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/quicktime', 'max:'.$videoMaxKb],
            'intro_video_url' => ['nullable', 'url', 'max:500'],
        ], [
            'photo.required' => 'الصورة الشخصية مطلوبة.',
            'id_document.required' => 'صورة البطاقة أو جواز السفر مطلوبة.',
            'certificate.required' => 'صورة الشهادة أو الإجازة مطلوبة.',
            'headline.required' => 'العنوان المختصر للملف مطلوب.',
            'bio.required' => 'النبذة التعريفية مطلوبة.',
            'experience.required' => 'الخبرات مطلوبة.',
            'phone.required' => 'رقم الجوال مطلوب.',
        ]);

        $hasVideo = $request->hasFile('intro_video')
            || filled($request->input('intro_video_url'))
            || filled($application->intro_video_path)
            || filled($application->intro_video_url);

        if (! $hasVideo) {
            return back()
                ->withInput()
                ->withErrors(['intro_video' => 'أرفق فيديو تعريفي أو ضع رابط الفيديو.']);
        }

        try {
            if ($request->hasFile('photo')) {
                $data['photo_path'] = TutorApplicationStorage::storePhoto(
                    $request->file('photo'),
                    $application->photo_path
                );
            }
            if ($request->hasFile('id_document')) {
                $data['id_document_path'] = TutorApplicationStorage::storeIdDocument(
                    $request->file('id_document'),
                    $application->id_document_path
                );
            }
            if ($request->hasFile('certificate')) {
                $data['certificate_path'] = TutorApplicationStorage::storeCertificate(
                    $request->file('certificate'),
                    $application->certificate_path
                );
            }
            if ($request->hasFile('intro_video')) {
                $data['intro_video_path'] = TutorApplicationStorage::storeVideo(
                    $request->file('intro_video'),
                    $application->intro_video_path
                );
            }
        } catch (\Throwable $e) {
            Log::error('tutor_application_profile_upload_failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'disk' => TutorApplicationStorage::resolvedDisk(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'photo' => app()->getLocale() === 'ar'
                        ? 'تعذّر رفع الملفات إلى التخزين السحابي. حاول مرة أخرى.'
                        : 'Cloud upload failed. Please try again.',
                ]);
        }

        unset($data['photo'], $data['id_document'], $data['certificate'], $data['intro_video']);
        $data['email'] = $user->email;
        $data['user_id'] = $user->id;
        $data['status'] = TutorApplication::STATUS_PENDING;
        $data['intro_video_url'] = filled($data['intro_video_url'] ?? null)
            ? trim((string) $data['intro_video_url'])
            : ($application->intro_video_url ?: null);
        $data['admin_notes'] = null;

        $application->update($data);
        $application->refresh();

        $introVideoUrl = filled($application->intro_video_url)
            ? $application->intro_video_url
            : TutorApplicationStorage::publicUrl($application->intro_video_path);

        $user->forceFill([
            'name' => $data['full_name'],
            'phone' => $data['phone'],
            'gender' => $data['gender'] ?? $user->gender,
            'bio' => $data['bio'],
            'portfolio_intro_video_url' => $introVideoUrl,
            'profile_image' => $application->photo_path ?: $user->profile_image,
        ])->save();

        InstructorProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'headline' => $data['headline'],
                'bio' => $data['bio'],
                'experience' => $data['experience'],
                'photo_path' => $application->photo_path,
                'status' => InstructorProfile::STATUS_PENDING_REVIEW,
                'submitted_at' => now(),
                'rejection_reason' => null,
            ]
        );

        return redirect()
            ->route('public.tutor.apply.profile')
            ->with('success', app()->getLocale() === 'ar'
                ? 'تم إرسال بياناتك للمراجعة. حسابك جاهز للدخول، وسيظهر ملفك للعامة بعد موافقة الإدارة.'
                : 'Your details were submitted for review. You can log in; your public profile appears after admin approval.');
    }
}
