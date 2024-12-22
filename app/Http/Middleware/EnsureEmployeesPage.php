<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Filament\Resources\EmployeeResource;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployeesPage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(auth()->check()  && (!auth()->user()?->hasRole('super_admin')) && 
        (count(request()->segments()) == 2 && request()->segments()[1] =='employees'))
        {
            if(!employeeHasPermission('view_any_employee')) return redirect(EmployeeResource::getUrl('view',[auth()->user()?->employee?->id]));
        }
        return $next($request);
    }
}
