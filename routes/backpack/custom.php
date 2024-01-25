<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () { // custom admin routes

    Route::crud('visitor', 'VisitorCrudController');
    Route::get('visitor/scan', 'VisitorCrudController@scanVisitor');
    Route::get('visitor/{barcode}/scan', 'VisitorCrudController@scan');
    Route::crud('company', 'CompanyCrudController');

    Route::group([
        'middleware' => ['role:Admin'],
    ], function () {
        Route::crud('visitor-category', 'VisitorCategoryCrudController');
        Route::crud('location', 'LocationCrudController');
        Route::crud('profession', 'ProfessionCrudController');
        Route::crud('field', 'FieldCrudController');
        Route::crud('duration', 'DurationCrudController');

        Route::crud('city', 'CityCrudController');
        Route::crud('state', 'StateCrudController');
        Route::crud('hotel', 'HotelCrudController');
        
        Route::get('operations', 'AdminController@operations');
        Route::post('operations/importvisitors', 'AdminController@importVisitors');
        Route::get('report/{type}/{print?}', 'AdminController@report');
        Route::get('job', function () {
            \App\Jobs\ProcessSync::dispatch();
            return "done";
        });
    });
    Route::group(['middleware' => ['can:Invites']], function () {
        Route::crud('invite', 'InviteCrudController');
    });
    Route::get('operations/check-data', 'AdminController@checkData');
}); // this should be the absolute last line of this file