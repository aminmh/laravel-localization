<?php

namespace Bugloos\LaravelLocalization\Views\Console;

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;

class Console
{
    private readonly ConsoleOutput $output;

    public function __construct(?string $styleName = null, ?OutputFormatterStyle $style = null)
    {
        $this->output = new ConsoleOutput();

        if ($styleName && $style) {
            $this->output->getFormatter()->setStyle($styleName, $style);
        }
    }

    public function success(string|\Stringable $message): void
    {
        $this->getOutput()->writeln("<info>{$message}</info>");
    }

    public function error(string $message): void
    {
        $this->getOutput()->writeln("<error>{$message}</error>");
    }

    public function getOutput(): ConsoleOutput
    {
        return $this->output;
    }
}
