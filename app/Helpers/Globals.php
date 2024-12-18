<?php

if(!function_exists('employeeHasPermission'))
{
     function employeeHasPermission($permission):bool
    { 
        return  (auth()->user()->hasRole('super_admin')) || (auth()->user()?->employee && auth()->user()?->employee->hasPermissionTo($permission));
    }
}