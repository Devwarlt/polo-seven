<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 00:33
 */

namespace php\controller;

use php\dao\LoginDAO;
use php\dao\ProdutoDAO;
use php\dao\PagamentoDAO;
use php\dao\UsuarioDAO;
use php\dao\VendaDAO;
use php\model\LoginModel;
use php\model\ProdutoModel;
use php\model\PagamentoModel;
use php\model\UsuarioModel;
use php\model\VendaModel;

final class DashboardController
{
    private static $id_regex_pattern = "/^[0-9]\d*$/";

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

    public function consultarGerenteId(int $id): ?array
    {
        $result = array(
            "status" => false,
            "usr" => null,
            "login" => null,
            "err" => null
        );

        if (!preg_match(self::$id_regex_pattern, $id)) {
            $result["err"] = "Índice inválido! Somente valores positivos e não nulos serão aceitos!";
            return $result;
        }

        $loginDao = LoginDAO::getSingleton();
        if (($login = $loginDao->consultarLoginId($id)) === null) {
            $result["err"] = "Nenhum cadastro encontrado!";
            return $result;
        }

        $usrDao = UsuarioDAO::getSingleton();
        if (($usr = $usrDao->consultarUsuarioPorNivel($id, UsuarioModel::GERENTE)) === null) {
            $result["err"] = "Este usuário não é um gerente!";
            return $result;
        }

        $result["usr"] = $usr;
        $result["login"] = $login;
        $result["status"] = true;
        return $result;
    }

    public function consultarGerentes(): ?array
    {
        $result = array(
            "status" => false,
            "entries" => null,
            "err" => null
        );
        $usrDao = UsuarioDAO::getSingleton();
        if (($collection = $usrDao->consultarUsuariosPorNivel(UsuarioModel::GERENTE)) === null) {
            $result["err"] = "Não existem registros de gerentes!";
            return $result;
        }

        $result["entries"] = $collection;
        $result["status"] = true;
        return $result;
    }

    public function alterarGerente(LoginModel $login, UsuarioModel $usr): ?array
    {
        $result = array(
            "status" => false,
            "err" => null
        );

        if (!preg_match(self::$id_regex_pattern, $login->getId()) || !preg_match(self::$id_regex_pattern, $usr->getId())) {
            $result["err"] = "Índice inválido! Somente valores positivos e não nulos serão aceitos!";
            return $result;
        }

        if (!preg_match(LoginController::$NAME_REGEX_PATTERN, $login->getNome())) {
            $result["err"] = "Nome inválido!";
            return $result;
        }

        if (!preg_match(LoginController::$PASSWORD_REGEX_PATTERN, $login->getSenha())) {
            $result["err"] = "Senha inválida!";
            return $result;
        }

        $nivel = $usr->getNivel();
        if ($nivel < UsuarioModel::GERENTE || $nivel > UsuarioModel::VENDEDOR) {
            $result["err"] = "Nível de Acesso inválido!";
            return $result;
        }

        $loginDao = LoginDAO::getSingleton();
        if (!$loginDao->alterarLogin($login)) {
            $result["err"] = "Não foi possível alterar as credenciais do gerente!";
            return $result;
        }

        $usrDao = UsuarioDAO::getSingleton();
        if (!$usrDao->alterarUsuario($usr)) {
            $result["err"] = "Não foi possível alterar o nível de acesso do gerente!";
            return $result;
        }

        $result["status"] = true;
        return $result;
    }

    public function removerGerente(int $id, int $id_usuario): ?array
    {
        $result = array(
            "status" => false,
            "err" => null
        );

        if (!preg_match(self::$id_regex_pattern, $id) || !preg_match(self::$id_regex_pattern, $id_usuario)) {
            $result["err"] = "Índice inválido! Somente valores positivos e não nulos serão aceitos!";
            return $result;
        }

        $usrDao = UsuarioDAO::getSingleton();
        if (!$usrDao->removerUsuario($id_usuario)) {
            $result["err"] = "Não foi possível remover o gerente!";
            return $result;
        }

        $loginDao = LoginDAO::getSingleton();
        if (!$loginDao->removerLogin($id)) {
            $result["err"] = "Não foi possível remover as credenciais do gerente!";
            return $result;
        }

        $result["status"] = true;
        return $result;
    }

