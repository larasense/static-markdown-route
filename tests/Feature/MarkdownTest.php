<?php

namespace Larasense\StaticMarkdownRoute\Tests\Feature;

use Illuminate\Support\Facades\File;
use Larasense\StaticMarkdownRoute\Facades\MarkDown;
use Larasense\StaticMarkdownRoute\Facades\MarkDownRoute;

$strMarkdown = <<<EOD
# Title

![alt text](./image.png)

  ![alt text2](./image2.png)

in the middle of a text ![](./image3.png) or something

EOD;

it('should generate html from filename', function () use ($strMarkdown) {
    File::shouldReceive('exists')->with('filename.md')->andReturn($strMarkdown);
    File::shouldReceive('get')->with('filename.md')->andReturn($strMarkdown);

    expect(
        MarkDown::toHtml('docs', 'filename.md')
    )->toContain('<h1>Title</h1>');
});

it('should update images link to destination URL', function () use ($strMarkdown) {

    MarkDownRoute::get('/public_docs', base_path() . "/base_docs");
    File::shouldReceive('exists')->with(base_path() . '/base_docs/filename.md')->andReturn($strMarkdown);
    File::shouldReceive('get')->with(base_path() . '/base_docs/filename.md')->andReturn($strMarkdown);

    expect(
        MarkDown::toHtml('docs', base_path() . "/base_docs/filename.md")
    )->toContain(
        '<img src="http://localhost/docs/image.png" alt="alt text" />',
        '<img src="http://localhost/docs/image2.png" alt="alt text2" />',
        '<img src="http://localhost/docs/image3.png" alt="" />'
    );

});

it('should update images link to destination URL for sub directories', function () use ($strMarkdown) {

    MarkDownRoute::get('/public_docs', base_path() . "/base_docs");
    File::shouldReceive('exists')->with(base_path(). '/base_docs/dir1/filename.md')->andReturn($strMarkdown);
    File::shouldReceive('get')->with(base_path(). '/base_docs/dir1/filename.md')->andReturn($strMarkdown);

    expect(
        MarkDown::toHtml('public_docs/dir1', base_path() . "/base_docs/dir1/filename.md")
    )->toContain(
        '<img src="http://localhost/public_docs/dir1/image.png" alt="alt text" />',
        '<img src="http://localhost/public_docs/dir1/image2.png" alt="alt text2" />',
        '<img src="http://localhost/public_docs/dir1/image3.png" alt="" />'
    );

});

