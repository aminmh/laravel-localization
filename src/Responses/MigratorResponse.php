<?php

namespace Bugloos\LaravelLocalization\Responses;

use Bugloos\LaravelLocalization\DTO\TranslatedDTO;

class MigratorResponse implements \Stringable
{
    protected ?TranslatedDTO $translatedResource = null;

    public function __construct(
        protected readonly bool    $statusOk,
        protected readonly ?string $message = null
    ) {
    }

    public function __toString(): string
    {
        return $this->message ?? sprintf(
            "Label <options=bold,underscore>%s</> from category <options=bold,underscore>%s</> translate to <options=bold,underscore>%s</> with <options=bold,underscore>%s</> language.",
            $this->getTranslatedResource()?->getLabel()->key,
            $this->getTranslatedResource()?->getCategory()->name,
            $this->getTranslatedResource()?->translate->text,
            $this->getTranslatedResource()?->getLocale()->locale
        );
    }

    /**
     * @return bool
     */
    public function isStatusOk(): bool
    {
        return $this->statusOk;
    }

    public function getTranslatedResource(): ?TranslatedDTO
    {
        return $this->translatedResource;
    }

    public function setTranslatedResource(TranslatedDTO $translatedResource): void
    {
        $this->translatedResource = $translatedResource;
    }
}
