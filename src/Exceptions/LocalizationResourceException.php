<?php

namespace Bugloos\LaravelLocalization\Exceptions;

use Bugloos\LaravelLocalization\Enums\ResourceExceptionMessages;
use Illuminate\Database\QueryException;
use Throwable;

class LocalizationResourceException extends \RuntimeException
{
    public function __construct(ResourceExceptionMessages $actionMessage, int $code = 0, ?Throwable $previous = null, ...$resources)
    {
        $message = sprintf($actionMessage->value, ...$resources);
        parent::__construct($message, $code, $previous);
    }
}
