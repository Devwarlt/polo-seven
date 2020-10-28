<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 03:15
 */

include "PhpUtils.php";

use php\controller\LoginController as loginctrl;
use php\PhpUtils as phputils;

$utils = phputils::getSingleton();
$errRef = "../index.php";

if (count($_POST) === 0 || !isset($_POST["controller"])) {
    $utils->onRawIndexErr("Requisição inválida!", $errRef);
    return;
}

include "controller/LoginController.php";

$controller = $_POST["controller"];
switch ($controller) {
    case "login":
        if (!isset($_POST["nome"]) || !isset($_POST["senha"])) {
            $utils->onRawIndexErr("Credenciais inválidas!", $errRef);
            return;
        }

        $login = loginctrl::getSingleton();
        if (!$login->validarLogin($_POST["nome"], $_POST["senha"]))
            $utils->onRawIndexErr("Credenciais não autenticadas.", $errRef);
        else
            header("Location:../dashboard.php");
        break;
    case "dashboard":
        break;
    default:
        die("Unable to connect to controller: <strong>$controller</strong>");
        break;
}