<?php

namespace Bugloos\LaravelLocalization\Commands;

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
    protected $signature = 'localization:migrate {path}';

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

            MigratorFacade::load($path);

            return Command::SUCCESS;
        } catch (QueryException $ex) {
            $this->error($ex->getMessage());
        }
    }
}
