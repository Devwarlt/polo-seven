<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 00:33
 */

namespace php\controller;

use php\dao\LoginDAO;
use php\dao\UsuarioDAO;
use php\model\LoginModel;
use php\model\UsuarioModel;

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
            $result["err"] = "Índice inválido! Somente valor positivos e não nulos serão aceitos!";
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
        if (!$loginDao->alterarPagamento($login)) {
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
}