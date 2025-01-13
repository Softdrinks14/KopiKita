<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class AbleCreateOrder
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     */

    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $user = Auth::user();
        
        if ($user->role_id != 1 && $user->role_id != 2) {
            return response('YOU CANNOT ACCESS THIS FITUR', 403);
        }

        return $next($request);
    }
}