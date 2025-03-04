<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Filament\Resources\EmployeeResource;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployeeViewRoles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(auth()->check()  && 
        (!auth()->user()?->hasRole('super_admin')))
        {
            if((count(request()->segments()) > 2) && request()->segments()[2] =='roles' && !auth()->user()?->hasRole('view_any_shield::role'))
            {
                return redirect()->route('filament.admin.pages.dashboard');

            }
        }
        return $next($request);
    }
}
