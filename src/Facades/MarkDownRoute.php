<?php

namespace Larasense\StaticMarkdownRoute\Facades;

use Illuminate\Support\Facades\Facade;
use Larasense\StaticMarkdownRoute\Services\MarkdownRouteService;

/**
 * Facade funtions that deal with the Attributes
 *
 *  @mixin MarkdownRouteService
 */
class MarkDownRoute extends Facade
{

    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return MarkdownRouteService::class;
    }
}
