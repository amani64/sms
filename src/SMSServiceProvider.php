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
            __DIR__.'/config/sms.php' => config_path('sms.php'),
            __DIR__.'/config/isms.php' => config_path('isms.php'),
            __DIR__.'/config/kavenegar.php' => config_path('kavenegar.php'),
            __DIR__.'/database/migrations/2019_07_13_113759_create_sms_log_table.php' => base_path('database/migrations/2019_07_13_113759_create_sms_log_table.php'),
        ]);
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
