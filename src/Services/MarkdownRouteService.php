<?php

namespace Larasense\StaticMarkdownRoute\Services;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route as RouteFacade;
use Larasense\StaticMarkdownRoute\Http\Controllers\MarkdownController;
use Larasense\StaticMarkdownRoute\Models\FileInfo;

class MarkdownRouteService
{
    /** @var array<string,string> $dir_info; */
    protected array $dir_info = [];

    public function get(string $uri, string $directory): Route
    {
        $this->addDirInfo($uri, $directory);

        return RouteFacade::get("$uri/{file}", [MarkdownController::class, 'handle'])
        ->where('file', '.*');
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
            $files = File::allFiles($directory);
            foreach($files as $file) {
                $dir_files []= new FileInfo(
                    $route,
                    dirname(urlPath($route).$file->getRelativePathname())."/".$file->getBasename('.' . $file->getExtension()),
                    $directory,
                    $file->getPathname()
                );
            }
        }
        return $dir_files;

    }

}
