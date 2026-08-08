<?php

namespace App\Services;

use App\Models\InstructorProfile;
use App\Models\Notification;
use App\Models\TutorApplication;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TutorApplicationActivationService
{
    /**
     * تفعيل الملف العام للمعلم بعد قبول الطلب.
     * الحساب يُنشأ عند التسجيل؛ هنا نعتمد الملف التعريفي ونفعّل الظهور للعامة.
     *
     * @return array{user: User, profile: InstructorProfile, password: ?string}
     */
    public static function activate(TutorApplication $application, User $admin, ?string $password = null): array
    {
        if ($application->status === TutorApplication::STATUS_ACTIVATED && $application->user_id) {
            throw new InvalidArgumentException('تم تفعيل هذا الطلب مسبقاً.');
        }

        if ($application->status === TutorApplication::STATUS_REJECTED) {
            throw new InvalidArgumentException('لا يمكن تفعيل طلب مرفوض.');
        }

        if ($application->status === TutorApplication::STATUS_DRAFT) {
            throw new InvalidArgumentException('المتقدم لم يُكمل بياناته بعد.');
        }

        if ($application->status === TutorApplication::STATUS_PENDING) {
            throw new InvalidArgumentException('راجع الطلب واقبله أولاً ثم فعّل الملف العام.');
        }

        $user = $application->user_id
            ? User::query()->find($application->user_id)
            : null;

        if (! $user) {
            throw new InvalidArgumentException('لا يوجد حساب مرتبط بهذا الطلب. يجب أن يسجّل المتقدم أولاً بالإيميل وكلمة المرور.');
        }

        return DB::transaction(function () use ($application, $admin, $user) {
            $introVideoUrl = filled($application->intro_video_url)
                ? trim((string) $application->intro_video_url)
                : TutorApplicationStorage::publicUrl($application->intro_video_path);

            $user->forceFill([
                'name' => trim((string) $application->full_name) ?: $user->name,
                'phone' => self::normalizePhone($application->phone) ?: $user->phone,
                'role' => 'instructor',
                'is_active' => true,
                'gender' => in_array($application->gender, ['male', 'female'], true) ? $application->gender : $user->gender,
                'bio' => $application->bio ?: $user->bio,
                'portfolio_intro_video_url' => $introVideoUrl ?: $user->portfolio_intro_video_url,
                'profile_image' => $application->photo_path ?: $user->profile_image,
            ])->save();

            $profile = InstructorProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'headline' => $application->headline,
                    'bio' => $application->bio,
                    'experience' => $application->experience,
                    'skills' => self::skillsFromExperience($application),
                    'photo_path' => $application->photo_path,
                    'status' => InstructorProfile::STATUS_APPROVED,
                    'submitted_at' => $application->created_at ?? now(),
                    'reviewed_at' => now(),
                    'reviewed_by' => $admin->id,
                    'rejection_reason' => null,
                    'social_links' => [],
                ]
            );

            $application->update([
                'status' => TutorApplication::STATUS_ACTIVATED,
                'user_id' => $user->id,
                'activated_at' => now(),
                'activated_by' => $admin->id,
                'reviewed_at' => $application->reviewed_at ?? now(),
                'reviewed_by' => $application->reviewed_by ?? $admin->id,
            ]);

            Notification::create([
                'user_id' => $user->id,
                'sender_id' => $admin->id,
                'title' => 'تم اعتماد ملفك كمعلّم',
                'message' => 'مرحباً '.$user->name.' — تم قبول طلبك وتفعيل ملفك للعامة. أكمل جدول التوافر من لوحتك.',
                'type' => 'general',
                'priority' => 'high',
                'audience' => 'instructor',
                'action_url' => route('instructor.personal-branding.edit'),
                'action_text' => 'الملف التعريفي',
            ]);

            return [
                'user' => $user->fresh(),
                'profile' => $profile->fresh(),
                'password' => null,
            ];
        });
    }

    private static function normalizePhone(?string $phone): ?string
    {
        if (! is_string($phone) || trim($phone) === '') {
            return null;
        }

        $digits = preg_replace('/[^\d+]/', '', trim($phone));

        return $digits !== '' ? $digits : null;
    }

    private static function skillsFromExperience(TutorApplication $application): ?string
    {
        $parts = array_filter([
            $application->education,
            $application->headline,
            $application->years_experience !== null ? ($application->years_experience.' سنوات خبرة') : null,
            $application->nationality,
            $application->city,
        ]);

        return $parts === [] ? null : implode("\n", $parts);
    }
}
