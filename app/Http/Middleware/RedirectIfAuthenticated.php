<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    protected static $redirectToCallback;

    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect($this->redirectTo($request, $guard));
            }
        }

        return $next($request);
    }

    protected function redirectTo(Request $request, string $guard): ?string
    {
        switch ($guard) {
            case 'admin':
                return route('admin.dashboard');
            case 'intern':
                return route('intern.dashboard'); // optional, if user role exists
            default:
                return $this->defaultRedirectUri();
        }
    }

    protected function defaultRedirectUri(): string
    {
        foreach (['dashboard', 'home'] as $uri) {
            if (Route::has($uri)) {
                return route($uri);
            }
        }

        return '/';
    }

    public static function redirectUsing(callable $redirectToCallback)
    {
        static::$redirectToCallback = $redirectToCallback;
    }
}
