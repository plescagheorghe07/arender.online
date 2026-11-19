<?php

namespace App\Http\Middleware;

use App\Models\StaffRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('admin.login');
        }

        $role = $user->role instanceof StaffRole ? $user->role->value : '';

        if (! in_array($role, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
