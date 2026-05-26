<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsTeacherOrAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || (! auth()->user()->isTeacher() && ! auth()->user()->isAdmin())) {
            return redirect('/')->with('error', 'Acceso exclusivo para docentes.');
        }

        return $next($request);
    }
}
