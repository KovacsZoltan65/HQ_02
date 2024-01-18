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

Route::get(
    '/', 
    [App\Http\Controllers\HomeController::class, 'index']
)->name('dashboard');


Route::post('/language', [App\Http\Controllers\LanguageController::class, 'index'])->name('language');
Route::post('/generate_password', function(Illuminate\Http\Request $request){
    
    //\Log::info( print_r( $request->get('minLength') , true) );
    //\Log::info( print_r( $request->get('maxLength') , true) );
    
    $minLength = $request->get('minLength', 6);
    $maxLength = $request->get('maxLength', 8);
    $password = generatePassword($minLength, $maxLength);
    
    //\Log::info( print_r( $password , true) );
    
    return $password;
    
})->name('generatePassword');

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
// Felhasználó csoportos törlése
Route::delete(
    '/users/bulkDelete', 
    /*[App\Http\Controllers\Admin\UsersController::class, 'bulkDelete']*/
    function(Illuminate\Http\Request $request){
    dd($request->all());
    }
)->name('users.bulkDelete');

Route::resource('/users', \App\Http\Controllers\Admin\UsersController::class
)->names([
      'index' => 'users',
     'create' => 'users_create',
      'store' => 'users_store',
       'edit' => 'users_edit',
     'update' => 'users_update',
    'destroy' => 'users_destroy',
    'restore' => 'users_restore',
]);

// ===============================
// SUBDOMAINS
// ===============================
Route::get(
    '/getSubdomains', 
    [\App\Http\Controllers\SubdomainController::class, 'getSubdomains']
)->name('getSubdomains');

Route::get('/getSubdomains2', [\App\Http\Controllers\SubdomainController::class, 'getSubdomains2'])->name('getSubdomains2');

Route::delete('/subdomains/{subdomain}/bulkDelete', [\App\Http\Controllers\SubdomainController::class, 'bulkDelete'])->name('subdomains_bulkDelete');
Route::post('/subdomains/{subdomain}/restore/', [\App\Http\Controllers\SubdomainController::class, 'restore'])->name('subdomains_restore');

Route::resource('/subdomains', \App\Http\Controllers\SubdomainController::class)->names([
      'index' => 'subdomains',
     'create' => 'subdomains_create',
      'store' => 'subdomains_store',
       'view' => 'subdomains_view',
       'edit' => 'subdomains_edit',
     'update' => 'subdomains_update',
    'destroy' => 'subdomains_destroy',
]);

// ===============================
// ROLES
// ===============================
Route::get(
    '/getRoles', 
    [\App\Http\Controllers\Admin\RoleController::class, 'getRoles']
)->name('getRoles');

Route::delete('/roles/{role}/bulkDelete', [\App\Http\Controllers\Admin\RoleController::class, 'bulkDelete'])->name('roles_bulkDelete');
Route::post('/roles/{role}/restore/', [\App\Http\Controllers\Admin\RoleController::class, 'restore'])->name('roles_restore');

Route::resource('/roles', \App\Http\Controllers\Admin\RoleController::class)->names([
      'index' => 'roles',
     'create' => 'roles_create',
      'store' => 'roles_store',
       'view' => 'roles_view',
       'edit' => 'roles_edit',
     'update' => 'roles_update',
    'destroy' => 'roles_destroy',
]);

require __DIR__.'/auth.php';
