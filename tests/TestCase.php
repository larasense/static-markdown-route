<?php

namespace Larasense\StaticMarkdownRoute\Tests;


use Orchestra\Testbench\TestCase as Orchestra;
use Larasense\StaticMarkdownRoute\StaticMarkdownRouteServiceProvider;
use Spatie\LaravelIgnition\IgnitionServiceProvider;


class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            IgnitionServiceProvider::class,
            StaticMarkdownRouteServiceProvider::class,
        ];
    }
}
