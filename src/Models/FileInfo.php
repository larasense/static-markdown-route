<?php

namespace Larasense\StaticMarkdownRoute\Models;

class FileInfo
{
    public function __construct(public string $uri, public string $url, public string $directory, public string $filename)
    {}
}
