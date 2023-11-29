<?php

use Larasense\StaticMarkdownRoute\Facades\MarkDownRoute;
use Illuminate\Support\Facades\Route as RouteFacade;
use Symfony\Component\Finder\SplFileInfo;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

use function Pest\Laravel\{get};

it('should add the `dir_info` to the Facade', function () {
    MarkDownRoute::get('/docs', base_path() . "/docs");

    expect(MarkDownRoute::getDirInfo('docs/{file}'))
        ->toBe(base_path() . "/docs")
    ;
});

it('should register the route as a `Illuminate\Routing\Route`', function () {
    MarkDownRoute::get('/docs', base_path() . "/docs");

    expect(RouteFacade::getRoutes()->getRoutesByMethod()['GET'])
        ->toBeArray()
        ->toHaveKey('docs/{file}')
        ->toHaveCount(2)
        ->toContainOnlyInstancesOf(\Illuminate\Routing\Route::class)
    ;
});


it('should response with a html page from the directory', function () {
    MarkDownRoute::get('/docs', base_path() . "/docs");

    File::shouldReceive('files')->with(base_path() . "/docs")->andReturn(
        [
            new SplFileInfo(base_path() . "/docs/README.md", "", "README.md"),
        ]
    )->times(1);

    File::shouldReceive('exists')->with(base_path() . "/docs/README.md")->andReturn(true);
    File::shouldReceive('get')->with(base_path() . "/docs/README.md")->andReturn("# title");
    File::partialMock();

    $response = get('/docs/README.html')->assertStatus(200);
    expect($response->getContent())->toContain("<h1>title</h1>");
});

it('should response with a html page from the directory in a directory', function () {
    MarkDownRoute::get('/docs', base_path() . "/docs");

    File::shouldReceive('files')->with(base_path() . "/docs/dir1")->andReturn(
        [
            new SplFileInfo(base_path() . "/docs/README.md", "", "README.md"),
        ]
    )->times(1);
    File::shouldReceive('exists')->with(base_path() . "/docs/dir1/README.md")->andReturn("# title dir1");
    File::shouldReceive('get')->with(base_path() . "/docs/dir1/README.md")->andReturn("# title dir1");
    File::partialMock();


    $response = get('/docs/dir1/README.html')->assertStatus(200);
    expect($response->getContent())->toContain("<h1>title dir1</h1>");
});

it('should contain the right image path', function () {

    MarkDownRoute::get('/public_docs', base_path() . "/base_docs");

    File::shouldReceive('files')->with(base_path() . "/base_docs/dir1")->andReturn(
        [
            new SplFileInfo(base_path() . "/docs/dir1/README.md", "", "README.md"),
            new SplFileInfo(base_path() . "/docs/dir1/image.png", "", "image.png"),
        ]
    )->times(1);
    File::shouldReceive('exists')->with(base_path() . "/base_docs/dir1/README.md")->andReturn("![alt text](./image.png)");
    File::shouldReceive('get')->with(base_path() . "/base_docs/dir1/README.md")->andReturn("![alt text](./image.png)");
    File::shouldReceive('copy');
    File::partialMock();


    $response = get('/public_docs/dir1/README.html')->assertStatus(200);
    expect($response->getContent())->toContain('<img src="http://localhost/public_docs/dir1/image.png" alt="alt text" />');
});
