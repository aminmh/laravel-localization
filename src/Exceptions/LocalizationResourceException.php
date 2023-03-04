<?php

namespace Bugloos\LaravelLocalization\Exceptions;

use Bugloos\LaravelLocalization\Enums\ResourceExceptionMessages;
use Throwable;

class LocalizationResourceException extends \RuntimeException
{
    public function __construct(ResourceExceptionMessages|string $actionMessage, int $code = 0, ?Throwable $previous = null, ...$resources)
    {
        $message = sprintf(is_string($actionMessage) ? $actionMessage : $actionMessage->value, ...$resources);
        parent::__construct($message, $code, $previous);
    }
}
