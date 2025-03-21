<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class customUploadFile
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

        // if(empty($request->can) || $request->can != 1){
        //     return redirect('/login');
        //     // return redirect('/user');
        // }


        if (Auth::user()->roleid != 1) {
            return redirect('dashboard');
        }
        return $next($request);
    }
}
