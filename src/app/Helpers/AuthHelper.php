<?php


namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class AuthHelper
{

    public static function hasPermission($permission)
    {
        return Auth::user()->hasPermission($permission);
    }

    public static function hasPermissionAny($permissions)
    {
        return Auth::user()->hasPermissionAny($permissions);
    }

    public static function hasPermissionElseAbort($permission)
    {
        if (!self::hasPermission($permission)) {
            abort(403, 'Forbidden');
        }
    }

    public static function hasPermissionAnyElseAbort($permissions)
    {
        if (!self::hasPermissionAny($permissions)) {
            abort(403, 'Forbidden');
        }
    }

    public static function isStudentElseAbort()
    {
        if (!Auth::user()->isStudent()) {
            abort(403, 'Forbidden');
        }
    }

    public static function ifStudentThenAbort()
    {
        if (Auth::user()->isStudent()) {
            abort(403, 'Forbidden');
        }
    }
}
