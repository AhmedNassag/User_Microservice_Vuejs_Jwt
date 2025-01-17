<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});




// Route::group(['middleware' => 'JwtMiddleware'],function() {

    //roles
    Route::resource('roles', RoleController::class);
    Route::post('storePermission', [RoleController::class, 'storePermission']);

    //users
    Route::resource('users', UserController::class);

    Route::post('logout',[UserController::class, 'logout']);
// });


Route::post('register',[UserController::class, 'store']);
Route::post('login',[UserController::class, 'login']);
