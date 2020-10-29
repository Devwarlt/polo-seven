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

$controller = $_POST["controller"];
switch ($controller) {
    case "login":
        include "controller/LoginController.php";
        include "dao/db/SQLQuery.php";
        include "dao/db/MySQLDatabase.php";
        include "dao/LoginDAO.php";
        include "model/LoginModel.php";

        if (!isset($_POST["nome"]) || !isset($_POST["senha"])) {
            $utils->onRawIndexErr("Credenciais inválidas!", $errRef);
            return;
        }

        $login = loginctrl::getSingleton();
        $loginResponse = null;
        if ($login->verificarSessaoLogin() || ($loginResponse = $login->autenticarLogin($_POST["nome"], $_POST["senha"])) === null)
            header("Location:../dashboard.php");
        else
            $utils->onRawIndexErr($loginResponse, $errRef);
        break;
    case "dashboard":
        include "controller/DashboardController.php";
        include "dao/db/SQLQuery.php";
        include "dao/db/MySQLDatabase.php";
        include "dao/PagamentoDAO.php";
        include "dao/ProdutoDAO.php";
        include "dao/ProdutoDAO.php";
        include "dao/UsuarioDAO.php";
        include "dao/VendaDAO.php";
        include "model/PagamentoModel.php";
        include "model/ProdutoModel.php";
        include "model/UsuarioModel.php";
        include "model/VendaModel.php";
        include "view/DashboardView.php";

        // TODO: implements 'dashboard' controller features.
        break;
    default:
        $utils->onRawIndexErr("Controlador não encontrado: <strong>$controller</strong>", $errRef);
        break;
}