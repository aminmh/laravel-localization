<?php

namespace Bugloos\LaravelLocalization\Migrator\Writers;

use Bugloos\LaravelLocalization\Abstract\AbstractWriter;
use Illuminate\Database\QueryException;

class PhpWriter extends AbstractWriter
{
    public function save(): bool
    {
        $categoryObject = static::$translator->addCategory($this->reader->getCategory());

        $locale = $this->reader->getLocale();

        try {
            foreach ($this->reader->getContent() as $label => $translate) {
                $labelObject = static::$translator->addLabel($label, $categoryObject);

                static::$translator->translate($labelObject, $translate, $locale);
            }
            return true;
        } catch (QueryException $ex) {
            return false;
        }
    }
}
