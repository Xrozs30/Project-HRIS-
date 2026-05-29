<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckFaceEnrollment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->employee_role === 'employee' && empty(Auth::user()->employee_face_descriptor)) {
            // Allow only the face enrollment routes and logout
            $allowed = ['profile.face', 'profile.face.save', 'logout'];
            if (!$request->routeIs(...$allowed)) {
                return redirect()->route('profile.face')->with('warning', 'Anda wajib mendaftarkan wajah sebelum mengakses sistem.');
            }
        }

        return $next($request);
    }
}
