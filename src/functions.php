<?php


function toUrl(string $filename): string
{
    if(substr($filename, -3)==='.md') {
        return substr($filename, 0, -3);
    }
    return $filename;
}

function urlPath(string $uri): string
{
    return str_replace('{file}', '', $uri);
}
