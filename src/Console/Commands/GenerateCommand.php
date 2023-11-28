<?php

namespace Larasense\StaticMarkdownRoute\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
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

        $output->progressStart(count($files));

        foreach($files as $fileInfo) {
            $htmlPage = Http::get("{$app_url}{$fileInfo->url}")->body();
            File::put(dirname(public_path(). $fileInfo->url) . "/" . basename($fileInfo->filename), $htmlPage);
            $content = File::get($fileInfo->filename);
            $pattern = "/!\[[^\]]*\]\((?<filename>.*?)(?=\"|\))(?<optionalpart>\".*\")?\)/i";
            if(preg_match_all($pattern, $content, $matches)) {
                /** @var array<int,string> files */
                $filenames = $matches['filename'];
                collect($filenames)
                    ->filter(fn (string $filename) => Str::startsWith($filename, './'))
                    ->map(fn (string $filename) => dirname($fileInfo->filename)."/".Str::substr($filename, 2))
                    ->each(function (string $filename) use ($fileInfo) {
                        File::ensureDirectoryExists(dirname(public_path(). $fileInfo->url));
                        File::copy($filename, dirname(public_path(). $fileInfo->url) . "/" . basename($filename));
                    })
                ;

            }
            $output->progressAdvance();
        }
        $output->progressFinish();
        $this->components->info('HTML pages generated successfully.');
    }
}
