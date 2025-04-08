<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;

class BetMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->amount < $request->user()->credits) {
            throw ValidationException::withMessages([
                'credits' => ['Insuficient credits.'],
            ]);
        }

        if(!in_array($request->side, ['M','W'])) {
            throw ValidationException::withMessages([
                'side' => ['Invalid bet selection.'],
            ]);
        }

        return $next($request);
    }
}
