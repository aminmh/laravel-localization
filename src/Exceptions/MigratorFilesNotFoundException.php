<?php

namespace Bugloos\LaravelLocalization\Exceptions;

class MigratorFilesNotFoundException extends \RuntimeException
{
    public function __construct(string $path = "", int $code = 0, ?\Throwable $previous = null)
    {
        $message = sprintf('No files found in this path: %s', $path);
        parent::__construct($message, $code, $previous);
    }
}
