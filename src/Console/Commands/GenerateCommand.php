<?php

namespace Larasense\StaticMarkdownRoute\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Larasense\StaticMarkdownRoute\Facades\MarkDownRoute;

class GenerateCommand extends Command
{
    protected $signature = 'static:generate-markdown-routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate de Html pages for markdown routes';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        /** @var string $app_url */
        $app_url = Config::get('app.url');
        $files = MarkDownRoute::getDirFiles();
        $output = $this->output;

        if(app()->environment('production')) {
            $this->components->info('Activating the markdown Routes and middlewares for Production.');
            Config::set('staticmarkdownroute.force', true)
        }

        $output->progressStart(count($files));


        foreach($files as $fileInfo) {
            Http::get("{$app_url}{$fileInfo->url}.html");
            $output->progressAdvance();
        }
        $output->progressFinish();
        $this->info('HTML pages generated successfully.');
    }
}
