<?php

namespace Umang\LaravelCodeFormatter;

use Illuminate\Support\ServiceProvider;
use Umang\LaravelCodeFormatter\Console\Commands\FormatControllersCommand;
use Umang\LaravelCodeFormatter\Console\Commands\FormatModelsCommand;
use Umang\LaravelCodeFormatter\Console\Commands\FormatBladesCommand;
use Umang\LaravelCodeFormatter\Console\Commands\InstallConfigCommand;

class LaravelCodeFormatterServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {   
        // Register the command
        // $this->commands([
        //     FormatControllersCommand::class,
        //     FormatModelsCommand::class,
        //     FormatBladesCommand::class,
        // ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {   
        if ($this->app->runningInConsole()) {
            $this->commands([
                FormatControllersCommand::class,
                FormatModelsCommand::class,
                FormatBladesCommand::class,
                InstallConfigCommand::class,
            ]);

            // Publish config file
            $this->publishes([
                __DIR__ . '/../config/formatter.json' => base_path('formatter.json'),
            ], 'formatter-config');

            $this->publishes([
                __DIR__ . '/../scripts/prettier-setup.sh' => base_path('prettier-setup.sh'),
            ], 'formatter-scripts');
        }
    }
}