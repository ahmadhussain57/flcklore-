<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSectionAccess
{
    public function handle(Request $request, Closure $next, string $section): Response
    {
        if (! in_array($section, ['content', 'marketing'], true)) {
            abort(500, 'قسم غير معروف.');
        }

        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // منع بات: لا دخول إن نقص دور المحتوى أو دور التسويق
        if (! $user->hasBothSectionRoles()) {
            abort(403, 'حسابك غير مكتمل. يجب أن تملك دوراً في قسم المحتوى وقسم التسويق معاً.');
        }

        return $next($request);
    }
}