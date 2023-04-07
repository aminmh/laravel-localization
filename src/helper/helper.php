<?php

if (!function_exists('translate')) {
    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    function translate(string $key, array $replace = [], ?string $locale = null): string
    {
        return (app()->get('localization'))->get($key, $replace, $locale);
    }
}
