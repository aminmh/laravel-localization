<?php

namespace Bugloos\LaravelLocalization\Commands;

use Bugloos\LaravelLocalization\Facades\MigratorFacade;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;

class Migrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'localization:migrate {path} {--lazy}';

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

            if ($this->option('lazy') || false) {
                return $this->lazyHandle($path);
            }

            MigratorFacade::load($path);

            return Command::SUCCESS;
        } catch (QueryException $ex) {
            $this->error($ex->getMessage());
        }
    }

    private function lazyHandle(string $path): int
    {
        foreach (MigratorFacade::lazyLoad($path) as $loaded) {
            echo $loaded;
        }

        return Command::SUCCESS;
    }
}
