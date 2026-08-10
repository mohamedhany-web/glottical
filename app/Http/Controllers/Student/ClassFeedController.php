<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClassFeedPost;
use App\Models\TutoringGroupCohort;
use App\Services\ClassFeedService;
use App\Services\TutoringClassService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClassFeedController extends Controller
{
    public function store(Request $request, TutoringGroupCohort $cohort): RedirectResponse
    {
        abort_unless(TutoringClassService::userCanAccessCohort($request->user(), $cohort), 403);

        $data = $request->validate([
            'body' => 'required|string|max:1000',
            'post_type' => 'nullable|in:question,announcement',
            'is_pinned' => 'nullable|boolean',
        ]);

        ClassFeedService::createPost(
            $cohort,
            $request->user(),
            $data['body'],
            $data['post_type'] ?? 'question',
            (bool) ($data['is_pinned'] ?? false),
        );

        return back()->with('success', 'تم نشر مشاركتك في مجتمع الفصل.');
    }

    public function comment(Request $request, ClassFeedPost $post): RedirectResponse
    {
        $post->loadMissing('cohort');
        abort_unless($post->cohort && TutoringClassService::userCanAccessCohort($request->user(), $post->cohort), 403);

        $data = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        ClassFeedService::addComment($post, $request->user(), $data['body']);

        return back()->with('success', 'تم إضافة التعليق.');
    }

    public function hide(Request $request, ClassFeedPost $post): RedirectResponse
    {
        ClassFeedService::hidePost($post, $request->user());

        return back()->with('success', 'تم إخفاء المنشور.');
    }

    public function unhide(Request $request, ClassFeedPost $post): RedirectResponse
    {
        ClassFeedService::unhidePost($post, $request->user());

        return back()->with('success', 'تم إظهار المنشور.');
    }

    public function pin(Request $request, ClassFeedPost $post): RedirectResponse
    {
        ClassFeedService::togglePin($post, $request->user());

        return back()->with('success', 'تم تحديث التثبيت.');
    }
}
