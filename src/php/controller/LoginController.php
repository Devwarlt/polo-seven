<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 02:53
 */

namespace php\controller;

use php\dao\LoginDAO;
use php\dao\UsuarioDAO;
use php\model\LoginModel;
use php\model\UsuarioModel;

define("LOGIN_ID", "login-id");
define("LOGIN_NAME", "login-name");
define("LOGIN_PASSWORD", "login-password");

final class LoginController
{
    public static $NAME_REGEX_PATTERN = "/^[a-zA-ZáàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ ]{3,128}$/";
    public static $PASSWORD_REGEX_PATTERN = "/^[a-zA-Z0-9'\"!@#$%¨&*()_+¹²³£¢¬§=-]{3,128}$/";

    private static $singleton;

    private function __construct()
    {
    }

    public static function getSingleton(): LoginController
    {
        if (self::$singleton === null)
            self::$singleton = new LoginController();
        return self::$singleton;
    }

    public function autenticarLogin(string $nome, string $senha): array
    {
        $result = array(
            "login" => null,
            "err" => null
        );

        if (!preg_match(self::$NAME_REGEX_PATTERN, $nome)) {
            $result["err"] = "Nome inválido!";
            return $result;
        }

        if (!preg_match(self::$PASSWORD_REGEX_PATTERN, $senha)) {
            $result["err"] = "Senha inválida!";
            return $result;
        }

        $login = new LoginModel(-1, $nome, $senha, -1);
        $dao = LoginDAO::getSingleton();
        if (($result["login"] = $dao->consultarLogin($login)) === null) {
            $result["err"] = "Credenciais não autenticadas!";
            return $result;
        }

        return $result;
    }

    public function criarCredenciais(string $nome, string $senha): array
    {
        $result = array(
            "status" => false,
            "err" => null
        );

        if (!preg_match(self::$NAME_REGEX_PATTERN, $nome)) {
            $result["err"] = "Nome inválido!";
            return $result;
        }

        if (!preg_match(self::$PASSWORD_REGEX_PATTERN, $senha)) {
            $result["err"] = "Senha inválida!";
            return $result;
        }

        $login = new LoginModel(-1, $nome, $senha, -1);
        $loginDao = LoginDAO::getSingleton();
        if (($loginDao->consultarLogin($login) !== null)) {
            $result["err"] = "Já existe um cadastro com esses dados, utilize outra senha se preferir manter o mesmo nome.";
            return $result;
        }

        $usrDao = UsuarioDAO::getSingleton();
        $usr = new UsuarioModel(-1, UsuarioModel::GERENTE);
        if (($id = $usrDao->criarUsuario($usr)) === null) {
            $result["err"] = "Não foi possível criar o usuário!";
            return $result;
        }

        if (!$loginDao->criarLogin($login, $id)) {
            $result["err"] = "Não foi possível criar o login!";
            return $result;
        }

        $result["status"] = true;
        return $result;
    }

    public function criarSessao(LoginModel $login): void
    {
        session_start();

        $_SESSION[LOGIN_ID] = $login->getId();
        $_SESSION[LOGIN_NAME] = $login->getNome();
        $_SESSION[LOGIN_PASSWORD] = $login->getSenha();
    }

    public function terminarSessao(): void
    {
        session_start();

        unset($_SESSION[LOGIN_ID]);
        unset($_SESSION[LOGIN_NAME]);
        unset($_SESSION[LOGIN_PASSWORD]);
    }

    public function verificarSessao(): bool
    {
        return array_key_exists(LOGIN_ID, $_SESSION) && array_key_exists(LOGIN_NAME, $_SESSION) && array_key_exists(LOGIN_PASSWORD, $_SESSION);
    }
}