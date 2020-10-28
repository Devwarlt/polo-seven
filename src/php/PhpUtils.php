<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 17:52
 */

namespace php;


final class PhpUtils
{
    private static $php_injection_regex_pattern = '/^(?=.*<\?)|(?=.*\?>).*$/';

    private static $singleton;

    private function __construct()
    {
    }

    public static function getSingleton(): PhpUtils
    {
        if (self::$singleton === null)
            self::$singleton = new PhpUtils();

        return self::$singleton;
    }

    public function onRawIndexErr(string $msg, string $ref): void
    {
        $err = urlencode($msg);
        header("Location:$ref?err=$err");
    }

    public function checkPhpInjection(string $str): bool
    {
        return preg_match(self::$php_injection_regex_pattern, $str);
    }
}