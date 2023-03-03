<?php

namespace Bugloos\LaravelLocalization\Exceptions;

use Bugloos\LaravelLocalization\DTO\FailedTranslationDTO;

class TranslationFailureException extends \RuntimeException
{
    public function __construct(private readonly FailedTranslationDTO $failedTranslate, ?\Throwable $previous = null)
    {
        parent::__construct((string)$failedTranslate, 400, $previous);
    }

    public function resourceToArray(): array
    {
        return [
            'label' => $this->failedTranslate->label,
            'category' => $this->failedTranslate->category,
            'translate' => $this->failedTranslate->translate,
            'locale' => $this->failedTranslate->locale,
        ];
    }
}
