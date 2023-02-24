<?php

namespace Bugloos\LaravelLocalization\Commands;

use Bugloos\LaravelLocalization\Facades\MigratorFacade;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

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
    public function handle()
    {
        try {
            MigratorFacade::load($this->argument('path'));
        } catch (QueryException $ex) {
            $this->error($ex->getMessage());
        }
        return Command::SUCCESS;
    }
}
