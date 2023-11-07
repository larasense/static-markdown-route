<?php

use Larasense\StaticMarkdownRoute\Facades\MarkDownRoute;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Finder\SplFileInfo;
use function Pest\Laravel\{artisan};

it('should list pages to generate', function(){
    Config::set('app.url', 'http://localhost');
    MarkDownRoute::get('/docs', base_path() . "/docus");

    File::shouldReceive('allFiles')
        ->with(base_path() . "/docus")
        ->andReturn(
            [
                new SplFileInfo(base_path() . "/docs/README.md", "", "README.md"),
                new SplFileInfo(base_path() . "/docs/dir1/README.md", "dir1", "dir1/README.md"),
            ])->times(1);

    artisan('static:list-markdown-routes')
    ->expectsTable(['Markdown Pages'], [["http://localhost/docs/README"],["http://localhost/docs/dir1/README"]])
    ->assertSuccessful()
    ;

});

it('should generate pages', function () {
    Config::set('app.url', 'http://localhost');
    MarkDownRoute::get('/docs', base_path() . "/docus");

    $response = fakeResponse(['props'=>[]]);
    File::shouldReceive('allFiles')
        ->with(base_path() . "/docus")
        ->andReturn(
            [
                new SplFileInfo(base_path() . "/docs/README.md", "", "README.md"),
                new SplFileInfo(base_path() . "/docs/dir1/README.md", "dir1", "dir1/README.md"),
            ])->times(1);
    File::makePartial();
    Http::shouldReceive('get')->with('http://localhost/docs/README')->andReturn($response)->times(1);
    Http::shouldReceive('get')->with('http://localhost/docs/dir1/README')->andReturn($response)->times(1);

    artisan('static:generate-markdown-routes')->assertSuccessful();
});

