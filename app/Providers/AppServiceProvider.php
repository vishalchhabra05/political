<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if((url('/')!='http://localhost:8000')&&(url('/')!='http://127.0.0.1:8000')&&(url('/')!='http://192.168.2.80:8000')) 
        URL::forceScheme('https');
    }
}
