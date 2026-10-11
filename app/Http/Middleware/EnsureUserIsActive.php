<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     * Memastikan pengguna yang sedang login memiliki status_delete = 0 (Aktif).
     * Jika akun telah dinonaktifkan/dihapus, sesi langsung di-logout.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            $isDeleted = $user->status_delete === true ||
                $user->status_delete === 1 ||
                $user->status_delete === '1';

            if ($isDeleted) {
                Auth::guard('web')->logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Akun Anda sudah dinonaktifkan. Silakan hubungi Administrator.',
                ]);
            }
        }

        return $next($request);
    }
}
