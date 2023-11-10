<?php

use Larasense\StaticMarkdownRoute\Facades\MarkDownRoute;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Finder\SplFileInfo;

use function Pest\Laravel\{artisan};

$strMarkdown = <<<EOD
# Title

![alt text](./image.png)

EOD;

it('should list pages to generate', function () {
    Config::set('app.url', 'http://localhost');
    MarkDownRoute::get('/docs', base_path() . "/docus");

    File::shouldReceive('allFiles')
        ->with(base_path() . "/docus")
        ->andReturn(
            [
                new SplFileInfo(base_path() . "/docs/README.md", "", "README.md"),
                new SplFileInfo(base_path() . "/docs/dir1/README.md", "dir1", "dir1/README.md"),
            ]
        )->times(1);

    artisan('static:list-markdown-routes')
    ->expectsTable(['Markdown Pages'], [["http://localhost/docs/README"],["http://localhost/docs/dir1/README"]])
    ->assertSuccessful()
    ;

});

it('should generate pages', function () {
    Config::set('app.url', 'http://localhost');
    MarkDownRoute::get('/public_docs', base_path() . "/base_docs");

    $response = fakeResponse(['props'=>[]]);
    File::shouldReceive('allFiles')
        ->with(base_path() . "/base_docs")
        ->andReturn(
            [
                new SplFileInfo(base_path() . "/base_docs/README.md", "", "README.md"),
                new SplFileInfo(base_path() . "/base_docs/dir1/README.md", "dir1", "dir1/README.md"),
            ]
        )->times(1);
    File::shouldReceive('get')->with(base_path() . "/base_docs/README.md")->andReturn("# Title");
    File::shouldReceive('get')->with(base_path() . "/base_docs/dir1/README.md")->andReturn("# Dir1 Title");
    File::makePartial();
    Http::shouldReceive('get')->with('http://localhost/public_docs/README')->andReturn($response)->times(1);
    Http::shouldReceive('get')->with('http://localhost/public_docs/dir1/README')->andReturn($response)->times(1);

    artisan('static:generate-markdown-routes')->assertSuccessful();
});


it('should copy images into destination URL', function () use($strMarkdown){

    MarkDownRoute::get('/docs', base_path() . "/docus");
    File::shouldReceive('allFiles')
        ->with(base_path() . "/docus")
        ->andReturn(
            [
                new SplFileInfo(base_path() . "/docus/README.md", "", "README.md"),
            ]
        )->times(1);
    File::shouldReceive('get')->with(base_path() . "/docus/README.md")->andReturn($strMarkdown);
    File::shouldReceive('copy')->with(base_path() . "/docus/image.png", public_path() . "/docs/image.png")->andReturn(true)->times(1);
    File::makePartial();

    $response = fakeResponse(['props'=>[]]);
    Http::shouldReceive('get')->with('http://localhost/docs/README')->andReturn($response)->times(1);

    artisan('static:generate-markdown-routes')->assertSuccessful();


});
