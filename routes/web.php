<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ===============================
// USERS
// ===============================
// felhasználók lekérése
Route::get(
    '/getUsers', 
    [App\Http\Controllers\Admin\UsersController::class, 'getUsers']
)->name('getUsers');
// jelszó módosítás
Route::post(
    '/change_password/{user}', 
    [App\Http\Controllers\Admin\UsersController::class, 'chengePassword']
)->name('change_password');

Route::resource('/users', App\Http\Controllers\Admin\UsersController::class 
)->names([
      'index' => 'users',
     'create' => 'users_create',
      'store' => 'users_store',
       'edit' => 'users_edit',
     'update' => 'users_update',
    'destroy' => 'users_destroy',
    'restore' => 'users_restore',
]);

require __DIR__.'/auth.php';
