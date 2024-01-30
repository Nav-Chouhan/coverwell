<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Intervention\Image\ImageManagerStatic;
use Clarkeash\Doorman\Facades\Doorman;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
         $this->app->bind(
            \Backpack\PermissionManager\app\Http\Controllers\UserCrudController::class, //this is package controller
            \App\Http\Controllers\Admin\UserCrudController::class //this should be your own controller
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('invite_code', function ($attribute, $value, $parameters, $validator) {
            
            return Doorman::check($value);
        },'The :attribute expired or invalid');

        Validator::extend('base64image', function ($attribute, $value, $parameters, $validator) {
            try {
                ImageManagerStatic::make($value);
                return true;
            } catch (\Exception $e) {
                return false;
            }
        });
    }
}
