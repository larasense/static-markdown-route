<?php

namespace Larasense\StaticMarkdownRoute\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Larasense\StaticMarkdownRoute\Facades\MarkDownRoute;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

class MarkdownController extends Controller
{
    public function __construct()
    {
    }

    public function handle(string $file, Request $request): View
    {
        if (!$request->route() instanceof Route){
            abort(404);
        }
        $directory = MarkDownRoute::getDirInfo($request->route()->uri);

        $content = File::get("$directory/$file.md");
        if(!$content){
            abort(404);
        }

        return view('static-markdown-route::base', [
            'raw' => Str::markdown($content),
        ]);
    }
}
