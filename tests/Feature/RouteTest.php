<?php

use Larasense\StaticMarkdownRoute\Facades\MarkDownRoute;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Facades\File;
use function Pest\Laravel\{get};

it('should add the `dir_info` to the Facade', function(){
    MarkDownRoute::get('/docs', base_path() . "/docs");

    expect(MarkDownRoute::getDirInfo('docs/{file}'))
        ->toBe(base_path() . "/docs")
    ;
});

it('should register the route as a `Illuminate\Routing\Route`', function(){
    MarkDownRoute::get('/docs', base_path() . "/docs");

    expect(RouteFacade::getRoutes()->getRoutesByMethod()['GET'])
        ->toBeArray()
        ->toHaveKey('docs/{file}')
        ->toHaveCount(2)
        ->toContainOnlyInstancesOf(\Illuminate\Routing\Route::class)
    ;
});


it('should response with a html page from the directory', function(){
    MarkDownRoute::get('/docs', base_path() . "/docs");

    File::shouldReceive('get')->with(base_path() . "/docs/README.md")->andReturn("# title");
    File::makePartial();


    $response = get('/docs/README')->assertStatus(200);
    expect($response->getContent())->toContain("<h1>title</h1>");
});

it('should response with a html page from the directory in a directory', function(){
    MarkDownRoute::get('/docs', base_path() . "/docs");

    File::shouldReceive('get')->with(base_path() . "/docs/dir1/README.md")->andReturn("# title dir1");
    File::makePartial();


    $response = get('/docs/dir1/README')->assertStatus(200);
    expect($response->getContent())->toContain("<h1>title dir1</h1>");
});
