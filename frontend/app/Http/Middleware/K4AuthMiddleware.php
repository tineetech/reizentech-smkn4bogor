<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class K4AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ambil session
        $session = $request->session()->get(config('app.session_name'));
        // Jika tidak ada kirim ke halaman login
        if (!$session) {
            return redirect()->route('auth.login')->withErrors(['errors' => 'Session tidak valid, harap login kembali.']);
        }

        return $next($request);
    }
}
