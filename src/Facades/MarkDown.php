<?php

namespace Larasense\StaticMarkdownRoute\Facades;

use Illuminate\Support\Facades\Facade;
use Larasense\StaticMarkdownRoute\Services\MarkdownService;

/**
 *
 * @mixin \Larasense\StaticMarkdownRoute\Services\MarkdownService
 */
class MarkDown extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return MarkdownService::class;
    }
}
