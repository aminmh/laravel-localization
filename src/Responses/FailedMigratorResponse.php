<?php

namespace Bugloos\LaravelLocalization\Responses;

use Bugloos\LaravelLocalization\Abstract\AbstractMigratorResponse;

class FailedMigratorResponse extends AbstractMigratorResponse
{
    public function __construct(
        public readonly string $label,
        public readonly string $category,
        public readonly string $translate,
        public readonly string $locale,
    )
    {
        $this->setStatusOk(false);
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return "";
    }
}
