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
$errRef = $okRef = "../";

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

        $login = loginctrl::getSingleton();
        if (isset($_POST["logout"])) {
            $login->terminarSessaoLogin();
            $utils->onRawIndexOk("Logout efetuado com sucesso!", $okRef);
            return;
        }

        if (!isset($_POST["nome"]) || !isset($_POST["senha"])) {
            $utils->onRawIndexErr("Credenciais inválidas!", $errRef);
            return;
        }

        $okRef = "../dashboard";
        if ($login->verificarSessaoLogin()) {
            header("Location:$okRef");
            return;
        }

        $loginResponse = $login->autenticarLogin($_POST["nome"], $_POST["senha"]);
        if ($loginResponse["err"] === null) {
            if ($loginResponse["login"] !== null)
                $login->criarSessaoLogin($loginResponse["login"]);
            header("Location:$okRef");
        } else
            $utils->onRawIndexErr($loginResponse["err"], $errRef);
        break;
    case "dashboard":
        include "controller/DashboardController.php";
        include "dao/db/SQLQuery.php";
        include "dao/db/MySQLDatabase.php";
        include "dao/PagamentoDAO.php";
        include "dao/ProdutoDAO.php";
        include "dao/UsuarioDAO.php";
        include "dao/VendaDAO.php";
        include "model/PagamentoModel.php";
        include "model/ProdutoModel.php";
        include "model/UsuarioModel.php";
        include "model/VendaModel.php";

        // TODO: implements 'dashboard' controller features.
        break;
    default:
        $utils->onRawIndexErr("Controlador não encontrado: <strong>$controller</strong>", $errRef);
        break;
}