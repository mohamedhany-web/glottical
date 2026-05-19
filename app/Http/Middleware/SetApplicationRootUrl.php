<?php

namespace App\Http\Middleware;

use App\Support\ApplicationUrl;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * يضبط جذر روابط Laravel من مسار index.php الفعلي (مثلاً /glottical/public).
 */
class SetApplicationRootUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        $root = ApplicationUrl::resolveRootUrl($request);
        if ($root !== '') {
            URL::forceRootUrl($root);
            config(['filesystems.disks.public.url' => $root.'/storage']);
        }

        return $next($request);
    }
}
