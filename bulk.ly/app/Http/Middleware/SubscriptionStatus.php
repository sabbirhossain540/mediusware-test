<?php

namespace Bulkly\Http\Middleware;

use Closure;

class SubscriptionStatus
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
        //

        if ($request->user() && empty($request->user()->subscriptions[0]) && ! $request->user()->onGenericTrial()) {
            // This user is not a paying customer...
            return redirect('/subscriptions');
        } 



        return $next($request);
    }
}
