<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 03:15
 */

include "PhpUtils.php";

use php\controller\DashboardController;
use php\model\UsuarioModel;
use php\model\LoginModel;
use php\model\ProdutoModel;
use php\model\PagamentoModel;
use php\model\VendaModel;
use php\controller\LoginController;
use php\dao\UsuarioDAO;
use php\dao\LoginDAO;
use php\dao\ProdutoDAO;
use php\dao\PagamentoDAO;
use php\PhpUtils;

$utils = PhpUtils::getSingleton();
$errRef = $okRef = "../";

if (count($_POST) === 0 || !isset($_POST["controller"])) {
    $utils->onRawIndexErr("Requisição inválida!", $errRef);
    return;
}

$controller = $_POST["controller"];
switch ($controller) {
    default:
        $utils->onRawIndexErr("Controlador não encontrado: <strong>$controller</strong>", $errRef);
        break;
    case "login":
        {

            include "controller/LoginController.php";
            include "dao/db/SQLQuery.php";
            include "dao/db/MySQLDatabase.php";
            include "dao/LoginDAO.php";
            include "model/LoginModel.php";

            $login = LoginController::getSingleton();
            if (isset($_POST["logout"])) {
                $login->terminarSessao();
                $utils->onRawIndexOk("Logout efetuado com sucesso!", $okRef);
                return;
            }

            if (!isset($_POST["nome"]) || !isset($_POST["senha"])) {
                $utils->onRawIndexErr("Credenciais inválidas!", $errRef);
                return;
            }

            $okRef = "../dashboard";
            if ($login->verificarSessao()) {
                header("Location:$okRef");
                return;
            }

            $loginResponse = $login->autenticarLogin($_POST["nome"], $_POST["senha"]);
            if ($loginResponse["err"] === null) {
                if ($loginResponse["login"] !== null)
                    $login->criarSessao($loginResponse["login"]);
                header("Location:$okRef");
            } else
                $utils->onRawIndexErr($loginResponse["err"], $errRef);
        }
        break;
    case "dashboard":
        {
            include "controller/DashboardController.php";
            include "controller/LoginController.php";
            include "dao/db/SQLQuery.php";
            include "dao/db/MySQLDatabase.php";
            include "dao/LoginDAO.php";
            include "dao/PagamentoDAO.php";
            include "dao/ProdutoDAO.php";
            include "dao/UsuarioDAO.php";
            include "dao/VendaDAO.php";
            include "model/LoginModel.php";
            include "model/PagamentoModel.php";
            include "model/ProdutoModel.php";
            include "model/UsuarioModel.php";
            include "model/VendaModel.php";

            if (!isset($_POST["operation"])) {
                echo "É necessário uma operação de identificação.";
                return;
            }

            session_start();

            $userDao = UsuarioDAO::getSingleton();
            $user = $userDao->consultarUsuario($_SESSION[LOGIN_ID]);
            if ($user === null) {
                $utils->onRawIndexErr("Usuário não encontrado!", "/");
                return;
            }

            $op = $_POST["operation"];
            switch ($op) {
                default:
                    echo "Operação não encontrada: <strong>$op</strong>";
                    break;
                case "criar-gerente":
                    {
                        if ($user->getNivel() !== UsuarioModel::SYSADMIN) {
                            echo "É necessário ter nível de acesso <strong>SYSADMIN</strong> para realizar essa operação!";
                            return;
                        }

                        if (!isset($_POST["nome"]) || $_POST["nome"] === "") {
                            echo "O campo <strong>Nome</strong> não pode ser vazio!";
                            return;
                        }

                        if (!isset($_POST["senha"]) || $_POST["senha"] === "") {
                            echo "O campo <strong>Senha</strong> não pode ser vazio!";
                            return;
                        }

                        $login = LoginController::getSingleton();
                        $response = $login->criarCredenciais($_POST["nome"], $_POST["senha"], UsuarioModel::GERENTE);
                        if ($response["status"])
                            echo "Conta para gerente criada com sucesso!";
                        else
                            echo "
                            Algo errado aconteceu durante a criação da conta para o novo gerente!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
                case "consultar-gerente-id":
                    {
                        if ($user->getNivel() !== UsuarioModel::SYSADMIN) {
                            echo pJustified("É necessário ter nível de acesso <strong>SYSADMIN</strong> para realizar essa operação!");
                            return;
                        }

                        if (!isset($_POST["id"]) || $_POST["id"] === "" || !is_numeric($_POST["id"])) {
                            echo pJustified("O campo <strong>Índice</strong> não pode ser vazio e deve ser numérico!");
                            return;
                        }

                        $dash = DashboardController::getSingleton();
                        $response = $dash->consultarGerenteId($_POST["id"]);
                        if ($response["status"]) {
                            $usr = $response["usr"];
                            $login = $response["login"];
                            echo "
                            <div class='card bg-info'>
                                <div class='table-responsive'>
                                    <table class='table table-info table-striped table-borderless text-center table-result'>
                                        <thead>
                                            <tr>
                                                <th scope='col'>ID</th>
                                                <th scope='col'>Nome</th>
                                                <th scope='col'>Nível de Acesso</th>
                                                <th scope='col'>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th scope='row'>" . $login->getId() . "</th>
                                                <td>" . $login->getNome() . "</td>
                                                <td>" . $usr->nivelDeAcesso() . "</td>
                                                <td>
                                                    <div class='text-center'>
                                                        <div class='btn-group'>
                                                            <button type='button' class='btn btn-sm btn-warning border-warning' data-toggle='collapse'
                                                                    data-target='#alterar-gerente' aria-expanded='false' aria-controls='alterar-gerente'>
                                                                <strong>Alterar</strong></button>
                                                            <button type='button' class='btn btn-sm btn-danger border-danger' data-toggle='collapse'
                                                                    data-target='#remover-gerente' aria-expanded='false' aria-controls='remover-gerente'>
                                                                <strong>Remover</strong></button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <hr/>
                            <div class='row'>
                                <div class='col'>
                                    <div class='collapse' id='alterar-gerente'>
                                        <div class='container alert alert-warning border-warning'>
                                            <button type='button' class='close' data-toggle='collapse' data-target='#alterar-gerente'
                                                    aria-expanded='false' aria-controls='alterar-gerente'>&times;
                                            </button>
                                            <h4>Alterar Gerente</h4>
                                            <hr/>
                                            <div class='form-row'>
                                                <div class='form-group col-md-6'>
                                                    <input type='text' class='form-control' id='alterar-gerente-nome' name='nome'
                                                           placeholder='Nome' value='" . $login->getNome() . "'/>
                                                </div>
                                                <div class='form-group col-md-6'>
                                                    <input type='password' class='form-control' id='alterar-gerente-senha' name='senha'
                                                           placeholder='Senha' value='" . $login->getSenha() . "'/>
                                                    <div class='form-check-inline'>
                                                        <input class='form-check-input' id='alterar-gerente-show-password' type='checkbox'
                                                               onclick='showPassword(\"alterar-gerente-senha\")'/>
                                                        <label class='form-check-label' for='alterar-gerente-show-password'>Mostrar senha</label>
                                                    </div>
                                                </div>
                                                <div class='form-group col-md-6'>
                                                    <select class='form-control' id='alterar-gerente-nivel'>
                                                        <option value='" . UsuarioModel::GERENTE . "' selected>GERENTE</option>
                                                        <option value='" . UsuarioModel::VENDEDOR . "'>VENDEDOR</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <input type='hidden' id='alterar-gerente-id' value='" . $login->getId() . "'/>
                                            <input type='hidden' id='alterar-gerente-id-usuario' value='" . $usr->getId() . "'/>
                                            <button id='alterar-gerente-btn' type='button' class='btn btn-outline-warning'
                                                    onclick='alterarGerente()'>
                                                <strong>Alterar</strong></button>
                                            <hr/>
                                            <div id='alterar-gerente-result'></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class='row'>
                                <div class='col'>
                                    <div class='collapse' id='remover-gerente'>
                                        <div class='container alert alert-danger border-danger'>
                                            <button type='button' class='close' data-toggle='collapse' data-target='#remover-gerente'
                                                    aria-expanded='false' aria-controls='remover-gerente'>&times;
                                            </button>
                                            <h4>Remover Gerente</h4>
                                            <hr/>
                                            <p align='justify'>
                                                Você realmente deseja remover o gerente <strong>" . $login->getNome() . "</strong>?
                                            </p>
                                            <br/>
                                            <input type='hidden' id='remover-gerente-id' value='" . $login->getId() . "'/>
                                            <input type='hidden' id='remover-gerente-id-usuario' value='" . $usr->getId() . "'/>
                                            <div class='text-center'>
                                                <div class='form-group col-md-12'>
                                                    <button id='remover-gerente-btn' type='button' class='btn btn-lg btn-outline-success'
                                                            onclick='removerGerente()'>
                                                        <strong>Sim</strong></button>
                                                    &nbsp;
                                                    <button type='button' class='btn btn-lg btn-outline-danger' data-toggle='collapse'
                                                            data-target='#remover-gerente' aria-expanded='false' aria-controls='remover-gerente'>
                                                        <strong>Não</strong></button>
                                                </div>
                                            </div>
                                            <hr/>
                                            <div id='remover-gerente-result'></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ";
                        } else
                            echo "
                            Algo errado aconteceu durante a consulta da conta do gerente!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
                case "consultar-gerente-todos":
                    {
                        if ($user->getNivel() !== UsuarioModel::SYSADMIN) {
                            echo pJustified("É necessário ter nível de acesso <strong>SYSADMIN</strong> para realizar essa operação!");
                            return;
                        }

                        $dash = DashboardController::getSingleton();
                        $response = $dash->consultarGerentes();
                        if ($response["status"]) {
                            $result = "
                            <p class='alert alert-warning form-text text-muted border-warning' align='justify'>
                                <strong><u>Importante!</u></strong> Operações de <strong class='text-warning'>Alterar</strong> e <strong class='text-danger'>Remover</strong> somente estão acessíveis através do botão <span class='badge badge-info border-info'>Por ID</span> , após realizado a consulta pelo índice.
                            </p>
                            <hr/>
                            <div class='card bg-info'>
                                <div class='table-responsive'>
                                    <table class='table table-info table-hover table-borderless text-center table-result'>
                                        <thead>
                                            <tr>
                                                <th scope='col'>ID</th>
                                                <th scope='col'>Nome</th>
                                                <th scope='col'>Nível de Acesso</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            ";
                            foreach ($response["entries"] as $entry)
                                $result .= "
                                                <tr>
                                                    <th scope='row'>" . $entry["login"]->getId() . "</th>
                                                    <td>" . $entry["login"]->getNome() . "</td>
                                                    <td>" . $entry["usr"]->nivelDeAcesso() . "</td>
                                                </tr>
                                ";
                            $result .= "
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            ";
                            echo $result;
                        } else
                            echo "
                            Algo errado aconteceu durante a consulta da conta dos gerentes!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
                case "alterar-gerente":
                    {
                        if ($user->getNivel() !== UsuarioModel::SYSADMIN) {
                            echo "É necessário ter nível de acesso <strong>SYSADMIN</strong> para realizar essa operação!";
                            return;
                        }

                        if (!isset($_POST["nome"]) || $_POST["nome"] === "") {
                            echo "O campo <strong>Nome</strong> não pode ser vazio!";
                            return;
                        }

                        if (!isset($_POST["senha"]) || $_POST["senha"] === "") {
                            echo "O campo <strong>Senha</strong> não pode ser vazio!";
                            return;
                        }

                        if (!isset($_POST["id"]) || $_POST["id"] === "" || !is_numeric($_POST["id"]) || !isset($_POST["id_usuario"]) || $_POST["id_usuario"] === "" || !is_numeric($_POST["id_usuario"])) {
                            echo "Elementos identificadores não especificados!";
                            return;
                        }

                        $dash = DashboardController::getSingleton();
                        $login = new LoginModel($_POST["id"], $_POST["nome"], $_POST["senha"], -1);
                        $usr = new UsuarioModel($_POST["id_usuario"], $_POST["nivel"]);
                        $response = $dash->alterarGerente($login, $usr);
                        if ($response["status"])
                            echo "Conta do gerente alterada com sucesso!";
                        else
                            echo "
                            Algo errado aconteceu durante a alteração da conta do gerente!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
                case "remover-gerente":
                    {
                        if ($user->getNivel() !== UsuarioModel::SYSADMIN) {
                            echo "É necessário ter nível de acesso <strong>SYSADMIN</strong> para realizar essa operação!";
                            return;
                        }

                        if (!isset($_POST["id"]) || $_POST["id"] === "" || !is_numeric($_POST["id"]) || !isset($_POST["id_usuario"]) || $_POST["id_usuario"] === "" || !is_numeric($_POST["id_usuario"])) {
                            echo "Elementos identificadores não especificados!";
                            return;
                        }

                        $dash = DashboardController::getSingleton();
                        $response = $dash->removerGerente($_POST["id"], $_POST["id_usuario"]);
                        if ($response["status"])
                            echo "Conta do gerente removida com sucesso!";
                        else
                            echo "
                            Algo errado aconteceu durante a remoção da conta do gerente!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
                case "criar-vendedor":
                    {
                        if ($user->getNivel() > UsuarioModel::GERENTE) {
                            echo "É necessário ter nível de acesso <strong>GERENTE</strong> para realizar essa operação!";
                            return;
                        }

                        if (!isset($_POST["nome"]) || $_POST["nome"] === "") {
                            echo "O campo <strong>Nome</strong> não pode ser vazio!";
                            return;
                        }

                        if (!isset($_POST["senha"]) || $_POST["senha"] === "") {
                            echo "O campo <strong>Senha</strong> não pode ser vazio!";
                            return;
                        }

                        $login = LoginController::getSingleton();
                        $response = $login->criarCredenciais($_POST["nome"], $_POST["senha"], UsuarioModel::VENDEDOR);
                        if ($response["status"])
                            echo "Conta para vendedor criada com sucesso!";
                        else
                            echo "
                            Algo errado aconteceu durante a criação da conta para o novo vendedor!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
                case "consultar-vendedor-id":
                    {
                        if ($user->getNivel() > UsuarioModel::GERENTE) {
                            echo pJustified("É necessário ter nível de acesso <strong>GERENTE</strong> para realizar essa operação!");
                            return;
                        }

                        if (!isset($_POST["id"]) || $_POST["id"] === "" || !is_numeric($_POST["id"])) {
                            echo pJustified("O campo <strong>Índice</strong> não pode ser vazio e deve ser numérico!");
                            return;
                        }

                        $dash = DashboardController::getSingleton();
                        $response = $dash->consultarVendedorId($_POST["id"]);
                        if ($response["status"]) {
                            $usr = $response["usr"];
                            $login = $response["login"];
                            echo "
                            <div class='card bg-info'>
                                <div class='table-responsive'>
                                    <table class='table table-info table-striped table-borderless text-center table-result'>
                                        <thead>
                                            <tr>
                                                <th scope='col'>ID</th>
                                                <th scope='col'>Nome</th>
                                                <th scope='col'>Nível de Acesso</th>
                                                <th scope='col'>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th scope='row'>" . $login->getId() . "</th>
                                                <td>" . $login->getNome() . "</td>
                                                <td>" . $usr->nivelDeAcesso() . "</td>
                                                <td>
                                                    <div class='text-center'>
                                                        <div class='btn-group'>
                                                            <button type='button' class='btn btn-sm btn-warning border-warning' data-toggle='collapse'
                                                                    data-target='#alterar-vendedor' aria-expanded='false' aria-controls='alterar-gerente'>
                                                                <strong>Alterar</strong></button>
                                                            <button type='button' class='btn btn-sm btn-danger border-danger' data-toggle='collapse'
                                                                    data-target='#remover-vendedor' aria-expanded='false' aria-controls='remover-gerente'>
                                                                <strong>Remover</strong></button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <hr/>
                            <div class='row'>
                                <div class='col'>
                                    <div class='collapse' id='alterar-vendedor'>
                                        <div class='container alert alert-warning border-warning'>
                                            <button type='button' class='close' data-toggle='collapse' data-target='#alterar-vendedor'
                                                    aria-expanded='false' aria-controls='alterar-vendedor'>&times;
                                            </button>
                                            <h4>Alterar Vendedor</h4>
                                            <hr/>
                                            <div class='form-row'>
                                                <div class='form-group col-md-6'>
                                                    <input type='text' class='form-control' id='alterar-vendedor-nome' name='nome'
                                                           placeholder='Nome' value='" . $login->getNome() . "'/>
                                                </div>
                                                <div class='form-group col-md-6'>
                                                    <input type='password' class='form-control' id='alterar-vendedor-senha' name='senha'
                                                           placeholder='Senha' value='" . $login->getSenha() . "'/>
                                                    <div class='form-check-inline'>
                                                        <input class='form-check-input' id='alterar-vendedor-show-password' type='checkbox'
                                                               onclick='showPassword(\"alterar-vendedor-senha\")'/>
                                                        <label class='form-check-label' for='alterar-vendedor-show-password'>Mostrar senha</label>
                                                    </div>
                                                </div>
                                                <div class='form-group col-md-6'>
                                                    <select class='form-control' id='alterar-vendedor-nivel'>
                                                        <option value='" . UsuarioModel::GERENTE . "'>GERENTE</option>
                                                        <option value='" . UsuarioModel::VENDEDOR . "' selected>VENDEDOR</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <input type='hidden' id='alterar-vendedor-id' value='" . $login->getId() . "'/>
                                            <input type='hidden' id='alterar-vendedor-id-usuario' value='" . $usr->getId() . "'/>
                                            <button id='alterar-vendedor-btn' type='button' class='btn btn-outline-warning'
                                                    onclick='alterarVendedor()'>
                                                <strong>Alterar</strong></button>
                                            <hr/>
                                            <div id='alterar-vendedor-result'></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class='row'>
                                <div class='col'>
                                    <div class='collapse' id='remover-vendedor'>
                                        <div class='container alert alert-danger border-danger'>
                                            <button type='button' class='close' data-toggle='collapse' data-target='#remover-vendedor'
                                                    aria-expanded='false' aria-controls='remover-vendedor'>&times;
                                            </button>
                                            <h4>Remover Vendedor</h4>
                                            <hr/>
                                            <p align='justify'>
                                                Você realmente deseja remover o vendedor <strong>" . $login->getNome() . "</strong>?
                                            </p>
                                            <br/>
                                            <input type='hidden' id='remover-vendedor-id' value='" . $login->getId() . "'/>
                                            <input type='hidden' id='remover-vendedor-id-usuario' value='" . $usr->getId() . "'/>
                                            <div class='text-center'>
                                                <div class='form-group col-md-12'>
                                                    <button id='remover-vendedor-btn' type='button' class='btn btn-lg btn-outline-success'
                                                            onclick='removerVendedor()'>
                                                        <strong>Sim</strong></button>
                                                    &nbsp;
                                                    <button type='button' class='btn btn-lg btn-outline-danger' data-toggle='collapse'
                                                            data-target='#remover-vendedor' aria-expanded='false' aria-controls='remover-gerente'>
                                                        <strong>Não</strong></button>
                                                </div>
                                            </div>
                                            <hr/>
                                            <div id='remover-vendedor-result'></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ";
                        } else
                            echo "
                            Algo errado aconteceu durante a consulta da conta do vendedor!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
                case "consultar-vendedor-todos":
                    {
                        if ($user->getNivel() > UsuarioModel::GERENTE) {
                            echo "É necessário ter nível de acesso <strong>GERENTE</strong> para realizar essa operação!";
                            return;
                        }

                        $dash = DashboardController::getSingleton();
                        $response = $dash->consultarVendedores();
                        if ($response["status"]) {
                            $result = "
                            <p class='alert alert-warning form-text text-muted border-warning' align='justify'>
                                <strong><u>Importante!</u></strong> Operações de <strong class='text-warning'>Alterar</strong> e <strong class='text-danger'>Remover</strong> somente estão acessíveis através do botão <span class='badge badge-info border-info'>Por ID</span> , após realizado a consulta pelo índice.
                            </p>
                            <hr/>
                            <div class='card bg-info'>
                                <div class='table-responsive'>
                                    <table class='table table-info table-hover table-borderless text-center table-result'>
                                        <thead>
                                            <tr>
                                                <th scope='col'>ID</th>
                                                <th scope='col'>Nome</th>
                                                <th scope='col'>Nível de Acesso</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            ";
                            foreach ($response["entries"] as $entry)
                                $result .= "
                                                <tr>
                                                    <th scope='row'>" . $entry["login"]->getId() . "</th>
                                                    <td>" . $entry["login"]->getNome() . "</td>
                                                    <td>" . $entry["usr"]->nivelDeAcesso() . "</td>
                                                </tr>
                                ";
                            $result .= "
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            ";
                            echo $result;
                        } else
                            echo "
                            Algo errado aconteceu durante a consulta da conta dos vendedores!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
                case "alterar-vendedor":
                    {
                        if ($user->getNivel() > UsuarioModel::GERENTE) {
                            echo "É necessário ter nível de acesso <strong>GERENTE</strong> para realizar essa operação!";
                            return;
                        }

                        if (!isset($_POST["nome"]) || $_POST["nome"] === "") {
                            echo "O campo <strong>Nome</strong> não pode ser vazio!";
                            return;
                        }

                        if (!isset($_POST["senha"]) || $_POST["senha"] === "") {
                            echo "O campo <strong>Senha</strong> não pode ser vazio!";
                            return;
                        }

                        if (!isset($_POST["id"]) || $_POST["id"] === "" || !is_numeric($_POST["id"]) || !isset($_POST["id_usuario"]) || $_POST["id_usuario"] === "" || !is_numeric($_POST["id_usuario"])) {
                            echo "Elementos identificadores não especificados!";
                            return;
                        }

                        $dash = DashboardController::getSingleton();
                        $login = new LoginModel($_POST["id"], $_POST["nome"], $_POST["senha"], -1);
                        $usr = new UsuarioModel($_POST["id_usuario"], $_POST["nivel"]);
                        $response = $dash->alterarVendedor($login, $usr);
                        if ($response["status"])
                            echo "Conta do vendedor alterada com sucesso!";
                        else
                            echo "
                            Algo errado aconteceu durante a alteração da conta do vendedor!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
                case "remover-vendedor":
                    {
                        if ($user->getNivel() > UsuarioModel::GERENTE) {
                            echo "É necessário ter nível de acesso <strong>GERENTE</strong> para realizar essa operação!";
                            return;
                        }

                        if (!isset($_POST["id"]) || $_POST["id"] === "" || !is_numeric($_POST["id"]) || !isset($_POST["id_usuario"]) || $_POST["id_usuario"] === "" || !is_numeric($_POST["id_usuario"])) {
                            echo "Elementos identificadores não especificados!";
                            return;
                        }

                        $dash = DashboardController::getSingleton();
                        $response = $dash->removerVendedor($_POST["id"], $_POST["id_usuario"]);
                        if ($response["status"])
                            echo "Conta do vendedor removida com sucesso!";
                        else
                            echo "
                            Algo errado aconteceu durante a remoção da conta do vendedor!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
                case "criar-produto":
                    {
                        if ($user->getNivel() > UsuarioModel::VENDEDOR) {
                            echo "É necessário ter nível de acesso <strong>VENDEDOR</strong> para realizar essa operação!";
                            return;
                        }

                        if (!isset($_POST["nome"]) || $_POST["nome"] === "") {
                            echo "O campo <strong>Nome</strong> não pode ser vazio!";
                            return;
                        }

                        if (!isset($_POST["preco_unitario"]) || $_POST["preco_unitario"] === "" || !is_numeric($_POST["preco_unitario"])) {
                            echo pJustified("O campo <strong>Preço Unitário</strong> não pode ser vazio e deve ser numérico!");
                            return;
                        }

                        if (!isset($_POST["total_unidades"]) || $_POST["total_unidades"] === "" || !is_numeric($_POST["total_unidades"])) {
                            echo pJustified("O campo <strong>Total Unidades</strong> não pode ser vazio e deve ser numérico!");
                            return;
                        }

                        $dash = DashboardController::getSingleton();
                        $produto = new ProdutoModel(-1, $_POST["nome"], $_POST["preco_unitario"], $_POST["total_unidades"]);
                        $response = $dash->criarProduto($produto);
                        if ($response["status"])
                            echo "Produto criado com sucesso!";
                        else
                            echo "
                            Algo errado aconteceu durante a criação do novo produto!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
                case "consultar-produto-id":
                    {
                        if ($user->getNivel() > UsuarioModel::VENDEDOR) {
                            echo pJustified("É necessário ter nível de acesso <strong>VENDEDOR</strong> para realizar essa operação!");
                            return;
                        }

                        if (!isset($_POST["id"]) || $_POST["id"] === "" || !is_numeric($_POST["id"])) {
                            echo pJustified("O campo <strong>Índice</strong> não pode ser vazio e deve ser numérico!");
                            return;
                        }

                        $dash = DashboardController::getSingleton();
                        $response = $dash->consultarProdutoId($_POST["id"]);
                        if ($response["status"]) {
                            $produto = $response["produto"];
                            echo "
                            <div class='card bg-info'>
                                <div class='table-responsive'>
                                    <table class='table table-info table-striped table-borderless text-center table-result'>
                                        <thead>
                                            <tr>
                                                <th scope='col'>ID</th>
                                                <th scope='col'>Nome</th>
                                                <th scope='col'>Preço Unitário</th>
                                                <th scope='col'>Total Unidades</th>
                                                <th scope='col'>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th scope='row'>" . $produto->getId() . "</th>
                                                <td>" . $produto->getNome() . "</td>
                                                <td>" . $produto->getPrecoUnitario() . "</td>
                                                <td>" . $produto->getTotalUnidades() . "</td>
                                                <td>
                                                    <div class='text-center'>
                                                        <div class='btn-group'>
                                                            <button type='button' class='btn btn-sm btn-warning border-warning' data-toggle='collapse'
                                                                    data-target='#alterar-produto' aria-expanded='false' aria-controls='alterar-produto'>
                                                                <strong>Alterar</strong></button>
                                                            <button type='button' class='btn btn-sm btn-danger border-danger' data-toggle='collapse'
                                                                    data-target='#remover-produto' aria-expanded='false' aria-controls='remover-produto'>
                                                                <strong>Remover</strong></button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <hr/>
                            <div class='row'>
                                <div class='col'>
                                    <div class='collapse' id='alterar-produto'>
                                        <div class='container alert alert-warning border-warning'>
                                            <button type='button' class='close' data-toggle='collapse' data-target='#alterar-produto'
                                                    aria-expanded='false' aria-controls='alterar-produto'>&times;
                                            </button>
                                            <h4>Alterar Produto</h4>
                                            <hr/>
                                            <div class='form-row'>
                                                <div class='form-group col-md-12'>
                                                    <input type='text' class='form-control' id='alterar-produto-nome' name='nome'
                                                           placeholder='Nome' value='" . $produto->getNome() . "'/>
                                                </div>
                                                <div class='form-group col-md-6'>
                                                    <input type='number' class='form-control' id='alterar-produto-preco-unitario'
                                                            placeholder='Preço Unitário' value='" . $produto->getPrecoUnitario() . "'
                                                            min='0' step='0.01'/>
                                                </div>
                                                <div class='form-group col-md-6'>
                                                    <input type='number' class='form-control' id='alterar-produto-total-unidades'
                                                            placeholder='Total Unidades' value='" . $produto->getTotalUnidades() . "'
                                                            min='1' step='1'/>
                                                </div>
                                            </div>
                                            <input type='hidden' id='alterar-produto-id' value='" . $produto->getId() . "'/>
                                            <button id='alterar-produto-btn' type='button' class='btn btn-outline-warning'
                                                    onclick='alterarProduto()'>
                                                <strong>Alterar</strong></button>
                                            <hr/>
                                            <div id='alterar-produto-result'></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class='row'>
                                <div class='col'>
                                    <div class='collapse' id='remover-produto'>
                                        <div class='container alert alert-danger border-danger'>
                                            <button type='button' class='close' data-toggle='collapse' data-target='#remover-produto'
                                                    aria-expanded='false' aria-controls='remover-produto'>&times;
                                            </button>
                                            <h4>Remover Produto</h4>
                                            <hr/>
                                            <p align='justify'>
                                                Você realmente deseja remover o produto <strong>" . $produto->getNome() . "</strong>?
                                            </p>
                                            <br/>
                                            <input type='hidden' id='remover-produto-id' value='" . $produto->getId() . "'/>
                                            <div class='text-center'>
                                                <div class='form-group col-md-12'>
                                                    <button id='remover-produto-btn' type='button' class='btn btn-lg btn-outline-success'
                                                            onclick='removerProduto()'>
                                                        <strong>Sim</strong></button>
                                                    &nbsp;
                                                    <button type='button' class='btn btn-lg btn-outline-danger' data-toggle='collapse'
                                                            data-target='#remover-produto' aria-expanded='false' aria-controls='remover-produto'>
                                                        <strong>Não</strong></button>
                                                </div>
                                            </div>
                                            <hr/>
                                            <div id='remover-produto-result'></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ";
                        } else
                            echo "
                            Algo errado aconteceu durante a consulta do produto!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
                case "consultar-produto-todos":
                    {
                        if ($user->getNivel() > UsuarioModel::VENDEDOR) {
                            echo "É necessário ter nível de acesso <strong>VENDEDOR</strong> para realizar essa operação!";
                            return;
                        }

                        $dash = DashboardController::getSingleton();
                        $response = $dash->consultarProdutos();
                        if ($response["status"]) {
                            $result = "
                            <p class='alert alert-warning form-text text-muted border-warning' align='justify'>
                                <strong><u>Importante!</u></strong> Operações de <strong class='text-warning'>Alterar</strong> e <strong class='text-danger'>Remover</strong> somente estão acessíveis através do botão <span class='badge badge-info border-info'>Por ID</span> , após realizado a consulta pelo índice.
                            </p>
                            <hr/>
                            <div class='card bg-info'>
                                <div class='table-responsive'>
                                    <table class='table table-info table-hover table-borderless text-center table-result'>
                                        <thead>
                                            <tr>
                                                <th scope='col'>ID</th>
                                                <th scope='col'>Nome</th>
                                                <th scope='col'>Preço Unitário</th>
                                                <th scope='col'>Total Unidades</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            ";
                            foreach ($response["entries"] as $produto)
                                $result .= "
                                                <tr>
                                                    <th scope='row'>" . $produto->getId() . "</th>
                                                    <td>" . $produto->getNome() . "</td>
                                                    <td>" . $produto->getPrecoUnitario() . "</td>
                                                    <td>" . $produto->getTotalUnidades() . "</td>
                                                </tr>
                                ";
                            $result .= "
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            ";
                            echo $result;
                        } else
                            echo "
                            Algo errado aconteceu durante a consulta dos produtos!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
                case "alterar-produto":
                    {
                        if ($user->getNivel() > UsuarioModel::VENDEDOR) {
                            echo "É necessário ter nível de acesso <strong>VENDEDOR</strong> para realizar essa operação!";
                            return;
                        }

                        if (!isset($_POST["nome"]) || $_POST["nome"] === "") {
                            echo "O campo <strong>Nome</strong> não pode ser vazio!";
                            return;
                        }

                        if (!isset($_POST["preco_unitario"]) || $_POST["preco_unitario"] === "" || !is_numeric($_POST["preco_unitario"])) {
                            echo pJustified("O campo <strong>Preço Unitário</strong> não pode ser vazio e deve ser numérico!");
                            return;
                        }

                        if (!isset($_POST["total_unidades"]) || $_POST["total_unidades"] === "" || !is_numeric($_POST["total_unidades"])) {
                            echo pJustified("O campo <strong>Total Unidades</strong> não pode ser vazio e deve ser numérico!");
                            return;
                        }

                        if (!isset($_POST["id"]) || $_POST["id"] === "" || !is_numeric($_POST["id"])) {
                            echo "Elemento identificador não especificado!";
                            return;
                        }

                        $dash = DashboardController::getSingleton();
                        $produto = new ProdutoModel($_POST["id"], $_POST["nome"], $_POST["preco_unitario"], $_POST["total_unidades"]);
                        $response = $dash->alterarProduto($produto);
                        if ($response["status"])
                            echo "Produto alterado com sucesso!";
                        else
                            echo "
                            Algo errado aconteceu durante a alteração do produto!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
                case "remover-produto":
                    {
                        if ($user->getNivel() > UsuarioModel::VENDEDOR) {
                            echo "É necessário ter nível de acesso <strong>VENDEDOR</strong> para realizar essa operação!";
                            return;
                        }

                        if (!isset($_POST["id"]) || $_POST["id"] === "" || !is_numeric($_POST["id"])) {
                            echo "Elemento identificador não especificado!";
                            return;
                        }

                        $dash = DashboardController::getSingleton();
                        $response = $dash->removerProduto($_POST["id"]);
                        if ($response["status"])
                            echo "Produto removido com sucesso!";
                        else
                            echo "
                            Algo errado aconteceu durante a remoção do produto!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
                case "criar-pagamento":
                    {
                        if ($user->getNivel() > UsuarioModel::VENDEDOR) {
                            echo "É necessário ter nível de acesso <strong>VENDEDOR</strong> para realizar essa operação!";
                            return;
                        }

                        if (!isset($_POST["nome"]) || $_POST["nome"] === "") {
                            echo "O campo <strong>Nome</strong> não pode ser vazio!";
                            return;
                        }

                        $dash = DashboardController::getSingleton();
                        $pagamento = new PagamentoModel(-1, $_POST["nome"]);
                        $response = $dash->criarPagamento($pagamento);
                        if ($response["status"])
                            echo "Pagamento criado com sucesso!";
                        else
                            echo "
                            Algo errado aconteceu durante a criação do novo pagamento!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
                case "consultar-pagamento-id":
                    {
                        if ($user->getNivel() > UsuarioModel::VENDEDOR) {
                            echo pJustified("É necessário ter nível de acesso <strong>VENDEDOR</strong> para realizar essa operação!");
                            return;
                        }

                        if (!isset($_POST["id"]) || $_POST["id"] === "" || !is_numeric($_POST["id"])) {
                            echo pJustified("O campo <strong>Índice</strong> não pode ser vazio e deve ser numérico!");
                            return;
                        }

                        $dash = DashboardController::getSingleton();
                        $response = $dash->consultarPagamentoId($_POST["id"]);
                        if ($response["status"]) {
                            $pagamento = $response["pagamento"];
                            echo "
                            <div class='card bg-info'>
                                <div class='table-responsive'>
                                    <table class='table table-info table-striped table-borderless text-center table-result'>
                                        <thead>
                                            <tr>
                                                <th scope='col'>ID</th>
                                                <th scope='col'>Nome</th>
                                                <th scope='col'>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th scope='row'>" . $pagamento->getId() . "</th>
                                                <td>" . $pagamento->getNome() . "</td>
                                                <td>
                                                    <div class='text-center'>
                                                        <div class='btn-group'>
                                                            <button type='button' class='btn btn-sm btn-warning border-warning' data-toggle='collapse'
                                                                    data-target='#alterar-pagamento' aria-expanded='false' aria-controls='alterar-pagamento'>
                                                                <strong>Alterar</strong></button>
                                                            <button type='button' class='btn btn-sm btn-danger border-danger' data-toggle='collapse'
                                                                    data-target='#remover-pagamento' aria-expanded='false' aria-controls='remover-pagamento'>
                                                                <strong>Remover</strong></button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <hr/>
                            <div class='row'>
                                <div class='col'>
                                    <div class='collapse' id='alterar-pagamento'>
                                        <div class='container alert alert-warning border-warning'>
                                            <button type='button' class='close' data-toggle='collapse' data-target='#alterar-pagamento'
                                                    aria-expanded='false' aria-controls='alterar-pagamento'>&times;
                                            </button>
                                            <h4>Alterar Pagamento</h4>
                                            <hr/>
                                            <div class='form-row'>
                                                <div class='form-group col-md-12'>
                                                    <input type='text' class='form-control' id='alterar-pagamento-nome' name='nome'
                                                           placeholder='Nome' value='" . $pagamento->getNome() . "'/>
                                                </div>
                                            </div>
                                            <input type='hidden' id='alterar-pagamento-id' value='" . $pagamento->getId() . "'/>
                                            <button id='alterar-pagamento-btn' type='button' class='btn btn-outline-warning'
                                                    onclick='alterarPagamento()'>
                                                <strong>Alterar</strong></button>
                                            <hr/>
                                            <div id='alterar-pagamento-result'></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class='row'>
                                <div class='col'>
                                    <div class='collapse' id='remover-pagamento'>
                                        <div class='container alert alert-danger border-danger'>
                                            <button type='button' class='close' data-toggle='collapse' data-target='#remover-pagamento'
                                                    aria-expanded='false' aria-controls='remover-pagamento'>&times;
                                            </button>
                                            <h4>Remover Pagamento</h4>
                                            <hr/>
                                            <p align='justify'>
                                                Você realmente deseja remover o pagamento <strong>" . $pagamento->getNome() . "</strong>?
                                            </p>
                                            <br/>
                                            <input type='hidden' id='remover-pagamento-id' value='" . $pagamento->getId() . "'/>
                                            <div class='text-center'>
                                                <div class='form-group col-md-12'>
                                                    <button id='remover-pagamento-btn' type='button' class='btn btn-lg btn-outline-success'
                                                            onclick='removerPagamento()'>
                                                        <strong>Sim</strong></button>
                                                    &nbsp;
                                                    <button type='button' class='btn btn-lg btn-outline-danger' data-toggle='collapse'
                                                            data-target='#remover-pagamento' aria-expanded='false' aria-controls='remover-pagamento'>
                                                        <strong>Não</strong></button>
                                                </div>
                                            </div>
                                            <hr/>
                                            <div id='remover-pagamento-result'></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ";
                        } else
                            echo "
                            Algo errado aconteceu durante a consulta do pagamento!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
                case "consultar-pagamento-todos":
                    {
                        if ($user->getNivel() > UsuarioModel::VENDEDOR) {
                            echo "É necessário ter nível de acesso <strong>VENDEDOR</strong> para realizar essa operação!";
                            return;
                        }

                        $dash = DashboardController::getSingleton();
                        $response = $dash->consultarPagamentos();
                        if ($response["status"]) {
                            $result = "
                            <p class='alert alert-warning form-text text-muted border-warning' align='justify'>
                                <strong><u>Importante!</u></strong> Operações de <strong class='text-warning'>Alterar</strong> e <strong class='text-danger'>Remover</strong> somente estão acessíveis através do botão <span class='badge badge-info border-info'>Por ID</span> , após realizado a consulta pelo índice.
                            </p>
                            <hr/>
                            <div class='card bg-info'>
                                <div class='table-responsive'>
                                    <table class='table table-info table-hover table-borderless text-center table-result'>
                                        <thead>
                                            <tr>
                                                <th scope='col'>ID</th>
                                                <th scope='col'>Nome</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            ";
                            foreach ($response["entries"] as $pagamento)
                                $result .= "
                                                <tr>
                                                    <th scope='row'>" . $pagamento->getId() . "</th>
                                                    <td>" . $pagamento->getNome() . "</td>
                                                </tr>
                                ";
                            $result .= "
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            ";
                            echo $result;
                        } else
                            echo "
                            Algo errado aconteceu durante a consulta dos pagamentos!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
                case "alterar-pagamento":
                    {
                        if ($user->getNivel() > UsuarioModel::VENDEDOR) {
                            echo "É necessário ter nível de acesso <strong>VENDEDOR</strong> para realizar essa operação!";
                            return;
                        }

                        if (!isset($_POST["nome"]) || $_POST["nome"] === "") {
                            echo "O campo <strong>Nome</strong> não pode ser vazio!";
                            return;
                        }

                        if (!isset($_POST["id"]) || $_POST["id"] === "" || !is_numeric($_POST["id"])) {
                            echo "Elemento identificador não especificado!";
                            return;
                        }

                        $dash = DashboardController::getSingleton();
                        $pagamento = new PagamentoModel($_POST["id"], $_POST["nome"]);
                        $response = $dash->alterarPagamento($pagamento);
                        if ($response["status"])
                            echo "Pagamento alterado com sucesso!";
                        else
                            echo "
                            Algo errado aconteceu durante a alteração do pagamento!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
                case "remover-pagamento":
                    {
                        if ($user->getNivel() > UsuarioModel::VENDEDOR) {
                            echo "É necessário ter nível de acesso <strong>VENDEDOR</strong> para realizar essa operação!";
                            return;
                        }

                        if (!isset($_POST["id"]) || $_POST["id"] === "" || !is_numeric($_POST["id"])) {
                            echo "Elemento identificador não especificado!";
                            return;
                        }

                        $dash = DashboardController::getSingleton();
                        $response = $dash->removerPagamento($_POST["id"]);
                        if ($response["status"])
                            echo "Pagamento removido com sucesso!";
                        else
                            echo "
                            Algo errado aconteceu durante a remoção do pagamento!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
                case "criar-venda":
                    {
                        if ($user->getNivel() > UsuarioModel::VENDEDOR) {
                            echo "É necessário ter nível de acesso <strong>VENDEDOR</strong> para realizar essa operação!";
                            return;
                        }

                        if (!isset($_POST["id_usuario"]) || $_POST["id_usuario"] === "" || !is_numeric($_POST["id_usuario"])) {
                            echo "O campo <strong>ID Usuário</strong> deve ser referenciado!";
                            return;
                        }

                        if (!isset($_POST["id_pagamento"]) || $_POST["id_pagamento"] === "" || !is_numeric($_POST["id_pagamento"])) {
                            echo "O campo <strong>ID Pagamento</strong> deve ser referenciado!";
                            return;
                        }

                        $dash = DashboardController::getSingleton();
                        $id_produtos = array();
                        if (isset($_POST["id_produtos"])) {
                            if (strpos($_POST["id_produtos"], ",") !== false)
                                $id_produtos = explode(",", $_POST["id_produtos"]);
                            else
                                array_push($id_produtos, $_POST["id_produtos"]);
                        }

                        $preco_produtos = array();
                        $valor = 0;
                        if (isset($id_produtos))
                            foreach ($id_produtos as $id_produto)
                                if (($result = $dash->consultarProdutoId(intval($id_produto)))["status"]) {
                                    $valor += floatval($result["produto"]->getPrecoUnitario());
                                    array_push($preco_produtos, $result["produto"]->getPrecoUnitario());
                                }

                        $venda = new VendaModel(
                            -1,
                            $_POST["id_usuario"],
                            $_POST["id_pagamento"],
                            $id_produtos,
                            $preco_produtos,
                            $valor,
                            null
                        );
                        $response = $dash->criarVenda($venda);
                        if ($response["status"])
                            echo "Venda criada com sucesso!";
                        else
                            echo "
                            Algo errado aconteceu durante a criação da nova venda!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
                case "consultar-venda-id":
                    {
                        if ($user->getNivel() > UsuarioModel::VENDEDOR) {
                            echo pJustified("É necessário ter nível de acesso <strong>VENDEDOR</strong> para realizar essa operação!");
                            return;
                        }

                        if (!isset($_POST["id"]) || $_POST["id"] === "" || !is_numeric($_POST["id"])) {
                            echo pJustified("O campo <strong>Índice</strong> não pode ser vazio e deve ser numérico!");
                            return;
                        }

                        $dash = DashboardController::getSingleton();
                        $response = $dash->consultarVendaId($_POST["id"]);
                        if ($response["status"]) {
                            $venda = $response["venda"];

                            $usrDao = UsuarioDAO::getSingleton();
                            $loginDao = LoginDAO::getSingleton();
                            $produtoDao = ProdutoDAO::getSingleton();
                            $pagamentoDao = PagamentoDAO::getSingleton();

                            $usr = $usrDao->consultarUsuario($venda->getIdUsuario());
                            $usrNome = $usr === null
                                ? "<strong class='text-danger'>Usuário Desconhecido</strong>"
                                : ($login = $loginDao->consultarLoginId($usr->getId())) === null
                                    ? "<strong class='text-danger'>Usuário Desconhecido</strong>"
                                    : $login->getNome();

                            $pagamento = $pagamentoDao->consultarPagamento($venda->getIdPagamento());
                            $pagamentoNome = $pagamento === null
                                ? "<strong class='text-danger'>Pagamento Desconhecido</strong>"
                                : $pagamento->getNome();

                            $produtos = null;
                            $produtos_alt = null;
                            $pId = 0;
                            if (sizeof(($id_produtos = $venda->getIdProdutos())) > 0) {
                                $produtos = $produtos_alt = "";
                                foreach ($id_produtos as $id_produto)
                                    if (($result = $dash->consultarProdutoId(intval($id_produto)))["status"]) {
                                        $produtos .= "<span class='badge badge-primary'>" . $result["produto"]->getNome() . "</span>&nbsp;";
                                        $produtos_alt .= "
                                        <div id='alterar-venda-id-produtos-div-" . ++$pId . "' class='card text-white bg-success'>
                                            <input type='hidden' id='alterar-venda-id-produtos-" . $pId . "' value='" . $id_produto . "'/>
                                            <button class='close' style='margin: 4px' data-toggle='collapse' data-target='alterar-venda-id-produtos-div-" . $pId . "' 
                                                    aria-expanded='false' aria-controls='alterar-venda-id-produtos-div-" . $pId . "' 
                                                    onclick='removerItemProduto(\"alterar\", " . intval($id_produto) . ")'>&times;</button>
                                            <div class='card-body'>
                                                <p class='card-text'>" . $result["produto"]->getNome() . "</p>
                                            </div>
                                        </div>
                                        ";
                                    }
                            }

                            $select_usuarios = "";
                            $gerentes = $usrDao->consultarUsuariosPorNivel(UsuarioModel::GERENTE);
                            foreach ($gerentes as $gerente) {
                                $selected = $venda->getIdUsuario() === $gerente["login"]->getNome();
                                $select_usuarios .= "<option value='" . $gerente["usr"]->getId() . "'" . ($selected ? " selected" : "") . ">" . $gerente["login"]->getNome() . "</option>";
                            }

                            $vendedores = $usrDao->consultarUsuariosPorNivel(UsuarioModel::VENDEDOR);
                            foreach ($vendedores as $vendedor) {
                                $selected = $venda->getIdUsuario() === $vendedor["login"]->getNome();
                                $select_usuarios .= "<option value='" . $vendedor["usr"]->getId() . "'" . ($selected ? " selected" : "") . ">" . $vendedor["login"]->getNome() . "</option>";
                            }

                            if (strlen($select_usuarios) == 0)
                                $select_usuarios = "<option value='-1' disabled>Nenhum usuário!</option>";

                            $select_pagamentos = "";
                            $pagamentos = $pagamentoDao->consultarPagamentos();
                            foreach ($pagamentos as $pagamento) {
                                $selected = $venda->getIdPagamento() === $pagamento->getId();
                                $select_pagamentos .= "<option value='" . $pagamento->getId() . "'" . ($selected ? " selected" : "") . ">" . $pagamento->getNome() . "</option>";
                            }

                            if (strlen($select_pagamentos) == 0)
                                $select_pagamentos = "<option value='-1' disabled>Nenhum pagamento!</option>";

                            $select_produtos = "";
                            foreach ($produtoDao->consultarProdutos() as $produto)
                                $select_produtos .= "<option value='" . $produto->getId() . "'>" . $produto->getNome() . "</option>";

                            if (strlen($select_pagamentos) == 0)
                                $select_produtos = "<option value='-1' disabled>Nenhum produto!</option>";
                            echo "
                            <div class='card bg-info'>
                                <div class='table-responsive'>
                                    <table class='table table-info table-striped table-borderless text-center table-result'>
                                        <thead>
                                            <tr>
                                                <th scope='col'>ID</th>
                                                <th scope='col'>Usuário</th>
                                                <th scope='col'>Pagamento</th>
                                                <th scope='col' style='width: 35%'>Produtos</th>
                                                <th scope='col'>Valor</th>
                                                <th scope='col'>Data Registro</th>
                                                <th scope='col'>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th scope='row'>" . $venda->getId() . "</th>
                                                <td>" . $usrNome . "</td>
                                                <td>" . $pagamentoNome . "</td>
                                                <td>" . $produtos . "</td>
                                                <td>" . $venda->getValor() . "</td>
                                                <td>" . $venda->getDataRegistro() . "</td>
                                                <td>
                                                    <div class='text-center'>
                                                        <div class='btn-group'>
                                                            <button type='button' class='btn btn-sm btn-warning border-warning' data-toggle='collapse'
                                                                    data-target='#alterar-venda' aria-expanded='false' aria-controls='alterar-venda'>
                                                                <strong>Alterar</strong></button>
                                                            <button type='button' class='btn btn-sm btn-danger border-danger' data-toggle='collapse'
                                                                    data-target='#remover-venda' aria-expanded='false' aria-controls='remover-venda'>
                                                                <strong>Remover</strong></button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <hr/>
                            <div class='row'>
                                <div class='col'>
                                    <div class='collapse' id='alterar-venda'>
                                        <div class='container alert alert-warning border-warning'>
                                            <button type='button' class='close' data-toggle='collapse' data-target='#alterar-pagamento'
                                                    aria-expanded='false' aria-controls='alterar-pagamento'>&times;
                                            </button>
                                            <h4>Alterar Pagamento</h4>
                                            <hr/>
                                            <div class='form-row'>
                                                <div class='form-group col-md-6'>
                                                    <select class='form-control' id='alterar-venda-id-usuario'>
                                                    " . $select_usuarios . "
                                                    </select>
                                                </div>
                                                <div class='form-group col-md-6'>
                                                    <select class='form-control' id='alterar-venda-id-pagamento'>
                                                    " . $select_pagamentos . "
                                                    </select>
                                                </div>
                                                <div class='form-group col-md-6'>
                                                    <select multiple class='form-control' id='alterar-venda-id-produtos-select'>
                                                    " . $select_produtos . "
                                                    </select>
                                                </div>
                                                <div class='form-group col-md-6'>
                                                    <button type='button' id='alterar-venda-id-produtos-btn' class='btn btn-outline-success'
                                                        onclick='adicionarItemProduto(\"alterar\")'>
                                                        Adicionar
                                                    </button>
                                                </div>
                                                <div class='card-columns col-md-12' id='alterar-venda-id-produtos-container'>
                                                    " . $produtos_alt . "
                                                </div>
                                            </div>
                                            <input type='hidden' id='alterar-venda-id' value='" . $venda->getId() . "'/>
                                            <button id='alterar-venda-btn' type='button' class='btn btn-outline-warning'
                                                    onclick='alterarVenda()'>
                                                <strong>Alterar</strong></button>
                                            <hr/>
                                            <div id='alterar-venda-result'></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class='row'>
                                <div class='col'>
                                    <div class='collapse' id='remover-venda'>
                                        <div class='container alert alert-danger border-danger'>
                                            <button type='button' class='close' data-toggle='collapse' data-target='#remover-venda'
                                                    aria-expanded='false' aria-controls='remover-venda'>&times;
                                            </button>
                                            <h4>Remover Venda</h4>
                                            <hr/>
                                            <p align='justify'>
                                                Você realmente deseja remover esta venda?
                                            </p>
                                            <br/>
                                            <input type='hidden' id='remover-venda-id' value='" . $venda->getId() . "'/>
                                            <div class='text-center'>
                                                <div class='form-group col-md-12'>
                                                    <button id='remover-venda-btn' type='button' class='btn btn-lg btn-outline-success'
                                                            onclick='removerVenda()'>
                                                        <strong>Sim</strong></button>
                                                    &nbsp;
                                                    <button type='button' class='btn btn-lg btn-outline-danger' data-toggle='collapse'
                                                            data-target='#remover-venda' aria-expanded='false' aria-controls='remover-venda'>
                                                        <strong>Não</strong></button>
                                                </div>
                                            </div>
                                            <hr/>
                                            <div id='remover-venda-result'></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ";
                        } else
                            echo "
                            Algo errado aconteceu durante a consulta da venda!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
                case "consultar-venda-todos":
                    {
                        if ($user->getNivel() > UsuarioModel::VENDEDOR) {
                            echo "É necessário ter nível de acesso <strong>VENDEDOR</strong> para realizar essa operação!";
                            return;
                        }

                        $dash = DashboardController::getSingleton();
                        $response = $dash->consultarVendas();
                        if ($response["status"]) {
                            $usrDao = UsuarioDAO::getSingleton();
                            $loginDao = LoginDAO::getSingleton();
                            $pagamentoDao = PagamentoDAO::getSingleton();

                            $result = "
                            <p class='alert alert-warning form-text text-muted border-warning' align='justify'>
                                <strong><u>Importante!</u></strong> Operações de <strong class='text-warning'>Alterar</strong> e <strong class='text-danger'>Remover</strong> somente estão acessíveis através do botão <span class='badge badge-info border-info'>Por ID</span> , após realizado a consulta pelo índice.
                            </p>
                            <hr/>
                            <div class='card bg-info'>
                                <div class='table-responsive'>
                                    <table class='table table-info table-hover table-borderless text-center table-result'>
                                        <thead>
                                            <tr>
                                                <th scope='col'>ID</th>
                                                <th scope='col'>Usuário</th>
                                                <th scope='col'>Pagamento</th>
                                                <th scope='col' style='width: 35%'>Produtos</th>
                                                <th scope='col'>Valor</th>
                                                <th scope='col'>Data Registro</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            ";

                            foreach ($response["entries"] as $venda) {
                                $usr = $usrDao->consultarUsuario($venda->getIdUsuario());
                                $usrNome = $usr === null
                                    ? "<strong class='text-danger'>Usuário Desconhecido</strong>"
                                    : ($login = $loginDao->consultarLoginId($usr->getId())) === null
                                        ? "<strong class='text-danger'>Usuário Desconhecido</strong>"
                                        : $login->getNome();

                                $pagamento = $pagamentoDao->consultarPagamento($venda->getIdPagamento());
                                $pagamentoNome = $pagamento === null
                                    ? "<strong class='text-danger'>Pagamento Desconhecido</strong>"
                                    : $pagamento->getNome();

                                $produtos = null;
                                if (sizeof(($id_produtos = $venda->getIdProdutos())) > 0) {
                                    $produtos = "";
                                    foreach ($id_produtos as $id_produto)
                                        if (($produto_result = $dash->consultarProdutoId(intval($id_produto)))["status"])
                                            $produtos .= "<span class='badge badge-primary'>" . $produto_result["produto"]->getNome() . "</span>&nbsp;";
                                }

                                $result .= "
                                                <tr>
                                                    <th scope='row'>" . $venda->getId() . "</th>
                                                    <td>" . $usrNome . "</td>
                                                    <td>" . $pagamentoNome . "</td>
                                                    <td>" . $produtos . "</td>
                                                    <td>" . $venda->getValor() . "</td>
                                                    <td>" . $venda->getDataRegistro() . "</td>
                                                </tr>
                                ";
                            }

                            $result .= "
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            ";
                            echo $result;
                        } else
                            echo "
                            Algo errado aconteceu durante a consulta das vendas!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
                case "alterar-venda":
                    {
                        if ($user->getNivel() > UsuarioModel::VENDEDOR) {
                            echo "É necessário ter nível de acesso <strong>VENDEDOR</strong> para realizar essa operação!";
                            return;
                        }

                        if (!isset($_POST["id"]) || $_POST["id"] === "" || !is_numeric($_POST["id"])) {
                            echo "Elemento identificador não especificado!";
                            return;
                        }

                        if (!isset($_POST["id_usuario"]) || $_POST["id_usuario"] === "" || !is_numeric($_POST["id_usuario"])) {
                            echo "O campo <strong>ID Usuário</strong> deve ser referenciado!";
                            return;
                        }

                        if (!isset($_POST["id_pagamento"]) || $_POST["id_pagamento"] === "" || !is_numeric($_POST["id_pagamento"])) {
                            echo "O campo <strong>ID Pagamento</strong> deve ser referenciado!";
                            return;
                        }

                        $dash = DashboardController::getSingleton();
                        $id_produtos = array();
                        if (isset($_POST["id_produtos"])) {
                            if (strpos($_POST["id_produtos"], ",") !== false)
                                $id_produtos = explode(",", $_POST["id_produtos"]);
                            else
                                array_push($id_produtos, $_POST["id_produtos"]);
                        }

                        $preco_produtos = array();
                        $valor = 0;
                        if (isset($id_produtos))
                            foreach ($id_produtos as $id_produto)
                                if (($result = $dash->consultarProdutoId(intval($id_produto)))["status"]) {
                                    $valor += floatval($result["produto"]->getPrecoUnitario());
                                    array_push($preco_produtos, $result["produto"]->getPrecoUnitario());
                                }

                        $venda = new VendaModel(
                            $_POST["id"],
                            $_POST["id_usuario"],
                            $_POST["id_pagamento"],
                            $id_produtos,
                            $preco_produtos,
                            $valor,
                            null
                        );
                        $response = $dash->alterarVenda($venda);
                        if ($response["status"])
                            echo "Venda alterada com sucesso!";
                        else
                            echo "
                            Algo errado aconteceu durante a alteração da venda!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
                case "remover-venda":
                    {
                        if ($user->getNivel() > UsuarioModel::VENDEDOR) {
                            echo "É necessário ter nível de acesso <strong>VENDEDOR</strong> para realizar essa operação!";
                            return;
                        }

                        if (!isset($_POST["id"]) || $_POST["id"] === "" || !is_numeric($_POST["id"])) {
                            echo "Elemento identificador não especificado!";
                            return;
                        }

                        $dash = DashboardController::getSingleton();
                        $response = $dash->removerVenda($_POST["id"]);
                        if ($response["status"])
                            echo "Venda removida com sucesso!";
                        else
                            echo "
                            Algo errado aconteceu durante a remoção da venda!
                            <br/>
                            <br/>
                            <strong>Motivo:</strong> " . $response["err"] . "
                            ";
                    }
                    break;
            }
        }
        break;
}

function pJustified(string $msg): string
{
    return "<p align='justify'>$msg</p>";
}