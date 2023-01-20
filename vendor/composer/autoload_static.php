<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd504512e060cd47b62e91b2aab289bb7
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'Database\\Seeders\\' => 17,
            'Database\\Factories\\' => 19,
        ),
        'B' => 
        array (
            'Bugloos\\LaravelLocalization\\' => 28,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Database\\Seeders\\' => 
        array (
            0 => __DIR__ . '/..' . '/laravel/pint/database/seeders',
        ),
        'Database\\Factories\\' => 
        array (
            0 => __DIR__ . '/..' . '/laravel/pint/database/factories',
        ),
        'Bugloos\\LaravelLocalization\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/..' . '/laravel/pint/app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd504512e060cd47b62e91b2aab289bb7::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd504512e060cd47b62e91b2aab289bb7::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitd504512e060cd47b62e91b2aab289bb7::$classMap;

        }, null, ClassLoader::class);
    }
}
