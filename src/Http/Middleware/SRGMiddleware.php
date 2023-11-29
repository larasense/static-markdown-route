<?php

namespace Larasense\StaticMarkdownRoute\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Larasense\StaticMarkdownRoute\Facades\MarkDownRoute;

class SRGMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  Closure  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        MarkDownRoute::processImages($request);

        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function terminate(Request $request, Response $response): void
    {
        MarkDownRoute::process($request, $response);
        // dd($response);

    }
}
