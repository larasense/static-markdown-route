<?php

namespace Larasense\StaticMarkdownRoute;

use Illuminate\Support\ServiceProvider;
use Larasense\StaticMarkdownRoute\Console\Commands\GenerateCommand;
use Larasense\StaticMarkdownRoute\Console\Commands\ListCommand;

class StaticMarkdownRouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands(
                [
                    GenerateCommand::class,
                    ListCommand::class,
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
            __DIR__.'/../config/staticmarkdownroute.php',
            'static-markdown-route'
        );
    }
}
