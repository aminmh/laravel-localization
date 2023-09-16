<?php

namespace Bugloos\LaravelLocalization\Commands;

use Bugloos\LaravelLocalization\Extractor\Extractor;
use Bugloos\LaravelLocalization\Factory\ExtractorFactory;
use Bugloos\LaravelLocalization\Factory\Model\ExtractorInitializerModel;
use Illuminate\Console\Command;

class Extract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "localization:extract {locale} {format} {--path=} {--category=}";

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

            $path = $this->option('path');

            $category = $this->option('category');

            $extractor = ExtractorFactory::createByModel(new ExtractorInitializerModel($locale, $format));

            $extractor->setCategory($category ?? '*');

            Extractor::extract($extractor, $path);

            return Command::SUCCESS;
        } catch (\Exception $ex) {
            $this->error($ex->getMessage());
            return Command::FAILURE;
        }
    }
}
