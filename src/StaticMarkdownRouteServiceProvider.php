<?php

namespace Larasense\StaticMarkdownRoute;

use Illuminate\Support\ServiceProvider;
use Larasense\StaticMarkdownRoute\Console\Commands\GenerateCommand;

class StaticMarkdownRouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands(
                [
                    GenerateCommand::class,
                ],
            );
        }
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'static-markdown-route');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/staticsitegen.php',
            'static-markdown-route'
        );
    }
}
