<?php

namespace Bugloos\LaravelLocalization\Commands;

use Illuminate\Console\Command;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Migrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'localization:migrate';

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
            $this->getLaravel()->get('migrator');
        } catch (NotFoundExceptionInterface $e) {
        } catch (ContainerExceptionInterface $e) {
        }
        return Command::SUCCESS;
    }
}
