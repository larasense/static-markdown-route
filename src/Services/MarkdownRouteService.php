<?php

namespace Larasense\StaticMarkdownRoute\Services;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route as RouteFacade;
use Larasense\StaticMarkdownRoute\Http\Controllers\MarkdownController;

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

    public function getDirInfo(string $uri):string
    {
        return $this->dir_info["/$uri"];
    }
    /**
     * @return array<string,string>
     */
    public function getDirFiles():array
    {
        $dir_files = [];
        foreach($this->dir_info as $route => $directory){
            $files = File::allFiles($directory);
            foreach($files as $file){
                $dir_files[$this->toUrl($this->urlPath($route)."".$file->getRelativePathname())]=$file->getPathname();
            }
        }
        return $dir_files;

    }

    protected function toUrl(string $filename): string
    {
        if(substr($filename, -3)==='.md'){
            return substr($filename,0,-3);
        }
        return $filename;
    }

    protected function urlPath(string $uri): string
    {
        return str_replace('{file}', '', $uri );
    }
}
