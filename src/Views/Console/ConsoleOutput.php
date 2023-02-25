<?php

namespace Bugloos\LaravelLocalization\Views\Console;

class ConsoleOutput
{
    public const CONSOLE_FORMAT_BOLD = 1;
    public const CONSOLE_FORMAT_DIM = 2;
    public const CONSOLE_FORMAT_UNDERLINE = 4;
    public const CONSOLE_FORMAT_BLINK = 5;
    public const CONSOLE_FORMAT_REVERSE = 7;
    public const CONSOLE_FORMAT_HIDDEN = 8;

    public const CONSOLE_TEXT_BLACK = '0;30';
    public const CONSOLE_TEXT_BLUE = '0;34';
    public const CONSOLE_TEXT_GREEN = '0;32';
    public const CONSOLE_TEXT_CYAN = '0;36';
    public const CONSOLE_TEXT_RED = '0;31';
    public const CONSOLE_TEXT_PURPLE = '0;35';
    public const CONSOLE_TEXT_BROWN = '0;33';
    public const CONSOLE_TEXT_LIGHT_GRAY = '0;37';
    public const CONSOLE_TEXT_NORMAL = '0;39';
    public const CONSOLE_TEXT_DARK_GRAY = '1;30';
    public const CONSOLE_TEXT_LIGHT_BLUE = '1;34';
    public const CONSOLE_TEXT_LIGHT_GREEN = '1;32';
    public const CONSOLE_TEXT_LIGHT_CYAN = '1;36';
    public const CONSOLE_TEXT_LIGHT_RED = '1;31';
    public const CONSOLE_TEXT_LIGHT_PURPLE = '1;35';
    public const CONSOLE_TEXT_YELLOW = '1;33';
    public const CONSOLE_TEXT_WHITE = '1;37';


    public static string $EOF = "\n";

    public static function write(string $str, string $color = self::CONSOLE_TEXT_NORMAL, array $options = []): string
    {
        $textColor = "\033[" . $color . "m";

        foreach ($options as $option) {
            $textColor .= "\033[" . $option . "m";
        }

        $textColor .= $str . "\033[0m";

        return $textColor;
    }

    public static function writeLn(string $str, string $color = self::CONSOLE_TEXT_NORMAL, array $options = []): string
    {
        return static::write($str . static::$EOF, $color, $options);
    }
}
