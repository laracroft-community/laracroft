<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermisionUserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PermissionRoleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoleUserController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/../helpers/utils.php';

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

Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:api'], function () {

    /*
     Security
    */
    Route::post('/logout', [AuthController::class, 'logout']);

    createRoute(
        [
            'permission',
            'user',
            'permission_user',
            'permission_role',
            'role',
            'role_user'
        ],
        [
            PermissionController::class,
            UserController::class,
            PermisionUserController::class,
            PermissionRoleController::class,
            RoleController::class,
            RoleUserController::class
        ]
    );

    /*
    |
    | Here is where you can register other API routes for your application.
    | Call createRoute() function with two arguments: routes prefix name, controllers class 
    */


});