    public function consultarVendedorId(int $id): ?array
    {
        $result = array(
            "status" => false,
            "usr" => null,
            "login" => null,
            "err" => null
        );

        if (!preg_match(self::$id_regex_pattern, $id)) {
            $result["err"] = "Índice inválido! Somente valores positivos e não nulo serão aceitos!";
            return $result;
        }

        $loginDao = LoginDAO::getSingleton();
        if (($login = $loginDao->consultarLoginId($id)) === null) {
            $result["err"] = "Nenhum cadastro encontrado!";
            return $result;
        }

        $usrDao = UsuarioDAO::getSingleton();
        if (($usr = $usrDao->consultarUsuarioPorNivel($id, UsuarioModel::VENDEDOR)) === null) {
            $result["err"] = "Este usuário não é um vendedor!";
            return $result;
        }

        $result["usr"] = $usr;
        $result["login"] = $login;
        $result["status"] = true;
        return $result;
    }

    public function consultarVendedores(): ?array
    {
        $result = array(
            "status" => false,
            "entries" => null,
            "err" => null
        );
        $usrDao = UsuarioDAO::getSingleton();
        if (($collection = $usrDao->consultarUsuariosPorNivel(UsuarioModel::VENDEDOR)) === null) {
            $result["err"] = "Não existem registros de vendedores!";
            return $result;
        }

        $result["entries"] = $collection;
        $result["status"] = true;
        return $result;
    }

    public function alterarVendedor(LoginModel $login, UsuarioModel $usr): ?array
    {
        $result = array(
            "status" => false,
            "err" => null
        );

        if (!preg_match(self::$id_regex_pattern, $login->getId()) || !preg_match(self::$id_regex_pattern, $usr->getId())) {
            $result["err"] = "Índice inválido! Somente valores positivos e não nulos serão aceitos!";
            return $result;
        }

        if (!preg_match(LoginController::$NAME_REGEX_PATTERN, $login->getNome())) {
            $result["err"] = "Nome inválido!";
            return $result;
        }

        if (!preg_match(LoginController::$PASSWORD_REGEX_PATTERN, $login->getSenha())) {
            $result["err"] = "Senha inválida!";
            return $result;
        }

        $nivel = $usr->getNivel();
        if ($nivel < UsuarioModel::GERENTE || $nivel > UsuarioModel::VENDEDOR) {
            $result["err"] = "Nível de Acesso inválido!";
            return $result;
        }

        $loginDao = LoginDAO::getSingleton();
        if (!$loginDao->alterarLogin($login)) {
            $result["err"] = "Não foi possível alterar as credenciais do vendedor!";
            return $result;
        }

        $usrDao = UsuarioDAO::getSingleton();
        if (!$usrDao->alterarUsuario($usr)) {
            $result["err"] = "Não foi possível alterar o nível de acesso do vendedor!";
            return $result;
        }

        $result["status"] = true;
        return $result;
    }

    public function removerVendedor(int $id, int $id_usuario): ?array
    {
        $result = array(
            "status" => false,
            "err" => null
        );

        if (!preg_match(self::$id_regex_pattern, $id) || !preg_match(self::$id_regex_pattern, $id_usuario)) {
            $result["err"] = "Índice inválido! Somente valores positivos e não nulos serão aceitos!";
            return $result;
        }

        $usrDao = UsuarioDAO::getSingleton();
        if (!$usrDao->removerUsuario($id_usuario)) {
            $result["err"] = "Não foi possível remover o vendedor!";
            return $result;
        }

        $loginDao = LoginDAO::getSingleton();
        if (!$loginDao->removerLogin($id)) {
            $result["err"] = "Não foi possível remover as credenciais do vendedor!";
            return $result;
        }

        $result["status"] = true;
        return $result;
    }

    public function criarProduto(ProdutoModel $produto): array
    {
        $result = array(
            "status" => false,
            "err" => null
        );

        if (!preg_match(LoginController::$PRODUCT_NAME_REGEX_PATTERN, $produto->getNome())) {
            $result["err"] = "Nome inválido!";
            return $result;
        }

        $produtoDao = ProdutoDAO::getSingleton();
        if ($produtoDao->consultarProdutoNome($produto->getNome()) !== null) {
            $result["err"] = "Já existe um registro com o mesmo nome, utilize outro para continuar.";
            return $result;
        }

        if (!$produtoDao->criarProduto($produto)) {
            $result["err"] = "Não foi possível criar o produto!";
            return $result;
        }

        $result["status"] = true;
        return $result;
    }

