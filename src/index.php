<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Polo Seven - Início</title>
</head>
<body>
<h1>Polo Seven - Início</h1>
<?php

include "php/controller/LoginController.php";

use php\controller\LoginController as loginctrl;
use php\PhpUtils as phputils;

session_start();

$login = loginctrl::getSingleton();

if ($login->verificarSessaoLogin()) {
    header("Location:/dashboard.php");
    return;
}

if (isset($_GET["err"])) {
    include "php/PhpUtils.php";

    $utils = phputils::getSingleton();
    $err = urldecode($_GET["err"]);
    if ($utils->checkPhpInjection($err)) {
        $utils->onRawIndexErr("Php Injection detectado!", "/index.php");
        return;
    }

    echo "
    <hr/>
    <h3 style='color: darkred'>Erro!</h3>
    <p style='color: red' align='justify'>$err</p>
    ";
}


if (isset($_GET["ok"])) {
    include "php/PhpUtils.php";

    $utils = phputils::getSingleton();
    $ok = urldecode($_GET["ok"]);
    if ($utils->checkPhpInjection($ok)) {
        $utils->onRawIndexErr("Php Injection detectado!", "/index.php");
        return;
    }

    echo "
    <hr/>
    <h3 style='color: green'>Sucesso!</h3>
    <p style='color: limegreen' align='justify'>$ok</p>
    ";
}
?>
<hr/>
<form action="/php/MVCRouter.php" method="post">
    <input type="hidden" name="controller" value="login"/>
    <h2>Login</h2>
    <br/>
    <label for="nome">Nome:</label>
    <br/>
    <input type="text" id="nome" name="nome" placeholder="Nome"/>
    <br/>
    <label for="senha">Senha:</label>
    <br/>
    <input type="password" id="senha" name="senha" placeholder="Senha"/>
    <br/><br/>
    <input type="submit" value="Entrar"/>&nbsp;
    <input type="reset" value="Resetar"/>
</form>
</body>
</html>