<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AttendanceLog
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            Auth::user()
                ->attendances()
                ->forToDay()
                ->firstOrCreate();
        }

        return $next($request);
    }
}