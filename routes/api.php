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

// /api/get_permissions
//Route::get(
//    '/get_permissions', 
//    [
//        \App\Http\Controllers\Admin\PermissionController::class, 
//        'getPermissionsToSelect'
//    ]
//)->name('get_permissions');

// /api/get_roles
//Route::get(
//    '/get_roles', 
//    [App\Http\Controllers\Admin\RoleController::class, 'getRolesToSelect']
//)->name('get_roles');