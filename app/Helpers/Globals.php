<?php

if(!function_exists('employeeHasPermission'))
{
     function employeeHasPermission($permission):bool
    {
        return auth()->user()?->employee && auth()->user()?->employee->hasPermissionTo($permission);
    }
}