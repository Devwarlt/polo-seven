<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 03:15
 */

if (count($_POST) === 0 || !isset($_POST["target"])) {
    header("Location:../index.php");
    return;
}

include "AutoLoader.php";

use php\AutoLoader as al;

al::register();

$target = $_POST["target"];
switch ($target) {
    case "login":
        break;
    case "dashboard":
        break;
    default:
        die("Unable to connect to target: <strong>$target</strong>");
        break;
}