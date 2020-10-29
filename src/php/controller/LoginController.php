<?php
/**
 * Created by PhpStorm.
 * User: devwarlt
 * Date: 28/10/2020
 * Time: 02:53
 */

namespace php\controller;

use php\dao\LoginDAO as login;
use php\model\LoginModel;

define("LOGIN_ID", "login-id");
define("LOGIN_NAME", "login-name");
define("LOGIN_PASSWORD", "login-password");

final class LoginController
{
    public const SECOND = 1;
    public const MINUTE_TO_SECONDS = 60;
    public const HOUR_TO_SECONDS = 3600;
    public const DAY_TO_SECONDS = 86400;

    private static $name_regex_pattern = "/^[a-zA-ZáàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ ]{3,128}$/";
    private static $password_regex_pattern = "/^[a-zA-Z0-9'\"!@#$%¨&*()_+¹²³£¢¬§=-]{3,128}$/";

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

        if (!preg_match(self::$name_regex_pattern, $nome)) {
            $result["err"] = "Nome inválido!";
            return $result;
        }

        if (!preg_match(self::$password_regex_pattern, $senha)) {
            $result["err"] = "Senha inválida!";
            return $result;
        }

        $dao = login::getSingleton();
        if (($result["login"] = $dao->consultarLogin($nome, $senha)) === null) {
            $result["err"] = "Credenciais não autenticadas!";
            return $result;
        }

        return $result;
    }

    public function criarSessaoLogin(LoginModel $login): void
    {
        session_start();

        $_SESSION[LOGIN_ID] = $login->getId();
        $_SESSION[LOGIN_NAME] = $login->getNome();
        $_SESSION[LOGIN_PASSWORD] = $login->getSenha();
    }

    public function terminarSessaoLogin(): void
    {
        session_start();

        unset($_SESSION[LOGIN_ID]);
        unset($_SESSION[LOGIN_NAME]);
        unset($_SESSION[LOGIN_PASSWORD]);
    }

    public function verificarSessaoLogin(): bool
    {
        return array_key_exists(LOGIN_ID, $_SESSION) && array_key_exists(LOGIN_NAME, $_SESSION) && array_key_exists(LOGIN_PASSWORD, $_SESSION);
    }
}