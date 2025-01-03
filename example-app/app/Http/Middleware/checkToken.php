<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class checkToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // if(empty($request->age) || $request->age < 18){
        //     // return redirect('/createuser');
        //     return redirect('login');
        // }

        if(Auth::check() && Auth::user()->id){
            // return redirect('/createuser');
            return redirect('login');
        }
        return $next($request);
    }
}
