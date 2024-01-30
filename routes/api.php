<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('ja-member','App\Http\Controllers\Api\VisitorController@ja_member');
Route::post('simpleform','App\Http\Controllers\Api\VisitorController@simpleForm');
Route::post('simpleform-no-invite','App\Http\Controllers\Api\VisitorController@simpleFormNoInvite');
Route::post('confirmation','App\Http\Controllers\Api\VisitorController@confirmation');
Route::post('sync/{slug}','App\Http\Controllers\Api\VisitorController@sync');
Route::post('error', function (Request $request) {
    $str = "ClientError $request->url".PHP_EOL;
	$str .="[stacktrace]".PHP_EOL;
	$str .="Url: $request->url".PHP_EOL;
	$str .="Line: $request->line $request->msg".PHP_EOL;
	$str .="Msg: $request->msg".PHP_EOL;
	if (($request->url == null && $request->line=="0") != true)
		Log::error($str.PHP_EOL,$request->all());
});