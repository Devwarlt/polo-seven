<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 01:00
 */

namespace php;

final class AutoLoader
{
    public static function register()
    {
        spl_autoload_register(function ($class) {
            $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

            if (!file_exists($file))
                return false;

            require($file);
            return true;
        });
    }
}