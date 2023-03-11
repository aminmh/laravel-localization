<?php

namespace Bugloos\LaravelLocalization\Commands;

use Bugloos\LaravelLocalization\Extractor\Extractor;
use Illuminate\Console\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;

class Extract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "localization:extract {locale} {format} {--lazy=} {--path=} {--category=}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract all labels in each or given category with translations in selected locale';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            $locale = $this->argument('locale');

            $format = $this->argument('format');

            $extractors = config('localization.extract.extractors');

            if (!array_key_exists($format, $extractors)) {
                throw new InvalidOptionException(sprintf('The %s not be configured in localization\'s extractors config!', $format));
            }

            $path = $this->option('path');

            $category = $this->option('category');

            $extractor = (new $extractors[$format]($locale, $category ?? '*'));

            if ($this->option('lazy')) {
                Extractor::lazyExtract($extractor, $path);
                return Command::SUCCESS;
            }

            Extractor::extract($extractor, $path);

            return Command::SUCCESS;
        } catch (\Exception $ex) {
            $this->error($ex->getMessage());
            return Command::FAILURE;
        }
    }
}
