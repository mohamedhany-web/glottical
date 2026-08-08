<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\InstructorProfile;
use App\Services\PublicMediaStorage;
use Illuminate\Http\Request;

class PersonalBrandingController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        if (!$user->isInstructor()) {
            abort(403);
        }
        $profile = InstructorProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['status' => InstructorProfile::STATUS_DRAFT]
        );
        return view('instructor.personal-branding.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        if (!$user->isInstructor()) {
            abort(403);
        }
        $profile = InstructorProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['status' => InstructorProfile::STATUS_DRAFT]
        );

        $data = $request->validate([
            'headline' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:5000',
            'experience' => 'nullable|string|max:50000',
            'skills' => 'nullable|string|max:5000',
            'consultation_price_egp' => 'nullable|numeric|min:0|max:999999.99',
            'consultation_duration_minutes' => 'nullable|integer|min:15|max:480',
            'photo' => 'nullable|image|max:'.config('upload_limits.max_upload_kb'),
            'gender' => 'nullable|in:male,female',
            'private_subjects' => 'nullable|array',
            'private_subjects.*' => 'string|max:64',
            'private_age_groups' => 'nullable|array',
            'private_age_groups.*' => 'string|max:64',
            'private_languages' => 'nullable|array',
            'private_languages.*' => 'string|max:64',
            'private_specializations' => 'nullable|array',
            'private_specializations.*' => 'string|max:64',
            'intro_video_url' => 'nullable|url|max:500',
        ], [
            'experience.max' => 'الخبرات في المجال يجب ألا تتجاوز 50 ألف حرف. إن احتجت مساحة أكبر تواصل مع الإدارة.',
            'skills.max' => 'المهارات يجب ألا تتجاوز 5 آلاف حرف.',
            'photo.image' => 'الملف الذي تم رفعه يجب أن يكون صورة',
            'photo.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = PublicMediaStorage::store(
                $request->file('photo'),
                'instructor-profiles',
                $profile->photo_path
            );
        }

        unset($data['photo']);
        $data['social_links'] = [];

        // normalize numeric fields (empty string -> null)
        foreach (['consultation_price_egp', 'consultation_duration_minutes'] as $k) {
            if (!array_key_exists($k, $data)) {
                continue;
            }
            if ($data[$k] === '' || $data[$k] === null) {
                $data[$k] = null;
            }
        }

        $allowedSubjects = array_keys(config('private_lessons.subjects', []));
        $allowedAges = array_keys(config('private_lessons.age_groups', []));
        $allowedLangs = array_keys(config('private_lessons.languages', []));
        $allowedSpecs = array_keys(config('private_lessons.specializations', []));

        $user->forceFill([
            'gender' => $data['gender'] ?? $user->gender,
            'portfolio_intro_video_url' => $data['intro_video_url'] ?? null,
            'private_teaching_meta' => [
                'subjects' => array_values(array_intersect($data['private_subjects'] ?? [], $allowedSubjects)),
                'age_groups' => array_values(array_intersect($data['private_age_groups'] ?? [], $allowedAges)),
                'languages' => array_values(array_intersect($data['private_languages'] ?? [], $allowedLangs)),
                'specializations' => array_values(array_intersect($data['private_specializations'] ?? [], $allowedSpecs)),
            ],
        ])->save();

        unset(
            $data['gender'],
            $data['intro_video_url'],
            $data['private_subjects'],
            $data['private_age_groups'],
            $data['private_languages'],
            $data['private_specializations']
        );

        $profile->update($data);

        return back()->with('success', 'تم حفظ الملف التعريفي.');
    }

    public function submit()
    {
        $user = auth()->user();
        if (!$user->isInstructor()) {
            abort(403);
        }
        $profile = InstructorProfile::where('user_id', $user->id)->firstOrFail();
        if ($profile->status !== InstructorProfile::STATUS_DRAFT && $profile->status !== InstructorProfile::STATUS_REJECTED) {
            return back()->with('error', 'الملف مقدم مسبقاً أو معتمد.');
        }

        // حد أدنى للجودة قبل الإرسال للمراجعة (تسويق شخصي للطلاب)
        if (!$profile->headline || !$profile->bio || count($profile->skills_list) < 3) {
            return back()->with('error', 'أكمل الملف قبل الإرسال: عنوان تعريفي + نبذة + 3 مهارات على الأقل.');
        }

        $profile->update([
            'status' => InstructorProfile::STATUS_PENDING_REVIEW,
            'submitted_at' => now(),
            'rejection_reason' => null,
        ]);
        return back()->with('success', 'تم إرسال الملف التعريفي للمراجعة. سيتم إعلامك بعد مراجعته من الإدارة.');
    }
}
