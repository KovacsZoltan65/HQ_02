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
Route::get('/getRoles', [\App\Http\Controllers\Admin\RoleController::class, 'getRoles'])->name('getRoles');
Route::resource('/roles', \App\Http\Controllers\Admin\RoleController::class)->names([
    'index' => 'roles'
]);

require __DIR__.'/auth.php';
