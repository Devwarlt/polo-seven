<?php
include "php/PhpUtils.php";
include "php/controller/LoginController.php";
include "php/dao/db/SQLQuery.php";
include "php/dao/db/MySQLDatabase.php";
include "php/dao/LoginDAO.php";
include "php/dao/PagamentoDAO.php";
include "php/dao/ProdutoDAO.php";
include "php/dao/UsuarioDAO.php";
include "php/dao/VendaDAO.php";
include "php/model/LoginModel.php";
include "php/model/PagamentoModel.php";
include "php/model/ProdutoModel.php";
include "php/model/UsuarioModel.php";
include "php/model/VendaModel.php";
include "php/view/DashboardView.php";

use php\controller\LoginController;
use php\dao\UsuarioDAO;
use php\dao\PagamentoDAO;
use php\dao\ProdutoDAO;
use php\model\UsuarioModel;
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
<?php
$view->adicionarElementosHTML();
if ($user->getNivel() === UsuarioModel::GERENTE || $user->getNivel() === UsuarioModel::VENDEDOR) {
    echo '
<div class="container">
    <div class="row">
        <div class="col border-secondary">
            <h3 class="card-header">Painel do Vendedor</h3>
            <br/>
            <div class="container">
                <div class="card card-body row border-secondary">
                    <div class="col-md-12 text-center">
                        <h4>Ações para Produto:</h4>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-success" data-toggle="collapse"
                                    data-target="#criar-produto" aria-expanded="false" aria-controls="criar-produto">
                                <strong>Criar</strong></button>
                            <button type="button" class="btn btn-sm btn-outline-info" data-toggle="collapse"
                                    data-target="#consultar-produto" aria-expanded="false" aria-controls="consultar-produto">
                                <strong>Consultar</strong></button>
                        </div>
                    </div>
                </div>
            </div>
            <br/>
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="collapse" id="criar-produto">
                            <div class="container alert alert-success border-success">
                                <button type="button" class="close" data-toggle="collapse" data-target="#criar-produto"
                                        aria-expanded="false" aria-controls="criar-produto">&times;
                                </button>
                                <h4>Criar Produto</h4>
                                <hr/>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <input type="text" class="form-control" id="criar-produto-nome" name="nome"
                                               placeholder="Nome"/>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <input type="number" class="form-control" id="criar-produto-preco-unitario" name="senha"
                                               placeholder="Preço Unitário" min="0" step="0.01"/>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <input type="number" class="form-control" id="criar-produto-total-unidades" name="senha"
                                               placeholder="Total Unidades" min="1" step="1"/>
                                    </div>
                                </div>
                                <button id="criar-produto-btn" type="button" class="btn btn-outline-success"
                                        onclick="criarProduto()">
                                    <strong>Criar</strong></button>
                                <hr/>
                                <div id="criar-produto-result"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="collapse" id="consultar-produto">
                            <div class="container alert alert-info border-info">
                                <button type="button" class="close" data-toggle="collapse" data-target="#consultar-produto"
                                        aria-expanded="false" aria-controls="consultar-produto">&times;
                                </button>
                                <h4>Consultar Produto</h4>
                                <hr/>
                                <div class="col-md-12 text-center">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-info" data-toggle="collapse"
                                                data-target="#consultar-produto-id" aria-expanded="false"
                                                aria-controls="consultar-produto-id">
                                            <strong>Por ID</strong></button>
                                        <button type="button" class="btn btn-sm btn-outline-info" data-toggle="collapse"
                                                data-target="#consultar-produto-todos" aria-expanded="false"
                                                aria-controls="consultar-produto-todos">
                                            <strong>Ver Todos</strong>
                                        </button>
                                    </div>
                                </div>
                                <br/>
                                <div class="row">
                                    <div class="col">
                                        <div class="collapse" id="consultar-produto-id">
                                            <div class="container alert alert-light border-info">
                                                <button type="button" class="close" data-toggle="collapse"
                                                        data-target="#consultar-produto-id" aria-expanded="false"
                                                        aria-controls="consultar-produto-id">&times;
                                                </button>
                                                <h4>Por ID</h4>
                                                <hr/>
                                                <div class="form-row form-inline">
                                                    <div class="form-group">
                                                        <input type="number" class="form-control" id="consultar-produto-index"
                                                               name="index"
                                                               placeholder="Índice" min="1" step="1"/>
                                                    </div>
                                                    &nbsp;
                                                    <button id="consultar-produto-id-btn" type="button"
                                                            class="btn btn-outline-primary" onclick="consultarProdutoId()"><strong>Consultar</strong>
                                                    </button>
                                                </div>
                                                <hr/>
                                                <div id="consultar-produto-id-result"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="collapse" id="consultar-produto-todos">
                                            <div class="container alert alert-light  border-info">
                                                <button type="button" class="close" data-toggle="collapse"
                                                        data-target="#consultar-produto-todos" aria-expanded="false"
                                                        aria-controls="consultar-produto-todos">&times;
                                                </button>
                                                <h4>Ver Todos</h4>
                                                <hr/>
                                                <div class="form-row">
                                                    <button id="consultar-produto-todos-btn" type="button"
                                                            class="btn btn-outline-primary" onclick="consultarProdutoTodos()">
                                                        <strong>Consultar</strong></button>
                                                </div>
                                                <hr/>
                                                <div id="consultar-produto-todos-result"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br/>
            <div class="container">
                <div class="card card-body row border-secondary">
                    <div class="col-md-12 text-center">
                        <h4>Ações para Venda:</h4>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-success" data-toggle="collapse"
                                    data-target="#criar-venda" aria-expanded="false" aria-controls="criar-venda">
                                <strong>Criar</strong></button>
                            <button type="button" class="btn btn-sm btn-outline-info" data-toggle="collapse"
                                    data-target="#consultar-venda" aria-expanded="false" aria-controls="consultar-venda">
                                <strong>Consultar</strong></button>
                        </div>
                    </div>
                </div>
            </div>
            <br/>
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="collapse" id="criar-venda">
                            <div class="container alert alert-success border-success">
                                <button type="button" class="close" data-toggle="collapse" data-target="#criar-venda"
                                        aria-expanded="false" aria-controls="criar-venda">&times;
                                </button>
                                <h4>Criar Venda</h4>
                                <hr/>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <select class="form-control" id="criar-venda-id-usuario">';

    $options = array();
    $usuarioDao = UsuarioDAO::getSingleton();
    $gerentes = $usuarioDao->consultarUsuariosPorNivel(UsuarioModel::GERENTE);
    foreach ($gerentes as $gerente) {
        $selected = $_SESSION[LOGIN_ID] === $gerente["login"]->getNome();
        array_push($options, "<option value='" . $gerente["usr"]->getId() . "'" . ($selected ? " selected" : "") . ">" . $gerente["login"]->getNome() . "</option>");
    }

    $vendedores = $usuarioDao->consultarUsuariosPorNivel(UsuarioModel::VENDEDOR);
    foreach ($vendedores as $vendedor) {
        $selected = $_SESSION[LOGIN_ID] === $vendedor["login"]->getNome();
        array_push($options, "<option value='" . $vendedor["usr"]->getId() . "'" . ($selected ? " selected" : "") . ">" . $vendedor["login"]->getNome() . "</option>");
    }

    if (sizeof($options) > 0)
        foreach ($options as $option)
            echo $option;
    else
        echo "<option value='-1' disabled>Nenhum usuário!</option>";

    echo '
                </select>
            </div>
            <div class="form-group col-md-6">
                <select class="form-control" id="criar-venda-id-pagamento">';

    $options = array();
    $pagamentoDao = PagamentoDAO::getSingleton();
    $pagamentos = $pagamentoDao->consultarPagamentos();
    foreach ($pagamentos as $pagamento)
        array_push($options, "<option value='" . $pagamento->getId() . "'>" . $pagamento->getNome() . "</option>");

    if (sizeof($options) > 0)
        foreach ($options as $option)
            echo $option;
    else
        echo "<option value='-1' disabled>Nenhum pagamento!</option>";

    echo '
                </select>
            </div>
            <div class="form-group col-md-6">
                <select multiple class="form-control" id="criar-venda-id-produtos-select">';

    $options = array();
    $produtoDao = ProdutoDAO::getSingleton();
    $produtos = $produtoDao->consultarProdutos();
    foreach ($produtos as $produto)
        array_push($options, "<option  value='" . $produto->getId() . "'>" . $produto->getNome() . "</option>");

    if (sizeof($options) > 0)
        foreach ($options as $option)
            echo $option;
    else
        echo "<option value='-1' disabled>Nenhum produto!</option>";

    echo '
                </select>
            </div>
            <div class="form-group col-md-6">
                <button type="button" id="criar-venda-id-produtos-btn" class="btn btn-outline-success"
                        onclick="adicionarItemProduto()">
                    Adicionar
                </button>
            </div>
            <div class="card-columns col-md-12" id="criar-venda-id-produtos-container">
            </div>
            </div>
            <button id="criar-venda-btn" type="button" class="btn btn-outline-success"
                    onclick="criarVenda()">
                <strong>Criar</strong></button>
            <hr/>
            <div id="criar-venda-result"></div>
            </div>
            </div>
            </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="collapse" id="consultar-venda">
                        <div class="container alert alert-info border-info">
                            <button type="button" class="close" data-toggle="collapse" data-target="#consultar-venda"
                                    aria-expanded="false" aria-controls="consultar-venda">&times;
                            </button>
                            <h4>Consultar Venda</h4>
                            <hr/>
                            <div class="col-md-12 text-center">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-info" data-toggle="collapse"
                                            data-target="#consultar-venda-id" aria-expanded="false"
                                            aria-controls="consultar-venda-id">
                                        <strong>Por ID</strong></button>
                                    <button type="button" class="btn btn-sm btn-outline-info" data-toggle="collapse"
                                            data-target="#consultar-venda-todos" aria-expanded="false"
                                            aria-controls="consultar-venda-todos">
                                        <strong>Ver Todos</strong>
                                    </button>
                                </div>
                            </div>
                            <br/>
                            <div class="row">
                                <div class="col">
                                    <div class="collapse" id="consultar-venda-id">
                                        <div class="container alert alert-light border-info">
                                            <button type="button" class="close" data-toggle="collapse"
                                                    data-target="#consultar-venda-id" aria-expanded="false"
                                                    aria-controls="consultar-venda-id">&times;
                                            </button>
                                            <h4>Por ID</h4>
                                            <hr/>
                                            <div class="form-row form-inline">
                                                <div class="form-group">
                                                    <input type="number" class="form-control" id="consultar-venda-index"
                                                           name="index"
                                                           placeholder="Índice" min="1" step="1"/>
                                                </div>
                                                &nbsp;
                                                <button id="consultar-venda-id-btn" type="button"
                                                        class="btn btn-outline-primary" onclick="consultarVendaId()"><strong>Consultar</strong>
                                                </button>
                                            </div>
                                            <hr/>
                                            <div id="consultar-venda-id-result"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="collapse" id="consultar-venda-todos">
                                        <div class="container alert alert-light  border-info">
                                            <button type="button" class="close" data-toggle="collapse"
                                                    data-target="#consultar-venda-todos" aria-expanded="false"
                                                    aria-controls="consultar-venda-todos">&times;
                                            </button>
                                            <h4>Ver Todos</h4>
                                            <hr/>
                                            <div class="form-row">
                                                <button id="consultar-venda-todos-btn" type="button"
                                                        class="btn btn-outline-primary" onclick="consultarVendaTodos()">
                                                    <strong>Consultar</strong></button>
                                            </div>
                                            <hr/>
                                            <div id="consultar-venda-todos-result"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';
}
?>
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