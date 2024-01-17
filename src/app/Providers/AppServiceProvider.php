<?php

namespace App\Providers;

use App\Helpers\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::defaultView('vendor.pagination.bootstrap-4');
        Paginator::defaultSimpleView('vendor.pagination.simple-bootstrap-4');
        //has Permission
        Blade::directive('hasPermission', function ($permission) {
            return "<?php if ( Auth::user()->hasPermission($permission) ) { ?>";
        });
        Blade::directive('endHasPermission', function () {
            return "<?php } ?>";
        });

        // has Role
        Blade::directive('hasRole', function ($role) {
            return "<?php if ( Auth::user()->hasRole($role) ) { ?>";
        });
        Blade::directive('endHasRole', function () {
            return "<?php } ?>";
        });

         //has Permission Any
         Blade::directive('hasPermissionAny', function ($permissions) {
            return "<?php if ( Auth::user()->hasPermissionAny($permissions) ) { ?>";
        });
        Blade::directive('endHasPermissionAny', function () {
            return "<?php } ?>";
        });

        // has Role Any
        Blade::directive('hasRoleAny', function ($roles) {
            return "<?php if ( Auth::user()->hasRoleAny($roles) ) { ?>";
        });
        Blade::directive('endHasRoleAny', function () {
            return "<?php } ?>";
        });

         // is Admin
         Blade::directive('isAdmin', function () {
            return "<?php if ( Auth::user()->isAdmin() ) { ?>";
        });
        Blade::directive('endIsAdmin', function () {
            return "<?php } ?>";
        });

         // is Teacher
         Blade::directive('isTeacher', function () {
            return "<?php if ( Auth::user()->isTeacher() ) { ?>";
        });
        Blade::directive('endIsTeacher', function () {
            return "<?php } ?>";
        });

         // is Student
         Blade::directive('isStudent', function () {
            return "<?php if ( Auth::user()->isStudent() ) { ?>";
        });
        Blade::directive('endIsStudent', function () {
            return "<?php } ?>";
        });

        if (env('APP_SECURE', true)) {
            URL::forceScheme('https');
        }

        \Illuminate\Support\Facades\Date::useClass(Carbon::class);
    }
}
