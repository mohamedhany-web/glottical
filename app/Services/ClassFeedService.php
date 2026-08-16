<?php

namespace App\Services;

use App\Models\ClassFeedComment;
use App\Models\ClassFeedPost;
use App\Models\TutoringGroupCohort;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class ClassFeedService
{
    public static function tablesReady(): bool
    {
        return Schema::hasTable('class_feed_posts') && Schema::hasTable('class_feed_comments');
    }

    /**
     * @return Collection<int, ClassFeedPost>
     */
    public static function postsFor(TutoringGroupCohort $cohort, User $viewer, int $limit = 30): Collection
    {
        if (! self::tablesReady()) {
            return collect();
        }

        $canModerate = self::canModerate($viewer, $cohort);

        return ClassFeedPost::query()
            ->with([
                'author:id,name,role',
                'visibleComments.author:id,name,role',
            ])
            ->where('tutoring_group_cohort_id', $cohort->id)
            ->when(! $canModerate, fn ($q) => $q->where('is_hidden', false))
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public static function createPost(
        TutoringGroupCohort $cohort,
        User $user,
        string $body,
        string $type = 'question',
        bool $pin = false,
    ): ClassFeedPost {
        $body = trim($body);
        $max = (int) config('school_game.feed.max_body', 1000);
        if ($body === '' || mb_strlen($body) > $max) {
            throw ValidationException::withMessages(['body' => 'نص المنشور غير صالح.']);
        }

        $isInstructor = self::canModerate($user, $cohort);
        if ($type === 'announcement' && ! $isInstructor && ! config('school_game.feed.student_can_announce')) {
            $type = 'question';
        }

        if (! self::tablesReady()) {
            throw new \InvalidArgumentException('مجتمع الفصل غير جاهز بعد. تحتاج الإدارة تشغيل جداول المنشورات.');
        }

        $post = ClassFeedPost::create([
            'tutoring_group_cohort_id' => $cohort->id,
            'user_id' => $user->id,
            'post_type' => $type === 'announcement' ? 'announcement' : 'question',
            'body' => $body,
            'is_pinned' => $isInstructor && $pin,
        ]);

        if (StudentSchoolGameService::tablesReady()) {
            StudentSchoolGameService::award(
                $user,
                (int) config('school_game.xp.feed_post', 15),
                'feed_post',
                ClassFeedPost::class,
                $post->id,
                (int) $cohort->id
            );
        }

        return $post;
    }

    public static function addComment(ClassFeedPost $post, User $user, string $body): ClassFeedComment
    {
        if (! self::tablesReady()) {
            throw new \InvalidArgumentException('مجتمع الفصل غير جاهز بعد.');
        }

        $body = trim($body);
        $max = (int) config('school_game.feed.max_body', 1000);
        if ($body === '' || mb_strlen($body) > $max) {
            throw ValidationException::withMessages(['body' => 'نص التعليق غير صالح.']);
        }

        if ($post->is_hidden) {
            throw ValidationException::withMessages(['body' => 'هذا المنشور مخفي.']);
        }

        $comment = ClassFeedComment::create([
            'class_feed_post_id' => $post->id,
            'user_id' => $user->id,
            'body' => $body,
        ]);

        if (StudentSchoolGameService::tablesReady()) {
            StudentSchoolGameService::award(
                $user,
                (int) config('school_game.xp.feed_comment', 5),
                'feed_comment',
                ClassFeedComment::class,
                $comment->id,
                (int) $post->tutoring_group_cohort_id
            );
        }

        return $comment;
    }

    public static function hidePost(ClassFeedPost $post, User $moderator): void
    {
        $post->loadMissing('cohort.tutoringGroup');
        abort_unless(self::canModerate($moderator, $post->cohort), 403);

        $post->update([
            'is_hidden' => true,
            'hidden_by' => $moderator->id,
            'hidden_at' => now(),
        ]);
    }

    public static function unhidePost(ClassFeedPost $post, User $moderator): void
    {
        $post->loadMissing('cohort.tutoringGroup');
        abort_unless(self::canModerate($moderator, $post->cohort), 403);

        $post->update([
            'is_hidden' => false,
            'hidden_by' => null,
            'hidden_at' => null,
        ]);
    }

    public static function togglePin(ClassFeedPost $post, User $moderator): void
    {
        $post->loadMissing('cohort.tutoringGroup');
        abort_unless(self::canModerate($moderator, $post->cohort), 403);
        $post->update(['is_pinned' => ! $post->is_pinned]);
    }

    public static function canModerate(User $user, ?TutoringGroupCohort $cohort): bool
    {
        if (! $cohort) {
            return false;
        }
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return true;
        }

        return (int) $cohort->tutoringGroup?->instructor_id === (int) $user->id;
    }
}
