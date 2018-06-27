<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3d8c65a0572235b8afb6cef83b4a1b22
{
    public static $files = array (
        'ce89ac35a6c330c55f4710717db9ff78' => __DIR__ . '/..' . '/kriswallsmith/assetic/src/functions.php',
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        '7d8c19660fc7bda7e3f1bb627c20c455' => __DIR__ . '/..' . '/yooper/stop-words/src/StopWordFactory.php',
        'fc73bab8d04e21bcdda37ca319c63800' => __DIR__ . '/..' . '/mikecao/flight/flight/autoload.php',
        '5b7d984aab5ae919d3362ad9588977eb' => __DIR__ . '/..' . '/mikecao/flight/flight/Flight.php',
        '82b15671fa4352bd2c1ea8902d4c0c5d' => __DIR__ . '/..' . '/yooper/php-text-analysis/src/helpers/storage.php',
        'c2fe535f6d51f069823351f60bd6b280' => __DIR__ . '/..' . '/yooper/php-text-analysis/src/helpers/print.php',
        '34faac671c44560451a381662d8b697c' => __DIR__ . '/..' . '/yooper/php-text-analysis/src/helpers/simplified.php',
        '97c3b78656a7c2fa22581078400c5264' => __DIR__ . '/..' . '/yooper/php-text-analysis/src/helpers/helpers.php',
    );

    public static $prefixLengthsPsr4 = array (
        'Y' => 
        array (
            'Yooper\\' => 7,
        ),
        'W' => 
        array (
            'Wamania\\Snowball\\' => 17,
        ),
        'T' => 
        array (
            'TextAnalysis\\' => 13,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Component\\Process\\' => 26,
            'Symfony\\Component\\Console\\' => 26,
        ),
        'R' => 
        array (
            'RedBeanPHP\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Yooper\\' => 
        array (
            0 => __DIR__ . '/..' . '/yooper/nicknames/src',
        ),
        'Wamania\\Snowball\\' => 
        array (
            0 => __DIR__ . '/..' . '/wamania/php-stemmer/src',
        ),
        'TextAnalysis\\' => 
        array (
            0 => __DIR__ . '/..' . '/yooper/php-text-analysis/src',
        ),
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Component\\Process\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/process',
        ),
        'Symfony\\Component\\Console\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/console',
        ),
        'RedBeanPHP\\' => 
        array (
            0 => __DIR__ . '/..' . '/gabordemooij/redbean/RedBeanPHP',
        ),
    );

    public static $prefixesPsr0 = array (
        'S' => 
        array (
            'SimplePie' => 
            array (
                0 => __DIR__ . '/..' . '/simplepie/simplepie/library',
            ),
        ),
        'L' => 
        array (
            'Less' => 
            array (
                0 => __DIR__ . '/..' . '/oyejorge/less.php/lib',
            ),
        ),
        'C' => 
        array (
            'ComponentInstaller' => 
            array (
                0 => __DIR__ . '/..' . '/robloach/component-installer/src',
            ),
        ),
        'A' => 
        array (
            'Assetic' => 
            array (
                0 => __DIR__ . '/..' . '/kriswallsmith/assetic/src',
            ),
        ),
    );

    public static $classMap = array (
        'Porter' => __DIR__ . '/..' . '/camspiers/porter-stemmer/src/Porter.php',
        'lessc' => __DIR__ . '/..' . '/oyejorge/less.php/lessc.inc.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3d8c65a0572235b8afb6cef83b4a1b22::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3d8c65a0572235b8afb6cef83b4a1b22::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit3d8c65a0572235b8afb6cef83b4a1b22::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit3d8c65a0572235b8afb6cef83b4a1b22::$classMap;

        }, null, ClassLoader::class);
    }
}
