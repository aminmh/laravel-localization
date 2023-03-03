<?php

namespace Bugloos\LaravelLocalization\Abstract;

abstract class AbstractMigratorResponse implements \Stringable
{
    protected bool $statusOk;

    /**
     * @return bool
     */
    public function isStatusOk(): bool
    {
        return $this->statusOk;
    }

    /**
     * @param bool $statusOk
     */
    public function setStatusOk(bool $statusOk): void
    {
        $this->statusOk = $statusOk;
    }
}
