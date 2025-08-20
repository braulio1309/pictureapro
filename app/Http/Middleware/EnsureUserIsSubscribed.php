<?php
namespace App\Http\Middleware;

use Closure;

class EnsureUserIsSubscribed
{
    public function handle($request, Closure $next)
    {
        if (! $request->user() || ! $request->user()->subscribed('default')) {
            return redirect()->route('subscribes')
                ->with('warning', 'Necesitas una suscripción activa.');
        }

        return $next($request);
    }
}
