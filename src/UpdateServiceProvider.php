<?php
namespace MrCrankHank\Update;

use Illuminate\Support\ServiceProvider;
use MrCrankHank\Update\Commands\Update;
use MrCrankHank\Update\Commands\GenerateJsonFileCommand;

class UpdateServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/update.php' => config_path('update.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/update.php', 'update');

        $this->app->bind('command.update:run', GenerateJsonFileCommand::class);

        $this->app->bind('command.update:generate-json-file', Update::class);

        $this->commands([
            'command.update:run',
            'command.update:generate-json-file',
        ]);
    }
}