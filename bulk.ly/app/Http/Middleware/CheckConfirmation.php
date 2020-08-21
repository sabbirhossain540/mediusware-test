<?php

namespace Bulkly\Http\Middleware;

use Closure;

class CheckConfirmation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->user()->varifide == 1) {
           return redirect('users/confirmation');
        }
        return $next($request);
    }
}
