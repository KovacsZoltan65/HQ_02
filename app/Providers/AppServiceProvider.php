<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Inertia::share([
            'errors' => function(){
                return Session::get('errors') 
                    ? Session::get('errors')->getBag('default')->getMessages() 
                    : (object)[];
            },
        ]);

        Inertia::share([
            // ...
            'locale' => function(){
                return app()->getLocale();
            },
            // Elérhető nyelvek
            'languages' => config('app.available_locales'),
        ]);

        Inertia::share('flash', function(){
            return [
                'message' => Session::get('message')
            ];
        });

        Inertia::share('csrf_token', function(){
            return csrf_token();
        });

    }
}
