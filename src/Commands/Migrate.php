<?php

namespace Bugloos\LaravelLocalization\Commands;

use Bugloos\LaravelLocalization\Exceptions\MigratorFilesNotFoundException;
use Bugloos\LaravelLocalization\Facades\MigratorFacade;
use Bugloos\LaravelLocalization\Responses\MigratorResponse;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;

class Migrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'localization:migrate {path} {--lang=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate all files in given path';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            $path = $this->argument('path');

            $filter = (array)$this->option('lang');

            try {
                MigratorFacade::load($path, $filter);
            } catch (MigratorFilesNotFoundException $ex) {
                echo $ex->getMessage();
                return Command::FAILURE;
            }

            $this->info(sprintf('All files in %s translated successfully!', $path));

            return Command::SUCCESS;
        } catch (QueryException $ex) {
            $this->error($ex->getMessage());
        }
    }
}
