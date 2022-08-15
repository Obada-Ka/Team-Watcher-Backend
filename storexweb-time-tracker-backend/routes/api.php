<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use  App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\TimerController;
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
Route::group(['middleware' => 'auth:sanctum'], function() {

    Route::post('logout', [AuthController::class, 'logout']);

    Route::post('/startTimer',[TimerController::class, 'startTimer']);
    Route::post('/stopTimer/{id}', [TimerController::class, 'stopTimer']);

    Route::get('/getrangeTimer', [TimerController::class, 'getRange']);
    Route::get('/getAllDayTimer', [TimerController::class, 'getAllDayTimer']);

    Route::get('/getAllUsers', [UserController::class, 'getAllUsers'])->middleware('roleChecker');
    Route::post('/deleteAssignRole/{user_id}/{roleid}', [RoleController::class, 'deleteAssignRole'])->middleware('roleChecker');
    Route::get('/getRole/{user_id}', [RoleController::class, 'getRole'])->middleware('roleChecker');
    Route::post('/assignRole/{user_id}/{roleid}', [RoleController::class, 'assignRole'])->middleware('roleChecker');
    Route::get('getStatus', [UserController::class, 'getStatus'])->middleware('roleChecker');
    Route::get('getAllRoles', [RoleController::class, 'getAllRoles'])->middleware('roleChecker');


});

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);






