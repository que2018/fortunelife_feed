<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitca40b9e695f3a26d40ad5fcda7cfd6ac
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Stripe\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Stripe\\' => 
        array (
            0 => __DIR__ . '/..' . '/stripe/stripe-php/lib',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitca40b9e695f3a26d40ad5fcda7cfd6ac::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitca40b9e695f3a26d40ad5fcda7cfd6ac::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
