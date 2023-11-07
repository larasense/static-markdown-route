<?php

namespace Larasense\StaticMarkdownRoute\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Larasense\StaticMarkdownRoute\Facades\MarkDownRoute;

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
            collect(array_keys($files))->map(fn($url) => ["{$app_url}{$url}"])->toArray()
        );

    }


}
