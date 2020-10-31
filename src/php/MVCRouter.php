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
use php\controller\LoginController;
use php\dao\UsuarioDAO;
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
                            Algo errado aconteceu durante a consulta da conta do gerente!
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
                                            <button id='alterar-gerente-btn' type='button' class='btn btn-outline-warning'
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
                            Algo errado aconteceu durante a consulta da conta do vendedor!
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
            }
        }
        break;
}

function pJustified(string $msg): string
{
    return "<p align='justify'>$msg</p>";
}