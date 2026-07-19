<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active || ! $user->isAdmin()) {
            if ($request->expectsJson()) {
                abort(403, 'Доступ запрещён.');
            }

            return redirect()
                ->route('admin.login')
                ->with('error', 'Войдите в админ-панель для продолжения.');
        }

        return $next($request);
    }
}
