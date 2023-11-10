<?php

namespace Larasense\StaticMarkdownRoute\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MarkdownService
{
    public function toHtml(string $root, string $filename): string|null
    {
        $content = File::get($filename);
        if(!$content) {
            return null;
        }
        $content = $this->replaceImages($root, $content);
        return  Str::markdown($content);
    }

    protected function replaceImages(string $root, string $content): string
    {
        /** @var string $app_url */
        $app_url = Config::get('app.url');

        $url_base = "$app_url/$root";


        $pattern = "/!\[[^\]]*\]\((?<filename>.*?)(?=\"|\))(?<optionalpart>\".*\")?\)/i";
        if(preg_match_all($pattern, $content, $matches)) {
            /** @var array<int,string> files */
            $filenames = $matches['filename'];
            collect($filenames)
                ->filter(fn (string $filename) =>Str::startsWith($filename, './'))
                ->each(function (string $filename) use (&$content, $url_base) {
                    $content = str_replace($filename, $url_base."/".Str::substr($filename, 2), $content);
                })
            ;

        }

        return $content;
    }
}
