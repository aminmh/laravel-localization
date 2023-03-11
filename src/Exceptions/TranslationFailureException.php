<?php

namespace Bugloos\LaravelLocalization\Exceptions;

use Bugloos\LaravelLocalization\DTO\FailedTranslationDTO;

class TranslationFailureException extends \RuntimeException
{
    public function __construct(private readonly FailedTranslationDTO $failedTranslate, ?\Throwable $previous = null)
    {
        parent::__construct((string)$failedTranslate, 400, $previous);
    }
}
