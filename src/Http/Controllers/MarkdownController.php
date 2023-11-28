<?php

namespace Larasense\StaticMarkdownRoute\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Larasense\StaticMarkdownRoute\Facades\MarkDown;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Larasense\StaticMarkdownRoute\Facades\MarkDownRoute;

class MarkdownController extends Controller
{
    public function handle(string $file, Request $request): View
    {
        $filename = Str::substr($file, 0, -5);
        if (!$request->route() instanceof Route) {
            abort(404);
        }

        $directory = MarkDownRoute::getDirInfo($request->route()->uri);
        $content = MarkDown::toHtml(dirname(urlPath($request->route()->uri)."$filename.md"), "$directory/$filename.md");

        if(!$content) {
            abort(404);
        }

        return view('static-markdown-route::base', [
            'raw' => $content,
        ]);
    }
}
