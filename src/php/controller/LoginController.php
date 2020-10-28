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

define("LOGIN_ID", "login-id-cookie");
define("LOGIN_NAME", "login-name-cookie");
define("LOGIN_PASSWORD", "login-password-cookie");

final class LoginController
{
    public const SECOND = 1;
    public const MINUTE_TO_SECONDS = 60;
    public const HOUR_TO_SECONDS = 3600;
    public const DAY_TO_SECONDS = 86400;

    private static $name_regex_pattern = "/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ ]{3,128}$/";
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

    public function autenticarLogin(string $nome, string $senha): string
    {
        if (!preg_match(self::$name_regex_pattern, $nome))
            return "Nome inválido!";

        if (!preg_match(self::$password_regex_pattern, $senha))
            return "Senha inválida!";

        $dao = login::getSingleton();
        if ($dao->consultarLogin($nome, $senha) === null)
            return "Credenciais não autenticadas!";

        return null;
    }

    public function criarSessaoLogin(LoginModel $login): void
    {
        $expire = time() + self::HOUR_TO_SECONDS;
        setcookie(LOGIN_ID, $login->getIdUsuario());
        setcookie(LOGIN_NAME, $login->getNome(), $expire);
        setcookie(LOGIN_PASSWORD, $login->getSenha(), $expire);
    }

    public function terminarSessaoLogin(): void
    {
        $expire = time() - self::HOUR_TO_SECONDS;
        setcookie(LOGIN_ID, "", $expire);
        setcookie(LOGIN_NAME, "", $expire);
        setcookie(LOGIN_PASSWORD, "", $expire);
    }

    public function verificarSessaoLogin(): bool
    {
        return array_key_exists(LOGIN_ID, $_COOKIE) && array_key_exists(LOGIN_NAME, $_COOKIE) && array_key_exists(LOGIN_PASSWORD, $_COOKIE);
    }
}