    public function consultarProdutoId(int $id): ?array
    {
        $result = array(
            "status" => false,
            "produto" => null,
            "err" => null
        );

        if (!preg_match(self::$id_regex_pattern, $id)) {
            $result["err"] = "Índice inválido! Somente valores positivos e não nulos serão aceitos!";
            return $result;
        }

        $produtoDao = ProdutoDAO::getSingleton();
        if (($produto = $produtoDao->consultarProduto($id)) === null) {
            $result["err"] = "Nenhum registro encontrado!";
            return $result;
        }

        $result["produto"] = $produto;
        $result["status"] = true;
        return $result;
    }

    public function consultarProdutos(): ?array
    {
        $result = array(
            "status" => false,
            "entries" => null,
            "err" => null
        );
        $produtoDao = ProdutoDAO::getSingleton();
        if (($collection = $produtoDao->consultarProdutos()) === null) {
            $result["err"] = "Não existem registros de produtos!";
            return $result;
        }

        $result["entries"] = $collection;
        $result["status"] = true;
        return $result;
    }

    public function alterarProduto(ProdutoModel $produto): ?array
    {
        $result = array(
            "status" => false,
            "err" => null
        );

        if (!preg_match(self::$id_regex_pattern, $produto->getId())) {
            $result["err"] = "Índice inválido! Somente valores positivos e não nulos serão aceitos!";
            return $result;
        }

        if (!preg_match(LoginController::$PRODUCT_NAME_REGEX_PATTERN, $produto->getNome())) {
            $result["err"] = "Nome inválido!";
            return $result;
        }

        $produtoDao = ProdutoDAO::getSingleton();
        if (!$produtoDao->alterarProduto($produto)) {
            $result["err"] = "Não foi possível alterar os dados do produto!";
            return $result;
        }

        $result["status"] = true;
        return $result;
    }

    public function removerProduto(int $id): ?array
    {
        $result = array(
            "status" => false,
            "err" => null
        );

        if (!preg_match(self::$id_regex_pattern, $id)) {
            $result["err"] = "Índice inválido! Somente valores positivos e não nulos serão aceitos!";
            return $result;
        }

        $produtoDao = ProdutoDAO::getSingleton();
        if (!$produtoDao->removerProduto($id)) {
            $result["err"] = "Não foi possível remover o produto!";
            return $result;
        }

        $result["status"] = true;
        return $result;
    }

    public function criarPagamento(PagamentoModel $pagamento): array
    {
        $result = array(
            "status" => false,
            "err" => null
        );

        if (!preg_match(LoginController::$NAME_REGEX_PATTERN, $pagamento->getNome())) {
            $result["err"] = "Nome inválido!";
            return $result;
        }

        $pagamentoDao = PagamentoDAO::getSingleton();
        if ($pagamentoDao->consultarPagamentoNome($pagamento->getNome()) !== null) {
            $result["err"] = "Já existe um registro com o mesmo nome, utilize outro para continuar.";
            return $result;
        }

        if (!$pagamentoDao->criarPagamento($pagamento)) {
            $result["err"] = "Não foi possível criar o pagamento!";
            return $result;
        }

        $result["status"] = true;
        return $result;
    }

    public function consultarPagamentoId(int $id): ?array
    {
        $result = array(
            "status" => false,
            "pagamento" => null,
            "err" => null
        );

        if (!preg_match(self::$id_regex_pattern, $id)) {
            $result["err"] = "Índice inválido! Somente valores positivos e não nulos serão aceitos!";
            return $result;
        }

        $pagamentoDao = PagamentoDAO::getSingleton();
        if (($pagamento = $pagamentoDao->consultarPagamento($id)) === null) {
            $result["err"] = "Nenhum registro encontrado!";
            return $result;
        }

        $result["pagamento"] = $pagamento;
        $result["status"] = true;
        return $result;
    }

