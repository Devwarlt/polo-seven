<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 02:53
 */

namespace php\controller;

final class LoginController
{
    private static $singleton;

    private function __construct()
    {
    }

    public static function getSingleton(): LoginController
    {
        if (self::$singleton === null)
            self::$singleton = new LoginController();

        return self::$singleton;
    }

    public function test(): string
    {
        return "LoginController::test()";
    }
}