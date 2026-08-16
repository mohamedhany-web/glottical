<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * عرض ملفات مناهج X: طالب، أو معلم معتمد شغّال مع الأكاديمية.
 */
class EnsureCurriculumLibraryViewer
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->guest(route('login'));
        }

        if ($user->isStudent()) {
            return $next($request);
        }

        if ($user->isAcademyWorkingInstructor()) {
            return $next($request);
        }

        abort(403, 'هذا المحتوى غير متاح لحسابك.');
    }
}
