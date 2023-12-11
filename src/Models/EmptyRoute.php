<?php

namespace Larasense\StaticMarkdownRoute\Models;

class EmptyRoute
{
    public function __call(string $method, mixed $params): self
    {
        return $this;
    }
}
