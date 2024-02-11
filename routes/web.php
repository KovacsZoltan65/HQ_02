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
// Szerepkörök lekérése táblázathoz
Route::get(
    '/getRoles', 
    [
        \App\Http\Controllers\Admin\RoleController::class, 
        'getRoles'
    ]
)->name('getRoles');
// Szerepkörök lekérése lenyílóhoz
Route::get(
    '/getRolesToSelect', 
    [
        \App\Http\Controllers\Admin\RoleController::class, 
        'getRolesToSelect'
    ]
)->name('getRolesToSelect');
// Csoportos törlés
Route::delete('/roles/{role}/bulkDelete', [\App\Http\Controllers\RoleController::class, 'bulkDelete'])->name('roles_bulkDelete');

Route::resource('/roles', \App\Http\Controllers\Admin\RoleController::class)->names([
      'index' => 'roles',
     'create' => 'roles_create',
      'store' => 'roles_store',
       'view' => 'roles_view',
       'edit' => 'roles_edit',
     'update' => 'roles_update',
    'destroy' => 'roles_destroy',
    'restore' => 'roles_restore',
]);

// ===============================
// PERMISSIONS
// ===============================
//
Route::get(
    '/getPermissions', 
    [
        \App\Http\Controllers\Admin\PermissionController::class, 
        'getPermissions'
    ]
    )->name('getPermissions');
//
Route::get(
    '/getPermissionsToSelect', 
    [
        \App\Http\Controllers\Admin\PermissionController::class, 
        'getPermissionsToSelect'
    ]
)->name('getPermissionsToSelect');
//
Route::delete('/permissions/{permission}/bulkDelete', [\App\Http\Controllers\PermissionController::class, 'bulkDelete'])->name('permissions_bulkDelete');

Route::resource('/permissions', \App\Http\Controllers\Admin\PermissionController::class)->names([
      'index' => 'permissions',
     'create' => 'permissions_create',
      'store' => 'permissions_store',
       'view' => 'permissions_view',
       'edit' => 'permissions_edit',
     'update' => 'permissions_update',
    'destroy' => 'permissions_destroy',
    'restore' => 'permissions_restore',
]);

require __DIR__.'/auth.php';