    public function consultarPagamentos(): ?array
    {
        $result = array(
            "status" => false,
            "entries" => null,
            "err" => null
        );
        $pagamentoDao = PagamentoDAO::getSingleton();
        if (($collection = $pagamentoDao->consultarPagamentos()) === null) {
            $result["err"] = "Não existem registros de pagamentos!";
            return $result;
        }

        $result["entries"] = $collection;
        $result["status"] = true;
        return $result;
    }

    public function alterarPagamento(PagamentoModel $pagamento): ?array
    {
        $result = array(
            "status" => false,
            "err" => null
        );

        if (!preg_match(self::$id_regex_pattern, $pagamento->getId())) {
            $result["err"] = "Índice inválido! Somente valores positivos e não nulos serão aceitos!";
            return $result;
        }

        if (!preg_match(LoginController::$NAME_REGEX_PATTERN, $pagamento->getNome())) {
            $result["err"] = "Nome inválido!";
            return $result;
        }

        $pagamentoDao = PagamentoDAO::getSingleton();
        if (!$pagamentoDao->alterarPagamento($pagamento)) {
            $result["err"] = "Não foi possível alterar os dados do pagamento!";
            return $result;
        }

        $result["status"] = true;
        return $result;
    }

    public function removerPagamento(int $id): ?array
    {
        $result = array(
            "status" => false,
            "err" => null
        );

        if (!preg_match(self::$id_regex_pattern, $id)) {
            $result["err"] = "Índice inválido! Somente valores positivos e não nulos serão aceitos!";
            return $result;
        }

        $pagamentoDao = PagamentoDAO::getSingleton();
        if (!$pagamentoDao->removerPagamento($id)) {
            $result["err"] = "Não foi possível remover o pagamento!";
            return $result;
        }

        $result["status"] = true;
        return $result;
    }

    public function criarVenda(VendaModel $venda): array
    {
        $result = array(
            "status" => false,
            "err" => null
        );

        $vendaDao = VendaDAO::getSingleton();
        if (!$vendaDao->criarVenda($venda)) {
            $result["err"] = "Não foi possível criar a venda!";
            return $result;
        }

        $result["status"] = true;
        return $result;
    }

    public function consultarVendaId(int $id): ?array
    {
        $result = array(
            "status" => false,
            "venda" => null,
            "err" => null
        );

        if (!preg_match(self::$id_regex_pattern, $id)) {
            $result["err"] = "Índice inválido! Somente valores positivos e não nulos serão aceitos!";
            return $result;
        }

        $vendaDao = VendaDAO::getSingleton();
        if (($venda = $vendaDao->consultarVenda($id)) === null) {
            $result["err"] = "Nenhum registro encontrado!";
            return $result;
        }

        $result["venda"] = $venda;
        $result["status"] = true;
        return $result;
    }

    public function consultarVendas(): ?array
    {
        $result = array(
            "status" => false,
            "entries" => null,
            "err" => null
        );
        $vendaDao = VendaDAO::getSingleton();
        if (($collection = $vendaDao->consultarVendas()) === null) {
            $result["err"] = "Não existem registros de vendas!";
            return $result;
        }

        $result["entries"] = $collection;
        $result["status"] = true;
        return $result;
    }

    public function alterarVenda(VendaModel $venda): ?array
    {
        $result = array(
            "status" => false,
            "err" => null
        );

        if (!preg_match(self::$id_regex_pattern, $venda->getId())) {
            $result["err"] = "Índice inválido! Somente valores positivos e não nulos serão aceitos!";
            return $result;
        }

        $vendaDao = VendaDAO::getSingleton();
        if (!$vendaDao->alterarVenda($venda)) {
            $result["err"] = "Não foi possível alterar os dados da venda!";
            return $result;
        }

        $result["status"] = true;
        return $result;
    }

    public function removerVenda(int $id): ?array
    {
        $result = array(
            "status" => false,
            "err" => null
        );

        if (!preg_match(self::$id_regex_pattern, $id)) {
            $result["err"] = "Índice inválido! Somente valores positivos e não nulos serão aceitos!";
            return $result;
        }

        $vendaDao = VendaDAO::getSingleton();
        if (!$vendaDao->removerVenda($id)) {
            $result["err"] = "Não foi possível remover a venda!";
            return $result;
        }

        $result["status"] = true;
        return $result;
    }
}