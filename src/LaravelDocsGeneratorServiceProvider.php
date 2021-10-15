<?php

namespace Mindz\LaravelDocsGenerator;

use Illuminate\Support\ServiceProvider;
use Mindz\LaravelDocsGenerator\Commands\FortifyDocsCommand;
use Mindz\LaravelDocsGenerator\Commands\GenerateCrudControllerDocsCommand;
use Mindz\LaravelDocsGenerator\Commands\GenerateDocCommand;
use Mindz\LaravelDocsGenerator\Commands\InitDocumentationCommand;

class LaravelDocsGeneratorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateCrudControllerDocsCommand::class,
                InitDocumentationCommand::class,
                GenerateDocCommand::class,
                FortifyDocsCommand::class
            ]);
        }

        $this->publishes([
            __DIR__ . '/../config/docs-generator.php' => config_path('docs-generator.php'),
        ], 'config');
    }
}
