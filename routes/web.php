<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/invite/nja-{barcode}', [App\Http\Controllers\HomeController::class, 'hbinvited'])->name('hbinvited');
Route::get('/internal/{id}', [App\Http\Controllers\InternalController::class, 'index'])->name('internal');
Route::post('/internal-store', [App\Http\Controllers\InternalController::class, 'store'])->name('internal.store');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [App\Http\Controllers\CustomAuthController::class, 'dashboard'])->name('dashboard'); 
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard'); 
Route::get('/hospitality-login', [App\Http\Controllers\CustomAuthController::class, 'index'])->name('login'); 
Route::post('/hospitality-login', [App\Http\Controllers\CustomAuthController::class, 'hospitalityLogin'])->name('hospitality-login');
// Route::get('/jas-login', [App\Http\Controllers\CustomAuthController::class, 'jasLoginFrm'])->name('jasLogin');
// Route::post('/jas-login', [App\Http\Controllers\CustomAuthController::class, 'jasLogin'])->name('jas-login');
Route::post('/update-car-info', [App\Http\Controllers\DashboardController::class, 'updateCarInfo'])->name('update-car-info');
Route::post('/update-room-info', [App\Http\Controllers\DashboardController::class, 'updateRoomInfo'])->name('update-room-info');
Route::get('/search-visitor', [App\Http\Controllers\DashboardController::class, 'searchVisitor'])->name('search-visitor'); 
Route::get('signout', [App\Http\Controllers\CustomAuthController::class, 'signOut'])->name('signout');
Route::get('/check-already-hosted', [App\Http\Controllers\RegisterController::class, 'checkHosted']);
// Route::get('/registration/{id}', [App\Http\Controllers\RegisterController::class, 'index'])->name('registration');
// Route::get('/hosted-by/{buyer_id}', [App\Http\Controllers\RegisterController::class, 'index'])->name('registration');
// Route::get('/{nri}/registration', [App\Http\Controllers\RegisterController::class, 'registration'])->name('registrationFrm');
// Route::get('/registration-vip', [App\Http\Controllers\RegisterController::class, 'registration'])->name('registrationFrm');
Route::post('/store', [App\Http\Controllers\RegisterController::class, 'store'])->name('visitor.store');
Route::get('/open-chb/{id}', [App\Http\Controllers\RegisterController::class, 'openChb'])->name('openChb');
Route::get('{page}/{subs?}', ['uses' => 'App\Http\Controllers\PageController@index'])->where(['page' => '^(((?=(?!admin))(?=(?!\/)).))*$', 'subs' => '.*']);
Route::get('/send-email', [EmailController::class, 'index']);
