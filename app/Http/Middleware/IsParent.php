<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Symfony\Component\HttpFoundation\Response;

class IsParent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(auth()->check()  && !auth()->user()->parent)
        {
           
            if(auth()->user()->student) {
                Filament::auth()->logout();
                return redirect()->route('filament.student.auth.login');
            }
            Filament::auth()->logout();
            return redirect()->route('filament.admin.auth.login');
        }
        return $next($request);
    }
}
