<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware Multi-Role Authorization (kebutuhan non-fungsional Keamanan).
 * Dipakai di routes: ->middleware('role:admin') atau ->middleware('role:siswa').
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! in_array($user->role, $roles, true)) {
            abort(403, 'Halaman ini tidak tersedia untuk akun Anda.');
        }

        return $next($request);
    }
}
