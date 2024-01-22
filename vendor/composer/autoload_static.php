<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit9e9634ec11814282d60114c263e0dfeb
{
    public static $prefixLengthsPsr4 = array (
        'K' => 
        array (
            'Karimaouaouda\\LaravelRater\\' => 27,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Karimaouaouda\\LaravelRater\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit9e9634ec11814282d60114c263e0dfeb::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit9e9634ec11814282d60114c263e0dfeb::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit9e9634ec11814282d60114c263e0dfeb::$classMap;

        }, null, ClassLoader::class);
    }
}