<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita1abcbfcef8e93abbe6fca74479ff97f
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPClassMapGenerator\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPClassMapGenerator\\' => 
        array (
            0 => __DIR__ . '/..' . '/michaeluno/php-classmap-generator/source',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita1abcbfcef8e93abbe6fca74479ff97f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita1abcbfcef8e93abbe6fca74479ff97f::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
