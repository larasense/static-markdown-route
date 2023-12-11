<?php

namespace Larasense\StaticMarkdownRoute\Services;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Larasense\StaticMarkdownRoute\Http\Controllers\MarkdownController;
use Larasense\StaticMarkdownRoute\Http\Middleware\SRGMiddleware;
use Larasense\StaticMarkdownRoute\Models\EmptyRoute;
use Larasense\StaticMarkdownRoute\Models\FileInfo;

class MarkdownRouteService
{
    /** @var array<string,string> $dir_info; */
    protected array $dir_info = [];

    public function get(string $uri, string $directory): Route | EmptyRoute
    {
        if(app()->environment('production') && !Config::get('staticmarkdownroute.force')) {
            return new EmptyRoute();
        }

        $this->addDirInfo($uri, $directory);

        return RouteFacade::get("$uri/{file}", [MarkdownController::class, 'handle'])
            ->where('file', '.*\.(md|MD|html|HTML)$')
            ->middleware(SRGMiddleware::class)
        ;
    }

    public function addDirInfo(string $uri, string $directory): self
    {
        $this->dir_info["$uri/{file}"] = $directory;
        return $this;
    }

    public function getDirInfo(string $uri): string
    {
        return $this->dir_info["/$uri"];
    }
    /**
     * @return array<int,\Larasense\StaticMarkdownRoute\Models\FileInfo>
     */
    public function getDirFiles(): array
    {
        $dir_files = [];
        foreach($this->dir_info as $route => $directory) {
            $files = collect(File::allFiles($directory))->filter(fn ($file) => Str::substr($file, -3) == '.md');
            foreach($files as $file) {
                $dir_files[] = new FileInfo(
                    $route,
                    dirname(urlPath($route).$file->getRelativePathname())."/".$file->getBasename('.' . $file->getExtension()),
                    $directory,
                    $file->getPathname()
                );
            }
        }
        return $dir_files;

    }

    public function process(Request $request, Response $response): void
    {
        if(!Config::get('staticmarkdownroute.force')) {
            return;
        }
        if ($response->status() !== 200) {
            return;
        }
        if (!$content = $response->getContent()) {
            return;
        }

        if($route = $request->route()) {

            $file = $route->parameters()['file']; /** @var string $file */
            $dir = Str::replace('{file}', '', $route->uri); /** @var string $dir */

            $path = public_path(). "/" . $dir . $file;
            File::put($path, $content);
        }
    }

    public function processImages(Request $request): void
    {
        if($route = $request->route()) {
            /** @var string $file_param */
            $file_param = $route->parameters()['file'];
            $directory = dirname($this->getDirInfo($route->uri) . "/" . $file_param);
            /** @var string $pub_uri */
            $pub_uri = Str::replace('{file}', '', $route->uri);
            $public_dir = dirname(public_path() ."/". $pub_uri . $file_param);

            /** @var \Illuminate\Support\Collection<int,\Symfony\Component\Finder\SplFileInfo> $files */
            $files = collect(File::files($directory))->filter(fn ($file_param) => Str::substr($file_param, -3) !== '.md');
            foreach($files as $file) {
                $fileInfo = new FileInfo(
                    $route->uri,
                    $public_dir."/".$file->getRelativePathname(),
                    $directory,
                    $file->getPathname()
                );
                File::ensureDirectoryExists(dirname($fileInfo->url));
                File::copy($fileInfo->filename, $fileInfo->url);
            }
        }
    }

    public function hasFiles(): bool
    {
        return true;
    }
}
