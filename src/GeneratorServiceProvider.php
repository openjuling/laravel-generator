<?php

namespace Juling\Generator;

use Juling\Generator\Console\Commands\GenDict;
use Juling\Generator\Console\Commands\GenEntity;
use Juling\Generator\Console\Commands\GenInterface;
use Juling\Generator\Console\Commands\GenModel;
use Juling\Generator\Console\Commands\GenRepository;
use Juling\Generator\Console\Commands\GenRoute;
use Juling\Generator\Console\Commands\GenService;
use Juling\Generator\Console\Commands\InstallCommand;
use Illuminate\Support\ServiceProvider;

class GeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                GenDict::class,
                GenEntity::class,
                GenInterface::class,
                GenModel::class,
                GenRepository::class,
                GenRoute::class,
                GenService::class,
            ]);
        }
    }
}