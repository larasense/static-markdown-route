<?php

namespace Larasense\StaticMarkdownRoute\Console\Commands;
use Illuminate\Console\Command;
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
        $files = MarkDownRoute::getDirFiles();
        $output = $this->output;

        $output->progressStart(count($files));

        foreach($files as $url => $file){
            Http::get("http://localhost/docs/README");
            // dd("http://localhost$url");
            $output->progressAdvance();
        }
        $output->progressFinish();
        $this->components->info('HTML pages generated successfully.');
    }
}
