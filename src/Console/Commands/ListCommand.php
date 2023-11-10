<?php

namespace Larasense\StaticMarkdownRoute\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Larasense\StaticMarkdownRoute\Facades\MarkDownRoute;
use Larasense\StaticMarkdownRoute\Models\FileInfo;

class ListCommand extends Command
{
    protected $signature = 'static:list-markdown-routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List de Html pages for markdown routes';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        /** @var string $app_url */
        $app_url = Config::get('app.url');
        $files = MarkDownRoute::getDirFiles();

        $this->table(
            ['Markdown Pages'],
            collect($files)->map(fn (FileInfo $fileInfo) => ["{$app_url}{$fileInfo->url}"])->toArray()
        );

    }


}
