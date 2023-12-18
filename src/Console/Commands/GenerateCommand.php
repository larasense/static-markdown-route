<?php

namespace Larasense\StaticMarkdownRoute\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Larasense\StaticMarkdownRoute\Facades\MarkDownRoute;

class GenerateCommand extends Command
{
    protected $signature = 'static:generate-markdown-routes {--F|force}';

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
        ['force'=>$force] = $this->options();
        /** @var string $app_url */
        $app_url = Config::get('app.url');
        $output = $this->output;

        if(MarkDownRoute::publicDirectoriesHaveFiles()) {
            $delete = (!$force)?
                    $this->components->ask('there are already generated files. Do you want to delete these files?[Y/n]', 'y'):
                    'y';
            if($delete !== 'y') {
                $this->info('generation Canceled.');
                return;
            }
            MarkDownRoute::deletePublicDirectories();
            $this->components->warn('files deleted');
        }


        if(app()->environment('production')) {
            $this->components->info('Activating the markdown Routes and middlewares for Production.');
            Config::set('staticmarkdownroute.force', true);
        }

        // Starting the process
        $files = MarkDownRoute::getDirFiles();
        $output->progressStart(count($files));


        foreach($files as $fileInfo) {
            Http::get("{$app_url}{$fileInfo->url}.html");
            $output->progressAdvance();
        }
        $output->progressFinish();

        if(app()->environment('production')) {
            $this->components->info('Activating the markdown Routes and middlewares for Production.');
            Config::set('staticmarkdownroute.force', false);
        }
        $this->info('HTML pages generated successfully.');
    }
}
