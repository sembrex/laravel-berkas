<?php

namespace Karogis\Berkas;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class BerkasServiceProvider extends ServiceProvider
{
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['berkas'];
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        $this->loadViewsFrom(__DIR__ . '/../views', 'berkas');

        $this->loadMigrationsFrom(__DIR__ . '/../migrations');

        $this->mergeConfigFrom(__DIR__ . '/../config/berkas.php', 'berkas');

        $this->publishes([
            __DIR__ . '/../config/berkas.php' => config_path('berkas.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../views' => resource_path('views/vendor/berkas'),
        ], 'views');

        $this->publishes([
            __DIR__ . '/../assets' => public_path('vendor/berkas'),
        ], 'public');

        $this->publishes([
            __DIR__ . '/../migrations/' => database_path('migrations')
        ], 'migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //$this->app->register('Intervention\Image');

        if (!File::exists(public_path('storage'))) {
            File::link(storage_path('app/public'), public_path('storage'));
        }

        $this->app->singleton('berkas', function ($app) {
            return new Berkas();
        });
    }
}
