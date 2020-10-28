<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 00:33
 */

namespace php\controller;

final class DashboardController
{
    private static $singleton;

    private function __construct()
    {
    }

    public static function getSingleton(): DashboardController
    {
        if (self::$singleton === null)
            self::$singleton = new DashboardController();
        return self::$singleton;
    }
}