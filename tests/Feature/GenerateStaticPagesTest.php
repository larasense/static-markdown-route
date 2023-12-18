<?php

use Larasense\StaticMarkdownRoute\Facades\MarkDownRoute;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

use function Pest\Laravel\{artisan};

$strMarkdown = <<<EOD
# Title

![alt text](./image.png)

EOD;

it('should list pages to generate', function () {
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
        ->expectsTable(['Markdown Pages'], [["http://localhost/docs/README.html"],["http://localhost/docs/dir1/README.html"]])
        ->assertSuccessful()
    ;

});

it('should generate pages', function () {
    MarkDownRoute::get('/public_docs', base_path() . "/base_docs");

    $response = fakeResponse(['props' => []]);
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
    File::shouldReceive('isEmptyDirectory')->with(public_path() . "/public_docs/", true)->andReturn(true);
    File::partialMock();
    Http::shouldReceive('get')->with('http://localhost/public_docs/README.html')->andReturn($response)->times(1);
    Http::shouldReceive('get')->with('http://localhost/public_docs/dir1/README.html')->andReturn($response)->times(1);

    artisan('static:generate-markdown-routes')
    ->assertSuccessful()
    ;
});

it('should generate pages even when public directory is not empty', function () {
    MarkDownRoute::get('/public_docs', base_path() . "/base_docs");

    $response = fakeResponse(['props' => []]);
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
    File::shouldReceive('isEmptyDirectory')->with(public_path() . "/public_docs/", true)->andReturn(false);
    File::partialMock();
    Http::shouldReceive('get')->with('http://localhost/public_docs/README.html')->andReturn($response)->times(1);
    Http::shouldReceive('get')->with('http://localhost/public_docs/dir1/README.html')->andReturn($response)->times(1);

    artisan('static:generate-markdown-routes')
    ->expectsQuestion('there are already generated files. Do you want to delete these files?[Y/n]', 'y')
    ->assertSuccessful()
    ;
});

it('should not generate the files if the user choose not to', function () {
    MarkDownRoute::get('/public_docs', base_path() . "/base_docs");

    File::shouldReceive('isEmptyDirectory')->with(public_path() . "/public_docs/", true)->andReturn(false);
    File::partialMock();

    artisan('static:generate-markdown-routes')
    ->expectsQuestion('there are already generated files. Do you want to delete these files?[Y/n]', 'n')
    ->assertSuccessful()
    ;
});

it('should generate pages even when public directory is not empty and its forced to', function () {
    MarkDownRoute::get('/public_docs', base_path() . "/base_docs");

    $response = fakeResponse(['props' => []]);
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
    File::shouldReceive('isEmptyDirectory')->with(public_path() . "/public_docs/", true)->andReturn(false);
    File::partialMock();
    Http::shouldReceive('get')->with('http://localhost/public_docs/README.html')->andReturn($response)->times(1);
    Http::shouldReceive('get')->with('http://localhost/public_docs/dir1/README.html')->andReturn($response)->times(1);

    artisan('static:generate-markdown-routes -F')
    ->assertSuccessful()
    ;
});

it('should copy images into destination URL', function () use ($strMarkdown) {

    MarkDownRoute::get('/docs', base_path() . "/docus");
    File::shouldReceive('allFiles')
        ->with(base_path() . "/docus")
        ->andReturn(
            [
                new SplFileInfo(base_path() . "/docus/README.md", "", "README.md"),
                new SplFileInfo(base_path() . "/docus/dir1/README.md", "dir1", "dir1/README.md"),
            ]
        )->times(1);
    File::shouldReceive('get')->with(base_path() . "/docus/README.md")->andReturn($strMarkdown);
    File::shouldReceive('get')->with(base_path() . "/docus/dir1/README.md")->andReturn($strMarkdown);
    File::shouldReceive('isEmptyDirectory')->with(public_path() . "/docs/", true)->andReturn(true);

    $response = fakeResponse(['props' => []]);
    Http::shouldReceive('get')->with('http://localhost/docs/README.html')->andReturn($response)->times(1);
    Http::shouldReceive('get')->with('http://localhost/docs/dir1/README.html')->andReturn($response)->times(1);

    artisan('static:generate-markdown-routes')->assertSuccessful("pregunta");


});
