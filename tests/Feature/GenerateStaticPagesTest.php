<?php

use Larasense\StaticMarkdownRoute\Facades\MarkDownRoute;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;
use function Pest\Laravel\{artisan, get};

it('should list pages to generate');

it('should generate pages', function () {
    MarkDownRoute::get('/docs', base_path() . "/docs");

    File::shouldReceive('get')->with(base_path() . "/docs/README.md")->andReturn("# title")->times(1);
    File::shouldReceive('get')->with(base_path() . "/docs/dir1/README.md")->andReturn("# title dir1");
    File::shouldReceive('allFiles')->with(base_path() . "/docs")->andReturn([new SplFileInfo(base_path() . "/docs/README.md", "", "README.md")])->times(1);
    File::makePartial();

    artisan('static:generate-markdown-routes')->assertSuccessful();
})->only();

it('should update images link to destination URL');

it('should copy images into destination URL');

it('should generate content menu');
