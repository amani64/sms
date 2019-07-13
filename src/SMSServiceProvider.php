<?php

namespace Amani64\SMS;

use Illuminate\Support\ServiceProvider;
use ReflectionClass;

class SMSServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->publishes([
            __DIR__ . '/../config/' => config_path(),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations')
        ], 'migrations');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('SMS', function(){
            $class = 'Amani64\\SMS\\' . ucfirst(config('sms.driver')) . 'Driver';
            return new $class();
        });
    }
}
