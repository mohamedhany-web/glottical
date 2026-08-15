<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * يمنع المعلم المتقدّم عبر التوظيف من لوحات التحكم حتى تفعيل الإدارة.
 */
class EnsureInstructorPanelAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || (! $user->isInstructor() && ! $user->isTeacher())) {
            return $next($request);
        }

        if ($user->canAccessInstructorPanel()) {
            return $next($request);
        }

        $message = app()->getLocale() === 'ar'
            ? 'لوحة المعلم تُفتح بعد تفعيل حسابك من الإدارة. أكمل ملفك التعريفي أو انتظر المراجعة.'
            : 'The instructor dashboard opens after admin activation. Complete your profile or wait for review.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 403);
        }

        return redirect()
            ->route('public.tutor.apply.profile')
            ->with('error', $message);
    }
}
