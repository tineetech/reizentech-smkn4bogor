<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class K4GuestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next): Response
    {
        $session = $request->session()->get(config('app.session_name'));

        // Jika sudah login, arahkan ke halaman dashboard atau halaman lainnya
        if ($session) {
            return redirect()->route('dashboard');  // Ubah sesuai dengan route yang sesuai
        }

        return $next($request);
    }
}
