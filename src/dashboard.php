<?php
include "php/PhpUtils.php";
include "php/controller/LoginController.php";
include "php/dao/db/SQLQuery.php";
include "php/dao/db/MySQLDatabase.php";
include "php/dao/PagamentoDAO.php";
include "php/dao/ProdutoDAO.php";
include "php/dao/UsuarioDAO.php";
include "php/dao/VendaDAO.php";
include "php/model/PagamentoModel.php";
include "php/model/ProdutoModel.php";
include "php/model/UsuarioModel.php";
include "php/model/VendaModel.php";
include "php/view/DashboardView.php";

use php\controller\LoginController;
use php\dao\UsuarioDAO;
use php\PhpUtils;

session_start();

$utils = PhpUtils::getSingleton();
$login = LoginController::getSingleton();
if (!$login->verificarSessao()) {
    $utils->onRawIndexErr("É necessário realizar login!", "/");
    return;
}

$userDao = UsuarioDAO::getSingleton();
$user = $userDao->consultarUsuario($_SESSION[LOGIN_ID]);
if ($user === null) {
    $utils->onRawIndexErr("Usuário não encontrado!", "/");
    return;
}

$view = new \php\view\DashboardView($user);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Polo Seven - Dashboard</title>
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="css/overlay.css?t=<?php echo time(); ?>"/>
</head>
<body style="height: 100%;" class="alert-light">
<table class="table" border="0" width="100%">
    <tr>
        <td width="75%">
            <p class="card-header text-secondary">
                Seja bem-vindo(a), <strong><?php echo $_SESSION[LOGIN_NAME] ?></strong>!
            </p>
        </td>
        <td align="right">
            <form action="php/MVCRouter.php" method="post">
                <input type="hidden" name="controller" value="login"/>
                <input type="hidden" name="logout"/>
                <input class="font-weight-bold btn btn-lg btn-outline-danger" type="submit" value="Sair"/>
            </form>
        </td>
    </tr>
</table>
<?php $view->adicionarElementosHTML(); ?>
<div id="loading" class="overlay-loading text-center">
    <img src="/media/loading.gif" width="32" height="32"/>
    &nbsp;
    <strong class="no-select">Carregando, por favor aguarde...</strong>
</div>
</body>
<script type="text/javascript" src="js/jquery-3.5.1.js"></script>
<script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
<?php $view->adicionarScriptsJS(); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $("#loading").height("0%");
    });
</script>
</html>