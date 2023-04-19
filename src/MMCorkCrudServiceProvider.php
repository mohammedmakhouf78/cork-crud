<?php

namespace Mohammedmakhlouf78\CorkCrud;

use Illuminate\Support\ServiceProvider;
use Mohammedmakhlouf78\CorkCrud\Console\CrudCommand;

class MMCorkCrudServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'corkcrud');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CrudCommand::class
            ]);
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('corkcrud.php'),
            ], 'config');
        }


        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }
}
